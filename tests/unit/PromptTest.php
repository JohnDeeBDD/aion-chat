<?php

use IonChat\Prompt;

require_once('/var/www/html/wp-content/plugins/ion-chat/src/IonChat/autoloader.php');

class PromptTest extends \Codeception\TestCase\WPTestCase{

    private $post_id;
    private $comment_id;
    private $user_id;
    private $user_email;

    public function setUp(): void {
        parent::setUp();

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
        $Prompt = Prompt::instantiate_dummy();
    }

    /**
     * @test
     * Prompt validator test
     */
    public function PromptValidatorTest()
    {
        $Prompt = (Prompt::instantiate_dummy());
        $string = serialize($Prompt);
        \PHPUnit\Framework\assertEquals(true, Prompt::isSerializedPrompt($string));
    }

    public function testInitThisPromptMethodExists()
    {
        $this->assertTrue(
            method_exists('IonChat\Prompt', 'init_this_prompt'),
            'Method init_this_prompt does not exist in Prompt class'
        );
    }

    /**
     * @test
     */
    public function it_initializes_prompt_with_correct_values() {
        // Given there is a class Prompt and it is instantiable
        $Prompt = new Prompt();

        // When a comment is created on $this->post_id by $this->user_id
        $comment_data = array(
            'comment_post_ID' => $this->post_id,
            'comment_content' => 'Hello, this is a comment.',
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => $this->user_id,
        );
        $this->comment_id = wp_insert_comment($comment_data);

        // Then the Prompt should initialize correctly
        $Prompt->init_this_prompt($this->comment_id, 'publish');
        $this->assertPromptHasCorrectValues($Prompt);
    }


    private function assertPromptHasCorrectValues($Prompt) {
        $this->assertEquals($this->post_id, $Prompt->post_id);
        $this->assertEquals($this->comment_id, $Prompt->comment_id);
        $this->assertEquals($this->user_id, $Prompt->user_id);
        $this->assertEquals($this->user_email, $Prompt->user_email);
    }
}

