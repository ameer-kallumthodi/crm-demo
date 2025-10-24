/**
 * Telecaller Behavior & Productivity Tracking JavaScript
 * Tracks idle time, user activity, and syncs data with Laravel backend
 */

class TelecallerTracking {
    constructor() {
        this.idleThreshold = 20 * 60 * 1000; // 20 minutes in milliseconds
        this.logoutThreshold = 20 * 1000; // 20 seconds for auto-logout
        this.syncInterval = 30 * 1000; // 30 seconds
        this.isIdle = false;
        this.idleStartTime = null;
        this.idleData = [];
        this.lastActivityTime = Date.now();
        this.pageHiddenTime = null;
        this.pageVisibleTime = null;
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
        console.log('Device type:', this.isMobile() ? 'Mobile' : 'Desktop');
        
        // Only initialize for telecallers (role_id = 3)
        if (this.isTelecaller()) {
            // Always start tracking for telecallers (working hours check disabled)
            console.log('TelecallerTracking: User is telecaller, starting tracking (working hours check disabled)...');
            this.startTracking();
            this.setupEventListeners();
            this.startSyncTimer();
            this.trackPageVisit(); // Track initial page visit
        } else {
            console.log('TelecallerTracking: User is not telecaller, skipping tracking');
        }
    }

    isTelecaller() {
        // Check if current user is a telecaller
        // This should match your role system
        return window.userRoleId === 3;
    }

    isMobile() {
        // Enhanced mobile detection
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
               (navigator.maxTouchPoints && navigator.maxTouchPoints > 2) ||
               window.innerWidth <= 768;
    }

    // isWithinWorkingHours() - REMOVED: Working hours check completely disabled
    // Telecallers can now work at any time

    // performWorkingHoursLogout() - REMOVED: Working hours check completely disabled
    // Telecallers can now work at any time

    // showWorkingHoursLogoutModal() - REMOVED: Working hours check completely disabled
    // Telecallers can now work at any time

