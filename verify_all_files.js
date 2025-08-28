const fs = require('fs');
const path = require('path');
const DeploymentVerifier = require('./deploy_verify');

class BatchVerifier {
    constructor() {
        this.verifier = new DeploymentVerifier();
        this.ignoredPatterns = [
            /node_modules/,
            /\.git/,
            /\.vscode/,
            /deploy_verify\.js/,
            /auto_deploy_verify\.js/,
            /verify_all_files\.js/,
            /package.*\.json/,
            /\.log$/,
            /\.tmp$/,
            /\.DS_Store/,
            /Thumbs\.db/
        ];
    }

    // Get all PHP files in the project
    getAllProjectFiles(dir = '.', fileList = []) {
        const files = fs.readdirSync(dir);

        files.forEach(file => {
            const filePath = path.join(dir, file);
            const stat = fs.statSync(filePath);

            if (stat.isDirectory()) {
                // Skip ignored directories
                if (!this.shouldIgnore(filePath)) {
                    this.getAllProjectFiles(filePath, fileList);
                }
            } else {
                // Include PHP, CSS, JS, HTML files
                if (this.isProjectFile(filePath) && !this.shouldIgnore(filePath)) {
                    fileList.push(filePath);
                }
            }
        });

        return fileList;
    }

    isProjectFile(filePath) {
        const ext = path.extname(filePath).toLowerCase();
        return ['.php', '.css', '.js', '.html', '.htm', '.sql'].includes(ext);
    }

    shouldIgnore(filePath) {
        return this.ignoredPatterns.some(pattern => pattern.test(filePath));
    }

    async verifyAllFiles() {
        console.log('🔍 Scanning project for files to verify...');
        
        const projectFiles = this.getAllProjectFiles();
        console.log(`📊 Found ${projectFiles.length} project files to verify`);

        if (projectFiles.length === 0) {
            console.log('ℹ️  No project files found to verify');
            return;
        }

        console.log('\n🚀 Starting batch verification...\n');

        let successCount = 0;
        let failCount = 0;

        for (let i = 0; i < projectFiles.length; i++) {
            const file = projectFiles[i];
            console.log(`[${i + 1}/${projectFiles.length}] Verifying: ${file}`);
            
            try {
                const success = await this.verifier.verifyDeployment(file);
                if (success) {
                    successCount++;
                } else {
                    failCount++;
                }
            } catch (error) {
                console.log(`❌ Error verifying ${file}: ${error.message}`);
                failCount++;
            }
            
            console.log(''); // Add spacing between files
        }

        // Summary
        console.log('📋 VERIFICATION SUMMARY');
        console.log('========================');
        console.log(`✅ Successful: ${successCount}`);
        console.log(`❌ Failed: ${failCount}`);
        console.log(`📊 Total: ${projectFiles.length}`);
        
        if (failCount === 0) {
            console.log('\n🎉 All files verified successfully!');
        } else {
            console.log(`\n⚠️  ${failCount} file(s) need attention`);
        }

        return { success: successCount, failed: failCount, total: projectFiles.length };
    }
}

// Run batch verification if script is executed directly
if (require.main === module) {
    const batchVerifier = new BatchVerifier();
    
    batchVerifier.verifyAllFiles()
        .then(results => {
            process.exit(results.failed > 0 ? 1 : 0);
        })
        .catch(error => {
            console.error('❌ Batch verification failed:', error.message);
            process.exit(1);
        });
}

module.exports = BatchVerifier;

