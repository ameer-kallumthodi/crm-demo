// Layout configuration file
// This file provides basic layout configuration for the CRM application

window.LayoutConfig = {
    // Basic configuration
    theme: 'light',
    sidebarCollapsed: false,
    
    // Initialize layout
    init: function() {
        console.log('Layout configuration loaded');
    }
};

// Initialize when DOM is ready
$(document).ready(function() {
    if (typeof LayoutConfig !== 'undefined') {
        LayoutConfig.init();
    }
});
