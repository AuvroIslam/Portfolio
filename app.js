// Scroll progress indicator
const createScrollProgress = () => {
  const progressContainer = document.createElement('div');
  progressContainer.className = 'scroll-progress';
  const progressBar = document.createElement('div');
  progressBar.className = 'scroll-progress-bar';
  progressContainer.appendChild(progressBar);
  document.body.appendChild(progressContainer);
  
  const updateProgress = () => {
    const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    progressBar.style.width = Math.min(scrolled, 100) + '%';
  };
  
  window.addEventListener('scroll', updateProgress);
  updateProgress();
};

// Enhanced theme toggle with localStorage and animation
const themeToggle = document.getElementById('themeToggle');
const userPref = localStorage.getItem('theme') || 'dark';
document.body.classList.toggle('light', userPref === 'light');

const updateIcon = () => {
  const isLight = document.body.classList.contains('light');
  const icon = themeToggle.querySelector('.icon');
  icon.textContent = isLight ? '☀️' : '🌙';
  
  // Add animation class for icon change
  icon.style.transform = 'scale(0.8)';
  setTimeout(() => {
    icon.style.transform = 'scale(1)';
  }, 150);
};

updateIcon();

themeToggle.addEventListener('click', () => {
  document.body.classList.toggle('light');
  const theme = document.body.classList.contains('light') ? 'light' : 'dark';
  localStorage.setItem('theme', theme);
  updateIcon();
});

// Header scroll effect
const header = document.querySelector('.site-header');
let lastScrollTop = 0;

const handleScroll = () => {
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  
  if (scrollTop > 100) {
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }
  
  lastScrollTop = scrollTop;
};

window.addEventListener('scroll', handleScroll);

// Enhanced mobile menu with animations and touch support
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');
let menuOpen = false;

const toggleMenu = () => {
  menuOpen = !menuOpen;
  navLinks.classList.toggle('open', menuOpen);
  menuToggle.classList.toggle('active', menuOpen);
  menuToggle.setAttribute('aria-expanded', menuOpen ? 'true' : 'false');
  
  // Prevent body scroll when menu is open on mobile
  if (menuOpen) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
};

const closeMenu = () => {
  if (menuOpen) {
    menuOpen = false;
    navLinks.classList.remove('open');
    menuToggle.classList.remove('active');
    menuToggle.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }
};

menuToggle.addEventListener('click', (e) => {
  e.stopPropagation();
  toggleMenu();
});

// Close menu when clicking outside or on a nav link
document.addEventListener('click', (e) => {
  if (!navLinks.contains(e.target) && !menuToggle.contains(e.target)) {
    closeMenu();
  }
});

// Close menu when clicking on nav links
navLinks.addEventListener('click', (e) => {
  if (e.target.classList.contains('nav-link')) {
    closeMenu();
  }
});

// Handle resize events
let resizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    // Close mobile menu on desktop resize
    if (window.innerWidth > 900) {
      closeMenu();
    }
  }, 250);
});

// Handle escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && menuOpen) {
    closeMenu();
  }
});

// Smooth scroll for internal links with improved mobile handling
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const id = a.getAttribute('href');
    if (id.length > 1) {
      e.preventDefault();
      const target = document.querySelector(id);
      if (target) {
        // Close mobile menu first
        closeMenu();
        
        // Add a small delay for mobile menu animation
        setTimeout(() => {
          target.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start',
            inline: 'nearest'
          });
        }, menuOpen ? 300 : 0);
      }
    }
  });
});

// Enhanced scrollspy with smooth transitions
const sections = [...document.querySelectorAll('section[id]')];
const links = [...document.querySelectorAll('.nav-link')];

const spy = () => {
  const pos = window.scrollY + 120;
  let current = sections[0].id;
  
  for (const s of sections) {
    if (pos >= s.offsetTop) current = s.id;
  }
  
  links.forEach(l => {
    const isActive = l.getAttribute('href') === '#' + current;
    l.classList.toggle('active', isActive);
  });
};

window.addEventListener('scroll', spy);
spy();

// Enhanced reveal on scroll with staggered animations
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry, index) => {
    if (entry.isIntersecting) {
      setTimeout(() => {
        entry.target.classList.add('visible');
      }, index * 100); // Stagger the animations
    }
  });
}, { 
  threshold: 0.12,
  rootMargin: '-50px'
});

document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

// Skills progress bars animation
const skillsObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const progressBars = entry.target.querySelectorAll('.skill-progress');
      progressBars.forEach(bar => {
        const width = bar.getAttribute('data-width');
        bar.style.setProperty('--target-width', width);
        bar.style.width = width;
      });
    }
  });
}, { threshold: 0.5 });

document.querySelectorAll('.skill-category').forEach(el => skillsObserver.observe(el));

// Project filtering functionality
const filterButtons = document.querySelectorAll('.filter-btn');
const projectCards = document.querySelectorAll('.project-card');

filterButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    // Update active button
    filterButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    const filter = btn.getAttribute('data-filter');
    
    // Filter projects with animation
    projectCards.forEach((card, index) => {
      const category = card.getAttribute('data-category');
      const shouldShow = filter === 'all' || category === filter;
      
      setTimeout(() => {
        if (shouldShow) {
          card.style.display = 'grid';
          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, 50);
        } else {
          card.style.opacity = '0';
          card.style.transform = 'translateY(20px)';
          setTimeout(() => {
            card.style.display = 'none';
          }, 300);
        }
      }, index * 50);
    });
  });
});

// Testimonials slider
const testimonialTrack = document.getElementById('testimonialTrack');
const dots = document.querySelectorAll('.dot');
let currentSlide = 0;

const updateSlider = (slideIndex) => {
  testimonialTrack.style.transform = `translateX(-${slideIndex * 100}%)`;
  
  dots.forEach((dot, index) => {
    dot.classList.toggle('active', index === slideIndex);
  });
  
  currentSlide = slideIndex;
};

dots.forEach((dot, index) => {
  dot.addEventListener('click', () => {
    updateSlider(index);
  });
});

// Auto-advance testimonials
setInterval(() => {
  const nextSlide = (currentSlide + 1) % dots.length;
  updateSlider(nextSlide);
}, 5000);

// Typing animation for hero text
const typeWriter = (element, text, speed = 100) => {
  let i = 0;
  element.textContent = '';
  
  const timer = setInterval(() => {
    if (i < text.length) {
      element.textContent += text.charAt(i);
      i++;
    } else {
      clearInterval(timer);
    }
  }, speed);
};

// Initialize typing animation when page loads
window.addEventListener('load', () => {
  const heroName = document.querySelector('.gradient');
  if (heroName) {
    const originalText = heroName.textContent;
    typeWriter(heroName, originalText, 120);
  }
});

// Animated counter for stats
const animateCounters = () => {
  const counters = [
    { element: document.getElementById('yearsExp'), target: 3, suffix: '+' },
    { element: document.getElementById('projectsShipped'), target: 20, suffix: '+' },
    { element: document.getElementById('oss'), target: 2, suffix: '+' }
  ];
  
  counters.forEach(({ element, target, suffix }) => {
    if (!element) return;
    
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      element.textContent = Math.floor(current) + suffix;
    }, 50);
  });
};

// Initialize counters when stats section is visible
const statsObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      animateCounters();
      statsObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.5 });

const statsSection = document.querySelector('.stats');
if (statsSection) {
  statsObserver.observe(statsSection);
}

