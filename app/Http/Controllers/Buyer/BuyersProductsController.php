<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;

class BuyersProductsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index(Buyer $buyer)
    {
        $products = $buyer->transactions()->with('product')->get()->pluck('product');

        return $this->showAll($products);
    }
}
