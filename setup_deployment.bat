@echo off
echo 🚀 Setting up Automatic Deployment for Volunteer App
echo ================================================

echo.
echo 📦 Installing Node.js dependencies...
npm install

echo.
echo 🔧 Setting up SFTP extension...
echo Please install the "SFTP" extension by Natizyskunk in Cursor if not already installed.

echo.
echo ✅ Setup complete!
echo.
echo 📖 USAGE INSTRUCTIONS:
echo =====================
echo.
echo 1. Install the SFTP extension in Cursor (if not already installed)
echo 2. The .vscode/sftp.json file is already configured for your Hostinger server
echo 3. Files will automatically upload when you save them
echo.
echo 📋 VERIFICATION COMMANDS:
echo ========================
echo.
echo • Verify a single file:     npm run verify path/to/your/file.php
echo • Verify all project files: npm run verify-all
echo • Start auto-verification:  node auto_deploy_verify.js
echo.
echo 🎯 Your files will now automatically upload to:
echo    https://sevakaro.in/volunteer/volunteer_app/
echo.
pause

