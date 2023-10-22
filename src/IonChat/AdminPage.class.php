<?php

namespace IonChat;

class AdminPage{

    public static function enable(){
        \add_action('admin_menu', '\IonChat\AdminPage::do_create_admin_page');
    }

    public static function do_create_admin_page(){
        if (!current_user_can('manage_options')) {
            return;
        }
        add_menu_page(
            'Ion Admin Page', // Page title
            'Ion', // Menu title
            'manage_options', // Capability
            'ion-admin-page', // Menu slug
            'IonChat\AdminPage::ion_admin_page_content', // Callback function for content
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

            // Update the option
            $openai_api_key = sanitize_text_field($_POST['openai-api-key']);
            update_option('openai-api-key', $openai_api_key);
        }

        // Get the existing API key, if any
        $existing_api_key = get_option('openai-api-key', '');

        ?>
        <div class="wrap">
            <h1></h1>
            <form method="post" action="">
                <?php wp_nonce_field('ion_admin_page_nonce_action', 'ion_admin_page_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="openai-api-key"><?php echo __('OpenAI API Key', 'ion-chat'); ?></label>
                        </th>
                        <td>
                            <input
                                name="openai-api-key"
                                type="text"
                                id="openai-api-key"
                                value="<?php echo esc_attr($existing_api_key); ?>"
                                placeholder="<?php echo __('Get at https://platform.openai.com/', 'ion-chat'); ?>"
                                class="regular-text"
                                oninput="checkInput()"
                            />
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input
                        type="submit"
                        name="submit"
                        id="submit"
                        class="button button-primary"
                        value="<?php echo __('Save Changes', 'ion-chat'); ?>"
                        <?php echo($existing_api_key === '' ? 'disabled' : ''); ?>
                    />
                </p>
            </form>
            <script>
                function checkInput() {
                    const inputField = document.getElementById("openai-api-key");
                    const submitButton = document.getElementById("submit");

                    if (inputField.value !== "") {
                        submitButton.disabled = false;
                    } else {
                        submitButton.disabled = true;
                    }
                }
            </script>
        </div>
        <?php
    }
}