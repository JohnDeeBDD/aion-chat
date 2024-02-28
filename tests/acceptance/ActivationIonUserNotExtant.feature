Feature: Ion Chat Plugin Activation
  As a WordPress admin on a remote site
  I want to activate the Ion Chat plugin
  So that I can use the plugin

  Scenario: There is no Ion user
    Given there is no user with the email address "jiminac@aol.com"
    When the plugin is activated
    Then a user should be created with the email "jiminac@aol.com", username "Ion"
    And an email should be sent to Ion with a password
    And the remote fires a ping to the mothership