/**
 * PWA bootstrap: service worker registration + install prompt UI.
 * Safe for Laravel auth/CSRF — does not alter form or navigation behavior.
 */
(function () {
    'use strict';

    const INSTALL_BTN_ID = 'pwaInstallBtn';
    let deferredPrompt = null;

    function isStandalone() {
        return (
            window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true
        );
    }

    function getInstallButton() {
        return document.getElementById(INSTALL_BTN_ID);
    }

    function hideInstallButton() {
        const button = getInstallButton();
        if (!button) {
            return;
        }
        button.classList.add('d-none');
        button.setAttribute('aria-hidden', 'true');
    }

    function showInstallButton() {
        if (isStandalone() || !deferredPrompt) {
            hideInstallButton();
            return;
        }

        const button = getInstallButton();
        if (!button) {
            return;
        }

        button.classList.remove('d-none');
        button.setAttribute('aria-hidden', 'false');
    }

    async function registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            return;
        }

        // Service workers require a secure context (HTTPS or localhost).
        if (!window.isSecureContext) {
            return;
        }

        try {
            await navigator.serviceWorker.register('/sw.js', {
                scope: '/',
            });
        } catch (error) {
            // Registration failures must not break the authenticated UI.
            if (typeof console !== 'undefined' && console.info) {
                console.info('[PWA] Service worker registration skipped:', error);
            }
        }
    }

    function bindInstallButton() {
        const button = getInstallButton();
        if (!button) {
            return;
        }

        button.addEventListener('click', async () => {
            if (!deferredPrompt) {
                hideInstallButton();
                return;
            }

            deferredPrompt.prompt();
            const choice = await deferredPrompt.userChoice;
            deferredPrompt = null;
            hideInstallButton();

            if (typeof console !== 'undefined' && console.info) {
                console.info('[PWA] Install choice:', choice.outcome);
            }
        });
    }

    function bindInstallEvents() {
        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredPrompt = event;
            showInstallButton();
        });

        window.addEventListener('appinstalled', () => {
            deferredPrompt = null;
            hideInstallButton();
        });

        window.matchMedia('(display-mode: standalone)').addEventListener('change', (event) => {
            if (event.matches) {
                hideInstallButton();
            }
        });
    }

    function init() {
        if (isStandalone()) {
            hideInstallButton();
        } else {
            hideInstallButton();
            bindInstallButton();
            bindInstallEvents();
        }

        registerServiceWorker();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
