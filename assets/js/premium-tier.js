// ================================================
// PREMIUM TIER JAVASCRIPT — Full Backend Integration
// ================================================

const PremiumBuilder = {
  data: {
    partner1Name: '', partner2Name: '', anniversaryDate: '',
    websiteTitle: '', welcomeMessage: '',
    template: 'romantic',
    primaryColor: '#FF1744', secondaryColor: '#FFB6C1',
    accentColor: '#FFD700', bgColor: '#FFFFFF',
    fontPairing: 'romantic-elegant',
    storyEnabled: true, storyContent: '',
    galleryEnabled: true, galleryData: [], galleryType: 'images',
    timelineEnabled: true, timelineData: [],
    musicType: 'none', musicFile: null, musicSpotifyUrl: '',
    customUrl: '', passwordProtected: false, sitePassword: ''
  },

  previewTimer: null,
  urlCheckTimer: null,
  autoSaveTimer: null,

  // ── Initialisation ──────────────────────────────
  async init() {
    await this.loadFromServer();
    this.setupTabs();
    this.setupBasicListeners();
    this.setupDesignListeners();
    this.setupContentListeners();
    this.setupSettingsListeners();
    this.setupActions();
    this.initSummernote();
    this.updatePreview();
    this.updateProgress();
    this.startAutoSave();
  },

  // ── Load from Server ────────────────────────────
  async loadFromServer() {
    try {
      const res = await fetch('../api/premium/load.php');
      const json = await res.json();
      if (json.success && json.data) {
        const d = json.data;
        this.data.partner1Name = d.partner1_name || '';
        this.data.partner2Name = d.partner2_name || '';
        this.data.anniversaryDate = d.anniversary_date || '';
        this.data.websiteTitle = d.website_title || '';
        this.data.welcomeMessage = d.welcome_message || '';
        this.data.template = d.template || 'romantic';
        this.data.primaryColor = d.primary_color || '#FF1744';
        this.data.secondaryColor = d.secondary_color || '#FFB6C1';
        this.data.accentColor = d.accent_color || '#FFD700';
        this.data.bgColor = d.bg_color || '#FFFFFF';
        this.data.fontPairing = d.font_pairing || 'romantic-elegant';
        this.data.storyEnabled = !!parseInt(d.story_enabled);
        this.data.storyContent = d.story_content || '';
        this.data.galleryEnabled = !!parseInt(d.gallery_enabled);
        this.data.galleryData = d.gallery_data || [];
        this.data.timelineEnabled = !!parseInt(d.timeline_enabled);
        this.data.timelineData = d.timeline_data || [];
        this.data.musicType = d.music_type || 'none';
        this.data.musicFile = d.music_file || null;
        this.data.musicSpotifyUrl = d.music_spotify_url || '';
        this.data.customUrl = d.custom_url || '';
        this.data.passwordProtected = !!parseInt(d.password_protected);
        this.data.sitePassword = d.site_password || '';
        this.populateForm();
      }
    } catch (e) { console.error('Load error:', e); }
  },

  populateForm() {
    const d = this.data;
    this.setVal('partner1Name', d.partner1Name);
    this.setVal('partner2Name', d.partner2Name);
    this.setVal('anniversaryDate', d.anniversaryDate);
    this.setVal('websiteTitle', d.websiteTitle);
    this.setVal('welcomeMessage', d.welcomeMessage);
    this.updateCharCounter();
    this.setVal('customUrl', d.customUrl);

    // Template
    document.querySelectorAll('.template-option').forEach(o => {
      o.classList.toggle('active', o.dataset.template === d.template);
    });
    // Colors
    this.setVal('primaryColor', d.primaryColor);
    this.setVal('secondaryColor', d.secondaryColor);
    this.setVal('accentColor', d.accentColor);
    this.setVal('bgColor', d.bgColor);
    // Font
    document.querySelectorAll('.font-option').forEach(o => {
      o.classList.toggle('active', o.dataset.font === d.fontPairing);
    });
    // Toggles
    this.setChecked('storyToggle', d.storyEnabled);
    this.setChecked('galleryToggle', d.galleryEnabled);
    this.setChecked('timelineToggle', d.timelineEnabled);
    this.setChecked('passwordProtected', d.passwordProtected);
    if (d.passwordProtected) {
      document.getElementById('passwordInput').style.display = 'block';
    }
    // Music type
    document.querySelectorAll('.music-type-btn').forEach(b => {
      b.classList.toggle('active', b.dataset.music === d.musicType);
    });
    this.showMusicPanel(d.musicType);
    if (d.musicSpotifyUrl) this.setVal('spotifyUrl', d.musicSpotifyUrl);
    if (d.musicFile) {
      document.getElementById('musicFileInfo').innerHTML =
        `<div class="music-file-info"><i class="fas fa-music"></i> <span>Audio uploaded</span>
         <button class="btn-icon danger" onclick="PremiumBuilder.removeMusic()"><i class="fas fa-trash"></i></button></div>`;
    }

    // Gallery
    if (d.galleryData.length > 0 && d.galleryData[0]?.type === 'video') {
      this.data.galleryType = 'video';
      document.querySelector('[data-type="video"]')?.click();
    }
    this.renderGallery();
    this.renderTimeline();
  },

  setVal(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val || '';
  },
  setChecked(id, checked) {
    const el = document.getElementById(id);
    if (el) el.checked = checked;
  },

  // ── Tabs ────────────────────────────────────────
  setupTabs() {
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.addEventListener('click', () => {
        const tab = btn.dataset.tab;
        document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(tab + 'Tab').classList.add('active');
      });
    });
  },

  // ── Basic Info ──────────────────────────────────
  setupBasicListeners() {
    ['partner1Name', 'partner2Name', 'websiteTitle', 'anniversaryDate'].forEach(id => {
      document.getElementById(id)?.addEventListener('input', e => {
        this.data[id] = e.target.value;
        this.schedulePreview();
        this.updateProgress();
      });
    });

    const welcome = document.getElementById('welcomeMessage');
    if (welcome) {
      welcome.addEventListener('input', e => {
        this.data.welcomeMessage = e.target.value;
        this.updateCharCounter();
        this.schedulePreview();
        this.updateProgress();
      });
    }
  },

  updateCharCounter() {
    const counter = document.getElementById('welcomeCounter');
    if (!counter) return;
    const len = this.data.welcomeMessage.length;
    counter.textContent = `${len} / 250`;
    counter.classList.toggle('warning', len >= 230);
  },

  // ── Design ──────────────────────────────────────
  setupDesignListeners() {
    // Templates
    document.querySelectorAll('.template-option').forEach(opt => {
      opt.addEventListener('click', () => {
        document.querySelectorAll('.template-option').forEach(o => o.classList.remove('active'));
        opt.classList.add('active');
        this.data.template = opt.dataset.template;
        this.schedulePreview();
      });
    });

    // Color presets
    const presets = {
      'romantic-red': { primaryColor: '#FF1744', secondaryColor: '#FFB6C1', accentColor: '#FFD700', bgColor: '#FFFFFF' },
      'elegant-purple': { primaryColor: '#8B4789', secondaryColor: '#D4A5D4', accentColor: '#FFD700', bgColor: '#F8F4F9' },
      'soft-pink': { primaryColor: '#FFB6C1', secondaryColor: '#FFC0CB', accentColor: '#FF69B4', bgColor: '#FFF0F5' },
      'midnight-rose': { primaryColor: '#1a1a2e', secondaryColor: '#e94560', accentColor: '#FFD700', bgColor: '#0f3460' }
    };

    document.querySelectorAll('.color-preset').forEach(p => {
      p.addEventListener('click', () => {
        document.querySelectorAll('.color-preset').forEach(pp => pp.classList.remove('active'));
        p.classList.add('active');
        const values = presets[p.dataset.preset];
        if (values) {
          Object.assign(this.data, values);
          this.setVal('primaryColor', values.primaryColor);
          this.setVal('secondaryColor', values.secondaryColor);
          this.setVal('accentColor', values.accentColor);
          this.setVal('bgColor', values.bgColor);
          this.schedulePreview();
        }
      });
    });

    // Individual color pickers
    ['primaryColor', 'secondaryColor', 'accentColor', 'bgColor'].forEach(id => {
      document.getElementById(id)?.addEventListener('input', e => {
        this.data[id] = e.target.value;
        this.schedulePreview();
      });
    });

    // Fonts
    document.querySelectorAll('.font-option').forEach(opt => {
      opt.addEventListener('click', () => {
        document.querySelectorAll('.font-option').forEach(o => o.classList.remove('active'));
        opt.classList.add('active');
        this.data.fontPairing = opt.dataset.font;
        this.schedulePreview();
      });
    });
  },

  // ── Content (Story, Gallery, Timeline, Music) ──
  setupContentListeners() {
    // Section toggles
    document.querySelectorAll('.section-toggle').forEach(toggle => {
      toggle.addEventListener('change', e => {
        const section = e.target.dataset.section;
        this.data[section + 'Enabled'] = e.target.checked;
        this.schedulePreview();
      });
    });

    // Gallery type switcher
    document.querySelectorAll('.gallery-type-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.gallery-type-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.data.galleryType = btn.dataset.type;
        document.getElementById('galleryImagesPanel').style.display = btn.dataset.type === 'images' ? 'block' : 'none';
        document.getElementById('galleryVideoPanel').style.display = btn.dataset.type === 'video' ? 'block' : 'none';
      });
    });

    // Gallery image upload button
    document.getElementById('galleryUploadBtn')?.addEventListener('click', () => {
      document.getElementById('galleryImageUpload')?.click();
    });
    document.getElementById('galleryImageUpload')?.addEventListener('change', e => this.handleGalleryImageUpload(e));

    // Gallery video upload button
    document.getElementById('videoUploadBtn')?.addEventListener('click', () => {
      document.getElementById('galleryVideoUpload')?.click();
    });
    document.getElementById('galleryVideoUpload')?.addEventListener('change', e => this.handleGalleryVideoUpload(e));

    // Timeline
    document.getElementById('addTimelineEvent')?.addEventListener('click', () => this.addTimelineEvent());

    // Music type
    document.querySelectorAll('.music-type-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.music-type-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.data.musicType = btn.dataset.music;
        this.showMusicPanel(btn.dataset.music);
        this.schedulePreview();
      });
    });

    // Music upload
    document.getElementById('musicUploadBtn')?.addEventListener('click', () => {
      document.getElementById('musicFileUpload')?.click();
    });
    document.getElementById('musicFileUpload')?.addEventListener('change', e => this.handleMusicUpload(e));

    // Spotify URL
    document.getElementById('spotifyUrl')?.addEventListener('input', e => {
      this.data.musicSpotifyUrl = e.target.value;
      this.schedulePreview();
    });
  },

  showMusicPanel(type) {
    document.getElementById('musicUploadPanel')?.classList.toggle('active', type === 'upload');
    document.getElementById('musicSpotifyPanel')?.classList.toggle('active', type === 'spotify');
  },

  // ── Summernote ──────────────────────────────────
  initSummernote() {
    if (typeof $ === 'undefined' || typeof $.fn.summernote === 'undefined') {
      console.warn('Summernote not loaded');
      // Fallback to regular textarea
      const ta = document.getElementById('storyContent');
      if (ta) {
        ta.value = this.data.storyContent;
        ta.addEventListener('input', e => {
          this.data.storyContent = e.target.value;
          this.schedulePreview();
          this.updateProgress();
        });
      }
      return;
    }

    $('#storyContent').summernote({
      placeholder: 'Tell your beautiful love story...',
      height: 200,
      toolbar: [
        ['style', ['bold', 'italic', 'underline']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['font', ['fontsize']],
        ['view', ['codeview']]
      ],
      callbacks: {
        onChange: (contents) => {
          this.data.storyContent = contents;
          this.schedulePreview();
          this.updateProgress();
        },
        onInit: () => {
          if (this.data.storyContent) {
            $('#storyContent').summernote('code', this.data.storyContent);
          }
        }
      }
    });
  },

  // ── Gallery Upload ──────────────────────────────
  async handleGalleryImageUpload(e) {
    const files = Array.from(e.target.files);
    const currentCount = this.data.galleryData.filter(g => g.type !== 'video').length;
    const remaining = 6 - currentCount;

    if (remaining <= 0) {
      alert('Maximum 6 photos allowed');
      return;
    }

    const toUpload = files.slice(0, remaining);

    for (const file of toUpload) {
      if (file.size > 5 * 1024 * 1024) {
        alert(`${file.name} is too large (max 5MB)`);
        continue;
      }
      const formData = new FormData();
      formData.append('file', file);
      formData.append('type', 'image');

      try {
        const res = await fetch('../api/premium/upload.php', { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
          this.data.galleryData.push({
            type: 'image', filename: json.filename,
            path: json.path, caption: ''
          });
          this.renderGallery();
          this.schedulePreview();
          this.updateProgress();
        } else {
          alert(json.error || 'Upload failed');
        }
      } catch (err) { alert('Upload error: ' + err.message); }
    }
    e.target.value = '';
  },

  async handleGalleryVideoUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 20 * 1024 * 1024) { alert('Video must be under 20MB'); return; }

    // Remove existing video if any
    this.data.galleryData = this.data.galleryData.filter(g => g.type !== 'video');

    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', 'video');

    try {
      const res = await fetch('../api/premium/upload.php', { method: 'POST', body: formData });
      const json = await res.json();
      if (json.success) {
        this.data.galleryData.push({
          type: 'video', filename: json.filename,
          path: json.path, caption: ''
        });
        this.renderGallery();
        this.schedulePreview();
      } else { alert(json.error || 'Upload failed'); }
    } catch (err) { alert('Upload error: ' + err.message); }
    e.target.value = '';
  },

  renderGallery() {
    // Images
    const imgContainer = document.getElementById('galleryImagesContainer');
    if (imgContainer) {
      const images = this.data.galleryData.filter(g => g.type !== 'video');
      imgContainer.innerHTML = images.map((img, idx) => `
        <div class="uploaded-image">
          <img src="${APP_BASE_URL}/${img.path}" alt="Gallery">
          <button class="remove-image" onclick="PremiumBuilder.removeGalleryItem(${idx})"><i class="fas fa-times"></i></button>
          <input class="uploaded-gallery-caption" placeholder="Add caption..." value="${img.caption || ''}"
                 onchange="PremiumBuilder.updateGalleryCaption(${idx}, this.value)">
        </div>
      `).join('');
    }

    // Video
    const vidContainer = document.getElementById('galleryVideoContainer');
    if (vidContainer) {
      const video = this.data.galleryData.find(g => g.type === 'video');
      if (video) {
        vidContainer.innerHTML = `
          <div class="uploaded-image" style="position:relative;">
            <video style="width:100%;height:150px;object-fit:cover;" muted>
              <source src="${APP_BASE_URL}/${video.path}">
            </video>
            <button class="remove-image" onclick="PremiumBuilder.removeGalleryVideo()"><i class="fas fa-times"></i></button>
          </div>`;
      } else { vidContainer.innerHTML = ''; }
    }
  },

  removeGalleryItem(idx) {
    const images = this.data.galleryData.filter(g => g.type !== 'video');
    images.splice(idx, 1);
    this.data.galleryData = [...images, ...this.data.galleryData.filter(g => g.type === 'video')];
    this.renderGallery();
    this.schedulePreview();
  },

  removeGalleryVideo() {
    this.data.galleryData = this.data.galleryData.filter(g => g.type !== 'video');
    this.renderGallery();
    this.schedulePreview();
  },

  updateGalleryCaption(idx, caption) {
    const images = this.data.galleryData.filter(g => g.type !== 'video');
    if (images[idx]) images[idx].caption = caption;
    this.schedulePreview();
  },

  // ── Music Upload ────────────────────────────────
  async handleMusicUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 10 * 1024 * 1024) { alert('Audio must be under 10MB'); return; }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', 'music');

    try {
      const res = await fetch('../api/premium/upload.php', { method: 'POST', body: formData });
      const json = await res.json();
      if (json.success) {
        this.data.musicFile = json.path;
        this.data.musicType = 'upload';
        document.getElementById('musicFileInfo').innerHTML =
          `<div class="music-file-info"><i class="fas fa-music"></i> <span>${file.name}</span>
           <button class="btn-icon danger" onclick="PremiumBuilder.removeMusic()"><i class="fas fa-trash"></i></button></div>`;
        this.schedulePreview();
      } else { alert(json.error || 'Upload failed'); }
    } catch (err) { alert('Upload error: ' + err.message); }
    e.target.value = '';
  },

  removeMusic() {
    this.data.musicFile = null;
    this.data.musicType = 'none';
    document.getElementById('musicFileInfo').innerHTML = '';
    document.querySelectorAll('.music-type-btn').forEach(b => {
      b.classList.toggle('active', b.dataset.music === 'none');
    });
    this.showMusicPanel('none');
    this.schedulePreview();
  },

  // ── Timeline ────────────────────────────────────
  addTimelineEvent() {
    if (this.data.timelineData.length >= 10) { alert('Maximum 10 events'); return; }
    this.data.timelineData.push({ date: '', title: '', description: '' });
    this.renderTimeline();
    this.schedulePreview();
  },

  renderTimeline() {
    const container = document.getElementById('timelineEventsContainer');
    if (!container) return;

    container.innerHTML = this.data.timelineData.map((ev, i) => `
      <div class="timeline-item">
        <div class="timeline-item-header">
          <span class="timeline-item-date">${ev.date || 'New Event'}</span>
          <div class="timeline-item-actions">
            <button class="btn-icon danger" onclick="PremiumBuilder.removeTimelineEvent(${i})">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
        <div class="timeline-inline-form">
          <input type="date" class="form-control form-control-sm" value="${ev.date}"
                 onchange="PremiumBuilder.updateTimeline(${i},'date',this.value)">
          <input type="text" class="form-control form-control-sm" placeholder="Event title" value="${ev.title}"
                 onchange="PremiumBuilder.updateTimeline(${i},'title',this.value)">
          <textarea class="form-control form-control-sm" rows="2" placeholder="Description"
                    onchange="PremiumBuilder.updateTimeline(${i},'description',this.value)">${ev.description}</textarea>
        </div>
      </div>
    `).join('');
  },

  updateTimeline(idx, field, value) {
    if (this.data.timelineData[idx]) {
      this.data.timelineData[idx][field] = value;
      // Update header date display
      if (field === 'date') {
        const header = document.querySelectorAll('.timeline-item')[idx]?.querySelector('.timeline-item-date');
        if (header) header.textContent = value || 'New Event';
      }
      this.schedulePreview();
    }
  },

  removeTimelineEvent(idx) {
    this.data.timelineData.splice(idx, 1);
    this.renderTimeline();
    this.schedulePreview();
  },

  // ── Settings ────────────────────────────────────
  setupSettingsListeners() {
    const urlInput = document.getElementById('customUrl');
    if (urlInput) {
      urlInput.addEventListener('input', e => {
        // Sanitize: only lowercase, numbers, hyphens
        let val = e.target.value.toLowerCase().replace(/[^a-z0-9-]/g, '').replace(/--+/g, '-');
        e.target.value = val;
        this.data.customUrl = val;
        this.checkUrlAvailability(val);
        this.updateProgress();
      });
    }

    const pwToggle = document.getElementById('passwordProtected');
    if (pwToggle) {
      pwToggle.addEventListener('change', e => {
        this.data.passwordProtected = e.target.checked;
        document.getElementById('passwordInput').style.display = e.target.checked ? 'block' : 'none';
      });
    }

    const pwInput = document.getElementById('sitePassword');
    if (pwInput) {
      pwInput.addEventListener('input', e => {
        this.data.sitePassword = e.target.value;
      });
    }
  },

  async checkUrlAvailability(slug) {
    clearTimeout(this.urlCheckTimer);
    const status = document.getElementById('urlStatus');
    if (!status) return;

    if (slug.length < 3) {
      status.textContent = slug.length > 0 ? 'URL must be at least 3 characters' : '';
      status.className = 'url-status';
      return;
    }

    status.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
    status.className = 'url-status';

    this.urlCheckTimer = setTimeout(async () => {
      try {
        const res = await fetch(`../api/premium/check-url.php?slug=${encodeURIComponent(slug)}`);
        const json = await res.json();
        if (json.available) {
          status.innerHTML = '<i class="fas fa-check-circle"></i> Available!';
          status.className = 'url-status available';
        } else {
          status.innerHTML = '<i class="fas fa-times-circle"></i> Already taken';
          status.className = 'url-status taken';
        }
      } catch (e) { status.textContent = ''; }
    }, 500);
  },

  // ── Actions ─────────────────────────────────────
  setupActions() {
    document.getElementById('saveBtn')?.addEventListener('click', () => this.save());
    document.getElementById('publishBtn')?.addEventListener('click', () => this.publish());
    document.getElementById('logoutBtn')?.addEventListener('click', async (e) => {
      e.preventDefault();
      await fetch('../api/auth/logout.php');
      window.location.href = 'index.php';
    });

    // Device toggle
    document.querySelectorAll('.device-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.device-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const frame = document.getElementById('previewFrame');
        frame.classList.remove('mobile', 'tablet', 'desktop');
        frame.classList.add(btn.dataset.device);
      });
    });
  },

  startAutoSave() {
    this.autoSaveTimer = setInterval(() => this.save(true), 30000);
  },

  async save(isAuto = false) {
    const statusEl = document.getElementById('saveStatus');

    const payload = {
      partner1Name: this.data.partner1Name,
      partner2Name: this.data.partner2Name,
      anniversaryDate: this.data.anniversaryDate,
      websiteTitle: this.data.websiteTitle,
      welcomeMessage: this.data.welcomeMessage,
      template: this.data.template,
      primaryColor: this.data.primaryColor,
      secondaryColor: this.data.secondaryColor,
      accentColor: this.data.accentColor,
      bgColor: this.data.bgColor,
      fontPairing: this.data.fontPairing,
      storyEnabled: this.data.storyEnabled,
      storyContent: this.data.storyContent,
      galleryEnabled: this.data.galleryEnabled,
      galleryData: this.data.galleryData,
      timelineEnabled: this.data.timelineEnabled,
      timelineData: this.data.timelineData,
      musicType: this.data.musicType,
      musicFile: this.data.musicFile,
      musicSpotifyUrl: this.data.musicSpotifyUrl,
      customUrl: this.data.customUrl,
      passwordProtected: this.data.passwordProtected,
      sitePassword: this.data.sitePassword
    };

    try {
      if (statusEl) statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      const res = await fetch('../api/premium/save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();

      if (json.success) {
        if (statusEl) statusEl.innerHTML = '<i class="fas fa-check text-success"></i> Saved!';
        if (!isAuto) {
          const saveBtn = document.getElementById('saveBtn');
          saveBtn.innerHTML = '<i class="fas fa-check"></i> Saved!';
          setTimeout(() => { saveBtn.innerHTML = '<i class="fas fa-save"></i> Save'; }, 2000);
        }
        setTimeout(() => {
          if (statusEl) statusEl.innerHTML = '<i class="fas fa-save"></i> Auto-saving...';
        }, 3000);
      } else {
        if (statusEl) statusEl.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> ' + (json.error || 'Save failed');
        if (!isAuto) alert(json.error || 'Save failed');
      }
    } catch (e) {
      if (statusEl) statusEl.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Save error';
      if (!isAuto) alert('Connection error. Please try again.');
    }
  },

  async publish() {
    // Validate locally first
    const errors = [];
    if (!this.data.partner1Name) errors.push('Partner 1 name');
    if (!this.data.partner2Name) errors.push('Partner 2 name');
    if (!this.data.websiteTitle) errors.push('Website title');
    if (!this.data.customUrl || this.data.customUrl.length < 3) errors.push('Custom URL (min 3 chars)');

    if (errors.length) {
      alert('Please complete these required fields:\n• ' + errors.join('\n• '));
      return;
    }

    // Save first
    await this.save();

    const publishBtn = document.getElementById('publishBtn');
    publishBtn.disabled = true;
    publishBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publishing...';

    try {
      const res = await fetch('../api/premium/publish.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' }
      });
      const json = await res.json();

      if (json.success) {
        publishBtn.innerHTML = '<i class="fas fa-check"></i> Published!';
        // Show success with URL
        const urlDisplay = document.createElement('div');
        urlDisplay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:9999;display:flex;align-items:center;justify-content:center;';
        urlDisplay.innerHTML = `
          <div style="background:#fff;border-radius:20px;padding:40px;max-width:500px;width:90%;text-align:center;">
            <div style="font-size:4rem;">🎉</div>
            <h2 style="color:#8B4789;margin:15px 0;">Published!</h2>
            <p>Your love website is live at:</p>
            <div style="background:#f8f4f9;padding:12px;border-radius:10px;margin:15px 0;word-break:break-all;">
              <a href="${json.url}" target="_blank" style="color:#8B4789;font-weight:600;">${json.url}</a>
            </div>
            <button onclick="navigator.clipboard.writeText('${json.url}');this.textContent='Copied!'" class="btn btn-outline-secondary me-2">
              <i class="fas fa-copy"></i> Copy URL
            </button>
            <button onclick="this.closest('div[style]').remove()" class="btn text-white" style="background:#8B4789;">
              Close
            </button>
          </div>`;
        document.body.appendChild(urlDisplay);
      } else {
        alert(json.error || 'Publish failed');
      }
    } catch (e) {
      alert('Connection error. Please try again.');
    }

    publishBtn.disabled = false;
    publishBtn.innerHTML = '<i class="fas fa-rocket"></i> Publish';
  },

  // ── Progress ────────────────────────────────────
  updateProgress() {
    const total = 8;
    let done = 0;
    if (this.data.partner1Name) done++;
    if (this.data.partner2Name) done++;
    if (this.data.websiteTitle) done++;
    if (this.data.welcomeMessage) done++;
    if (this.data.template) done++;
    if (this.data.storyContent) done++;
    if (this.data.galleryData.length > 0) done++;
    if (this.data.customUrl.length >= 3) done++;

    const pct = Math.round((done / total) * 100);
    const fill = document.getElementById('progressFill');
    const text = document.getElementById('progressText');
    if (fill) fill.style.width = pct + '%';
    if (text) text.textContent = pct + '% Complete';
  },

  // ── Live Preview ────────────────────────────────
  schedulePreview() {
    clearTimeout(this.previewTimer);
    this.previewTimer = setTimeout(() => this.updatePreview(), 300);
  },

  updatePreview() {
    const frame = document.getElementById('previewFrame');
    if (!frame) return;
    frame.innerHTML = this.generatePreviewHTML();
  },

  generatePreviewHTML() {
    const d = this.data;
    const fonts = {
      'romantic-elegant': { heading: "'Dancing Script', cursive", body: "'Lora', serif" },
      'modern-clean': { heading: "'Montserrat', sans-serif", body: "'Inter', sans-serif" },
      'classic-serif': { heading: "'Playfair Display', serif", body: "'Source Serif Pro', serif" }
    };
    const font = fonts[d.fontPairing] || fonts['romantic-elegant'];
    const p1 = d.partner1Name || 'Partner 1';
    const p2 = d.partner2Name || 'Partner 2';

    let html = `<div style="font-family:${font.body};overflow-y:auto;max-height:calc(100vh - 120px);">`;

    // Hero
    html += `<div style="padding:60px 20px;text-align:center;background:linear-gradient(135deg,${d.primaryColor},${d.secondaryColor});color:#fff;">
      <h1 style="font-family:${font.heading};font-size:2.2rem;margin:0 0 8px;text-shadow:2px 2px 4px rgba(0,0,0,0.3);">${p1} & ${p2}</h1>
      <p style="font-size:1.1rem;opacity:0.9;">${d.websiteTitle || 'Our Love Story'}</p>
    </div>`;

    // Welcome
    if (d.welcomeMessage) {
      html += `<div style="padding:30px 20px;text-align:center;background:${d.bgColor};">
        <h2 style="font-family:${font.heading};color:${d.primaryColor};font-size:1.8rem;margin-bottom:12px;">Welcome to Our Story</h2>
        <p style="color:#555;line-height:1.7;max-width:600px;margin:0 auto;">${d.welcomeMessage}</p>
      </div>`;
    }

    // Story
    if (d.storyEnabled && d.storyContent) {
      html += `<div style="padding:30px 20px;background:#f8f8f8;">
        <h2 style="font-family:${font.heading};color:${d.primaryColor};text-align:center;font-size:1.8rem;margin-bottom:16px;">Our Story</h2>
        <div style="max-width:600px;margin:0 auto;line-height:1.8;color:#444;">${d.storyContent}</div>
      </div>`;
    }

    // Gallery
    if (d.galleryEnabled && d.galleryData.length > 0) {
      html += `<div style="padding:30px 20px;background:${d.bgColor};">
        <h2 style="font-family:${font.heading};color:${d.primaryColor};text-align:center;font-size:1.8rem;margin-bottom:16px;">Our Memories</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:8px;">`;
      d.galleryData.forEach(item => {
        if (item.type === 'video') {
          html += `<div style="position:relative;border-radius:8px;overflow:hidden;aspect-ratio:1;">
            <video muted style="width:100%;height:100%;object-fit:cover;"><source src="${APP_BASE_URL}/${item.path}"></video>
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:2rem;color:#fff;">▶</div>
          </div>`;
        } else {
          html += `<div style="border-radius:8px;overflow:hidden;aspect-ratio:1;">
            <img src="${APP_BASE_URL}/${item.path}" style="width:100%;height:100%;object-fit:cover;" alt="">
          </div>`;
        }
      });
      html += `</div></div>`;
    }

    // Timeline
    if (d.timelineEnabled && d.timelineData.length > 0) {
      html += `<div style="padding:30px 20px;background:#f8f8f8;">
        <h2 style="font-family:${font.heading};color:${d.primaryColor};text-align:center;font-size:1.8rem;margin-bottom:20px;">Our Journey</h2>
        <div style="max-width:500px;margin:0 auto;position:relative;padding-left:30px;">
          <div style="position:absolute;left:10px;top:0;bottom:0;width:3px;background:${d.primaryColor};"></div>`;
      d.timelineData.forEach(ev => {
        if (ev.title || ev.date) {
          html += `<div style="margin-bottom:20px;position:relative;">
            <div style="position:absolute;left:-26px;top:4px;width:12px;height:12px;border-radius:50%;background:${d.primaryColor};border:3px solid #fff;box-shadow:0 0 0 2px ${d.primaryColor};"></div>
            <div style="background:#fff;padding:12px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
              <div style="color:${d.primaryColor};font-weight:600;font-size:0.9rem;">${ev.date || ''}</div>
              <div style="font-weight:600;margin:4px 0;">${ev.title || ''}</div>
              <p style="color:#666;font-size:0.85rem;margin:0;">${ev.description || ''}</p>
            </div>
          </div>`;
        }
      });
      html += `</div></div>`;
    }

    // Music indicator
    if (d.musicType !== 'none') {
      html += `<div style="padding:12px;text-align:center;background:${d.primaryColor}22;font-size:0.85rem;color:${d.primaryColor};">
        <i class="fas fa-music"></i> Background music will play on the live page
      </div>`;
    }

    // Final message
    html += `<div style="padding:40px 20px;text-align:center;background:linear-gradient(135deg,${d.primaryColor},${d.secondaryColor});color:#fff;">
      <h2 style="font-family:${font.heading};font-size:2rem;margin-bottom:12px;">Forever & Always</h2>
      <p style="font-size:1rem;">Happy Valentine's Day, ${p2}! ❤️<br>All my love, ${p1}</p>
    </div>`;

    html += '</div>';
    return html;
  }
};

// Initialise when builder page loads
document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('premiumBuilder')) {
    PremiumBuilder.init();
  }
});
