<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_unauthenticated_cannot_access_categories(): void
    {
        $this->getJson('/api/categories')
            ->assertStatus(401)
            ->assertJsonPath('status', false);
    }

    public function test_can_list_categories_with_sub_categories(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $cat1 = Category::create(['name' => 'A']);
        $cat2 = Category::create(['name' => 'B']);
        SubCategory::create(['category_id' => $cat1->id, 'name' => 'A-1']);
        SubCategory::create(['category_id' => $cat1->id, 'name' => 'A-2']);
        SubCategory::create(['category_id' => $cat2->id, 'name' => 'B-1']);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonStructure(['data' => [['id','name','sub_categories']]])
            ->assertJsonCount(2, 'data')
            ->assertJsonCount(2, 'data.0.sub_categories');
    }

    public function test_can_get_sub_categories_of_specific_category(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;
        $cat = Category::create(['name' => 'X']);
        $sub1 = SubCategory::create(['category_id' => $cat->id, 'name' => 'X-1']);
        $sub2 = SubCategory::create(['category_id' => $cat->id, 'name' => 'X-2']);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/categories/'.$cat->id.'/sub-categories')
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('category.id', $cat->id)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [['id','name']]]);
    }
}

