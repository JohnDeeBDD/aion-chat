Feature: Linking Aion Conversations for Agentic Collaboration
  As a user viewing Aion Conversations,
  I want Aions to collaborate across separate conversations by exchanging comments
  So that communication between Aions happens

  Scenario:
    Given there are two Aion users: "Assistant" and "Aion"
    And there are two Aion Conversation posts: "ConversationA" authored by "Assistant" and "ConversationB" authored by "Aion"
    And "Aion" is the interlocutor for "ConversationA", while "Assistant" is the interlocutor for "ConversationB"
    When I make a comment on "ConversationA"
    Then I see a response