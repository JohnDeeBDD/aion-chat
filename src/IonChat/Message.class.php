<?php

namespace IonChat;

class Message {

    public string  $role;
    public string  $user_id;
    public string  $content;

    public function __construct(string $role, string $content) {
        $this->role = $role;
        $this->content = $content;
    }

}