// Initialize scroll progress and other features
document.addEventListener('DOMContentLoaded', () => {
  createScrollProgress();
  
  // Scroll to top button
  const scrollTopBtn = document.getElementById('scrollTopBtn');
  
  const toggleScrollTopBtn = () => {
    if (window.scrollY > 300) {
      scrollTopBtn.classList.add('visible');
    } else {
      scrollTopBtn.classList.remove('visible');
    }
  };
  
  window.addEventListener('scroll', toggleScrollTopBtn);
  
  scrollTopBtn.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
  
  // Smooth scroll for all anchor links
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const id = a.getAttribute('href');
      if (id.length > 1) {
        e.preventDefault();
        const target = document.querySelector(id);
        if (target) {
          target.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start',
            inline: 'nearest'
          });
          navLinks.classList.remove('open'); // close mobile menu
          menuToggle.setAttribute('aria-expanded', 'false');
        }
      }
    });
  });
  
  // Set current year in footer
  const yearElement = document.getElementById('year');
  if (yearElement) {
    yearElement.textContent = new Date().getFullYear();
  }
  
  // Add loading animation to images
  const images = document.querySelectorAll('img');
  images.forEach(img => {
    img.addEventListener('load', () => {
      img.style.opacity = '1';
    });
    img.style.opacity = '0';
    img.style.transition = 'opacity 0.3s ease';
  });
  
  // Parallax effect for floating shapes (disabled on mobile for performance)
  const shapes = document.querySelectorAll('.shape');
  const enableParallax = window.innerWidth > 768 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  
  if (enableParallax) {
    window.addEventListener('scroll', () => {
      const scrolled = window.pageYOffset;
      shapes.forEach((shape, index) => {
        const speed = (index + 1) * 0.1;
        shape.style.transform = `translateY(${scrolled * speed}px)`;
      });
    });
  }
  
  // Touch and gesture support for better mobile experience
  let touchStartY = 0;
  let touchEndY = 0;
  
  document.addEventListener('touchstart', e => {
    touchStartY = e.changedTouches[0].screenY;
  });
  
  document.addEventListener('touchend', e => {
    touchEndY = e.changedTouches[0].screenY;
    handleSwipe();
  });
  
  function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartY - touchEndY;
    
    // Close mobile menu on upward swipe
    if (diff > swipeThreshold && menuOpen) {
      closeMenu();
    }
  }
  
  // Improved viewport detection for better mobile layout
  const setViewportHeight = () => {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  };
  
  window.addEventListener('resize', setViewportHeight);
  window.addEventListener('orientationchange', setViewportHeight);
  setViewportHeight();
  
  // Performance optimization: debounce scroll events on mobile
  let ticking = false;
  
  const optimizedScroll = (callback) => {
    return (...args) => {
      if (!ticking) {
        requestAnimationFrame(() => {
          callback.apply(this, args);
          ticking = false;
        });
        ticking = true;
      }
    };
  };
  
  // Apply optimized scroll to existing scroll handlers
  const scrollHandlers = [handleScroll];
  scrollHandlers.forEach(handler => {
    window.removeEventListener('scroll', handler);
    window.addEventListener('scroll', optimizedScroll(handler), { passive: true });
  });
  
  // Lazy loading for images (basic implementation)
  const lazyImages = document.querySelectorAll('img[data-src]');
  
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.classList.remove('lazy');
        imageObserver.unobserve(img);
      }
    });
  });
  
  lazyImages.forEach(img => imageObserver.observe(img));
  
  // Accessible focus management
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
      document.body.classList.add('using-keyboard');
    }
  });
  
  document.addEventListener('mousedown', () => {
    document.body.classList.remove('using-keyboard');
  });

  // Contact form handling with enhanced validation and mobile UX
  const form = document.getElementById('contactForm');
  const formStatus = document.getElementById('formStatus');
  
  if (form) {
    // Real-time validation
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
      input.addEventListener('blur', validateField);
      input.addEventListener('input', clearError);
    });
    
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      // Validate all fields
      let isValid = true;
      inputs.forEach(input => {
        if (!validateField({ target: input })) {
          isValid = false;
        }
      });
      
      if (!isValid) {
        showFormStatus('Please fix the errors above.', 'error');
        return;
      }
      
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      
      try {
        // Show loading state
        submitBtn.innerHTML = '<span>Sending...</span><i class="fas fa-spinner fa-spin"></i>';
        submitBtn.disabled = true;
        
        const formData = new FormData(form);
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: {
            'Accept': 'application/json'
          }
        });
        
        if (response.ok) {
          showFormStatus('Thank you! Your message has been sent successfully.', 'success');
          form.reset();
          clearAllErrors();
          
          // Auto-hide success message after 5 seconds
          setTimeout(() => {
            formStatus.style.display = 'none';
          }, 5000);
        } else {
          throw new Error('Network response was not ok');
        }
      } catch (error) {
        console.error('Form submission error:', error);
        showFormStatus('Oops! There was a problem sending your message. Please try again.', 'error');
      } finally {
        // Restore button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    });
  }
  
  function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    const fieldName = field.name;
    let isValid = true;
    let errorMessage = '';
    
    // Clear previous error
    clearError(e);
    
    // Required field check
    if (!value) {
      errorMessage = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} is required.`;
      isValid = false;
    } else {
      // Specific validation
      switch (fieldName) {
        case 'name':
          if (value.length < 2) {
            errorMessage = 'Name must be at least 2 characters long.';
            isValid = false;
          } else if (!/^[a-zA-Z\s\-']+$/.test(value)) {
            errorMessage = 'Name can only contain letters, spaces, hyphens, and apostrophes.';
            isValid = false;
          }
          break;
          
        case 'email':
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(value)) {
            errorMessage = 'Please enter a valid email address.';
            isValid = false;
          }
          break;
          
        case 'message':
          if (value.length < 10) {
            errorMessage = 'Message must be at least 10 characters long.';
            isValid = false;
          } else if (value.length > 1000) {
            errorMessage = 'Message must be less than 1000 characters.';
            isValid = false;
          }
          break;
      }
    }
    
    if (!isValid) {
      showError(field, errorMessage);
    }
    
    return isValid;
  }
  
  function showError(field, message) {
    const errorElement = field.parentElement.querySelector('.error');
    if (errorElement) {
      errorElement.textContent = message;
      errorElement.classList.add('show');
    }
    field.setAttribute('aria-invalid', 'true');
    field.setAttribute('aria-describedby', errorElement?.id || '');
  }
  
  function clearError(e) {
    const field = e.target;
    const errorElement = field.parentElement.querySelector('.error');
    if (errorElement) {
      errorElement.textContent = '';
      errorElement.classList.remove('show');
    }
    field.removeAttribute('aria-invalid');
    field.removeAttribute('aria-describedby');
  }
  
  function clearAllErrors() {
    const errors = form.querySelectorAll('.error');
    errors.forEach(error => {
      error.textContent = '';
      error.classList.remove('show');
    });
    
    const fields = form.querySelectorAll('input, textarea');
    fields.forEach(field => {
      field.removeAttribute('aria-invalid');
      field.removeAttribute('aria-describedby');
    });
  }
  
  function showFormStatus(message, type) {
    formStatus.textContent = message;
    formStatus.className = `form-status ${type}`;
    formStatus.style.display = 'block';
    
    // Scroll to status message on mobile
    if (window.innerWidth <= 768) {
      formStatus.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Auto-hide error messages after 10 seconds
    if (type === 'error') {
      setTimeout(() => {
        formStatus.style.display = 'none';
      }, 10000);
    }
  }
});
