<?php
require_once __DIR__ . '/includes/auth_admin.php';
$pagina_actual = 'respaldos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Respaldos</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<link rel="stylesheet" href="css/dashboard.css">
<style>
.bk-item {
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
    padding: 12px 14px; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.bk-nombre { font-size: 0.84rem; font-weight: 600; color: var(--text); }
.bk-meta { font-size: 0.72rem; color: var(--muted); margin-top: 2px; }
.bk-empty { text-align: center; padding: 40px 20px; color: var(--muted); font-size: 0.82rem; }
</style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Menú">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="page-title">Respaldos</span>
        </div>
        <div class="topbar-right">
            <button class="btn-sm btn-primary" id="btn-generar" onclick="generarBackup()">Generar respaldo nuevo</button>
        </div>
    </header>

    <div class="content">
        <div class="table-card">
            <div class="table-header">
                <span class="table-title">Respaldos guardados en el servidor</span>
            </div>
            <div style="padding:16px;" id="bk-contenedor">
                <div class="bk-empty">Cargando...</div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleTheme() {
    var isDark = document.body.getAttribute('data-theme') === 'dark';
    document.body.setAttribute('data-theme', isDark ? '' : 'dark');
    localStorage.setItem('vt-theme', isDark ? 'light' : 'dark');
    document.getElementById('toggle-track').classList.toggle('on', !isDark);
}

(function() {
    if (localStorage.getItem('vt-theme') === 'dark') {
        document.body.setAttribute('data-theme', 'dark');
        var tt = document.getElementById('toggle-track');
        if (tt) tt.classList.add('on');
    }
})();

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('open');
}

function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('open');
}

function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function cargarBackups() {
    var cont = document.getElementById('bk-contenedor');
    fetch('controllers/listar_backups.php')
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (!res.ok || !res.items.length) {
                cont.innerHTML = '<div class="bk-empty">Aún no hay respaldos generados</div>';
                return;
            }
            var html = '';
            res.items.forEach(function(b) {
                html += '<div class="bk-item">' +
                    '<div>' +
                        '<div class="bk-nombre">' + esc(b.nombre) + '</div>' +
                        '<div class="bk-meta">' + esc(b.fecha) + ' · ' + esc(b.tamano) + '</div>' +
                    '</div>' +
                    '<a class="btn-sm btn-outline" href="controllers/descargar_backup.php?file=' + encodeURIComponent(b.nombre) + '">Descargar</a>' +
                '</div>';
            });
            cont.innerHTML = html;
        });
}

function generarBackup() {
    var btn = document.getElementById('btn-generar');
    btn.disabled = true;
    btn.textContent = 'Generando...';
    fetch('controllers/generar_backup.php')
        .then(function(r){ return r.json(); })
        .then(function(res) {
            btn.disabled = false;
            btn.textContent = 'Generar respaldo nuevo';
            if (res.ok) cargarBackups(); else alert('Error: ' + res.msg);
        })
        .catch(function() {
            btn.disabled = false;
            btn.textContent = 'Generar respaldo nuevo';
            alert('Error de conexión al generar el respaldo');
        });
}

cargarBackups();
</script>
</body>
</html>