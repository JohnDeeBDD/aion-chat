<?php
/*
Plugin Name: Ion Chat
Plugin URI: https://generalchicken.guru
Description: The Singularity is near.
Version: 1.0
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2023, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/

namespace IonChat;
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE);

//die("IonChat!");

require_once("/var/www/html/wp-content/plugins/ion-chat/src/IonChat/autoloader.php");
require_once("/var/www/html/wp-content/plugins/ion-chat/src/action-scheduler/action-scheduler.php");

$file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
$IPs = json_decode($file);
$mothership_url = "http://" . $IPs[0];
$remote_url = "http://" . $IPs[1];
global $dev1IP;
global $dev2IP;
$dev1IP = $IPs[0];
$dev2IP = $IPs[1];

if (!isset($IonChatProtocal)) {
    global $IonChatProtocal;
    $IonChatProtocal = "remote_node";
}


\add_action('ion_prompt_incoming', 'IonChat\TrafficController::prompt_incoming', 10, 1);
\add_action('rest_api_init', function () {
    \register_rest_route(
        "ion-chat/v1",
        "ion-prompt",
        array(
            'methods' => ['POST', 'GET'],
            'callback' => function ($args) {
                $Prompt = \unserialize($args["prompt"]);

                //this function passes the prompt to ion_prompt_incoming on the next page load
                \wp_schedule_single_event(time(), "ion_prompt_incoming", [$Prompt]);
                return "Prompt received. Status 200";
            },
            'permission_callback' => function () {
                return true;
            },
        )
    );
});

/*
\add_action('rest_api_init', function () {
    \register_rest_route(
        "ion-chat/v1",
        "ion-reply",
        array(
            'methods' => ['POST', 'GET'],
            'callback' => function ($args) {
                $Prompt = \unserialize($args["prompt"]);
                update_option('down_bus', \var_export($Prompt, true));
                if (isset($Prompt->response['choices'][0]['message']['content'])) {
                    $response = $Prompt->response['choices'][0]['message']['content'];
                } else {
                    $response = \var_export($Prompt, true);
                }
                $message_id = \Better_Messages()->functions->new_message([
                    'sender_id' => get_ion_user(),
                    'thread_id' => $Prompt->remote_thread_id,
                    'content' => $response,
                    'return' => 'message_id',
                    'error_type' => 'wp_error'
                ]);
                return "Prompt received. Status 200";
            },
            'permission_callback' => function () {
                return true;
            },
        )
    );
});
*/
//\add_action('better_messages_message_sent', 'IonChat\on_message_sent', 10, 1);

\register_activation_hook(__FILE__, 'IonChat\activate_ion_chat');

function on_message_sent($bm_message)
{
    //global $IonChatProtocal;
    //if ($IonChatProtocal === "remote_node") {
        TrafficController::on_message_sent($bm_message);
    //}
}

function createFunctionMetadata($name, $description, $parameters)
{
    return [
        "name" => $name,
        "description" => $description,
        "parameters" => $parameters
    ];
}

function doPutMessageInDB($sender_id, $thread_id, $content)
{
    $message_id = Better_Messages()->functions->new_message([
        'sender_id' => $sender_id,
        'thread_id' => $thread_id,
        'content' => $content,
        'return' => 'message_id',
        'error_type' => 'wp_error'
    ]);
    if (is_wp_error($message_id)) {
        $error = $message_id->get_error_message();
    }
}

/*
function getIonReply($thread_id)
{

    $userInThread = Better_Messages()->functions->get_recipients_ids($thread_id);

    //Only support for two chatters so far!
    if (!(\count($userInThread)) === 2) {
        return;
    }

    $noIons = true;
    foreach ($userInThread as $user_id) {
        if (isIonUser($user_id)) {
            $noIons = false;
        }
    }

    if ($noIons) {
        return;
    }

    $api_key = get_api_key(123);
    log_data_to_option($api_key, "api key");
    $message_thread_ids_array = returnArrayOfMessagesThread($thread_id);
    log_data_to_option($message_thread_ids_array, "message thread ids array");
    $compiled_messages = compile_messages_for_transport($message_thread_ids_array);
    $response = sendToChatGPT($compiled_messages, $api_key);
    log_data_to_option($response, "response from GPT");

    if (isset($response["choices"][0]["message"]["content"])) {
        $response = $response["choices"][0]["message"]["content"];
    } else {
        $response = \var_export($response, true);
    }
    doPutMessageInDB(3, $thread_id, $response);

}
*/
function get_ion_email()
{
    return "jiminac@aol.com";
}

