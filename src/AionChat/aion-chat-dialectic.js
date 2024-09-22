console.log("aion-chat-dialectic.js is loaded");

jQuery( document ).ready(function() {
    console.log( "ready!" );
    console.log(aion_chat_data.nonce);
    console.log(aion_chat_data.post_id);
    console.log("Number of comments:");
    aion_chat_count_comments(aion_chat_data.post_id, aion_chat_data.nonce).then(console.log);

});
async function aion_chat_count_comments(postID, nonce) {
    try {
        // Define the REST API endpoint to fetch comments for a specific post
        const apiEndpoint = `/wp-json/wp/v2/comments?post=${postID}`;

        // Make an authenticated request to the REST API
        const response = await fetch(apiEndpoint, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        // Check if the response is okay
        if (!response.ok) {
            throw new Error(`Error fetching comments: ${response.statusText}`);
        }

        // Parse the response as JSON
        const comments = await response.json();

        // Return the number of comments
        console.log(comments.length);
        return comments.length;
    } catch (error) {
        console.error('Error:', error);
        return 0; // Return 0 in case of error
    }
}
