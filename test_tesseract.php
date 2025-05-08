<?php
// Define the paths for the input image and output file
$inputImage = 'test_tesseract.jpg'; // Replace with the path to your test image
$outputFile = 'output_text';    // Output base file name (Tesseract will add .txt)

// Define the Tesseract command
$tesseractPath = '/usr/local/bin/tesseract'; // Adjust if Tesseract is in a different location
$command = $tesseractPath . ' ' . escapeshellarg($inputImage) . ' ' . escapeshellarg($outputFile);

// Execute the Tesseract command
exec($command, $output, $returnVar);

// Handle the result
if ($returnVar === 0) {
    echo "Tesseract OCR executed successfully.\n";
    echo "Output text file: " . $outputFile . ".txt\n";

    // Optionally, display the content of the output file
    $outputText = file_get_contents($outputFile . '.txt');
    echo "Extracted Text:\n$outputText\n";
} else {
    echo "Tesseract OCR failed with return code: $returnVar\n";
    echo "Command Output:\n";
    print_r($output);
}

?>