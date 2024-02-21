<?php

namespace AionChatMothership;

class User {

    public static function enable(){
        \add_action('rest_api_init', function () {
            register_rest_route('aion-chat/v1', '/app-password', array(
                'methods' => 'POST',
                'callback' => ['\AionChatMothership\User', 'handle_app_password_submission'],
                'permission_callback' => function () {
                    return true;
                }
            ));

            register_rest_route('aion-chat/v1', '/app-password', array(
                'methods' => 'DELETE',
                'callback' => ['\AionChatMothership\User', 'handle_app_password_deletion'],
                'permission_callback' => function () {
                    return current_user_can('edit_users');
                }
            ));
        });
    }

    public static function handle_app_password_submission($request) {
        $params = $request->get_json_params();
        $userId = isset($params['user_id']) ? intval($params['user_id']) : get_current_user_id();
        $remoteSiteUrl = sanitize_text_field($params['remote_site_url']);
        $remoteUserName = sanitize_text_field($params['remote_user_name']);
        $applicationPassword = sanitize_text_field($params['remote_application_password']);

        $result = self::addRemoteApplicationPassword($userId, $remoteSiteUrl, $remoteUserName, $applicationPassword);

        if ($result) {
            $updatedPasswords = self::getRemoteApplicationPasswords($userId);
            return new \WP_REST_Response(['message' => 'Application password added successfully', 'passwords' => $updatedPasswords], 200);
        } else {
            return new \WP_Error('app_password_error', 'Error adding application password', ['status' => 500]);
        }
    }

    public static function handle_app_password_deletion($request) {
        $params = $request->get_json_params();
        $userId = isset($params['user_id']) ? intval($params['user_id']) : \get_current_user_id();
        $remoteSiteUrl = isset($params['remote_site_url']) ? sanitize_text_field($params['remote_site_url']) : '';


        if (!current_user_can('edit_user', $userId)) {
            return new \WP_Error('rest_cannot_edit', __('Sorry, you are not allowed to delete application passwords for this user.'), array('status' => rest_authorization_required_code()));
        }

        $result = self::deleteRemoteApplicationPassword($userId, $remoteSiteUrl);

        if ($result) {
            $updatedPasswords = self::getRemoteApplicationPasswords($userId);
            return new \WP_REST_Response(['message' => 'Application password deleted successfully', 'passwords' => $updatedPasswords], 200);
        } else {
            return new \WP_Error('app_password_error', 'Error deleting application password', ['status' => 500]);
        }
    }

    public static function addRemoteApplicationPassword($userId, $remoteSiteUrl, $remoteUserName, $applicationPassword) {
        // Encode the URL before using it in the metaKey
        $encodedUrl = urlencode($remoteSiteUrl);
        $metaKey = 'remote_app_password_' . md5($encodedUrl);
        $metaValue = [
            'remoteSiteUrl' => $remoteSiteUrl, // Store the original URL
            'remoteUserName' => $remoteUserName,
            'applicationPassword' => $applicationPassword
        ];

        \update_user_meta($userId, $metaKey, $metaValue);
        return $metaKey;
    }

    public static function deleteRemoteApplicationPassword($userId, $remoteSiteUrl) {
        $encodedUrl = urlencode($remoteSiteUrl);
        $metaKey = 'remote_app_password_' . md5($encodedUrl);
        return \delete_user_meta($userId, $metaKey);
    }

    public static function getRemoteApplicationPasswords($userId) {
        $allMeta = \get_user_meta($userId);
        $appPasswords = [];

        foreach ($allMeta as $key => $value) {
            if (strpos($key, 'remote_app_password_') === 0) {
                $appPasswords[] = maybe_unserialize($value[0]);
            }
        }

        return $appPasswords;
    }

    public static function enable_user_edit_app_passwords_screen(){
        \add_action('show_user_profile', '\AionChatMothership\User::add_custom_user_profile_fields');
        \add_action('edit_user_profile', '\AionChatMothership\User::add_custom_user_profile_fields');
    }

