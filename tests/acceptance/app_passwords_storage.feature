# /var/www/html/wp-content/plugins/ion-chat/tests/acceptance/app_passwords_storage_context.php
Feature: Application Passwords Storage

  As an acceptance tester,
  I want to manage application passwords for remote sites,
  So that I can securely connect to these remote sites.

  Background:
    Given Ion is a user on the mothership

  Scenario: Displaying the 'Add New Application Password' Interface
    Given I am logged in as an admin
    When I navigate to the user-edit.php page
    Then I should see the 'Add Remote Application Password' div

  Scenario: Storing a New Application Password
    Given I am on the user-edit.php page with the 'Add Remote Application Password' div visible
    When I fill in the application password data fields and submit
    Then the new application password should be stored
    And I should see a confirmation message

  Scenario: Viewing Stored Application Passwords
    Given I have stored application passwords
    When I navigate to the application passwords section
    Then I should see a list of my stored application passwords

  Scenario: Deleting an Existing Application Password
    Given I have stored application passwords
    When I choose to delete an existing application password
    Then the application password should be removed
    And I should see a confirmation message