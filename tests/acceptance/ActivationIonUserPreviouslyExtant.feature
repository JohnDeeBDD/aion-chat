Feature: Ion Chat Plugin Activation
  As a WordPress admin on a remote site
  I want to activate the Ion Chat plugin
  So that I can use the plugin

  Scenario: There is an Ion user previously setup
    Given there is a user with the email address "jiminac@aol.com"
    When the plugin is activated
    Then the remote should fire a ping to the mothership
