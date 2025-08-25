# 🚀 Automatic Deployment Setup for Volunteer App

This setup provides automatic FTP deployment to your Hostinger server with verification.

## 📋 Features

- ✅ **Auto-upload on save** - Files automatically upload when you save them in Cursor
- ✅ **Deployment verification** - Checks if uploaded files match local files
- ✅ **Success/failure feedback** - Clear visual feedback with ✅ or ❌ status
- ✅ **Incremental uploads** - Only changed files are uploaded
- ✅ **Batch verification** - Verify all project files at once

## 🛠️ Setup Instructions

### 1. Install Dependencies
```bash
npm install
```

### 2. Install SFTP Extension
- Open Cursor
- Go to Extensions (Ctrl+Shift+X)
- Search for "SFTP" by Natizyskunk
- Install the extension

### 3. Configuration Files
The following files are already configured:
- `.vscode/sftp.json` - SFTP configuration for Hostinger
- `deploy_verify.js` - File verification script
- `auto_deploy_verify.js` - Automatic verification watcher

## 🎯 How It Works

1. **Save a file** in Cursor → File automatically uploads to Hostinger
2. **Verification runs** → Compares local and remote file content
3. **Status display** → Shows ✅ Deployment Verified or ❌ Deployment Failed

## 📖 Usage Commands

### Verify Single File
```bash
npm run verify path/to/your/file.php
```

### Verify All Project Files
```bash
npm run verify-all
```

### Start Auto-Verification Watcher
```bash
node auto_deploy_verify.js
```

## 🔧 Server Configuration

- **Host:** ftp.sevakaro.in
- **Protocol:** FTP
- **Port:** 21
- **Remote Path:** /public_html/volunteer/volunteer_app/
- **Live URL:** https://sevakaro.in/volunteer/volunteer_app/

## 📁 File Structure

```
volunteer_app/
├── .vscode/
│   └── sftp.json                 # SFTP configuration
├── deploy_verify.js              # Verification script
├── auto_deploy_verify.js         # Auto-verification watcher
├── verify_all_files.js           # Batch verification
├── package.json                  # Node.js dependencies
└── README_DEPLOYMENT.md          # This file
```

## 🚨 Troubleshooting

### Files Not Uploading
1. Check if SFTP extension is installed and enabled
2. Verify FTP credentials in `.vscode/sftp.json`
3. Check Cursor's output panel for error messages

### Verification Failing
1. Ensure internet connection is stable
2. Check if remote file exists on server
3. Verify FTP credentials are correct

### Extension Issues
1. Restart Cursor after installing SFTP extension
2. Try manually uploading a file using Ctrl+Shift+P → "SFTP: Upload"

## 📞 Support

If you encounter issues:
1. Check the Cursor output panel for detailed error messages
2. Verify your Hostinger FTP credentials
3. Ensure the remote directory exists and has write permissions

## 🎉 Success!

Once set up, your workflow becomes:
1. Edit file in Cursor
2. Save file (Ctrl+S)
3. See "✅ Deployment Verified" message
4. File is live at https://sevakaro.in/volunteer/volunteer_app/
