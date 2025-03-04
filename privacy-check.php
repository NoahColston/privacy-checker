<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <?php include '../../includes/header.php'; ?>

    <section class="privacy-check">

        <div class="privacy-check__header">
            <h1 class="privacy-check__title">Privacy Awareness Check</h1>
            <p class="privacy-check__description"><strong>Warning:</strong> The following information may be visible when you visit a website.</p>
            <p class="privacy-check__description">Click the button below to see what data is exposed and receive security recommendations.</p>

            <h2 class="privacy-check__subtitle">Features</h2>
            <div class="list--centered">
                <ul>
                    <li>Identifies user-exposed data such as IP address, browser info, and ISP details.</li>
                    <li>AI-generated security recommendations based on detected vulnerabilities.</li>
                    <li>Interactive UI with real-time privacy check functionality.</li>
                </ul>
            </div>
            <h2 class="privacy-check__subtitle">Technologies Used</h2>
                    <div class="list--centered">
                        <ul>
                            <li><strong>Frontend:</strong> HTML, CSS, JavaScript</li>
                            <li><strong>Backend:</strong> PHP</li>
                            <li><strong>APIs:</strong> Hugging Face AI, IP Geolocation</li>
                            <li><strong>Hosting:</strong> cPanel</li>
                        </ul>
                    </div>
            <div class="button-container">
                <button id="runCheck" class="button button--secondary">Run Privacy Check</button>
                <button id="clearData" class="button button--secondary" style="display:none;">Clear Data</button>
            </div>
        </div>



        <div class="privacy-check__results" id="results" style="display:none;">
            <h2 class="privacy-check__subtitle">Your Exposed Data</h2>
            <div id="output"></div>
            <br>
            <h2 class="privacy-check__subtitle">How to Protect Your Data</h2>
            <div id="ai-summary">Loading security recommendations...</div>
        </div>
    </section>
    <?php include '../../includes/footer.php'; ?>
</body>

</html>

<script>
    document.getElementById("runCheck").addEventListener("click", function() {
        document.getElementById("results").style.display = "block";
        document.getElementById("clearData").style.display = "inline-block";
        document.getElementById("ai-summary").innerText = "Generating security recommendations...";

        fetch("fetch-data.php")
            .then(response => response.json())
            .then(data => {
                console.log("User Data Received:", data);

                // Display user-exposed data in a well-formatted way
                let output = "";
                for (const key in data) {
                    output += `<p><strong>${key}:</strong> ${data[key]}</p>`;
                }
                document.getElementById("output").innerHTML = output;

                return fetch("generate-summary.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(Object.keys(data)) // Send only the data keys
                });
            })
            .then(response => response.json())
            .then(result => {
                console.log("AI Security Advice Received:", result);

                if (result.error) {
                    document.getElementById("ai-summary").innerText = "Error: " + result.error;
                } else {
                    // Format AI-generated security recommendations properly
                    let formattedSummary = result.summary
                        .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
                        .replace(/\n- /g, "<br>") // Replace bullet points with a clearer symbol
                        .replace(/(^|\n)\d+\./g,
                            "$1<br><br>") // Format section headers (Remove 1., 2., 3., etc.)
                        .replace(/Expert Tip:/g,
                            "<br>💡 <strong>Expert Tip:</strong>") // Highlight expert tips
                        .replace(/\b(IP Address|Time Zone|ISP & ASN|Browser Information)\b/g, "<h2 class='privacy-check__subtitle'>$1</h2>") 


                        .replace(/\n/g, "<br>"); // Ensure proper line breaks

                    document.getElementById("ai-summary").innerHTML = formattedSummary;
                }
            })
            .catch(error => {
                console.error("Error fetching AI summary:", error);
                document.getElementById("ai-summary").innerText = "Error: Unable to generate security advice.";
            });
    });

    document.getElementById("clearData").addEventListener("click", function() {
        document.getElementById("results").style.display = "none";
        document.getElementById("clearData").style.display = "none";
    });
</script>