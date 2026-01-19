// Mobile Navigation Toggle
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        hamburger.classList.toggle('active');
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
            hamburger.classList.remove('active');
        });
    });
}

// Login Form Handler (only for simple login forms without backend handlers)
// Network.php and login-user.php have their own handlers, so we skip them
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) {
        // No login form found, skip
        return;
    }
    
    // Check if this form has its own backend handler (network.php uses user-auth.php)
    const formAction = loginForm.getAttribute('action');
    if (formAction) {
        // Skip if form has backend handler - let backend handle submission
        if (formAction.includes('user-auth.php') || formAction.includes('login.php') || formAction.includes('/user-auth')) {
            return; // Form has backend handler, don't interfere
        }
    }
    
    // Only handle forms with email/password IDs (not loginEmail/loginPassword used in network.php)
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    // Only add handler if BOTH elements exist and are not null
    if (emailInput && passwordInput && emailInput !== null && passwordInput !== null) {
        loginForm.addEventListener('submit', function(e) {
            // Double-check elements still exist before accessing value
            if (!emailInput || !passwordInput) {
                console.warn('Login form elements not found, allowing normal submission');
                return true; // Let form submit normally
            }
            
            const emailValue = (emailInput.value || '').trim();
            const passwordValue = passwordInput.value || '';
            
            // Basic validation
            if (!emailValue || !passwordValue) {
                e.preventDefault();
                alert('Please enter both email and password.');
                return false;
            }
            // Otherwise let form submit normally (backend will handle it)
        });
    } else {
        // Elements don't exist (likely network.php with loginEmail/loginPassword)
        // Let form submit normally without JavaScript interference
        console.log('Login form elements (email/password) not found - form will submit normally');
    }
    // If elements don't exist, form will submit normally without JavaScript interference
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href.length > 1) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// Add scroll effect to navbar
let lastScroll = 0;
const navbar = document.querySelector('.navbar');

if (navbar) {
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            navbar.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.15)';
        } else {
            navbar.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';
        }
        
        lastScroll = currentScroll;
    });
}

// Animate numbers on scroll (for stats section)
const animateNumbers = () => {
    const stats = document.querySelectorAll('.stat-number');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const finalNumber = target.textContent;
                const isPlus = finalNumber.includes('+');
                const numberValue = parseInt(finalNumber.replace(/\D/g, ''));
                
                if (!target.classList.contains('animated')) {
                    target.classList.add('animated');
                    animateValue(target, 0, numberValue, 1000, isPlus);
                }
            }
        });
    }, { threshold: 0.5 });
    
    stats.forEach(stat => observer.observe(stat));
};

const animateValue = (element, start, end, duration, hasPlus) => {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const current = Math.floor(progress * (end - start) + start);
        element.textContent = current + (hasPlus ? '+' : '');
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
};

// Initialize number animation when page loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', animateNumbers);
} else {
    animateNumbers();
}

// Newsletter form handler
const newsletterForm = document.getElementById('newsletterForm');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = newsletterForm.querySelector('input[type="email"]').value;
        if (email) {
            alert('Thank you for subscribing! You will receive updates from CGS.');
            newsletterForm.reset();
        }
    });
}

// Search Functionality
const searchBtn = document.getElementById('searchBtn');
let searchModal = document.getElementById('searchModal');
let searchInput = document.getElementById('searchInput');
let searchResults = document.getElementById('searchResults');

