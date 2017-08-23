<?php
namespace ObjectivePHP\Package\Connect\Exception;

/**
 * Class AccessDeniedException
 *
 * @package ObjectivePHP\Package\Connect\Exception
 */
class AccessDeniedException extends \Exception
{
    public function __construct($message = "", $code = 403, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
