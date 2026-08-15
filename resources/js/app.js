import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Global loading-state feedback.
 *
 * This app is classic server-rendered Blade — every action is a full page
 * reload, so on a slow mobile connection a tap can sit for seconds with
 * zero visible change. This adds two things everywhere, with no per-view
 * wiring required:
 *
 *   1. A slim top progress bar on any same-tab internal navigation.
 *   2. A spinner + disabled state on the button that submitted a form.
 *
 * Opt out per-form with `data-no-loading`; customize a button's label with
 * `data-loading-text="Uploading…"`.
 */
(function () {
    const bar = document.createElement('div');
    bar.id = 'kg-progress';
    const mount = () => document.body.appendChild(bar);
    if (document.body) mount(); else document.addEventListener('DOMContentLoaded', mount);

    let safetyTimer = null;

    function startProgress() {
        clearTimeout(safetyTimer);
        bar.style.transition = 'none';
        bar.style.width = '0';
        bar.style.opacity = '1';
        void bar.offsetWidth; // force reflow so the transition below animates from 0
        bar.style.transition = 'width 4s cubic-bezier(.1,.6,.2,1), opacity .2s ease-out';
        requestAnimationFrame(() => { bar.style.width = '78%'; });
        // If navigation never actually happens (validation blocked it, the
        // request errored client-side, etc.) don't leave the bar stuck.
        safetyTimer = setTimeout(resetProgress, 8000);
    }

    function resetProgress() {
        clearTimeout(safetyTimer);
        bar.style.transition = 'width .15s ease-out, opacity .2s ease-out';
        bar.style.width = '0';
        bar.style.opacity = '0';
    }

    // Page served from bfcache (browser back/forward) — any bar left over
    // from the previous view of this page is stale.
    window.addEventListener('pageshow', (e) => { if (e.persisted) resetProgress(); });

    function isPlainLeftClick(e) {
        return e.button === 0 && !e.metaKey && !e.ctrlKey && !e.shiftKey && !e.altKey;
    }

    // Same-tab internal link clicks.
    document.addEventListener('click', (e) => {
        if (e.defaultPrevented || !isPlainLeftClick(e)) return;
        const a = e.target.closest('a[href]');
        if (!a || (a.target && a.target !== '_self')) return;
        if (a.hasAttribute('download') || a.dataset.noProgress !== undefined) return;
        const href = a.getAttribute('href');
        if (!href || /^(#|javascript:|mailto:|tel:)/i.test(href)) return;
        try {
            if (new URL(href, window.location.href).origin !== window.location.origin) return;
        } catch { return; }
        startProgress();
    });

    // Form submissions: progress bar + a spinner on the button that was
    // actually pressed. Checking e.defaultPrevented lets an onsubmit="return
    // confirm(...)" that the user cancelled skip the loading state entirely.
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.noLoading !== undefined) return;
        if (e.defaultPrevented) return;

        if (form.dataset.kgSubmitting === '1') {
            e.preventDefault(); // guard against a double tap re-submitting
            return;
        }
        form.dataset.kgSubmitting = '1';
        startProgress();

        const buttons = form.querySelectorAll('button[type=submit], input[type=submit]');
        buttons.forEach((btn) => {
            btn.classList.add('kg-btn-disabled');
            btn.style.pointerEvents = 'none'; // visual-only guard; leaves name/value intact for submission
        });

        const submitter = e.submitter || buttons[0];
        if (submitter && submitter.tagName === 'BUTTON' && !submitter.querySelector('.kg-spinner')) {
            submitter.dataset.kgOriginalHtml = submitter.innerHTML;
            submitter.classList.add('kg-btn-loading');
            submitter.innerHTML = '<span class="kg-spinner"></span>' + (submitter.dataset.loadingText || 'Please wait…');
        }

        // Safety net: restore if the page is somehow still here later (the
        // submit didn't actually navigate away — e.g. it was blocked).
        setTimeout(() => {
            if (!document.body.contains(form)) return;
            form.dataset.kgSubmitting = '';
            buttons.forEach((btn) => {
                btn.classList.remove('kg-btn-disabled');
                btn.style.pointerEvents = '';
            });
            if (submitter && submitter.dataset.kgOriginalHtml) {
                submitter.innerHTML = submitter.dataset.kgOriginalHtml;
                submitter.classList.remove('kg-btn-loading');
                delete submitter.dataset.kgOriginalHtml;
            }
        }, 10000);
    });
})();
