
    <style>
.event-result-wrapper {
    margin-top: 10px;
}

/* Toggle header */
.event-toggle-header {
    display: flex;
    justify-content: end;
    align-items: center;
    margin: 10px 0;
    padding: 6px 10px;
 
}

/* Title */
.toggle-title {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
}

/* Button */
.toggle-btn {
    border-radius: 4px;
    border: 1px solid #ccc;
    background: rgba(0,0,0,0.03);

    cursor: pointer;
    padding: 4px;
    transition: 0.2s ease;
}

.toggle-btn:hover {
    transform: scale(1.1);
}

/* Icon animation */
.toggle-icon {
    width: 16px;
    height: 16px;
    transition: transform 0.25s ease;
    color: #000;
}

/* Smooth collapse */
.collapse-box {
    overflow: hidden;
    transition: all 0.25s ease;
    display: none;
}

.collapse-box.open {
    animation: fadeSlide 0.25s ease forwards;
}

@keyframes fadeSlide {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
/* ── Event card status dropdown ── */
.ec-status-wrap {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.ec-status-btn {
    opacity: .55;
    transition: opacity .15s;
}

.ec-status-btn:hover {
    opacity: 1;
}

.ec-status-drop {
    position: absolute;
    right: 0;
    top: calc(100% + 6px);
    z-index: 999;
    background: #111827;
    border: 1px solid #1e2d45;
    border-radius: 10px;
    padding: 6px;
    min-width: 140px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, .55);
    animation: ec-fadein .15s ease;
}

@keyframes ec-fadein {
    from {
        opacity: 0;
        transform: translateY(-4px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.ec-status-opt {
    display: flex;
    align-items: center;
    gap: 7px;
    width: 100%;
    padding: 7px 10px;
    border: none;
    background: transparent;
    border-radius: 7px;
    font-size: 1rem;
    font-weight: 600;
    color: #94a3b8;
    cursor: pointer;
    transition: background .12s, color .12s;
}

.ec-status-opt:hover {
    background: rgba(99, 102, 241, .12);
    color: #e2e8f0;
}

.ec-status-opt.active {
    color: #f1f5f9;
    background: rgba(99, 102, 241, .18);
}

.ec-status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.ec-dot-draft {
    background: #f59e0b;
    box-shadow: 0 0 5px rgba(245, 158, 11, .5);
}

.ec-dot-live {
    background: #10b981;
    box-shadow: 0 0 5px rgba(16, 185, 129, .5);
}

.ec-dot-closed {
    background: #ef4444;
    box-shadow: 0 0 5px rgba(239, 68, 68, .5);
}


.events_card-header .ec-status-wrap {
    margin-right: 6px;
    display: inline-flex;
    align-items: center;
}

    </style>
<?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/events/style.blade.php ENDPATH**/ ?>