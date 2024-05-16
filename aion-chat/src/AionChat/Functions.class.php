<?php

namespace AionChat;

class Functions {


    // The app is receiving a chat communication response from OpenAI. It is either a textual response of some kind,
    // or it is a "function call". The format of the "function call" response is unusual and not a typical PHP format.
    // The purpose of this function is to a) identify if the string is in fact a function call.
    // this is accomplished by the function returning boolean false if the string is not a function call.
    // and b) if it is a function call, return the name of the function, and the parameters and data of the function is a
    public static function get_open_a_i_funcion_call_parameters($string) {
        // Look for the specific segment containing "execute_local_command"
      //  $startMarker = "'name' => 'execute_local_command'";
        $startMarker = "'name' => 'execute_wordpress_api_request'";

        $startPos = \strpos($string, $startMarker);

        if ($startPos === false) {
            // If the marker is not found, the required function call does not exist in the string
            return false;
        }

        // Extract the substring starting from the found marker
        $subString = substr($string, $startPos);

        // Assume the structure ends with the next occurrence of "),"
        // Adjust the logic here based on the actual structure and how it ends
        $endPos = strpos($subString, "),");
        $functionCallString = substr($subString, 0, $endPos);

        // Extract the arguments part
        $argumentsMarker = "'arguments' => '";
        $argumentsStartPos = strpos($functionCallString, $argumentsMarker) + strlen($argumentsMarker);
        $argumentsEndPos = strpos($functionCallString, "'", $argumentsStartPos);
        $argumentsString = substr($functionCallString, $argumentsStartPos, $argumentsEndPos - $argumentsStartPos);

        // Decode the arguments JSON string
        $arguments = json_decode($argumentsString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // If there's an error in decoding, return false
            return false;
        }

        // Construct the expected result format
        return [
            'name' => 'execute_local_command',
            'arguments' => $arguments
        ];
    }

    public static function enableFunctionCall(){
        if(isset($_GET['f'])){
            \add_action("init", function(){
                    $postID = self::returnPostWithTag("local-function-call");
                    if(!$postID){return;}
                    $function = \get_option("function_call_" . $postID, true);
                    $function_name = $function['choices'][0]['message']['function_call']['name'];
                    $arguments = $function['choices'][0]['message']['function_call']['arguments'];
                    //var_dump($arguments);die();
                    $arguments = json_decode($arguments);
                    $endpoint = $arguments->endpoint;
                    $endpoint = "http://localhost" . $endpoint;
                    $data = json_decode($arguments->data);
                    $data = json_decode(json_encode($data), true);
                    unset($data['author']);
                    //var_dump($data);die();
                $url = $endpoint;
                //die($url);
                $args = array(
                    'method'    => 'POST',
                    'headers'   => array(
                        'Authorization' => 'Basic ' . base64_encode( 'Assistant:WHHxkczVJyhBP52Ym7piHntM' ),
                        'Content-Type'  => 'application/json',
                    ),
                    'body'      => json_encode( $data )
                );

// Execute the request using wp_remote_post()
                $response = \wp_remote_post( $url, $args );

// Handle the response
                if ( is_wp_error( $response ) ) {
                    $error_message = $response->get_error_message();
                    echo "Something went wrong: $error_message";
                }
                self::doRemoveTag($postID, "local-function-call");
            });

        }
    }

    public static function doRemoveTag($post_id, $tagToDelete){
        $post_tags = \wp_get_post_terms( $post_id );
        $new_tags = array();
        foreach ( $post_tags as $tag) {
            $tag_string = \http_build_query( $tag );
            if( strpos( $tag_string , $tagToDelete ) === false ) {
                $new_tags[] = $tag->name;
            }
        }
        \wp_set_post_terms ($post_id, $new_tags, 'post_tag');
    }

    public static function returnPostWithTag($tag) {
        // Check if the tag parameter is valid
        if (empty($tag)) {
            return false;
        }

        // Set up the query arguments
        $args = array(
            'post_type'      => 'aion-conversation', // Your custom post type
            'tag'            => $tag,                // Tag to search for
            'posts_per_page' => 1,                   // We only need one post
        );

        // Execute the query
        $query = new \WP_Query($args);

        // Check if there are any posts found
        if ($query->have_posts()) {
            $query->the_post(); // Set up the post data
            return \get_the_ID(); // Return the current post ID
        }

        // Return false if no posts were found
        return false;
    }

    public static function returnLatestCommentID($postID) {
        // Check if $postID is valid
        if (!is_numeric($postID) || $postID <= 0) {
            return false;
        }

        // Get the comments for the post
        $comments = \get_comments(array(
            'post_id' => $postID,
            'number' => 1, // Get only the latest comment
            'status' => 'approve', // Get only approved comments
            'order' => 'DESC' // Order by latest comments
        ));

        // Check if there are any comments
        if (count($comments) > 0) {
            // Return the ID of the latest comment
            return $comments[0]->comment_ID;
        } else {
            // Return false if there are no comments
            return false;
        }
    }

}
