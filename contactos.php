<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user'])) { header('Location: login.php'); exit; }
$pagina_actual = 'contactos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Contactos</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<link rel="stylesheet" href="css/dashboard.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/tesseract.js/4.1.1/tesseract.min.js"></script>
<style>
.fiscal-section {
    margin-top: 8px;
    border-top: 1px solid var(--border);
    padding-top: 14px;
}
.fiscal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    cursor: pointer;
    user-select: none;
}
.fiscal-title {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 6px;
}
.fiscal-badge {
    font-size: 0.6rem;
    padding: 2px 7px;
    border-radius: 999px;
    font-weight: 600;
    background: #dcfce7;
    color: #15803d;
}
.fiscal-badge.incompleto {
    background: #fef9c3;
    color: #854d0e;
}
[data-theme="dark"] .fiscal-badge { background: rgba(34,197,94,0.15); color: #4ade80; }
[data-theme="dark"] .fiscal-badge.incompleto { background: rgba(234,179,8,0.15); color: #fbbf24; }
.fiscal-toggle-icon {
    color: var(--muted);
    transition: transform 0.2s;
    font-size: 0.8rem;
}
.fiscal-toggle-icon.open { transform: rotate(180deg); }
.fiscal-body { display: none; }
.fiscal-body.open { display: flex; flex-direction: column; gap: 12px; }
.scan-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border: 1.5px dashed var(--accent);
    border-radius: var(--radius-sm);
    background: rgba(234,179,8,0.05);
    color: var(--accent);
    font-family: inherit;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: background 0.15s;
}
.scan-btn:hover { background: rgba(234,179,8,0.12); }
.scan-btn svg { width: 16px; height: 16px; flex-shrink: 0; }
.scan-loading {
    display: none;
    flex-direction: column;
    gap: 8px;
    padding: 12px 14px;
    border-radius: var(--radius-sm);
    background: var(--bg);
    border: 1px solid var(--border);
    font-size: 0.78rem;
    color: var(--muted);
}
.scan-loading.show { display: flex; }
.scan-progress-bar {
    width: 100%;
    height: 4px;
    background: var(--border);
    border-radius: 999px;
    overflow: hidden;
}
.scan-progress-fill {
    height: 100%;
    background: var(--accent);
    border-radius: 999px;
    transition: width 0.3s;
    width: 0%;
}
.scan-status { font-size: 0.72rem; color: var(--muted); }
.rfc-input { text-transform: uppercase; }
.fiscal-ok  { color: #15803d; font-size: 0.72rem; font-weight: 600; }
.fiscal-no  { color: var(--muted); font-size: 0.72rem; }
[data-theme="dark"] .fiscal-ok { color: #4ade80; }

.hist-item {
    display:flex; align-items:center; gap:14px;
    padding:12px 14px; border:1px solid var(--border);
    border-radius:var(--radius-sm); background:var(--bg);
    transition:background 0.1s; cursor:pointer;
}
.hist-item:hover { background:var(--surface); }
.hist-tipo-dot {
    width:8px; height:8px; border-radius:50%; flex-shrink:0;
}
.dot-cot { background:#60a5fa; }
.dot-rem { background:#eab308; }
.hist-info { flex:1; min-width:0; }
.hist-folio { font-size:0.72rem; font-weight:700; color:var(--accent); }
.hist-fecha { font-size:0.7rem; color:var(--muted); margin-top:1px; }
.hist-total { font-size:0.88rem; font-weight:700; color:var(--text); flex-shrink:0; }
.hist-badge { font-size:0.62rem; padding:2px 8px; border-radius:999px; font-weight:600; }
.hist-badge.cot { background:rgba(59,130,246,0.12); color:#60a5fa; }
.hist-badge.rem { background:rgba(234,179,8,0.12); color:#fbbf24; }
[data-theme=""] .hist-badge.cot { background:#dbeafe; color:#1d4ed8; }
[data-theme=""] .hist-badge.rem { background:#fef9c3; color:#854d0e; }

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
            <span class="page-title">Contactos</span>
        </div>
        <div class="topbar-right">
            <button class="btn-sm btn-primary" onclick="abrirModal()">+ Nuevo contacto</button>
        </div>
    </header>

    <div class="content">
        <div class="stats-grid stats-3" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="stat-label">Total contactos</div>
                <div class="stat-value" id="stat-total">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="stat-label">Listos para facturar</div>
                <div class="stat-value" id="stat-facturar">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div class="stat-label">Sin datos fiscales</div>
                <div class="stat-value" id="stat-simples">—</div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <span class="table-title">Todos los contactos</span>
                <input type="text" id="buscador" placeholder="Buscar por nombre, RFC o teléfono..."
                    oninput="filtrar()"
                    style="padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
                           font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);
                           outline:none;width:260px;max-width:100%;">
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre / Alias</th>
                            <th>Teléfono</th>
                            <th>RFC</th>
                            <th>Régimen fiscal</th>
                            <th>Datos fiscales</th>
                            <th>Registro</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px;">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<input type="file" id="scan-input" accept="image/*" style="display:none" onchange="procesarConstancia(this)">

<!-- ══ MODAL NUEVO CONTACTO ══ -->
<div class="modal-overlay" id="modal" onclick="clickOverlay(event)">
    <div class="modal" style="max-width:540px;">
        <div class="modal-header">
            <span class="modal-title">Nuevo contacto</span>
            <button class="modal-close" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" id="f-nombre" placeholder="Nombre completo o empresa" autocomplete="off">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" id="f-telefono" placeholder="Opcional">
                </div>
                <div class="form-group">
                    <label>Correo</label>
                    <input type="email" id="f-correo" placeholder="Opcional">
                </div>
            </div>
            <div class="form-group">
                <label>Dirección</label>
                <input type="text" id="f-direccion" placeholder="Opcional">
            </div>
            <div class="form-group">
                <label>Notas</label>
                <textarea id="f-notas" rows="2" placeholder="Opcional"></textarea>
            </div>

            <div class="fiscal-section">
                <div class="fiscal-header" onclick="toggleFiscal('nuevo')">
                    <div class="fiscal-title">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Datos fiscales
                    </div>
                    <span class="fiscal-toggle-icon" id="icon-fiscal-nuevo">▼</span>
                </div>
                <div class="fiscal-body" id="fiscal-nuevo">
                    <button class="scan-btn" onclick="iniciarScan('nuevo')">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        Escanear constancia de situación fiscal
                    </button>
                    <div class="scan-loading" id="scan-loading-nuevo">
                        <div class="scan-status" id="scan-status-nuevo">Iniciando OCR...</div>
                        <div class="scan-progress-bar">
                            <div class="scan-progress-fill" id="scan-progress-nuevo"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alias (nombre corto)</label>
                        <input type="text" id="f-alias" placeholder="Ej. Taller de Juan">
                    </div>
                    <div class="form-group">
                        <label>Razón social</label>
                        <input type="text" id="f-razon" placeholder="Como aparece en la constancia">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>RFC</label>
                            <input type="text" id="f-rfc" placeholder="XAXX010101000" class="rfc-input" oninput="this.value=this.value.toUpperCase()">
                        </div>
                        <div class="form-group">
                            <label>Código Postal</label>
                            <input type="text" id="f-cp" placeholder="00000" maxlength="5">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Régimen fiscal</label>
                        <input type="text" id="f-regimen" placeholder="Ej. 626 - Simplificado de Confianza">
                    </div>
                </div>
            </div>
            <p class="form-hint">Solo el nombre es obligatorio. Los datos fiscales se pueden agregar después.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-sm btn-primary" id="btn-guardar" onclick="guardar()">Guardar contacto</button>
        </div>
    </div>
</div>

<!-- ══ MODAL VER/EDITAR ══ -->
<div class="modal-overlay" id="modal-ver" onclick="clickOverlayVer(event)">
    <div class="modal" style="max-width:540px;">
        <div class="modal-header">
            <span class="modal-title" id="ver-titulo">Detalle del contacto</span>
            <button class="modal-close" onclick="cerrarVer()">&times;</button>
        </div>
        <div class="modal-body" id="detalle-body"></div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-sm btn-outline" style="color:#ef4444;border-color:#fecaca;" onclick="eliminarContacto()">Eliminar</button>
            <div style="display:flex;gap:8px;">
                <button class="btn-sm btn-outline" onclick="cerrarVer()">Cerrar</button>
                <button class="btn-sm btn-outline" onclick="abrirHistorial()">Historial</button>
                <button class="btn-sm btn-primary" id="btn-editar" onclick="guardarEdicion()">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL HISTORIAL CLIENTE ══ -->
<div class="modal-overlay" id="modal-historial" onclick="if(event.target===this)cerrarHistorial()">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <span class="modal-title" id="hist-titulo">Historial</span>
            <button class="modal-close" onclick="cerrarHistorial()">&times;</button>
        </div>
        <div class="modal-body">
            <div style="display:flex;gap:8px;margin-bottom:12px;font-size:0.72rem;color:var(--muted);align-items:center;">
                <span class="hist-badge cot">Cotización</span>
                <span class="hist-badge rem">Remisión</span>
                <span style="margin-left:auto;" id="hist-count"></span>
            </div>
            <div id="hist-lista" style="display:flex;flex-direction:column;gap:6px;max-height:400px;overflow-y:auto;">
                <p style="text-align:center;color:var(--muted);padding:20px 0;font-size:0.82rem;">Cargando...</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarHistorial()">Cerrar</button>
        </div>
    </div>
</div>

<script>
var clientes = [];
var contactoActivoId = null;
var scanDestino = 'nuevo';

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
    fetch('controllers/get_clientes.php')
        .then(function(r){ return r.json(); })
        .then(function(data) {
            clientes = data;
            renderTabla(data);
            renderStats(data);
        })
        .catch(function() {
            document.getElementById('tbody').innerHTML =
                '<tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px;">Error al cargar</td></tr>';
        });
}

function listo_facturar(c) {
    return c.rfc && c.regimen_fiscal && c.codigo_postal;
}

function renderStats(data) {
    document.getElementById('stat-total').textContent    = data.length;
    document.getElementById('stat-facturar').textContent = data.filter(function(c){ return listo_facturar(c); }).length;
    document.getElementById('stat-simples').textContent  = data.filter(function(c){ return !listo_facturar(c); }).length;
}

function renderTabla(lista) {
    var tbody = document.getElementById('tbody');
    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--muted);font-size:0.78rem;padding:40px;">Sin contactos registrados</td></tr>';
        return;
    }
    var html = '';
    lista.forEach(function(c) {
        var nombreDisplay = esc(c.alias || c.nombre);
        var subNombre = c.alias ? '<div style="font-size:0.7rem;color:var(--muted);">' + esc(c.nombre) + '</div>' : '';
        var fiscalOk = listo_facturar(c);
        var regimen = c.regimen_fiscal ? c.regimen_fiscal.substring(0, 30) + (c.regimen_fiscal.length > 30 ? '…' : '') : '—';
        html += '<tr onclick="verContacto(' + c.id + ')" style="cursor:pointer;">' +
        '<td><strong>' + nombreDisplay + '</strong>' + subNombre + '</td>' +
        '<td style="color:var(--muted)">' + (c.telefono || '—') + '</td>' +
        '<td style="color:var(--muted);font-size:0.78rem;">' + (c.rfc || '—') + '</td>' +
        '<td style="color:var(--muted);font-size:0.75rem;">' + regimen + '</td>' +
        '<td>' + (fiscalOk ? '<span class="fiscal-ok">✓ Completo</span>' : '<span class="fiscal-no">Pendiente</span>') + '</td>' +
        '<td style="color:var(--muted);font-size:0.75rem;">' + (c.created_at ? c.created_at.slice(0,10) : '—') + '</td>' +
        '</tr>';
    });
    tbody.innerHTML = html;
}

function filtrar() {
    var q = document.getElementById('buscador').value.toLowerCase();
    renderTabla(clientes.filter(function(c) {
        return c.nombre.toLowerCase().includes(q) ||
            (c.alias    || '').toLowerCase().includes(q) ||
            (c.rfc      || '').toLowerCase().includes(q) ||
            (c.telefono || '').toLowerCase().includes(q);
    }));
}

// ── Toggle datos fiscales ──
function toggleFiscal(cual) {
    var body = document.getElementById('fiscal-' + cual);
    var icon = document.getElementById('icon-fiscal-' + cual);
    var isOpen = body.classList.contains('open');
    body.classList.toggle('open', !isOpen);
    icon.classList.toggle('open', !isOpen);
}

// ── Escanear constancia con Tesseract.js ──
function iniciarScan(destino) {
    scanDestino = destino;
    document.getElementById('scan-input').value = '';
    document.getElementById('scan-input').click();
}

function procesarConstancia(input) {
    var file = input.files[0];
    if (!file) return;

    var loadingId  = 'scan-loading-'  + scanDestino;
    var statusId   = 'scan-status-'   + scanDestino;
    var progressId = 'scan-progress-' + scanDestino;

    document.getElementById(loadingId).classList.add('show');
    document.getElementById(statusId).textContent  = 'Cargando imagen...';
    document.getElementById(progressId).style.width = '0%';

    Tesseract.recognize(file, 'spa', {
        logger: function(m) {
            if (m.status === 'recognizing text') {
                var pct = Math.round(m.progress * 100);
                document.getElementById(progressId).style.width = pct + '%';
                document.getElementById(statusId).textContent = 'Leyendo texto... ' + pct + '%';
            }
        }
    }).then(function(result) {
        document.getElementById(loadingId).classList.remove('show');
        var texto = result.data.text;
        var datos = extraerDatosFiscales(texto);
        rellenarDatosFiscales(scanDestino, datos);

        if (!datos.rfc && !datos.codigo_postal) {
            alert('No se encontraron datos fiscales. Asegúrate de que la imagen sea clara y legible.');
        }
    }).catch(function(err) {
        document.getElementById(loadingId).classList.remove('show');
        alert('Error al leer la imagen. Intenta con una imagen más clara.');
        console.error(err);
    });
}

function extraerDatosFiscales(texto) {
    var datos = { nombre: '', rfc: '', regimen_fiscal: '', codigo_postal: '' };

    // RFC — buscar patrón alfanumérico de 12-13 caracteres
    var rfcMatch = texto.match(/[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}/i);
    if (rfcMatch) datos.rfc = rfcMatch[0].toUpperCase();

    // CP — cualquier número de 5 dígitos entre 10000 y 99999
    var cpMatches = texto.match(/\b\d{5}\b/g);
    if (cpMatches) {
        for (var i = 0; i < cpMatches.length; i++) {
            var n = parseInt(cpMatches[i]);
            if (n >= 10000 && n <= 99999) {
                datos.codigo_postal = cpMatches[i];
                break;
            }
        }
    }

    // Razón social — buscar línea después de "Denom" o "Social" aunque esté mal escrito
    var razonMatch = texto.match(/Denom[a-z\s]*[:\|]\s*([A-ZÁÉÍÓÚÑ\s]{5,})/i);
    if (razonMatch) datos.nombre = razonMatch[1].trim();

    // Régimen — buscar "Regimen" aunque esté cortado
    var regimenMatch = texto.match(/R[eé]g[a-z\s]*[:\|]\s*([^\n\r]{4,})/i);
    if (regimenMatch) datos.regimen_fiscal = regimenMatch[1].trim();

    return datos;
}

function rellenarDatosFiscales(destino, datos) {
    var prefijo = destino === 'nuevo' ? 'f' : 'e';

    if (datos.nombre) {
        var nombreEl = document.getElementById(prefijo + '-nombre');
        if (nombreEl && !nombreEl.value) nombreEl.value = datos.nombre;
        var razonEl = document.getElementById(prefijo + '-razon');
        if (razonEl) razonEl.value = datos.nombre;
    }
    if (datos.rfc) {
        var rfcEl = document.getElementById(prefijo + '-rfc');
        if (rfcEl) rfcEl.value = datos.rfc.toUpperCase();
    }
    if (datos.regimen_fiscal) {
        var regimenEl = document.getElementById(prefijo + '-regimen');
        if (regimenEl) regimenEl.value = datos.regimen_fiscal;
    }
    if (datos.codigo_postal) {
        var cpEl = document.getElementById(prefijo + '-cp');
        if (cpEl) cpEl.value = datos.codigo_postal;
    }

    // Abrir sección fiscal si estaba cerrada
    var body = document.getElementById('fiscal-' + destino);
    if (body && !body.classList.contains('open')) toggleFiscal(destino);
}

// ── Modal nuevo ──
function abrirModal() {
    ['f-nombre','f-telefono','f-correo','f-direccion','f-notas',
     'f-alias','f-razon','f-rfc','f-cp','f-regimen'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('f-nombre').style.borderColor = '';
    document.getElementById('btn-guardar').disabled = false;
    document.getElementById('btn-guardar').textContent = 'Guardar contacto';
    document.getElementById('fiscal-nuevo').classList.remove('open');
    document.getElementById('icon-fiscal-nuevo').classList.remove('open');
    document.getElementById('scan-loading-nuevo').classList.remove('show');
    document.getElementById('modal').classList.add('open');
    setTimeout(function(){ document.getElementById('f-nombre').focus(); }, 100);
}

function cerrarModal() { document.getElementById('modal').classList.remove('open'); }
function clickOverlay(e) { if (e.target === document.getElementById('modal')) cerrarModal(); }

function guardar() {
    var nombre = document.getElementById('f-nombre').value.trim();
    if (!nombre) {
        document.getElementById('f-nombre').style.borderColor = '#ef4444';
        document.getElementById('f-nombre').focus();
        return;
    }
    document.getElementById('f-nombre').style.borderColor = '';

    var btn = document.getElementById('btn-guardar');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    var fd = new FormData();
    fd.append('nombre',         nombre);
    fd.append('telefono',       document.getElementById('f-telefono').value.trim());
    fd.append('correo',         document.getElementById('f-correo').value.trim());
    fd.append('direccion',      document.getElementById('f-direccion').value.trim());
    fd.append('notas',          document.getElementById('f-notas').value.trim());
    fd.append('alias',          document.getElementById('f-alias').value.trim());
    fd.append('razon_social',   document.getElementById('f-razon').value.trim());
    fd.append('rfc',            document.getElementById('f-rfc').value.trim());
    fd.append('codigo_postal',  document.getElementById('f-cp').value.trim());
    fd.append('regimen_fiscal', document.getElementById('f-regimen').value.trim());

    fetch('controllers/add_cliente.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarModal(); cargar(); }
            else {
                alert('Error: ' + res.msg);
                btn.disabled = false;
                btn.textContent = 'Guardar contacto';
            }
        });
}

// ── Modal ver/editar ──
function verContacto(id) {
    contactoActivoId = id;
    var c = clientes.find(function(x){ return x.id == id; });
    if (!c) return;

    document.getElementById('ver-titulo').textContent = c.alias || c.nombre;
    var fiscalOk = listo_facturar(c);

    document.getElementById('detalle-body').innerHTML =
        '<div class="form-group">' +
            '<label>Nombre *</label>' +
            '<input type="text" id="e-nombre" value="' + esc(c.nombre) + '">' +
        '</div>' +
        '<div class="form-row">' +
            '<div class="form-group"><label>Teléfono</label><input type="text" id="e-telefono" value="' + esc(c.telefono||'') + '"></div>' +
            '<div class="form-group"><label>Correo</label><input type="email" id="e-correo" value="' + esc(c.correo||'') + '"></div>' +
        '</div>' +
        '<div class="form-group"><label>Dirección</label><input type="text" id="e-direccion" value="' + esc(c.direccion||'') + '"></div>' +
        '<div class="form-group"><label>Notas</label><textarea id="e-notas" rows="2">' + esc(c.notas||'') + '</textarea></div>' +

        '<div class="fiscal-section">' +
            '<div class="fiscal-header" onclick="toggleFiscal(\'editar\')">' +
                '<div class="fiscal-title">' +
                    '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>' +
                    'Datos fiscales <span class="fiscal-badge ' + (fiscalOk ? '' : 'incompleto') + '">' + (fiscalOk ? 'Completo' : 'Pendiente') + '</span>' +
                '</div>' +
                '<span class="fiscal-toggle-icon" id="icon-fiscal-editar">▼</span>' +
            '</div>' +
            '<div class="fiscal-body" id="fiscal-editar">' +
                '<button class="scan-btn" onclick="iniciarScan(\'editar\')">' +
                    '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>' +
                    'Escanear constancia de situación fiscal' +
                '</button>' +
                '<div class="scan-loading" id="scan-loading-editar">' +
                    '<div class="scan-status" id="scan-status-editar">Iniciando OCR...</div>' +
                    '<div class="scan-progress-bar"><div class="scan-progress-fill" id="scan-progress-editar"></div></div>' +
                '</div>' +
                '<div class="form-group"><label>Alias (nombre corto)</label><input type="text" id="e-alias" value="' + esc(c.alias||'') + '" placeholder="Ej. Taller de Juan"></div>' +
                '<div class="form-group"><label>Razón social</label><input type="text" id="e-razon" value="' + esc(c.razon_social||'') + '" placeholder="Como aparece en la constancia"></div>' +
                '<div class="form-row">' +
                    '<div class="form-group"><label>RFC</label><input type="text" id="e-rfc" value="' + esc(c.rfc||'') + '" placeholder="XAXX010101000" class="rfc-input" oninput="this.value=this.value.toUpperCase()"></div>' +
                    '<div class="form-group"><label>Código Postal</label><input type="text" id="e-cp" value="' + esc(c.codigo_postal||'') + '" placeholder="00000" maxlength="5"></div>' +
                '</div>' +
                '<div class="form-group"><label>Régimen fiscal</label><input type="text" id="e-regimen" value="' + esc(c.regimen_fiscal||'') + '" placeholder="Ej. 626 - Simplificado de Confianza"></div>' +
            '</div>' +
        '</div>';

    if (fiscalOk || c.rfc) {
        setTimeout(function(){ toggleFiscal('editar'); }, 50);
    }

    document.getElementById('modal-ver').classList.add('open');
}

function cerrarVer() { document.getElementById('modal-ver').classList.remove('open'); }
function clickOverlayVer(e) { if (e.target === document.getElementById('modal-ver')) cerrarVer(); }

function guardarEdicion() {
    var nombre = document.getElementById('e-nombre').value.trim();
    if (!nombre) {
        document.getElementById('e-nombre').style.borderColor = '#ef4444';
        document.getElementById('e-nombre').focus();
        return;
    }

    var btn = document.getElementById('btn-editar');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    var fd = new FormData();
    fd.append('id',             contactoActivoId);
    fd.append('nombre',         nombre);
    fd.append('telefono',       document.getElementById('e-telefono').value.trim());
    fd.append('correo',         document.getElementById('e-correo').value.trim());
    fd.append('direccion',      document.getElementById('e-direccion').value.trim());
    fd.append('notas',          document.getElementById('e-notas').value.trim());
    fd.append('alias',          document.getElementById('e-alias').value.trim());
    fd.append('razon_social',   document.getElementById('e-razon').value.trim());
    fd.append('rfc',            document.getElementById('e-rfc').value.trim());
    fd.append('codigo_postal',  document.getElementById('e-cp').value.trim());
    fd.append('regimen_fiscal', document.getElementById('e-regimen').value.trim());

    fetch('controllers/update_cliente.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarVer(); cargar(); }
            else {
                alert('Error: ' + res.msg);
                btn.disabled = false;
                btn.textContent = 'Guardar cambios';
            }
        });
}

function eliminarContacto() {
    if (!contactoActivoId) return;
    var c = clientes.find(function(x){ return x.id == contactoActivoId; });
    if (!confirm('¿Eliminar a "' + (c ? c.nombre : '') + '"?')) return;

    var fd = new FormData();
    fd.append('id', contactoActivoId);

    fetch('controllers/eliminar_cliente.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarVer(); cargar(); }
            else alert('Error: ' + res.msg);
        });
}

function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Historial cliente ──
function abrirHistorial() {
    var c = clientes.find(function(x){ return x.id == contactoActivoId; });
    if (!c) return;
    document.getElementById('hist-titulo').textContent = 'Historial — ' + (c.alias || c.nombre);
    document.getElementById('hist-count').textContent = '';
    document.getElementById('hist-lista').innerHTML = '<p style="text-align:center;color:var(--muted);padding:20px 0;font-size:0.82rem;">Cargando...</p>';
    document.getElementById('modal-historial').classList.add('open');

    fetch('controllers/get_historial_cliente.php?nombre=' + encodeURIComponent(c.nombre))
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (!res.ok) { document.getElementById('hist-lista').innerHTML = '<p style="text-align:center;color:var(--muted);padding:20px 0;">Error al cargar</p>'; return; }
            renderHistorial(res.data);
        });
}

function renderHistorial(lista) {
    document.getElementById('hist-count').textContent = lista.length + ' registro' + (lista.length !== 1 ? 's' : '');
    if (!lista.length) {
        document.getElementById('hist-lista').innerHTML = '<p style="text-align:center;color:var(--muted);padding:30px 0;font-size:0.82rem;">Sin cotizaciones ni remisiones registradas</p>';
        return;
    }
    var html = lista.map(function(item) {
        var esCot  = item.tipo === 'cotizacion';
        var fecha  = new Date((item.created_at||'').replace(' ','T'));
        var fStr   = fecha.toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'});
        var hStr   = fecha.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'});
        var modLabel = '';
        if (!esCot) {
            var mod = item.modalidad || 'completo';
            modLabel = ' · ' + (mod==='completo' ? 'Contado' : mod==='fijo' ? 'Crédito fijo' : 'Crédito variable');
        }
        var link = esCot
            ? 'cotizaciones.php?open=' + item.id
            : 'remisiones.php?open=' + item.id;
        return '<div class="hist-item" onclick="window.location.href=\'' + link + '\'">' +
            '<div class="hist-tipo-dot ' + (esCot ? 'dot-cot' : 'dot-rem') + '"></div>' +
            '<div class="hist-info">' +
                '<div class="hist-folio">' + esc(item.folio || '—') + '</div>' +
                '<div class="hist-fecha">' + fStr + ' · ' + hStr + modLabel + '</div>' +
            '</div>' +
            '<span class="hist-badge ' + (esCot ? 'cot' : 'rem') + '">' + (esCot ? 'Cotización' : 'Remisión') + '</span>' +
            '<div class="hist-total">' + fmt(item.total) + '</div>' +
        '</div>';
    }).join('');
    document.getElementById('hist-lista').innerHTML = html;
}

function cerrarHistorial() {
    document.getElementById('modal-historial').classList.remove('open');
}

function fmt(n) { return '$' + parseFloat(n).toFixed(2); }
cargar();
</script>
</body>
</html>