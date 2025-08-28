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

// Enhanced theme toggle with localStorage
const themeToggle = document.getElementById('themeToggle');
const userPref = localStorage.getItem('theme') || 'dark';
document.body.classList.toggle('light', userPref === 'light');

const updateIcon = () => {
  const isLight = document.body.classList.contains('light');
  themeToggle.querySelector('.icon').textContent = isLight ? '🌞' : '🌙';
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

// Enhanced mobile menu with animations
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');

menuToggle.addEventListener('click', () => {
  const open = navLinks.classList.toggle('open');
  menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  
  // Animate hamburger
  const spans = menuToggle.querySelectorAll('span');
  if (open) {
    spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
    spans[1].style.opacity = '0';
    spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
  } else {
    spans.forEach(span => {
      span.style.transform = '';
      span.style.opacity = '';
    });
  }
});

// Close menu when clicking outside
document.addEventListener('click', (e) => {
  if (!menuToggle.contains(e.target) && !navLinks.contains(e.target)) {
    navLinks.classList.remove('open');
    menuToggle.setAttribute('aria-expanded', 'false');
    const spans = menuToggle.querySelectorAll('span');
    spans.forEach(span => {
      span.style.transform = '';
      span.style.opacity = '';
    });
  }
});

// Smooth scroll for internal links
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const id = a.getAttribute('href');
    if (id.length > 1) {
      e.preventDefault();
      document.querySelector(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      navLinks.classList.remove('open'); // close mobile menu
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
    { element: document.getElementById('oss'), target: 50, suffix: '+' }
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
// Enhanced contact form with better validation and animations
const form = document.getElementById('contactForm');
const status = document.getElementById('formStatus');

const showError = (name, msg = '') => {
  const el = document.querySelector(`.error[data-for="${name}"]`);
  if (el) {
    el.textContent = msg;
    el.style.color = '#ff6b6b';
  }
  
  const input = form[name];
  if (input) {
    input.style.borderColor = msg ? '#ff6b6b' : 'var(--border)';
  }
};

const showSuccess = (name) => {
  const input = form[name];
  if (input) {
    input.style.borderColor = 'var(--primary)';
  }
};

// Real-time validation
const validateField = (field) => {
  const value = field.value.trim();
  const name = field.name;
  
  switch (name) {
    case 'name':
      if (!value) {
        showError(name, 'Please enter your name');
        return false;
      } else if (value.length < 2) {
        showError(name, 'Name must be at least 2 characters');
        return false;
      } else {
        showError(name, '');
        showSuccess(name);
        return true;
      }
      
    case 'email':
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!value) {
        showError(name, 'Please enter your email');
        return false;
      } else if (!emailRegex.test(value)) {
        showError(name, 'Please enter a valid email address');
        return false;
      } else {
        showError(name, '');
        showSuccess(name);
        return true;
      }
      
    case 'message':
      if (!value) {
        showError(name, 'Please write a message');
        return false;
      } else if (value.length < 10) {
        showError(name, 'Message must be at least 10 characters');
        return false;
      } else {
        showError(name, '');
        showSuccess(name);
        return true;
      }
      
    default:
      return true;
  }
};

// Add real-time validation listeners
['name', 'email', 'message'].forEach(fieldName => {
  const field = form[fieldName];
  if (field) {
    field.addEventListener('blur', () => validateField(field));
    field.addEventListener('input', () => {
      if (field.value.trim()) {
        validateField(field);
      }
    });
  }
});

form.addEventListener('submit', (e) => {
  e.preventDefault();
  
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  
  // Validate all fields
  const isNameValid = validateField(form.name);
  const isEmailValid = validateField(form.email);
  const isMessageValid = validateField(form.message);
  
  if (!isNameValid || !isEmailValid || !isMessageValid) {
    status.textContent = 'Please fix the errors above';
    status.style.color = '#ff6b6b';
    return;
  }
  
  // Simulate form submission
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
  submitBtn.disabled = true;
  
  setTimeout(() => {
    status.textContent = '✅ Thanks! Your message has been sent successfully.';
    status.style.color = 'var(--primary)';
    form.reset();
    
    // Reset form styling
    ['name', 'email', 'message'].forEach(fieldName => {
      const field = form[fieldName];
      if (field) {
        field.style.borderColor = 'var(--border)';
      }
      showError(fieldName, '');
    });
    
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
    
    setTimeout(() => {
      status.textContent = '';
    }, 5000);
  }, 2000);
});

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
  
  // Parallax effect for floating shapes
  const shapes = document.querySelectorAll('.shape');
  window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    shapes.forEach((shape, index) => {
      const speed = (index + 1) * 0.1;
      shape.style.transform = `translateY(${scrolled * speed}px)`;
    });
  });
});
