<?php

namespace IonChat;

class Exception extends \Exception {

 public static function handle(Exception $e) {
        if ($e instanceof IonChatException) {
            // Handle custom exceptions
        } else {
            // Handle generic exceptions
        }
        // Log, alert, etc.
    }
}
