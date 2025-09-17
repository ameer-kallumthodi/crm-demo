/**
 * Telecaller Behavior & Productivity Tracking JavaScript
 * Tracks idle time, user activity, and syncs data with Laravel backend
 */

class TelecallerTracking {
    constructor() {
        this.idleThreshold = 30 * 1000; // 30 seconds in milliseconds
        this.logoutThreshold = 10 * 1000; // 10 seconds for auto-logout
        this.syncInterval = 30 * 1000; // 30 seconds
        this.isIdle = false;
        this.idleStartTime = null;
        this.idleData = [];
        this.lastActivityTime = Date.now();
        this.activityTimer = null;
        this.logoutTimer = null; // New timer for auto-logout
        this.syncTimer = null;
        this.countdownInterval = null; // Timer for countdown display
        this.isTracking = false;
        
        this.init();
    }

    init() {
        console.log('TelecallerTracking: Initializing...');
        console.log('User role ID:', window.userRoleId);
        
        // Only initialize for telecallers (role_id = 3)
        if (this.isTelecaller()) {
            console.log('TelecallerTracking: User is telecaller, starting tracking...');
            this.startTracking();
            this.setupEventListeners();
            this.startSyncTimer();
        } else {
            console.log('TelecallerTracking: User is not telecaller, skipping tracking');
        }
    }

    isTelecaller() {
        // Check if current user is a telecaller
        // This should match your role system
        return window.userRoleId === 3;
    }

    startTracking() {
        this.isTracking = true;
        console.log('TelecallerTracking: Tracking started');
        this.logActivity('tracking_started', 'User activity tracking started');
        
        // Start the initial activity timer
        this.resetActivityTimer();
    }

    stopTracking() {
        this.isTracking = false;
        this.endIdleTime();
        this.clearTimers();
        this.logActivity('tracking_stopped', 'User activity tracking stopped');
    }

