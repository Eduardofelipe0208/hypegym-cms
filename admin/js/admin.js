document.addEventListener('DOMContentLoaded', () => {
    // Sidebar Toggle Logic
    const sidebarToggle = document.getElementById('sidebarToggle');
    const body = document.body;

    // Cargar estado guardado
    const savedState = localStorage.getItem('sidebarState');
    if (savedState === 'collapsed') {
        body.classList.add('collapsed');
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            body.classList.toggle('collapsed');
            const newState = body.classList.contains('collapsed') ? 'collapsed' : 'expanded';
            localStorage.setItem('sidebarState', newState);
        });
    }

    // Auto-init tooltips or other global logic here if needed
});
