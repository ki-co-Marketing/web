/**
 * Urban CMS — Newsletter Signup JS
 */

/* global urbanNewsletter */

(function () {
  'use strict';

  if (typeof urbanNewsletter === 'undefined') return;

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.newsletter-form').forEach(initNewsletterForm);
  });

  function initNewsletterForm(form) {
    const submitBtn  = form.querySelector('.newsletter-form__submit');
    const successEl  = form.querySelector('.newsletter-form__success');
    const errorEl    = form.querySelector('.newsletter-form__error');
    const emailInput = form.querySelector('input[name="email"]');

    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      // Clear prior errors
      if (errorEl) { errorEl.textContent = ''; errorEl.hidden = true; }

      const email = emailInput?.value.trim();
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        if (errorEl) { errorEl.textContent = 'Please enter a valid email address.'; errorEl.hidden = false; }
        emailInput?.focus();
        return;
      }

      setLoading(submitBtn, true);

      const body = new FormData(form);
      body.set('action', 'urban_newsletter_subscribe');
      body.set('nonce',  urbanNewsletter.nonce);
      body.set('lists',  form.dataset.list   || 'general');
      body.set('source', form.dataset.source || 'website');

      try {
        const res  = await fetch(urbanNewsletter.ajaxUrl, { method: 'POST', body });
        const data = await res.json();

        if (data.success) {
          form.querySelector('.newsletter-form__fields').hidden = true;
          if (successEl) successEl.hidden = false;
        } else {
          const msg = data.data?.message || 'Something went wrong. Please try again.';
          if (errorEl) { errorEl.textContent = msg; errorEl.hidden = false; }
          setLoading(submitBtn, false);
        }
      } catch {
        if (errorEl) { errorEl.textContent = 'Something went wrong. Please try again.'; errorEl.hidden = false; }
        setLoading(submitBtn, false);
      }
    });
  }

  function setLoading(btn, isLoading) {
    if (!btn) return;
    btn.disabled = isLoading;
    const label   = btn.querySelector('.btn-label');
    const loading = btn.querySelector('.btn-loading');
    if (label)   label.hidden   = isLoading;
    if (loading) loading.hidden = !isLoading;
  }

})();
