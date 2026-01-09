// Sidebar Loader - Dynamically load and inject the shared sidebar
(function() {
  'use strict';

  /**
   * Load the shared sidebar component and inject it into the page
   * This function reads the sidebar.html file and replaces any existing sidebar
   */
  function loadSharedSidebar() {
    // Get the current script's directory to resolve relative paths correctly
    const scriptPath = document.currentScript ? document.currentScript.src : '';
    const scriptDir = scriptPath ? scriptPath.substring(0, scriptPath.lastIndexOf('/')) : '.';
    
    // Determine the correct path based on current location
    const currentPath = window.location.pathname;
    const isInSubfolder = currentPath.split('/').filter(p => p && p !== 'Task-08thJan').length > 1;
    const sidebarPath = isInSubfolder ? '../components/sidebar.html' : './components/sidebar.html';

    fetch(sidebarPath)
      .then(response => {
        if (!response.ok) {
          console.warn('Failed to load sidebar component');
          return null;
        }
        return response.text();
      })
      .then(html => {
        if (!html) return;

        // Find the existing sidebar or the app wrapper
        const existingSidebar = document.querySelector('.app-sidebar');
        const appWrapper = document.querySelector('.app-wrapper');

        if (!appWrapper) {
          console.warn('App wrapper not found');
          return;
        }

        // Create a temporary container to parse the HTML
        const temp = document.createElement('div');
        temp.innerHTML = html.trim();
        const newSidebar = temp.firstElementChild;

        if (existingSidebar) {
          // Replace existing sidebar
          existingSidebar.replaceWith(newSidebar);
        } else {
          // Insert after header if no sidebar exists
          const header = appWrapper.querySelector('.app-header');
          if (header) {
            header.insertAdjacentElement('afterend', newSidebar);
          }
        }

        // Reinitialize AdminLTE components for the new sidebar
        if (window.AdminLTE) {
          // Trigger re-initialization of treeview and other components
          initializeSidebarComponents();
        }
      })
      .catch(error => {
        console.warn('Error loading sidebar:', error);
      });
  }

  /**
   * Initialize sidebar components after injecting the sidebar
   */
  function initializeSidebarComponents() {
    // Initialize overlay scrollbars for sidebar if available
    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
    if (sidebarWrapper && window.OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
      window.OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
        scrollbars: {
          theme: 'os-theme-light',
          autoHide: 'leave',
          clickScroll: true,
        },
      });
    }
  }

  // Load sidebar when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadSharedSidebar);
  } else {
    loadSharedSidebar();
  }
})();
