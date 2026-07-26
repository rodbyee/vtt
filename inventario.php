<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user'])) { header('Location: index.php'); exit; }
$pagina_actual = 'inventario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Inventario</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<link rel="stylesheet" href="css/dashboard.css">
<style>
.inv-list { display: flex; flex-direction: column; gap: 8px; }
.inv-item {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); display: flex; align-items: center;
    gap: 16px; padding: 14px 18px 14px 0; transition: box-shadow 0.15s; position: relative;
}
.inv-item:hover { box-shadow: var(--shadow); }
.inv-bar { width: 5px; min-width: 5px; align-self: stretch; border-radius: 4px 0 0 4px; background: var(--border); flex-shrink: 0; }
.inv-info { flex: 1; min-width: 0; padding-left: 4px; }
.inv-top { display: flex; align-items: center; gap: 8px; margin-bottom: 2px; }
.inv-nombre { font-size: 0.88rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.inv-dep-badge { font-size: 0.65rem; font-weight: 600; padding: 2px 8px; border-radius: 999px; white-space: nowrap; flex-shrink: 0; opacity: 0.85; }
.inv-bottom { display: flex; align-items: center; gap: 10px; }
.inv-sku { font-size: 0.7rem; color: var(--muted); font-weight: 500; letter-spacing: 0.04em; }
.inv-desc { font-size: 0.72rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px; }
.inv-precios { display: flex; flex-direction: column; align-items: flex-end; flex-shrink: 0; gap: 1px; }
.inv-precio-final { font-size: 0.95rem; font-weight: 700; color: var(--text); white-space: nowrap; }
.inv-precio-desglose { font-size: 0.65rem; color: var(--muted); white-space: nowrap; }
.inv-precio-anterior { font-size: 0.68rem; color: var(--muted); white-space: nowrap; text-decoration: line-through; }
.inv-menu-btn { background: none; border: none; cursor: pointer; color: var(--muted); padding: 6px; border-radius: var(--radius-sm); display: flex; align-items: center; position: relative; flex-shrink: 0; transition: background 0.15s, color 0.15s; }
.inv-menu-btn:hover { background: var(--bg); color: var(--text); }
.inv-menu-btn svg { width: 18px; height: 18px; }
.inv-dropdown { display: none; position: absolute; right: 0; top: 100%; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); box-shadow: 0 8px 24px rgba(0,0,0,0.15); z-index: 50; min-width: 140px; overflow: hidden; }
.inv-dropdown.open { display: block; }
.inv-dropdown-item { display: flex; align-items: center; gap: 8px; padding: 10px 14px; font-size: 0.8rem; color: var(--text); cursor: pointer; border: none; background: none; width: 100%; font-family: inherit; text-align: left; transition: background 0.1s; }
.inv-dropdown-item:hover { background: var(--bg); }
.inv-dropdown-item.danger { color: #ef4444; }
.inv-dropdown-item svg { width: 14px; height: 14px; flex-shrink: 0; }
.inv-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; padding: 60px 20px; text-align: center; }
.inv-empty svg { width: 40px; height: 40px; color: var(--muted); opacity: 0.4; }
.inv-empty p { font-size: 0.82rem; color: var(--muted); }

.precio-preview {
    background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-sm);
    padding: 12px 14px; font-size: 0.82rem;
}
.precio-preview-row { display: flex; justify-content: space-between; padding: 3px 0; color: var(--muted); }
.precio-preview-row.final { font-weight: 700; color: var(--text); border-top: 1px solid var(--border); margin-top: 6px; padding-top: 8px; font-size: 0.92rem; }
.precio-preview-row strong { color: var(--text); font-weight: 600; }

.color-picker-row { display: flex; align-items: center; gap: 10px; }
.color-picker-row input[type="color"] { width: 36px; height: 36px; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 2px; cursor: pointer; background: var(--bg); flex-shrink: 0; }

