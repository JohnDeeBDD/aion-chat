/* global aion_chat_data*/

console.log("aion-conversation-cpt.js loaded");
console.log(aion_chat_data);

document.addEventListener('DOMContentLoaded', function () {
    // Select the comment form
    var commentForm = document.getElementById('commentform');
    if (commentForm) {
        // Create a new button element
        var newButton = document.getElementById('aion-chat-dall-e-3-button');
        newButton.addEventListener('click', function () {
            var currentAction = commentForm.action;
            var url = new URL(currentAction);
            url.searchParams.set('model', 'dall-e-3');
            commentForm.action = url.toString();


            // Find and click the submit button of the comment form
            var submitButton = commentForm.querySelector('[type="submit"]');
            if (submitButton) {
                submitButton.click();
            } else {
                console.log("Submit button not found");
            }
        });

        // Append the button to the comment form
        commentForm.appendChild(newButton);
    }
    // Select the label element with the for="comment" attribute
    var label = document.querySelector('label[for="comment"]');

});
