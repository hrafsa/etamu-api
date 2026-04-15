<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\SubCategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    /**
     * GET /api/categories
     * Query params:
     *  - include_sub=true|false (default true)
     *  - include=sub (alternative flag)
     */
    public function index(Request $request): JsonResponse
    {
        $include = $request->boolean('include_sub', true) || $request->get('include') === 'sub';

        $categories = Category::query()
            ->when($include, fn($q) => $q->with('subCategories'))
            ->orderBy('name')
            ->get();

        return $this->success('Daftar kategori', CategoryResource::collection($categories));
    }

    /**
     * GET /api/categories/{category}/sub-categories
     */
    public function subCategories(Category $category): JsonResponse
    {
        $subs = $category->subCategories()->orderBy('name')->get();
        return $this->success('Daftar sub kategori', [
            'category' => [ 'id' => $category->id, 'name' => $category->name ],
            'data' => SubCategoryResource::collection($subs),
        ]);
    }
}