.dep-list { display: flex; flex-direction: column; gap: 6px; margin-top: 8px; }
.dep-list-item { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.82rem; color: var(--text); }
.dep-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
.dep-list-item span { flex: 1; }
.dep-del { background: none; border: none; cursor: pointer; color: var(--muted); font-size: 1rem; line-height: 1; padding: 2px 4px; transition: color 0.15s; }
.dep-del:hover { color: #ef4444; }

@media (max-width: 480px) {
    .inv-desc { display: none; }
    .inv-item { padding: 12px 14px 12px 0; gap: 10px; }
}
</style>
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
            <span class="page-title">Inventario</span>
        </div>
        <div class="topbar-right" style="gap:8px;">
            <button class="btn-sm btn-outline" onclick="abrirModalDep()">Departamentos</button>
            <button class="btn-sm btn-primary" onclick="abrirModal()">+ Agregar producto</button>
        </div>
    </header>

    <div class="content">

        <div class="stats-grid stats-2" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon red">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
                <div class="stat-label">Total productos</div>
                <div class="stat-value" id="stat-total">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                </div>
                <div class="stat-label">Departamentos</div>
                <div class="stat-value" id="stat-deps">—</div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <span class="table-title">Productos</span>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <select id="filtro-dep" onchange="filtrar()"
                        style="padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);
                               font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);outline:none;">
                        <option value="">Todos los depto.</option>
                    </select>
                    <input type="text" id="buscador" placeholder="Buscar nombre o SKU..."
                        oninput="filtrar()"
                        style="padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
                               font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);
                               outline:none;width:200px;max-width:100%;">
                </div>
            </div>
            <div style="padding:16px;" id="inv-contenedor">
                <div class="inv-empty">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    <p>Cargando productos...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL PRODUCTO ══ -->
<div class="modal-overlay" id="modal" onclick="clickOverlay(event)">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modal-titulo">Nuevo producto</span>
            <button class="modal-close" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="f-id">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" id="f-nombre" placeholder="Nombre del producto" autocomplete="off">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>SKU *</label>
                    <input type="text" id="f-sku" placeholder="Ej. PROD-001">
                </div>
                <div class="form-group">
                    <label>Precio final (con IVA) *</label>
                    <input type="number" id="f-precio" placeholder="0.00" min="0" step="0.01" oninput="actualizarPreview()">
                </div>
            </div>
            <div class="precio-preview" id="precio-preview" style="display:none;">
                <div class="precio-preview-row"><span>Precio base (sin IVA)</span><strong id="prev-base">$0.00</strong></div>
                <div class="precio-preview-row"><span>IVA (16%)</span><strong id="prev-iva">$0.00</strong></div>
                <div class="precio-preview-row final"><span>Precio final</span><span id="prev-final">$0.00</span></div>
            </div>
            <div class="form-group">
                <label>Departamento</label>
                <select id="f-dep" style="padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
                    font-family:inherit;font-size:0.85rem;background:var(--bg);color:var(--text);outline:none;width:100%;">
                    <option value="">Sin departamento</option>
                </select>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea id="f-descripcion" rows="2" placeholder="Opcional"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-sm btn-primary" id="btn-guardar" onclick="guardar()">Guardar</button>
        </div>
    </div>
</div>

<!-- ══ MODAL DEPARTAMENTOS ══ -->
<div class="modal-overlay" id="modal-dep" onclick="clickOverlayDep(event)">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Departamentos</span>
            <button class="modal-close" onclick="cerrarModalDep()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre del departamento</label>
                    <input type="text" id="dep-nombre" placeholder="Ej. Químicos">
                </div>
                <div class="form-group">
                    <label>Color</label>
                    <div class="color-picker-row">
                        <input type="color" id="dep-color" value="#eab308">
                        <span id="dep-color-hex" style="font-size:0.78rem;color:var(--muted);">#eab308</span>
                    </div>
                </div>
            </div>
            <button class="btn-sm btn-primary" style="width:100%;justify-content:center;" onclick="guardarDep()">
                + Añadir departamento
            </button>
            <div id="dep-list-container">
                <div class="dep-list" id="dep-list"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarModalDep()">Cerrar</button>
        </div>
    </div>
