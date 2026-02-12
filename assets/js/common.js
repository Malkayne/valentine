// ================================================
// COMMON JAVASCRIPT UTILITIES
// ================================================

const ValentineUtils = {
  // Smooth scroll to element
  smoothScroll(target) {
    const element = document.querySelector(target);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  },

  // Copy text to clipboard
  async copyToClipboard(text) {
    try {
      await navigator.clipboard.writeText(text);
      return true;
    } catch (err) {
      // Fallback for older browsers
      const textArea = document.createElement('textarea');
      textArea.value = text;
      textArea.style.position = 'fixed';
      textArea.style.left = '-999999px';
      document.body.appendChild(textArea);
      textArea.select();
      try {
        document.execCommand('copy');
        document.body.removeChild(textArea);
        return true;
      } catch (err) {
        document.body.removeChild(textArea);
        return false;
      }
    }
  },

  // Show tooltip notification
  showTooltip(message, element, duration = 2000) {
    const tooltip = document.createElement('div');
    tooltip.className = 'playful-message show';
    tooltip.textContent = message;
    
    const rect = element.getBoundingClientRect();
    tooltip.style.top = rect.top - 40 + 'px';
    tooltip.style.left = rect.left + (rect.width / 2) + 'px';
    tooltip.style.transform = 'translateX(-50%)';
    
    document.body.appendChild(tooltip);
    
    setTimeout(() => {
      tooltip.classList.remove('show');
      setTimeout(() => tooltip.remove(), 300);
    }, duration);
  },

  // Generate random string for unique IDs
  generateId(length = 8) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
      result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
  },

  // Validate email
  validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  },

  // Format date
  formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString('en-US', options);
  },

  // Get URL parameters
  getUrlParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  },

  // Show loading spinner
  showLoading(container) {
    const spinner = document.createElement('div');
    spinner.className = 'spinner';
    spinner.id = 'loading-spinner';
    container.appendChild(spinner);
  },

  // Hide loading spinner
  hideLoading() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
      spinner.remove();
    }
  },

  // Debounce function for performance
  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },

  // LocalStorage helpers
  storage: {
    set(key, value) {
      try {
        localStorage.setItem(key, JSON.stringify(value));
        return true;
      } catch (e) {
        console.error('Error saving to localStorage:', e);
        return false;
      }
    },

    get(key) {
      try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : null;
      } catch (e) {
        console.error('Error reading from localStorage:', e);
        return null;
      }
    },

    remove(key) {
      try {
        localStorage.removeItem(key);
        return true;
      } catch (e) {
        console.error('Error removing from localStorage:', e);
        return false;
      }
    },

    clear() {
      try {
        localStorage.clear();
        return true;
      } catch (e) {
        console.error('Error clearing localStorage:', e);
        return false;
      }
    }
  },

  // Image preview handler
  handleImagePreview(input, previewContainer) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
        previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        previewContainer.style.display = 'block';
      };
      
      reader.readAsDataURL(input.files[0]);
    }
  },

  // Random color generator
  randomColor() {
    const colors = [
      '#FF69B4', '#FF1744', '#FFB6C1', '#FFC0CB',
      '#8B4789', '#D4A5D4', '#E91E63', '#9C27B0'
    ];
    return colors[Math.floor(Math.random() * colors.length)];
  },

  // Animate element entrance
  animateEntrance(element, animationClass = 'fade-in') {
    element.classList.add(animationClass);
  },

  // Confetti celebration (requires canvas-confetti library)
  celebrate() {
    if (typeof confetti !== 'undefined') {
      const duration = 3 * 1000;
      const animationEnd = Date.now() + duration;
      const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };

      function randomInRange(min, max) {
        return Math.random() * (max - min) + min;
      }

      const interval = setInterval(function() {
        const timeLeft = animationEnd - Date.now();

        if (timeLeft <= 0) {
          return clearInterval(interval);
        }

        const particleCount = 50 * (timeLeft / duration);

        confetti(Object.assign({}, defaults, {
          particleCount,
          origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 }
        }));
        confetti(Object.assign({}, defaults, {
          particleCount,
          origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 }
        }));
      }, 250);
    }
  },

  // Share functionality
  share: {
    whatsapp(url, text) {
      const message = encodeURIComponent(`${text}\n${url}`);
      window.open(`https://wa.me/?text=${message}`, '_blank');
    },

    facebook(url) {
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
    },

    twitter(url, text) {
      window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank');
    },

    email(url, subject, body) {
      const mailtoLink = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body + '\n\n' + url)}`;
      window.location.href = mailtoLink;
    }
  },

  // Form validation
  validateForm(formData, requiredFields) {
    const errors = [];
    
    requiredFields.forEach(field => {
      if (!formData[field] || formData[field].trim() === '') {
        errors.push(`${field} is required`);
      }
    });

    return {
      isValid: errors.length === 0,
      errors: errors
    };
  },

  // Show error message
  showError(message, container) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger fade-in';
    errorDiv.style.cssText = `
      background: #f8d7da;
      color: #721c24;
      padding: 12px 20px;
      border-radius: 8px;
      margin-bottom: 15px;
      border: 1px solid #f5c6cb;
    `;
    errorDiv.textContent = message;
    
    container.insertBefore(errorDiv, container.firstChild);
    
    setTimeout(() => {
      errorDiv.remove();
    }, 5000);
  },

  // Show success message
  showSuccess(message, container) {
    const successDiv = document.createElement('div');
    successDiv.className = 'alert alert-success fade-in';
    successDiv.style.cssText = `
      background: #d4edda;
      color: #155724;
      padding: 12px 20px;
      border-radius: 8px;
      margin-bottom: 15px;
      border: 1px solid #c3e6cb;
    `;
    successDiv.textContent = message;
    
    container.insertBefore(successDiv, container.firstChild);
    
    setTimeout(() => {
      successDiv.remove();
    }, 5000);
  },

  // Initialize floating hearts background
  initFloatingHearts(container) {
    const hearts = ['❤️', '💕', '💖', '💗', '💝', '💞', '💓'];
    
    for (let i = 0; i < 9; i++) {
      const heart = document.createElement('div');
      heart.className = 'heart';
      heart.textContent = hearts[Math.floor(Math.random() * hearts.length)];
      container.appendChild(heart);
    }
  },

  // Mobile detection
  isMobile() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
  },

  // Initialize AOS (Animate on Scroll) if available
  initAOS() {
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 1000,
        once: true,
        offset: 100
      });
    }
  }
};

// Initialize common features on DOM load
document.addEventListener('DOMContentLoaded', function() {
  // Initialize floating hearts if container exists
  const heartBg = document.querySelector('.heart-bg');
  if (heartBg) {
    ValentineUtils.initFloatingHearts(heartBg);
  }

  // Initialize AOS
  ValentineUtils.initAOS();

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      const target = this.getAttribute('href');
      if (target !== '#') {
        ValentineUtils.smoothScroll(target);
      }
    });
  });
});
