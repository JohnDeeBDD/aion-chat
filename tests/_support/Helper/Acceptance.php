<?php
namespace Helper;

class Acceptance extends \Codeception\Module{

    private $hostname = "";

    /*
    Command line functions are executed either on localhost or on the remote dev servers.
    This function checks where the function is running, and if on local adds a prefix
    */
    public function doExecuteCommandLine($command, $target){
        $prefix = "";
        if($this->hostname == "kali"){
            $prefix = "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $target . " ";
        }
        return shell_exec($prefix . $command);
    }
    public function reconfigureThisVariable($array){
        $this->getModule('WPWebDriver')->_reconfigure($array);
        $this->getModule('WPWebDriver')->_restart();
    }

    public function _afterSuite(){
        //Cleanup:
        //shell_exec("wp post delete $(wp post list --format=ids) --force");
    }

    public function _beforeSuite($settings = []){
        //$hostname =
        $this->hostname = shell_exec("hostname");
    }

    public function pauseInTerminal(){
        echo "Press ENTER to continue: ";
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        fclose($handle);
        echo "\n";
    }

    public function getSiteUrls(){
        return json_decode(file_get_contents('/var/www/html/wp-content/plugins/aion-chat/servers.json'), true);
    }

    /**
     * Helper function to execute remote commands via SSH
     */
    public function executeRemoteCommandAsUbuntu($serverIP, $command) {
        $sshCommand = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@$serverIP $command";
        $result = shell_exec($sshCommand);
        echo($result);
        return $result;
    }

}