</div>

<script>
var productos = [];
var departamentos = [];
var dropdownAbierto = null;

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

document.addEventListener('click', function(e) {
    if (dropdownAbierto && !dropdownAbierto.contains(e.target)) {
        dropdownAbierto.querySelector('.inv-dropdown').classList.remove('open');
        dropdownAbierto = null;
    }
});

document.getElementById('dep-color').addEventListener('input', function() {
    document.getElementById('dep-color-hex').textContent = this.value;
});

function fmt(n) { return '$' + parseFloat(n).toFixed(2); }

// ── Cargar todo ──
function cargar() {
    Promise.all([
        fetch('controllers/get_inventario.php').then(function(r){ return r.json(); }),
        fetch('controllers/get_departamentos.php').then(function(r){ return r.json(); })
    ]).then(function(results) {
        productos     = results[0];
        departamentos = results[1];
        renderLista(productos);
        renderStats(productos, departamentos);
        poblarSelectDeps();
        poblarFiltroDep();
        renderDepList();
    }).catch(function() {
        document.getElementById('inv-contenedor').innerHTML =
            '<div class="inv-empty"><p>Error al cargar</p></div>';
    });
}

function renderStats(prods, deps) {
    document.getElementById('stat-total').textContent = prods.length;
    document.getElementById('stat-deps').textContent  = deps.length;
}

function poblarSelectDeps() {
    var sel = document.getElementById('f-dep');
    var val = sel.value;
    var html = '<option value="">Sin departamento</option>';
    departamentos.forEach(function(d){ html += '<option value="' + d.id + '">' + esc(d.nombre) + '</option>'; });
    sel.innerHTML = html;
    sel.value = val;
}

function poblarFiltroDep() {
    var sel = document.getElementById('filtro-dep');
    var val = sel.value;
    var html = '<option value="">Todos los depto.</option>';
    departamentos.forEach(function(d){ html += '<option value="' + d.id + '">' + esc(d.nombre) + '</option>'; });
    sel.innerHTML = html;
    sel.value = val;
}

function renderDepList() {
    var list = document.getElementById('dep-list');
    if (!departamentos.length) {
        list.innerHTML = '<p style="font-size:0.78rem;color:var(--muted);text-align:center;padding:16px 0;">Sin departamentos creados</p>';
        return;
    }
    var html = '';
    departamentos.forEach(function(d) {
        html += '<div class="dep-list-item">' +
            '<div class="dep-dot" style="background:' + esc(d.color) + '"></div>' +
            '<span>' + esc(d.nombre) + '</span>' +
            '<button class="dep-del" onclick="eliminarDep(' + d.id + ')" title="Eliminar">&times;</button>' +
        '</div>';
    });
    list.innerHTML = html;
}

function renderLista(lista) {
    var cont = document.getElementById('inv-contenedor');
    if (!lista.length) {
        cont.innerHTML = '<div class="inv-empty">' +
            '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>' +
            '<p>Sin productos registrados</p></div>';
        return;
    }

    var html = '<div class="inv-list">';
    lista.forEach(function(p) {
        var precioFinal = parseFloat(p.precio_final);
        var precioBase  = precioFinal / 1.16;
        var color  = p.dep_color || '#475569';
        var depBadge = p.dep_nombre
            ? '<span class="inv-dep-badge" style="background:' + color + '22;color:' + color + '">' + esc(p.dep_nombre) + '</span>'
            : '';
        html += '<div class="inv-item">' +
            '<div class="inv-bar" style="background:' + color + '"></div>' +
            '<div class="inv-info">' +
                '<div class="inv-top">' +
                    '<div class="inv-nombre">' + esc(p.nombre) + '</div>' +
                    depBadge +
                '</div>' +
                '<div class="inv-bottom">' +
                    '<span class="inv-sku">SKU: ' + esc(p.sku) + '</span>' +
                    (p.descripcion ? '<span class="inv-desc">· ' + esc(p.descripcion) + '</span>' : '') +
                '</div>' +
            '</div>' +
            '<div class="inv-precios">' +
                '<div class="inv-precio-final">' + fmt(precioFinal) + '</div>' +
                '<div class="inv-precio-desglose">' + fmt(precioBase) + ' + IVA</div>' +
                (p.precio_anterior ? '<div class="inv-precio-anterior">' + fmt(p.precio_anterior) + '</div>' : '') +
            '</div>' +
            '<div class="inv-menu-btn" onclick="toggleDropdown(event, this, ' + p.id + ')">' +
                '<svg fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>' +
                '<div class="inv-dropdown" id="dd-' + p.id + '">' +
                    '<button class="inv-dropdown-item" onclick="editarProducto(' + p.id + ')">' +
                        '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>' +
                        'Editar' +
                    '</button>' +
                    '<button class="inv-dropdown-item danger" onclick="eliminarProducto(' + p.id + ')">' +
                        '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>' +
                        'Eliminar' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
    });
    html += '</div>';
    cont.innerHTML = html;
}

