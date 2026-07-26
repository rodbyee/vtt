<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user'])) { header('Location: login.php'); exit; }
if ($_SESSION['id_rol'] != 1) { header('Location: dashboard.php'); exit; }
$pagina_actual = 'usuarios';
$mi_id = $_SESSION['id_user'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Usuarios</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Menú">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="page-title">Gestión de usuarios</span>
        </div>
        <div class="topbar-right">
            <button class="btn-sm btn-primary" onclick="abrirNuevo()">+ Nuevo usuario</button>
        </div>
    </header>

    <div class="content">
        <div class="stats-grid stats-3" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="stat-label">Total usuarios</div>
                <div class="stat-value" id="stat-total">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
                </div>
                <div class="stat-label">Administradores</div>
                <div class="stat-value" id="stat-admins">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div class="stat-label">Empleados</div>
                <div class="stat-value" id="stat-empleados">—</div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <span class="table-title">Usuarios registrados</span>
                <input type="text" id="buscador" placeholder="Buscar nombre o correo..."
                    oninput="filtrar()"
                    style="padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
                           font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);
                           outline:none;width:240px;max-width:100%;">
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:40px;">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL NUEVO USUARIO ══ -->
<div class="modal-overlay" id="modal-nuevo" onclick="clickOverlayNuevo(event)">
    <div class="modal" style="max-width:460px;">
        <div class="modal-header">
            <span class="modal-title">Nuevo usuario</span>
            <button class="modal-close" onclick="cerrarNuevo()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Nombre completo *</label>
                <input type="text" id="n-nombre" placeholder="Nombre y apellido">
            </div>
            <div class="form-group">
                <label>Correo *</label>
                <input type="email" id="n-correo" placeholder="correo@ejemplo.com">
            </div>
            <div class="form-group">
                <label>Contraseña *</label>
                <input type="password" id="n-password" placeholder="Mínimo 8 caracteres">
            </div>
            <div class="form-group">
                <label>Rol *</label>
                <select id="n-rol" style="padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.85rem;outline:none;width:100%;">
                    <option value="2">Empleado</option>
                    <option value="1">Administrador</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarNuevo()">Cancelar</button>
            <button class="btn-sm btn-primary" id="btn-guardar-nuevo" onclick="guardarNuevo()">Crear usuario</button>
        </div>
    </div>
</div>

<!-- ══ MODAL EDITAR USUARIO ══ -->
<div class="modal-overlay" id="modal-editar" onclick="clickOverlayEditar(event)">
    <div class="modal" style="max-width:460px;">
        <div class="modal-header">
            <span class="modal-title" id="editar-titulo">Editar usuario</span>
            <button class="modal-close" onclick="cerrarEditar()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="e-id">
            <div class="form-group">
                <label>Nombre completo *</label>
                <input type="text" id="e-nombre" placeholder="Nombre y apellido">
            </div>
            <div class="form-group">
                <label>Correo *</label>
                <input type="email" id="e-correo" placeholder="correo@ejemplo.com">
            </div>
            <div class="form-group">
                <label>Nueva contraseña <span style="color:var(--muted);font-weight:400;">(dejar vacío para no cambiar)</span></label>
                <input type="password" id="e-password" placeholder="Nueva contraseña">
            </div>
            <div class="form-group" id="e-rol-group">
                <label>Rol</label>
                <select id="e-rol" style="padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.85rem;outline:none;width:100%;">
                    <option value="2">Empleado</option>
                    <option value="1">Administrador</option>
                </select>
                <div id="e-rol-aviso" style="display:none;margin-top:6px;padding:8px 12px;background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.2);border-radius:6px;font-size:0.72rem;color:#854d0e;">
                    ⚠️ No puedes cambiar el rol de un administrador.
                </div>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-sm btn-outline" style="color:#ef4444;border-color:#fecaca;" id="btn-eliminar-u" onclick="eliminarUsuario()">Eliminar</button>
            <div style="display:flex;gap:8px;">
                <button class="btn-sm btn-outline" onclick="cerrarEditar()">Cancelar</button>
                <button class="btn-sm btn-primary" id="btn-guardar-editar" onclick="guardarEdicion()">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
