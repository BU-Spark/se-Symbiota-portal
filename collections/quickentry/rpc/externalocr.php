<?php

$response = shell_exec("curl http://ocr-server:8050/output/1");

// Return the raw JSON response directly
header('Content-Type: application/json');
echo $response;