function filtrar() {
    var q   = document.getElementById('buscador').value.toLowerCase();
    var dep = document.getElementById('filtro-dep').value;
    renderLista(productos.filter(function(p) {
        return (!dep || p.id_departamento == dep) &&
               (p.nombre.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
    }));
}

function toggleDropdown(e, btn, id) {
    e.stopPropagation();
    var dd = document.getElementById('dd-' + id);
    var yaAbierto = dd.classList.contains('open');
    if (dropdownAbierto) dropdownAbierto.querySelector('.inv-dropdown').classList.remove('open');
    if (!yaAbierto) { dd.classList.add('open'); dropdownAbierto = btn; }
    else dropdownAbierto = null;
}

function actualizarPreview() {
    var val = parseFloat(document.getElementById('f-precio').value);
    var prev = document.getElementById('precio-preview');
    if (!isNaN(val) && val > 0) {
        var base = val / 1.16;
        var iva  = val - base;
        document.getElementById('prev-base').textContent  = fmt(base);
        document.getElementById('prev-iva').textContent   = fmt(iva);
        document.getElementById('prev-final').textContent = fmt(val);
        prev.style.display = 'block';
    } else {
        prev.style.display = 'none';
    }
}

// ── Modal producto ──
function abrirModal() {
    document.getElementById('f-id').value          = '';
    document.getElementById('f-nombre').value      = '';
    document.getElementById('f-sku').value         = '';
    document.getElementById('f-precio').value      = '';
    document.getElementById('f-descripcion').value = '';
    document.getElementById('f-dep').value         = '';
    document.getElementById('precio-preview').style.display = 'none';
    document.getElementById('modal-titulo').textContent = 'Nuevo producto';
    document.getElementById('btn-guardar').disabled = false;
    document.getElementById('btn-guardar').textContent = 'Guardar';
    document.getElementById('modal').classList.add('open');
    setTimeout(function(){ document.getElementById('f-nombre').focus(); }, 100);
}

function editarProducto(id) {
    if (dropdownAbierto) { dropdownAbierto.querySelector('.inv-dropdown').classList.remove('open'); dropdownAbierto = null; }
    var p = productos.find(function(x){ return x.id == id; });
    if (!p) return;
    document.getElementById('f-id').value          = p.id;
    document.getElementById('f-nombre').value      = p.nombre;
    document.getElementById('f-sku').value         = p.sku || '';
    document.getElementById('f-precio').value      = p.precio_final;
    document.getElementById('f-descripcion').value = p.descripcion || '';
    document.getElementById('f-dep').value         = p.id_departamento || '';
    actualizarPreview();
    document.getElementById('modal-titulo').textContent = 'Editar producto';
    document.getElementById('btn-guardar').disabled = false;
    document.getElementById('btn-guardar').textContent = 'Guardar cambios';
    document.getElementById('modal').classList.add('open');
}

function cerrarModal() { document.getElementById('modal').classList.remove('open'); }
function clickOverlay(e) { if (e.target === document.getElementById('modal')) cerrarModal(); }

function guardar() {
    var nombre = document.getElementById('f-nombre').value.trim();
    var precio = document.getElementById('f-precio').value.trim();

    if (!nombre) { resaltar('f-nombre'); return; }
    if (!precio) { resaltar('f-precio'); return; }

    var btn = document.getElementById('btn-guardar');
    btn.disabled = true; btn.textContent = 'Guardando...';

    var id  = document.getElementById('f-id').value;
    var url = id ? 'controllers/edit_inventario.php' : 'controllers/add_inventario.php';

    var fd = new FormData();
    if (id) fd.append('id', id);
    fd.append('nombre',          nombre);
    fd.append('sku',             document.getElementById('f-sku').value.trim());
    fd.append('precio_final',    precio);
    fd.append('descripcion',     document.getElementById('f-descripcion').value.trim());
    fd.append('id_departamento', document.getElementById('f-dep').value);

    fetch(url, { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarModal(); cargar(); }
            else {
                alert('Error: ' + res.msg);
                btn.disabled = false;
                btn.textContent = id ? 'Guardar cambios' : 'Guardar';
            }
        });
}

