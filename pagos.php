<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user'])) { header('Location: login.php'); exit; }
$pagina_actual = 'pagos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Historial de Pagos</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<link rel="stylesheet" href="css/dashboard.css">
<style>
.pago-list { display:flex; flex-direction:column; gap:8px; }

.pago-item {
    background:var(--surface); border:1px solid var(--border);
    border-radius:var(--radius); display:flex; align-items:center;
    gap:16px; padding:14px 18px; transition:box-shadow 0.15s; cursor:pointer;
}
.pago-item:hover { box-shadow:var(--shadow); background:var(--bg); }

.pago-metodo-icon {
    width:40px; height:40px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; font-size:1.2rem;
}
.metodo-efectivo      { background:rgba(34,197,94,0.15); }
.metodo-transferencia { background:rgba(59,130,246,0.15); }
.metodo-tarjeta       { background:rgba(168,85,247,0.15); }

.pago-info { flex:1; min-width:0; }
.pago-cliente { font-size:0.88rem; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.pago-detalle { font-size:0.72rem; color:var(--muted); margin-top:2px; }

.pago-right { display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0; }
.pago-monto { font-size:0.95rem; font-weight:700; color:var(--text); white-space:nowrap; }

.metodo-badge {
    font-size:0.62rem; padding:2px 8px;
    border-radius:999px; font-weight:600;
}
.badge-efectivo      { background:rgba(34,197,94,0.15); color:#4ade80; }
.badge-transferencia { background:rgba(59,130,246,0.15); color:#60a5fa; }
.badge-tarjeta       { background:rgba(168,85,247,0.15); color:#c084fc; }
[data-theme=""] .badge-efectivo      { background:#dcfce7; color:#15803d; }
[data-theme=""] .badge-transferencia { background:#dbeafe; color:#1d4ed8; }
[data-theme=""] .badge-tarjeta       { background:#f3e8ff; color:#7c3aed; }

.modalidad-badge {
    font-size:0.62rem; padding:2px 8px;
    border-radius:999px; font-weight:600;
}
.mod-completo  { background:rgba(34,197,94,0.12); color:#4ade80; }
.mod-fijo      { background:rgba(234,179,8,0.12); color:#fbbf24; }
.mod-variable  { background:rgba(59,130,246,0.12); color:#60a5fa; }
[data-theme=""] .mod-completo { background:#dcfce7; color:#15803d; }
[data-theme=""] .mod-fijo     { background:#fef9c3; color:#854d0e; }
[data-theme=""] .mod-variable { background:#dbeafe; color:#1d4ed8; }

.prog-bar { height:5px; border-radius:999px; background:var(--border); overflow:hidden; margin-top:4px; width:120px; }
.prog-fill { height:100%; border-radius:999px; background:linear-gradient(90deg,#22c55e,#16a34a); }

/* Doc selector */
.doc-sel-item {
    display:flex; align-items:center; gap:12px;
    padding:10px 14px; border:1px solid var(--border);
    border-radius:var(--radius-sm); cursor:pointer;
    background:var(--surface); transition:background 0.1s;
}
.doc-sel-item:hover { background:var(--bg); border-color:var(--accent); }
.doc-sel-folio { font-size:0.72rem; font-weight:700; color:var(--accent); min-width:80px; }
.doc-sel-info { flex:1; min-width:0; }
.doc-sel-cliente { font-size:0.82rem; font-weight:600; color:var(--text); }
.doc-sel-sub { font-size:0.68rem; color:var(--muted); margin-top:2px; }

.ep-badge { font-size:0.6rem; padding:2px 7px; border-radius:999px; font-weight:600; }
.ep-pendiente { background:rgba(239,68,68,0.12); color:#f87171; }
.ep-parcial   { background:rgba(234,179,8,0.12); color:#fbbf24; }
.ep-pagado    { background:rgba(34,197,94,0.12); color:#4ade80; }
[data-theme=""] .ep-pendiente { background:#fee2e2; color:#b91c1c; }
[data-theme=""] .ep-parcial   { background:#fef9c3; color:#854d0e; }
[data-theme=""] .ep-pagado    { background:#dcfce7; color:#15803d; }

.pendiente-bar { height:5px; border-radius:999px; background:var(--border); overflow:hidden; margin-top:5px; }
.pendiente-fill { height:100%; border-radius:999px; background:linear-gradient(90deg,#22c55e,#16a34a); }

/* Abonos */
.abono-row {
    display:flex; align-items:center; gap:12px;
    padding:10px 14px; border:1px solid var(--border);
    border-radius:var(--radius-sm); background:var(--bg);
}
.abono-num { font-size:0.72rem; font-weight:700; color:var(--muted); min-width:24px; }
.abono-info { flex:1; font-size:0.82rem; color:var(--text); }
.abono-monto { font-size:0.88rem; font-weight:700; color:var(--text); }

/* Mensualidades preview */
.mens-list { display:flex; flex-direction:column; gap:6px; max-height:200px; overflow-y:auto; }
.mens-item {
    display:flex; align-items:center; justify-content:space-between;
    padding:8px 12px; border:1px solid var(--border);
    border-radius:var(--radius-sm); background:var(--bg);
    font-size:0.82rem;
}
.mens-item.pagada { border-color:rgba(34,197,94,0.3); background:rgba(34,197,94,0.05); }
.mens-num { font-weight:600; color:var(--muted); font-size:0.72rem; }
.mens-monto { font-weight:700; color:var(--text); }
.mens-estado { font-size:0.65rem; font-weight:600; }

.toggle-modalidad {
    display:flex; gap:6px; margin-bottom:14px;
}
.toggle-modalidad button { flex:1; }

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
            <span class="page-title">Historial de pagos</span>
        </div>
        <div class="topbar-right">
            <button class="btn-sm btn-outline" onclick="abrirDesdeCotizacion()" style="margin-right:6px;">Desde cotización</button>
            <button class="btn-sm btn-primary" onclick="abrirNuevo()">+ Registrar pago</button>
        </div>
    </header>

    <div class="content">
        <div class="stats-grid stats-4" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="stat-label">Total cobrado</div>
                <div class="stat-value" id="stat-total">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div class="stat-label">Este mes</div>
                <div class="stat-value" id="stat-mes">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="stat-label">Por cobrar</div>
                <div class="stat-value" id="stat-pendiente">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-label">Diferidos activos</div>
                <div class="stat-value" id="stat-diferidos">—</div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <span class="table-title">Todos los pagos</span>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <select id="filtro-modalidad" onchange="filtrar()"
                        style="padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);outline:none;">
                        <option value="">Todas las modalidades</option>
                        <option value="completo">Completo</option>
                        <option value="fijo">Diferido fijo</option>
                        <option value="variable">Diferido variable</option>
                    </select>
                    <select id="filtro-metodo" onchange="filtrar()"
                        style="padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);outline:none;">
                        <option value="">Todos los métodos</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                    </select>
                    <input type="text" id="buscador" placeholder="Buscar cliente o folio..."
                        oninput="filtrar()"
                        style="padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);outline:none;width:200px;max-width:100%;">
                </div>
            </div>
            <div style="padding:16px;" id="pago-contenedor">
                <p style="text-align:center;color:var(--muted);padding:40px 0;font-size:0.82rem;">Cargando...</p>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL NUEVO PAGO ══ -->
<div class="modal-overlay" id="modal-nuevo" onclick="clickOverlay(event)">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <span class="modal-title">Registrar pago</span>
            <button class="modal-close" onclick="cerrarNuevo()">&times;</button>
        </div>
        <div class="modal-body">

            <!-- Modalidad -->
            <div style="margin-bottom:4px;">
                <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted);margin-bottom:8px;">Modalidad de pago</div>
                <div class="toggle-modalidad">
                    <button class="btn-sm btn-primary"  id="mod-btn-completo"  onclick="setModalidad('completo')">Completo</button>
                    <button class="btn-sm btn-outline"  id="mod-btn-fijo"      onclick="setModalidad('fijo')">Diferido fijo</button>
                    <button class="btn-sm btn-outline"  id="mod-btn-variable"  onclick="setModalidad('variable')">Diferido variable</button>
                </div>
                <p id="mod-desc" style="font-size:0.72rem;color:var(--muted);margin-bottom:10px;">Pago único por el total del documento.</p>
            </div>

            <!-- Documento relacionado -->
            <div class="form-group">
                <label>Documento relacionado (opcional)</label>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="n-doc-display" placeholder="Sin documento / pago general" readonly
                        style="flex:1;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.82rem;cursor:pointer;"
                        onclick="abrirSelDoc()">
                    <button class="btn-sm btn-outline" onclick="abrirSelDoc()">Buscar</button>
                    <button class="btn-sm btn-outline" onclick="limpiarDoc()">✕</button>
                </div>
                <div id="doc-pendiente-preview" style="display:none;margin-top:8px;"></div>
            </div>

            <!-- Cliente -->
            <div class="form-group" style="position:relative;">
                <label>Cliente *</label>
                <input type="text" id="n-cliente" placeholder="Nombre del cliente" autocomplete="off" oninput="filtrarClientesPago()">
                <div id="clientes-pago-dropdown" style="display:none;position:absolute;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);z-index:100;max-height:160px;overflow-y:auto;width:100%;box-shadow:var(--shadow);top:100%;left:0;"></div>
            </div>

            <!-- Monto total -->
            <div class="form-row">
                <div class="form-group">
                    <label id="lbl-monto">Monto *</label>
                    <input type="number" id="n-monto" placeholder="0.00" min="0" step="0.01" oninput="onMontoChange()">
                </div>
                <div class="form-group">
                    <label>Método de pago *</label>
                    <select id="n-metodo" style="padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.85rem;outline:none;width:100%;">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                    </select>
                </div>
            </div>

            <!-- Opciones diferido fijo -->
            <div id="sec-fijo" style="display:none;">
                <div class="form-group">
                    <label>Número de mensualidades *</label>
                    <input type="number" id="n-mensualidades" placeholder="Ej. 3" min="2" max="60" step="1" oninput="calcularMensualidades()">
                </div>
                <div id="mens-preview" style="display:none;margin-top:8px;">
                    <div style="font-size:0.72rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px;">Plan de pagos</div>
                    <div class="mens-list" id="mens-lista"></div>
                </div>
            </div>

            <!-- Opciones diferido variable -->
            <div id="sec-variable" style="display:none;">
                <div style="padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.78rem;color:var(--muted);margin-bottom:4px;">
                    El cliente irá haciendo abonos de montos variables. El monto ingresado es el total a diferir.
                </div>
            </div>

            <!-- Recordatorio -->
            <div id="sec-recordatorio" style="display:none;margin-top:4px;">
                <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);">
                    <span style="font-size:0.82rem;color:var(--text);flex:1;"><strong>Activar recordatorio</strong> — Guardar teléfono para avisar al cliente</span>
                    <div class="toggle-track" id="rec-track" onclick="toggleRecordatorio()" style="cursor:pointer;">
                        <div class="toggle-thumb"></div>
                    </div>
                </div>
                <div id="sec-telefono" style="display:none;margin-top:8px;">
                    <div class="form-group">
                        <label>Teléfono para recordatorio</label>
                        <input type="text" id="n-telefono" placeholder="Ej. 8331234567">
                    </div>
                </div>
            </div>

            <!-- Referencia y notas -->
            <div class="form-group">
                <label>Referencia (opcional)</label>
                <input type="text" id="n-referencia" placeholder="Ej. Folio de transferencia">
            </div>
            <div class="form-group">
                <label>Notas (opcional)</label>
                <input type="text" id="n-notas" placeholder="Ej. Primer abono">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarNuevo()">Cancelar</button>
            <button class="btn-sm btn-primary" id="btn-guardar-pago" onclick="guardarPago()">Registrar pago</button>
        </div>
    </div>
</div>

<!-- ══ MODAL SELECTOR DOCUMENTO ══ -->
<div class="modal-overlay" id="modal-sel-doc" onclick="if(event.target===this)cerrarSelDoc()">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <span class="modal-title">Seleccionar documento</span>
            <button class="modal-close" onclick="cerrarSelDoc()">&times;</button>
        </div>
        <div class="modal-body">
            <div style="display:flex;gap:8px;margin-bottom:10px;">
                <button class="btn-sm btn-primary"  id="tab-facturas"      onclick="setTab('facturas')"      style="flex:1;">Facturas</button>
                <button class="btn-sm btn-outline"  id="tab-cotizaciones"  onclick="setTab('cotizaciones')"  style="flex:1;">Cotizaciones</button>
            </div>
            <input type="text" id="doc-buscar" placeholder="Buscar..." oninput="filtrarDocs()"
                style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.82rem;outline:none;margin-bottom:10px;">
            <div id="doc-lista" style="max-height:320px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;"></div>
        </div>
    </div>
</div>

<!-- ══ MODAL VER PAGO ══ -->
<div class="modal-overlay" id="modal-ver" onclick="if(event.target===this)cerrarVer()">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <span class="modal-title" id="ver-titulo">Detalle del pago</span>
            <button class="modal-close" onclick="cerrarVer()">&times;</button>
        </div>
        <div class="modal-body" id="modal-ver-body"></div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-sm btn-outline" style="color:#ef4444;border-color:#fecaca;" onclick="eliminarPago()">Eliminar</button>
            <div style="display:flex;gap:8px;">
                <button class="btn-sm btn-outline" onclick="cerrarVer()">Cerrar</button>
                
                <button class="btn-sm btn-outline" id="btn-ticket" onclick="imprimirTicket()" style="display:none;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:4px;">
                        <path d="M6 9V2h12v7"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    Nota de remisión
                </button>
                
                <button class="btn-sm btn-outline" id="btn-editar-pago" onclick="abrirEditarPago()">⋮</button>
                <button class="btn-sm btn-primary" id="btn-abono" style="display:none;" onclick="abrirAbono()">+ Registrar abono</button>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL NUEVO ABONO ══ -->
<div class="modal-overlay" id="modal-abono" onclick="if(event.target===this)cerrarAbono()">
    <div class="modal" style="max-width:380px;">
        <div class="modal-header">
            <span class="modal-title">Registrar abono</span>
            <button class="modal-close" onclick="cerrarAbono()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="abono-pendiente-info" style="padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.82rem;margin-bottom:4px;"></div>
            <div class="form-group">
                <label>Monto del abono *</label>
                <input type="number" id="ab-monto" placeholder="0.00" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label>Método</label>
                <select id="ab-metodo" style="padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.85rem;outline:none;width:100%;">
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="tarjeta">Tarjeta</option>
                </select>
            </div>
            <div class="form-group">
                <label>Referencia (opcional)</label>
                <input type="text" id="ab-referencia" placeholder="Folio, últimos 4 dígitos...">
            </div>
            <div class="form-group">
                <label>Notas (opcional)</label>
                <input type="text" id="ab-notas" placeholder="Ej. Segundo abono">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarAbono()">Cancelar</button>
            
            <button class="btn-sm btn-outline" id="btn-ticket-abono" onclick="imprimirTicket()" style="display:none;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:4px;">
                    <path d="M6 9V2h12v7"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Nota de remisión
            </button>
            
            <button class="btn-sm btn-primary" id="btn-guardar-abono" onclick="guardarAbono()">Guardar abono</button>
        </div>
    </div>
</div>

<!-- ══ MODAL EDITAR PAGO ══ -->
<div class="modal-overlay" id="modal-editar-pago" onclick="if(event.target===this)cerrarEditarPago()">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <span class="modal-title">Editar pago</span>
            <button class="modal-close" onclick="cerrarEditarPago()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Monto *</label>
                <input type="number" id="ep-monto" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label>Método</label>
                <select id="ep-metodo" style="padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.85rem;outline:none;width:100%;">
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="tarjeta">Tarjeta</option>
                </select>
            </div>
            <div class="form-group">
                <label>Referencia (opcional)</label>
                <input type="text" id="ep-referencia" placeholder="Folio, últimos 4 dígitos...">
            </div>
            <div class="form-group">
                <label>Notas (opcional)</label>
                <input type="text" id="ep-notas">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarEditarPago()">Cancelar</button>
            <button class="btn-sm btn-primary" id="btn-guardar-ep" onclick="guardarEditarPago()">Guardar cambios</button>
        </div>
    </div>
</div>

<!-- ══ MODAL EDITAR ABONO ══ -->
<div class="modal-overlay" id="modal-editar-abono" onclick="if(event.target===this)cerrarEditarAbono()">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <span class="modal-title">Editar abono</span>
            <button class="modal-close" onclick="cerrarEditarAbono()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="ea-id">
            <div class="form-group">
                <label>Monto *</label>
                <input type="number" id="ea-monto" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label>Método</label>
                <select id="ea-metodo" style="padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.85rem;outline:none;width:100%;">
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="tarjeta">Tarjeta</option>
                </select>
            </div>
            <div class="form-group">
                <label>Referencia (opcional)</label>
                <input type="text" id="ea-referencia">
            </div>
            <div class="form-group">
                <label>Notas (opcional)</label>
                <input type="text" id="ea-notas">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarEditarAbono()">Cancelar</button>
            <button class="btn-sm btn-primary" id="btn-guardar-ea" onclick="guardarEditarAbono()">Guardar cambios</button>
        </div>
    </div>
</div>

<script>
var pagos         = [];
var facturas      = [];
var cotizaciones  = [];
var clientesLista = [];
var modalidad     = 'completo';
var tabActual     = 'facturas';
var docSel        = null;
var pagoActivoId  = null;
var pagoActivo    = null;
var recordatorio  = false;

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
    Promise.all([
        fetch('controllers/get_pagos.php').then(function(r){ return r.json(); }),
        fetch('controllers/get_facturas.php').then(function(r){ return r.json(); }),
        fetch('controllers/get_cotizaciones.php').then(function(r){ return r.json(); }),
        fetch('controllers/get_clientes.php').then(function(r){ return r.json(); })
    ]).then(function(results) {
        pagos         = results[0];
        facturas      = results[1];
        cotizaciones  = results[2];
        clientesLista = results[3];
        renderLista(pagos);
        renderStats(pagos);
    }).catch(function(err){ console.error('Error:', err); });
}

function renderStats(data) {
    var hoy = new Date(); var mes = hoy.getMonth(); var anio = hoy.getFullYear();

    var totalCobrado = data.reduce(function(a,p){ return a + parseFloat(p.monto_cobrado || 0); }, 0);

    var esMes = data.filter(function(p) {
        var f = new Date(p.created_at);
        return f.getMonth() === mes && f.getFullYear() === anio;
    }).reduce(function(a,p){ return a + parseFloat(p.monto_cobrado || 0); }, 0);

    var diferidos = data.filter(function(p){
        return (p.modalidad === 'fijo' || p.modalidad === 'variable') &&
               parseFloat(p.monto_cobrado || 0) < parseFloat(p.monto);
    }).length;

    var pendiente = data.reduce(function(a,p){
        return a + Math.max(0, parseFloat(p.monto) - parseFloat(p.monto_cobrado || 0));
    }, 0);

    document.getElementById('stat-total').textContent     = fmt(totalCobrado);
    document.getElementById('stat-mes').textContent       = fmt(esMes);
    document.getElementById('stat-pendiente').textContent = fmt(pendiente);
    document.getElementById('stat-diferidos').textContent = diferidos;
}

var metodoIconos = { efectivo:'💵', transferencia:'🏦', tarjeta:'💳' };
var modLabels    = { completo:'Completo', fijo:'Diferido fijo', variable:'Diferido variable' };

function renderLista(lista) {
    var cont = document.getElementById('pago-contenedor');
    if (!lista.length) {
        cont.innerHTML = '<p style="text-align:center;color:var(--muted);padding:40px 0;font-size:0.82rem;">Sin pagos registrados</p>';
        return;
    }
    var html = '<div class="pago-list">';
    lista.forEach(function(p) {
        var fecha     = new Date(p.created_at);
        var fechaStr  = fecha.toLocaleDateString('es-MX', {day:'2-digit',month:'short',year:'numeric'});
        var cobrado   = parseFloat(p.monto_cobrado || 0);
        var total     = parseFloat(p.monto);
        var pendiente = Math.max(0, total - cobrado);
        var pct       = total > 0 ? Math.min(100, Math.round(cobrado / total * 100)) : 100;
        var esDif     = p.modalidad === 'fijo' || p.modalidad === 'variable';
        var docStr    = p.folio_doc ? p.folio_doc : 'Pago general';

        html += '<div class="pago-item" onclick="verPago(' + p.id + ')">' +
            '<div class="pago-metodo-icon metodo-' + p.metodo + '">' + (metodoIconos[p.metodo]||'💰') + '</div>' +
            '<div class="pago-info">' +
                '<div class="pago-cliente">' + esc(p.nombre_cliente || '—') + '</div>' +
                '<div class="pago-detalle">' + docStr + ' · ' + fechaStr + '</div>' +
                (esDif ? '<div class="prog-bar"><div class="prog-fill" style="width:' + pct + '%"></div></div>' : '') +
            '</div>' +
            '<div class="pago-right">' +
                '<div class="pago-monto">' + fmt(esDif ? cobrado : total) + (esDif ? '<span style="font-size:0.68rem;color:var(--muted);font-weight:400;"> / ' + fmt(total) + '</span>' : '') + '</div>' +
                '<span class="modalidad-badge mod-' + p.modalidad + '">' + modLabels[p.modalidad] + '</span>' +
                '<span class="metodo-badge badge-' + p.metodo + '">' + p.metodo.charAt(0).toUpperCase() + p.metodo.slice(1) + '</span>' +
            '</div>' +
        '</div>';
    });
    html += '</div>';
    cont.innerHTML = html;
}

function filtrar() {
    var q   = document.getElementById('buscador').value.toLowerCase();
    var mod = document.getElementById('filtro-modalidad').value;
    var met = document.getElementById('filtro-metodo').value;
    renderLista(pagos.filter(function(p) {
        var matchQ = !q || (p.nombre_cliente||'').toLowerCase().includes(q) || (p.folio_doc||'').toLowerCase().includes(q);
        var matchM = !mod || p.modalidad === mod;
        var matchE = !met || p.metodo === met;
        return matchQ && matchM && matchE;
    }));
}

// ── Nuevo pago ──
function abrirNuevo() {
    modalidad = 'completo'; docSel = null; recordatorio = false;
    ['n-cliente','n-monto','n-mensualidades','n-referencia','n-notas','n-telefono','n-doc-display'].forEach(function(id){
        var el = document.getElementById(id); if(el) el.value = '';
    });
    document.getElementById('doc-pendiente-preview').style.display = 'none';
    document.getElementById('rec-track').classList.remove('on');
    document.getElementById('sec-telefono').style.display = 'none';
    document.getElementById('mens-preview').style.display = 'none';
    document.getElementById('btn-guardar-pago').disabled = false;
    document.getElementById('btn-guardar-pago').textContent = 'Registrar pago';
    setModalidad('completo');
    document.getElementById('modal-nuevo').classList.add('open');
    setTimeout(function(){ document.getElementById('n-cliente').focus(); }, 100);
}

function cerrarNuevo() { document.getElementById('modal-nuevo').classList.remove('open'); }
function clickOverlay(e) { if (e.target === document.getElementById('modal-nuevo')) cerrarNuevo(); }

function setModalidad(mod) {
    modalidad = mod;
    var descs = {
        completo: 'Pago único por el total del documento.',
        fijo:     'Se divide en mensualidades iguales. Ingresa el total y el número de meses.',
        variable: 'El cliente abona montos variables. Ingresa el total a diferir.'
    };
    document.getElementById('mod-desc').textContent = descs[mod];
    document.getElementById('lbl-monto').textContent = mod === 'completo' ? 'Monto *' : 'Total a diferir *';
    ['completo','fijo','variable'].forEach(function(m) {
        document.getElementById('mod-btn-' + m).className = m === mod ? 'btn-sm btn-primary' : 'btn-sm btn-outline';
    });
    document.getElementById('sec-fijo').style.display     = mod === 'fijo'     ? 'block' : 'none';
    document.getElementById('sec-variable').style.display = mod === 'variable' ? 'block' : 'none';
    document.getElementById('sec-recordatorio').style.display = mod !== 'completo' ? 'block' : 'none';
}

function toggleRecordatorio() {
    recordatorio = !recordatorio;
    document.getElementById('rec-track').classList.toggle('on', recordatorio);
    document.getElementById('sec-telefono').style.display = recordatorio ? 'block' : 'none';
}

function onMontoChange() { if (modalidad === 'fijo') calcularMensualidades(); }

function calcularMensualidades() {
    var total = parseFloat(document.getElementById('n-monto').value) || 0;
    var meses = parseInt(document.getElementById('n-mensualidades').value) || 0;
    var prev  = document.getElementById('mens-preview');
    if (!total || !meses || meses < 2) { prev.style.display = 'none'; return; }

    var monto_mens = Math.floor((total / meses) * 100) / 100;
    var ultimo     = parseFloat((total - monto_mens * (meses - 1)).toFixed(2));

    var html = '';
    for (var i = 1; i <= meses; i++) {
        var m = i === meses ? ultimo : monto_mens;
        html += '<div class="mens-item">' +
            '<span class="mens-num">Mes ' + i + '</span>' +
            '<span class="mens-monto">' + fmt(m) + '</span>' +
            '<span class="mens-estado" style="color:var(--muted);">Pendiente</span>' +
        '</div>';
    }
    document.getElementById('mens-lista').innerHTML = html;
    prev.style.display = 'block';
}

// ── Autocomplete clientes ──
function filtrarClientesPago() {
    var q  = document.getElementById('n-cliente').value.toLowerCase();
    var dd = document.getElementById('clientes-pago-dropdown');
    if (!q) { dd.style.display = 'none'; return; }
    var matches = clientesLista.filter(function(c){
        return c.nombre.toLowerCase().includes(q) || (c.alias||'').toLowerCase().includes(q);
    }).slice(0, 6);
    if (!matches.length) { dd.style.display = 'none'; return; }
    dd.style.display = 'block';
    var html = '';
    matches.forEach(function(c) {
        html += '<div onclick="selClientePago(\'' + esc(c.nombre).replace(/'/g,"\\'") + '\')" ' +
            'style="padding:8px 12px;cursor:pointer;font-size:0.82rem;color:var(--text);border-bottom:1px solid var(--border);" ' +
            'onmouseover="this.style.background=\'var(--bg)\'" onmouseout="this.style.background=\'\'">' +
            esc(c.alias || c.nombre) + '</div>';
    });
    dd.innerHTML = html;
}

function selClientePago(nombre) {
    document.getElementById('n-cliente').value = nombre;
    document.getElementById('clientes-pago-dropdown').style.display = 'none';
}

document.addEventListener('click', function(e) {
    var dd = document.getElementById('clientes-pago-dropdown');
    if (dd && !dd.contains(e.target) && e.target.id !== 'n-cliente') dd.style.display = 'none';
});

// ── Selector documento ──
function abrirSelDoc() {
    document.getElementById('doc-buscar').value = '';
    filtrarDocs();
    document.getElementById('modal-sel-doc').classList.add('open');
}
function cerrarSelDoc() { document.getElementById('modal-sel-doc').classList.remove('open'); }

function setTab(tab) {
    tabActual = tab;
    document.getElementById('tab-facturas').className    = tab === 'facturas'    ? 'btn-sm btn-primary' : 'btn-sm btn-outline';
    document.getElementById('tab-cotizaciones').className = tab === 'cotizaciones' ? 'btn-sm btn-primary' : 'btn-sm btn-outline';
    document.getElementById('doc-buscar').value = '';
    filtrarDocs();
}

function filtrarDocs() {
    var q    = (document.getElementById('doc-buscar').value || '').toLowerCase();
    var lista = (tabActual === 'facturas' ? facturas : cotizaciones).filter(function(d) {
        return d.nombre_cliente.toLowerCase().includes(q) || d.folio.toLowerCase().includes(q);
    });
    var el = document.getElementById('doc-lista');
    if (!lista.length) { el.innerHTML = '<p style="text-align:center;color:var(--muted);font-size:0.78rem;padding:20px 0;">Sin resultados</p>'; return; }
    var html = '';
    lista.forEach(function(d) {
        var pagado   = parseFloat(d.monto_pagado || 0);
        var total    = parseFloat(d.total);
        var pendiente = Math.max(0, total - pagado);
        var pct      = total > 0 ? Math.min(100, Math.round(pagado / total * 100)) : 0;
        var ep       = d.estatus_pago || 'pendiente';
        var epLabel  = ep === 'pagado' ? 'Pagado' : ep === 'parcial' ? 'Parcial' : 'Pendiente';
        html += '<div class="doc-sel-item" onclick="selDoc(\'' + tabActual + '\',' + d.id + ',\'' + esc(d.folio) + '\',\'' + esc(d.nombre_cliente).replace(/'/g,"\\'") + '\',' + total + ',' + pagado + ')">' +
            '<div class="doc-sel-folio">' + esc(d.folio) + '</div>' +
            '<div class="doc-sel-info">' +
                '<div class="doc-sel-cliente">' + esc(d.nombre_cliente) + '</div>' +
                '<div class="doc-sel-sub">Total: ' + fmt(total) + ' · Pendiente: ' + fmt(pendiente) + '</div>' +
                '<div class="pendiente-bar"><div class="pendiente-fill" style="width:' + pct + '%"></div></div>' +
            '</div>' +
            '<span class="ep-badge ep-' + ep + '">' + epLabel + '</span>' +
        '</div>';
    });
    el.innerHTML = html;
}

function selDoc(tipo, id, folio, cliente, total, pagado) {
    docSel = { tipo:tipo, id:id, folio:folio, cliente:cliente, total:total, pagado:pagado };
    document.getElementById('n-doc-display').value = folio + ' — ' + cliente;
    document.getElementById('n-cliente').value = cliente;
    var pendiente = Math.max(0, total - pagado);
    var pct = total > 0 ? Math.min(100, Math.round(pagado / total * 100)) : 0;
    document.getElementById('doc-pendiente-preview').style.display = 'block';
    document.getElementById('doc-pendiente-preview').innerHTML =
        '<div style="padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.78rem;">' +
            '<div style="display:flex;justify-content:space-between;margin-bottom:6px;">' +
                '<span style="color:var(--muted);">Pendiente por pagar</span>' +
                '<strong>' + fmt(pendiente) + '</strong>' +
            '</div>' +
            '<div class="pendiente-bar"><div class="pendiente-fill" style="width:' + pct + '%"></div></div>' +
            '<div style="font-size:0.68rem;color:var(--muted);margin-top:4px;">' + pct + '% pagado</div>' +
        '</div>';
    if (!document.getElementById('n-monto').value) document.getElementById('n-monto').value = pendiente.toFixed(2);
    cerrarSelDoc();
}

function limpiarDoc() {
    docSel = null;
    document.getElementById('n-doc-display').value = '';
    document.getElementById('doc-pendiente-preview').style.display = 'none';
}

// ── Guardar pago ──
function guardarPago() {
    var cliente = document.getElementById('n-cliente').value.trim();
    var monto   = parseFloat(document.getElementById('n-monto').value) || 0;
    var metodo  = document.getElementById('n-metodo').value;
    if (!cliente) { resaltar('n-cliente'); return; }
    if (!monto)   { resaltar('n-monto');   return; }
    if (modalidad === 'fijo') {
        var meses = parseInt(document.getElementById('n-mensualidades').value) || 0;
        if (meses < 2) { resaltar('n-mensualidades'); return; }
    }

    var btn = document.getElementById('btn-guardar-pago');
    btn.disabled = true; btn.textContent = 'Registrando...';

    var fd = new FormData();
    fd.append('modalidad',      modalidad);
    fd.append('nombre_cliente', cliente);
    fd.append('monto',          monto);
    fd.append('metodo',         metodo);
    fd.append('referencia',     document.getElementById('n-referencia').value.trim());
    fd.append('notas',          document.getElementById('n-notas').value.trim());
    fd.append('recordatorio',   recordatorio ? 1 : 0);
    fd.append('telefono_recordatorio', recordatorio ? document.getElementById('n-telefono').value.trim() : '');
    if (modalidad === 'fijo') fd.append('mensualidades', document.getElementById('n-mensualidades').value);
    if (docSel) {
        fd.append('tipo_doc',  docSel.tipo);
        fd.append('id_doc',    docSel.id);
        fd.append('folio_doc', docSel.folio);
    }

    fetch('controllers/add_pago.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarNuevo(); cargar(); }
            else {
                alert('Error: ' + res.msg);
                btn.disabled = false; btn.textContent = 'Registrar pago';
            }
        });
}

// ── Ver pago ──
function verPago(id) {
    fetch('controllers/get_pago.php?id=' + id)
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (!res.ok) { alert('Error al cargar'); return; }
            pagoActivoId = id;
            pagoActivo   = res.data;
            renderVerPago(res.data);
            document.getElementById('modal-ver').classList.add('open');
        });
}

function renderVerPago(p) {
    var esDif   = p.modalidad === 'fijo' || p.modalidad === 'variable';
    var cobrado = parseFloat(p.monto_cobrado || 0);
    var total   = parseFloat(p.monto);
    var pend    = Math.max(0, total - cobrado);
    var pct     = total > 0 ? Math.min(100, Math.round(cobrado / total * 100)) : 100;var tieneCot = (p.id_cotizacion && p.id_cotizacion != '0') || (p.id_factura && p.id_factura != '0');

    document.getElementById('ver-titulo').textContent = 'Pago — ' + (p.nombre_cliente || '—');
    document.getElementById('btn-abono').style.display = (esDif && pend > 0) ? 'inline-flex' : 'none';

    var html =
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">' +
            '<div style="padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);">' +
                '<div style="font-size:0.65rem;color:var(--muted);text-transform:uppercase;font-weight:600;">Modalidad</div>' +
                '<div style="font-weight:600;color:var(--text);margin-top:2px;">' + modLabels[p.modalidad] + '</div>' +
            '</div>' +
            '<div style="padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);">' +
                '<div style="font-size:0.65rem;color:var(--muted);text-transform:uppercase;font-weight:600;">Método</div>' +
                '<div style="font-weight:600;color:var(--text);margin-top:2px;">' + p.metodo.charAt(0).toUpperCase() + p.metodo.slice(1) + '</div>' +
            '</div>' +
        '</div>';

    if (esDif) {
        html += '<div style="padding:12px 16px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:14px;">' +
            '<div style="display:flex;justify-content:space-between;margin-bottom:6px;">' +
                '<span style="font-size:0.78rem;color:var(--muted);">Avance</span>' +
                '<span style="font-size:0.78rem;font-weight:700;">' + fmt(cobrado) + ' / ' + fmt(total) + '</span>' +
            '</div>' +
            '<div class="prog-bar" style="height:8px;"><div class="prog-fill" style="width:' + pct + '%"></div></div>' +
            '<div style="display:flex;justify-content:space-between;margin-top:6px;font-size:0.72rem;">' +
                '<span style="color:var(--muted);">' + pct + '% pagado</span>' +
                '<span style="color:#FF0000 ;font-weight:600;font-size:0.9rem;">Pendiente: ' + fmt(pend) + '</span>' +
            '</div>' +
        '</div>';
    } else {
        html += '<div style="padding:12px 16px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:14px;display:flex;justify-content:space-between;align-items:center;">' +
            '<span style="font-size:0.82rem;color:var(--muted);">Monto pagado</span>' +
            '<span style="font-size:1.1rem;font-weight:700;color:var(--text);">' + fmt(total) + '</span>' +
        '</div>';
    }

    if (p.folio_doc) {
        html += '<div style="font-size:0.78rem;color:var(--muted);margin-bottom:10px;">Documento: <strong style="color:var(--text);">' + esc(p.folio_doc) + '</strong></div>';
    }

    if (p.referencia) {
        html += '<div style="font-size:0.78rem;color:var(--muted);margin-bottom:10px;">Referencia: <strong style="color:var(--text);">' + esc(p.referencia) + '</strong></div>';
    }

    if (p.recordatorio && p.telefono_recordatorio) {
        html += '<div style="padding:8px 12px;background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.2);border-radius:var(--radius-sm);font-size:0.78rem;margin-bottom:10px;">🔔 Recordatorio activo — ' + esc(p.telefono_recordatorio) + '</div>';
    }

    if (p.notas) {
        html += '<div style="font-size:0.78rem;color:var(--muted);margin-bottom:14px;">Notas: ' + esc(p.notas) + '</div>';
    }

    // Abonos
    if (p.abonos && p.abonos.length) {
        html += '<div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted);margin-bottom:8px;">Abonos registrados</div>';
        html += '<div style="display:flex;flex-direction:column;gap:6px;">';
        p.abonos.forEach(function(a) {
            var fa = new Date(a.created_at).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'});
           html += '<div class="abono-row">' +
           '<span class="abono-num">#' + a.numero_abono + '</span>' +
           '<div class="abono-info">' + fa + (a.referencia ? ' · ' + esc(a.referencia) : '') + (a.notas ? ' · ' + esc(a.notas) : '') + '</div>' +
           '<span class="metodo-badge badge-' + a.metodo + '">' + a.metodo + '</span>' +
           '<span class="abono-monto">' + fmt(a.monto) + '</span>' +
           '<button onclick="abrirEditarAbono(' + a.id + ',' + a.monto + ',\'' + esc(a.metodo) + '\',\'' + esc(a.referencia||'') + '\',\'' + esc(a.notas||'') + '\')" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:0.72rem;font-weight:600;padding:2px 6px;border-radius:4px;transition:color 0.15s;" onmouseover="this.style.color=\'var(--accent)\'" onmouseout="this.style.color=\'var(--muted)\'">Editar</button>' +
           '</div>';
        });
        html += '</div>';
    }

    document.getElementById('modal-ver-body').innerHTML = html;
}

function cerrarVer() { document.getElementById('modal-ver').classList.remove('open'); }

function eliminarPago() {
    if (!pagoActivoId) return;
    if (!confirm('¿Eliminar este pago?')) return;
    var fd = new FormData();
    fd.append('id', pagoActivoId);
    fetch('controllers/delete_pago.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarVer(); cargar(); }
            else alert('Error: ' + res.msg);
        });
}

// ── Abono ──
function abrirAbono() {
    if (!pagoActivo) return;
    var cobrado = parseFloat(pagoActivo.monto_cobrado || 0);
    var total   = parseFloat(pagoActivo.monto);
    var pend    = Math.max(0, total - cobrado);

    document.getElementById('abono-pendiente-info').innerHTML =
        'Total: <strong>' + fmt(total) + '</strong> · ' + '<br>'+
        'Pagado: <strong style="color:#008000; font-size:1.2em;">' + fmt(cobrado) + '</strong> · ' + '<br>' +
        'Pendiente: <strong style="color:#FF0000; font-size:1.4em;">' + fmt(pend) + '</strong>';

    document.getElementById('ab-monto').value       = '';
    document.getElementById('ab-monto').placeholder = 'Ej. 200.00  (pendiente: ' + fmt(pend) + ')';
    document.getElementById('ab-referencia').value  = '';
    document.getElementById('ab-notas').value       = '';
    document.getElementById('btn-guardar-abono').disabled = false;
    document.getElementById('btn-guardar-abono').textContent = 'Guardar abono';
    document.getElementById('modal-abono').classList.add('open');
}

function cerrarAbono() { document.getElementById('modal-abono').classList.remove('open'); }

function guardarAbono() {
    var monto = parseFloat(document.getElementById('ab-monto').value) || 0;
    if (!monto) { resaltar('ab-monto'); return; }

    var btn = document.getElementById('btn-guardar-abono');
    btn.disabled = true; btn.textContent = 'Guardando...';

    var fd = new FormData();
    fd.append('id_pago',    pagoActivoId);
    fd.append('monto',      monto);
    fd.append('metodo',     document.getElementById('ab-metodo').value);
    fd.append('referencia', document.getElementById('ab-referencia').value.trim());
    fd.append('notas',      document.getElementById('ab-notas').value.trim());

    fetch('controllers/add_abono.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                cerrarAbono();
                cargar();
                setTimeout(function(){ verPago(pagoActivoId); }, 300);
            } else {
                alert('Error: ' + res.msg);
                btn.disabled = false; btn.textContent = 'Guardar abono';
            }
        });
}

function fmt(n) { return '$' + parseFloat(n).toFixed(2); }
function resaltar(id) {
    var el = document.getElementById(id);
    el.style.borderColor = '#ef4444'; el.focus();
    el.addEventListener('input', function(){ el.style.borderColor = ''; }, { once:true });
}
function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function abrirDesdeCotizacion() {
    abrirNuevo();
    setTimeout(function() {
        tabActual = 'cotizaciones';
        document.getElementById('doc-buscar').value = '';
        filtrarDocs();
        document.getElementById('modal-sel-doc').classList.add('open');
    }, 150);
}
// ── Nota de remisión ──
function imprimirTicket() {
    if (!pagoActivo) return;
    var p = pagoActivo;

    // Solo si hay documento vinculado
    if (!p.id_cotizacion && !p.id_factura) {
        alert('Esta nota de remisión solo está disponible cuando el pago tiene una cotización o factura vinculada.');
        return;
    }

    // Traer items del documento vinculado
    var url = p.id_cotizacion
        ? 'controllers/get_cotizacion.php?id=' + p.id_cotizacion
        : 'controllers/get_factura.php?id='    + p.id_factura;

    fetch(url)
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (!res.ok) { alert('No se pudo cargar el documento vinculado.'); return; }
            generarNotaRemision(p, res.data);
        });
}

function generarNotaRemision(p, doc) {
    var cobrado = parseFloat(p.monto_cobrado || 0);
    var total   = parseFloat(p.monto);
    var pend    = Math.max(0, total - cobrado);
    var pct     = total > 0 ? Math.min(100, Math.round(cobrado / total * 100)) : 100;
    var pagadoCompleto = pend <= 0;
    var fecha   = new Date().toLocaleDateString('es-MX', {weekday:'long', day:'2-digit', month:'long', year:'numeric'});
    var modLabels = { completo:'Pago de contado', fijo:'Diferido fijo', variable:'Diferido variable' };

    // ── Items del documento ──
    var itemsHtml = '';
    if (doc.items && doc.items.length) {
        var filas = '';
        var subtotalDoc = 0;
        doc.items.forEach(function(i) {
            var sub = parseFloat(i.subtotal || 0);
            subtotalDoc += sub;
            filas += '<tr>' +
                '<td><span style="font-size:0.6rem;padding:2px 6px;border-radius:999px;font-weight:500;background:#dbeafe;color:#1d4ed8;">' +
                    (i.tipo === 'servicio' ? '<span style="background:#f3e8ff;color:#7c3aed;">Servicio</span>' : 'Producto') +
                '</span></td>' +
                '<td>' + esc(i.nombre) + '</td>' +
                '<td style="text-align:center;">' + i.cantidad + '</td>' +
                '<td style="text-align:right;">' + fmt(i.precio_unitario) + '</td>' +
                '<td style="text-align:right;font-weight:500;">' + fmt(sub) + '</td>' +
            '</tr>';
        });

        var baseDoc = subtotalDoc / 1.16;
        var ivaDoc  = subtotalDoc - baseDoc;

        itemsHtml =
            '<div class="nr-section-title">Detalle de productos y servicios</div>' +
            '<table class="nr-table">' +
                '<thead><tr><th>Tipo</th><th>Descripción</th><th style="text-align:center">Cant.</th><th style="text-align:right">P. Unit.</th><th style="text-align:right">Subtotal</th></tr></thead>' +
                '<tbody>' + filas + '</tbody>' +
            '</table>' +
            '<div style="display:flex;justify-content:flex-end;margin-bottom:24px;">' +
                '<table style="width:220px;font-size:0.78rem;border-collapse:collapse;">' +
                    '<tr><td style="padding:4px 8px;color:#475569;">Subtotal (base)</td><td style="padding:4px 8px;text-align:right;color:#0f172a;font-weight:500;">' + fmt(baseDoc) + '</td></tr>' +
                    '<tr><td style="padding:4px 8px;color:#475569;">IVA (16%, incluido)</td><td style="padding:4px 8px;text-align:right;color:#0f172a;font-weight:500;">' + fmt(ivaDoc) + '</td></tr>' +
                    '<tr><td style="padding:8px 8px 4px;color:#0f172a;font-weight:600;border-top:2px solid #eab308;">Total</td><td style="padding:8px 8px 4px;text-align:right;font-weight:700;color:#0f172a;border-top:2px solid #eab308;">' + fmt(subtotalDoc) + '</td></tr>' +
                '</table>' +
            '</div>';
    }

    // ── Saldo ──
    var saldoHtml = pagadoCompleto
        ? '<div class="nr-saldo pagado"><div class="nr-saldo-label">✓ Pagado completamente</div><div class="nr-saldo-monto">' + fmt(total) + '</div></div>'
        : '<div class="nr-saldo pendiente"><div class="nr-saldo-label">Saldo pendiente</div><div class="nr-saldo-monto">' + fmt(pend) + '</div><div class="nr-saldo-sub">Pagado ' + fmt(cobrado) + ' de ' + fmt(total) + ' · ' + pct + '%</div></div>';

    // ── Abonos ──
    var abonosHtml = '';
    if (p.abonos && p.abonos.length) {
        var filas2 = '';
        p.abonos.forEach(function(a) {
            var fa = new Date(a.created_at).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'});
            filas2 += '<tr>' +
                '<td>#' + a.numero_abono + '</td>' +
                '<td>' + fa + '</td>' +
                '<td>' + a.metodo + '</td>' +
                '<td>' + (a.referencia || '—') + '</td>' +
                '<td style="text-align:right;font-weight:500;">' + fmt(a.monto) + '</td>' +
            '</tr>';
        });
        abonosHtml =
            '<div class="nr-section-title">Historial de abonos</div>' +
            '<table class="nr-table">' +
                '<thead><tr><th>#</th><th>Fecha</th><th>Método</th><th>Referencia</th><th style="text-align:right">Monto</th></tr></thead>' +
                '<tbody>' + filas2 + '</tbody>' +
            '</table>';
    }

    // ── Resumen de pago ──
    var resumenHtml =
        '<div class="nr-section-title">Resumen de pago</div>' +
        '<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">' +
            '<div class="nr-info-chip"><div class="nr-info-label">Modalidad</div><div class="nr-info-val">' + (modLabels[p.modalidad] || p.modalidad) + '</div></div>' +
            '<div class="nr-info-chip"><div class="nr-info-label">Método</div><div class="nr-info-val">' + p.metodo.charAt(0).toUpperCase() + p.metodo.slice(1) + '</div></div>' +
            (p.referencia ? '<div class="nr-info-chip"><div class="nr-info-label">Referencia</div><div class="nr-info-val">' + esc(p.referencia) + '</div></div>' : '') +
        '</div>' +
        saldoHtml +
        abonosHtml +
        '<div style="display:flex;justify-content:flex-end;margin-top:16px;">' +
            '<table style="width:220px;font-size:0.78rem;border-collapse:collapse;">' +
                '<tr><td style="padding:4px 8px;color:#475569;">Total del documento</td><td style="padding:4px 8px;text-align:right;color:#0f172a;font-weight:500;">' + fmt(total) + '</td></tr>' +
                '<tr><td style="padding:4px 8px;color:#475569;">Total pagado</td><td style="padding:4px 8px;text-align:right;color:#0f172a;font-weight:500;">' + fmt(cobrado) + '</td></tr>' +
                '<tr><td style="padding:8px 8px 4px;color:' + (pagadoCompleto ? '#15803d' : '#dc2626') + ';font-weight:600;border-top:2px solid #eab308;">Saldo pendiente</td>' +
                '<td style="padding:8px 8px 4px;text-align:right;font-weight:700;font-size:0.95rem;color:' + (pagadoCompleto ? '#15803d' : '#dc2626') + ';border-top:2px solid #eab308;">' + fmt(pend) + '</td></tr>' +
            '</table>' +
        '</div>';

    // ── HTML final ──
    var html =
        '<!DOCTYPE html><html><head><meta charset="UTF-8">' +
        '<title>Nota de Remisión — ' + esc(p.nombre_cliente) + '</title>' +
        '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">' +
        '<style>' +
            '*{box-sizing:border-box;margin:0;padding:0;}' +
            'body{font-family:Poppins,sans-serif;background:#fff;padding:20px;color:#0f172a;font-weight:400;}' +
            '.nr{max-width:680px;margin:0 auto;padding:36px 40px;}' +
            '.nr-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;padding-bottom:20px;border-bottom:2px solid #eab308;}' +
            '.nr-logo-area{display:flex;align-items:center;gap:12px;}' +
            '.nr-logo-ring{width:48px;height:48px;border-radius:50%;border:2px solid #eab308;overflow:hidden;background:#0f172a;display:flex;align-items:center;justify-content:center;}' +
            '.nr-logo-ring img{width:75%;height:75%;object-fit:contain;}' +
            '.nr-empresa{font-size:1rem;font-weight:500;color:#0f172a;line-height:1.2;}' +
            '.nr-empresa small{font-size:0.65rem;color:#64748b;font-weight:400;display:block;}' +
            '.nr-meta{text-align:right;}' +
            '.nr-titulo{font-size:1rem;font-weight:600;color:#eab308;text-transform:uppercase;letter-spacing:0.06em;}' +
            '.nr-folio{font-size:0.75rem;color:#64748b;margin-top:3px;}' +
            '.nr-fecha{font-size:0.7rem;color:#64748b;margin-top:2px;}' +
            '.nr-cliente{margin-bottom:24px;padding:12px 16px;background:#f8fafc;border-radius:8px;border-left:3px solid #eab308;}' +
            '.nr-cliente-label{font-size:0.6rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;}' +
            '.nr-cliente-nombre{font-size:0.95rem;font-weight:500;color:#0f172a;margin-top:2px;}' +
            '.nr-cliente-sub{font-size:0.7rem;color:#64748b;margin-top:2px;}' +
            '.nr-divider{border:none;border-top:1px solid #e2e8f0;margin:24px 0;}' +
            '.nr-section-title{font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#64748b;margin-bottom:12px;}' +
            '.nr-table{width:100%;border-collapse:collapse;font-size:0.8rem;margin-bottom:16px;}' +
            '.nr-table thead th{text-align:left;padding:7px 10px;background:#0f172a;color:#f1f5f9;font-size:0.65rem;font-weight:500;text-transform:uppercase;}' +
            '.nr-table tbody td{padding:9px 10px;border-bottom:1px solid #e2e8f0;color:#0f172a;font-weight:400;}' +
            '.nr-saldo{margin-bottom:20px;padding:16px;border-radius:8px;text-align:center;}' +
            '.nr-saldo.pendiente{background:#fee2e2;border:1.5px solid #fca5a5;}' +
            '.nr-saldo.pagado{background:#dcfce7;border:1.5px solid #86efac;}' +
            '.nr-saldo-label{font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#b91c1c;margin-bottom:4px;}' +
            '.nr-saldo.pagado .nr-saldo-label{color:#15803d;}' +
            '.nr-saldo-monto{font-size:2rem;font-weight:700;color:#dc2626;line-height:1;}' +
            '.nr-saldo.pagado .nr-saldo-monto{color:#15803d;}' +
            '.nr-saldo-sub{font-size:0.65rem;color:#64748b;margin-top:4px;}' +
            '.nr-info-chip{padding:8px 14px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;}' +
            '.nr-info-label{font-size:0.6rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;}' +
            '.nr-info-val{font-size:0.82rem;font-weight:500;color:#0f172a;margin-top:2px;}' +
            '.nr-footer{margin-top:28px;padding-top:16px;border-top:1px solid #e2e8f0;text-align:center;font-size:0.68rem;color:#94a3b8;}' +
            '.nr-firma{display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-top:40px;}' +
            '.nr-firma-linea{border-top:1px solid #0f172a;padding-top:6px;font-size:0.68rem;color:#64748b;text-align:center;}' +
        '</style></head><body>' +
        '<div class="nr">' +

            // Header
            '<div class="nr-header">' +
                '<div class="nr-logo-area">' +
                    '<div class="nr-logo-ring"><img src="assets/logo/logovttr.png" alt="VT"></div>' +
                    '<div><div class="nr-empresa">Villa Tecnia Tampico<small>Materiales para alberca</small></div></div>' +
                '</div>' +
                '<div class="nr-meta">' +
                    '<div class="nr-titulo">Nota de Remisión</div>' +
                    (p.folio_doc ? '<div class="nr-folio">Ref: ' + esc(p.folio_doc) + '</div>' : '') +
                    '<div class="nr-fecha">' + fecha + '</div>' +
                '</div>' +
            '</div>' +

            // Cliente
            '<div class="nr-cliente">' +
                '<div class="nr-cliente-label">Cliente</div>' +
                '<div class="nr-cliente-nombre">' + esc(p.nombre_cliente) + '</div>' +
                (p.folio_doc ? '<div class="nr-cliente-sub">Documento: ' + esc(p.folio_doc) + '</div>' : '') +
            '</div>' +

            // Items
            itemsHtml +

            '<hr class="nr-divider">' +

            // Pago
            resumenHtml +

            // Firmas
            '<div class="nr-firma">' +
                '<div class="nr-firma-linea">Firma del cliente</div>' +
                '<div class="nr-firma-linea">Villa Tecnia Tampico</div>' +
            '</div>' +

            '<div class="nr-footer">Nota de remisión generada por Villa Tecnia Tampico · ' + fecha + '</div>' +
        '</div>' +
        '</body></html>';

    var win = window.open('', '_blank');
    win.document.write(html);
    win.document.close();
    setTimeout(function(){ win.print(); }, 800);
}
function generarTicketHTML(p) {
    var esDif   = p.modalidad === 'fijo' || p.modalidad === 'variable';
    var total   = parseFloat(p.monto);
    var cobrado = parseFloat(p.monto_cobrado || 0);
    var pend    = Math.max(0, total - cobrado);
    var fecha   = new Date(p.created_at);
    var fechaStr = fecha.toLocaleDateString('es-MX', {weekday:'long', day:'2-digit', month:'long', year:'numeric'});
    var horaStr  = fecha.toLocaleTimeString('es-MX', {hour:'2-digit', minute:'2-digit'});
    var metodoLabel = { efectivo:'Efectivo', transferencia:'Transferencia bancaria', tarjeta:'Tarjeta' };

    if (!esDif) {
        // ── TICKET CONTADO ──
        return '<div class="ticket">' +
            encabezadoTicket(fechaStr, horaStr) +
            '<div class="tk-cliente">' +
                '<div class="tk-label">Cliente</div>' +
                '<div class="tk-nombre">' + esc(p.nombre_cliente) + '</div>' +
            '</div>' +
            (p.folio_doc ? '<div class="tk-folio">Documento: <strong>' + esc(p.folio_doc) + '</strong></div>' : '') +
            '<table class="tk-table">' +
                '<thead><tr><th>Concepto</th><th>Método</th><th>Monto</th></tr></thead>' +
                '<tbody>' +
                    '<tr>' +
                        '<td>' + (p.notas || 'Pago de contado') + '</td>' +
                        '<td>' + (metodoLabel[p.metodo] || p.metodo) + '</td>' +
                        '<td class="r">' + fmt(total) + '</td>' +
                    '</tr>' +
                '</tbody>' +
            '</table>' +
            '<div class="tk-totales">' +
                '<div class="tk-tot-row"><span>Total pagado</span><span>' + fmt(total) + '</span></div>' +
            '</div>' +
            (p.referencia ? '<div class="tk-ref">Ref: ' + esc(p.referencia) + '</div>' : '') +
            pieTicket() +
        '</div>';
    } else {
        // ── TICKET CRÉDITO / DIFERIDO ──
        var modLabel = p.modalidad === 'fijo' ? 'Diferido en mensualidades' : 'Crédito con abonos variables';
        var pct = total > 0 ? Math.min(100, Math.round(cobrado / total * 100)) : 0;

        // Tabla de abonos
        var filasAbonos = '';
        if (p.abonos && p.abonos.length) {
            p.abonos.forEach(function(a) {
                var fa = new Date(a.created_at);
                var faStr = fa.toLocaleDateString('es-MX', {day:'2-digit', month:'short', year:'numeric'});
                var faHora = fa.toLocaleTimeString('es-MX', {hour:'2-digit', minute:'2-digit'});
                filasAbonos +=
                    '<tr>' +
                        '<td>Abono #' + a.numero_abono + '</td>' +
                        '<td>' + faStr + ' ' + faHora + '</td>' +
                        '<td>' + (metodoLabel[a.metodo] || a.metodo) + '</td>' +
                        (a.referencia ? '<td>' + esc(a.referencia) + '</td>' : '<td>—</td>') +
                        '<td class="r">' + fmt(a.monto) + '</td>' +
                    '</tr>';
            });
        } else {
            filasAbonos = '<tr><td colspan="5" style="text-align:center;color:#64748b;">Sin abonos registrados aún</td></tr>';
        }

        // Mensualidades programadas (solo fijo)
        var secMensualidades = '';
        if (p.modalidad === 'fijo' && p.mensualidades) {
            var mens = parseInt(p.mensualidades);
            var montoMens = parseFloat(p.monto_mensualidad || (total / mens));
            var pagadas = parseInt(p.abonos_pagados || 0);
            secMensualidades =
                '<div class="tk-section-title">Plan de mensualidades</div>' +
                '<table class="tk-table">' +
                    '<thead><tr><th>Mes</th><th>Monto</th><th>Estado</th></tr></thead>' +
                    '<tbody>';
            for (var i = 1; i <= mens; i++) {
                var ultimo = (i === mens) ? parseFloat((total - montoMens * (mens - 1)).toFixed(2)) : montoMens;
                var estado = i <= pagadas ? '✓ Pagado' : 'Pendiente';
                var estClass = i <= pagadas ? 'pagado' : 'pendiente';
                secMensualidades +=
                    '<tr class="' + estClass + '">' +
                        '<td>Mes ' + i + '</td>' +
                        '<td>' + fmt(ultimo) + '</td>' +
                        '<td class="estado-' + estClass + '">' + estado + '</td>' +
                    '</tr>';
            }
            secMensualidades += '</tbody></table>';
        }

        return '<div class="ticket">' +
            encabezadoTicket(fechaStr, horaStr) +
            '<div class="tk-cliente">' +
                '<div class="tk-label">Cliente</div>' +
                '<div class="tk-nombre">' + esc(p.nombre_cliente) + '</div>' +
            '</div>' +
            '<div class="tk-mod-badge">' + modLabel + '</div>' +
            (p.folio_doc ? '<div class="tk-folio">Documento relacionado: <strong>' + esc(p.folio_doc) + '</strong></div>' : '') +

            // Resumen financiero
            '<div class="tk-resumen">' +
                '<div class="tk-res-item">' +
                    '<div class="tk-label">Total acordado</div>' +
                    '<div class="tk-res-val">' + fmt(total) + '</div>' +
                '</div>' +
                '<div class="tk-res-item">' +
                    '<div class="tk-label">Total pagado</div>' +
                    '<div class="tk-res-val cobrado">' + fmt(cobrado) + '</div>' +
                '</div>' +
                '<div class="tk-res-item">' +
                    '<div class="tk-label">Saldo pendiente</div>' +
                    '<div class="tk-res-val pendiente">' + fmt(pend) + '</div>' +
                '</div>' +
            '</div>' +

            // Barra de progreso
            '<div class="tk-prog-wrap">' +
                '<div class="tk-prog-bar"><div class="tk-prog-fill" style="width:' + pct + '%"></div></div>' +
                '<div class="tk-prog-label">' + pct + '% pagado</div>' +
            '</div>' +

            // Historial de abonos
            '<div class="tk-section-title">Historial de abonos</div>' +
            '<table class="tk-table">' +
                '<thead><tr><th>Concepto</th><th>Fecha</th><th>Método</th><th>Ref.</th><th>Monto</th></tr></thead>' +
                '<tbody>' + filasAbonos + '</tbody>' +
                '<tfoot>' +
                    '<tr><td colspan="4" style="text-align:right;font-weight:600;">Total abonado</td><td class="r" style="font-weight:700;">' + fmt(cobrado) + '</td></tr>' +
                '</tfoot>' +
            '</table>' +

            secMensualidades +

            (p.referencia ? '<div class="tk-ref">Referencia: ' + esc(p.referencia) + '</div>' : '') +
            (p.notas ? '<div class="tk-ref">Notas: ' + esc(p.notas) + '</div>' : '') +
            pieTicket() +
        '</div>';
    }
}

function encabezadoTicket(fechaStr, horaStr) {
    return '<div class="tk-header">' +
        '<div class="tk-logo-wrap">' +
            '<div class="tk-logo-ring"><img src="assets/logo/logovttr.png" alt="VT"></div>' +
            '<div>' +
                '<div class="tk-empresa">Villa Tecnia Tampico</div>' +
                '<div class="tk-empresa-sub">Materiales para alberca</div>' +
            '</div>' +
        '</div>' +
        '<div class="tk-fecha">' + fechaStr + '<br>' + horaStr + ' hrs</div>' +
    '</div>';
}

function pieTicket() {
    return '<div class="tk-footer">Gracias por su confianza · Villa Tecnia Tampico</div>';
}

function ticketCSS() {
    return [
        'body{font-family:Poppins,sans-serif;margin:0;padding:20px;background:#f8fafc;}',
        '.ticket{max-width:680px;margin:0 auto;background:#fff;padding:36px 40px;border-radius:12px;border:1px solid #e2e8f0;}',
        '.tk-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;padding-bottom:20px;border-bottom:2px solid #eab308;}',
        '.tk-logo-wrap{display:flex;align-items:center;gap:12px;}',
        '.tk-logo-ring{width:46px;height:46px;border-radius:50%;border:2px solid #eab308;overflow:hidden;background:#0f172a;display:flex;align-items:center;justify-content:center;}',
        '.tk-logo-ring img{width:75%;height:75%;object-fit:contain;}',
        '.tk-empresa{font-size:1rem;font-weight:700;color:#0f172a;}',
        '.tk-empresa-sub{font-size:0.65rem;color:#64748b;}',
        '.tk-fecha{font-size:0.72rem;color:#64748b;text-align:right;}',
        '.tk-cliente{margin-bottom:16px;padding:12px 16px;background:#f8fafc;border-radius:8px;border-left:3px solid #eab308;}',
        '.tk-label{font-size:0.62rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;}',
        '.tk-nombre{font-size:0.95rem;font-weight:600;color:#0f172a;margin-top:3px;}',
        '.tk-folio{font-size:0.75rem;color:#64748b;margin-bottom:14px;}',
        '.tk-mod-badge{display:inline-block;font-size:0.68rem;font-weight:600;padding:3px 10px;border-radius:999px;background:#fef9c3;color:#854d0e;margin-bottom:12px;}',
        '.tk-resumen{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:16px;}',
        '.tk-res-item{padding:10px 14px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;}',
        '.tk-res-val{font-size:1rem;font-weight:700;color:#0f172a;margin-top:4px;}',
        '.tk-res-val.cobrado{color:#16a34a;}',
        '.tk-res-val.pendiente{color:#dc2626;}',
        '.tk-prog-wrap{margin-bottom:20px;}',
        '.tk-prog-bar{height:8px;background:#e2e8f0;border-radius:999px;overflow:hidden;margin-bottom:4px;}',
        '.tk-prog-fill{height:100%;background:linear-gradient(90deg,#22c55e,#16a34a);border-radius:999px;}',
        '.tk-prog-label{font-size:0.68rem;color:#64748b;text-align:right;}',
        '.tk-section-title{font-size:0.68rem;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;color:#64748b;margin:16px 0 8px;}',
        'table{width:100%;border-collapse:collapse;font-size:0.8rem;margin-bottom:14px;}',
        'thead th{text-align:left;padding:8px 10px;background:#0f172a;color:#f1f5f9;font-size:0.65rem;font-weight:600;text-transform:uppercase;}',
        'tbody td{padding:9px 10px;border-bottom:1px solid #e2e8f0;color:#0f172a;}',
        'tfoot td{padding:9px 10px;border-top:2px solid #eab308;}',
        'tr.pagado td{background:#f0fdf4;}',
        'tr.pendiente td{background:#fff;}',
        '.estado-pagado{color:#16a34a;font-weight:600;}',
        '.estado-pendiente{color:#94a3b8;}',
        '.r{text-align:right;font-weight:600;}',
        '.tk-totales{margin-bottom:12px;}',
        '.tk-tot-row{display:flex;justify-content:space-between;padding:8px 14px;background:#0f172a;color:#f1f5f9;border-radius:8px;font-weight:700;font-size:0.9rem;}',
        '.tk-ref{font-size:0.75rem;color:#64748b;margin-bottom:6px;}',
        '.tk-footer{margin-top:24px;padding-top:16px;border-top:1px solid #e2e8f0;text-align:center;font-size:0.68rem;color:#94a3b8;}',
        '@media print{body{padding:0;}}'
    ].join('');
}
// ── Editar pago ──
function abrirEditarPago() {
    if (!pagoActivo) return;
    document.getElementById('ep-monto').value      = parseFloat(pagoActivo.monto).toFixed(2);
    document.getElementById('ep-metodo').value     = pagoActivo.metodo || 'efectivo';
    document.getElementById('ep-referencia').value = pagoActivo.referencia || '';
    document.getElementById('ep-notas').value      = pagoActivo.notas || '';
    document.getElementById('btn-guardar-ep').disabled = false;
    document.getElementById('btn-guardar-ep').textContent = 'Guardar cambios';
    document.getElementById('modal-editar-pago').classList.add('open');
}

function cerrarEditarPago() { document.getElementById('modal-editar-pago').classList.remove('open'); }

function guardarEditarPago() {
    var monto = parseFloat(document.getElementById('ep-monto').value) || 0;
    if (!monto) { resaltar('ep-monto'); return; }

    var btn = document.getElementById('btn-guardar-ep');
    btn.disabled = true; btn.textContent = 'Guardando...';

    var fd = new FormData();
    fd.append('id',          pagoActivoId);
    fd.append('monto',       monto);
    fd.append('metodo',      document.getElementById('ep-metodo').value);
    fd.append('referencia',  document.getElementById('ep-referencia').value.trim());
    fd.append('notas',       document.getElementById('ep-notas').value.trim());

    fetch('controllers/update_pago.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                cerrarEditarPago();
                cargar();
                setTimeout(function(){ verPago(pagoActivoId); }, 300);
            } else {
                alert('Error: ' + res.msg);
                btn.disabled = false; btn.textContent = 'Guardar cambios';
            }
        });
}

// ── Editar abono ──
function abrirEditarAbono(id, monto, metodo, referencia, notas) {
    document.getElementById('ea-id').value         = id;
    document.getElementById('ea-monto').value      = parseFloat(monto).toFixed(2);
    document.getElementById('ea-metodo').value     = metodo || 'efectivo';
    document.getElementById('ea-referencia').value = referencia || '';
    document.getElementById('ea-notas').value      = notas || '';
    document.getElementById('btn-guardar-ea').disabled = false;
    document.getElementById('btn-guardar-ea').textContent = 'Guardar cambios';
    document.getElementById('modal-editar-abono').classList.add('open');
}

function cerrarEditarAbono() { document.getElementById('modal-editar-abono').classList.remove('open'); }

function guardarEditarAbono() {
    var monto = parseFloat(document.getElementById('ea-monto').value) || 0;
    if (!monto) { resaltar('ea-monto'); return; }

    var btn = document.getElementById('btn-guardar-ea');
    btn.disabled = true; btn.textContent = 'Guardando...';

    var fd = new FormData();
    fd.append('id',         document.getElementById('ea-id').value);
    fd.append('monto',      monto);
    fd.append('metodo',     document.getElementById('ea-metodo').value);
    fd.append('referencia', document.getElementById('ea-referencia').value.trim());
    fd.append('notas',      document.getElementById('ea-notas').value.trim());

    fetch('controllers/update_abono.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                cerrarEditarAbono();
                cargar();
                setTimeout(function(){ verPago(pagoActivoId); }, 300);
            } else {
                alert('Error: ' + res.msg);
                btn.disabled = false; btn.textContent = 'Guardar cambios';
            }
        });
}

cargar();
</script>
</body>
</html>