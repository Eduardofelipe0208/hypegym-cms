<header class="topbar">
    <div style="display:flex; align-items:center; gap:15px;">
        <button id="sidebarToggle" style="background:none; border:none; color:#fff; font-size:1.5rem; cursor:pointer;">
            <i class="ph ph-list"></i>
        </button>
        <div class="page-title"><?php echo isset($page_title) ? $page_title : 'Panel de Administración'; ?></div>
    </div>
    
    <div class="user-profile">
        <div class="admin-name">
            <i class="ph ph-user-circle"></i> 
            <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
        </div>
    </div>
</header>