function get_ion_user_id()
{
    $user = \get_user_by('email', get_ion_email());
    return ($user->ID);
}

function is_ion_user($user_id)
{
    $user_info = \get_userdata($user_id);
    $user_email = $user_info->user_email;

    if ($user_email === get_ion_email()) {
        return true;
    } else {
        return false;
    }
}

function log_data_to_option($data, $tag = "tag")
{
    $db = get_option('ion-chat-log');
    \update_option('ion-chat', $db . $tag . "<br />" . \var_export($data, true) . "<br /><br />");
}

function sendToChatGPT($messages, $api_key, $functions = null)
{
    // OpenAI API endpoint for ChatGPT
    $url = "https://api.openai.com/v1/chat/completions";

    // Ensure messages is an array
    $messages = is_object($messages) ? (array)$messages : $messages;

    // Prepare the data for the request
    $data = [
        "model" => "gpt-3.5-turbo-0613",
        //"model" => "gpt-4",
        'messages' => array_values($messages), // Convert to indexed array
        'max_tokens' => 150 // You can adjust this as needed
    ];
    $functions = [
        [
            "name" => "send_user_php",
            "description" => "Send user PHP related data or tasks",
            "parameters" => [
                "type" => "object",
                "properties" => [
                    "php_code" => [
                        "type" => "string",
                        "description" => "The PHP code or script to be sent",
                    ],
                    "action" => [
                        "type" => "string",
                        "enum" => ["execute", "analyze", "store"],
                        "description" => "The action to be performed on the provided PHP code",
                    ],
                ],
                "required" => ["php_code", "action"],
            ],
        ]
    ];


    // If functions are provided, add them to the data
    if ($functions !== null) {
        $data['functions'] = $functions;
    }

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_key",
        'Content-Type: application/json'
    ]);

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Decode the response
    return json_decode($response, true);
}

function activate_ion_chat()
{
    //Create Prompt database tables
   // \IonChat\Prompt::create_tables();

    \add_role(
        'ion',          // Role slug
        'Ion',          // Display name
        array(
            'read' => true,  // Subscriber capability
        )
    );

    // Create post object
    $my_post = array(
        'post_title' => "Chat",
        'post_content' => '[bp_better_messages_chat_room id="40"]',
        'post_status' => 'publish',
        'post_author' => 1,
    );
    \wp_insert_post($my_post);

    //TrafficController::activation_setup_db();

}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function force_return_user_id(string $email): int
{
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new \Exception('Invalid email address.');
    }

    // Check if email belongs to a user
    $user = \get_user_by('email', $email);

    // If user exists, return user ID
    if ($user) {
        return $user->ID;
    }

    // Generate random screen name and password
    $random_screen_name = generateRandomString();
    $random_password = generateRandomString();

    // Create new user
    $user_id = \wp_create_user($random_screen_name, $random_password, $email);

    // Set user role to 'subscriber'
    $user = new \WP_User($user_id);
    $user->set_role('subscriber');

    // Return new user ID
    return $user_id;
}

//Servers page
if (isset($_GET['ion-dev'])) {
    $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
    $IPs = json_decode($file);
    $dev1IP = $IPs[0];
    $dev2IP = $IPs[1];
    echo("<a href = 'http://$dev1IP/chat?username=codeception&pass=password' target = '_blank'>Mothership Chat Post</a><br />");
    echo("<a href = 'http://$dev2IP/chat?username=codeception&pass=password' target = '_blank'>Remote Chat Post</a><br />");
    echo("<a href = 'http://$dev1IP/?ion-chat-log=1' target = '_blank'>ion-chat-log</a><br />");
    echo("<a href = 'http://$dev1IP/?ion-chat-protocol=1' target = '_blank'>Mothership Protocol</a><br />");
    echo("<a href = 'http://$dev2IP/?ion-chat-protocol=1' target = '_blank'>Remote Protocol</a><br />");
    echo("<a href = 'http://$dev1IP/?ion-chat-up-bus=1' target = '_blank'>Up Bus Mothership</a><br />");
    echo("<a href = 'http://$dev2IP/?ion-chat-up-bus=1' target = '_blank'>Up Bus Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?curl_debug=1' target = '_blank'>Curl Debug</a><br />");
    echo("<a href = 'http://$dev1IP/?down_bus=1' target = '_blank'>Down Bus MS</a><br />");
    echo("<a href = 'http://$dev2IP/?down_bus=1' target = '_blank'>Down Bus Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?debug_info=1' target = '_blank'>Debug</a><br />");
    die();
}

