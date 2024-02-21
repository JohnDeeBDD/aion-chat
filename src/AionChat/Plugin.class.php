<?php

namespace AionChat;

class Plugin{

    public static function enable(){
        self::setupProtocol();
        \add_action('admin_menu', '\AionChat\Plugin::do_create_admin_page');

    }


    public static function setupProtocol(){
        global $AionChat_mothership_url;
        global $AionChat_remote_node_url;
        //$AionChat_mothership_url = "https://ioncity.ai";
        $file = file_get_contents(\plugin_dir_path(__FILE__) . "../../servers.json");
        $IPs = json_decode($file);
        $AionChat_mothership_url = "http://" . $IPs[0];
        $AionChat_remote_node_url = "http://" . $IPs[1];
        global $AionChatProtocal;
        if (!isset($AionChatProtocal)) {
            $AionChatProtocal = "remote_node";
        }
        $siteURL = \get_site_url();
        if($siteURL === "http://localhost"){
            $AionChatProtocal = "mothership";
        }


    }


    public static function do_create_admin_page(){
        if (!current_user_can('manage_options')) {
            return;
        }
        add_menu_page(
            'Aion Admin Page', // Page title
            'Aion', // Menu title
            'manage_options', // Capability
            'aion-admin-page', // Menu slug
            'AionChat\Plugin::ion_admin_page_content', // Callback function for content
            'dashicons-admin-generic', // Icon
            99 // Position
        );
    }

    public static function ion_admin_page_content()
    {
        // Check if form has been submitted
        if (isset($_POST['submit'])) {
            // Verify nonce
            check_admin_referer('ion_admin_page_nonce_action', 'ion_admin_page_nonce');

            // Update the OpenAI API Key option
            $openai_api_key = sanitize_text_field($_POST['openai-api-key']);
            update_option('openai-api-key', $openai_api_key);

            // Save the Ion Chat Protocol option
            $ion_chat_protocol = sanitize_text_field($_POST['aion-chat-protocol']);
            update_option('aion-chat-protocol', $ion_chat_protocol);
        }

        // Get the existing options, if any
        $existing_api_key = get_option('openai-api-key', '');
        $existing_protocol = get_option('aion-chat-protocol', 'remote_node'); // Default to 'remote_node'

        ?>
        <div class="wrap">
            <h1>Ion Chat Settings</h1>
            <form method="post" action="">
                <?php wp_nonce_field('ion_admin_page_nonce_action', 'ion_admin_page_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="openai-api-key">OpenAI API Key</label>
                        </th>
                        <td>
                            <input
                                    name="openai-api-key"
                                    type="text"
                                    id="openai-api-key"
                                    value="<?php echo esc_attr($existing_api_key); ?>"
                                    placeholder="Get at https://platform.openai.com/"
                                    class="regular-text"
                                    oninput="checkInput()"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Ion Chat Protocol</th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="radio" name="ion-chat-protocol" value="remote_node" <?php checked($existing_protocol, 'remote_node'); ?>>
                                    Remote Node
                                </label><br>
                                <label>
                                    <input type="radio" name="ion-chat-protocol" value="mothership" <?php checked($existing_protocol, 'mothership'); ?>>
                                    Mothership
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input
                            type="submit"
                            name="submit"
                            id="submit"
                            class="button button-primary"
                            value="Save Changes"
                        <?php echo($existing_api_key === '' ? 'disabled' : ''); ?>
                    />
                </p>
            </form>
            <script>
                function checkInput() {
                    const apiKeyInput = document.getElementById("openai-api-key");
                    const submitButton = document.getElementById("submit");
                    submitButton.disabled = apiKeyInput.value === "";
                }
            </script>
        </div>
        <?php
    }

}