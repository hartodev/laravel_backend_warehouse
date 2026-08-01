/* ======================================================
   StockFlow — Premium WMS Landing Page
   script.js — All Animations & Interactions
   ====================================================== */

'use strict';

// -------------------------------------------------------
// 1. Initialize Lucide Icons
// -------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();
  init();
});

function init() {
  initNavbar();
  initNavToggle();
  initSmoothScroll();
  initScrollReveal();
  initCounters();
  initStatBars();
  initBenefitBars();
  initParallax();
  initFAQ();
  initRipple();
  initBackToTop();
  initActiveNav();
}

// -------------------------------------------------------
// 2. Navbar — Blur + Scroll Effect
// -------------------------------------------------------
function initNavbar() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;

  const onScroll = () => {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  };

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

// -------------------------------------------------------
// 3. Mobile Nav Toggle
// -------------------------------------------------------
function initNavToggle() {
  const toggle = document.getElementById('navToggle');
  const navLinks = document.getElementById('navLinks');
  if (!toggle || !navLinks) return;

  toggle.addEventListener('click', () => {
    const isOpen = navLinks.classList.toggle('open');
    document.body.style.overflow = isOpen ? 'hidden' : '';
    // Swap icon
    const icon = toggle.querySelector('i');
    if (icon) {
      icon.setAttribute('data-lucide', isOpen ? 'x' : 'menu');
      lucide.createIcons();
    }
  });

  // Close when a link is clicked
  navLinks.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('open');
      document.body.style.overflow = '';
      const icon = toggle.querySelector('i');
      if (icon) { icon.setAttribute('data-lucide', 'menu'); lucide.createIcons(); }
    });
  });
}

// -------------------------------------------------------
// 4. Smooth Scroll
// -------------------------------------------------------
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
      const targetId = anchor.getAttribute('href');
      if (targetId === '#') return;
      const target = document.querySelector(targetId);
      if (!target) return;
      e.preventDefault();
      const offset = 80;
      const top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top, behavior: 'smooth' });
    });
  });
}

// -------------------------------------------------------
// 5. Scroll Reveal Animation (Intersection Observer)
// -------------------------------------------------------
function initScrollReveal() {
  const elements = document.querySelectorAll('.fade-up, .fade-left, .fade-right');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Stagger children cards
        const card = entry.target;
        const children = card.querySelectorAll(
          '.problem-card, .feature-card, .bento-card, .benefit-card, .testimonial-card, .workflow-step'
        );
        if (children.length > 0) {
          children.forEach((child, i) => {
            setTimeout(() => {
              child.style.opacity = '1';
              child.style.transform = 'translateY(0)';
            }, i * 80);
          });
        }
        card.classList.add('revealed');
        observer.unobserve(card);
      }
    });
  }, {
    threshold: 0.08,
    rootMargin: '0px 0px -60px 0px'
  });

  elements.forEach(el => {
    // Pre-style staggered children
    const children = el.querySelectorAll(
      '.problem-card, .feature-card, .bento-card, .benefit-card, .testimonial-card, .workflow-step'
    );
    children.forEach(child => {
      child.style.opacity = '0';
      child.style.transform = 'translateY(30px)';
      child.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    observer.observe(el);
  });
}

// -------------------------------------------------------
// 6. Counter Animation
// -------------------------------------------------------
function initCounters() {
  const counters = document.querySelectorAll('.stat-value[data-target], .benefit-num[data-target]');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(counter => observer.observe(counter));
}

function animateCounter(el) {
  const target = parseFloat(el.getAttribute('data-target'));
  const suffix = el.getAttribute('data-suffix') || '';
  const decimal = parseInt(el.getAttribute('data-decimal')) || 0;
  const duration = 2000;
  const start = performance.now();

  const easeOut = t => 1 - Math.pow(1 - t, 3);

  const step = (now) => {
    const progress = Math.min((now - start) / duration, 1);
    const eased = easeOut(progress);
    const current = target * eased;

    if (decimal > 0) {
      el.textContent = current.toFixed(decimal) + suffix;
    } else {
      el.textContent = Math.round(current).toLocaleString('id-ID') + suffix;
    }

    if (progress < 1) {
      requestAnimationFrame(step);
    } else {
      if (decimal > 0) {
        el.textContent = target.toFixed(decimal) + suffix;
      } else {
        el.textContent = target.toLocaleString('id-ID') + suffix;
      }
    }
  };

  requestAnimationFrame(step);
}

// -------------------------------------------------------
// 7. Stat Bars (animate width)
// -------------------------------------------------------
function initStatBars() {
  const bars = document.querySelectorAll('.stat-bar-fill');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const bar = entry.target;
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => { bar.style.width = width; }, 200);
        observer.unobserve(bar);
      }
    });
  }, { threshold: 0.5 });

  bars.forEach(bar => observer.observe(bar));
}

// -------------------------------------------------------
// 8. Benefit Progress Bars
// -------------------------------------------------------
function initBenefitBars() {
  const bars = document.querySelectorAll('.benefit-bar');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const bar = entry.target;
        const targetWidth = bar.style.getPropertyValue('--target-width');
        bar.style.width = '0';
        setTimeout(() => {
          bar.style.width = targetWidth;
          bar.style.transition = 'width 1.8s cubic-bezier(0.4,0,0.2,1)';
        }, 300);
        observer.unobserve(bar);
      }
    });
  }, { threshold: 0.3 });

  bars.forEach(bar => observer.observe(bar));
}

