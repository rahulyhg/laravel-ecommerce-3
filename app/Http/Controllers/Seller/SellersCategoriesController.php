<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SellersCategoriesController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('scope:read-general')->only('index');

        $this->middleware('can:view,seller')->only('index');
    }

    public function index(Seller $seller)
    {
        $categories = $seller->products()->has('categories')->with('categories')->get()
            ->pluck('categories')->collapse()->unique('id')->values();

        return $this->showAll($categories);
    }
}
