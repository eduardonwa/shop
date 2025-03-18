<?php

namespace App\Exceptions;

use Exception;

class MinimumPurchaseAmountException extends Exception
{
    protected $message = 'El monto mínimo de compra es de 10 pesos.';
}
