document.addEventListener('DOMContentLoaded', () => {
    // Sidebar Toggle Logic
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const body = document.body;

    // Create Overlay for Mobile
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    body.appendChild(overlay);

    // Cargar estado guardado (Solo Desktop)
    if (window.innerWidth > 768) {
        const savedState = localStorage.getItem('sidebarState');
        if (savedState === 'collapsed') {
            body.classList.add('collapsed');
        }
    }

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            // Mobile: Toggle class on Sidebar and Overlay
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            // Desktop: Toggle collapsed class on Body
            body.classList.toggle('collapsed');
            const newState = body.classList.contains('collapsed') ? 'collapsed' : 'expanded';
            localStorage.setItem('sidebarState', newState);
        }
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevenir burbujeo
            toggleSidebar();
        });
    }

    // Close on Overlay Click (Mobile)
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    // Close on resizing to desktop key
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        }
    });
});
