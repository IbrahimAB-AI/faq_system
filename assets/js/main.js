// Main JavaScript file

/**
 * FAQ System - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Handle flash message URL params
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') || urlParams.has('error')) {
        if (history.replaceState) {
            const cleanUrl = window.location.pathname;
            history.replaceState({}, document.title, cleanUrl);
        }
    }
    
    // Character count for textarea
    const charCountInputs = document.querySelectorAll('textarea[maxlength]');
    charCountInputs.forEach(textarea => {
        const countDisplay = document.getElementById('charCount');
        if (countDisplay) {
            textarea.addEventListener('input', function() {
                countDisplay.textContent = this.value.length;
            });
        }
    });
});

/**
 * Show notification toast
 * @param {string} message 
 * @param {string} type - success, error, warning, info 
 */
function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(function() {
        const bsToast = new bootstrap.Alert(toast);
        bsToast.close();
    }, 5000);
}

/**
 * Format relative time
 * @param {Date|string} date 
 * @returns {string}
 */
function formatRelativeTime(date) {
    const now = new Date();
    const then = new Date(date);
    const diffMs = now - then;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHour = Math.floor(diffMin / 60);
    const diffDay = Math.floor(diffHour / 24);
    
    if (diffSec < 60) return 'just now';
    if (diffMin < 60) return `${diffMin} minute${diffMin > 1 ? 's' : ''} ago`;
    if (diffHour < 24) return `${diffHour} hour${diffHour > 1 ? 's' : ''} ago`;
    if (diffDay < 7) return `${diffDay} day${diffDay > 1 ? 's' : ''} ago`;
    
    return then.toLocaleDateString();
}
