/**
 * StudentHub Unified Client Application Logic
 * Dark Mode, Keyboard Shortcuts, Page Search, Mobile Navigation
 */
(() => {
  'use strict';

  // --- Theme Toggle Logic ---
  const themeBtn = document.getElementById('themeToggleBtn');
  const sunIcon = document.querySelector('.theme-icon-sun');
  const moonIcon = document.querySelector('.theme-icon-moon');

  function updateThemeUI(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    if (theme === 'dark') {
      document.documentElement.classList.add('dark');
      if (sunIcon) sunIcon.style.display = 'none';
      if (moonIcon) moonIcon.style.display = 'block';
    } else {
      document.documentElement.classList.remove('dark');
      if (sunIcon) sunIcon.style.display = 'block';
      if (moonIcon) moonIcon.style.display = 'none';
    }
  }

  const initialTheme = document.documentElement.getAttribute('data-theme') || 'light';
  updateThemeUI(initialTheme);

  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      const target = current === 'dark' ? 'light' : 'dark';
      localStorage.setItem('studenthub_theme', target);
      updateThemeUI(target);
    });
  }

  // --- Mobile Navigation Drawer & Scrim ---
  const sidebar = document.getElementById('sidebar');
  const scrim = document.getElementById('mobileScrim');
  const menuToggle = document.getElementById('menuToggle');
  const moreBtn = document.getElementById('mobileMoreBtn');

  function openSidebar() {
    if (sidebar) sidebar.classList.add('open');
    if (scrim) scrim.classList.add('show');
    if (menuToggle) menuToggle.setAttribute('aria-expanded', 'true');
  }

  function closeSidebar() {
    if (sidebar) sidebar.classList.remove('open');
    if (scrim) scrim.classList.remove('show');
    if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
  }

  if (menuToggle) {
    menuToggle.addEventListener('click', () => {
      if (sidebar && sidebar.classList.contains('open')) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });
  }

  if (moreBtn) {
    moreBtn.addEventListener('click', () => {
      openSidebar();
    });
  }

  if (scrim) {
    scrim.addEventListener('click', closeSidebar);
  }

  // --- Working Page Search (Cmd+K) ---
  const searchInput = document.getElementById('pageSearch');

  function filterPageContent(query) {
    const q = query.trim().toLowerCase();
    const searchableItems = document.querySelectorAll('[data-search]');
    const tableRows = document.querySelectorAll('.data-table tbody tr, .admin-data-table tbody tr');

    if (searchableItems.length > 0) {
      searchableItems.forEach(el => {
        const text = (el.getAttribute('data-search') || el.textContent).toLowerCase();
        el.style.display = (q === '' || text.includes(q)) ? '' : 'none';
      });
    } else if (tableRows.length > 0) {
      tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = (q === '' || text.includes(q)) ? '' : 'none';
      });
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      filterPageContent(e.target.value);
    });
  }

  // --- Desktop Collapsible Sidebar System (Icon Rail Toggle) ---
  function updateSidebarCollapseUI(collapsed) {
    document.documentElement.classList.toggle('sidebar-collapsed', collapsed);
    const topbarCollapseIcon = document.querySelector('.icon-collapse');
    const topbarExpandIcon = document.querySelector('.icon-expand');
    if (topbarCollapseIcon && topbarExpandIcon) {
      topbarCollapseIcon.style.display = collapsed ? 'none' : 'block';
      topbarExpandIcon.style.display = collapsed ? 'block' : 'none';
    }

    const toggleBtn = document.getElementById('sidebarToggleBtn');
    if (toggleBtn) {
      const toggleText = toggleBtn.querySelector('.toggle-text');
      const toggleIcon = toggleBtn.querySelector('.toggle-icon');
      if (toggleText) {
        toggleText.textContent = collapsed ? 'Expand' : 'Collapse sidebar';
      }
      if (toggleIcon) {
        toggleIcon.innerHTML = collapsed
          ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><polyline points="12 9 15 12 12 15"></polyline></svg>'
          : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><polyline points="15 9 12 12 15 15"></polyline></svg>';
      }
      toggleBtn.setAttribute('data-tooltip', collapsed ? 'Expand sidebar (Ctrl+B)' : 'Collapse sidebar (Ctrl+B)');
    }
  }

  function toggleSidebarCollapse() {
    if (window.innerWidth < 769) {
      // On mobile, toggle the mobile drawer
      if (sidebar && sidebar.classList.contains('open')) {
        closeSidebar();
      } else {
        openSidebar();
      }
      return;
    }
    const currentCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
    const nextState = !currentCollapsed;
    localStorage.setItem('studenthub_sidebar_collapsed', nextState ? 'true' : 'false');
    updateSidebarCollapseUI(nextState);
  }

  // Initialize sidebar icons
  const initialCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
  updateSidebarCollapseUI(initialCollapsed);

  document.addEventListener('click', (e) => {
    if (e.target.closest('#sidebarToggleBtn') || e.target.closest('#topbarSidebarToggle')) {
      e.preventDefault();
      toggleSidebarCollapse();
    }
  });

  // Keyboard Shortcuts: Cmd/Ctrl + K, Cmd/Ctrl + B, and Escape
  document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
      if (searchInput) {
        e.preventDefault();
        searchInput.focus();
        searchInput.select();
      }
    }
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'b') {
      e.preventDefault();
      toggleSidebarCollapse();
    }
    if (e.key === 'Escape') {
      closeSidebar();
      if (searchInput && document.activeElement === searchInput) {
        searchInput.blur();
      }
    }
  });

  // --- Print Shortcut Helper ---
  document.addEventListener('click', (e) => {
    const printBtn = e.target.closest('[data-action="print"]');
    if (printBtn) {
      e.preventDefault();
      window.print();
    }
  });

  // Auto reload on bfcache navigation to prevent stale sessions
  window.addEventListener('pageshow', (e) => {
    if (e.persisted) {
      window.location.reload();
    }
  });
})();

