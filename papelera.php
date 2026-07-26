<?php
require_once __DIR__ . '/includes/auth_admin.php';
$pagina_actual = 'papelera';
$tablasPapelera = require __DIR__ . '/config/tablas_papelera.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Papelera</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<link rel="stylesheet" href="css/dashboard.css">
<style>
.pap-toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
.pap-select {
    padding: 8px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm);
    font-family: inherit; font-size: 0.82rem; background: var(--bg); color: var(--text); outline: none;
}
.pap-item {
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
    padding: 14px 16px; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.pap-campos { font-size: 0.85rem; color: var(--text); line-height: 1.5; }
.pap-campos span:not(:last-child)::after { content: '·'; color: var(--muted); margin: 0 8px; font-weight: 700; }
.pap-fecha { font-size: 0.7rem; color: var(--muted); margin-top: 4px; }
.pap-acciones { display: flex; gap: 6px; flex-shrink: 0; }
.pap-empty { text-align: center; padding: 40px 20px; color: var(--muted); font-size: 0.82rem; }
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
            <span class="page-title">Papelera</span>
        </div>
        <div class="topbar-right">
            <button class="btn-sm btn-outline" onclick="prepararTablas()">Preparar tablas</button>
        </div>
    </header>

    <div class="content">
        <div class="table-card">
            <div class="table-header">
                <span class="table-title">Elementos eliminados recientemente</span>
                <div class="pap-toolbar" style="margin-bottom:0;">
                    <select class="pap-select" id="sel-tabla" onchange="cargarPapelera()">
                        <?php foreach ($tablasPapelera as $key => $label): ?>
                            <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="padding:16px;" id="pap-contenedor">
                <div class="pap-empty">Cargando...</div>
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

function prepararTablas() {
    fetch('controllers/preparar_papelera.php')
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                alert('Listo:\n' + res.reporte.join('\n'));
                cargarPapelera();
            } else {
                alert('Error: ' + res.msg);
            }
        });
}

function esColumnaTecnica(nombre) {
    if (nombre === 'id' || nombre === 'activo' || nombre === 'deleted_at' || nombre === 'created_at') return true;
    if (/^id_/.test(nombre) || /_id$/.test(nombre)) return true;
    return false;
}

function cargarPapelera() {
    var tabla = document.getElementById('sel-tabla').value;
    var cont = document.getElementById('pap-contenedor');
    cont.innerHTML = '<div class="pap-empty">Cargando...</div>';

    fetch('controllers/papelera_listar.php?tabla=' + encodeURIComponent(tabla))
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (!res.ok) { cont.innerHTML = '<div class="pap-empty">' + esc(res.msg) + '</div>'; return; }
            if (!res.items.length) { cont.innerHTML = '<div class="pap-empty">No hay elementos eliminados en esta tabla</div>'; return; }

            var html = '';
            res.items.forEach(function(item) {
                var campos = '';
                for (var k in item) {
                    if (esColumnaTecnica(k)) continue;
                    if (item[k] === null || item[k] === '') continue;
                    campos += '<span>' + esc(item[k]) + '</span>';
                }
                html += '<div class="pap-item">' +
                    '<div>' +
                        '<div class="pap-campos">' + campos + '</div>' +
                        '<div class="pap-fecha">Eliminado: ' + esc(item.deleted_at || '—') + '</div>' +
                    '</div>' +
                    '<div class="pap-acciones">' +
                        '<button class="btn-sm btn-primary" onclick="restaurar(\'' + tabla + '\', ' + item.id + ')">Restaurar</button>' +
                        '<button class="btn-sm btn-outline" onclick="eliminarDefinitivo(\'' + tabla + '\', ' + item.id + ')">Eliminar def.</button>' +
                    '</div>' +
                '</div>';
            });
            cont.innerHTML = html;
        });
}

function restaurar(tabla, id) {
    if (!confirm('¿Restaurar este elemento?')) return;
    var fd = new FormData();
    fd.append('tabla', tabla);
    fd.append('id', id);
    fetch('controllers/papelera_restaurar.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) cargarPapelera(); else alert('Error: ' + res.msg);
        });
}

function eliminarDefinitivo(tabla, id) {
    if (!confirm('Esto borra el elemento para siempre, no se puede deshacer. ¿Continuar?')) return;
    var fd = new FormData();
    fd.append('tabla', tabla);
    fd.append('id', id);
    fetch('controllers/papelera_eliminar_definitivo.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) cargarPapelera(); else alert('Error: ' + res.msg);
        });
}

cargarPapelera();
</script>
</body>
</html>