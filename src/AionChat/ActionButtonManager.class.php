<?php

namespace AionChat;

/**
 * Class responsible for managing action buttons related to Aion Conversations.
 */
class ActionButtonManager {

    /**
     * Registers the action to add custom buttons to the comment form.
     */
    public static function enable_custom_comment_buttons() {
        \add_action('comment_form', [self::class, 'add_custom_comment_buttons']);
        self::listen_for_button_click();
    }

    /**
     * Outputs custom buttons for the comment form.
     */
    public static function add_custom_comment_buttons() {
        $custom_button_html = '
            <div class="comment-form-custom-buttons">
                <button type="button" id="aion-chat-dall-e-3-button">Fetch Image</button>
            </div>';

        echo $custom_button_html;
    }

    public static function listen_for_button_click(){
        if(isset($_GET['aion-chat-action'])){

        }
    }
}
