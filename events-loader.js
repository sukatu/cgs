// Events Loader - Fetches events from database and displays them

// Extract YouTube video ID from URL
function getYouTubeId(url) {
    if (!url) return null;
    const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
    return match ? match[1] : null;
}

// Format date for display
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Get date in format for datetime-local input
function formatDateForInput(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().slice(0, 16);
}

// Format time only
function formatTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        timeZoneName: 'short'
    });
}

// Create timeline item HTML (for series diary)
function createTimelineItem(event) {
    const eventDate = formatDate(event.event_date);
    const youtubeId = event.youtube_url ? getYouTubeId(event.youtube_url) : null;
    
    let timelineHTML = `
        <div class="timeline-item">
            <h3 class="timeline-title">${escapeHtml(event.title)}</h3>
            <div class="timeline-meta">
                ${event.description ? `<strong>Theme:</strong> ${escapeHtml(event.description.substring(0, 100))}${event.description.length > 100 ? '...' : ''}<br>` : ''}
                ${event.location ? `<strong>Location:</strong> ${escapeHtml(event.location)}<br>` : ''}
                ${eventDate ? `<strong>Date:</strong> ${eventDate}<br>` : ''}
                ${event.format ? `<strong>Format:</strong> ${escapeHtml(event.format)}<br>` : ''}
            </div>
            ${event.description ? `<p style="color: var(--text-charcoal); line-height: 1.7; margin-bottom: 1.5rem;">${escapeHtml(event.description)}</p>` : ''}
    `;
    
    if (youtubeId) {
        timelineHTML += `
            <div class="video-container" style="margin: 1.5rem 0;">
                <iframe src="https://www.youtube.com/embed/${youtubeId}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                        title="${escapeHtml(event.title)}"></iframe>
            </div>
        `;
    }
    
    timelineHTML += `
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
    `;
    
    if (event.youtube_url) {
        timelineHTML += `<a href="${escapeHtml(event.youtube_url)}" target="_blank" class="btn btn-primary">Watch Full Video</a>`;
    }
    
    if (event.registration_link) {
        timelineHTML += `<a href="${escapeHtml(event.registration_link)}" target="_blank" class="btn btn-outline">View Details</a>`;
    }
    
    timelineHTML += `
            </div>
        </div>
    `;
    
    return timelineHTML;
}

