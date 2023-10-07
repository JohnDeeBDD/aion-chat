<?php

use IonChat\Prompt;

require_once('/var/www/html/wp-content/plugins/ion-chat/src/IonChat/autoloader.php');

class PromptTest extends \Codeception\TestCase\WPTestCase{

	/**
	 * @test
	 * it should be instantiable
	 */
	public function isShouldBeInstantiable(){
        $Prompt = Prompt::instantiate_dummy();
    }

}

