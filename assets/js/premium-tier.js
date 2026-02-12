// ================================================
// PREMIUM TIER JAVASCRIPT
// ================================================

const PremiumBuilder = {
  builderData: {
    basicInfo: {
      partner1Name: '',
      partner2Name: '',
      relationshipStatus: 'dating',
      anniversaryDate: '',
      websiteTitle: '',
      welcomeMessage: ''
    },
    design: {
      template: 'template-romantic',
      primaryColor: '#8B4789',
      secondaryColor: '#D4A5D4',
      accentColor: '#FFD700',
      backgroundColor: '#FFFFFF',
      fontPairing: 'romantic-elegant'
    },
    content: {
      sections: {
        hero: { enabled: true, backgroundImage: null },
        welcome: { enabled: true },
        story: { enabled: true, content: '' },
        gallery: { enabled: true, images: [] },
        timeline: { enabled: true, events: [] },
        video: { enabled: false, url: '' },
        music: { enabled: false, file: null },
        finalMessage: { enabled: true, content: '' }
      }
    },
    settings: {
      customUrl: '',
      password: '',
      passwordProtected: false
    }
  },

  currentTab: 'basic',
  previewDevice: 'desktop',

  init() {
    this.loadFromStorage();
    this.setupTabs();
    this.setupEventListeners();
    this.updatePreview();
    this.updateProgress();
  },

  loadFromStorage() {
    const saved = ValentineUtils.storage.get('premium_builder_data');
    if (saved) {
      this.builderData = { ...this.builderData, ...saved };
      this.populateForm();
    }
  },

  populateForm() {
    // Basic Info
    document.getElementById('partner1Name').value = this.builderData.basicInfo.partner1Name || '';
    document.getElementById('partner2Name').value = this.builderData.basicInfo.partner2Name || '';
    // ... populate other fields
  },

  setupTabs() {
    document.querySelectorAll('.tab-button').forEach(button => {
      button.addEventListener('click', (e) => {
        const tab = e.target.dataset.tab;
        this.switchTab(tab);
      });
    });
  },

  switchTab(tabName) {
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    document.getElementById(`${tabName}Tab`).classList.add('active');

    this.currentTab = tabName;
  },

  setupEventListeners() {
    this.setupBasicInfoListeners();
    this.setupDesignListeners();
    this.setupContentListeners();
    this.setupSettingsListeners();
    this.setupActionButtons();
    this.setupAutoSave();
  },

  setupBasicInfoListeners() {
    const fields = ['partner1Name', 'partner2Name', 'websiteTitle', 'welcomeMessage', 'anniversaryDate'];
    fields.forEach(field => {
      const element = document.getElementById(field);
      if (element) {
        element.addEventListener('input', (e) => {
          const keys = field.split('.');
          if (keys.length === 1) {
            this.builderData.basicInfo[field] = e.target.value;
          }
          this.updatePreview();
          this.updateProgress();
        });
      }
    });
  },

  setupDesignListeners() {
    document.querySelectorAll('.template-option').forEach(option => {
      option.addEventListener('click', (e) => {
        this.selectTemplate(e.currentTarget);
      });
    });

    document.querySelectorAll('.color-input').forEach(input => {
      input.addEventListener('change', (e) => {
        const colorType = e.target.dataset.colorType;
        this.builderData.design[colorType] = e.target.value;
        this.updatePreview();
      });
    });

    document.querySelectorAll('.color-preset').forEach(preset => {
      preset.addEventListener('click', (e) => {
        this.applyColorPreset(e.currentTarget.dataset.preset);
      });
    });
  },

  selectTemplate(element) {
    document.querySelectorAll('.template-option').forEach(opt => opt.classList.remove('active'));
    element.classList.add('active');
    
    const template = element.dataset.template;
    this.builderData.design.template = template;
    this.updatePreview();
  },

  applyColorPreset(presetName) {
    const presets = {
      'romantic-red': {
        primaryColor: '#FF1744',
        secondaryColor: '#FFB6C1',
        accentColor: '#FFD700',
        backgroundColor: '#FFFFFF'
      },
      'elegant-purple': {
        primaryColor: '#8B4789',
        secondaryColor: '#D4A5D4',
        accentColor: '#FFD700',
        backgroundColor: '#F8F4F9'
      },
      'soft-pink': {
        primaryColor: '#FFB6C1',
        secondaryColor: '#FFC0CB',
        accentColor: '#FF69B4',
        backgroundColor: '#FFF0F5'
      }
    };

    const preset = presets[presetName];
    if (preset) {
      Object.assign(this.builderData.design, preset);
      this.updateColorInputs();
      this.updatePreview();
    }
  },

  updateColorInputs() {
    Object.keys(this.builderData.design).forEach(key => {
      if (key.includes('Color')) {
        const input = document.querySelector(`[data-color-type="${key}"]`);
        if (input) {
          input.value = this.builderData.design[key];
        }
      }
    });
  },

  setupContentListeners() {
    // Summernote initialization would go here
    // For now, simple textarea
    const storyContent = document.getElementById('storyContent');
    if (storyContent) {
      storyContent.addEventListener('input', (e) => {
        this.builderData.content.sections.story.content = e.target.value;
        this.updatePreview();
      });
    }

    // Gallery upload
    const galleryUpload = document.getElementById('galleryUpload');
    if (galleryUpload) {
      galleryUpload.addEventListener('change', (e) => this.handleGalleryUpload(e));
    }

    // Timeline add button
    const addTimelineBtn = document.getElementById('addTimelineEvent');
    if (addTimelineBtn) {
      addTimelineBtn.addEventListener('click', () => this.addTimelineEvent());
    }

    // Toggle switches
    document.querySelectorAll('.section-toggle').forEach(toggle => {
      toggle.addEventListener('change', (e) => {
        const section = e.target.dataset.section;
        this.builderData.content.sections[section].enabled = e.target.checked;
        this.updatePreview();
      });
    });
  },

  handleGalleryUpload(e) {
    const files = Array.from(e.target.files);
    files.forEach(file => {
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.builderData.content.sections.gallery.images.push({
            id: ValentineUtils.generateId(),
            src: e.target.result,
            caption: ''
          });
          this.renderGalleryImages();
          this.updatePreview();
        };
        reader.readAsDataURL(file);
      }
    });
  },

  renderGalleryImages() {
    const container = document.getElementById('galleryImagesContainer');
    if (!container) return;

    container.innerHTML = this.builderData.content.sections.gallery.images.map(img => `
      <div class="uploaded-image" data-id="${img.id}">
        <img src="${img.src}" alt="Gallery image">
        <button class="remove-image" onclick="PremiumBuilder.removeGalleryImage('${img.id}')">
          <i class="fas fa-times"></i>
        </button>
      </div>
    `).join('');
  },

  removeGalleryImage(imageId) {
    this.builderData.content.sections.gallery.images = 
      this.builderData.content.sections.gallery.images.filter(img => img.id !== imageId);
    this.renderGalleryImages();
    this.updatePreview();
  },

  addTimelineEvent() {
    const event = {
      id: ValentineUtils.generateId(),
      date: '',
      title: '',
      description: '',
      image: null
    };
    this.builderData.content.sections.timeline.events.push(event);
    this.renderTimelineEvents();
  },

  renderTimelineEvents() {
    const container = document.getElementById('timelineEventsContainer');
    if (!container) return;

    container.innerHTML = this.builderData.content.sections.timeline.events.map(event => `
      <div class="timeline-item" data-id="${event.id}">
        <div class="timeline-item-header">
          <span class="timeline-item-date">${event.date || 'Set date'}</span>
          <div class="timeline-item-actions">
            <button class="btn-icon" onclick="PremiumBuilder.editTimelineEvent('${event.id}')">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn-icon danger" onclick="PremiumBuilder.removeTimelineEvent('${event.id}')">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
        <div class="timeline-item-content">
          <strong>${event.title || 'Event title'}</strong>
          <p>${event.description || 'Event description'}</p>
        </div>
      </div>
    `).join('');
  },

  removeTimelineEvent(eventId) {
    this.builderData.content.sections.timeline.events = 
      this.builderData.content.sections.timeline.events.filter(e => e.id !== eventId);
    this.renderTimelineEvents();
    this.updatePreview();
  },

  setupSettingsListeners() {
    const customUrl = document.getElementById('customUrl');
    if (customUrl) {
      customUrl.addEventListener('input', (e) => {
        this.builderData.settings.customUrl = e.target.value;
      });
    }

    const passwordToggle = document.getElementById('passwordProtected');
    if (passwordToggle) {
      passwordToggle.addEventListener('change', (e) => {
        this.builderData.settings.passwordProtected = e.target.checked;
        document.getElementById('passwordInput').style.display = e.target.checked ? 'block' : 'none';
      });
    }
  },

  setupActionButtons() {
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
      saveBtn.addEventListener('click', () => this.save());
    }

    const previewBtn = document.getElementById('previewBtn');
    if (previewBtn) {
      previewBtn.addEventListener('click', () => this.openFullPreview());
    }

    const publishBtn = document.getElementById('publishBtn');
    if (publishBtn) {
      publishBtn.addEventListener('click', () => this.publish());
    }

    // Device toggle
    document.querySelectorAll('.device-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        document.querySelectorAll('.device-btn').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
        this.previewDevice = e.target.dataset.device;
        this.updatePreviewDevice();
      });
    });
  },

  setupAutoSave() {
    setInterval(() => {
      this.save(true);
    }, 30000); // Auto-save every 30 seconds
  },

  updatePreview() {
    const preview = document.getElementById('previewFrame');
    if (!preview) return;

    // Generate preview HTML based on builderData
    const previewHTML = this.generatePreviewHTML();
    
    // Update iframe or container
    preview.innerHTML = previewHTML;
  },

  generatePreviewHTML() {
    const data = this.builderData;
    
    return `
      <div style="font-family: ${this.getFontFamily()}; color: ${data.design.primaryColor};">
        <div style="padding: 20px; text-align: center;">
          <h1 style="font-size: 2.5rem; margin-bottom: 10px;">
            ${data.basicInfo.partner1Name || 'Partner 1'} & ${data.basicInfo.partner2Name || 'Partner 2'}
          </h1>
          <p style="font-size: 1.2rem;">${data.basicInfo.welcomeMessage || 'Welcome to our love story...'}</p>
        </div>
        ${data.content.sections.story.enabled ? `
          <div style="padding: 20px; background: ${data.design.backgroundColor};">
            <h2>Our Story</h2>
            <p>${data.content.sections.story.content || 'Tell your beautiful story here...'}</p>
          </div>
        ` : ''}
        <div style="padding: 20px; text-align: center;">
          <em>Preview updating in real-time...</em>
        </div>
      </div>
    `;
  },

  getFontFamily() {
    const fonts = {
      'romantic-elegant': "'Dancing Script', cursive",
      'modern-clean': "'Poppins', sans-serif",
      'classic-serif': "'Playfair Display', serif"
    };
    return fonts[this.builderData.design.fontPairing] || fonts['romantic-elegant'];
  },

  updatePreviewDevice() {
    const frame = document.getElementById('previewFrame');
    if (!frame) return;

    frame.classList.remove('mobile', 'tablet', 'desktop');
    frame.classList.add(this.previewDevice);
  },

  updateProgress() {
    const required = this.getRequiredFields();
    const completed = this.getCompletedFields();
    const percentage = Math.round((completed / required) * 100);

    const progressFill = document.querySelector('.progress-fill');
    const progressText = document.querySelector('.progress-text');

    if (progressFill) {
      progressFill.style.width = percentage + '%';
    }

    if (progressText) {
      progressText.textContent = `${percentage}% Complete`;
    }
  },

  getRequiredFields() {
    return 8; // Total required fields
  },

  getCompletedFields() {
    let count = 0;
    if (this.builderData.basicInfo.partner1Name) count++;
    if (this.builderData.basicInfo.partner2Name) count++;
    if (this.builderData.basicInfo.websiteTitle) count++;
    if (this.builderData.basicInfo.welcomeMessage) count++;
    if (this.builderData.design.template) count++;
    if (this.builderData.content.sections.story.content) count++;
    if (this.builderData.content.sections.gallery.images.length > 0) count++;
    if (this.builderData.settings.customUrl) count++;
    return count;
  },

  save(isAutoSave = false) {
    ValentineUtils.storage.set('premium_builder_data', this.builderData);
    
    if (!isAutoSave) {
      const saveBtn = document.getElementById('saveBtn');
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML = '<i class="fas fa-check"></i> Saved!';
      
      setTimeout(() => {
        saveBtn.innerHTML = originalText;
      }, 2000);
    }
  },

  openFullPreview() {
    // Open preview in new tab
    window.open('/premium-tier/preview.html', '_blank');
  },

  publish() {
    const validation = this.validateForPublish();
    
    if (!validation.isValid) {
      alert('Please complete all required fields:\n' + validation.errors.join('\n'));
      return;
    }

    // In production, this would redirect to payment
    if (confirm('Ready to publish? This will take you to payment.')) {
      // Redirect to payment page
      console.log('Redirecting to payment...');
      // window.location.href = '/payment.php';
    }
  },

  validateForPublish() {
    const errors = [];
    
    if (!this.builderData.basicInfo.partner1Name) errors.push('Partner 1 name');
    if (!this.builderData.basicInfo.partner2Name) errors.push('Partner 2 name');
    if (!this.builderData.basicInfo.websiteTitle) errors.push('Website title');
    if (!this.builderData.settings.customUrl) errors.push('Custom URL');
    
    return {
      isValid: errors.length === 0,
      errors: errors
    };
  }
};

// Initialize builder
document.addEventListener('DOMContentLoaded', function() {
  if (document.getElementById('premiumBuilder')) {
    PremiumBuilder.init();
  }
});
