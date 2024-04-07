Feature: Connect remote_node to mothership and share Remote Application Password

  Scenario: remote_node activates the Aion Chat plugin
    Given there is an Aion assistant designated by the function \AionChat\User::get_aion_assistant_email()
    When the plugin is activated on the remote node
    And the Aion assistant does not previously exist
    Then the plugin should create the Aion assistant user on the remote_node
    And the plugin should creates an application password if they do not already exist for the Aion assistant
    And the plugin should send a POST request over HTTP to the mothership with the assistant's remote application password

  Scenario: the Mothership receives a request to store a remote application password
    Given the plugin is activated on the remote node
    When the remote_node sends a POST request over HTTP to the mothership with the application password
    Then the mothership server should store the remote application password

  Scenario: Plugin activation fails on Remote node
    Given a remote node with the plugin installed
    When the plugin activation fails on the remote node
    Then the remote node will not send any request to the Mothership

  Scenario: Post request from Remote node to mothership fails
    Given a remote node with the plugin installed and an active HTTPS connection
    When the post request to the mothership fails
    Then the mothership will not receive any application password