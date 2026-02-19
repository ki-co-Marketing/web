/**
 * Urban CMS Platform — Main JavaScript
 * Handles: mobile nav, live directory search, smooth scroll, misc UI.
 */

/* global urbanCMS */

(function () {
  'use strict';

  /* =====================================================
     Mobile Navigation Toggle
     ===================================================== */
  const navToggle = document.querySelector('.nav-toggle');
  const primaryNav = document.getElementById('primary-nav');

  if (navToggle && primaryNav) {
    navToggle.addEventListener('click', () => {
      const isOpen = primaryNav.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', String(isOpen));
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Close nav on outside click
    document.addEventListener('click', (e) => {
      if (!primaryNav.contains(e.target) && !navToggle.contains(e.target)) {
        primaryNav.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      }
    });

    // Close nav on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && primaryNav.classList.contains('is-open')) {
        primaryNav.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        navToggle.focus();
      }
    });
  }

  /* =====================================================
     Business Directory Live Search (AJAX)
     ===================================================== */
  const searchForm     = document.getElementById('directory-search-form');
  const searchInput    = document.getElementById('biz-search-input');
  const categorySelect = document.getElementById('biz-category-select');
  const resultsArea    = document.getElementById('directory-results');

  if (searchForm && resultsArea && urbanCMS) {
    let debounceTimer = null;

    function fetchBusinesses() {
      const search   = searchInput   ? searchInput.value.trim()   : '';
      const category = categorySelect ? categorySelect.value.trim() : '';

      resultsArea.style.opacity = '0.5';
      resultsArea.setAttribute('aria-busy', 'true');

      const body = new FormData();
      body.append('action',   'urban_search_businesses');
      body.append('nonce',    urbanCMS.nonce);
      body.append('search',   search);
      body.append('category', category);

      fetch(urbanCMS.ajaxUrl, { method: 'POST', body })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            resultsArea.innerHTML = data.data.html;

            const count = document.createElement('p');
            count.className = 'results-count';
            count.style.cssText = 'color:var(--color-text-muted);margin-bottom:var(--space-4)';
            count.textContent = data.data.found + ' business' + (data.data.found !== 1 ? 'es' : '') + ' found';
            resultsArea.prepend(count);
          }
        })
        .catch(() => {
          resultsArea.innerHTML = '<p>An error occurred. Please try again.</p>';
        })
        .finally(() => {
          resultsArea.style.opacity = '1';
          resultsArea.removeAttribute('aria-busy');
        });
    }

    // Debounced search-as-you-type
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchBusinesses, 400);
      });
    }

    if (categorySelect) {
      categorySelect.addEventListener('change', fetchBusinesses);
    }

    searchForm.addEventListener('submit', (e) => {
      e.preventDefault();
      fetchBusinesses();
    });
  }

  /* =====================================================
     Progress Bar Animation (Initiatives)
     ===================================================== */
  const progressBars = document.querySelectorAll('.progress-bar__fill');

  if (progressBars.length && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const bar    = entry.target;
            const target = bar.getAttribute('aria-valuenow') || '0';
            bar.style.width = target + '%';
            observer.unobserve(bar);
          }
        });
      },
      { threshold: 0.3 }
    );

    progressBars.forEach((bar) => {
      bar.style.width = '0%';
      observer.observe(bar);
    });
  }

  /* =====================================================
     District Stats Counter Animation
     ===================================================== */
  const statNumbers = document.querySelectorAll('.stat-item__number');

  if (statNumbers.length && 'IntersectionObserver' in window) {
    const counterObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          const el       = entry.target;
          const raw      = el.textContent.trim();
          const numMatch = raw.match(/[\d,]+/);
          if (!numMatch) return;

          const target  = parseInt(numMatch[0].replace(/,/g, ''), 10);
          const prefix  = raw.slice(0, numMatch.index);
          const suffix  = raw.slice(numMatch.index + numMatch[0].length);
          const dur     = 1600;
          const start   = performance.now();

          function step(now) {
            const elapsed  = now - start;
            const progress = Math.min(elapsed / dur, 1);
            const eased    = 1 - Math.pow(1 - progress, 3);
            const val      = Math.floor(eased * target);
            el.textContent = prefix + val.toLocaleString() + suffix;
            if (progress < 1) requestAnimationFrame(step);
          }

          requestAnimationFrame(step);
          counterObserver.unobserve(el);
        });
      },
      { threshold: 0.5 }
    );

    statNumbers.forEach((el) => counterObserver.observe(el));
  }

  /* =====================================================
     Smooth Scroll for anchor links
     ===================================================== */
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href^="#"]');
    if (!link) return;
    const target = document.querySelector(link.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });

})();
