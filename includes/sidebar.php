<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$nombre = $_SESSION['nombre'] ?? 'Usuario';
$id_rol = $_SESSION['id_rol'] ?? 2;
$pagina_actual = $pagina_actual ?? '';
?>
<div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-ring">
            <img src="assets/logo/logovttr.png" alt="Villa Tecnia">
        </div>
        <div>
            <div class="sidebar-brand">VILLA <span>TECNIA</span></div>
            <div class="sidebar-tagline">Panel administrativo</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>
        <a class="nav-item <?= $pagina_actual === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>

        <div class="nav-section">Ventas</div>
        <a class="nav-item <?= $pagina_actual === 'cotizaciones' ? 'active' : '' ?>" href="cotizaciones.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
            Cotizaciones
        </a>
        <a class="nav-item <?= $pagina_actual === 'remisiones' ? 'active' : '' ?>" href="remisiones.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
            Notas de Remision
        </a>

        <div class="nav-section">Operaciones</div>
        <a class="nav-item <?= $pagina_actual === 'contactos' ? 'active' : '' ?>" href="contactos.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Contactos
        </a>
        <a class="nav-item <?= $pagina_actual === 'inventario' ? 'active' : '' ?>" href="inventario.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            Inventario
        </a>

        <?php if ($id_rol == 1): ?>
        <div class="nav-section">Administración</div>
        <a class="nav-item <?= $pagina_actual === 'usuarios' ? 'active' : '' ?>" href="usuarios.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
            Gestión de usuarios
        </a>
        <a class="nav-item <?= $pagina_actual === 'papelera' ? 'active' : '' ?>" href="papelera.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
            Papelera
        </a>
        <a class="nav-item <?= $pagina_actual === 'respaldos' ? 'active' : '' ?>" href="respaldos.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Respaldos
        </a>

        
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <span class="config-label">Configuración</span>
        <div class="config-section">
            <button class="config-item" onclick="toggleTheme()">
                <span class="config-item-left">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    Modo oscuro
                </span>
                <div class="toggle-track" id="toggle-track">
                    <div class="toggle-thumb"></div>
                </div>
            </button>
            <a href="controllers/logout.php" class="config-item logout-item">
                <span class="config-item-left">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Cerrar sesión
                </span>
            </a>
        </div>
        <div class="user-chip">
            <div class="user-avatar"><?= strtoupper(substr($nombre, 0, 2)) ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($nombre) ?></div>
                <div class="user-role"><?= $id_rol == 1 ? 'Administrador' : 'Empleado' ?></div>
            </div>
        </div>
    </div>
</aside>