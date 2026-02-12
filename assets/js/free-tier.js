// ================================================
// FREE TIER JAVASCRIPT
// ================================================

// Creator Page Functionality
const FreeTierCreator = {
  formData: {
    senderName: '',
    recipientName: '',
    question: 'Will you be my Valentine?',
    senderEmail: '',
    theme: 'theme-romantic-red',
    imageFile: null
  },

  init() {
    this.setupEventListeners();
  },

  setupEventListeners() {
    // Question selection
    document.querySelectorAll('.question-option').forEach(option => {
      option.addEventListener('click', (e) => this.selectQuestion(e));
    });

    // Custom question input
    const customQuestionInput = document.getElementById('customQuestion');
    if (customQuestionInput) {
      customQuestionInput.addEventListener('input', (e) => {
        this.formData.question = e.target.value;
      });
    }

    // Theme selection
    document.querySelectorAll('.theme-option').forEach(theme => {
      theme.addEventListener('click', (e) => this.selectTheme(e));
    });

    // Image upload
    const imageUpload = document.getElementById('imageUpload');
    if (imageUpload) {
      imageUpload.addEventListener('change', (e) => this.handleImageUpload(e));
    }

    // Form inputs
    const inputs = ['senderName', 'recipientName', 'senderEmail'];
    inputs.forEach(inputId => {
      const element = document.getElementById(inputId);
      if (element) {
        element.addEventListener('input', (e) => {
          this.formData[inputId] = e.target.value;
        });
      }
    });

    // Generate button
    const generateBtn = document.getElementById('generateBtn');
    if (generateBtn) {
      generateBtn.addEventListener('click', () => this.generateLink());
    }

    // Copy button
    const copyBtn = document.getElementById('copyBtn');
    if (copyBtn) {
      copyBtn.addEventListener('click', () => this.copyLink());
    }

    // Share buttons
    this.setupShareButtons();
  },

  selectQuestion(e) {
    const option = e.currentTarget;
    const questionText = option.dataset.question;

    document.querySelectorAll('.question-option').forEach(opt => {
      opt.classList.remove('active');
    });
    option.classList.add('active');

    if (questionText === 'custom') {
      const customInput = document.getElementById('customQuestion');
      customInput.style.display = 'block';
      customInput.focus();
      this.formData.question = customInput.value || '';
    } else {
      document.getElementById('customQuestion').style.display = 'none';
      this.formData.question = questionText;
    }
  },

  selectTheme(e) {
    const theme = e.currentTarget;
    const themeClass = theme.dataset.theme;

    document.querySelectorAll('.theme-option').forEach(t => {
      t.classList.remove('active');
    });
    theme.classList.add('active');
    this.formData.theme = themeClass;
  },

  handleImageUpload(e) {
    const file = e.target.files[0];
    if (file) {
      if (!file.type.startsWith('image/')) {
        alert('Please upload an image file');
        return;
      }
      if (file.size > 5 * 1024 * 1024) {
        alert('Image size should be less than 5MB');
        return;
      }

      this.formData.imageFile = file;

      const reader = new FileReader();
      reader.onload = (e) => {
        const preview = document.getElementById('imagePreview');
        if (preview) {
          preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
          preview.style.display = 'block';
        }
      };
      reader.readAsDataURL(file);
    }
  },

  validateForm() {
    const errors = [];
    if (!this.formData.senderName.trim()) errors.push('Your name is required');
    if (!this.formData.recipientName.trim()) errors.push('Recipient name is required');
    if (!this.formData.question.trim()) errors.push('Please select or enter a question');
    if (this.formData.senderEmail && !ValentineUtils.validateEmail(this.formData.senderEmail)) {
      errors.push('Please enter a valid email address');
    }
    return { isValid: errors.length === 0, errors };
  },

  generateLink() {
    const validation = this.validateForm();

    if (!validation.isValid) {
      const errorContainer = document.querySelector('.creation-card');
      validation.errors.forEach(error => {
        ValentineUtils.showError(error, errorContainer);
      });
      return;
    }

    // Show loading
    const generateBtn = document.getElementById('generateBtn');
    const originalHTML = generateBtn.innerHTML;
    generateBtn.disabled = true;
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

    // Build FormData for file upload support
    const formData = new FormData();
    formData.append('senderName', this.formData.senderName);
    formData.append('recipientName', this.formData.recipientName);
    formData.append('senderEmail', this.formData.senderEmail);
    formData.append('question', this.formData.question);
    formData.append('theme', this.formData.theme);

    if (this.formData.imageFile) {
      formData.append('image', this.formData.imageFile);
    }

    // Determine API base URL
    const apiBase = window.API_BASE || '../api';

    fetch(apiBase + '/create.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        generateBtn.disabled = false;
        generateBtn.innerHTML = originalHTML;

        if (data.success) {
          this.displayGeneratedLink(data.url);
          ValentineUtils.showSuccess('Your Valentine link has been created! 💝', document.querySelector('.creation-card'));
          ValentineUtils.smoothScroll('#linkGenerated');
        } else {
          ValentineUtils.showError(data.error || 'Something went wrong', document.querySelector('.creation-card'));
        }
      })
      .catch(err => {
        generateBtn.disabled = false;
        generateBtn.innerHTML = originalHTML;
        ValentineUtils.showError('Network error. Please check your connection.', document.querySelector('.creation-card'));
        console.error('Error:', err);
      });
  },

  displayGeneratedLink(url) {
    const linkSection = document.getElementById('linkGenerated');
    const linkInput = document.getElementById('generatedLink');

    if (linkSection && linkInput) {
      linkInput.value = url;
      linkSection.classList.add('show');
      this.generatedUrl = url;
    }
  },

  copyLink() {
    const linkInput = document.getElementById('generatedLink');
    const copyBtn = document.getElementById('copyBtn');

    ValentineUtils.copyToClipboard(linkInput.value).then(success => {
      if (success) {
        const originalText = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        copyBtn.classList.add('copied');

        setTimeout(() => {
          copyBtn.innerHTML = originalText;
          copyBtn.classList.remove('copied');
        }, 2000);
      } else {
        linkInput.select();
        alert('Press Ctrl+C to copy the link');
      }
    });
  },

  setupShareButtons() {
    const shareText = "Someone has a special question for you! 💝";

    document.querySelectorAll('.btn-share').forEach(btn => {
      btn.addEventListener('click', () => {
        const platform = btn.dataset.platform;
        const url = this.generatedUrl || document.getElementById('generatedLink')?.value || '';

        switch (platform) {
          case 'whatsapp':
            ValentineUtils.share.whatsapp(url, shareText);
            break;
          case 'facebook':
            ValentineUtils.share.facebook(url);
            break;
          case 'twitter':
            ValentineUtils.share.twitter(url, shareText);
            break;
          case 'email':
            ValentineUtils.share.email(url, 'A Special Valentine Question', shareText);
            break;
        }
      });
    });
  }
};


