<?php

namespace POPSuL\Brainfuck\Exception;

use Exception;

class BrainfuckException extends \Exception
{

    public function __construct($message = "Fuck my brain", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}