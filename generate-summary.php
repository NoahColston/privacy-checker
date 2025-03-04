<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$debugLogFile = '../../../private/debug_log.txt';
$json = file_get_contents("php://input");

// Check if JSON input is received
if (!$json) {
    file_put_contents($debugLogFile, "ERROR: No JSON input received.\n", FILE_APPEND);
    die(json_encode(["error" => "No JSON input received."]));
}

// Decode JSON
$dataKeys = json_decode($json, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($dataKeys)) {
    file_put_contents($debugLogFile, "ERROR: JSON decoding failed: " . json_last_error_msg() . "\n", FILE_APPEND);
    die(json_encode(["error" => "JSON decoding failed.", "json_error" => json_last_error_msg(), "raw_input" => $json]));
}

// Load API Key from config.php
require_once '../../../private/config.php';
$apiKey = HF_API_KEY ?? null;

if (!$apiKey) {
    file_put_contents($debugLogFile, "ERROR: Missing Hugging Face API key.\n", FILE_APPEND);
    die(json_encode(["error" => "Missing Hugging Face API key."]));
}

// Define types of vulnerable data
$vulnerableDescriptions = [
    "ip" => "IP address (tracks location & online activity)",
    "location" => "Physical location (GPS, city, country)",
    "device" => "Device type and OS (browser fingerprinting)",
    "browser" => "Browser information (cookies, tracking scripts)",
    "isp" => "Internet Service Provider (ISP can monitor browsing)",
    "timezone" => "Time zone (can reveal general location)"
];

// Generate a description of exposed data
$vulnerableData = [];
foreach ($dataKeys as $key) {
    if (isset($vulnerableDescriptions[$key])) {
        $vulnerableData[] = $vulnerableDescriptions[$key];
    }
}

$description = implode(", ", $vulnerableData);

$prompt = "The following types of personal data are currently visible to websites when this user visits them: $description.

- You are a cybersecurity expert writing a security report.  
- Your goal is to help the user understand how their data can be exploited and provide exact steps to protect themselves.  
- You must directly generate solutions—do NOT repeat the input.  

Security Report:

1. IP Address
- Why this is a risk: Explain why websites, hackers, or governments collect this data.
- How to protect yourself: Provide clear, actionable steps (e.g., using a VPN, disabling WebRTC, etc.).
- Expert Tip: Provide an advanced measure for extra security.

2. Time Zone
- Why this is a risk: Explain what attackers or advertisers can infer from this.
- How to protect yourself:** List exact security measures.
- Expert Tip:** Provide an advanced privacy strategy.

3. ISP & ASN
- Why this is a risk: Describe how ISPs monitor and track users.
- How to protect yourself: Provide countermeasures.
- Expert Tip: Recommend a next-level privacy method.

4. Browser Information
- Why this is a risk: Explain how browser fingerprinting works.
- How to protect yourself: Recommend privacy tools or settings.
- Expert Tip: Offer an expert-level recommendation.

Important: Start IMMEDIATELY with the first security issue. Do NOT repeat this request or provide greetings/conclusions.";

// AI Model & API Request
$hfModel = "mistralai/Mistral-7B-Instruct-v0.3";

$url = "https://api-inference.huggingface.co/models/$hfModel";

$payload = json_encode([
    "inputs" => $prompt,
    "parameters" => [
        "max_new_tokens" => 700,  // Forces AI to generate more detailed responses
        "temperature" => 0.7,  // Makes responses more natural & varied
        "do_sample" => true,  // Prevents repetitive AI responses
        "top_p" => 0.9  // Increases response diversity
    ]
]);

// Initialize cURL request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

// Execute API call
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log AI response
file_put_contents($debugLogFile, "HUGGING FACE RESPONSE:\n$response\n", FILE_APPEND);

if ($httpCode !== 200) {
    die(json_encode(["error" => "Failed to connect to Hugging Face API.", "http_code" => $httpCode]));
}

$result = json_decode($response, true);

// Extract AI response
$summaryText = $result[0]['generated_text'] ?? "AI failed to generate security instructions.";

// Remove AI repeating input at the start
if (preg_match_all('/1\. IP address/i', $summaryText, $matches, PREG_OFFSET_CAPTURE) >= 2) {
    $secondOccurrence = $matches[0][1][1]; // Get position of second occurrence
    $cleanedResponse = substr($summaryText, $secondOccurrence); // Extract everything from there onward
}

if (empty(trim($cleanedResponse))) {
    $cleanedResponse = $summaryText;
}

// Log cleaned response
file_put_contents($debugLogFile, "CLEANED AI RESPONSE:\n$cleanedResponse\n", FILE_APPEND);

// Return cleaned response
echo json_encode(["summary" => trim($cleanedResponse)]);
?>