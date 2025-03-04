# Privacy Awareness Tool

A web-based privacy awareness tool that reveals what data websites can see about you, including IP address, browser details, ISP information, and more. It also provides AI-generated security recommendations to improve online privacy.

---

## Overview

When you visit a website, your browser exposes more personal information than you might realize. This tool scans and displays the data that websites can see about you in real-time, then generates personalized security recommendations using AI.

---

## Features

- Detects exposed data, including IP address, location, ISP, and browser details.  
- AI-powered security advice using the Hugging Face API.  
- Real-time privacy check with instant results.  
- No data storage—this tool does not collect, store, or log any user information.  

---

## Live Demo

[Try It Online](https://noahcolston.vt.domains/projects/privacy-checker/privacy-check.php)

---

## Project Structure

```
/privacy-checker
│── privacy-check.php        # Frontend page displaying results
│── fetch-data.php           # Retrieves user-exposed data
│── generate-summary.php     # AI-generated privacy recommendations
│── README.md                # Project documentation
│── .gitignore               # Prevents committing sensitive files
```
> **Note:** The project uses a shared CSS file from the main website.
---

## **Technologies Used**

- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP  
- **APIs:**  
  - Hugging Face AI (for security recommendations)  
  - IP Geolocation API (for detecting user location)  
- **Hosting:** cPanel  

---

## **How It Works**

1. The user clicks the "Run Privacy Check" button.  
2. The tool fetches exposed data, including IP, location, browser details, and ISP.  
3. AI generates security recommendations based on detected vulnerabilities.  
4. The user receives a detailed report on how to protect their data.  

---

## **Security & Privacy**

- No user data is stored or logged.  
- Only temporary processing occurs while the page is open.  
- Designed to mitigate the risks of browser fingerprinting and tracking.  

---

## **Installation Guide**

To run this project on your local machine:

### **1. Clone the Repository**
```sh
git clone https://github.com/your-username/privacy-awareness-tool.git
cd privacy-awareness-tool
```

### **2. Set Up a Local PHP Server**
If using PHP's built-in server:
```sh
php -S localhost:8000
```
Then, open `http://localhost:8000/privacy-check.php` in your browser.

### **3. Add API Keys (If Required)**
If using AI features, edit `private/config.php` to include your Hugging Face API key.

---

## **License**

This project is open-source and available under the MIT License.

---

## **Contact**

For questions or feedback, contact:  
Email: [noahcolston@vt.edu](mailto:noahcolston@vt.edu)  
GitHub: [NoahColston](https://github.com/NoahColston)  

---
