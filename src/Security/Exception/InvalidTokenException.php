<?php

namespace App\Security\Exception;

use Symfony\Component\HttpFoundation\Response;

class InvalidTokenException extends \Exception
{
    protected $code = Response::HTTP_FORBIDDEN;
    protected $message = 'Invalid token';
}