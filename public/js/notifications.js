/**
 * Notification System for Musix
 * 
 * Provides toast notifications with different styles (success, error, info, warning)
 * Features:
 * - Auto dismiss after customizable duration
 * - Progress bar showing time left
 * - Close button
 * - Shake animation for errors
 * - Stacked notifications
 */
class NotificationSystem {
    constructor() {
        this.container = document.getElementById('notificationSystem');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'notificationSystem';
            this.container.className = 'notification-system';
            document.body.appendChild(this.container);
        }
        this.notifications = [];
    }
    
    /**
     * Show a success notification
     * @param {string} title - Notification title
     * @param {string} message - Notification message
     * @param {number} duration - Duration in ms
     */
    success(title, message, duration = 5000) {
        this._showNotification(title, message, 'success', duration);
    }
    
    /**
     * Show an error notification
     * @param {string} title - Notification title
     * @param {string} message - Notification message
     * @param {number} duration - Duration in ms
     */
    error(title, message, duration = 8000) {
        this._showNotification(title, message, 'error', duration, true);
    }
    
    /**
     * Show an info notification
     * @param {string} title - Notification title
     * @param {string} message - Notification message
     * @param {number} duration - Duration in ms
     */
    info(title, message, duration = 5000) {
        this._showNotification(title, message, 'info', duration);
    }
    
    /**
     * Show a warning notification
     * @param {string} title - Notification title
     * @param {string} message - Notification message
     * @param {number} duration - Duration in ms
     */
    warning(title, message, duration = 6000) {
        this._showNotification(title, message, 'warning', duration);
    }
    
    /**
     * Create and show a notification
     * @private
     */
    _showNotification(title, message, type, duration, shake = false) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        // Get appropriate icon based on type
        const icon = this._getIconForType(type);
        
        // Build notification content
        notification.innerHTML = `
            <div class="notification-icon">${icon}</div>
            <div class="notification-content">
                <h3 class="notification-title">${title}</h3>
                <p class="notification-message">${message}</p>
            </div>
            <button class="notification-close" aria-label="Close">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.41 6L11.71 1.71C11.8983 1.5217 12.0041 1.2663 12.0041 1C12.0041 0.7337 11.8983 0.478301 11.71 0.29C11.5217 0.101699 11.2663 -0.00412941 11 -0.00412941C10.7337 -0.00412941 10.4783 0.101699 10.29 0.29L6 4.59L1.71 0.29C1.5217 0.101699 1.2663 -0.00412941 1 -0.00412941C0.733695 -0.00412941 0.478296 0.101699 0.289997 0.29C0.101697 0.478301 -0.00413132 0.7337 -0.00413132 1C-0.00413132 1.2663 0.101697 1.5217 0.289997 1.71L4.59 6L0.289997 10.29C0.101697 10.4783 -0.00413132 10.7337 -0.00413132 11C-0.00413132 11.2663 0.101697 11.5217 0.289997 11.71C0.478296 11.8983 0.733695 12.0041 1 12.0041C1.2663 12.0041 1.5217 11.8983 1.71 11.71L6 7.41L10.29 11.71C10.4783 11.8983 10.7337 12.0041 11 12.0041C11.2663 12.0041 11.5217 11.8983 11.71 11.71C11.8983 11.5217 12.0041 11.2663 12.0041 11C12.0041 10.7337 11.8983 10.4783 11.71 10.29L7.41 6Z" fill="currentColor"/>
                </svg>
            </button>
            <div class="notification-progress">
                <div class="notification-progress-bar"></div>
            </div>
        `;
        
        // Add to container
        this.container.appendChild(notification);
        
        // Store notification data
        const notificationData = {
            element: notification,
            timeoutId: null,
            progressBar: notification.querySelector('.notification-progress-bar')
        };
        
        this.notifications.push(notificationData);
        
        // Show notification (with a small delay for the animation)
        setTimeout(() => {
            notification.classList.add('show');
            if (shake) {
                notification.classList.add('shake');
                // Remove shake class after animation completes
                setTimeout(() => {
                    notification.classList.remove('shake');
                }, 500);
            }
        }, 50);
        
        // Setup progress bar
        if (duration > 0) {
            notificationData.progressBar.style.transition = `transform ${duration}ms linear`;
            setTimeout(() => {
                notificationData.progressBar.style.transform = 'scaleX(1)';
            }, 50);
            
            // Set timeout to remove notification
            notificationData.timeoutId = setTimeout(() => {
                this._removeNotification(notificationData);
            }, duration);
        }
        
        // Setup close button
        const closeButton = notification.querySelector('.notification-close');
        closeButton.addEventListener('click', () => {
            this._removeNotification(notificationData);
        });
        
        return notificationData;
    }
    
    /**
     * Remove a notification with animation
     * @private
     */
    _removeNotification(notificationData) {
        // Clear timeout if exists
        if (notificationData.timeoutId) {
            clearTimeout(notificationData.timeoutId);
        }
        
        // Start hide animation
        notificationData.element.classList.add('hiding');
        
        // Remove after animation completes
        setTimeout(() => {
            if (notificationData.element.parentNode) {
                notificationData.element.parentNode.removeChild(notificationData.element);
            }
            // Remove from array
            const index = this.notifications.indexOf(notificationData);
            if (index > -1) {
                this.notifications.splice(index, 1);
            }
        }, 500);
    }
    
    /**
     * Get SVG icon for notification type
     * @private
     */
    _getIconForType(type) {
        switch(type) {
            case 'success':
                return `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59 4.58L17 6L8 15Z" fill="currentColor"/>
                </svg>`;
            case 'error':
                return `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V13H11V15ZM11 11H9V5H11V11Z" fill="currentColor"/>
                </svg>`;
            case 'info':
                return `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V9H11V15ZM11 7H9V5H11V7Z" fill="currentColor"/>
                </svg>`;
            case 'warning':
                return `<svg width="20" height="20" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 0.25C9.43 0.25 8.95 0.56 8.71 1.05L0.37 16.98C0.14 17.45 0.12 18.02 0.34 18.51C0.56 19 1.02 19.28 1.52 19.28H18.49C19 19.28 19.46 19 19.68 18.51C19.9 18.02 19.87 17.45 19.64 16.98L11.3 1.05C11.05 0.56 10.57 0.25 10 0.25ZM11 16.25H9V14.25H11V16.25ZM11 12.25H9V6.25H11V12.25Z" fill="currentColor"/>
                </svg>`;
            default:
                return '';
        }
    }
}

// Create global notification instance
window.notifications = new NotificationSystem();

// Usage examples:
// notifications.success('Success', 'Your changes have been saved successfully.');
// notifications.error('Error', 'Something went wrong. Please try again.');
// notifications.info('Information', 'Your account will expire in 7 days.');
// notifications.warning('Warning', 'This action cannot be undone.');

// Optional: Add to window.onload to test notifications
document.addEventListener('DOMContentLoaded', function() {
    // Test notifications if needed
    // You can remove this section in production
    
    /* Example usage:
    setTimeout(() => {
        window.notifications.success('Success', 'Your profile has been updated successfully.');
    }, 1000);
    
    setTimeout(() => {
        window.notifications.error('Error', 'Unable to connect to the server. Please try again later.');
    }, 3000);
    
    setTimeout(() => {
        window.notifications.info('New Message', 'You have 3 new messages in your inbox.');
    }, 5000);
    
    setTimeout(() => {
        window.notifications.warning('Storage Full', 'Your storage is almost full. Please free up some space.');
    }, 7000);
    */
});