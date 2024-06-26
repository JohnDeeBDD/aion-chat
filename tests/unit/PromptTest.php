<?php

use AionChat\Exception;
use AionChat\Prompt;

class PromptTest extends \Codeception\TestCase\WPTestCase{

    private $post_id;
    private $comment_id;
    private $user_id;
    private $user_email;

    public function setUp(): void {
        parent::setUp();
        require_once('/var/www/html/wp-content/plugins/aion-chat/src/AionChat/autoloader.php');
        $user = get_user_by('login', "Codeception");
        $this->user_id = $user->ID;
        $this->user_email = "codeception@email.com";

        // Existing logic to fetch the post
        $query = new \WP_Query([
            'name' => 'first-chat',
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 1
        ]);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = \get_the_ID();
                $this->post_id = $post_id;
            }
            \wp_reset_postdata();
        }
    }

	/**
	 * @test
	 * it should be instantiable
	 */
	public function isShouldBeInstantiable(){
       // $Prompt = Prompt::instantiate_dummy();
    }

    /**
     * @test
     * Prompt validator test
     */
    public function PromptValidatorTest()
    {
       // $Prompt = (Prompt::instantiate_dummy());
       // $string = serialize($Prompt);
       // \PHPUnit\Framework\assertEquals(true, self::isSerializedPrompt($string));
    }

    /**
     * Determines if a given string is a serialized instance of the "Prompt" class.
     *
     * This function performs the following steps:
     * 1. Checks if the string contains the class name "Prompt".
     * 2. Uses a custom error handler to catch any errors during unserialization.
     * 3. Attempts to unserialize the string and checks if the resulting object is an instance of "Prompt".
     *
     * @param string $str The string to check.
     * @return bool True if the string is a serialized instance of "Prompt", false otherwise.
     * @throws Exception If there's an error during unserialization.
     */
    public static function isSerializedPrompt($str) {
        // Step 1: Check if the string contains the class name
        if (strpos($str, 'Prompt') === false) {
            return false;
        }

        // Step 2: Use a custom error handler
        set_error_handler(function($errno, $errstr) {
            throw new \Exception($errstr);
        });

        try {
            $object = unserialize($str);

            // Step 3: Check the type of the unserialized object
            if ($object instanceof Prompt) {
                restore_error_handler();
                return true;
            }
        } catch (\Exception $e) {
            // Unserialization failed
        }

        //restore_error_handler();
        return false;
    }


    public function testInitThisPromptMethodExists()
    {
        $this->assertTrue(
            method_exists('AionChat\Prompt', 'init_this_prompt'),
            'Method init_this_prompt does not exist in Prompt class'
        );
    }

}

