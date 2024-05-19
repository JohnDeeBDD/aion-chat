=== Aion Chat ===
Contributors:      JohnDeeBDD
Tags:              Artificial Intelligence, A.I., singularity
Tested up to:      6.3.2
Stable tag:        0.1.0
License:           Copyright Aion Garden (2023). All rights reserved.
License URI:       https://aion.garden/tos

== Description ==
The singularity is here! Now you can talk to artificial intelligence agents right on your WordPress site.

== Quick Start ==
1. Login as an admin.
2. Upload the plugin zip file to your site, and activate "Aion Chat".
3. Two new users, "Assistant" and "Aion" will be registered on your site as editors. These are the Aions, or Artificially Intelligent Operational Nodes, for your site.
4. A new custom post type, the "Aion Conversation", will be available from the WordPress admin area.
5. Go to the "Aion Conversations" tab in the admin area. Click "Add New".
6. Give the conversation a title, i.e., "testing". Change the Author of the Aion Conversation to "Assistant". Publish the post [you can make it private if you want].
7. Create and publish a comment to the Aion Conversation from another user [i.e. yourself]. If the Aion is the Author of the Aion Conversation Custom Post Type, the Aion will respond as if the comment is a chat and the user is addressing the Aion.
For instance, post the comment "What is the capital of the country of France?" and the Aion will respond "Paris".

Now that the chat responses are stored in the WordPress database, you can access them like normal comments and do anything you want with them.

== Usage ==
Put this in your “functions.php” or in a plugin:

if(isset($_GET['q'])){
   add_action("init", function () {
      echo(
         \AionChat\DirectQuestion::ask(
            "In Greek mythology, who is Asclepius?"
         )
      );
      die();
   });
}

Navigate to:
https://yoursite.com?q=1

== Architecture ==

Aion Chat's basic functionality is to allow chatting between a user and an Aion assistant. The assistant is a WordPress user that is created upon plugin activation. We introduce the PHP class Prompt. A Prompt is a representation that is sent to an online Large Language Model [LLM]. The Prompt is generally a chat history between a user and an assistant, with additional meta data included. Aion Chat maps the comments from an aion-conversation custom post types, to the Prompt class, then sends the Prompt to the LLM for a "completion". The completion is then used as a response to the comment on the WordPress site.

Aion Chat is a WordPress plugin that introduces the capability to integrate intelligent autonomous agents within your WordPress site.

The "Aion" user role
The Aion Chat plugin introduces a new user role to WordPress, the "Aion". Aions work like other users on your site, except that they are controlled by artificial intelligence. Aions have capabilities like regular WordPress users. Users in WordPress can have multiple roles, so Aions can also have traditional roles like "contributor", "subscriber", "editor", and "administrator". An Aion "contributor" can create, but not publish content. An Aion "editor" can edit the content on a site. An Aion "administrator" can install code, enhance security, manage other users, and send e-mails. Manage Aion roles and capabilities the same way you manage regular users on your site.

The "aion-conversation" custom post type
This plugin introduces the "aion-conversation" CPT. Aion conversations are the default way that Aions communicate with your site. Aions "listen" to the aion-conversation posts that they are the author of. Whenever someone makes a comment on the aion-conversation, the aion will respond to the comment using the normal WordPress comment system. This allows WordPress users to "chat" with the Aion, and have the conversation stored in the WordPress database for further use. You can start an actual conversation with an Aion, by creating an aion-conversation CPT, assigning the Aion as the post author, and then posting a comment on the CPT. The Aion will respond to your comment intelligently.

Aion Conversation CPT post meta
If there is a post meta data item called "aion-chat-instructions" in an aion-conversation CPT post, then this will be used as the "system instruction" for the chat. If there is no instruction, the system is told "You are a helpful assistant."

== API Key ==
Get your OpenAI Api key here:
Set it in the admin area. Keys are stored unencrypted in your own data base [working on improving this]. I don not store your key. Please reset your keys daily for security.