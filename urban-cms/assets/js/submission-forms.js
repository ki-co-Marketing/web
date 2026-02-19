/**
 * Urban CMS — Public Submission Forms JS
 * Handles business listing & event submission via AJAX with
 * client-side validation and image previews.
 */

/* global urbanForms */

(function () {
  'use strict';

  if (typeof urbanForms === 'undefined') return;

  const { ajaxUrl, bizNonce, eventNonce, maxFileSize, strings } = urbanForms;

  /* =====================================================
     Shared Utilities
     ===================================================== */

  function showError(el, msg) {
    if (!el) return;
    el.textContent = msg;
    el.hidden = false;
  }

  function clearError(el) {
    if (!el) return;
    el.textContent = '';
    el.hidden = true;
  }

  function clearAllErrors(form) {
    form.querySelectorAll('.form-error').forEach((el) => clearError(el));
    const banner = form.querySelector('.form-message--error');
    if (banner) { banner.textContent = ''; banner.hidden = true; }
  }

  function setLoading(btn, isLoading) {
    const label   = btn.querySelector('.btn-label');
    const loading = btn.querySelector('.btn-loading');
    btn.disabled = isLoading;
    if (label)   label.hidden = isLoading;
    if (loading) loading.hidden = !isLoading;
  }

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function validateFile(file) {
    if (!file) return null;
    const allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!allowed.includes(file.type)) return strings.error_file;
    if (file.size > maxFileSize)      return strings.error_file;
    return null;
  }

  /* =====================================================
     Image Preview Handler
     ===================================================== */
  function initFilePreview(inputId, previewId, previewImgId) {
    const input    = document.getElementById(inputId);
    const preview  = document.getElementById(previewId);
    const previewImg = document.getElementById(previewImgId);
    if (!input || !preview || !previewImg) return;

    input.addEventListener('change', () => {
      const file  = input.files[0];
      const error = document.getElementById(inputId + '-error');
      clearError(error);

      if (!file) { preview.hidden = true; return; }

      const fileErr = validateFile(file);
      if (fileErr) {
        showError(error, fileErr);
        input.value = '';
        preview.hidden = true;
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        previewImg.src = e.target.result;
        preview.hidden = false;
      };
      reader.readAsDataURL(file);
    });

    // Remove button
    preview.querySelector('.file-preview__remove')?.addEventListener('click', () => {
      input.value    = '';
      previewImg.src = '';
      preview.hidden = true;
    });
  }

  /* =====================================================
     Generic Form Submitter
     ===================================================== */
  function setupForm(formId, action, nonce, successId) {
    const form    = document.getElementById(formId);
    if (!form) return;

    const submitBtn = form.querySelector('[type="submit"]');
    const successEl = document.getElementById(successId);
    const errorBanner = form.querySelector('.form-message--error');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearAllErrors(form);

      // Client-side required field validation
      let hasErrors = false;

      form.querySelectorAll('[required]').forEach((field) => {
        const errEl = document.getElementById(field.id + '-error');
        if (!field.value.trim()) {
          showError(errEl, field.getAttribute('aria-errormessage') || 'This field is required.');
          hasErrors = true;
        } else if (field.type === 'email' && !isValidEmail(field.value)) {
          showError(errEl, 'Please enter a valid email address.');
          hasErrors = true;
        } else if (field.type === 'checkbox' && !field.checked) {
          showError(errEl, 'You must agree to continue.');
          hasErrors = true;
        }
      });

      // Validate file inputs
      form.querySelectorAll('input[type="file"]').forEach((fileInput) => {
        if (fileInput.files[0]) {
          const fileErr = validateFile(fileInput.files[0]);
          if (fileErr) {
            showError(document.getElementById(fileInput.id + '-error'), fileErr);
            hasErrors = true;
          }
        }
      });

      if (hasErrors) {
        // Scroll to first error
        const firstErr = form.querySelector('.form-error:not([hidden])');
        firstErr?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      setLoading(submitBtn, true);

      const formData = new FormData(form);
      formData.set('action', action);
      formData.set('nonce',  nonce);

      try {
        const res  = await fetch(ajaxUrl, { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
          form.hidden       = true;
          successEl.hidden  = false;
          successEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
          const code = data.data?.code;
          const msg  = code === 'rate_limit'
            ? strings.error_rate
            : (data.data?.message || strings.error_generic);

          if (errorBanner) {
            errorBanner.textContent = msg;
            errorBanner.hidden      = false;
            errorBanner.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          setLoading(submitBtn, false);
        }
      } catch {
        if (errorBanner) {
          errorBanner.textContent = strings.error_generic;
          errorBanner.hidden      = false;
        }
        setLoading(submitBtn, false);
      }
    });
  }

  /* =====================================================
     End-date must be >= start-date
     ===================================================== */
  function initDateValidation(startId, endId) {
    const startInput = document.getElementById(startId);
    const endInput   = document.getElementById(endId);
    if (!startInput || !endInput) return;

    startInput.addEventListener('change', () => {
      if (endInput.value && endInput.value < startInput.value) {
        endInput.value = startInput.value;
      }
      endInput.min = startInput.value;
    });
  }

  /* =====================================================
     Initialise both forms
     ===================================================== */
  document.addEventListener('DOMContentLoaded', () => {

    // Business form
    initFilePreview('biz-logo',    'biz-logo-preview',    'biz-logo-preview-img');
    setupForm('biz-submit-form',   'urban_submit_business', bizNonce,   'biz-success');

    // Event form
    initFilePreview('event-image', 'event-image-preview', 'event-image-preview-img');
    setupForm('event-submit-form', 'urban_submit_event',   eventNonce, 'event-success');
    initDateValidation('event-start-date', 'event-end-date');
  });

})();
