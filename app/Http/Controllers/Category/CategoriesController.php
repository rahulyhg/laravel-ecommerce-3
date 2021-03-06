<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;

class CategoriesController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['index', 'show']);

        $this->middleware('auth:api')->except(['index', 'show']);

        $this->middleware('transform.input:' . CategoryTransformer::class)->only(['store', 'update']);
    }

    public function index()
    {
        $this->allowedAdminAction();

        $categories = Category::all();

        return $this->showAll($categories);
    }

    public function store(Request $request)
    {
        $this->allowedAdminAction();

        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);

        $newCategory = Category::query()->create($request->all());

        return $this->showOne($newCategory, 201);
    }

    public function show(Category $category)
    {
        return $this->showOne($category);
    }

    public function update(Request $request, Category $category)
    {
        $this->allowedAdminAction();

        $category->fill($request->only([
            'name',
            'description'
        ]));

        if (!$category->isDirty()) {
            return $this->errorResponse('You need to specify any different value to update.', 422);
        }

        $category->update();

        return $this->showOne($category);
    }

    public function destroy(Category $category)
    {
        $this->allowedAdminAction();

        $category->delete();

        return $this->showOne($category);
    }
}
