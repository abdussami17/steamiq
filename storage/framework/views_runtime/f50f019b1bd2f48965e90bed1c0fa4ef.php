

<div id="orientation-hint-overlay"
     class="orientation-hint-overlay"
     role="dialog"
     aria-modal="true"
     aria-labelledby="hint-title"
     aria-describedby="hint-desc"
     style="display:none;">

    <div class="orientation-hint-modal">

        
        <div class="hint-animation" aria-hidden="true">

            
            <div class="hint-phone portrait" id="hint-phone-portrait">
                <div class="phone-screen"></div>
                <div class="phone-btn"></div>
            </div>

            
            <div class="hint-arrow" id="hint-arrow">
                <svg width="20" height="16" viewBox="0 0 20 16" fill="none">
                    <path d="M1 8H19M19 8L12 1M19 8L12 15"
                          stroke="currentColor" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            
            <div class="hint-phone landscape" id="hint-phone-landscape">
                <div class="phone-screen"></div>
                <div class="phone-btn"></div>
            </div>

            
            <div class="hint-pinch" id="hint-pinch">
                <svg width="44" height="44" viewBox="0 0 44 44" fill="none">
                    <circle cx="22" cy="22" r="14"
                            stroke="currentColor" stroke-width="1"
                            stroke-dasharray="4 3" opacity="0.25"/>
                    <line x1="22" y1="7"  x2="22" y2="14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="22" y1="37" x2="22" y2="30" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="7"  y1="22" x2="14" y2="22" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="37" y1="22" x2="30" y2="22" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="22" cy="22" r="4.5" fill="currentColor"/>
                </svg>
            </div>
        </div>

        
        <h3 id="hint-title" class="hint-title">Better on horizontal</h3>
        <p id="hint-desc" class="hint-desc">
            Rotate your phone sideways for a wider table view.
            You can also pinch to zoom in on any section.
        </p>

        
        <label class="hint-dismiss-label" for="hint-dont-show">
            <input type="checkbox" id="hint-dont-show" class="hint-checkbox">
            <span class="hint-checkbox-custom" aria-hidden="true">
                <svg width="11" height="9" viewBox="0 0 11 9" fill="none">
                    <path d="M1 4L4.5 7.5L10 1.5"
                          stroke="white" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="hint-dismiss-text">Don't show this again</span>
        </label>

        
        <button type="button" id="hint-got-it" class="hint-btn-primary">
            Got it
        </button>

    </div>
</div>


<button type="button"
        id="orientation-hint-trigger"
        class="orientation-hint-trigger"
        title="View table tips"
        aria-label="Show table viewing tips"
        style="display:none;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
        <path d="M12 8v1M12 11v5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
</button><?php /**PATH C:\Users\PC\Downloads\steamiq (8)\resources\views/components/mobile-hint.blade.php ENDPATH**/ ?>