// Create event card HTML
function createEventCard(event) {
    const eventDate = formatDate(event.event_date);
    const isUpcoming = event.status === 'upcoming';
    const youtubeId = event.youtube_url ? getYouTubeId(event.youtube_url) : null;
    
    let cardHTML = `
        <div class="event-card">
            <div class="event-header">
                <div>
                    <h3 class="event-title">${escapeHtml(event.title)}</h3>
                    <div class="event-date">${eventDate}</div>
                </div>
            </div>
    `;
    
    if (event.description) {
        cardHTML += `
            <div class="event-description">
                ${escapeHtml(event.description)}
            </div>
        `;
    }
    
    if (event.location || event.format) {
        cardHTML += `
            <div class="event-meta">
                ${event.location ? `<div class="event-meta-item"><strong>Location:</strong> ${escapeHtml(event.location)}</div>` : ''}
                ${event.format ? `<div class="event-meta-item"><strong>Format:</strong> ${escapeHtml(event.format)}</div>` : ''}
            </div>
        `;
    }
    
    if (event.speakers) {
        cardHTML += `
            <div class="event-speakers">
                <strong>Speakers:</strong>
                <p style="margin-top: 0.5rem;">${escapeHtml(event.speakers)}</p>
            </div>
        `;
    }
    
    if (event.moderator) {
        cardHTML += `
            <div class="event-meta-item" style="margin-bottom: 1rem;">
                <strong>Moderator:</strong> ${escapeHtml(event.moderator)}
            </div>
        `;
    }
    
    // Show YouTube video if available and event is completed
    if (youtubeId && !isUpcoming) {
        cardHTML += `
            <div class="video-container" style="margin: 1.5rem 0;">
                <iframe src="https://www.youtube.com/embed/${youtubeId}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                        title="${escapeHtml(event.title)}"></iframe>
            </div>
        `;
    }
    
    cardHTML += `
            <div class="event-actions">
    `;
    
    // Check if user is logged in (check for session)
    const isUserLoggedIn = document.cookie.includes('PHPSESSID') || typeof userLoggedIn !== 'undefined';
    
    if (isUpcoming) {
        if (isUserLoggedIn) {
            // Show booking form button
            cardHTML += `
                <button onclick="bookEvent(${event.id}, '${escapeHtml(event.title)}')" class="btn btn-primary">Book This Event</button>
            `;
        } else if (event.registration_link) {
            cardHTML += `<a href="${escapeHtml(event.registration_link)}" target="_blank" class="btn btn-primary">Register</a>`;
        } else {
            cardHTML += `<a href="login.html?redirect=${encodeURIComponent(window.location.href)}" class="btn btn-primary">Login to Book</a>`;
        }
    } else if (event.registration_link) {
        cardHTML += `<a href="${escapeHtml(event.registration_link)}" target="_blank" class="btn btn-outline">View Details</a>`;
    }
    
    if (youtubeId) {
        cardHTML += `<a href="${escapeHtml(event.youtube_url)}" target="_blank" class="btn btn-outline">Watch on YouTube</a>`;
    }
    
    if (event.agenda) {
        cardHTML += `<a href="#" class="btn btn-outline" onclick="showAgenda('${escapeHtml(event.agenda)}')">View Agenda</a>`;
    }
    
    cardHTML += `
            </div>
        </div>
    `;
    
    return cardHTML;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Show agenda in alert/modal
function showAgenda(agenda) {
    alert(agenda);
}

// Load events from API
async function loadEvents(type = null, status = null, containerId = 'eventsContainer') {
    try {
        let apiUrl = 'api-events.php?';
        if (type) apiUrl += `type=${encodeURIComponent(type)}&`;
        if (status) apiUrl += `status=${encodeURIComponent(status)}&`;
        
        const response = await fetch(apiUrl);
        const data = await response.json();
        
        if (data.success && data.events) {
            const container = document.getElementById(containerId);
            if (!container) {
                console.error('Container not found:', containerId);
                return;
            }
            
            if (data.events.length === 0) {
                container.innerHTML = '';
                container.style.display = 'none';
                return;
            }
            
            container.innerHTML = data.events.map(event => createEventCard(event)).join('');
        } else {
            console.error('Error loading events:', data);
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 2rem;">Error loading events. Please try again later.</p>';
            }
        }
    } catch (error) {
        console.error('Error fetching events:', error);
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 2rem;">Error loading events. Please check your connection.</p>';
        }
    }
}

// Load upcoming events
function loadUpcomingEvents(type = null, containerId = 'upcomingEvents') {
    loadEvents(type, 'upcoming', containerId);
}

// Load completed events
function loadCompletedEvents(type = null, containerId = 'completedEvents') {
    loadEvents(type, 'completed', containerId);
}

// Load all events
function loadAllEvents(type = null, containerId = 'allEvents') {
    loadEvents(type, null, containerId);
}

// Book event function
function bookEvent(eventId, eventTitle) {
    if (confirm('Do you want to register for: ' + eventTitle + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'book-event.php';
        
        const eventIdInput = document.createElement('input');
        eventIdInput.type = 'hidden';
        eventIdInput.name = 'event_id';
        eventIdInput.value = eventId;
        form.appendChild(eventIdInput);
        
        const redirectInput = document.createElement('input');
        redirectInput.type = 'hidden';
        redirectInput.name = 'redirect';
        redirectInput.value = window.location.href;
        form.appendChild(redirectInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Load events as timeline items (for series diary)
async function loadEventsAsTimeline(type = null, status = null, containerId = 'seriesEvents') {
    try {
        let apiUrl = 'api-events.php?';
        if (type) apiUrl += `type=${encodeURIComponent(type)}&`;
        if (status) apiUrl += `status=${encodeURIComponent(status)}&`;
        
        const response = await fetch(apiUrl);
        const data = await response.json();
        
        if (data.success && data.events) {
            const container = document.getElementById(containerId);
            if (!container) {
                console.error('Container not found:', containerId);
                return;
            }
            
            if (data.events.length === 0) {
                container.innerHTML = '';
                container.style.display = 'none';
                return;
            }
            
            // Wrap in timeline class
            container.innerHTML = '<div class="timeline">' + 
                data.events.map(event => createTimelineItem(event)).join('') + 
                '</div>';
        } else {
            console.error('Error loading events:', data);
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 2rem;">Error loading events. Please try again later.</p>';
            }
        }
    } catch (error) {
        console.error('Error fetching events:', error);
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 2rem;">Error loading events. Please check your connection.</p>';
        }
    }
}

