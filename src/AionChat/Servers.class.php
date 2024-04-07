<?php

namespace AionChat;

class Servers{

    public $mothershipIP;
    public $remoteNodeIP;
    public $mothershipURL;
    public $remoteNodeURL;

    public function __construct(){
        $fileName = "/var/www/html/wp-content/plugins/aion-chat/servers.json";
        if(file_exists($fileName)){
            $file = file_get_contents($fileName);
            $IPs = json_decode($file);
            $this->mothershipIP = $IPs[0];
            $this->mothershipURL = "http://" . $this->mothershipIP;
            $this->remoteNodeIP = $IPs[1];
            $this->remoteNodeURL = "http://" . $this->remoteNodeIP;
        }else{
            $this->mothershipURL = "https://aion.garden";
        }
    }
}