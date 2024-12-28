<?php
// Include the Composer autoload file
require 'vendor/autoload.php';

use OpenApi\Generator as OA;

// Scan the directory where your controllers are located
$openapi = OA::scan(['src/Controller']);

// Set the response header to JSON
header('Content-Type: application/json');

// Output the generated OpenAPI documentation
echo $openapi->toJson();