<?php

use AionChatMothership\User;

class AionChatMothership_UserTest extends \Codeception\TestCase\WPTestCase {
    private $userId;
    private $remoteSiteUrl = 'https://example.com';
    private $remoteUserName = 'testuser';
    private $applicationPassword = 'testpass';

    protected function _before() {
        // Given a test user
        $this->userId = $this->factory->user->create(['role' => 'subscriber']);
    }

    protected function _after() {
        // Clean up after tests
        \delete_user_meta($this->userId, 'remote_app_password_' . md5($this->remoteSiteUrl));
    }

    public function testAddRemoteApplicationPassword() {
        // When we add a remote application password
        $metaKey = User::addRemoteApplicationPassword($this->userId, $this->remoteSiteUrl, $this->remoteUserName, $this->applicationPassword);

        // Calculate expected meta key based on the method's logic
        $expectedMetaKey = 'remote_app_password_' . md5(urlencode($this->remoteSiteUrl));

        // Output the expected and actual metaKey for debugging
        codecept_debug('Expected Meta Key: ' . $expectedMetaKey);
        codecept_debug('Actual Meta Key: ' . $metaKey);

        // Then the returned meta key should match the expected format
        $this->assertEquals($expectedMetaKey, $metaKey, "The meta key does not match the expected format. Expected: {$expectedMetaKey}, Actual: {$metaKey}");
    }


    // Test retrieving a remote application password
    public function testGetRemoteApplicationPassword() {
        // Given a remote application password is added
        User::addRemoteApplicationPassword($this->userId, $this->remoteSiteUrl, $this->remoteUserName, $this->applicationPassword);

        // When we retrieve the remote application password
        $passwordData = User::getRemoteApplicationPassword($this->userId, $this->remoteSiteUrl);

        // Then we should get the correct password data
        $this->assertIsArray($passwordData);
        $this->assertEquals($this->remoteSiteUrl, $passwordData['remoteSiteUrl']);
        $this->assertEquals($this->remoteUserName, $passwordData['remoteUserName']);
        $this->assertEquals($this->applicationPassword, $passwordData['applicationPassword']);
    }

    public function testDeleteRemoteApplicationPassword() {
        // Given a remote application password is added
        User::addRemoteApplicationPassword($this->userId, $this->remoteSiteUrl, $this->remoteUserName, $this->applicationPassword);

        // Debug: Confirm password is added
        $passwordDataBeforeDelete = User::getRemoteApplicationPassword($this->userId, $this->remoteSiteUrl);
        codecept_debug('Password Data Before Delete: ' . print_r($passwordDataBeforeDelete, true));

        // When we delete the remote application password
        $deleteSuccess = User::deleteRemoteApplicationPassword($this->userId, $this->remoteSiteUrl);

        // Debug: Output the result of the delete operation
        codecept_debug('Delete Operation Result: ' . ($deleteSuccess ? 'Success' : 'Failure'));

        // Debug: Check if the password still exists
        $passwordDataAfterDelete = User::getRemoteApplicationPassword($this->userId, $this->remoteSiteUrl);
        codecept_debug('Password Data After Delete: ' . print_r($passwordDataAfterDelete, true));

        // Then the deletion should be successful and the password data should be empty
        $this->assertTrue($deleteSuccess);
        $this->assertEmpty($passwordDataAfterDelete);
    }


}
