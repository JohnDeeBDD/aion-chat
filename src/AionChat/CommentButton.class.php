<?php

namespace AionChat;

class CommentButton {

    // Public static method to add a button after each comment
    public static function add_button_after_comment($comment_text, $comment, $args) {
        // Define the button HTML
        $button_html = '<br /><button class="my-custom-button" onclick="AionChat\CommentButton::myButtonFunction()">Click Me</button>';

        // Append the button HTML to the comment text
        return $comment_text . $button_html;
    }

    // Public static method to enqueue custom script for the button functionality
    public static function my_custom_button_script() {
        ?>
        <script type="text/javascript">
            function myButtonFunction() {
                alert('Button clicked!');
            }
        </script>
        <?php
    }
}

