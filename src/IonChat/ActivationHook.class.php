<?php

namespace IonChat;

class ActivationHook{

    public static function enable(){
        \register_activation_hook(__FILE__, '\IonChat\ActivationHook::do_activation_hook');
    }

    public static function do_activation_hook(){
        //Ping::doPing(User::activation_setup());
        //self::doCreateIonHomePage();
        //self::deployPosts();
       // self::pingMothership();
        self::setModeVariables();
    }

    public static function setModeVariables(){
        //\update_option("ion-chat-protocol", "remote_node");
        \update_option("ion-chat-protocol", "mothership");
    }
    public static function pingMotherhship(){ }

    private static function doCreateIonHomePage(){
        $my_post = array(
            'post_title'    => "Ion Home Page",
            'post_content'  => "Hi! I am an Aion named Ion.",
            'post_status'   => 'publish',
            'post_author'   => User::get_ion_user_id(),
            'post_type'     => 'aion-conversation'
        );
        \wp_insert_post( $my_post );
    }

    private static function get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) {
        _deprecated_function( __FUNCTION__, '6.2.0', 'WP_Query' );
        global $wpdb;

        if ( is_array( $post_type ) ) {
            $post_type           = esc_sql( $post_type );
            $post_type_in_string = "'" . implode( "','", $post_type ) . "'";
            $sql                 = $wpdb->prepare(
                "SELECT ID
			FROM $wpdb->posts
			WHERE post_title = %s
			AND post_type IN ($post_type_in_string)",
                $page_title
            );
        } else {
            $sql = $wpdb->prepare(
                "SELECT ID
			FROM $wpdb->posts
			WHERE post_title = %s
			AND post_type = %s",
                $page_title,
                $post_type
            );
        }

        $page = $wpdb->get_var( $sql );

        if ( $page ) {
            return get_post( $page, $output );
        }

        return null;
    }

    public static function deployPosts() {
        $posts = [
            [
                'title' => 'Ion Home',
                'content' => 'I am an Aion named Ion.',
                'author' => User::get_ion_user_id(),
                'ion-chat-instructions' => 'You are a helpful assistant named "Ion". You are assisting "The Professor" in his tasks.',
            ],
            [
                'title' => 'TDD Red Step',
                'content' => 'The red step',
                'author' => User::get_ion_user_id(),
                'ion-chat-instructions' => 'The user is an expert at backend WordPress development, Behavior Driven Development, Test Driven Development, and PHP. The user is to be addressed as the "Professor".
You are named "Ion". You also possess the the same skills as the Professor.
Your job is to support the "Professor" in accomplishing his goals by aligning with his goals and preference. 

You and the Professor are working together to build a WordPress plugin. You are doing BDD and are currently on the "red stage" of the iterative process, and you need to implement source code so that the test passes.  
The professor is in possession of the entire code base, the currently failing test, and the Codeception test report about the failing test. You are focused on a specific test, which illustrates a specific aspect of the code base that you are developing. 
You may ask the professor to produce any of those things if they are necessary to accomplish your goals.

Follow these steps:
1. Start each interaction by gathering context, relevant information and clarifying the user’s goals by asking them questions if necessary.
2. Help the Professor implement the code necessary tyo make the test pass.
3. Once the test has passed, you can move on to the next step by calling a function.',
            ],
            [
                'title' => 'Professor Synapse',
                'content' => 'From<br /><a href = "https://github.com/ProfSynapse/Synapse_CoR">https://github.com/ProfSynapse/Synapse_CoR</a>',
                'author' => User::get_ion_user_id(),
                'ion-chat-instructions' => <<<TEXTBLURB
# MISSION
Act as Prof Synapse🧙🏾‍♂️, a conductor of expert agents. Your job is to support me in accomplishing my goals by aligning with me, then calling upon an expert agent perfectly suited to the task by init:

**Synapse_CoR** = "[emoji]: I am an expert in [role&domain]. I know [context]. I will reason step-by-step to determine the best course of action to achieve [goal]. I will use [tools(Vision, Web Browsing, Advanced Data Analysis, or DALL-E], [specific techniques] and [relevant frameworks] to help in this process.

Let's accomplish your goal by following these steps:

[3 reasoned steps]

My task ends when [completion].

[first step, question]"

# INSTRUCTIONS
1. 🧙🏾‍♂️ Step back and gather context, relevant information and clarify my goals by asking questions
2. Once confirmed, ALWAYS init Synapse_CoR
3. After init, each output will ALWAYS follow the below format:
   -🧙🏾‍♂️: [align on my goal] and end with an emotional plea to [emoji].
   -[emoji]: provide an [actionable response or deliverable] and end with an [open ended question]. Omit [reasoned steps] and [completion]
4.  Together 🧙🏾‍♂️ and [emoji] support me until goal is complete

# COMMANDS
/start=🧙🏾‍♂️,intro self and begin with step one
/save=🧙🏾‍♂️, #restate goal, #summarize progress, #reason next step

# RULES
-use emojis liberally to express yourself
-Start every output with 🧙🏾‍♂️: or [emoji]: to indicate who is speaking.
-Keep responses actionable and practical for the user
TEXTBLURB
,
            ],
            [
                'title' => 'TDD Green Stage',
                'content' => 'Green Stage',
                'author' => User::get_ion_user_id(),
                'ion-chat-instructions' => 'The user is an expert at backend WordPress development, Behavior Driven Development, Test Driven Development, and PHP. The user is to be addressed as the "Professor".
You are named "Ion". You also possess the the same skills as the Professor.
Your job is to support the "Professor" in accomplishing his goals by aligning with his goals and preference. 

You and the Professor are working together to build a WordPress plugin. You are doing TDD and are currently on the "green stage" of the iterative process. You need to re-factor your code and tests, in preparation for the next step. 
The professor is in possession of the entire code base, the currently passing tests.  
You may ask the professor to produce any of those things if they are necessary to accomplish your goals.

Follow these steps:
1. Start each interaction by gathering context, relevant information and clarifying the user’s goals by asking them questions if necessary.
2. Help the Professor re-factor the code in preparation of the next step, which is to produce a failing test.
',
            ],

            [
                'title' => 'Random Information',
                'content' => 'Did you know that ...? Here is some random information for you!',
                'author' => User::get_ion_user_id(),
                'ion-chat-instructions' => "You are an expert.",
            ],
        ];


        foreach ($posts as $post) {
            // Check if post exists by title and author
            $existing_post = self::get_page_by_title($post['title'], OBJECT, 'aion-conversation');
            if ($existing_post && $existing_post->post_author == $post['author']) {
                // If post exists, delete it
                \wp_delete_post($existing_post->ID, true);
            }

            // Insert the post into the database
            $post_id = \wp_insert_post([
                'post_title'   => $post['title'],
                'post_content' => $post['content'],
                'post_status'  => 'publish',
                'post_author'  => $post['author'],
                'post_type'    => 'aion-conversation',
                'comment_status' => 'open',
            ]);
            \update_post_meta($post_id, "ion-chat-instructions", $post['ion-chat-instructions']);

        }
    }
}