    setupEventListeners() {
        // Mouse events
        ['mousedown', 'mousemove', 'mouseup', 'click'].forEach(event => {
            document.addEventListener(event, () => this.handleActivity('mouse'), true);
        });

        // Keyboard events
        ['keydown', 'keyup', 'keypress'].forEach(event => {
            document.addEventListener(event, () => this.handleActivity('keyboard'), true);
        });

        // Scroll events
        ['scroll', 'wheel'].forEach(event => {
            document.addEventListener(event, () => this.handleActivity('scroll'), true);
        });

        // Touch events for mobile
        ['touchstart', 'touchmove', 'touchend'].forEach(event => {
            document.addEventListener(event, () => this.handleActivity('touch'), true);
        });

        // Page visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.handlePageHidden();
            } else {
                this.handlePageVisible();
            }
        });

        // Page unload
        window.addEventListener('beforeunload', () => {
            this.handlePageUnload();
        });

        // Window focus/blur
        window.addEventListener('focus', () => this.handleActivity('window_focus'));
        window.addEventListener('blur', () => this.handleActivity('window_blur'));
    }

    handleActivity(type) {
        if (!this.isTracking) return;

        this.lastActivityTime = Date.now();
        
        // End idle time if user becomes active
        if (this.isIdle) {
            console.log('User became active - ending idle time');
            this.endIdleTime();
        }

        // Clear all timers and start fresh
        this.clearAllTimers();
        this.resetActivityTimer();

        console.log('Activity detected:', type, '- Timer reset');
        // Don't log every activity - only log significant ones
        // this.logActivity('user_activity', `User activity detected: ${type}`);
    }

    resetActivityTimer() {
        if (this.activityTimer) {
            clearTimeout(this.activityTimer);
        }

        this.activityTimer = setTimeout(() => {
            this.startIdleTime();
        }, this.idleThreshold);
        
        console.log('Activity timer reset - will check for idle in 30 seconds');
    }

    startIdleTime() {
        if (this.isIdle || !this.isTracking) return;

        this.isIdle = true;
        this.idleStartTime = Date.now();

        // Send to backend
        this.sendIdleTimeStart();

        // Set up auto-logout timer
        this.logoutTimer = setTimeout(() => {
            this.performAutoLogout();
        }, this.logoutThreshold);

        console.log('User became idle - auto-logout in 10 seconds');
        console.log('Idle start time:', new Date(this.idleStartTime).toLocaleTimeString());
        
        // Show countdown to user
        this.showCountdown();
    }

    endIdleTime() {
        if (!this.isIdle) return;

        const idleDuration = Date.now() - this.idleStartTime;
        
        // Store idle data for syncing
        this.idleData.push({
            start_time: new Date(this.idleStartTime).toISOString(),
            end_time: new Date().toISOString(),
            duration: idleDuration,
            type: 'general'
        });

        this.isIdle = false;
        this.idleStartTime = null;

        // Clear logout timer
        if (this.logoutTimer) {
            clearTimeout(this.logoutTimer);
            this.logoutTimer = null;
        }

        // Hide countdown
        this.hideCountdown();

        // Send to backend
        this.sendIdleTimeEnd(idleDuration);

        console.log(`User became active after ${Math.round(idleDuration / 1000)} seconds of idle time`);
    }

    sendIdleTimeStart() {
        fetch('/api/telecaller-tracking/start-idle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                idle_type: 'general'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Idle time start logged');
            }
        })
        .catch(error => {
            console.error('Error logging idle time start:', error);
        });
    }

    sendIdleTimeEnd(duration) {
        fetch('/api/telecaller-tracking/end-idle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                duration: duration
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Idle time end logged');
            }
        })
        .catch(error => {
            console.error('Error logging idle time end:', error);
        });
    }

    logActivity(activityName, description, metadata = {}) {
        if (!this.isTracking) return;

        fetch('/api/telecaller-tracking/log-activity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                activity_type: 'action',
                activity_name: activityName,
                description: description,
                page_url: window.location.href,
                metadata: metadata
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Activity logged:', activityName);
            }
        })
        .catch(error => {
            console.error('Error logging activity:', error);
        });
    }

    startSyncTimer() {
        this.syncTimer = setInterval(() => {
            this.syncIdleData();
        }, this.syncInterval);
    }

    syncIdleData() {
        if (this.idleData.length === 0) return;

        fetch('/api/telecaller-tracking/sync-idle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                idle_data: this.idleData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Idle data synced:', data.total_idle_seconds);
                this.idleData = []; // Clear synced data
            }
        })
        .catch(error => {
            console.error('Error syncing idle data:', error);
        });
    }

    handlePageHidden() {
        if (this.isIdle) {
            this.endIdleTime();
        }
        this.logActivity('page_hidden', 'Page became hidden');
    }

    handlePageVisible() {
        this.handleActivity('page_visible');
        this.logActivity('page_visible', 'Page became visible');
    }

    handlePageUnload() {
        this.stopTracking();
        this.syncIdleData(); // Force sync before page unload
    }

    performAutoLogout() {
        console.log('Auto-logout triggered due to inactivity');
        
        // Stop tracking immediately
        this.stopTracking();
        
        // Log the auto-logout activity
        this.logActivity('auto_logout', 'User auto-logged out due to 10 seconds of inactivity');
        
        // Send auto-logout to backend
        fetch('/api/telecaller-tracking/auto-logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                reason: 'inactivity_timeout',
                duration: this.logoutThreshold
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Auto-logout response:', data);
            if (data.success) {
                // Clear any stored data
                localStorage.clear();
                sessionStorage.clear();
                
                // Show logout message
                alert('You have been automatically logged out due to inactivity.');
                // Redirect to login page
                window.location.href = '/';
            } else {
                // Force redirect even if API call fails
                localStorage.clear();
                sessionStorage.clear();
                alert('You have been automatically logged out due to inactivity.');
                window.location.href = '/';
            }
        })
        .catch(error => {
            console.error('Error during auto-logout:', error);
            // Force redirect even if API call fails
            localStorage.clear();
            sessionStorage.clear();
            alert('You have been automatically logged out due to inactivity.');
            window.location.href = '/';
        });
    }

    clearTimers() {
        if (this.activityTimer) {
            clearTimeout(this.activityTimer);
        }
        if (this.logoutTimer) {
            clearTimeout(this.logoutTimer);
        }
        if (this.syncTimer) {
            clearInterval(this.syncTimer);
        }
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
    }

    clearAllTimers() {
        this.clearTimers();
    }

    showCountdown() {
        // Create or update countdown display
        let countdownElement = document.getElementById('idle-countdown');
        if (!countdownElement) {
            countdownElement = document.createElement('div');
            countdownElement.id = 'idle-countdown';
            countdownElement.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #ff4444;
                color: white;
                padding: 10px 15px;
                border-radius: 5px;
                font-weight: bold;
                z-index: 9999;
                box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            `;
            document.body.appendChild(countdownElement);
        }

        let timeLeft = 10; // 10 seconds
        countdownElement.textContent = `Auto-logout in ${timeLeft} seconds`;

        const countdownInterval = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = `Auto-logout in ${timeLeft} seconds`;
            
            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                countdownElement.remove();
            }
        }, 1000);

        // Store interval ID to clear it if user becomes active
        this.countdownInterval = countdownInterval;
    }

    hideCountdown() {
        const countdownElement = document.getElementById('idle-countdown');
        if (countdownElement) {
            countdownElement.remove();
        }
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
            this.countdownInterval = null;
        }
    }

    // Public methods for external use
    getCurrentSession() {
        return fetch('/api/telecaller-tracking/current-session', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.session) {
                return data;
            }
            throw new Error('No active session found');
        });
    }

    // Method to manually log specific activities
    logCustomActivity(activityName, description, metadata = {}) {
        this.logActivity(activityName, description, metadata);
    }

    // Method to get idle statistics
    getIdleStats() {
        const totalIdleTime = this.idleData.reduce((total, idle) => total + idle.duration, 0);
        return {
            isIdle: this.isIdle,
            currentIdleTime: this.isIdle ? Date.now() - this.idleStartTime : 0,
            totalIdleTime: totalIdleTime,
            idleCount: this.idleData.length
        };
    }
}

// Initialize tracking when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Make sure we have the required meta tag for CSRF
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = document.querySelector('input[name="_token"]')?.value || '';
        document.head.appendChild(meta);
    }

    // Initialize tracking
    window.telecallerTracking = new TelecallerTracking();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TelecallerTracking;
}
