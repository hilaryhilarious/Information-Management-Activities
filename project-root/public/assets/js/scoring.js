// Scoring interface for judges

let currentEvent = null;
let currentCriteria = [];
let currentParticipants = [];

// Load event criteria
async function loadEventCriteria(eventId) {
    try {
        const response = await fetch(`/api/get_events.php?event_id=${eventId}`);
        const data = await response.json();
        
        if (data.success) {
            currentEvent = data.event;
            // Load criteria would require additional API endpoint
            displayScoringForm();
        }
    } catch (error) {
        console.error('Error loading event criteria:', error);
    }
}

// Display scoring form
function displayScoringForm() {
    const container = document.getElementById('scoringForm');
    if (!container) return;
    
    // This would be populated with actual criteria and participants
    // For now, showing structure
    container.innerHTML = `
        <div class="card">
            <h3 class="text-2xl font-bold text-primary mb-4" data-testid="scoring-event-title">${currentEvent?.event_name || 'Event'}</h3>
            <p class="text-gray-600 mb-6">${currentEvent?.description || ''}</p>
            
            <form id="scoreSubmitForm" data-testid="score-submit-form">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Participant/Team</label>
                    <select name="participant_id" class="input-field" required data-testid="participant-select">
                        <option value="">Select participant/team</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Criteria</label>
                    <select name="criteria_id" class="input-field" required data-testid="criteria-select">
                        <option value="">Select criteria</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Score</label>
                    <input type="number" name="score_value" class="input-field" step="0.01" min="0" required data-testid="score-value-input">
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Remarks (Optional)</label>
                    <textarea name="remarks" class="input-field" rows="3" data-testid="score-remarks-input"></textarea>
                </div>
                
                <button type="submit" class="btn-primary" data-testid="submit-score-btn">Submit Score</button>
            </form>
        </div>
    `;
}

// Submit score
async function submitScore(scoreData) {
    try {
        const response = await fetch('/api/submit_score.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(scoreData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Score submitted successfully!');
            return true;
        } else {
            showNotification(data.error || 'Failed to submit score', 'error');
            return false;
        }
    } catch (error) {
        console.error('Error submitting score:', error);
        showNotification('An error occurred', 'error');
        return false;
    }
}

// Export functions
window.loadEventCriteria = loadEventCriteria;
window.submitScore = submitScore;