    public static function add_custom_user_profile_fields($user) {
        $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : $user->ID;
        $remoteAppPasswords = self::getRemoteApplicationPasswords($userId);
        $hasPasswords = !empty($remoteAppPasswords);
        ?>
        <div id="application-passwords-storage">
            <h2><?php _e("Ion Chat Application Passwords", "aion-chat"); ?></h2>

            <?php if (!$hasPasswords): ?>
                <p id="no-remote-app-passwords"><?php _e('No remote application passwords.'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped table-view-list application-passwords-user" id="ion-chat-app-passwords-table">
                    <thead>
                    <tr>
                        <th><?php _e("Remote Site URL"); ?></th>
                        <th><?php _e("Remote User Name"); ?></th>
                        <th><?php _e("Delete"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($remoteAppPasswords as $password): ?>
                        <tr>
                            <td><?php echo esc_html($password['remoteSiteUrl']); ?></td>
                            <td><?php echo esc_html($password['remoteUserName']); ?></td>
                            <td><a href="#" class="delete-link" data-url="<?php echo esc_attr($password['remoteSiteUrl']); ?>"><?php _e('Delete'); ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <strong><?php _e("New Remote Application Password"); ?></strong>
            <table class="form-table">
                <tr>
                    <th><label for="remote-site-url"><?php _e("Remote Site URL"); ?></label></th>
                    <td><input type="text" name="remote_site_url" id="remote-site-url" value="" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="remote-user-name"><?php _e("Remote User Name"); ?></label></th>
                    <td><input type="text" name="remote_user_name" id="remote-user-name" value="" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="remote-application-password"><?php _e("Remote Application Password"); ?></label></th>
                    <td><input type="password" name="remote_application_password" id="remote-application-password" value="" class="regular-text" /></td>
                </tr>
                <tr>
                    <th></th>
                    <td><input type="hidden" id="remote-user-id" value="<?php echo esc_attr($userId); ?>"></td>
                </tr>
            </table>
            <p class="submit">
                <button type="button" class="button button-secondary" onclick="submitRemoteAppPasswordForm();"><?php _e('Add Remote Application Password', 'aion-chat'); ?></button>
            </p>
            <script type="text/javascript">
                function updatePasswordTable(passwords) {
                    var table = jQuery('#aion-chat-app-passwords-table tbody');
                    table.empty(); // Clear existing rows

                    if (passwords.length === 0) {
                        jQuery('#no-remote-app-passwords').show();
                    } else {
                        jQuery('#no-remote-app-passwords').hide();
                        passwords.forEach(function(password) {
                            var row = jQuery('<tr></tr>');
                            row.append(jQuery('<td></td>').text(password.remoteSiteUrl));
                            row.append(jQuery('<td></td>').text(password.remoteUserName));
                            row.append(jQuery('<td><a href="#" class="delete-link" data-url="' + password.remoteSiteUrl + '">Delete</a></td>'));
                            table.append(row);
                        });
                    }
                }

                function submitRemoteAppPasswordForm() {
                    var data = {
                        'user_id': document.getElementById('remote-user-id').value,
                        'remote_site_url': document.getElementById('remote-site-url').value,
                        'remote_user_name': document.getElementById('remote-user-name').value,
                        'remote_application_password': document.getElementById('remote-application-password').value
                    };

                    fetch('/wp-json/ion-chat/v1/app-password', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': '<?php echo \wp_create_nonce('wp_rest'); ?>'
                        },
                        body: JSON.stringify(data)
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Success:', data);
                            updatePasswordTable(data.passwords);
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                        });
                }

                document.querySelector('#aion-chat-app-passwords-table').addEventListener('click', function(event) {
                    if (event.target.classList.contains('delete-link')) {
                        event.preventDefault();
                        var remoteSiteUrl = event.target.getAttribute('data-url');
                        deleteRemoteAppPassword(remoteSiteUrl);
                    }
                });

                function deleteRemoteAppPassword(remoteSiteUrl) {
                    var userId = document.getElementById('remote-user-id').value;

                    fetch('/wp-json/ion-chat/v1/app-password', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': '<?php echo \wp_create_nonce('wp_rest'); ?>'
                        },
                        body: JSON.stringify({ 'user_id': userId, 'remote_site_url': remoteSiteUrl })
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Success:', data);
                            updatePasswordTable(data.passwords);
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                        });
                }
            </script>
        </div>
        <?php
    }

    public static function getRemoteApplicationPassword($userId, $remoteSiteUrl) {
        $encodedUrl = urlencode($remoteSiteUrl);
        $metaKey = 'remote_app_password_' . md5($encodedUrl);
        $passwordData = \get_user_meta($userId, $metaKey, true);

        if (!empty($passwordData)) {
            return $passwordData;
        }

        return null; // or an empty array, depending on how you want to handle the absence of data
    }

}
