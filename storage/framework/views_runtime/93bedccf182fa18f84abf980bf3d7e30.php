
<style>
    .lk-wrapper { margin-bottom: 3rem; }
    
    .lk-section {
      position: relative;
      border: 0.5px solid var(--color-border-tertiary);
      border-radius: var(--border-radius-lg);
      overflow: hidden;
      background: url('/assets/ACTION.png') no-repeat center center / cover;
      min-height: 360px;
      display: flex;
      align-items: stretch;
      padding: 3rem 0rem;
    }
    
    .lk-section::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(255,255,255,0.08);
      z-index: 0;
    }
    
    .lk-content {
      position: relative;
      z-index: 1;
      padding: 2rem 1.5rem;
      width: calc(100% - 220px);
      box-sizing: border-box;
      color: #fff;
    }
    
    .lk-image {
      position: absolute;
      right: 0;
      top: 0;
      bottom: 0;
      width: 200px;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      /* background: rgba(255,255,255,0.07); */
      /* border-left: 0.5px solid rgba(255,255,255,0.15); */
      padding: 1rem;
      box-sizing: border-box;
    }
    @media only screen and (min-width:1000px){
        .lk-image {
            width: 260px !important
        }
    }
    
    .lk-image img {
      max-width: 100%;
      max-height: 260px;
      object-fit: contain;
      
      display: block;
    }
    
    .lk-title {
      font-size: 2rem;
      font-weight: 700;
      color: #fff;
      text-transform: capitalize;
      line-height: 1.35;
      margin: 0 0 0.75rem;
    }
    
    .lk-desc {
      font-size: 13px;
      color: rgba(255,255,255,0.82);
      line-height: 1.7;
      margin: 0 0 1.25rem;
    }
    
    .lk-features {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 1.5rem;
    }
    
    .lk-feat {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .lk-feat-icon {
      width: 34px;
      height: 34px;
      border-radius: var(--border-radius-md);
      background: #fff;
      color: #000;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    
    .lk-feat-icon svg { width: 16px; height: 16px; }
    
    .lk-feat-title {
      font-size: 13px;
      font-weight: 500;
      color: #fff;
      margin: 0;
      line-height: 1.3;
    }
    
    .lk-feat-sub {
      font-size: 12px;
      color: rgba(255,255,255,0.7);
      margin: 0;
    }
    
    .lk-btns {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .lk-btn {
      display: inline-flex;
      align-items: center;
      justify-content: space-between;
      border-radius: 50px;
      padding: 8px 8px 8px 18px;
      height: 50px;
      text-decoration: none;
      cursor: pointer;
      border: none;
      outline: none;
      min-width: 140px;
      flex: 1 1 140px;
      max-width: 280px;
      box-sizing: border-box;
      transition: filter 0.15s, transform 0.1s;
    }
    
    .lk-btn:hover { filter: brightness(1.1); transform: scale(1.02); }
    .lk-btn:active { transform: scale(0.98); }
    
    .lk-btn-text {
      font-weight: 900;
      font-size: 14px;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      line-height: 1.2;
      color: #fff;
      flex: 1;
      padding: 0 8px;
      text-align: center;
    }
    
    .lk-btn-circle {
      width: 30px;
      height: 30px;
      min-width: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    
    .lk-btn-circle svg { width: 16px; height: 16px; fill: none; stroke: #fff; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; }
    
    .lk-red { background: #cc2222; }
    .lk-red .lk-btn-circle { background: #ff2020; }
    .lk-blue { background: #1a7ca8; }
    .lk-blue .lk-btn-circle { background: #1fa8db; }
    .lk-gold { background: #c9a000; }
    .lk-gold .lk-btn-circle { background: #f0c500; }
    
    @media (max-width: 640px) {
      .lk-content { width: 100%; padding: 1.5rem 1.25rem 1.5rem; }
      .lk-image {
        position: static;
        width: 100%;
        border-left: none;
        border-top: 0.5px solid rgba(255,255,255,0.15);
        padding: 1rem;
        min-height: 160px;
      }
      .lk-section { flex-direction: column; min-height: auto; }
      .lk-btn { max-width: 100%; flex: 1 1 100%; height: 48px; }
      .lk-btn-text { font-size: 11px; }
      .lk-title { font-size: 19px; }
    }
    
    @media (min-width: 641px) and (max-width: 900px) {
      .lk-content { width: calc(100% - 170px); }
      .lk-section { flex-direction: column; min-height: auto; }
      .lk-image { width: 160px; }
      .lk-btn { max-width: 100%; flex: 1 1 100%; height: 48px; }
      .lk-image img { max-height: 200px; }
    }
    main{
        padding: 0rem !important
    }
    </style>
    
    <div class="lk-wrapper">
      <div class="lk-section">
    
        <div class="lk-content">
          <h2 class="lk-title">Your all-in-one portal for<br>gamified STEAM education</h2>
    
          <p class="lk-desc">
           
The STEAM XR Sports Events and Tournament Management Portal was built from the ground up for large organizations such as schools, Parks and Recreation departments, and community programs. It enables staff to manage large groups, assign teams, and track participation across multiple activity types. Players earn points through Brain Games based on STEM projects, Playground Activities that blend physical movement with problem solving, and organized e-Gaming and Esports tournaments.
<br>
It solves a major challenge for large groups: coordinating hundreds of participants across different skill levels, schedules, and activity formats while keeping engagement, learning outcomes, and competition fair, measurable, and fun.

          </p>
    
          <div class="lk-features">
            <div class="lk-feat">
              <div class="lk-feat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </div>
              <div>
                <p class="lk-feat-title">15,000+ participants &amp; families</p>
                <p class="lk-feat-sub">Across the DMV area since 2019</p>
              </div>
            </div>
    
            <div class="lk-feat">
              <div class="lk-feat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              </div>
              <div>
                <p class="lk-feat-title">Free STEAM XR Sports League for Schools &amp; Communities</p>
                <p class="lk-feat-sub">Completely free for non-profits, schools, and parks and recreation departments!</p>
              </div>
            </div>
          </div>
    
          <div class="lk-btns">
            <a href="https://steamyourdreams.org" class="lk-btn lk-red">
              <span class="lk-btn-text">VISIT<br>STEAMYOURDREAMS.ORG</span>
              <span class="lk-btn-circle"><svg viewBox="0 0 24 24"><polyline points="7 18 13 12 7 6"/><polyline points="13 18 19 12 13 6"/></svg></span>
            </a>
            <a href="https://steamxrsports.org/" class="lk-btn lk-blue">
              <span class="lk-btn-text">STEAM XR<br>SPORTS LEAGUE</span>
              <span class="lk-btn-circle"><svg viewBox="0 0 24 24"><polyline points="7 18 13 12 7 6"/><polyline points="13 18 19 12 13 6"/></svg></span>
            </a>
            <a href="https://steamiq.net" class="lk-btn lk-gold">
              <span class="lk-btn-text">STEAMIQ.NET</span>
              <span class="lk-btn-circle"><svg viewBox="0 0 24 24"><polyline points="7 18 13 12 7 6"/><polyline points="13 18 19 12 13 6"/></svg></span>
            </a>
          </div>
        </div>
    
        <div class="lk-image">
          <img src="/assets/second_logo.png" alt="STEAM" onerror="this.style.display='none'">
        </div>
    
      </div>
    </div>
    <?php /**PATH C:\Users\PC\Downloads\steamiq (8)\resources\views/dashboard/welcome.blade.php ENDPATH**/ ?>