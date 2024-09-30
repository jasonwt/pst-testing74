<?php

declare(strict_types=1);

namespace Pst\Testing\Exceptions;

use Exception;
use Throwable;

class ShouldException extends Exception {
    

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null) {
        $trace = $this->getTrace()[0];
        $message = "\n" . $trace["file"] . " [" . $trace["line"] . "]\n\n" . $message . "\n";

        $messageParts = explode("\n", $message);

        $maxMessagePartLength = array_reduce($messageParts, function($carry, $item) {
            return max($carry, strlen($item));
        }, 0);

        $message = str_repeat("!", $maxMessagePartLength) . "\n";
        $message .= implode("\n", $messageParts) . "\n";
        $message .= str_repeat("!", $maxMessagePartLength) . "\n";


        parent::__construct($message, $code, $previous);
    }
}