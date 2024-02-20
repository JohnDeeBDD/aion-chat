# /var/www/html/wp-content/plugins/ion-chat/tests/acceptance/test_stubs_are_deployed_context.php
# Note: This feature can be run in 3 separate contexts, those contexts are set in the yml file

Feature: Basic intelligent response

  As an acceptance tester
  I want to receive intelligent replies in aion-conversations
  So that I can receive a basic intelligent response


  Scenario: the plugin is setup on localhost
    Given the plugin is setup on localhost

   Scenario Outline: Ion replies to comments
    When I make a comment with text "<post_comment>"
    Then I should see an intelligent response "<response>"

    Examples:
      | post_comment                              | response    |
      | What is the capital city of France        | Paris       |
      | What is the tallest structure in that city? | Eiffel Tower |

  Scenario: tear down after
    When the feature test is done the post is deleted