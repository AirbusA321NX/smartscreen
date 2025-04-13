SmartScreen Setup Guide
This guide will help you set up and run the SmartScreen project on your local machine.

📦 Clone the Project
git clone https://github.com/AirbusA321NX/smartscreen
Or download the config.php file directly if you only need the configuration.

📋 Requirements
PHP server

Apache server + MySQL server (recommended via XAMPP)

🛠️ Installation Steps
Download XAMPP (Localhost)
https://www.apachefriends.org/download.html

Download PHP Server

Recommended version: VS17 x64 Non Thread Safe

https://windows.php.net/download/

Extract PHP

Extract the downloaded PHP zip into your root project directory (e.g., C:\smartscreen\php).

Set Environment Variable for PHP

Add the PHP path (e.g., C:\smartscreen\php) to the system PATH.

Verify PHP Installation
Open CMD and run:
php -v

Start PHP Development Server
php -S localhost:8000

Navigate to Root Directory
cd path\to\your\project

Open in Browser
Visit http://localhost:8000/ in your browser.

🌐 Recommended Browser Setup
For best results:

Use Brave Browser

Disable all security features (This app requires multiple permissions to function properly.)

🔐 Security Notice
Please be aware of the following:

All responses and images are stored locally on your system.

This app makes external API calls to:

Mistral API

Imagga API

Ensure you are aware of the data flow and privacy implications before using in production environments.

for further accuracy we use pyTesseract library which also analyzes current screen status 