    showFallbackModal(title, message) {
        // Create a custom modal if SweetAlert2 is not available
        const modalHtml = `
            <div id="fallback-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                <div style="
                    background: white;
                    border-radius: 12px;
                    padding: 30px;
                    max-width: 400px;
                    width: 90%;
                    text-align: center;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                ">
                    <div style="margin-bottom: 20px;">
                        <i class="ti ti-clock-off" style="font-size: 3rem; color: #dc3545;"></i>
                    </div>
                    <h3 style="margin-bottom: 15px; color: #333;">${title}</h3>
                    <p style="margin-bottom: 20px; color: #666;">${message}</p>
                    <button onclick="document.getElementById('fallback-modal').remove(); window.location.href = window.location.origin;" 
                            style="
                                background: #dc3545;
                                color: white;
                                border: none;
                                padding: 10px 20px;
                                border-radius: 5px;
                                cursor: pointer;
                                font-size: 16px;
                            ">
                        OK
                    </button>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    startTracking() {
        this.isTracking = true;
        console.log('TelecallerTracking: Tracking started');
        // Removed internal tracking activity log
        
        // Start the initial activity timer
        this.resetActivityTimer();
    }

    stopTracking() {
        this.isTracking = false;
        this.endIdleTime();
        this.clearTimers();
        // Removed internal tracking activity log
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

        // Touch events for mobile - enhanced support
        ['touchstart', 'touchmove', 'touchend', 'touchcancel'].forEach(event => {
            document.addEventListener(event, (e) => {
                // Handle touch events properly for mobile
                this.handleActivity('touch');
            }, { passive: true });
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

        // Setup page visit tracking
        this.setupPageVisitTracking();

        // Window focus/blur - these are crucial for detecting when user switches away
        window.addEventListener('focus', () => {
            console.log('Window gained focus - idle tracking continues');
            // Don't automatically reset timers on focus
            // Only reset when actual mouse/keyboard activity is detected
        });
        window.addEventListener('blur', () => {
            console.log('Window lost focus - idle tracking continues in background');
            // Don't treat window blur as activity, just log it
        });
    }

    handleActivity(type) {
        if (!this.isTracking) return;

        // Only respond to actual user interactions, not page visibility or window events
        const userInteractionTypes = ['mouse', 'keyboard', 'scroll', 'touch'];
        if (!userInteractionTypes.includes(type)) {
            console.log('Non-user activity detected:', type, '- ignoring for timer reset');
            return;
        }

        this.lastActivityTime = Date.now();
        
        // End idle time if user becomes active
        if (this.isIdle) {
            console.log('User became active - ending idle time');
            this.endIdleTime();
        }

        // Clear all timers and start fresh
        this.clearAllTimers();
        this.resetActivityTimer();

        console.log('User activity detected:', type, '- Timer reset');
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
        
        console.log('Activity timer reset - will check for idle in 20 minutes');
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

        console.log('User became idle - auto-logout in 20 seconds');
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

        // Only log page visits, not internal API calls or tracking activities
        const shouldLog = this.shouldLogActivity(activityName, window.location.href);
        
        if (!shouldLog) {
            console.log('Skipping activity log for:', activityName, '- not a user page visit');
            return;
        }

        // Additional check: Don't log if this is an API call or internal tracking
        if (activityName.includes('api/') || activityName.includes('telecaller-tracking') || 
            activityName.includes('notifications') || activityName.includes('start-idle') || 
            activityName.includes('end-idle') || activityName.includes('auto-logout') ||
            activityName.includes('sync-idle') || activityName.includes('log-activity')) {
            console.log('Skipping activity log for internal API call:', activityName);
            return;
        }

        console.log('Logging page visit:', activityName, 'for URL:', window.location.href);

        fetch('/api/telecaller-tracking/log-activity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                activity_type: 'page_view',
                activity_name: activityName,
                description: description,
                page_url: window.location.href,
                metadata: metadata
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Page visit logged:', activityName);
            }
        })
        .catch(error => {
            console.error('Error logging page visit:', error);
        });
    }

    shouldLogActivity(activityName, pageUrl) {
        // Don't log internal tracking activities
        const internalActivities = [
            'tracking_started',
            'tracking_stopped', 
            'user_activity',
            'window_focus',
            'window_blur',
            'page_hidden',
            'page_visible',
            'auto_logout',
            'idle_time_started',
            'idle_time_ended',
            'sync_idle_data',
            'notifications.api'
        ];

        if (internalActivities.includes(activityName)) {
            return false;
        }

        // Don't log API endpoints
        if (pageUrl.includes('/api/')) {
            return false;
        }

        // Don't log internal tracking URLs
        const internalUrls = [
            '/api/telecaller-tracking/',
            '/api/notifications',
            '/api/telecaller-tracking/log-activity',
            '/api/telecaller-tracking/start-idle',
            '/api/telecaller-tracking/end-idle',
            '/api/telecaller-tracking/sync-idle',
            '/api/telecaller-tracking/auto-logout'
        ];

        for (const url of internalUrls) {
            if (pageUrl.includes(url)) {
                return false;
            }
        }

        // Don't log if the page URL contains API or if it's an AJAX call
        if (pageUrl.includes('api/') || pageUrl.includes('/api/')) {
            return false;
        }

        // Only log actual page visits (not AJAX calls or internal activities)
        return true;
    }

    setupPageVisitTracking() {
        // Track page visits when the page loads
        console.log('Setting up page visit tracking for URL:', window.location.href);
        this.trackPageVisit();

        // Track page visits when navigating (for SPA-like behavior)
        let lastUrl = window.location.href;
        const observer = new MutationObserver(() => {
            const currentUrl = window.location.href;
            if (currentUrl !== lastUrl) {
                console.log('URL changed from', lastUrl, 'to', currentUrl);
                lastUrl = currentUrl;
                this.trackPageVisit();
            }
        });

        // Start observing
        observer.observe(document, { subtree: true, childList: true });
    }

    trackPageVisit() {
        if (!this.isTracking) return;

        // Skip if current URL is an API endpoint
        if (window.location.href.includes('/api/')) {
            console.log('Skipping page visit tracking for API URL:', window.location.href);
            return;
        }

        const pageName = this.getPageName(window.location.href);
        
        // Skip if getPageName returns null (API URLs)
        if (!pageName) {
            console.log('Skipping page visit tracking for API URL:', window.location.href);
            return;
        }
        
        const description = `Page view: ${pageName}`;
        
        // Use the existing logActivity function which now has filtering
        this.logActivity(pageName, description, {
            page_title: document.title,
            referrer: document.referrer,
            timestamp: new Date().toISOString()
        });
    }

    getPageName(url) {
        // Extract meaningful page names from URLs
        const path = new URL(url).pathname;
        
        // Don't process API URLs - they should be filtered out
        if (path.includes('/api/')) {
            return null; // This will be filtered out by shouldLogActivity
        }
        
        // Remove common prefixes and get the meaningful part
        let pageName = path.replace(/^\/admin\//, '').replace(/^\/api\//, '');
        
        // Handle specific routes
        if (pageName === '') {
            return 'dashboard';
        }
        
        if (pageName.includes('leads')) {
            if (pageName.includes('create')) return 'leads.create';
            if (pageName.includes('edit')) return 'leads.edit';
            if (pageName.includes('show')) return 'leads.show';
            return 'leads.index';
        }
        
        if (pageName.includes('telecaller-tracking')) {
            if (pageName.includes('dashboard')) return 'telecaller-tracking.dashboard';
            if (pageName.includes('reports')) return 'telecaller-tracking.reports';
            if (pageName.includes('session-details')) return 'telecaller-tracking.session-details';
            return 'telecaller-tracking.index';
        }
        
        if (pageName.includes('converted-leads')) {
            return 'converted-leads.index';
        }
        
        if (pageName.includes('profile')) {
            return 'profile';
        }
        
        // Default to the pathname
        return pageName || 'unknown';
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
        console.log('Page became hidden - continuing idle tracking in background');
        this.pageHiddenTime = Date.now();
        
        // Continue idle tracking even when page is hidden
        // Don't end idle time or reset timers - let them continue running
    }

    handlePageVisible() {
        console.log('Page became visible - checking if user is still idle');
        this.pageVisibleTime = Date.now();
        
        // Don't automatically reset timers when page becomes visible
        // Only reset when actual mouse/keyboard activity is detected
        if (this.pageHiddenTime) {
            const timeAway = this.pageVisibleTime - this.pageHiddenTime;
            console.log('Page was hidden for:', Math.round(timeAway / 1000), 'seconds');
            console.log('Idle tracking continues - waiting for actual user activity');
        }
    }

    handlePageUnload() {
        this.stopTracking();
        this.syncIdleData(); // Force sync before page unload
    }

    performAutoLogout() {
        console.log('Auto-logout triggered due to inactivity');
        
        // Stop tracking immediately
        this.stopTracking();
        
        // Auto-logout activity (not logged as it's internal)
        
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
                
                // Show logout modal instead of alert
                this.showLogoutModal();
            } else {
                // Force redirect even if API call fails
                localStorage.clear();
                sessionStorage.clear();
                this.showLogoutModal();
            }
        })
        .catch(error => {
            console.error('Error during auto-logout:', error);
            // Force redirect even if API call fails
            localStorage.clear();
            sessionStorage.clear();
            this.showLogoutModal();
        });
    }

    showLogoutModal() {
        // Use SweetAlert2 for logout modal
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Session Expired',
                html: `
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="ti ti-clock-off" style="font-size: 3rem; color: #dc3545;"></i>
                        </div>
                        <p class="mb-3">You have been automatically logged out due to inactivity.</p>
                        <p class="text-muted small">For security reasons, your session has expired after 20 minutes of inactivity.</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: false,
                confirmButtonText: 'Login Again',
                confirmButtonColor: '#dc3545',
                allowOutsideClick: false,
                allowEscapeKey: false,
                customClass: {
                    popup: 'swal2-popup-custom',
                    title: 'swal2-title-custom',
                    content: 'swal2-content-custom',
                    confirmButton: 'swal2-confirm-custom'
                },
                didOpen: () => {
                    // Add custom styling
                    const popup = document.querySelector('.swal2-popup');
                    if (popup) {
                        popup.style.borderRadius = '12px';
                        popup.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.2)';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to root URL
                    window.location.href = window.location.origin;
                }
            });
        } else {
            // Fallback modal if SweetAlert2 is not available
            this.showFallbackModal('Session Expired', 'You have been automatically logged out due to inactivity.');
        }
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

        let timeLeft = 20; // 20 seconds
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
