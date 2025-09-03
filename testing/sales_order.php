<?php
// Define the Flask URL
$url = "https://sturgeon-still-falcon.ngrok-free.app/agents";

// Initialize cURL session
$curl = curl_init($url);

// Set cURL options
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return response as a string
curl_setopt($curl, CURLOPT_HTTPGET, true);        // Use GET request
curl_setopt($curl, CURLOPT_TIMEOUT, 10);          // Timeout after 10 seconds
curl_setopt($curl, CURLOPT_VERBOSE, true);        // Enable verbose output

// Execute the request and store the response
$response = curl_exec($curl);

// Debug the cURL request
if ($response === false) {
    $error = curl_error($curl);
    echo "cURL Error: $error";
} else {
    // Output the response
    echo "Response from Flask server:\n";
    echo $response;
}

// Close cURL session
curl_close($curl);
?>
