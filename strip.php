<?php
// Function to fetch a webpage using cURL
function fetch_webpage($url) {
    $ch = curl_init();

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, $url);

    // Return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Set user agent to mimic a browser
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');

    // Execute the session and store the content in a variable
    $output = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);

    return $output;
}

// Function to strip non-human readable data from the HTML
function strip_non_human_readable($html) {
    // Remove script and style tags and their content
    $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
    $html = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $html);

    // Strip all HTML tags
    $html = strip_tags($html);

    // Decode HTML entities
    $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Remove extra whitespace
    $html = trim(preg_replace('/\s+/', ' ', $html));

    return $html;
}

// URL of the webpage to fetch
$url = 'https://generalchicken.guru';

// Fetch the webpage content
$html_content = fetch_webpage($url);

// Strip non-human readable data from the content
$clean_content = strip_non_human_readable($html_content);

// Display the clean content
echo $clean_content;