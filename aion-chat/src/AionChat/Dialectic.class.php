<?php

namespace AionDialectic;

class Dialectic{

    public static function chain_response($post_id, $response){

        if (\is_singular("aion-conversation")){
            die("aion-conversation");
        }
        global $AionChatProtocal;
        if ($AionChatProtocal == "remote_node"){

        }

    }
    public static function enable(){
        \add_action('wp_enqueue_scripts', function(){
            \wp_enqueue_script('jquery');
        });

        //die("enabled");
        \add_action("wp", function(){
            if (!\is_singular("aion-conversation")){
                return;
            }
           // die("line 24");
            $post_id = \get_the_ID(); // Get the post ID of the current aion-conversation
            if (\metadata_exists("post", $post_id, "aion-dialectic-conversant-url")){
                $meta_value = \get_post_meta($post_id, "aion-dialectic-conversant-url", true);
                $target_post_id = \url_to_postid($meta_value); // Convert the URL to a post ID

                // Fetch the latest comment for the target post
                $comments = \get_comments(array(
                    'post_id' => $post_id,
                    'number' => 1, // Only fetch the most recent comment
                    'status' => 'approve', // Ensure the comment is approved
                    'orderby' => 'comment_date_gmt',
                    'order' => 'DESC',
                ));
             //   var_dump($target_post_id);die("x");
                if (!empty($comments)) {
                   // die("line 40");
                    $comment_author_id = $comments[0]->user_id; // Get the ID of the latest comment's author
                    $post_author_id = \get_post_field('post_author', $post_id); // Get the ID of the post's author
               //    var_dump($comments);die();


                 //   var_dump($post_author_id);die();
                    //var_dump($comment_author_id);die();
                    // Check if the author of the comment is also the author of the post
                    if ($comment_author_id == $post_author_id) {
                        $response = $comments[0]->comment_content; // Get the content of the latest comment
                        $encode = \urlencode($response);
                        $url = $meta_value . "?aion-dialectic-response=" . $encode;
                        if(isset($_GET['aion-dialectic-response'])){

                        }else{
                            \wp_redirect($url);
                            exit; // Ensure execution stops after redirect
                        }


                    } else {
                        return; // The authors do not match, so do nothing
                    }
                }
            }
        });

        \add_action("wp_footer", function(){
            echo(
<<<JavaScript
<script>
jQuery(document).ready(function() {
    // Check if the textarea with the ID of "comment" exists
    if (jQuery('#comment').length) {
        // Get the URL parameters
        var urlParams = new URLSearchParams(window.location.search);
        
        // Check if "aion-dialectic-response" parameter exists
        if (urlParams.has('aion-dialectic-response')) {
            // Decode the URL-encoded string
            var decodedString = decodeURIComponent(urlParams.get('aion-dialectic-response'));
            
            // Set the decoded string as the value of the textarea
            jQuery('#comment').val(decodedString);
        }
    }
    jQuery("#submit").focus();
});
</script>
JavaScript
);
            
        });

    }

}