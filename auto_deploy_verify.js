const chokidar = require('chokidar');
const DeploymentVerifier = require('./deploy_verify');
const path = require('path');

class AutoDeployVerifier {
    constructor() {
        this.verifier = new DeploymentVerifier();
        this.verificationQueue = new Map();
        this.debounceTime = 2000; // Wait 2 seconds after file change before verifying
    }

    start() {
        console.log('🚀 Starting Auto Deployment Verifier...');
        console.log('📁 Watching for file changes in project directory');
        
        // Watch for file changes
        const watcher = chokidar.watch('.', {
            ignored: [
                /(^|[\/\\])\../, // ignore dotfiles
                '**/node_modules/**',
                '**/deploy_verify.js',
                '**/auto_deploy_verify.js',
                '**/package*.json',
                '**/*.log',
                '**/*.tmp'
            ],
            persistent: true,
            ignoreInitial: true
        });

        watcher
            .on('change', (filePath) => this.scheduleVerification(filePath, 'changed'))
            .on('add', (filePath) => this.scheduleVerification(filePath, 'added'))
            .on('ready', () => console.log('👀 Watching for file changes...'));

        return watcher;
    }

    scheduleVerification(filePath, action) {
        console.log(`📝 File ${action}: ${filePath}`);
        
        // Clear existing timeout for this file
        if (this.verificationQueue.has(filePath)) {
            clearTimeout(this.verificationQueue.get(filePath));
        }

        // Schedule verification after debounce time
        const timeoutId = setTimeout(async () => {
            console.log(`🔄 Verifying deployment for: ${filePath}`);
            await this.verifier.verifyDeployment(filePath);
            this.verificationQueue.delete(filePath);
        }, this.debounceTime);

        this.verificationQueue.set(filePath, timeoutId);
    }

    stop() {
        console.log('🛑 Stopping Auto Deployment Verifier...');
        // Clear all pending verifications
        for (const timeoutId of this.verificationQueue.values()) {
            clearTimeout(timeoutId);
        }
        this.verificationQueue.clear();
    }
}

// Start the auto verifier if this script is run directly
if (require.main === module) {
    const autoVerifier = new AutoDeployVerifier();
    const watcher = autoVerifier.start();

    // Graceful shutdown
    process.on('SIGINT', () => {
        console.log('\n🛑 Shutting down gracefully...');
        autoVerifier.stop();
        watcher.close();
        process.exit(0);
    });
}

module.exports = AutoDeployVerifier;