// ================================================
// QUESTION PAGE — No Button Viewport Dodge
// ================================================
const FreeTierQuestion = {
  // Dodge cooldown — prevents rapid-fire dodging from mousemove
  isDodging: false,
  dodgeCooldownMs: 500,

  // Message state — only one message at a time
  activeMessage: null,
  messageTimer: null,

  playfulMessages: [
    "Oops! Try again! 😅",
    "Not there! 😜",
    "You can't click me! 😏",
    "Almost got me! 😄",
    "So close! 😉",
    "Nope! 💕",
    "I'm too fast! ⚡",
    "Give up yet? 😋",
    "Just say YES! ❤️",
    "Please? 🥺",
    "C'mon, say YES! 💘",
    "The YES button is right there! 👆"
  ],

  // Track whether the button has been "activated" (first dodge happened)
  activated: false,

  init() {
    this.setupNoButtonBehavior();
    this.setupYesButton();
  },

  setupNoButtonBehavior() {
    const noBtn = document.getElementById('noBtn');
    if (!noBtn) return;

    // ── IMPORTANT: Leave the button in its natural position on first load ──
    // It sits next to YES inside the .button-section via normal flow.
    // Only once it dodges for the first time do we switch to fixed positioning.

    // Desktop: dodge on hover
    noBtn.addEventListener('mouseenter', () => {
      this.triggerDodge(noBtn);
    });

    // Desktop: dodge on mouse proximity
    document.addEventListener('mousemove', (e) => {
      if (!this.activated) return; // don't proximity-dodge before first interaction
      this.checkProximityAndDodge(noBtn, e.clientX, e.clientY);
    });

    // Mobile: dodge on touch
    noBtn.addEventListener('touchstart', (e) => {
      e.preventDefault();
      this.triggerDodge(noBtn);
    });

    // Mobile: dodge when finger gets close
    document.addEventListener('touchmove', (e) => {
      if (!this.activated) return;
      const touch = e.touches[0];
      this.checkProximityAndDodge(noBtn, touch.clientX, touch.clientY);
    });

    // Block any actual click
    noBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      this.triggerDodge(noBtn);
    });
  },

  /**
   * Gate that prevents rapid-fire dodges.
   * Only allows one dodge per cooldown period.
   */
  triggerDodge(button) {
    if (this.isDodging) return;

    this.isDodging = true;
    this.dodgeNoButton(button);

    setTimeout(() => {
      this.isDodging = false;
    }, this.dodgeCooldownMs);
  },

  checkProximityAndDodge(button, cursorX, cursorY) {
    const rect = button.getBoundingClientRect();
    const btnCenterX = rect.left + rect.width / 2;
    const btnCenterY = rect.top + rect.height / 2;

    const distance = Math.sqrt(
      Math.pow(cursorX - btnCenterX, 2) +
      Math.pow(cursorY - btnCenterY, 2)
    );

    // Dodge when cursor/finger is within 90px
    if (distance < 90) {
      this.triggerDodge(button);
    }
  },

  dodgeNoButton(button) {
    // ── First dodge: switch from static to fixed positioning ──
    if (!this.activated) {
      this.activated = true;
      button.style.position = 'fixed';
      button.style.zIndex = '9999';
      button.style.transition = 'left 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), top 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.3s ease';
      button.style.margin = '0'; // reset any flow margins
    }

    const btnRect = button.getBoundingClientRect();
    const btnWidth = btnRect.width;
    const btnHeight = btnRect.height;

    // Viewport bounds with safe padding
    const padding = 20;
    const maxX = window.innerWidth - btnWidth - padding;
    const maxY = window.innerHeight - btnHeight - padding;

    // Current position
    const currentX = btnRect.left;
    const currentY = btnRect.top;

    // Generate random position ensuring it moves at least 150px away
    let newX, newY;
    let attempts = 0;
    const minJumpDistance = 150;

    do {
      newX = padding + Math.random() * (maxX - padding);
      newY = padding + Math.random() * (maxY - padding);
      attempts++;
    } while (
      Math.sqrt(Math.pow(newX - currentX, 2) + Math.pow(newY - currentY, 2)) < minJumpDistance &&
      attempts < 30
    );

    // Small random rotation for personality (±12 degrees max)
    const rotation = (Math.random() - 0.5) * 24;

    // Apply — only position and rotation change, size stays the same
    button.style.left = newX + 'px';
    button.style.top = newY + 'px';
    button.style.transform = `rotate(${rotation}deg)`;

    // Show ONE playful message
    this.showPlayfulMessage(newX, newY, btnWidth, btnHeight);
  },

  showPlayfulMessage(btnX, btnY, btnW, btnH) {
    // ── Remove any existing message immediately ──
    if (this.activeMessage) {
      this.activeMessage.remove();
      this.activeMessage = null;
    }
    if (this.messageTimer) {
      clearTimeout(this.messageTimer);
      this.messageTimer = null;
    }

    // Pick a random message
    const text = this.playfulMessages[
      Math.floor(Math.random() * this.playfulMessages.length)
    ];

    const msgEl = document.createElement('div');
    msgEl.className = 'dodge-message';
    msgEl.textContent = text;

    // Position above the button's new location
    let msgX = btnX + btnW / 2;
    let msgY = btnY - 10;

    // If button is near top of viewport, show message below instead
    if (btnY < 60) {
      msgY = btnY + btnH + 10;
    }

    msgEl.style.cssText = `
      position: fixed;
      z-index: 10000;
      left: ${msgX}px;
      top: ${msgY}px;
      transform: translateX(-50%) translateY(0);
      background: rgba(0, 0, 0, 0.8);
      color: #fff;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 600;
      pointer-events: none;
      white-space: nowrap;
      opacity: 0;
      transition: opacity 0.25s ease, transform 0.6s ease;
    `;

    document.body.appendChild(msgEl);
    this.activeMessage = msgEl;

    // Fade in (next frame so transition triggers)
    requestAnimationFrame(() => {
      msgEl.style.opacity = '1';
      msgEl.style.transform = 'translateX(-50%) translateY(-8px)';
    });

    // Fade out after 1.2s, then remove
    this.messageTimer = setTimeout(() => {
      msgEl.style.opacity = '0';
      msgEl.style.transform = 'translateX(-50%) translateY(-20px)';

      setTimeout(() => {
        if (msgEl.parentNode) msgEl.remove();
        if (this.activeMessage === msgEl) this.activeMessage = null;
      }, 350);
    }, 1200);
  },

  setupYesButton() {
    const yesBtn = document.getElementById('yesBtn');

    if (yesBtn) {
      yesBtn.addEventListener('mouseenter', () => {
        yesBtn.style.transform = 'scale(1.12)';
      });
      yesBtn.addEventListener('mouseleave', () => {
        yesBtn.style.transform = 'scale(1)';
      });

      yesBtn.addEventListener('click', () => {
        this.handleYesClick();
      });
    }
  },

  handleYesClick() {
    // Hide question content
    const questionContent = document.getElementById('questionContent');
    if (questionContent) {
      questionContent.style.display = 'none';
    }

    // Hide the No button
    const noBtn = document.getElementById('noBtn');
    if (noBtn) {
      noBtn.style.display = 'none';
    }

    // Remove any lingering dodge message
    if (this.activeMessage) {
      this.activeMessage.remove();
      this.activeMessage = null;
    }

    // Show success state
    const successState = document.getElementById('successState');
    if (successState) {
      successState.classList.add('show');
    }

    // Trigger confetti
    ValentineUtils.celebrate();

    // Send response to backend
    this.sendResponse();
  },

  sendResponse() {
    const uniqueId = window.VALENTINE_ID;
    const apiBase = window.API_BASE || '../api';

    if (!uniqueId) {
      console.log('No valentine ID — skipping API call');
      return;
    }

    fetch(apiBase + '/respond.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ uniqueId: uniqueId })
    })
      .then(res => res.json())
      .then(data => {
        console.log('Response recorded:', data);
      })
      .catch(err => {
        console.error('Failed to send response:', err);
      });
  }
};


// ================================================
// INITIALIZE
// ================================================
document.addEventListener('DOMContentLoaded', function () {
  // Creator page
  if (document.getElementById('valentineCreatorForm')) {
    FreeTierCreator.init();
  }

  // Question/view page
  if (document.getElementById('questionContent')) {
    FreeTierQuestion.init();
  }
});