var usuarios  = [];
var miId      = <?= intval($mi_id) ?>;

// ── Tema / Sidebar ──
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

// ── Cargar ──
function cargar() {
    fetch('controllers/get_usuarios.php')
        .then(function(r){ return r.json(); })
        .then(function(data) {
            usuarios = data;
            renderTabla(data);
            renderStats(data);
        });
}

function renderStats(data) {
    document.getElementById('stat-total').textContent    = data.length;
    document.getElementById('stat-admins').textContent   = data.filter(function(u){ return u.id_rol == 1; }).length;
    document.getElementById('stat-empleados').textContent = data.filter(function(u){ return u.id_rol == 2; }).length;
}

function renderTabla(lista) {
    var tbody = document.getElementById('tbody');
    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:40px;font-size:0.78rem;">Sin usuarios registrados</td></tr>';
        return;
    }
    var html = '';
    lista.forEach(function(u) {
        var esAdmin  = u.id_rol == 1;
        var esMio    = u.id == miId;
        var rolBadge = esAdmin
            ? '<span class="badge badge-yellow">Administrador</span>'
            : '<span class="badge badge-gray">Empleado</span>';
        var fecha = u.created_at ? u.created_at.slice(0,10) : '—';
        var iniciales = u.nombre.split(' ').slice(0,2).map(function(p){ return p[0]; }).join('').toUpperCase();

        html += '<tr>' +
            '<td>' +
                '<div style="display:flex;align-items:center;gap:10px;">' +
                    '<div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#eab308,#ca9a04);display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:#1e293b;flex-shrink:0;">' + esc(iniciales) + '</div>' +
                    '<div>' +
                        '<div style="font-weight:600;color:var(--text);">' + esc(u.nombre) + (esMio ? ' <span style="font-size:0.65rem;color:var(--muted);">(tú)</span>' : '') + '</div>' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td style="color:var(--muted);font-size:0.82rem;">' + esc(u.correo) + '</td>' +
            '<td>' + rolBadge + '</td>' +
            '<td style="color:var(--muted);font-size:0.75rem;">' + fecha + '</td>' +
            '<td>' +
                '<button class="btn-sm btn-outline" style="font-size:0.7rem;padding:4px 10px;" onclick="abrirEditar(' + u.id + ')">Editar</button>' +
            '</td>' +
        '</tr>';
    });
    tbody.innerHTML = html;
}

function filtrar() {
    var q = document.getElementById('buscador').value.toLowerCase();
    renderTabla(usuarios.filter(function(u) {
        return u.nombre.toLowerCase().includes(q) || u.correo.toLowerCase().includes(q);
    }));
}

// ── Nuevo usuario ──
function abrirNuevo() {
    ['n-nombre','n-correo','n-password'].forEach(function(id){ document.getElementById(id).value = ''; });
    document.getElementById('n-rol').value = '2';
    document.getElementById('btn-guardar-nuevo').disabled = false;
    document.getElementById('btn-guardar-nuevo').textContent = 'Crear usuario';
    document.getElementById('modal-nuevo').classList.add('open');
    setTimeout(function(){ document.getElementById('n-nombre').focus(); }, 100);
}

function cerrarNuevo() { document.getElementById('modal-nuevo').classList.remove('open'); }
function clickOverlayNuevo(e) { if (e.target === document.getElementById('modal-nuevo')) cerrarNuevo(); }

