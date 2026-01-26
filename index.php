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
    <title>Corporate Governance Series (CGS) | Elevating Governance Standards in Africa</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CGS II Banner Responsive Styles */
        @media (max-width: 768px) {
            #cgsIIBanner h2 {
                font-size: 1.4rem !important;
            }
            #cgsIIBanner .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'header-main.php'; ?>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1 class="hero-title">Elevating the Standard of Corporate Governance in Africa</h1>
                <p class="hero-subtitle">Corporate Governance Series (CGS) is the premier platform for African stakeholders to debate, refine, and implement world-class governance frameworks, with Ghana as a strategic launchpad.</p>
                <div class="hero-buttons">
                    <a href="events.php" class="btn btn-primary">Explore the Series</a>
                    <a href="network.php" class="btn btn-secondary">Join the Network</a>
                </div>
            </div>
        </section>

        <!-- CGS II Event Announcement Banner -->
        <section id="cgsIIBanner" class="section" style="padding: 2rem 0; background: linear-gradient(135deg, var(--accent-gold) 0%, #d4a017 100%); position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px); opacity: 0.3; z-index: 0;"></div>
            <div class="container" style="position: relative; z-index: 1;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 2rem; max-width: 1400px; margin: 0 auto; padding: 1.5rem 2rem;">
                    <div style="flex: 1; min-width: 300px;">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                            <span style="background-color: var(--primary-navy); color: white; padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">New Event</span>
                            <span style="color: var(--primary-navy); font-weight: 600; font-size: 0.9rem;">Series Event</span>
                        </div>
                        <h2 style="font-size: 1.75rem; color: var(--primary-navy); margin-bottom: 0.5rem; font-weight: 700; line-height: 1.3;">
                            CGS II: Bank Corporate Governance and Financial Stability
                        </h2>
                        <p style="color: var(--primary-navy); font-size: 1rem; margin-bottom: 0; opacity: 0.9; font-weight: 500;">
                            Thursday, February 12, 2026 at 5:00 PM (Africa/Accra) | Hybrid (Online & In-Person)
                        </p>
                    </div>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; width: 100%; max-width: 400px;">
                        <button onclick="openCGSIIModal()" class="btn" style="background-color: var(--primary-navy); color: white; padding: 1rem 2rem; font-weight: 600; font-size: 1rem; border: none; border-radius: 4px; cursor: pointer; transition: all 0.3s ease; flex: 1; min-width: 150px;">
                            View Details
                        </button>
                        <a href="register-cgs-ii.php" class="btn" style="background-color: white; color: var(--primary-navy); padding: 1rem 2rem; font-weight: 600; font-size: 1rem; text-decoration: none; border-radius: 4px; transition: all 0.3s ease; flex: 1; min-width: 150px; border: 2px solid var(--primary-navy); text-align: center;">
                            Register Now →
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Upcoming Event Banner (IBA-style) - Prominently placed at top -->
        <section class="section" style="padding: 4rem 0; background-color: var(--bg-offwhite);">
            <div class="container">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; max-width: 1400px; margin: 0 auto;">
                    <!-- Left Card: About CGS -->
                    <div class="featured-card" style="background-color: var(--white); padding: 3rem; border-radius: 8px; box-shadow: var(--shadow); display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h2 style="font-size: 2rem; margin-bottom: 1.5rem; color: var(--primary-navy);">About CGS</h2>
                            <p style="font-size: 1.1rem; line-height: 1.8; color: var(--text-charcoal); margin-bottom: 2rem;">
                                The foremost platform for African governance stakeholders. CGS was born out of the conviction that we can contribute to sustainable business growth across Africa through the promotion and protection of corporate governance excellence.
                            </p>
                            <a href="about.php" style="color: var(--primary-navy); text-decoration: none; font-weight: 600;">Read more →</a>
                        </div>
                        <div style="margin-top: 2rem;">
                            <a href="network.php" class="btn" style="background-color: var(--primary-navy); color: white; padding: 1rem 2rem; font-weight: 600; font-size: 1rem; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 0.5rem; transition: background-color 0.3s ease;">
                                <span>👥</span> Join the Network
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right Card: Featured Event Banner -->
                    <div id="featuredEventCard" class="featured-card" style="background: linear-gradient(135deg, var(--primary-navy) 0%, #0d2f7a 100%); padding: 3rem; border-radius: 8px; box-shadow: var(--shadow); position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; color: white;">
                        <!-- Background pattern overlay -->
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.03) 10px, rgba(255,255,255,0.03) 20px); opacity: 0.5; z-index: 0;"></div>
                        
                        <!-- Event Content -->
                        <div style="position: relative; z-index: 1;">
                            <div style="background-color: rgba(255, 255, 255, 0.2); color: white; padding: 0.5rem 1rem; border-radius: 4px; display: inline-block; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1.5rem; font-weight: 600;">
                                Upcoming Event
                            </div>
                            <h2 id="featuredEventTitle" style="font-size: 2rem; margin-bottom: 1.5rem; color: white; line-height: 1.3; font-weight: 700;">
                                Directors' Duties and Corporate Misconduct: Comparative Insights on Liability Regimes
                            </h2>
                            <div id="featuredEventMeta" style="margin-bottom: 1.5rem; font-size: 1rem; opacity: 0.95; display: flex; flex-direction: column; gap: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;"><span style="font-size: 1.2rem;">📅</span> <span><strong>Date:</strong> 15th January 2026</span></div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;"><span style="font-size: 1.2rem;">🕐</span> <span><strong>Time:</strong> 3pm GMT</span></div>
                            </div>
                        </div>
                        
                        <div style="position: relative; z-index: 1; display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 2rem;">
                            <a id="featuredEventButton" href="register-cgs-ii.php" class="btn" style="background-color: var(--accent-gold); color: var(--primary-navy); padding: 1rem 2rem; font-weight: 600; font-size: 1rem; text-decoration: none; border-radius: 4px; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <span>Register Now</span> <span>→</span>
                            </a>
                            <a href="events.php" class="btn" style="background-color: rgba(255, 255, 255, 0.1); color: white; padding: 1rem 2rem; font-weight: 600; font-size: 1rem; text-decoration: none; border-radius: 4px; border: 2px solid rgba(255, 255, 255, 0.3); transition: all 0.3s ease;">
                                All events
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- <section class="trust-markers">
            <div class="trust-markers-container">
                <div class="trust-marker">
                    <strong>Convenor</strong>
                    <img src="images/CSTS Logo.png" alt="CSTS Ghana" class="convenor-logo" onerror="this.style.display='none';">
                </div>
                <div class="trust-marker strategic-partners-marker">
                    <strong>Strategic Partners</strong>
                    <div class="strategic-partners-logos">
                        <div class="strategic-partner-logo">
                            <img src="images/logo_GIMPS2.png" alt="GIMPA Law School" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <span class="partner-name" style="display: none;">GIMPA Law School</span>
                        </div>
                        <div class="strategic-partner-logo">
                            <img src="images/siga_logo-01.png" alt="SIGA" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <span class="partner-name" style="display: none;">SIGA</span>
                        </div>
                        <div class="strategic-partner-logo">
                            <img src="images/iodghana-logo-white.png" alt="IOD" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <span class="partner-name" style="display: none;">IOD</span>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->

        <section class="section delivers-section">
            <div class="container">
                <h2 class="section-title">What CGS Delivers</h2>
                <div class="delivers-grid">
                    <div class="deliver-card">
                        <h3>High-level Dialogue</h3>
                        <p>Board-ready conversations that move from governance theory to operating reality.</p>
                    </div>
                    <div class="deliver-card">
                        <h3>Knowledge Vault</h3>
                        <p>Codes, playbooks, and executive insights built for busy decision-makers.</p>
                    </div>
                    <div class="deliver-card">
                        <h3>Capability Building</h3>
                        <p>Training and board support that strengthens governance at the source.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section content-section">
            <div class="container">
                <h2 class="section-title">Featured Video</h2>
                <div style="max-width: 900px; margin: 0 auto;">
                    <div class="video-wrapper">
                        <div class="video-container">
                            <iframe src="https://www.youtube.com/embed/1809paF4iuk" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    title="CGS Accra 2025: Inaugural Series Highlights"></iframe>
                        </div>
                        <div style="padding: 2rem; background-color: var(--white);">
                            <h3 style="font-size: 1.75rem; margin-bottom: 1rem; color: var(--primary-navy);">LIVE: Strengthening Corporate Governance of State-Owned Entities in Ghana: Opportunities & Challenges</h3>
                            <p style="color: var(--text-charcoal); line-height: 1.7; margin-bottom: 1.5rem;">
                                An in-depth live discussion exploring the critical opportunities and challenges in strengthening corporate governance of State-Owned Entities (SOEs) in Ghana. Featuring expert panelists and stakeholders.
                            </p>
                            <div style="font-size: 0.9rem; color: var(--text-charcoal); margin-bottom: 1.5rem;">
                                <strong>Duration:</strong> 1:14:22 | <strong>Series:</strong> CGS SOE Governance
                            </div>
                            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                <a href="https://www.youtube.com/watch?v=1809paF4iuk" target="_blank" class="btn btn-primary">Watch on YouTube</a>
                                <a href="videos.php" class="btn btn-outline">View All Videos</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section updates-section">
            <div class="container">
                <h2 class="section-title">Latest Updates</h2>
                <div class="updates-grid">
                    <article class="update-card">
                        <div class="update-content">
                            <span class="update-category">From the Blog</span>
                            <h3>The 2025 Boardroom: Navigating AI Ethics under the New Ghana Companies Act</h3>
                            <p>Exploring how boards must adapt their oversight frameworks to address artificial intelligence governance, data ethics, and algorithmic accountability in an evolving regulatory landscape.</p>
                            <a href="blog.php" class="update-link">Read Article →</a>
                        </div>
                    </article>
                    <article class="update-card">
                        <div class="update-content">
                            <span class="update-category">From the Codes Vault</span>
                            <h3>Ghana: National Corporate Governance Code (Latest)</h3>
                            <p>The most recent edition of Ghana's national corporate governance code, providing comprehensive guidance for boards, directors, and governance professionals across all sectors.</p>
                            <a href="governance-codes.php" class="update-link">View Code →</a>
                        </div>
                    </article>
                    <article class="update-card">
                        <div class="update-content">
                            <span class="update-category">Upcoming Event</span>
                            <h3>Directorship in the Digital Age (Monthly Series)</h3>
                            <p>Join our monthly series exploring practical governance in an era of AI, cyber risk, data governance, ESG, and increased stakeholder scrutiny. Register now for the next session.</p>
                            <a href="webinar-diary.php" class="update-link">Register →</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="quote-section">
            <div class="container">
                <div class="quote-content">
                    <p class="quote-text">"CGS is the dialogue Africa needs now: practical, principled, and action-oriented."</p>
                    <div class="quote-author">Featured Governance Leader</div>
                    <div class="quote-role">Board Chair, Leading Financial Institution</div>
                </div>
            </div>
        </section>

        <section class="partner-strip">
            <div class="container">
                <div class="partner-strip-content">
                    <!-- Left Side: Convenor -->
                    <div class="partner-section-left">
                        <h3 class="strategic-partners-title">Convenor</h3>
                        <div class="partner-logos">
                            <div class="partner-logo">
                                <img src="images/CSTS Logo.png" alt="CSTS Ghana" onerror="this.style.display='none';">
                                <span class="partner-name">CSTS Ghana</span>
                            </div>
                           
                        </div>
                    </div>
                    
                    <!-- Right Side: Strategic Partners -->
                    <div class="partner-section-right">
                        <h3 class="strategic-partners-title">Strategic Partners</h3>
                        <div class="partner-logos">
                            <div class="partner-logo">
                                <img src="images/partners/Screenshot-2025-11-10-130506.png" alt="GIMPA Law School" onerror="this.style.display='none';">
                                <span class="partner-name">GIMPA Law School</span>
                            </div>
                            <div class="partner-logo">
                                <img src="images/partners/siga_logo-01-2048x565-1.png" alt="SIGA" onerror="this.style.display='none';">
                                <span class="partner-name">SIGA</span>
                            </div>
                            <div class="partner-logo">
                                <img src="images/partners/ID3.png" alt="IOD" onerror="this.style.display='none';">
                                <span class="partner-name">IOD</span>
                            </div>
                            <div class="partner-logo">
                                <img src="images/partners/logo_2.webp" alt="VOLTIC" onerror="this.style.display='none';">
                                <span class="partner-name">VOLTIC</span>
                            </div>
                            <div class="partner-logo">
                                <img src="images/partners/22030173923DL-NEWS-LOGO.jpg" alt="DLNEWS" onerror="this.style.display='none';">
                                <span class="partner-name">DLNEWS</span>
                            </div>
                            <div class="partner-logo">
                                <img src="images/partners/Jema.png" alt="JEM" onerror="this.style.display='none';">
                                <span class="partner-name">JEMA</span>
                            </div>
                            <div class="partner-logo">
                                <img src="images/partners/Joy-business.png" alt="JEM" onerror="this.style.display='none';">
                                <span class="partner-name">JOY BUSINESS</span>
                            </div>
                            <div class="partner-logo">
                                <img src="images/partners/updated-mx24logo-300x88-1.png" alt="JEM" onerror="this.style.display='none';">
                                <span class="partner-name">MX24TV</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section content-section-alt">
            <div class="container">
                <div style="text-align: center; max-width: 700px; margin: 0 auto;">
                    <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Build your governance advantage.</h2>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 2rem;">
                        <a href="network.php" class="btn btn-primary">Join the Network</a>
                        <a href="contact.php" class="btn btn-outline">Contact CGS</a>
                    </div>
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

    <script src="script.js"></script>
    <script src="events-loader.js"></script>
    <script>
        // Load featured upcoming event on homepage
        document.addEventListener('DOMContentLoaded', function() {
            loadFeaturedEvent();
            
            // Header search functionality
            const headerSearchInput = document.getElementById('headerSearchInput');
            if (headerSearchInput) {
                headerSearchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        // Open the search modal with the search term
                        const searchBtn = document.getElementById('searchBtn');
                        if (searchBtn) {
                            searchBtn.click();
                            // Set the search value in the modal
                            setTimeout(() => {
                                const searchModalInput = document.querySelector('.search-modal-content input[type="text"]');
                                if (searchModalInput) {
                                    searchModalInput.value = headerSearchInput.value;
                                    searchModalInput.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                            }, 100);
                        }
                    }
                });
            }
        });
        
        // Load the next upcoming event as featured
        async function loadFeaturedEvent() {
            try {
                const response = await fetch('api-events.php?status=upcoming&limit=1');
                const data = await response.json();
                
                if (data.success && data.events && data.events.length > 0) {
                    const event = data.events[0];
                    const eventDate = formatDate(event.event_date);
                    const eventTime = formatTime(event.event_date);
                    
                    // Only update title if it's different, otherwise keep the default topic
                    const titleElement = document.getElementById('featuredEventTitle');
                    if (titleElement && event.title && event.title.trim() !== '') {
                        titleElement.textContent = event.title;
                    }
                    
                    let metaHTML = '';
                    if (eventDate) {
                        metaHTML += `<div style="display: flex; align-items: center; gap: 0.5rem;"><span style="font-size: 1.2rem;">📅</span> <span>${eventDate}</span></div>`;
                    }
                    if (event.location) {
                        metaHTML += `<div style="display: flex; align-items: center; gap: 0.5rem;"><span style="font-size: 1.2rem;">📍</span> <span>${escapeHtml(event.location)}</span></div>`;
                    }
                    if (event.format) {
                        metaHTML += `<div style="display: flex; align-items: center; gap: 0.5rem;"><span style="font-size: 1.2rem;">💻</span> <span>${escapeHtml(event.format)}</span></div>`;
                    }
                    const metaElement = document.getElementById('featuredEventMeta');
                    if (metaElement) {
                        metaElement.innerHTML = metaHTML;
                    }
                    
                    const button = document.getElementById('featuredEventButton');
                    if (button) {
                        if (event.registration_link) {
                            button.href = event.registration_link;
                        } else {
                            button.href = 'events.php';
                        }
                    }
                } else {
                    // If no events from database, the static flyer and topic will remain visible
                    // This is fine - the flyer is already displayed
                }
            } catch (error) {
                console.error('Error loading featured event:', error);
                // Hide featured event section on error
                const featuredSection = document.querySelector('.featured-event-section');
                if (featuredSection) {
                    featuredSection.style.display = 'none';
                }
            }
        }
        
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric'
            });
        }
        
        function formatTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit'
            });
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // CGS II Event Modal Functions
        function openCGSIIModal() {
            const modal = document.getElementById('cgsIIModal');
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeCGSIIModal() {
            const modal = document.getElementById('cgsIIModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('cgsIIModal');
            if (event.target === modal) {
                closeCGSIIModal();
            }
        });
    </script>

    <!-- CGS II Event Modal Popup -->
    <div id="cgsIIModal" class="event-modal" style="display: none;">
        <div class="event-modal-content">
            <span class="event-modal-close" onclick="closeCGSIIModal()">&times;</span>
            <div style="padding: 3rem; max-width: 900px; margin: 0 auto;">
                <!-- Event Image -->
                <div style="width: 100%; margin-bottom: 2rem; border-radius: 8px; overflow: hidden; box-shadow: var(--shadow);">
                    <img src="images/bank-corporate-governance.jpeg" alt="CGS II: Bank Corporate Governance and Financial Stability" style="width: 100%; height: auto; display: block; object-fit: cover;">
                </div>

                <!-- Event Header -->
                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <span style="background-color: var(--primary-navy); color: white; padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">CGS II</span>
                        <span style="background-color: var(--accent-gold); color: var(--primary-navy); padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Series Event</span>
                        <span style="color: var(--text-light); font-size: 0.9rem;">Upcoming</span>
                    </div>
                    <h1 style="font-size: 2.5rem; color: var(--primary-navy); margin-bottom: 1.5rem; font-weight: 700; line-height: 1.3;">
                        Bank Corporate Governance and Financial Stability: The Role of Bank Boards
                    </h1>
                    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 1.5rem;">📅</span>
                            <div>
                                <div style="font-weight: 600; color: var(--text-charcoal);">Date</div>
                                <div style="color: var(--text-light);">Thursday, February 12, 2026</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 1.5rem;">🕐</span>
                            <div>
                                <div style="font-weight: 600; color: var(--text-charcoal);">Time</div>
                                <div style="color: var(--text-light);">5:00 PM (Africa/Accra / GMT)</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 1.5rem;">💻</span>
                            <div>
                                <div style="font-weight: 600; color: var(--text-charcoal);">Format</div>
                                <div style="color: var(--text-light);">Hybrid (Online & In-Person)</div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 1.5rem;">📍</span>
                            <div>
                                <div style="font-weight: 600; color: var(--text-charcoal);">Location</div>
                                <div style="color: var(--text-light);">Dr. Daniel McKorley Moot Court Room, GIMPA Law School</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Description -->
                <div style="margin-bottom: 2rem;">
                    <h2 style="font-size: 1.5rem; color: var(--primary-navy); margin-bottom: 1rem; font-weight: 700;">About This Event</h2>
                    <p style="font-size: 1.1rem; line-height: 1.8; color: var(--text-charcoal); margin-bottom: 1rem;">
                        Join us for CGS II, a critical discussion on bank corporate governance and financial stability, focusing on the essential role of bank boards in ensuring robust governance frameworks and financial stability in the banking sector.
                    </p>
                    <p style="font-size: 1.1rem; line-height: 1.8; color: var(--text-charcoal);">
                        This session will explore how effective board governance contributes to financial stability, risk management, and sustainable banking practices across Africa.
                    </p>
                </div>

                <!-- Zoom Meeting Details -->
                <div style="background-color: var(--bg-offwhite); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h2 style="font-size: 1.5rem; color: var(--primary-navy); margin-bottom: 1rem; font-weight: 700;">Zoom Meeting Details</h2>
                    <div style="display: grid; gap: 1rem;">
                        <div>
                            <strong style="color: var(--text-charcoal);">Meeting ID:</strong>
                            <span style="color: var(--text-light); margin-left: 0.5rem;">885 0243 0789</span>
                        </div>
                        <div>
                            <strong style="color: var(--text-charcoal);">Passcode:</strong>
                            <span style="color: var(--text-light); margin-left: 0.5rem;">822412</span>
                        </div>
                        <div>
                            <strong style="color: var(--text-charcoal);">Join Link:</strong>
                            <a href="https://us06web.zoom.us/j/88502430789?pwd=e3a79VijbjKZTolGnhZDoaN4s7OIug.1" target="_blank" style="color: var(--primary-navy); text-decoration: underline; margin-left: 0.5rem; word-break: break-all;">
                                https://us06web.zoom.us/j/88502430789?pwd=e3a79VijbjKZTolGnhZDoaN4s7OIug.1
                            </a>
                        </div>
                        <div>
                            <strong style="color: var(--text-charcoal);">Join Instructions:</strong>
                            <a href="https://us06web.zoom.us/meetings/88502430789/invitations?signature=jv3kLZCqPxnGY0kOXjKJ-j_yX8d2Rbww5hhLcVJeOWA" target="_blank" style="color: var(--primary-navy); text-decoration: underline; margin-left: 0.5rem; word-break: break-all;">
                                View detailed join instructions
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Call to Action -->
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 2rem;">
                    <a href="register-cgs-ii.php" class="btn" style="background-color: var(--accent-gold); color: var(--primary-navy); padding: 1rem 2rem; font-weight: 600; font-size: 1.1rem; text-decoration: none; border-radius: 4px; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <span>Register for Event</span>
                        <span>→</span>
                    </a>
                    <a href="series-diary.php" class="btn" style="background-color: var(--primary-navy); color: white; padding: 1rem 2rem; font-weight: 600; font-size: 1.1rem; text-decoration: none; border-radius: 4px; border: 2px solid var(--primary-navy); transition: all 0.3s ease;">
                        View All Series Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>