// Create search modal if it doesn't exist
if (!searchModal && searchBtn) {
    const modalHTML = `
        <div class="search-modal" id="searchModal">
            <div class="search-modal-content">
                <div class="search-modal-header">
                    <div class="search-input-wrapper">
                        <input type="text" id="searchInput" placeholder="Search pages, content, events..." autocomplete="off">
                    </div>
                    <button class="close-search" id="closeSearch">&times;</button>
                </div>
                <div class="search-results" id="searchResults">
                    <div class="search-loading">Start typing to search...</div>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    searchModal = document.getElementById('searchModal');
    searchInput = document.getElementById('searchInput');
    searchResults = document.getElementById('searchResults');
}

// Search database - Pages and content to search
const searchDatabase = [
    {
        title: 'Home',
        url: 'index.php',
        keywords: ['home', 'corporate governance series', 'cgs', 'africa', 'ghana', 'governance', 'elevating standards', 'premier platform'],
        snippet: 'Corporate Governance Series (CGS) is the premier platform for African stakeholders to debate, refine, and implement world-class governance frameworks.'
    },
    {
        title: 'About CGS',
        url: 'about.php',
        keywords: ['about', 'cgs', 'vision', 'mission', 'csts', 'platform', 'africa', 'governance', 'ethical leadership', 'sustainable growth'],
        snippet: 'A platform shaping ethical leadership, accountability, and sustainable growth across Africa.'
    },
    {
        title: 'Events - Upcoming Events',
        url: 'webinar-diary.php',
        keywords: ['events', 'webinar', 'diary', 'monthly', 'virtual', 'directors', 'directorship', 'digital age', 'ai ethics', 'cybersecurity'],
        snippet: 'Monthly, high-signal virtual sessions built for directors, deal teams, in-house counsel, bankers, regulators, and students.'
    },
    {
        title: 'Events - Series Diary',
        url: 'series-diary.php',
        keywords: ['events', 'series', 'diary', 'flagship', 'sessions', 'stakeholder', 'convenings', 'accra', 'soe', 'state owned entities'],
        snippet: 'The Corporate Governance Series is a structured roadmap of flagship discussions and stakeholder convenings.'
    },
    {
        title: 'Governance Codes in Africa',
        url: 'governance-codes.php',
        keywords: ['resources', 'governance codes', 'africa', 'codes', 'national', 'ghana', 'nigeria', 'kenya', 'download', 'pdf'],
        snippet: 'Search and download national corporate governance codes and key guidance across the continent.'
    },
    {
        title: 'CGS Blog',
        url: 'blog.php',
        keywords: ['blog', 'resources', 'articles', 'insights', 'board effectiveness', 'compliance', 'esg', 'risk', 'digital', 'governance'],
        snippet: 'Executive summaries first. Deep-dive analysis when it matters. Expert insights on board effectiveness, compliance, ESG, risk, and digital governance.'
    },
    {
        title: 'Videos',
        url: 'videos.php',
        keywords: ['media', 'videos', 'recordings', 'events', 'highlights', 'fireside chats', 'soe', 'state owned entities', 'governance'],
        snippet: 'Watch highlights, fireside chats, and full-length event recordings from CGS events.'
    },
    {
        title: 'Pictures',
        url: 'pictures.php',
        keywords: ['media', 'pictures', 'photos', 'gallery', 'events', 'albums', 'networking', 'sessions'],
        snippet: 'Event albums with high-resolution images from CGS sessions and stakeholder convenings.'
    },
    {
        title: 'Training',
        url: 'training.php',
        keywords: ['training', 'capacity building', 'director induction', 'board evaluations', 'compliance', 'workshops', 'certification'],
        snippet: 'Capacity building that upgrades governance from policy to performance.'
    },
    {
        title: 'Network',
        url: 'network.php',
        keywords: ['network', 'membership', 'community', 'directors', 'lawyers', 'bankers', 'regulators', 'scholars', 'governance professionals'],
        snippet: 'Connect with Africa\'s governance elite. A curated network of directors, lawyers, bankers, regulators, scholars, and governance professionals.'
    },
    {
        title: 'Contact',
        url: 'contact.php',
        keywords: ['contact', 'partnerships', 'speaking', 'training requests', 'enquiries', 'accra', 'kumasi', 'email', 'phone'],
        snippet: 'Partnerships, speaking opportunities, training requests, and general enquiries.'
    }
];

// Header search input functionality (IBA-style)
const headerSearchInput = document.getElementById('headerSearchInput');
if (headerSearchInput) {
    headerSearchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            // Open the search modal with the search term
            if (searchBtn) {
                searchBtn.click();
                // Set the search value in the modal
                setTimeout(() => {
                    if (searchInput) {
                        searchInput.value = headerSearchInput.value;
                        searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }, 100);
            }
        }
    });
}

// Open search modal
if (searchBtn) {
    searchBtn.addEventListener('click', () => {
        if (searchModal) {
            searchModal.classList.add('active');
            if (searchInput) searchInput.focus();
        }
    });
}

// Close search modal
const closeSearch = document.getElementById('closeSearch');
if (closeSearch) {
    closeSearch.addEventListener('click', () => {
        if (searchModal) {
            searchModal.classList.remove('active');
            if (searchInput) searchInput.value = '';
            if (searchResults) searchResults.innerHTML = '<div class="search-loading">Start typing to search...</div>';
        }
    });
}

// Close modal on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && searchModal && searchModal.classList.contains('active')) {
        searchModal.classList.remove('active');
        if (searchInput) searchInput.value = '';
        if (searchResults) searchResults.innerHTML = '<div class="search-loading">Start typing to search...</div>';
    }
});

// Close modal when clicking outside
if (searchModal) {
    searchModal.addEventListener('click', (e) => {
        if (e.target === searchModal) {
            searchModal.classList.remove('active');
            if (searchInput) searchInput.value = '';
            if (searchResults) searchResults.innerHTML = '<div class="search-loading">Start typing to search...</div>';
        }
    });
}

// Search function
function performSearch(query) {
    if (!query || query.trim().length < 2) {
        searchResults.innerHTML = '';
        return;
    }

    const searchTerm = query.toLowerCase().trim();
    const results = [];

    // Search through database
    searchDatabase.forEach(item => {
        const titleMatch = item.title.toLowerCase().includes(searchTerm);
        const keywordMatch = item.keywords.some(keyword => keyword.toLowerCase().includes(searchTerm));
        const snippetMatch = item.snippet.toLowerCase().includes(searchTerm);
        
        if (titleMatch || keywordMatch || snippetMatch) {
            let score = 0;
            if (item.title.toLowerCase() === searchTerm) score = 100;
            else if (item.title.toLowerCase().startsWith(searchTerm)) score = 80;
            else if (titleMatch) score = 60;
            else if (keywordMatch) score = 40;
            else score = 20;

            results.push({ ...item, score });
        }
    });

    // Sort by relevance score
    results.sort((a, b) => b.score - a.score);

    displayResults(results, searchTerm);
}

// Display search results
function displayResults(results, searchTerm) {
    if (!searchResults) return;

    if (!searchTerm || searchTerm.trim().length < 2) {
        searchResults.innerHTML = '<div class="search-loading">Start typing to search...</div>';
        return;
    }

    if (results.length === 0) {
        searchResults.innerHTML = `
            <div class="search-no-results">
                <p>No results found for "<strong>${escapeHtml(searchTerm)}</strong>"</p>
                <p style="margin-top: 1rem; font-size: 0.9rem;">Try different keywords or check your spelling.</p>
            </div>
        `;
        return;
    }

    const resultsHTML = results.map(item => {
        const highlightedTitle = highlightText(item.title, searchTerm);
        const highlightedSnippet = highlightText(item.snippet, searchTerm);
        
        return `
            <div class="search-result-item" onclick="window.location.href='${item.url}'">
                <div class="search-result-title">${highlightedTitle}</div>
                <div class="search-result-url">${item.url}</div>
                <div class="search-result-snippet">${highlightedSnippet}</div>
            </div>
        `;
    }).join('');

    searchResults.innerHTML = resultsHTML;
}

// Highlight search term in text
function highlightText(text, term) {
    const escapedText = escapeHtml(text);
    const escapedTerm = escapeHtml(term);
    const regex = new RegExp(`(${escapedTerm})`, 'gi');
    return escapedText.replace(regex, '<span class="highlight">$1</span>');
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Search input handler
if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value;
        
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300); // Debounce search
    });

    // Search on Enter key
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch(e.target.value);
        }
    });
}
