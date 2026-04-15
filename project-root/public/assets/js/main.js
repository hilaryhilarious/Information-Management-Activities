// Main application logic

// Check authentication status
async function checkAuth() {
    try {
        const response = await fetch('/api/auth.php?action=check');
        const data = await response.json();
        return data.authenticated ? data.user : null;
    } catch (error) {
        console.error('Auth check error:', error);
        return null;
    }
}

// Logout function
async function logout() {
    try {
        await fetch('/api/auth.php?action=logout', { method: 'POST' });
        window.location.href = '/login.html';
    } catch (error) {
        console.error('Logout error:', error);
    }
}

// Format date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Export functions
window.checkAuth = checkAuth;
window.logout = logout;
window.formatDate = formatDate;
window.showNotification = showNotification;