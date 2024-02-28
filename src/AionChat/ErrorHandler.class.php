<?php

namespace AionChat;

class Exception extends \Exception {

 public static function handle(Exception $e) {
        if ($e instanceof AionChatException) {
            // Handle custom exceptions
        } else {
            // Handle generic exceptions
        }
        // Log, alert, etc.
    }
}
