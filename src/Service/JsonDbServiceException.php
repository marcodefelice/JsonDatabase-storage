<?php
namespace Mdf\JsonStorage\Service;

class JsonDbServiceException extends \Exception
{
    public function __construct($message = 'An error occurred while interacting with the JSON database.', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}