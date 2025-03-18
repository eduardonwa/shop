<?php

namespace App\Exceptions;

use Exception;

class EmptyCartException extends Exception
{
    protected $message = 'Tu carrito está vacío.';
}
