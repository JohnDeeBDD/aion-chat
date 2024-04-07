Feature: Email Verification after Application Password Transfer

Scenario: Successful Password Transfer Followed by Email Verification
Given a remote node that successfully sent its application password to the mothership
When the mothership successfully stores the application password
Then the mothership sends a verification email to the user’s email address

Scenario: Mothership fails to send the verification Email
Given a remote node that successfully sent its application password to the mothership
When the mothership successfully stores the application password
But fails to send a verification email
Then a log entry about the failure should be created for debugging

Scenario: User confirms the email
Given the user receives the confirmation email from the mothership
When the user clicks the confirmation link in the email
Then the mothership confirms the verification in its records