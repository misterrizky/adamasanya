<?php

namespace App\Exceptions;

use Exception;

class InvalidPaymentAmountException extends Exception
{
    protected $message = 'Jumlah pembayaran tidak valid.';
}