// Live score polling and display

let pollingInterval = null;
let currentEventId = null;

// Start polling for live scores
function startLiveScorePolling(eventId, intervalMs = 5000) {
    currentEventId = eventId;
    
    // Initial load
    loadLiveScores();
    
    // Set up polling
    pollingInterval = setInterval(loadLiveScores, intervalMs);
}

// Stop polling
function stopLiveScorePolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

// Load live scores
async function loadLiveScores() {
    if (!currentEventId) return;
    
    try {
        const response = await fetch(`/api/get_rankings.php?event_id=${currentEventId}`);
        const data = await response.json();
        
        if (data.success) {
            displayLiveScores(data.rankings);
        }
    } catch (error) {
        console.error('Error loading live scores:', error);
    }
}

// Display live scores in table
function displayLiveScores(rankings) {
    const tbody = document.getElementById('liveScoresBody');
    if (!tbody) return;
    
    tbody.innerHTML = rankings.map((rank, index) => {
        const name = rank.team_name || `${rank.p_first_name} ${rank.p_last_name}`;
        const identifier = rank.team_code || rank.participant_number || '-';
        const position = rank.rank_position || (index + 1);
        
        return `
            <tr class="border-b hover:bg-soft transition-colors" data-testid="ranking-row-${position}">
                <td class="px-6 py-4 font-bold text-lg ${position <= 3 ? 'text-accent' : ''}">${position}</td>
                <td class="px-6 py-4 font-semibold">${name}</td>
                <td class="px-6 py-4 text-gray-600">${identifier}</td>
                <td class="px-6 py-4 font-bold text-primary text-lg">${parseFloat(rank.total_score).toFixed(2)}</td>
            </tr>
        `;
    }).join('');
    
    // Update last updated time
    const lastUpdated = document.getElementById('lastUpdated');
    if (lastUpdated) {
        lastUpdated.textContent = new Date().toLocaleTimeString();
    }
}

// Export functions
window.startLiveScorePolling = startLiveScorePolling;
window.stopLiveScorePolling = stopLiveScorePolling;
window.loadLiveScores = loadLiveScores;