// ── Modal departamentos ──
function abrirModalDep() {
    document.getElementById('dep-nombre').value = '';
    document.getElementById('dep-color').value  = '#eab308';
    document.getElementById('dep-color-hex').textContent = '#eab308';
    renderDepList();
    document.getElementById('modal-dep').classList.add('open');
    setTimeout(function(){ document.getElementById('dep-nombre').focus(); }, 100);
}

function cerrarModalDep() { document.getElementById('modal-dep').classList.remove('open'); }
function clickOverlayDep(e) { if (e.target === document.getElementById('modal-dep')) cerrarModalDep(); }

function guardarDep() {
    var nombre = document.getElementById('dep-nombre').value.trim();
    var color  = document.getElementById('dep-color').value;
    if (!nombre) { resaltar('dep-nombre'); return; }

    var fd = new FormData();
    fd.append('nombre', nombre);
    fd.append('color',  color);

    fetch('controllers/add_departamento.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                document.getElementById('dep-nombre').value = '';
                fetch('controllers/get_departamentos.php')
                    .then(function(r){ return r.json(); })
                    .then(function(deps) {
                        departamentos = deps;
                        renderDepList();
                        poblarSelectDeps();
                        poblarFiltroDep();
                        document.getElementById('stat-deps').textContent = deps.length;
                    });
            } else {
                alert('Error: ' + res.msg);
            }
        })
        .catch(function(err) {
            alert('Error de conexión: ' + err);
            console.error(err);
        });
}

function eliminarDep(id) {
    if (!confirm('¿Eliminar este departamento?')) return;
    var fd = new FormData();
    fd.append('id', id);
    fetch('controllers/delete_departamento.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                fetch('controllers/get_departamentos.php').then(function(r){ return r.json(); }).then(function(deps) {
                    departamentos = deps;
                    renderDepList();
                    poblarSelectDeps();
                    poblarFiltroDep();
                    document.getElementById('stat-deps').textContent = deps.length;
                });
            }
        });
}

function eliminarProducto(id) {
    if (dropdownAbierto) { dropdownAbierto.querySelector('.inv-dropdown').classList.remove('open'); dropdownAbierto = null; }
    var p = productos.find(function(x){ return x.id == id; });
    if (!confirm('¿Eliminar "' + p.nombre + '"?')) return;
    var fd = new FormData();
    fd.append('id', id);
    fetch('controllers/delete_inventario.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) { if (res.ok) cargar(); else alert('Error: ' + res.msg); });
}

function resaltar(id) {
    var el = document.getElementById(id);
    el.style.borderColor = '#ef4444';
    el.focus();
    el.addEventListener('input', function(){ el.style.borderColor = ''; }, { once: true });
}

function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

cargar();
</script>
</body>
</html>