function guardarNuevo() {
    var nombre   = document.getElementById('n-nombre').value.trim();
    var correo   = document.getElementById('n-correo').value.trim();
    var password = document.getElementById('n-password').value;
    var rol      = document.getElementById('n-rol').value;

    if (!nombre)   { resaltar('n-nombre');   return; }
    if (!correo)   { resaltar('n-correo');   return; }
    if (!password || password.length < 8) { resaltar('n-password'); alert('La contraseña debe tener al menos 8 caracteres'); return; }

    var btn = document.getElementById('btn-guardar-nuevo');
    btn.disabled = true; btn.textContent = 'Creando...';

    var fd = new FormData();
    fd.append('nombre',   nombre);
    fd.append('correo',   correo);
    fd.append('password', password);
    fd.append('id_rol',   rol);

    fetch('controllers/add_usuario.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarNuevo(); cargar(); }
            else {
                alert('Error: ' + res.msg);
                btn.disabled = false; btn.textContent = 'Crear usuario';
            }
        });
}

// ── Editar usuario ──
function abrirEditar(id) {
    var u = usuarios.find(function(x){ return x.id == id; });
    if (!u) return;

    var esAdmin = u.id_rol == 1;

    document.getElementById('e-id').value      = u.id;
    document.getElementById('e-nombre').value  = u.nombre;
    document.getElementById('e-correo').value  = u.correo;
    document.getElementById('e-password').value = '';
    document.getElementById('e-rol').value     = u.id_rol;
    document.getElementById('editar-titulo').textContent = 'Editar — ' + u.nombre;

    // Si es admin, bloquear cambio de rol
    var rolSelect = document.getElementById('e-rol');
    var rolAviso  = document.getElementById('e-rol-aviso');
    if (esAdmin) {
        rolSelect.disabled = true;
        rolAviso.style.display = 'block';
    } else {
        rolSelect.disabled = false;
        rolAviso.style.display = 'none';
    }

    // No se puede eliminar admins
    var btnEliminar = document.getElementById('btn-eliminar-u');
    if (esAdmin) {
        btnEliminar.style.display = 'none';
    } else {
        btnEliminar.style.display = '';
    }

    document.getElementById('btn-guardar-editar').disabled = false;
    document.getElementById('btn-guardar-editar').textContent = 'Guardar cambios';
    document.getElementById('modal-editar').classList.add('open');
}

function cerrarEditar() { document.getElementById('modal-editar').classList.remove('open'); }
function clickOverlayEditar(e) { if (e.target === document.getElementById('modal-editar')) cerrarEditar(); }

function guardarEdicion() {
    var id     = document.getElementById('e-id').value;
    var nombre = document.getElementById('e-nombre').value.trim();
    var correo = document.getElementById('e-correo').value.trim();
    var pass   = document.getElementById('e-password').value;
    var rol    = document.getElementById('e-rol').value;

    if (!nombre) { resaltar('e-nombre'); return; }
    if (!correo) { resaltar('e-correo'); return; }
    if (pass && pass.length < 8) { resaltar('e-password'); alert('La contraseña debe tener al menos 8 caracteres'); return; }

    var btn = document.getElementById('btn-guardar-editar');
    btn.disabled = true; btn.textContent = 'Guardando...';

    var fd = new FormData();
    fd.append('id',     id);
    fd.append('nombre', nombre);
    fd.append('correo', correo);
    fd.append('id_rol', rol);
    if (pass) fd.append('password', pass);

    fetch('controllers/update_usuario.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarEditar(); cargar(); }
            else {
                alert('Error: ' + res.msg);
                btn.disabled = false; btn.textContent = 'Guardar cambios';
            }
        });
}

function eliminarUsuario() {
    var id = document.getElementById('e-id').value;
    var u  = usuarios.find(function(x){ return x.id == id; });
    if (!u) return;
    if (u.id_rol == 1) { alert('No se puede eliminar un administrador.'); return; }
    if (!confirm('¿Eliminar al usuario "' + u.nombre + '"? Esta acción no se puede deshacer.')) return;

    var fd = new FormData();
    fd.append('id', id);

    fetch('controllers/delete_usuario.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarEditar(); cargar(); }
            else alert('Error: ' + res.msg);
        });
}

function resaltar(id) {
    var el = document.getElementById(id);
    el.style.borderColor = '#ef4444'; el.focus();
    el.addEventListener('input', function(){ el.style.borderColor = ''; }, { once:true });
}

function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

cargar();
</script>
</body>
</html>