<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Transaction;

class TransactionsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('scope:read-general')->only('show');

        $this->middleware('can:view,transaction')->only('show');
    }

    public function index()
    {
        $this->allowedAdminAction();

        $transactions = Transaction::all();

        return $this->showAll($transactions);
    }

    public function show(Transaction $transaction)
    {
        return $this->showOne($transaction);
    }
}
