// AI Agent JavaScript file

/**
 * FAQ System - AI Agent Chat JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    const agentToggleBtn = document.getElementById('agentToggleBtn');
    const agentDrawer = document.getElementById('agentDrawer');
    const agentCloseBtn = document.getElementById('agentCloseBtn');
    const agentForm = document.getElementById('agentForm');
    const agentInput = document.getElementById('agentInput');
    const agentMessages = document.getElementById('agentMessages');

    // Toggle drawer
    if (agentToggleBtn) {
        agentToggleBtn.addEventListener('click', function() {
            agentDrawer.classList.add('show');
            agentInput.focus();
        });
    }

    // Close drawer
    if (agentCloseBtn) {
        agentCloseBtn.addEventListener('click', function() {
            agentDrawer.classList.remove('show');
        });
    }

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (agentDrawer && !agentDrawer.contains(e.target) && 
            !agentToggleBtn.contains(e.target)) {
            agentDrawer.classList.remove('show');
        }
    });

    // Handle form submission
    if (agentForm) {
        agentForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const message = agentInput.value.trim();
            if (!message) return;

            addMessage(message, 'user');
            agentInput.value = '';
            showLoading();

            try {
                const response = await fetch('agent.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                removeLoading();

                if (data.success) {
                    addMessage(data.reply, 'bot');
                } else {
                    addMessage(data.reply || 'Sorry, something went wrong.', 'bot');
                }
            } catch (error) {
                removeLoading();
                addMessage('Sorry, I couldn\'t connect. Please try again.', 'bot');
                console.error('Agent error:', error);
            }
        });
    }

    /**
     * Add message to chat
     */
    function addMessage(content, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `agent-message agent-message-${type}`;
        
        const avatar = type === 'user' 
            ? '<i class="bi bi-person"></i>' 
            : '<i class="bi bi-robot"></i>';
        
        messageDiv.innerHTML = `
            <div class="agent-avatar">${avatar}</div>
            <div class="agent-bubble">${escapeHtml(content)}</div>
        `;
        
        agentMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    /**
     * Show typing indicator
     */
    function showLoading() {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'agent-message agent-message-loading';
        loadingDiv.id = 'agent-loading';
        loadingDiv.innerHTML = `
            <div class="agent-avatar"><i class="bi bi-robot"></i></div>
            <div class="agent-typing">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
        
        agentMessages.appendChild(loadingDiv);
        scrollToBottom();
    }

    /**
     * Remove loading indicator
     */
    function removeLoading() {
        const loading = document.getElementById('agent-loading');
        if (loading) loading.remove();
    }

    /**
     * Scroll to bottom
     */
    function scrollToBottom() {
        agentMessages.scrollTop = agentMessages.scrollHeight;
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});

/**
 * Open agent from anywhere
 */
function openAgent() {
    const agentDrawer = document.getElementById('agentDrawer');
    const agentInput = document.getElementById('agentInput');
    
    if (agentDrawer && agentInput) {
        agentDrawer.classList.add('show');
        agentInput.focus();
    }
}

window.openAgent = openAgent;
