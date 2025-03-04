<?php
header('Content-Type: application/json');

require_once '../../../private/config.php'; // Securely load configurations

// Get the user's IP address
$ip = $_SERVER['REMOTE_ADDR'];

// Initialize cURL request to fetch location data
$ch = curl_init("http://ip-api.com/json/$ip");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Set timeout to prevent long waits
$locationData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check if API request was successful
if ($httpCode !== 200) {
    $locationData = json_encode(["error" => "Failed to fetch location data."]);
}

$locationJson = json_decode($locationData, true);

$data = [
    'ip' => $ip,
    'city' => $locationJson['city'] ?? 'Unknown',
    'country' => $locationJson['country'] ?? 'Unknown',
    'region' => $locationJson['regionName'] ?? 'Unknown',
    'timezone' => $locationJson['timezone'] ?? 'Unknown',
    'isp' => $locationJson['isp'] ?? 'Unknown',
    'asn' => $locationJson['as'] ?? 'Unknown',
    'browser' => $_SERVER['HTTP_USER_AGENT'],
];

// Return JSON response
echo json_encode($data);
?>
