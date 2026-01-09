/**
 * Sidebar Menu Toggle Handler - FIXED VERSION
 * Manages opening/closing of submenu items with proper toggle behavior
 * Features:
 * - Click to open/close submenus
 * - Only one submenu open at a time (accordion style)
 * - Smooth transitions
 * - Active state highlighting
 * - Fixed: Proper state tracking and switching between menus
 */

(function() {
  'use strict';

  // Track which menu is currently open
  let currentOpenMenu = null;

  /**
   * Initialize sidebar menu toggle functionality
   */
  function initSidebarToggle() {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', setupToggleHandlers);
    } else {
      setupToggleHandlers();
    }
  }

  /**
   * Setup click handlers for all main menu items with submenus
   */
  function setupToggleHandlers() {
    const mainMenuItems = document.querySelectorAll('.nav-item > .nav-link');

    mainMenuItems.forEach(link => {
      const parentLi = link.parentElement;
      const submenu = parentLi.querySelector('.nav-treeview');

      if (submenu) {
        // Add click handler
        link.addEventListener('click', function(e) {
          e.preventDefault();
          handleMenuClick(parentLi, link, submenu);
        });

        if (link.getAttribute('href') === '#') {
          link.style.cursor = 'pointer';
        }
      }
    });

    // Submenu items can navigate normally
    const submenuItems = document.querySelectorAll('.nav-treeview a');
    submenuItems.forEach(link => {
      link.addEventListener('click', function(e) {
        // Mark as active
        submenuItems.forEach(a => a.classList.remove('active'));
        this.classList.add('active');
      });
    });
  }

  /**
   * Handle menu click - improved logic
   */
  function handleMenuClick(parentLi, link, submenu) {
    // If this menu is already open, close it
    if (currentOpenMenu === parentLi) {
      closeSubmenu(parentLi, link, submenu);
      currentOpenMenu = null;
      return;
    }

    // Close previously open menu
    if (currentOpenMenu) {
      const prevLink = currentOpenMenu.querySelector('.nav-link');
      const prevSubmenu = currentOpenMenu.querySelector('.nav-treeview');
      closeSubmenu(currentOpenMenu, prevLink, prevSubmenu);
    }

    // Open new menu
    openSubmenu(parentLi, link, submenu);
    currentOpenMenu = parentLi;
  }

  /**
   * Open a submenu with animation
   */
  function openSubmenu(parentLi, link, submenu) {
    // Set initial state
    parentLi.classList.add('menu-open');
    link.classList.add('active');
    submenu.classList.add('menu-open');

    // Make visible and trigger animation
    submenu.style.display = 'block';
    submenu.style.maxHeight = '0';
    
    // Force reflow to ensure animation triggers
    void submenu.offsetHeight;
    
    // Animate to full height
    submenu.style.maxHeight = submenu.scrollHeight + 'px';
  }

  /**
   * Close a submenu with animation
   */
  function closeSubmenu(parentLi, link, submenu) {
    // Start closing animation
    submenu.style.maxHeight = '0';
    
    // Remove classes after animation
    setTimeout(() => {
      parentLi.classList.remove('menu-open');
      link.classList.remove('active');
      submenu.classList.remove('menu-open');
      submenu.style.display = 'none';
    }, 300);
  }

  /**
   * Highlight the appropriate menu based on current page
   */
  function highlightCurrentPage() {
    const currentFile = window.location.pathname.split('/').pop() || 'index.html';
    let foundCurrent = false;
    
    document.querySelectorAll('.nav-treeview a').forEach(link => {
      const href = link.getAttribute('href') || '';
      
      if (href === currentFile || href.endsWith('/' + currentFile)) {
        link.classList.add('active');
        foundCurrent = true;
        
        // Open parent menu automatically
        const submenu = link.closest('.nav-treeview');
        if (submenu) {
          const parentLi = submenu.parentElement;
          const parentLink = parentLi.querySelector('.nav-link');
          
          if (parentLi && parentLink) {
            parentLi.classList.add('menu-open');
            parentLink.classList.add('active');
            submenu.classList.add('menu-open');
            submenu.style.display = 'block';
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            currentOpenMenu = parentLi;
          }
        }
      } else {
        link.classList.remove('active');
      }
    });
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      initSidebarToggle();
      // Small delay to ensure sidebar is loaded
      setTimeout(highlightCurrentPage, 100);
    });
  } else {
    initSidebarToggle();
    setTimeout(highlightCurrentPage, 100);
  }

  // Also initialize after sidebar is injected (for dynamic loading)
  // Use MutationObserver to detect sidebar injection
  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.addedNodes.length) {
        for (let node of mutation.addedNodes) {
          if (node.classList && node.classList.contains('app-sidebar')) {
            // Sidebar was injected, reinitialize
            setupToggleHandlers();
            highlightCurrentPage();
            observer.disconnect();
          }
        }
      }
    });
  });

  // Start observing for sidebar injection
  observer.observe(document.body, {
    childList: true,
    subtree: true
  });
})();
