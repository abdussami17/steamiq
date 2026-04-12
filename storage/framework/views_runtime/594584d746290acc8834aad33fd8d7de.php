<style>
    /* === Steam Event Management System — Profile Editor === */
.sems-profile-wrapper { padding: 1.5rem 0; }
.sems-profile-card { background: var(--bs-body-bg, #fff); border: 0.5px solid rgba(0,0,0,0.1); border-radius: 14px; overflow: hidden; max-width: 100%; }

/* Hero banner */
.sems-profile-hero-banner { background: #1a1a2e; padding: 2rem 2rem 2.5rem; position: relative; }
.sems-profile-hero-banner::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, #7F77DD 0%, #1D9E75 50%, #D85A30 100%); }
.sems-profile-avatar-ring { width: 80px; height: 80px; border-radius: 50%; background: #000; border: 2px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 500; color: #fff; flex-shrink: 0; }
.sems-profile-hero-name { font-size: 18px; font-weight: 500; color: #E8E8F0; margin: 0 0 2px; }
.sems-profile-hero-role { font-size: 13px; color: #9090B0; margin: 0; }
.sems-profile-badge-pill { display: inline-flex; align-items: center; gap: 6px; background: rgba(127,119,221,0.15); border: 0.5px solid rgba(127,119,221,0.4); border-radius: 20px; padding: 4px 12px; font-size: 12px; color: #fff; }
.sems-profile-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #5DCAA5; display: inline-block; }
.sems-profile-stat-tile { background: rgba(255,255,255,0.06); border: 0.5px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 14px; }
.sems-profile-stat-label { font-size: 11px; color: #9090B0; margin: 0 0 2px; }
.sems-profile-stat-value { font-size: 16px; font-weight: 500; color: #E8E8F0; margin: 0; }

/* Body sections */
.sems-profile-body-section { padding: 1.5rem 2rem; }
.sems-profile-section-header { display: flex; align-items: center; gap: 8px; margin-bottom: 1.25rem; }
.sems-profile-section-icon { width: 28px; height: 28px; border-radius: 8px; background: #f5f5f5; border: 0.5px solid rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #666; }
.sems-profile-section-title { font-size: 11px; font-weight: 600; color: #999; text-transform: uppercase; letter-spacing: 0.07em; }
.sems-profile-optional-tag { font-size: 11px; color: #bbb; }

/* Inputs */
.sems-profile-label { font-size: 12px; font-weight: 500; color: #555; margin-bottom: 6px; display: block; }
.sems-profile-input { width: 100%; padding: 9px 12px; font-size: 14px; border: 0.5px solid rgba(0,0,0,0.15); border-radius: 8px; background: #fafafa; color: #1a1a1a; transition: border-color 0.15s, box-shadow 0.15s; }
.sems-profile-input:focus { outline: none; border-color: #7F77DD; box-shadow: 0 0 0 3px rgba(127,119,221,0.12); background: #fff; }
.sems-profile-input-wrap { position: relative; display: flex; align-items: center; }
.sems-profile-input-wrap .sems-profile-input { padding-right: 40px; }
.sems-profile-at-prefix { position: absolute; left: 10px; font-size: 13px; color: #aaa; pointer-events: none; }
.sems-profile-eye-btn { position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #aaa; display: flex; align-items: center; padding: 0; }
.sems-profile-eye-btn:hover { color: #555; }

/* Strength meter */
.sems-profile-strength-track { height: 3px; background: #eee; border-radius: 2px; margin-top: 6px; overflow: hidden; }
.sems-profile-strength-fill { height: 100%; width: 0; border-radius: 2px; transition: width 0.25s, background 0.25s; }
.sems-profile-strength-label { font-size: 11px; color: #aaa; margin-top: 4px; margin-bottom: 0; }

/* Divider & actions */
.sems-profile-divider { border: none; border-top: 0.5px solid rgba(0,0,0,0.08); margin: 0; }
.sems-profile-actions-row { padding: 1.25rem 2rem 1.75rem; display: flex; align-items: center; gap: 12px; }
.sems-profile-submit-btn { flex: 1; padding: 10px 20px; background: #000; border: none; border-radius: 8px; color: #fff; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.15s, transform 0.1s; }
.sems-profile-submit-btn:hover { background: #000; opacity: .7;}
.sems-profile-submit-btn:active { transform: scale(0.98); }
.sems-profile-cancel-btn { padding: 10px 18px; background: #f5f5f5; border: 0.5px solid rgba(0,0,0,0.1); border-radius: 8px; color: #666; font-size: 14px; cursor: pointer; transition: background 0.15s; }
.sems-profile-cancel-btn:hover { background: #ececec; }

/* Messages */
.sems-profile-msg-success { display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: #EAF3DE; border: 0.5px solid #97C459; border-radius: 8px; font-size: 13px; color: #3B6D11; margin-bottom: 12px; }
.sems-profile-msg-danger { display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: #FCEBEB; border: 0.5px solid #F09595; border-radius: 8px; font-size: 13px; color: #A32D2D; margin-bottom: 12px; }

</style>


<script>
    document.addEventListener("DOMContentLoaded", function () {
    
      const form = document.getElementById('profile-update-form');
      const messageBox = document.getElementById('profile-update-message');
      const nameInput = document.getElementById('semsNameInput');
      const heroName = document.getElementById('semsProfileHeroName');
      const heroInitials = document.getElementById('semsProfileAvatarInitials');
      const strengthFill = document.getElementById('semsStrengthFill');
      const strengthLabel = document.getElementById('semsStrengthLabel');
      const confirmLabel = document.getElementById('semsConfirmMatchLabel');
    
      // Live avatar initials update
      nameInput.addEventListener('input', function () {
        const parts = this.value.trim().split(' ').filter(Boolean);
        const initials = parts.map(w => w[0].toUpperCase()).slice(0, 2).join('');
        if (initials) { heroInitials.textContent = initials; heroName.textContent = this.value.trim(); }
      });
    
      // Toggle password visibility
      document.querySelectorAll('.toggle-pass').forEach(btn => {
        btn.addEventListener('click', function () {
          const target = document.getElementById(this.dataset.target);
          target.type = target.type === 'password' ? 'text' : 'password';
        });
      });
    
      // Password strength meter
      document.getElementById('profile-password-input').addEventListener('input', function () {
        const pw = this.value;
        if (!pw) { strengthFill.style.width = '0'; strengthLabel.textContent = ''; return; }
        let score = 0;
        if (pw.length >= 6) score++;
        if (pw.length >= 10) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        const pct = Math.round((score / 5) * 100);
        const colors = ['#E24B4A','#E24B4A','#EF9F27','#1D9E75','#1D9E75'];
        const labels = ['','Very weak','Weak','Fair','Strong','Very strong'];
        strengthFill.style.width = pct + '%';
        strengthFill.style.background = colors[Math.min(score, 4)];
        strengthLabel.textContent = labels[score] || '';
        checkMatch();
      });
    
      // Confirm match indicator
      document.getElementById('profile-password-confirm-input').addEventListener('input', checkMatch);
      function checkMatch() {
        const np = document.getElementById('profile-password-input').value;
        const cp = document.getElementById('profile-password-confirm-input').value;
        if (!cp) { confirmLabel.textContent = ''; return; }
        confirmLabel.textContent = np === cp ? 'Passwords match' : 'Does not match';
        confirmLabel.style.color = np === cp ? '#3B6D11' : '#A32D2D';
      }
    
      function showMsg(type, text) {
        messageBox.innerHTML = `<div class="sems-profile-msg-${type}">${text}</div>`;
        setTimeout(() => { messageBox.innerHTML = ''; }, 4000);
      }
    
      // Reset button
      document.getElementById('semsResetBtn').addEventListener('click', function () {
        form.reset();
        strengthFill.style.width = '0';
        strengthLabel.textContent = '';
        confirmLabel.textContent = '';
        messageBox.innerHTML = '';
      });
    
      // Form submit
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        const name = nameInput.value.trim();
        const username = document.getElementById('semsUsernameInput').value.trim();
        const cur = document.getElementById('profile-current-password').value;
        const np = document.getElementById('profile-password-input').value;
        const cp = document.getElementById('profile-password-confirm-input').value;
    
        if (!name || !username) { showMsg('danger', 'Name and username are required.'); return; }
        if (np) {
          if (!cur) { showMsg('danger', 'Enter your current password to set a new one.'); return; }
          if (np.length < 6) { showMsg('danger', 'New password must be at least 6 characters.'); return; }
          if (np !== cp) { showMsg('danger', 'Passwords do not match.'); return; }
        }
    
        const submitBtn = form.querySelector('.sems-profile-submit-btn');
        submitBtn.textContent = 'Saving…';
        submitBtn.disabled = true;
    
        const formData = new FormData(form);
    
        fetch("<?php echo e(route('profile.update')); ?>", {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          showMsg('success', data.message || 'Profile updated successfully.');
          document.getElementById('profile-current-password').value = '';
          document.getElementById('profile-password-input').value = '';
          document.getElementById('profile-password-confirm-input').value = '';
          strengthFill.style.width = '0';
          strengthLabel.textContent = '';
          confirmLabel.textContent = '';
        })
        .catch(() => { showMsg('danger', 'Server error. Please try again.'); })
        .finally(() => { submitBtn.textContent = 'Save changes'; submitBtn.disabled = false; });
      });
    
    });
    </script><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/settings/scripts/profile-script.blade.php ENDPATH**/ ?>