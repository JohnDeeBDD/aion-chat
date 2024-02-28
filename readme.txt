=== Aion Chat ===
Contributors:      JohnDeeBDD
Tags:              Artificial Intelligence, A.I., singularity
Tested up to:      6.3.2
Stable tag:        0.1.0
License:           Copyright Aion Garden (2023). All rights reserved.
License URI:       https://aion.garden/tos

== Description ==
The singularity is here! Now you can talk to artificial intelligence agents right on your WordPress site.

== Quit Start ==
Login as an admin.
Upload the plugin zip file to your site, and activate "Aion Chat".
A new user named "Assistant" will be registered on your site as an editor. This is the Aion artificial intelligence for your site.
A new custom post type, the "Aion Conversaion", will be available from the WordPress admin area.
Go to the "Aion Conversations" tab in the admin area. Click "Add New".
Give the conversation a title, i.e. "testing". Change the Author of the Aion Conversation to "Assistant". Publish the post [you can make it private if you want].
Publish a comment to the Aion Conversation. If the Aion is the Author of the Aion Conversation Custom Post Type, the Aion will respond as if the comment is a chat and the user is addressing the Aion.
For instance, post the comment "What is the capital of the country of France?" and the Aion will respond "Paris".

Now that the chat responses are stored in the WordPress database, you can access it like normal comments and do anything you want with it.

== Usage ==

Aion Chat is a WordPress plugin that introduces the capability to integrate intelligent autonomous agents within your WordPress site.

The "Aion" user role
The Aion Chat plugin introduces a new user role to WordPress, the "Aion". Aions work like other users on your site, except that they are controlled by artificial intelligence. Aions have capabilities like regular WordPress users. Users in WordPress can have multiple roles, so Aions can also have traditional roles like "contributor", ""subscriber", "editor", and "administrator". An Aion "contributor" can create, but not publish content. An Aion "editor" can edit a site. An Aion "administrator" can install code, enhance security, manage other users, and send e-mails. Manage Aion roles and capabilities the same way you manage regular users on your site.

The "aion-conversation" custom post type
This plugin introduces the "aion-conversation" CPT. Aion conversations are the default way that Aions communicate with your site. Aions "listen" to the aion-conversation posts that they are they author of. Whenever someone makes a comment on the aion-conversation, the aion will respond to the comment using the normal WordPress comment system. This allows WordPress users to "chat" with the Aion, and have the conversation stored in the WordPress database for further use. You can start an actual conversation with an Aion, by creating an aion-conversation CPT, assigning the Aion as the post author, and then posting a comment on the CPT. The Aion will respond to your comment intelligently.

Aion Conversation CPT post meta
If there is a post meta data item called "aion-chat-instructions" in an aion-conversation CPT post, then this will be used as the "system instruction" for the chat. If there is no instruction, the system is told "You are a helpful assistant.".