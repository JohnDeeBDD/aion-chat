<?php

namespace IonChat;

class DebugMode{

    public static function enable(){
        if(isset($_GET['option'])){
            try {
                DebugMode::display_option($_GET['option']);
            }
            catch(\Exception $e) {}
            die();
        }
    }

    public static function display_option($option_name) {
        // Retrieve the value of the WordPress option
        $option_value = \get_option($option_name);

        // Pretty-print the option value
        echo '<pre>';
        var_dump($option_value);
        echo '</pre>';

        // Terminate the script
        throw new \Exception("Script terminated");
    }

}
