<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Transaction;
use App\Transformers\TransactionTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsBuyersTransactionsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . TransactionTransformer::class)->only(['store']);

        $this->middleware('scope:purchase-product')->only(['store']);

        $this->middleware('can:purchase,buyer')->only('store');
    }

    public function store(Request $request, Product $product, User $buyer)
    {
        $this->validate($request, [
            'quantity' => 'required|integer|min:1'
        ]);

        if ($buyer->id === $product->seller_id) {
            return $this->errorResponse('The buyer must be different from the seller.', 409);
        }

        if (!$buyer->isVerified()) {
            return $this->errorResponse('The buyer must be a verified user.', 409);
        }

        if (!$product->seller->isVerified()) {
            return $this->errorResponse('The seller must be a verified user.', 409);
        }

        if (!$product->isAvailable()) {
            return $this->errorResponse('The product must be an available product.', 409);
        }

        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('The product does not have enough quantity for this transaction.', 409);
        }

        return DB::transaction(function () use ($request, $product, $buyer) {
            $product->quantity -= $request->quantity;
            $product->save();

            $transactions = Transaction::create([
                'quantity' => $product->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            return $this->showOne($transactions, 201);
        });
    }
}