// -------------------------------------------------------
// 9. Mouse Parallax (Hero)
// -------------------------------------------------------
function initParallax() {
  const heroVisual = document.getElementById('heroVisual');
  if (!heroVisual) return;

  let ticking = false;
  let mouseX = 0;
  let mouseY = 0;
  const intensity = 0.012;

  document.addEventListener('mousemove', (e) => {
    mouseX = (e.clientX - window.innerWidth / 2);
    mouseY = (e.clientY - window.innerHeight / 2);
    if (!ticking) {
      requestAnimationFrame(() => {
        const tx = mouseX * intensity;
        const ty = mouseY * intensity;
        heroVisual.style.transform = `perspective(1200px) rotateY(${-tx}deg) rotateX(${ty * 0.5}deg) translateZ(0)`;
        ticking = false;
      });
      ticking = true;
    }
  });

  document.addEventListener('mouseleave', () => {
    heroVisual.style.transform = 'perspective(1200px) rotateY(0deg) rotateX(0deg) translateZ(0)';
  });
}

// -------------------------------------------------------
// 10. FAQ Accordion
// -------------------------------------------------------
function initFAQ() {
  const items = document.querySelectorAll('.faq-item');

  items.forEach(item => {
    const question = item.querySelector('.faq-question');
    const answer   = item.querySelector('.faq-answer');
    if (!question || !answer) return;

    question.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');

      // Close all
      items.forEach(i => {
        i.classList.remove('open');
        const a = i.querySelector('.faq-answer');
        if (a) a.style.maxHeight = '0';
      });

      // Toggle current
      if (!isOpen) {
        item.classList.add('open');
        answer.style.maxHeight = answer.scrollHeight + 'px';
      }
    });
  });
}

// -------------------------------------------------------
// 11. Button Ripple Effect
// -------------------------------------------------------
function initRipple() {
  document.querySelectorAll('.ripple').forEach(btn => {
    btn.addEventListener('click', function (e) {
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height) * 1.5;
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;

      const wave = document.createElement('span');
      wave.className = 'ripple-wave';
      wave.style.cssText = `
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
      `;
      this.appendChild(wave);

      wave.addEventListener('animationend', () => wave.remove());
    });
  });
}

// -------------------------------------------------------
// 12. Back To Top Button
// -------------------------------------------------------
function initBackToTop() {
  const btn = document.getElementById('backToTop');
  if (!btn) return;

  window.addEventListener('scroll', () => {
    if (window.scrollY > 400) {
      btn.classList.add('show');
    } else {
      btn.classList.remove('show');
    }
  }, { passive: true });

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

// -------------------------------------------------------
// 13. Active Nav Link on Scroll
// -------------------------------------------------------
function initActiveNav() {
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.getAttribute('id');
        navLinks.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('href') === '#' + id) {
            link.classList.add('active');
          }
        });
      }
    });
  }, {
    rootMargin: '-40% 0px -55% 0px',
    threshold: 0
  });

  sections.forEach(s => observer.observe(s));
}

// -------------------------------------------------------
// 14. Hero Image Float (CSS-driven, enhanced via JS)
// -------------------------------------------------------
function initHeroFloat() {
  const mockup = document.querySelector('.dashboard-mockup');
  if (!mockup) return;

  let angle = 0;
  const amplitude = 6;

  const float = () => {
    angle += 0.8;
    const y = Math.sin((angle * Math.PI) / 180) * amplitude;
    mockup.style.transform = `translateY(${y}px)`;
    requestAnimationFrame(float);
  };

  requestAnimationFrame(float);
}

// -------------------------------------------------------
// 15. Card Hover Lift (already via CSS, but ensure
//     dynamic glow color reflects accent per card)
// -------------------------------------------------------
function initCardGlow() {
  document.querySelectorAll('.feature-card').forEach(card => {
    card.addEventListener('mouseenter', function(e) {
      const rect = this.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      this.style.setProperty('--mouse-x', x + 'px');
      this.style.setProperty('--mouse-y', y + 'px');
    });
  });
}

// -------------------------------------------------------
// 16. Dashboard Chart Tabs Interaction
// -------------------------------------------------------
function initChartTabs() {
  document.querySelectorAll('.dp-chart-tabs').forEach(tabGroup => {
    const buttons = tabGroup.querySelectorAll('button');
    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      });
    });
  });
}

// -------------------------------------------------------
// 17. Marquee pause on hover (already in CSS, add
//     touch support for mobile)
// -------------------------------------------------------
function initMarquee() {
  const inner = document.querySelector('.marquee-inner');
  if (!inner) return;

  inner.addEventListener('touchstart', () => {
    inner.style.animationPlayState = 'paused';
  });
  inner.addEventListener('touchend', () => {
    inner.style.animationPlayState = 'running';
  });
}

// -------------------------------------------------------
// Initialize additional features after DOM ready
// -------------------------------------------------------
window.addEventListener('load', () => {
  initHeroFloat();
  initCardGlow();
  initChartTabs();
  initMarquee();

  // Trigger bar fills after load
  document.querySelectorAll('.stat-bar-fill').forEach(bar => {
    const originalWidth = bar.style.width;
    if (originalWidth) {
      bar.style.setProperty('width', originalWidth);
    }
  });
});
