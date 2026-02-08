// Global variables
let confirmCallback = null;

// Navigation Functions
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.page-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Show selected section
    const targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Update navigation active state
    document.querySelectorAll('nav a').forEach(link => {
        link.classList.remove('active');
    });
    if (event && event.target) {
        event.target.classList.add('active');
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}



// Tab Switching
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    const targetTab = document.getElementById(tabName + '-tab');
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    if (event && event.target) {
        event.target.classList.add('active');
    }
}









function updateCategoryOptions() {
    const pillarSelect = document.getElementById('scorePillar');
    const categoryGroup = document.getElementById('categoryGroup');
    const categoryLabel = document.getElementById('categoryLabel');
    const categorySelect = document.getElementById('scoreCategory');
    
    if (!pillarSelect || !categoryGroup || !categoryLabel || !categorySelect) return;
    
    const pillar = pillarSelect.value;
    
    if (!pillar) {
        categoryGroup.style.display = 'none';
        return;
    }
    
    categoryGroup.style.display = 'block';
    categorySelect.innerHTML = '';
    
    let options = [];
    
    switch(pillar) {
        case 'brain':
            categoryLabel.textContent = 'STEAM Category *';
            options = [
                { value: '', text: '-- Select Category --' },
                { value: 'science', text: 'Science' },
                { value: 'technology', text: 'Technology' },
                { value: 'engineering', text: 'Engineering' },
                { value: 'art', text: 'Art' },
                { value: 'math', text: 'Math' }
            ];
            break;
        case 'playground':
            categoryLabel.textContent = 'Game Name *';
            options = [
                { value: '', text: '-- Select Game --' },
                { value: 'soccer', text: 'Soccer' },
                { value: 'basketball', text: 'Basketball' },
                { value: 'volleyball', text: 'Volleyball' },
                { value: 'beachball', text: 'Beach Balling' }
            ];
            break;
        case 'egaming':
            categoryLabel.textContent = 'Game Title *';
            options = [
                { value: '', text: '-- Select Game --' },
                { value: 'aquaball', text: 'AquaBall Clash' },
                { value: 'league', text: 'League Rotation' },
                { value: 'beachball', text: 'Beach Balling' }
            ];
            break;
        case 'esports':
            categoryLabel.textContent = 'Match/Round *';
            options = [
                { value: '', text: '-- Select Match --' },
                { value: 'match1', text: 'Match #1' },
                { value: 'match2', text: 'Match #2' },
                { value: 'match3', text: 'Match #3' }
            ];
            break;
    }
    
    options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.text;
        categorySelect.appendChild(option);
    });
}






// Card hover effect with mouse tracking
function setupCardHoverEffects() {
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            this.style.setProperty('--mouse-x', `${x}%`);
            this.style.setProperty('--mouse-y', `${y}%`);
        });
    });
}

// Animate stats on load
function animateStats() {
    document.querySelectorAll('.stat-value').forEach((stat, index) => {
        stat.style.opacity = '0';
        stat.style.transform = 'translateY(20px)';
        setTimeout(() => {
            stat.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            stat.style.opacity = '1';
            stat.style.transform = 'translateY(0)';
        }, index * 100);
    });
}



// Initialize all functionality
function init() {
    
    
    setupCardHoverEffects();
   
}

// Run initialization when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

// Run animation when window loads
window.addEventListener('load', animateStats);
