<?php

namespace IonChat;

class Step{

    public string $method;
    public int $retries;
    public int $maxRetries;
    public int $onfail;
    public array $parameters;
    public string $name;
    public string $return_type;
    public string $description;
    public int $ID;

}