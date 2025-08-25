const fs = require('fs');
const path = require('path');
const ftp = require('basic-ftp');
const crypto = require('crypto');

class DeploymentVerifier {
    constructor() {
        this.ftpConfig = {
            host: 'ftp.sevakaro.in',
            port: 21,
            user: 'u231942554',
            password: 'Waheguru@12345',
            remotePath: '/public_html/volunteer/volunteer_app/'
        };
    }

    // Calculate MD5 hash of file content
    calculateHash(content) {
        return crypto.createHash('md5').update(content).digest('hex');
    }

    // Get local file content
    getLocalFileContent(filePath) {
        try {
            return fs.readFileSync(filePath, 'utf8');
        } catch (error) {
            throw new Error(`Failed to read local file: ${error.message}`);
        }
    }

    // Get remote file content via FTP
    async getRemoteFileContent(client, remotePath) {
        try {
            const tempFile = path.join(__dirname, '.temp_download');
            await client.downloadTo(tempFile, remotePath);
            const content = fs.readFileSync(tempFile, 'utf8');
            fs.unlinkSync(tempFile); // Clean up temp file
            return content;
        } catch (error) {
            throw new Error(`Failed to download remote file: ${error.message}`);
        }
    }

    // Main verification function
    async verifyDeployment(localFilePath, relativeRemotePath = null) {
        const client = new ftp.Client();
        
        try {
            // Connect to FTP server
            await client.access(this.ftpConfig);
            console.log('🔌 Connected to FTP server');

            // Determine remote file path
            const remotePath = relativeRemotePath || localFilePath.replace(/\\/g, '/');
            const fullRemotePath = this.ftpConfig.remotePath + remotePath;

            console.log(`📁 Verifying: ${localFilePath} → ${fullRemotePath}`);

            // Get local file content
            const localContent = this.getLocalFileContent(localFilePath);
            const localHash = this.calculateHash(localContent);

            // Get remote file content
            const remoteContent = await this.getRemoteFileContent(client, fullRemotePath);
            const remoteHash = this.calculateHash(remoteContent);

            // Compare hashes
            if (localHash === remoteHash) {
                console.log('✅ Deployment Verified');
                console.log(`   Local Hash:  ${localHash}`);
                console.log(`   Remote Hash: ${remoteHash}`);
                return true;
            } else {
                console.log('❌ Deployment Failed - Content Mismatch');
                console.log(`   Local Hash:  ${localHash}`);
                console.log(`   Remote Hash: ${remoteHash}`);
                console.log(`   Local Size:  ${localContent.length} bytes`);
                console.log(`   Remote Size: ${remoteContent.length} bytes`);
                return false;
            }

        } catch (error) {
            console.log('❌ Deployment Failed');
            console.log(`   Error: ${error.message}`);
            return false;
        } finally {
            client.close();
        }
    }

    // Batch verify multiple files
    async verifyMultipleFiles(fileList) {
        const results = [];
        for (const filePath of fileList) {
            const result = await this.verifyDeployment(filePath);
            results.push({ file: filePath, verified: result });
        }
        return results;
    }
}

// Export for use in other scripts
module.exports = DeploymentVerifier;

// Command line usage
if (require.main === module) {
    const verifier = new DeploymentVerifier();
    const filePath = process.argv[2];
    
    if (!filePath) {
        console.log('Usage: node deploy_verify.js <file-path>');
        process.exit(1);
    }

    verifier.verifyDeployment(filePath)
        .then(success => process.exit(success ? 0 : 1))
        .catch(error => {
            console.error('❌ Verification Error:', error.message);
            process.exit(1);
        });
}
