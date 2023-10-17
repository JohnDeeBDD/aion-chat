# Feature: The traffic controller
# Controls the direction our custom class "Prompts" move, to and from the remote node and mothership server.
# Context: /var/www/html/wp-content/plugins/ion-chat/tests/_support/AcceptanceTester.php
Feature: Prompts Traffic Controller

  Scenario: A comment is posted on the mothership server
    Given the server I_P_s are readable in the servers.json file
    And there is a mothership server
    And there is a stub post called Chat on the mothership
    When a comment is published on the mothership server
    And the comment is of a tracked post
    Then a Prompt is created on the mothership server
    And the Prompt is visable in the debug
    And the Prompt is sent to ChatGPT
    And the mothership server gets the reply from ChatGPT

  # Scenario: A comment is posted on the remote node
  # Remote nodes are individual servers that communicate with the mothership.
  Scenario: A comment is posted on the remote node
    Given there is a remote node
    #And there is a mothership server
    #And there is a stub post called Chat on the mothership
    #When a comment is published on the remote node
    #And the comment is of a tracked post
    #Then a Prompt is created on the remote node
    #And the Prompt is sent to the mothership server
    #And the Prompt is sent to ChatGPT
    #And the mothership server gets the reply from ChatGPT
    #And the Prompt is sent back to the remote node
    #And the Prompt is received on the remote node


