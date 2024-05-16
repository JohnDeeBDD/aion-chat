<?php

namespace AionChat;

class DebugMode{

    public static function enable(){
       // die("DebugMode::enable");
        if(isset($_GET['option'])){
            try {
                DebugMode::display_option($_GET['option']);
            }
            catch(\Exception $e) {}
            die();
        }
        if(isset($_GET['force_delete_all_posts'])){
            //die("xxx");
            DebugMode::force_delete_all_posts();
        }
    }

    public static function display_option($option_name) {
        // Retrieve the value of the WordPress option
        $option_value = \get_option($option_name);

        // Pretty-print the option value
        echo '<pre>';
        var_dump($option_value);
        echo '</pre>';
        die("display_option");
        // Terminate the script
        throw new \Exception("Script terminated");
    }

    public static function set_option($key, $option){
        update_option($key, $option);
    }

    public static function force_delete_all_posts() {
    $args = array(
        'post_type' => 'post',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'fields' => 'ids'
    );
    $all_posts = get_posts($args);

    foreach ($all_posts as $post_id) {
        \wp_delete_post($post_id, true);
    }
}


}
