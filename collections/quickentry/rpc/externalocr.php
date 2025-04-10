<?php

// $response = shell_exec("curl https://catfact.ninja/fact");

// // Decode the JSON response
// $decodedResponse = json_decode($response, true);

// // Define default variables
// $ocr_catalogNumber = 0;
// $ocr_collectionCode = 0;
// $ocr_country = '';
// $ocr_donatedBy = '';
// $ocr_eventDate = '';
// $ocr_institutionCode = 0;
// $ocr_occurrenceID = 0;
// $ocr_recordedBy = '';
// $ocr_scientificName = '';
// $ocr_yearDonated = 0;
// $ocr_fact = '';
// $ocr_length = 0;

// // Update variables if the JSON response is valid
// if ($decodedResponse !== null && is_array($decodedResponse)) {
//     $catalogNumber = $decodedResponse['catalogNumber'] ?? $catalogNumber;
//     $collectionCode = $decodedResponse['collectionCode'] ?? $collectionCode;
//     $country = $decodedResponse['country'] ?? $country;
//     $donatedBy = $decodedResponse['donatedBy'] ?? $donatedBy;
//     $eventDate = $decodedResponse['eventDate'] ?? $eventDate;
//     $institutionCode = $decodedResponse['institutionCode'] ?? $institutionCode;
//     $occurrenceID = $decodedResponse['occurrenceID'] ?? $occurrenceID;
//     $recordedBy = $decodedResponse['recordedBy'] ?? $recordedBy;
//     $scientificName = $decodedResponse['scientificName'] ?? $scientificName;
//     $yearDonated = $decodedResponse['yearDonated'] ?? $yearDonated;
//     $fact = $decodedResponse['fact'] ?? $fact;
//     $length = $decodedResponse['length'] ?? $length;
// }

// // Return the raw JSON response directly
// header('Content-Type: application/json');
// echo $response;

header('Content-Type: application/json'); // Set JSON response header

// Fetch JSON from catfact.ninja
$catFactJson = file_get_contents('https://catfact.ninja/fact');

// Check for fetch errors
if ($catFactJson === FALSE) {
    echo json_encode([
        "error" => "Failed to retrieve cat fact."
    ]);
    exit;
}

// Decode JSON
$catFact = json_decode($catFactJson, true);

// If decoding fails
if ($catFact === NULL) {
    echo json_encode([
        "error" => "Failed to decode cat fact JSON."
    ]);
    exit;
}

// Return the JSON
echo json_encode([
    "fact" => $catFact['fact'],
    "length" => $catFact['length']
]);

