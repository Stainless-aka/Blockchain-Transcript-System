/**
 * Blockchain Transcript Verification System
 * Main JavaScript Application
 */

(function () {
    'use strict';

    // ── Sidebar Toggle ──────────────────────────────────────────────────────────
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar       = document.getElementById('sidebar');
    const mainContent   = document.getElementById('mainContent');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                // Mobile: slide in/out
                sidebar.classList.toggle('show');

                // Backdrop
                let backdrop = document.querySelector('.sidebar-backdrop');
                if (sidebar.classList.contains('show')) {
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.className = 'sidebar-backdrop';
                        document.body.appendChild(backdrop);
                    }
                    backdrop.addEventListener('click', function () {
                        sidebar.classList.remove('show');
                        backdrop.remove();
                    });
                } else {
                    if (backdrop) backdrop.remove();
                }
            } else {
                // Desktop: collapse/expand
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('sidebar-collapsed');
            }
        });
    }

    // ── Dark Mode Toggle ────────────────────────────────────────────────────────
    const darkModeToggle = document.getElementById('darkModeToggle');
    const htmlRoot       = document.getElementById('htmlRoot');

    // Restore saved preference
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (htmlRoot) {
        htmlRoot.setAttribute('data-bs-theme', savedTheme);
    }
    if (darkModeToggle) {
        darkModeToggle.checked = savedTheme === 'dark';

        darkModeToggle.addEventListener('change', function () {
            const theme = this.checked ? 'dark' : 'light';
            if (htmlRoot) htmlRoot.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
        });
    }

    // ── Auto-dismiss Flash Alerts ───────────────────────────────────────────────
    const flashAlerts = document.querySelectorAll('.alert.alert-success, .alert.alert-info');
    flashAlerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

    // ── Confirm Delete (data-confirm attribute) ─────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    // ── Copy to Clipboard ───────────────────────────────────────────────────────
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = document.querySelector(this.getAttribute('data-copy'));
            if (target) {
                navigator.clipboard.writeText(target.value || target.textContent)
                    .then(() => {
                        const original = btn.innerHTML;
                        btn.innerHTML = '<i class="bi bi-check text-success"></i>';
                        setTimeout(() => { btn.innerHTML = original; }, 1500);
                    });
            }
        });
    });

    // ── GPA CGPA Auto-Format ────────────────────────────────────────────────────
    document.querySelectorAll('input[name="gpa"], input[name="cgpa"]').forEach(function (input) {
        input.addEventListener('blur', function () {
            const val = parseFloat(this.value);
            if (!isNaN(val)) {
                this.value = Math.min(5, Math.max(0, val)).toFixed(2);
            }
        });
    });

    // ── Form Validation (HTML5 Bootstrap visual feedback) ──────────────────────
    document.querySelectorAll('form[novalidate]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // ── Tooltip Initialization ──────────────────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    // ── Hash Truncation (hover to expand) ──────────────────────────────────────
    document.querySelectorAll('.hash-truncate').forEach(function (el) {
        const full = el.getAttribute('data-full-hash');
        if (full) {
            el.style.cursor = 'pointer';
            el.title = 'Click to copy: ' + full;
            el.addEventListener('click', function () {
                navigator.clipboard.writeText(full).then(() => {
                    el.classList.add('text-success');
                    setTimeout(() => el.classList.remove('text-success'), 1000);
                });
            });
        }
    });

    // ── Active nav link (highlight current page) ────────────────────────────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar .nav-link').forEach(function (link) {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href) && href.length > 1) {
            link.classList.add('active');
        }
    });

})();
