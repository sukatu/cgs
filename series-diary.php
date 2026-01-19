<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Series Diary | Corporate Governance Series</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header-main.php'; ?>

    <main>
        <section class="page-header">
            <div class="container">
                <h1>Series Diary</h1>
                <p class="page-subtitle">The Corporate Governance Series is a structured roadmap of flagship discussions and stakeholder convenings. Each session produces practical outcomes and follow-through.</p>
            </div>
        </section>

        <section class="section content-section">
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <!-- Upcoming Events Section -->
                <div>
                    <div style="text-align: center; margin-bottom: 3rem;">
                        <h2 style="font-size: 2.5rem; color: var(--primary-navy); margin-bottom: 1rem; font-weight: 700;">Upcoming Events</h2>
                        <p style="text-align: center; color: var(--text-light); font-size: 1.1rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                            Future series sessions and flagship discussions
                        </p>
                    </div>
                    <div id="upcomingSeriesEvents">
                        <!-- Upcoming events will be loaded here dynamically -->
                        <p style="text-align: center; color: var(--text-light); padding: 2rem;">Loading upcoming events...</p>
                    </div>
                </div>
                
                <!-- Past Events Section -->
                <div>
                    <div style="text-align: center; margin-bottom: 3rem;">
                        <h2 style="font-size: 2.5rem; color: var(--primary-navy); margin-bottom: 1rem; font-weight: 700;">Past Events</h2>
                        <p style="text-align: center; color: var(--text-light); font-size: 1.1rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                            Previous series sessions and flagship discussions
                        </p>
                    </div>
                    <div id="pastEventsList" class="events-list-container">
                        <div class="event-list-item" data-event-id="past-0">
                            <div class="event-list-image-thumbnail-top">
                                <img src="images/gallery/New/Fredan-94.JPG" alt="Strengthening Corporate Governance of State-Owned Entities in Ghana" class="event-list-thumbnail-img">
                            </div>
                            <div class="event-list-header" onclick="openPastEventDetails()">
                                <div class="event-list-header-content">
                                    <h3 class="event-list-title">Strengthening Corporate Governance of State-Owned Entities in Ghana: Opportunities and Challenges</h3>
                                    <div class="event-list-meta">
                                        <span class="event-list-meta-item">13 Nov 2025</span>
                                        <span class="event-list-meta-item">GIMPA Law School, Dr. Daniel McKarley Moot Court Room</span>
                                    </div>
                                </div>
                                <div class="event-list-actions">
                                    <button class="event-list-toggle" aria-label="View event details">▶</button>
                                </div>
                                <span class="event-list-type-badge series">Series</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Static fallback -->
                <!--
                <div class="timeline">
                    <div class="timeline-item">
                        <h3 class="timeline-title">Strengthening Corporate Governance of State-Owned Entities in Ghana</h3>
                        <div class="timeline-meta">
                            <strong>Theme:</strong> SOE Governance: Opportunities & Challenges<br>
                            <strong>Location:</strong> Hybrid (Accra + Virtual)<br>
                            <strong>Duration:</strong> 1:14:22
                        </div>
                        <p style="color: var(--text-charcoal); line-height: 1.7; margin-bottom: 1.5rem;">
                            An in-depth live discussion exploring the critical opportunities and challenges in strengthening corporate governance of State-Owned Entities (SOEs) in Ghana. Featuring expert panelists and stakeholders from the public and private sectors.
                        </p>
                        <div class="video-container" style="margin: 1.5rem 0;">
                            <iframe src="https://www.youtube.com/embed/1809paF4iuk" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    title="LIVE: Strengthening Corporate Governance of State-Owned Entities in Ghana"></iframe>
                        </div>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <a href="https://www.youtube.com/watch?v=1809paF4iuk" target="_blank" class="btn btn-primary">Watch Full Video</a>
                            <a href="pictures.php" class="btn btn-outline">View Photos</a>
                            <a href="blog.php" class="btn btn-outline">Read Summary</a>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <h3 class="timeline-title">Corporate Governance of SOEs in Ghana: Panel Discussion</h3>
                        <div class="timeline-meta">
                            <strong>Theme:</strong> Challenges & Recommendations for SOE Governance<br>
                            <strong>Format:</strong> Panel Discussion<br>
                            <strong>Series:</strong> CGS SOE Governance
                        </div>
                        <p style="color: var(--text-charcoal); line-height: 1.7; margin-bottom: 1.5rem;">
                            A comprehensive panel discussion addressing the governance challenges facing State-Owned Enterprises in Ghana, with actionable recommendations from governance experts, regulators, and industry leaders.
                        </p>
                        <div class="video-container" style="margin: 1.5rem 0;">
                            <iframe src="https://www.youtube.com/embed/bVH51cX7dhs" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    title="Corporate Governance of SOEs in Ghana: Panel Discussion"></iframe>
                        </div>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <a href="https://www.youtube.com/watch?v=bVH51cX7dhs" target="_blank" class="btn btn-primary">Watch Recording</a>
                            <a href="blog.php" class="btn btn-outline">Read Summary</a>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <h3 class="timeline-title">Overview of State-Owned Enterprises in Ghana: Challenges and Recommendations | Mr. Joseph Sarpong</h3>
                        <div class="timeline-meta">
                            <strong>Speaker:</strong> Mr. Joseph Sarpong<br>
                            <strong>Format:</strong> Expert Presentation<br>
                            <strong>Series:</strong> CGS SOE Governance
                        </div>
                        <p style="color: var(--text-charcoal); line-height: 1.7; margin-bottom: 1.5rem;">
                            An insightful presentation by Mr. Joseph Sarpong providing a comprehensive overview of State-Owned Enterprises in Ghana, examining key challenges and offering practical recommendations for improved governance and operational effectiveness.
                        </p>
                        <div class="video-container" style="margin: 1.5rem 0;">
                            <iframe src="https://www.youtube.com/embed/RFs_7S6FCO0" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    title="Overview of State-Owned Enterprises in Ghana: Mr. Joseph Sarpong"></iframe>
                        </div>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <a href="https://www.youtube.com/watch?v=RFs_7S6FCO0" target="_blank" class="btn btn-primary">Watch Recording</a>
                            <a href="blog.php" class="btn btn-outline">Read Summary</a>
                        </div>
                    </div>
                </div>
                -->
            </div>
        </section>

        <section class="section content-section-alt">
            <div class="container" style="text-align: center; max-width: 700px; margin: 0 auto;">
                <h2 style="font-size: 2.25rem; margin-bottom: 1rem;">Stay Connected</h2>
                <p style="font-size: 1.1rem; color: var(--text-charcoal); margin-bottom: 2rem; line-height: 1.7;">
                    Join the network to receive first-to-know invitations for upcoming series sessions and access exclusive pre-event briefs and post-event resources.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="network.php" class="btn btn-primary">Join the Network</a>
                    <a href="contact.php" class="btn btn-outline">Contact Us</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>CGS</h3>
                    <p>Corporate Governance Series<br>Transforming governance standards across Africa</p>
                    <div class="social-links">
                        <a href="https://web.facebook.com/cstsghana/about" target="_blank" title="Facebook" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"></path>
                            </svg>
                        </a>
                        <a href="https://x.com/cstsgh" target="_blank" title="Twitter/X" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path>
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/cstsghana/#" target="_blank" title="Instagram" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.325 3.608 1.3.975.975 1.238 2.243 1.3 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.325 2.633-1.3 3.608-.975.975-2.243 1.238-3.608 1.3-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.325-3.608-1.3-.975-.975-1.238-2.243-1.3-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.325-2.633 1.3-3.608.975-.975 2.243-1.238 3.608-1.3 1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.257 0-3.667.012-4.947.07-1.417.062-2.798.364-3.877 1.443-1.078 1.078-1.381 2.46-1.443 3.877-.058 1.28-.07 1.69-.07 4.947s.012 3.667.07 4.947c.062 1.417.364 2.798 1.443 3.877 1.078 1.078 2.46 1.381 3.877 1.443 1.28.058 1.69.07 4.947.07s3.667-.012 4.947-.07c1.417-.062 2.798-.364 3.877-1.443 1.078-1.078 1.381-2.46 1.443-3.877.058-1.28.07-1.69.07-4.947s-.012-3.667-.07-4.947c-.062-1.417-.364-2.798-1.443-3.877-1.078-1.078-2.46-1.381-3.877-1.443-1.28-.058-1.69-.07-4.947-.07zm0 5.838c-3.403 0-6.162 2.76-6.162 6.162s2.76 6.162 6.162 6.162 6.162-2.76 6.162-6.162-2.76-6.162-6.162-6.162zm0 10.002c-2.117 0-3.841-1.725-3.841-3.841s1.725-3.841 3.841-3.841 3.841 1.725 3.841 3.841-1.725 3.841-3.841 3.841zm6.406-10.846c-.796 0-1.441-.646-1.441-1.441s.646-1.441 1.441-1.441 1.441.646 1.441 1.441-.646 1.441-1.441 1.441z"></path>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/in/csts-ghana-398975174/" target="_blank" title="LinkedIn" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.225 0h-20.451c-.979 0-1.774.796-1.774 1.774v20.451c0 .979.796 1.774 1.774 1.774h20.451c.979 0 1.774-.796 1.774-1.774v-20.451c0-.979-.796-1.774-1.774-1.774zm-15.451 20.451h-3.605v-11.69h3.605v11.69zm-1.802-13.243c-1.148 0-2.082-.935-2.082-2.082s.935-2.082 2.082-2.082 2.082.935 2.082 2.082-.935 2.082-2.082 2.082zm14.053 13.243h-3.605v-5.991c0-1.428-.028-3.27-1.991-3.27-1.993 0-2.297 1.554-2.297 3.164v6.097h-3.605v-11.69h3.461v1.597h.049c.482-.911 1.66-1.872 3.417-1.872 3.649 0 4.323 2.403 4.323 5.525v6.439z"></path>
                            </svg>
                        </a>
                        <a href="https://www.youtube.com/@cstsghana6166" target="_blank" title="YouTube" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.615 3.184c-2.096-.218-10.634-.218-12.73 0-2.536.263-4.5 2.18-4.772 4.748-.218 2.096-.218 6.466 0 8.562.263 2.536 2.18 4.5 4.748 4.772 2.096.218 10.634.218 12.73 0 2.536-.263 4.5-2.18 4.772-4.748.218-2.096.218-6.466 0-8.562-.263-2.536-2.18-4.5-4.748-4.772zm-9.615 11.816v-7l6 3.5-6 3.5z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About CGS</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="training.php">Training</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="governance-codes.php">Governance Codes</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="media.php">Media</a></li>
                        <li><a href="network.php">Network</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Stay Informed</h4>
                    <p>Subscribe to our newsletter for updates on events, resources, and governance insights.</p>
                    <form class="newsletter-form" id="newsletterForm">
                        <input type="email" placeholder="Your email address" required>
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Corporate Governance Series (CGS) by CSTS Ghana. All rights reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a> | <a href="admin-login.php" style="color: var(--accent-gold);">Admin Login</a></p>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-light);">Website by <a href="https://www.dennislaw.com" target="_blank" style="color: var(--accent-gold); font-weight: 600;">ADDENS TECHNOLOGY LIMITED</a> - Makers of DENNISLAW</p>
            </div>
        </div>
    </footer>

    <!-- Event Details Modal -->
    <div id="eventDetailsModal" class="event-modal">
        <div class="event-modal-content">
            <span class="event-modal-close" onclick="closeEventModal()">&times;</span>
            <div id="eventModalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script src="events-loader.js"></script>
    <script>
        // Load current and upcoming series events when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCurrentAndUpcomingEvents('series');
        });
        
        // Function to load current and upcoming events separately
        async function loadCurrentAndUpcomingEvents(type = null) {
            try {
                let apiUrl = 'api-events.php?';
                if (type) apiUrl += `type=${encodeURIComponent(type)}&`;
                
                const response = await fetch(apiUrl);
                const data = await response.json();
                
                if (data.success && data.events) {
                    const now = new Date();
                    now.setHours(0, 0, 0, 0);
                    
                    // Separate upcoming events
                    const upcomingEvents = [];
                    
                    data.events.forEach(event => {
                        if (!event.event_date) {
                            // If no date, check status
                            if (event.status === 'upcoming') {
                                upcomingEvents.push(event);
                            }
                            return;
                        }
                        
                        const eventDate = new Date(event.event_date);
                        eventDate.setHours(0, 0, 0, 0);
                        
                        // Upcoming events: future dates or status is upcoming
                        if (eventDate > now || event.status === 'upcoming') {
                            upcomingEvents.push(event);
                        }
                    });
                    
                    // Display upcoming events
                    const upcomingContainer = document.getElementById('upcomingSeriesEvents');
                    if (upcomingContainer) {
                        if (upcomingEvents.length === 0) {
                            upcomingContainer.innerHTML = '<p style="text-align: center; color: var(--text-light); padding: 2rem;">No upcoming events scheduled.</p>';
                        } else {
                            upcomingContainer.innerHTML = upcomingEvents.map((event, index) => createEventListItem(event, index, 'upcoming')).join('');
                            // Store events for modal access
                            window.upcomingEvents = upcomingEvents;
                            window.allEvents.upcoming = upcomingEvents;
                        }
                    }
                } else {
                    console.error('Error loading events:', data);
                    const upcomingContainer = document.getElementById('upcomingSeriesEvents');
                    if (upcomingContainer) {
                        upcomingContainer.style.display = 'none';
                    }
                }
            } catch (error) {
                console.error('Error fetching events:', error);
                const upcomingContainer = document.getElementById('upcomingSeriesEvents');
                if (upcomingContainer) {
                    upcomingContainer.style.display = 'none';
                }
            }
        }
        
        // Create IBA-style event list item
        function createEventListItem(event, index, prefix) {
            const eventDate = event.event_date ? formatDate(event.event_date) : '';
            const eventTime = event.event_date ? formatTime(event.event_date) : '';
            const eventType = event.event_type || 'series';
            const location = event.location || '';
            const uniqueId = `${prefix}-${index}`;
            
            let metaItems = [];
            if (eventDate) {
                if (eventTime) {
                    metaItems.push(`${eventDate} ${eventTime}`);
                } else {
                    metaItems.push(eventDate);
                }
            }
            if (location) {
                metaItems.push(location);
            }
            
            const typeClass = eventType.toLowerCase();
            const typeLabel = eventType.charAt(0).toUpperCase() + eventType.slice(1);
            
            let html = `
                <div class="event-list-item" data-event-id="${uniqueId}">
                    <div class="event-list-header" onclick="openEventDetails(${index}, '${prefix}')">
                        <div class="event-list-header-content">
                            <h3 class="event-list-title">${escapeHtml(event.title)}</h3>
                            ${metaItems.length > 0 ? `
                                <div class="event-list-meta">
                                    ${metaItems.map(item => `<span class="event-list-meta-item">${escapeHtml(item)}</span>`).join('')}
                                </div>
                            ` : ''}
                        </div>
                        <div class="event-list-actions">
                            ${event.registration_link ? `
                                <a href="${escapeHtml(event.registration_link)}" target="_blank" class="btn btn-primary" onclick="event.stopPropagation();">Book now</a>
                            ` : ''}
                            <button class="event-list-toggle" aria-label="View event details">▶</button>
                        </div>
                        <span class="event-list-type-badge ${typeClass}">${typeLabel}</span>
                    </div>
                </div>
            `;
            
            return html;
        }
        
        // Store all events globally
        window.allEvents = {
            upcoming: [],
            past: []
        };
        
        // Open event details modal
        function openEventDetails(index, type) {
            let events;
            if (type === 'upcoming') {
                events = window.upcomingEvents || window.allEvents.upcoming;
            }
            
            if (!events || !events[index]) {
                console.error('Event not found:', index, type, events);
                return;
            }
            
            const event = events[index];
            const modal = document.getElementById('eventDetailsModal');
            const modalContent = document.getElementById('eventModalContent');
            
            const eventDate = event.event_date ? formatDateForModal(event.event_date) : '';
            const eventTime = event.event_date ? formatTime(event.event_date) : '';
            const eventType = event.event_type || 'series';
            const typeLabel = eventType.charAt(0).toUpperCase() + eventType.slice(1);
            
            let html = `
                <div class="event-detail-header">
                    <h1 class="event-detail-title">${escapeHtml(event.title)}</h1>
                    <div class="event-detail-meta-top">
                        ${eventDate ? `<span class="event-detail-date">${eventDate}${eventTime ? `  ${eventTime} GMT` : ''}</span>` : ''}
                        <span class="event-type-badge-large ${eventType.toLowerCase()}">${typeLabel}</span>
                    </div>
                </div>
                
                <div class="event-detail-menu">
                    ${event.registration_link ? `
                        <a href="${escapeHtml(event.registration_link)}" target="_blank" class="btn btn-primary event-book-btn">Book now</a>
                    ` : ''}
                </div>
                
                <div class="event-detail-content">
                    ${event.description ? `
                        <div class="event-detail-description">
                            <div id="eventDescriptionShort">
                                <p>${escapeHtml(event.description.substring(0, 200))}${event.description.length > 200 ? '...' : ''}</p>
                                ${event.description.length > 200 ? `<button class="read-more-btn" onclick="toggleDescription()">Read more</button>` : ''}
                            </div>
                            ${event.description.length > 200 ? `
                                <div id="eventDescriptionFull" style="display: none;">
                                    <p>${escapeHtml(event.description)}</p>
                                    <button class="read-more-btn" onclick="toggleDescription()">Read less</button>
                                </div>
                            ` : ''}
                        </div>
                    ` : ''}
                    
                    ${event.moderator ? `
                        <div class="event-detail-section">
                            <h3 class="event-detail-section-title">Moderator</h3>
                            <div class="speaker-card">
                                <div class="speaker-info">
                                    <h4 class="speaker-name">${escapeHtml(event.moderator)}</h4>
                                    ${event.moderator_role ? `<p class="speaker-role">${escapeHtml(event.moderator_role)}</p>` : ''}
                                    ${event.moderator_bio ? `
                                        <div class="speaker-bio-toggle">
                                            <button class="read-bio-btn" onclick="toggleSpeakerBio('moderator')">Read biography</button>
                                        </div>
                                        <div id="moderator-bio" class="speaker-bio-content" style="display: none;">
                                            <p>${escapeHtml(event.moderator_bio)}</p>
                                            <button class="read-bio-btn" onclick="toggleSpeakerBio('moderator')">Close biography</button>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    
                    ${event.speakers ? `
                        <div class="event-detail-section">
                            <h3 class="event-detail-section-title">Confirmed Speakers</h3>
                            <div class="speakers-grid">
                                ${event.speakers.split(',').map((speaker, idx) => {
                                    const speakerName = speaker.trim();
                                    return `
                                        <div class="speaker-card">
                                            <div class="speaker-info">
                                                <h4 class="speaker-name">${escapeHtml(speakerName)}</h4>
                                                ${event.speaker_roles && event.speaker_roles.split(',')[idx] ? `
                                                    <p class="speaker-role">${escapeHtml(event.speaker_roles.split(',')[idx].trim())}</p>
                                                ` : ''}
                                                ${event.speaker_bios && event.speaker_bios.split('|||')[idx] ? `
                                                    <div class="speaker-bio-toggle">
                                                        <button class="read-bio-btn" onclick="toggleSpeakerBio('speaker-${idx}')">Read biography</button>
                                                    </div>
                                                    <div id="speaker-${idx}-bio" class="speaker-bio-content" style="display: none;">
                                                        <p>${escapeHtml(event.speaker_bios.split('|||')[idx].trim())}</p>
                                                        <button class="read-bio-btn" onclick="toggleSpeakerBio('speaker-${idx}')">Close biography</button>
                                                    </div>
                                                ` : ''}
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    ` : ''}
                    
                    ${event.agenda ? `
                        <div class="event-detail-section">
                            <h3 class="event-detail-section-title">Agenda</h3>
                            <div class="event-agenda">
                                <pre class="agenda-content">${escapeHtml(event.agenda)}</pre>
                            </div>
                        </div>
                    ` : ''}
                    
                    ${event.location ? `
                        <div class="event-detail-section">
                            <h3 class="event-detail-section-title">Location</h3>
                            <p class="event-location">${escapeHtml(event.location)}</p>
                            ${event.format ? `<p class="event-format">Format: ${escapeHtml(event.format)}</p>` : ''}
                        </div>
                    ` : ''}
                    
                    ${event.youtube_url ? `
                        <div class="event-detail-section">
                            <h3 class="event-detail-section-title">Recording</h3>
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/${getYouTubeId(event.youtube_url)}" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen
                                        title="${escapeHtml(event.title)}"></iframe>
                            </div>
                            <a href="${escapeHtml(event.youtube_url)}" target="_blank" class="btn btn-outline" style="margin-top: 1rem;">Watch on YouTube</a>
                        </div>
                    ` : ''}
                    
                    ${event.registration_link ? `
                        <div class="event-detail-cta">
                            <a href="${escapeHtml(event.registration_link)}" target="_blank" class="btn btn-primary btn-large">Register Now</a>
                        </div>
                    ` : ''}
                </div>
            `;
            
            modalContent.innerHTML = html;
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        // Close event modal
        function closeEventModal() {
            const modal = document.getElementById('eventDetailsModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
        
        // Toggle description
        function toggleDescription() {
            const short = document.getElementById('eventDescriptionShort');
            const full = document.getElementById('eventDescriptionFull');
            if (short && full) {
                const isFullVisible = full.style.display !== 'none';
                short.style.display = isFullVisible ? 'block' : 'none';
                full.style.display = isFullVisible ? 'none' : 'block';
            }
        }
        
        // Toggle speaker bio
        function toggleSpeakerBio(id) {
            const bio = document.getElementById(`${id}-bio`);
            if (bio) {
                bio.style.display = bio.style.display === 'none' ? 'block' : 'none';
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('eventDetailsModal');
            if (event.target === modal) {
                closeEventModal();
            }
        }
        
        // Helper functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('en-GB', options);
        }
        
        function formatTime(dateString) {
            const date = new Date(dateString);
            let hours = date.getHours();
            const minutes = date.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            return `${hours}:${minutes} ${ampm}`;
        }
        
        function formatDateForModal(dateString) {
            const date = new Date(dateString);
            const day = date.getDate();
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();
            return `${day} ${month} ${year}`;
        }
        
        function getYouTubeId(url) {
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
            const match = url.match(regExp);
            return (match && match[2].length === 11) ? match[2] : null;
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.toString().replace(/[&<>"']/g, m => map[m]);
        }
        
        // Open past event details
        function openPastEventDetails() {
            const modal = document.getElementById('eventDetailsModal');
            const modalContent = document.getElementById('eventModalContent');
            
            const html = `
                <div class="event-detail-header">
                    <h1 class="event-detail-title">Strengthening Corporate Governance of State-Owned Entities in Ghana: Opportunities and Challenges</h1>
                    <div class="event-detail-meta-top">
                        <span class="event-detail-date">13 Nov 2025</span>
                        <span class="event-type-badge-large series">Series</span>
                    </div>
                </div>
                
                <div class="event-detail-content">
                    <div class="event-detail-description">
                        <p>A comprehensive discussion exploring the critical opportunities and challenges in strengthening corporate governance of State-Owned Entities (SOEs) in Ghana. This session brought together governance experts, regulators, and industry leaders to address key governance challenges and provide actionable recommendations.</p>
                    </div>
                    
                    <div class="event-detail-section">
                        <div class="event-image-container">
                            <img src="images/gallery/New/Fredan-94.JPG" alt="Strengthening Corporate Governance of State-Owned Entities in Ghana" class="event-detail-image">
                        </div>
                    </div>
                    
                    <div class="event-detail-section">
                        <h3 class="event-detail-section-title">Format</h3>
                        <p class="event-location">In-Person Session</p>
                    </div>
                    
                    <div class="event-detail-section">
                        <h3 class="event-detail-section-title">Location</h3>
                        <p class="event-location">GIMPA Law School, Dr. Daniel McKarley Moot Court Room</p>
                    </div>
                    
                    <div class="event-detail-section">
                        <h3 class="event-detail-section-title">Moderator</h3>
                        <div class="speaker-card">
                            <div class="speaker-image-container">
                                <img src="images/past events/Gertrude.png" alt="Gertrude Amorkor Amarh" class="speaker-image">
                            </div>
                            <div class="speaker-info">
                                <h4 class="speaker-name">Gertrude Amorkor Amarh</h4>
                                <p class="speaker-role">Lecturer, UPSA Law School</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="event-detail-section">
                        <h3 class="event-detail-section-title">Panel Members</h3>
                        <div class="speakers-grid">
                            <div class="speaker-card">
                                <div class="speaker-image-container">
                                    <img src="images/past events/Joseph.png" alt="Joseph Sarpong" class="speaker-image">
                                </div>
                                <div class="speaker-info">
                                    <h4 class="speaker-name">Joseph Sarpong</h4>
                                    <p class="speaker-role">Ag. Head of Governance, Risk and Compliance, SIGA</p>
                                </div>
                            </div>
                            <div class="speaker-card">
                                <div class="speaker-image-container">
                                    <img src="images/past events/Alfred.png" alt="Alfred Mahamadu Braimah" class="speaker-image">
                                </div>
                                <div class="speaker-info">
                                    <h4 class="speaker-name">Alfred Mahamadu Braimah, PhD. FIoD, CA</h4>
                                    <p class="speaker-role">CEO, IoD-Ghana</p>
                                </div>
                            </div>
                            <div class="speaker-card">
                                <div class="speaker-image-container">
                                    <img src="images/past events/Prof.png" alt="Professor Albert Puni" class="speaker-image">
                                </div>
                                <div class="speaker-info">
                                    <h4 class="speaker-name">Professor Albert Puni</h4>
                                    <p class="speaker-role">Professor of Management, UPSA</p>
                                </div>
                            </div>
                            <div class="speaker-card">
                                <div class="speaker-image-container">
                                    <img src="images/past events/Yao.png" alt="Mr. E. Yao Klinogo" class="speaker-image">
                                </div>
                                <div class="speaker-info">
                                    <h4 class="speaker-name">Mr. E. Yao Klinogo</h4>
                                    <p class="speaker-role">Management Consultant, KNG & Associates</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="event-detail-section">
                        <h3 class="event-detail-section-title">Event Videos</h3>
                        <div class="event-videos-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 1.5rem;">
                            <div class="event-video-item">
                                <div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px;">
                                    <iframe src="https://www.youtube.com/embed/1809paF4iuk" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen
                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                            title="LIVE: Strengthening Corporate Governance of State-Owned Entities in Ghana"></iframe>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <h4 style="font-size: 1.1rem; color: var(--primary-navy); margin-bottom: 0.5rem;">LIVE: Strengthening Corporate Governance of State-Owned Entities in Ghana: Opportunities & Challenges</h4>
                                    <p style="font-size: 0.9rem; color: var(--text-charcoal); margin-bottom: 0.75rem;">An in-depth live discussion exploring the critical opportunities and challenges in strengthening corporate governance of State-Owned Entities (SOEs) in Ghana.</p>
                                    <div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 0.75rem;">
                                        <strong>Duration:</strong> 1:14:22
                                    </div>
                                    <a href="https://www.youtube.com/watch?v=1809paF4iuk" target="_blank" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Watch on YouTube →</a>
                                </div>
                            </div>
                            
                            <div class="event-video-item">
                                <div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px;">
                                    <iframe src="https://www.youtube.com/embed/bVH51cX7dhs" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen
                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                            title="Corporate Governance of SOEs in Ghana: Panel Discussion"></iframe>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <h4 style="font-size: 1.1rem; color: var(--primary-navy); margin-bottom: 0.5rem;">Corporate Governance of SOEs in Ghana: Challenges & Recommendations | Panel Discussion</h4>
                                    <p style="font-size: 0.9rem; color: var(--text-charcoal); margin-bottom: 0.75rem;">A comprehensive panel discussion addressing the governance challenges facing State-Owned Enterprises in Ghana, with actionable recommendations.</p>
                                    <div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 0.75rem;">
                                        <strong>Format:</strong> Panel Discussion
                                    </div>
                                    <a href="https://www.youtube.com/watch?v=bVH51cX7dhs" target="_blank" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Watch on YouTube →</a>
                                </div>
                            </div>
                            
                            <div class="event-video-item">
                                <div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px;">
                                    <iframe src="https://www.youtube.com/embed/RFs_7S6FCO0" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen
                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                            title="Overview of State-Owned Enterprises in Ghana"></iframe>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <h4 style="font-size: 1.1rem; color: var(--primary-navy); margin-bottom: 0.5rem;">Overview of State-Owned Enterprises in Ghana: Challenges and Recommendations | Mr. Joseph Sarpong</h4>
                                    <p style="font-size: 0.9rem; color: var(--text-charcoal); margin-bottom: 0.75rem;">An insightful presentation providing a comprehensive overview of State-Owned Enterprises in Ghana, examining key challenges and offering practical recommendations.</p>
                                    <div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 0.75rem;">
                                        <strong>Speaker:</strong> Mr. Joseph Sarpong
                                    </div>
                                    <a href="https://www.youtube.com/watch?v=RFs_7S6FCO0" target="_blank" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Watch on YouTube →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="event-detail-cta">
                        <a href="pictures.php" class="btn btn-outline">View Photos</a>
                    </div>
                </div>
            `;
            
            modalContent.innerHTML = html;
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    </script>
</body>
</html>
