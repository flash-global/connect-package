<?php

namespace Fei\Service\Connect\Package\Exception;

/**
 * Class RoleNotAllowedException
 *
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Connect\Exception
 */
class RoleNotAllowedException extends \Exception
{
    public function __construct(string $message = "", int $code = 401, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
