<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('subCategories:id,category_id,name')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255','unique:categories,name'],
        ]);
        $category = Category::create($validated);
        ActivityLogger::log('category.created', $category, 'Kategori dibuat', ['name' => $category->name]);
        return back()->with('status','Kategori berhasil ditambahkan');
    }

    public function destroy(Category $category)
    {
        $snapshot = $category->only(['id','name']);
        $category->delete();
        ActivityLogger::log('category.deleted', null, 'Kategori dihapus', $snapshot);
        return back()->with('status','Kategori dihapus');
    }

    public function storeSub(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
        ]);
        $exists = SubCategory::where('category_id',$category->id)->where('name',$validated['name'])->exists();
        if ($exists) {
            return back()->withErrors(['name'=>'Sub kategori sudah ada.']);
        }
        $sub = $category->subCategories()->create($validated);
        ActivityLogger::log('subcategory.created', $sub, 'Sub kategori dibuat', ['name'=>$sub->name,'category_id'=>$category->id]);
        return back()->with('status','Sub kategori ditambahkan');
    }

    public function destroySub(Category $category, SubCategory $subCategory)
    {
        if ($subCategory->category_id !== $category->id) {
            abort(404);
        }
        $snapshot = $subCategory->only(['id','name','category_id']);
        $subCategory->delete();
        ActivityLogger::log('subcategory.deleted', null, 'Sub kategori dihapus', $snapshot);
        return back()->with('status','Sub kategori dihapus');
    }
}
