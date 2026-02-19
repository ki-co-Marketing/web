/**
 * Urban CMS — Initiatives Archive Live Filtering
 */

/* global urbanCMS */

(function () {
  'use strict';

  if (typeof urbanCMS === 'undefined') return;

  const searchInput = document.getElementById('initiative-search');
  const typeSelect  = document.getElementById('initiative-type-select');
  const resultsArea = document.getElementById('initiatives-results');
  const countEl     = document.getElementById('initiatives-count');
  const loadingEl   = document.getElementById('initiatives-loading');
  const clearBtn    = document.getElementById('initiatives-clear');
  const statusBtns  = document.querySelectorAll('[data-filter="status"]');

  if (!searchInput || !resultsArea) return;

  let activeStatus  = '';
  let activeType    = '';
  let activeSearch  = '';
  let debounce      = null;

  /* =====================================================
     Helpers
     ===================================================== */
  function hasActiveFilters() {
    return activeStatus !== '' || activeType !== '' || activeSearch !== '';
  }

  function syncClearButton() {
    if (clearBtn) clearBtn.hidden = !hasActiveFilters();
  }

  function syncStatusButtons() {
    statusBtns.forEach((btn) => {
      const on = btn.dataset.value === activeStatus;
      btn.classList.toggle('filter-chip--active', on);
      btn.setAttribute('aria-pressed', String(on));
    });
  }

  /* =====================================================
     AJAX fetch
     ===================================================== */
  function fetchInitiatives() {
    if (loadingEl) loadingEl.hidden = false;
    resultsArea.setAttribute('aria-busy', 'true');

    const body = new FormData();
    body.append('action', 'urban_filter_initiatives');
    body.append('nonce',  urbanCMS.nonce);
    body.append('status', activeStatus);
    body.append('type',   activeType);
    body.append('search', activeSearch);

    fetch(urbanCMS.ajaxUrl, { method: 'POST', body })
      .then((r) => r.json())
      .then((data) => {
        if (!data.success) return;

        // Replace the existing grid (or entire inner content)
        const grid = document.getElementById('initiatives-grid');
        const tpl  = document.createElement('template');
        tpl.innerHTML = data.data.html;
        const newNode = tpl.content.firstElementChild;

        if (grid && newNode) {
          grid.replaceWith(newNode);
        } else {
          // Clear old content except loading indicator, insert new
          Array.from(resultsArea.children).forEach((child) => {
            if (child !== loadingEl) child.remove();
          });
          if (newNode) resultsArea.appendChild(newNode);
        }

        // Update count
        if (countEl) {
          const n = data.data.found;
          countEl.textContent = n + ' initiative' + (n !== 1 ? 's' : '');
        }
      })
      .catch(() => {})
      .finally(() => {
        if (loadingEl) loadingEl.hidden = true;
        resultsArea.setAttribute('aria-busy', 'false');
      });
  }

  /* =====================================================
     Event listeners
     ===================================================== */

  // Keyword search — debounced 350 ms
  searchInput.addEventListener('input', () => {
    activeSearch = searchInput.value.trim();
    syncClearButton();
    clearTimeout(debounce);
    debounce = setTimeout(fetchInitiatives, 350);
  });

  // Status chip buttons
  statusBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      activeStatus = btn.dataset.value;
      syncStatusButtons();
      syncClearButton();
      fetchInitiatives();
    });
  });

  // Type dropdown
  if (typeSelect) {
    typeSelect.addEventListener('change', () => {
      activeType = typeSelect.value;
      syncClearButton();
      fetchInitiatives();
    });
  }

  // Clear all
  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      activeStatus      = '';
      activeType        = '';
      activeSearch      = '';
      searchInput.value = '';
      if (typeSelect) typeSelect.value = '';
      syncStatusButtons();
      syncClearButton();
      fetchInitiatives();
    });
  }

})();
