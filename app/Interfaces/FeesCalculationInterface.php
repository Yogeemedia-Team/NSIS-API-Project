<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\Paginator;

interface FeesCalculationInterface {
    public function monthlyFee();
    public function invoice_generate();
    public function user_payments($id);
    public function current_user_pay(array $data);
    public function all_user_pay(array $data);
    public function user_payment_update(array $data);

}