if (isset($_GET['debug_info'])) {
    echo '<pre>';
    $Prompt = (\get_option('wp_curl_debug_info'));
    \var_dump($Prompt);
    echo '</pre>';

    echo '<pre>';
    $Prompt = (\get_option('wp_curl_debug_info_data'));
    \var_dump($Prompt);
    echo '</pre>';
    die();
}
//Show the protocol:
if (isset($_GET['ion-chat-protocol'])) {
    \add_action("init", function () {
        global $IonChatProtocal;
        die($IonChatProtocal);
    });
}

//login via URL
if (isset($_GET['username']) and $_GET['pass']) {
    \add_action('after_setup_theme', function () {
        $creds = array(
            'user_login' => $_GET['username'],
            'user_password' => $_GET['pass'],
            'remember' => true
        );

        $user = \wp_signon($creds, false);

        if (is_wp_error($user)) {
            echo $user->get_error_message();
        }
    });
}

//Show local up bus
if (isset($_GET['ion-chat-up-bus'])) {
    \add_action('init', function () {
        echo '<pre>';
        $Prompt = (\get_option('ion-chat-up-bus'));
        \var_dump($Prompt);
        echo '</pre>';
        die();
    });
}
//Show local up bus
if (isset($_GET['down_bus'])) {
    \add_action('init', function () {
        echo '<pre>';
        $X = new Prompt();
        $Prompt = \get_option('down_bus');
        // \var_dump($Prompt);
        \var_dump($Prompt);
        echo '</pre>';
        die();
    });
}


//Show local up bus
if (isset($_GET['curl_debug'])) {
    \add_action('init', function () {
        echo '<pre>';
        $Prompt = (\get_option('wp_curl_debug_info'));
        \var_dump($Prompt);
        echo '</pre>';
        echo("*******************************************");
        echo '<pre>';
        $Prompt = (\get_option('wp_curl_debug_info_data'));
        \var_dump($Prompt);
        echo '</pre>';
        die();
    });
}

add_action('admin_menu', 'IonChat\create_admin_page');

function create_admin_page()
{
    // Only display to admins
    if (!current_user_can('manage_options')) {
        return;
    }

    // Add menu page
    add_menu_page(
        'Ion Admin Page', // Page title
        'Ion', // Menu title
        'manage_options', // Capability
        'ion-admin-page', // Menu slug
        'IonChat\ion_admin_page_content', // Callback function for content
        'dashicons-admin-generic', // Icon
        99 // Position
    );
}

function ion_admin_page_content()
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


function enqueue_prismjs()
{
    wp_enqueue_script('prismjs', plugin_dir_url(__FILE__) . 'src/prismjs/prism.js', array(), '1.0.0', true);
    wp_enqueue_script('ion-chat-prismjs', plugin_dir_url(__FILE__) . 'src/IonChat/ion-chat-prism.js', array(), '1.0.0', true);
    wp_enqueue_script('jquery');
    wp_enqueue_style('prismjs', plugin_dir_url(__FILE__) . 'src/prismjs/themes/prism.css');
}

\add_action('wp_enqueue_scripts', 'IonChat\enqueue_prismjs');

function enqueue_prismjs_customizations()
{
    // Enqueue a different PrismJS theme
    wp_enqueue_style('prismjs-theme', plugin_dir_url(__FILE__) . 'src/prismjs/themes/prism-okaidia.css');

    // Enqueue PrismJS Line Numbers plugin
    wp_enqueue_script('prismjs-line-numbers', plugin_dir_url(__FILE__) . 'src/prismjs/plugins/line-numbers/prism-line-numbers.min.js', array('prismjs'), '1.0.0', true);
    wp_enqueue_style('prismjs-line-numbers', plugin_dir_url(__FILE__) . 'src/prismjs/plugins/line-numbers/prism-line-numbers.css');
}

//add_action('wp_enqueue_scripts', 'IonChat\enqueue_prismjs_customizations');


//require 'src/plugin-update-checker/plugin-update-checker.php';

//use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/*
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://ioncity.ai/wp-content/uploads/details.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'unique-plugin-or-theme-slug'
);
*/