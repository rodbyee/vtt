<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user'])) { header('Location: login.php'); exit; }
$pagina_actual = 'facturas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Facturas</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<link rel="stylesheet" href="css/dashboard.css">
<style>
.fac-list { display:flex; flex-direction:column; gap:8px; }

.fac-item {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 18px;
    transition: box-shadow 0.15s;
    cursor: pointer;
}
.fac-item:hover { box-shadow: var(--shadow); background: var(--bg); }

.fac-folio {
    font-size: 0.72rem; font-weight: 700;
    color: var(--accent); letter-spacing: 0.06em;
    white-space: nowrap; min-width: 80px;
}
.fac-info { flex:1; min-width:0; }
.fac-cliente { font-size:0.88rem; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.fac-rfc { font-size:0.7rem; color:var(--muted); margin-top:2px; }
.fac-total { font-size:0.95rem; font-weight:700; color:var(--text); white-space:nowrap; flex-shrink:0; }

.estatus-badge {
    font-size:0.65rem; padding:3px 9px;
    border-radius:999px; font-weight:600; flex-shrink:0;
}
.estatus-borrador { background:rgba(100,116,139,0.15); color:#94a3b8; }
.estatus-timbrada { background:rgba(34,197,94,0.15); color:#4ade80; }
[data-theme=""] .estatus-borrador { background:#f1f5f9; color:#475569; }
[data-theme=""] .estatus-timbrada { background:#dcfce7; color:#15803d; }

.ticket {
    background:#fff; color:#000;
    max-width:680px; margin:0 auto;
    padding:36px 40px;
    border-radius:var(--radius);
    border:1px solid #e2e8f0;
    font-family:'Poppins',sans-serif;
}
.ticket-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; padding-bottom:20px; border-bottom:2px solid #eab308; }
.ticket-logo-area { display:flex; align-items:center; gap:12px; }
.ticket-logo-ring { width:48px; height:48px; border-radius:50%; border:2px solid #eab308; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#0f172a; }
.ticket-logo-ring img { width:75%; height:75%; object-fit:contain; }
.ticket-empresa { font-size:1rem; font-weight:700; color:#0f172a; line-height:1.2; }
.ticket-empresa small { font-size:0.65rem; color:#64748b; font-weight:400; display:block; }
.ticket-meta { text-align:right; }
.ticket-folio { font-size:1rem; font-weight:700; color:#eab308; }
.ticket-fecha { font-size:0.72rem; color:#64748b; margin-top:4px; }

.ticket-partes { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px; }
.ticket-parte { padding:12px 16px; background:#f8fafc; border-radius:8px; border-left:3px solid #eab308; }
.ticket-parte-label { font-size:0.6rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; }
.ticket-parte-nombre { font-size:0.88rem; font-weight:600; color:#0f172a; margin-top:3px; }
.ticket-parte-sub { font-size:0.72rem; color:#64748b; margin-top:2px; }

.ticket-table { width:100%; border-collapse:collapse; margin-bottom:20px; font-size:0.82rem; }
.ticket-table thead th { text-align:left; padding:8px 10px; background:#0f172a; color:#f1f5f9; font-size:0.68rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; }
.ticket-table thead th:last-child { text-align:right; }
.ticket-table tbody td { padding:10px; border-bottom:1px solid #e2e8f0; color:#0f172a; vertical-align:middle; }
.ticket-table tbody td:last-child { text-align:right; font-weight:600; }
.ticket-table tbody tr:last-child td { border-bottom:none; }
.ticket-table .tipo-badge { font-size:0.6rem; padding:2px 6px; border-radius:999px; font-weight:600; background:#dbeafe; color:#1d4ed8; }
.ticket-table .tipo-badge.srv { background:#f3e8ff; color:#7c3aed; }

.ticket-totales { margin-left:auto; width:240px; }
.ticket-totales table { width:100%; font-size:0.82rem; min-width:unset !important; }
.ticket-totales td { padding:5px 8px; color:#475569; }
.ticket-totales td:last-child { text-align:right; font-weight:600; color:#0f172a; }
.ticket-totales .total-row td { font-size:1rem; font-weight:700; color:#0f172a; border-top:2px solid #eab308; padding-top:10px; }

.ticket-notas { margin-top:20px; padding:12px 16px; background:#f8fafc; border-radius:8px; font-size:0.78rem; color:#475569; }
.ticket-footer { margin-top:28px; padding-top:16px; border-top:1px solid #e2e8f0; text-align:center; font-size:0.7rem; color:#94a3b8; }

.ticket-table tbody tr:hover { background:transparent !important; }
#factura-print tbody td { color:#0f172a !important; background:#fff !important; }
#factura-print tbody tr:hover { background:#fff !important; }
.ticket table { min-width:unset !important; }
.ticket-totales table tbody td { padding:5px 8px !important; border-bottom:none !important; color:#475569 !important; }
.ticket-totales table tbody td:last-child { color:#0f172a !important; font-weight:600 !important; }
.ticket-totales .total-row td { color:#0f172a !important; font-weight:700 !important; border-top:2px solid #eab308 !important; }

.modal-xl { max-width:780px; }

.builder-section { margin-bottom:20px; }
.builder-section-title { font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted); margin-bottom:10px; }
.items-list { display:flex; flex-direction:column; gap:6px; margin-bottom:10px; }
.item-row { display:grid; grid-template-columns:1fr 60px 100px 32px; gap:8px; align-items:center; }
.item-row input { padding:7px 10px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--bg); color:var(--text); font-family:inherit; font-size:0.8rem; outline:none; width:100%; }
.item-row input:focus { border-color:var(--accent); }
.item-del { background:none; border:none; cursor:pointer; color:var(--muted); font-size:1.1rem; line-height:1; display:flex; align-items:center; justify-content:center; padding:4px; transition:color 0.15s; }
.item-del:hover { color:#ef4444; }
.add-row-btns { display:flex; gap:8px; flex-wrap:wrap; }

.iva-info-banner {
    display:flex; align-items:center; gap:10px;
    padding:10px 14px; background:rgba(34,197,94,0.08);
    border:1px solid rgba(34,197,94,0.2); border-radius:var(--radius-sm);
    margin-bottom:14px; font-size:0.78rem; color:#15803d;
}
[data-theme="dark"] .iva-info-banner { background:rgba(34,197,94,0.1); color:#4ade80; }
.iva-info-banner svg { width:16px;height:16px;flex-shrink:0; }

.totales-preview { background:var(--bg); border:1px solid var(--border); border-radius:var(--radius-sm); padding:12px 16px; font-size:0.82rem; }
.totales-preview .t-row { display:flex; justify-content:space-between; padding:3px 0; color:var(--muted); }
.totales-preview .t-row.total { font-size:1rem; font-weight:700; color:var(--text); border-top:1px solid var(--border); margin-top:6px; padding-top:8px; }

.iva-hint { font-size:0.65rem; color:var(--muted); margin-top:2px; white-space:nowrap; }
@media print { .iva-hint { display:none !important; } }

.btn-timbrar { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; opacity:0.5; cursor:not-allowed; }
.timbrar-tooltip { font-size:0.7rem; color:var(--muted); text-align:center; margin-top:6px; }

.share-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(120px,1fr)); gap:10px; margin-top:16px; }
.share-btn { display:flex; flex-direction:column; align-items:center; gap:8px; padding:16px 10px; border:1px solid var(--border); border-radius:var(--radius); cursor:pointer; background:var(--surface); color:var(--text); font-family:inherit; font-size:0.78rem; font-weight:600; transition:background 0.15s,border-color 0.15s; text-decoration:none; }
.share-btn:hover { background:var(--bg); border-color:var(--accent); }
.share-btn svg { width:28px; height:28px; }
.share-btn.whatsapp svg { color:#25d366; }
.share-btn.email svg { color:#4b8ef1; }
.share-btn.download svg { color:var(--accent); }

.cot-sel-item { display:flex; align-items:center; gap:12px; padding:10px 14px; border:1px solid var(--border); border-radius:var(--radius-sm); cursor:pointer; background:var(--surface); transition:background 0.1s; }
.cot-sel-item:hover { background:var(--bg); border-color:var(--accent); }
.cot-sel-folio { font-size:0.72rem; font-weight:700; color:var(--accent); min-width:72px; }
.cot-sel-info { flex:1; min-width:0; }
.cot-sel-cliente { font-size:0.82rem; font-weight:600; color:var(--text); }
.cot-sel-fecha { font-size:0.68rem; color:var(--muted); }
.cot-sel-total { font-size:0.85rem; font-weight:700; color:var(--text); }

@media (max-width:600px) {
    .ticket { padding:20px; }
    .ticket-header { flex-direction:column; gap:12px; }
    .ticket-meta { text-align:left; }
    .ticket-partes { grid-template-columns:1fr; }
    .item-row { grid-template-columns:1fr 50px 90px 28px; }
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
            <span class="page-title">Facturas</span>
        </div>
        <div class="topbar-right">
            <button class="btn-sm btn-outline" onclick="abrirDesdeCotizacion()" style="margin-right:6px;">Desde cotización</button>
            <button class="btn-sm btn-primary" onclick="abrirNueva()">+ Nueva factura</button>
        </div>
    </header>

    <div class="content">
        <div class="stats-grid stats-3" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="stat-label">Total facturas</div>
                <div class="stat-value" id="stat-total">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="stat-label">Valor total</div>
                <div class="stat-value" id="stat-valor">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="stat-label">Este mes</div>
                <div class="stat-value" id="stat-mes">—</div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <span class="table-title">Historial de facturas</span>
                <input type="text" id="buscador" placeholder="Buscar cliente, RFC o folio..."
                    oninput="filtrar()"
                    style="padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
                           font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);
                           outline:none;width:240px;max-width:100%;">
            </div>
            <div style="padding:16px;" id="fac-contenedor">
                <p style="text-align:center;color:var(--muted);padding:40px 0;font-size:0.82rem;">Cargando...</p>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL NUEVA FACTURA ══ -->
<div class="modal-overlay" id="modal-nueva" onclick="clickOverlayNueva(event)">
    <div class="modal modal-xl">
        <div class="modal-header">
            <span class="modal-title" id="modal-nueva-titulo">Nueva factura</span>
            <button class="modal-close" onclick="cerrarNueva()">&times;</button>
        </div>
        <div class="modal-body">

            <div class="builder-section">
                <div class="builder-section-title">Datos del cliente</div>
                <div class="form-row">
                    <div class="form-group" style="position:relative;">
                        <label>Cliente *</label>
                        <input type="text" id="n-cliente" placeholder="Buscar cliente..." autocomplete="off" oninput="filtrarClientesFac()">
                        <div id="clientes-fac-dropdown" style="display:none;position:absolute;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);z-index:100;max-height:180px;overflow-y:auto;width:100%;box-shadow:var(--shadow);top:100%;left:0;"></div>
                    </div>
                    <div class="form-group">
                        <label>Uso de CFDI</label>
                        <select id="n-cfdi" style="padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.85rem;outline:none;width:100%;">
                            <option value="G01">G01 - Adquisición de mercancias</option>
                            <option value="G02">G02 - Devoluciones, descuentos o bonificaciones</option>
                            <option value="G03" selected>G03 - Gastos en general</option>
                            <option value="I01">I01 - Construcciones</option>
                            <option value="I04">I04 - Equipo de computo y accesorios</option>
                            <option value="I08">I08 - Otra maquinaria y equipo</option>
                            <option value="S01">S01 - Sin efectos fiscales</option>
                            <option value="CP01">CP01 - Pagos</option>
                        </select>
                    </div>
                </div>
                <div id="cliente-fiscal-preview" style="display:none;margin-top:10px;padding:12px 16px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.78rem;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <div><span style="color:var(--muted);font-size:0.68rem;text-transform:uppercase;font-weight:600;">RFC</span><div id="prev-rfc" style="color:var(--text);font-weight:600;margin-top:2px;">—</div></div>
                        <div><span style="color:var(--muted);font-size:0.68rem;text-transform:uppercase;font-weight:600;">C.P.</span><div id="prev-cp" style="color:var(--text);font-weight:600;margin-top:2px;">—</div></div>
                        <div style="grid-column:1/-1;"><span style="color:var(--muted);font-size:0.68rem;text-transform:uppercase;font-weight:600;">Régimen fiscal</span><div id="prev-regimen" style="color:var(--text);font-weight:600;margin-top:2px;">—</div></div>
                    </div>
                    <div id="aviso-fiscal" style="display:none;margin-top:8px;padding:8px 10px;background:rgba(239,68,68,0.08);border-radius:6px;color:#ef4444;font-size:0.72rem;">
                        ⚠️ Este cliente no tiene datos fiscales completos. <a href="contactos.php" style="color:#ef4444;font-weight:600;">Completar en Contactos</a>
                    </div>
                </div>
            </div>

            <!-- IVA informativo, ya no editable -->
            <div class="iva-info-banner">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                Los precios ya incluyen IVA. El desglose es solo informativo y no se suma al total.
            </div>

            <div class="builder-section">
                <div class="builder-section-title">Productos y servicios</div>
                <div style="display:grid;grid-template-columns:1fr 60px 100px 32px;gap:8px;margin-bottom:6px;">
                    <span style="font-size:0.68rem;color:var(--muted);font-weight:600;text-transform:uppercase;">Descripción</span>
                    <span style="font-size:0.68rem;color:var(--muted);font-weight:600;text-transform:uppercase;">Cant.</span>
                    <span style="font-size:0.68rem;color:var(--muted);font-weight:600;text-transform:uppercase;">Precio (c/IVA)</span>
                    <span></span>
                </div>
                <div class="items-list" id="items-list"></div>
                <div class="add-row-btns">
                    <button class="btn-sm btn-outline" onclick="agregarProducto()">+ Del inventario</button>
                    <button class="btn-sm btn-outline" onclick="agregarServicio()">+ Servicio</button>
                </div>
            </div>

            <div class="form-group">
                <label>Notas (opcional)</label>
                <input type="text" id="n-notas" placeholder="Ej. Pago a 30 días">
            </div>

            <div class="totales-preview">
                <div class="t-row"><span>Subtotal (base sin IVA)</span><span id="prev-sub">$0.00</span></div>
                <div class="t-row"><span>IVA (16%, incluido)</span><span id="prev-iva">$0.00</span></div>
                <div class="t-row total"><span>Total a cobrar</span><span id="prev-total">$0.00</span></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarNueva()">Cancelar</button>
            <button class="btn-sm btn-primary" id="btn-guardar-fac" onclick="guardarFactura()">Guardar factura</button>
        </div>
    </div>
</div>

<!-- ══ MODAL SELECTOR DE COTIZACIÓN ══ -->
<div class="modal-overlay" id="modal-sel-cot" onclick="if(event.target===this)cerrarSelCot()">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <span class="modal-title">Seleccionar cotización</span>
            <button class="modal-close" onclick="cerrarSelCot()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="text" id="cot-buscar" placeholder="Buscar cliente o folio..." oninput="filtrarCotsSel()"
                style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
                       background:var(--bg);color:var(--text);font-family:inherit;font-size:0.82rem;outline:none;margin-bottom:10px;">
            <div id="cot-sel-lista" style="max-height:320px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;"></div>
        </div>
    </div>
</div>

<!-- ══ MODAL SELECTOR DE PRODUCTO ══ -->
<div class="modal-overlay" id="modal-prod" onclick="if(event.target===this)cerrarProd()">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <span class="modal-title">Seleccionar producto</span>
            <button class="modal-close" onclick="cerrarProd()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="text" id="prod-buscar" placeholder="Buscar..." oninput="filtrarProds()"
                style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
                       background:var(--bg);color:var(--text);font-family:inherit;font-size:0.82rem;outline:none;margin-bottom:10px;">
            <div id="prod-lista" style="max-height:280px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;"></div>
        </div>
    </div>
</div>

<!-- ══ MODAL VER FACTURA ══ -->
<div class="modal-overlay" id="modal-ver" onclick="if(event.target===this)cerrarVer()">
    <div class="modal modal-xl">
        <div class="modal-header">
            <span class="modal-title" id="ver-titulo">Factura</span>
            <button class="modal-close" onclick="cerrarVer()">&times;</button>
        </div>
        <div class="modal-body" id="ver-body" style="padding:0;"></div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-sm btn-outline" style="color:#ef4444;border-color:#fecaca;" onclick="eliminarFactura()">Eliminar</button>
            <div style="display:flex;gap:8px;align-items:center;flex-direction:column;">
                <div style="display:flex;gap:8px;">
                    <button class="btn-sm btn-outline" onclick="cerrarVer()">Cerrar</button>
                    <button class="btn-sm btn-outline" onclick="abrirCompartir()">Compartir / PDF</button>
                    <button class="btn-sm btn-timbrar" disabled title="Próximamente disponible">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:4px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Timbrar
                    </button>
                </div>
                <div class="timbrar-tooltip">El timbrado estará disponible próximamente</div>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL COMPARTIR ══ -->
<div class="modal-overlay" id="modal-compartir" onclick="if(event.target===this)cerrarCompartir()">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title">Compartir factura</span>
            <button class="modal-close" onclick="cerrarCompartir()">&times;</button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.82rem;color:var(--muted);">Elige cómo deseas compartir esta factura:</p>
            <div class="share-grid">
                <button class="share-btn whatsapp" onclick="compartirWhatsapp()">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </button>
                <button class="share-btn email" onclick="compartirEmail()">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Correo
                </button>
                <button class="share-btn download" onclick="descargarPDF()">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 15V3m0 12l-4-4m4 4l4-4M2 17l.621 2.485A2 2 0 004.561 21h14.878a2 2 0 001.94-1.515L22 17"/></svg>
                    Descargar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var facturas      = [];
var cotizaciones  = [];
var productosInv  = [];
var clientesLista = [];
var itemsFac      = [];
var facActivaId   = null;
var facActivaData = null;
var clienteSelId  = null;

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

function cargar() {
    Promise.all([
        fetch('controllers/get_facturas.php').then(function(r){ return r.json(); }),
        fetch('controllers/get_cotizaciones.php').then(function(r){ return r.json(); }),
        fetch('controllers/get_inventario.php').then(function(r){ return r.json(); }),
        fetch('controllers/get_clientes.php').then(function(r){ return r.json(); })
    ]).then(function(results) {
        facturas      = results[0];
        cotizaciones  = results[1];
        productosInv  = results[2];
        clientesLista = results[3];
        renderLista(facturas);
        renderStats(facturas);
    }).catch(function(err){ console.error('Error:', err); });
}

function renderStats(data) {
    var hoy = new Date(); var mes = hoy.getMonth(); var anio = hoy.getFullYear();
    document.getElementById('stat-total').textContent = data.length;
    var valorTotal = data.reduce(function(a,c){ return a + parseFloat(c.total); }, 0);
    document.getElementById('stat-valor').textContent = fmt(valorTotal);
    var esteMes = data.filter(function(c) {
        var f = new Date(c.created_at);
        return f.getMonth() === mes && f.getFullYear() === anio;
    }).length;
    document.getElementById('stat-mes').textContent = esteMes;
}

function renderLista(lista) {
    var cont = document.getElementById('fac-contenedor');
    if (!lista.length) {
        cont.innerHTML = '<p style="text-align:center;color:var(--muted);padding:40px 0;font-size:0.82rem;">Sin facturas registradas</p>';
        return;
    }
    var html = '<div class="fac-list">';
    lista.forEach(function(f) {
        var fecha = new Date(f.created_at);
        var fechaStr = fecha.toLocaleDateString('es-MX', {day:'2-digit',month:'short',year:'numeric'});
        html += '<div class="fac-item" onclick="verFactura(' + f.id + ')">' +
            '<div class="fac-folio">' + esc(f.folio) + '</div>' +
            '<div class="fac-info">' +
                '<div class="fac-cliente">' + esc(f.nombre_cliente) + '</div>' +
                '<div class="fac-rfc">' + esc(f.rfc) + ' · ' + fechaStr + '</div>' +
            '</div>' +
            '<span class="estatus-badge estatus-' + f.estatus + '">' + (f.estatus === 'timbrada' ? '✓ Timbrada' : 'Borrador') + '</span>' +
            '<div class="fac-total">' + fmt(f.total) + '</div>' +
        '</div>';
    });
    html += '</div>';
    cont.innerHTML = html;
}

function filtrar() {
    var q = document.getElementById('buscador').value.toLowerCase();
    renderLista(facturas.filter(function(f) {
        return f.nombre_cliente.toLowerCase().includes(q) ||
               f.folio.toLowerCase().includes(q) ||
               (f.rfc || '').toLowerCase().includes(q);
    }));
}

function abrirNueva() {
    itemsFac = []; clienteSelId = null;
    document.getElementById('modal-nueva-titulo').textContent = 'Nueva factura';
    document.getElementById('n-cliente').value = '';
    document.getElementById('n-notas').value   = '';
    document.getElementById('cliente-fiscal-preview').style.display = 'none';
    document.getElementById('btn-guardar-fac').disabled = false;
    document.getElementById('btn-guardar-fac').textContent = 'Guardar factura';
    renderItems();
    actualizarTotales();
    document.getElementById('modal-nueva').classList.add('open');
    setTimeout(function(){ document.getElementById('n-cliente').focus(); }, 100);
}

function cerrarNueva() { document.getElementById('modal-nueva').classList.remove('open'); }
function clickOverlayNueva(e) { if (e.target === document.getElementById('modal-nueva')) cerrarNueva(); }

function abrirDesdeCotizacion() {
    document.getElementById('cot-buscar').value = '';
    filtrarCotsSel();
    document.getElementById('modal-sel-cot').classList.add('open');
}
function cerrarSelCot() { document.getElementById('modal-sel-cot').classList.remove('open'); }

function filtrarCotsSel() {
    var q = (document.getElementById('cot-buscar').value || '').toLowerCase();
    var lista = cotizaciones.filter(function(c) {
        return c.nombre_cliente.toLowerCase().includes(q) || c.folio.toLowerCase().includes(q);
    });
    var el = document.getElementById('cot-sel-lista');
    if (!lista.length) {
        el.innerHTML = '<p style="text-align:center;color:var(--muted);font-size:0.78rem;padding:20px 0;">Sin resultados</p>';
        return;
    }
    var html = '';
    lista.forEach(function(c) {
        var fecha = new Date(c.created_at).toLocaleDateString('es-MX', {day:'2-digit',month:'short',year:'numeric'});
        html += '<div class="cot-sel-item" onclick="cargarDesdeCot(' + c.id + ')">' +
            '<div class="cot-sel-folio">' + esc(c.folio) + '</div>' +
            '<div class="cot-sel-info">' +
                '<div class="cot-sel-cliente">' + esc(c.nombre_cliente) + '</div>' +
                '<div class="cot-sel-fecha">' + fecha + '</div>' +
            '</div>' +
            '<div class="cot-sel-total">' + fmt(c.total) + '</div>' +
        '</div>';
    });
    el.innerHTML = html;
}

function cargarDesdeCot(id) {
    fetch('controllers/get_cotizacion.php?id=' + id)
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (!res.ok) { alert('Error al cargar cotización'); return; }
            cerrarSelCot();
            var c = res.data;

            // Los items de la cotización ya traen el precio con IVA incluido
            itemsFac = c.items.map(function(i) {
                return { tipo: i.tipo, nombre: i.nombre, cantidad: parseInt(i.cantidad), precio_unitario: parseFloat(i.precio_unitario) };
            });

            document.getElementById('n-cliente').value = c.nombre_cliente;

            var clienteEncontrado = clientesLista.find(function(cl) {
                return cl.nombre.toLowerCase() === c.nombre_cliente.toLowerCase();
            });
            if (clienteEncontrado) selClienteFac(clienteEncontrado);

            document.getElementById('n-notas').value = c.notas || '';
            document.getElementById('modal-nueva-titulo').textContent = 'Factura desde ' + c.folio;
            document.getElementById('btn-guardar-fac').disabled = false;
            document.getElementById('btn-guardar-fac').textContent = 'Guardar factura';

            renderItems();
            actualizarTotales();
            document.getElementById('modal-nueva').classList.add('open');
        });
}

function filtrarClientesFac() {
    var q  = document.getElementById('n-cliente').value.toLowerCase();
    var dd = document.getElementById('clientes-fac-dropdown');
    clienteSelId = null;
    document.getElementById('cliente-fiscal-preview').style.display = 'none';
    if (!q) { dd.style.display = 'none'; return; }
    var matches = clientesLista.filter(function(c){ return c.nombre.toLowerCase().includes(q) || (c.alias||'').toLowerCase().includes(q); }).slice(0, 6);
    if (!matches.length) { dd.style.display = 'none'; return; }
    dd.style.display = 'block';
    var html = '';
    matches.forEach(function(c) {
        var display = c.alias ? c.alias + ' (' + c.nombre + ')' : c.nombre;
        html += '<div data-id="' + c.id + '" onclick="selClienteFacById(' + c.id + ')" ' +
            'style="padding:10px 12px;cursor:pointer;font-size:0.82rem;color:var(--text);border-bottom:1px solid var(--border);" ' +
            'onmouseover="this.style.background=\'var(--bg)\'" onmouseout="this.style.background=\'\'">' +
            '<div style="font-weight:600;">' + esc(display) + '</div>' +
            (c.rfc ? '<div style="font-size:0.68rem;color:var(--muted);">RFC: ' + esc(c.rfc) + '</div>' : '<div style="font-size:0.68rem;color:#f59e0b;">Sin datos fiscales</div>') +
        '</div>';
    });
    dd.innerHTML = html;
}

function selClienteFacById(id) {
    var c = clientesLista.find(function(x){ return x.id == id; });
    if (c) selClienteFac(c);
}

function selClienteFac(c) {
    clienteSelId = c.id;
    document.getElementById('n-cliente').value = c.alias || c.nombre;
    document.getElementById('clientes-fac-dropdown').style.display = 'none';

    document.getElementById('cliente-fiscal-preview').style.display = 'block';
    document.getElementById('prev-rfc').textContent     = c.rfc            || '—';
    document.getElementById('prev-cp').textContent      = c.codigo_postal  || '—';
    document.getElementById('prev-regimen').textContent = c.regimen_fiscal || '—';

    var completo = c.rfc && c.codigo_postal && c.regimen_fiscal;
    document.getElementById('aviso-fiscal').style.display = completo ? 'none' : 'block';
}

document.addEventListener('click', function(e) {
    var dd = document.getElementById('clientes-fac-dropdown');
    if (dd && !dd.contains(e.target) && e.target.id !== 'n-cliente') dd.style.display = 'none';
});

function agregarProducto() {
    document.getElementById('prod-buscar').value = '';
    filtrarProds();
    document.getElementById('modal-prod').classList.add('open');
}
function cerrarProd() { document.getElementById('modal-prod').classList.remove('open'); }

function filtrarProds() {
    var q = (document.getElementById('prod-buscar').value || '').toLowerCase();
    var lista = productosInv.filter(function(p){ return p.nombre.toLowerCase().includes(q) || (p.sku||'').toLowerCase().includes(q); });
    var el = document.getElementById('prod-lista');
    if (!lista.length) { el.innerHTML = '<p style="text-align:center;color:var(--muted);font-size:0.78rem;padding:20px 0;">Sin resultados</p>'; return; }
    var html = '';
    lista.forEach(function(p) {
        // El precio del inventario YA incluye IVA — se usa tal cual
        var precio = parseFloat(p.precio_final !== undefined ? p.precio_final : p.precio_sin_iva * 1.16);
        html += '<div class="prod-item-row" data-nombre="' + esc(p.nombre) + '" data-precio="' + precio.toFixed(2) + '" onclick="selProducto(this)" ' +
            'style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;background:var(--surface);" ' +
            'onmouseover="this.style.background=\'var(--bg)\'" onmouseout="this.style.background=\'var(--surface)\'">' +
            '<div><div style="font-size:0.82rem;font-weight:600;color:var(--text);">' + esc(p.nombre) + '</div>' +
            (p.sku ? '<div style="font-size:0.68rem;color:var(--muted);">SKU: ' + esc(p.sku) + '</div>' : '') + '</div>' +
            '<div style="font-size:0.85rem;font-weight:700;color:var(--text);">' + fmt(precio) + '</div>' +
        '</div>';
    });
    el.innerHTML = html;
}

function selProducto(el) {
    var nombre = el.getAttribute('data-nombre');
    var precio = parseFloat(el.getAttribute('data-precio'));
    itemsFac.push({ tipo:'producto', nombre:nombre, cantidad:1, precio_unitario:precio });
    renderItems(); actualizarTotales(); cerrarProd();
}

function agregarServicio() {
    itemsFac.push({ tipo:'servicio', nombre:'', cantidad:1, precio_unitario:0 });
    renderItems(); actualizarTotales();
}

function eliminarItem(i) {
    itemsFac.splice(i, 1);
    renderItems(); actualizarTotales();
}

function renderItems() {
    var el = document.getElementById('items-list');
    if (!itemsFac.length) {
        el.innerHTML = '<p style="font-size:0.78rem;color:var(--muted);padding:8px 0;">Sin productos aún.</p>';
        return;
    }
    var html = '';
    itemsFac.forEach(function(item, i) {
        var precio  = parseFloat(item.precio_unitario) || 0;
        var base    = precio / 1.16;
        var ivaHint = '<div class="iva-hint">base: ' + fmt(base) + ' + IVA: ' + fmt(precio - base) + '</div>';

        html += '<div class="item-row">' +
            '<div>' +
                '<input type="text" value="' + esc(item.nombre) + '" placeholder="' + (item.tipo==='servicio'?'Nombre del servicio':'Producto') + '" onchange="itemsFac[' + i + '].nombre=this.value">' +
                ivaHint +
            '</div>' +
            '<input type="number" value="' + item.cantidad + '" min="1" onchange="itemsFac[' + i + '].cantidad=parseInt(this.value)||1;actualizarTotales()">' +
            '<input type="number" value="' + parseFloat(item.precio_unitario).toFixed(2) + '" min="0" step="0.01" onchange="itemsFac[' + i + '].precio_unitario=parseFloat(this.value)||0;actualizarTotales();renderItems()">' +
            '<button class="item-del" onclick="eliminarItem(' + i + ')">&times;</button>' +
        '</div>';
    });
    el.innerHTML = html;
}

// El total = suma de precios finales (ya con IVA incluido). El desglose es informativo.
function actualizarTotales() {
    var totalFinal = itemsFac.reduce(function(a,i){ return a + (parseFloat(i.precio_unitario)||0) * (parseInt(i.cantidad)||1); }, 0);
    var base = totalFinal / 1.16;
    var iva  = totalFinal - base;
    document.getElementById('prev-sub').textContent   = fmt(base);
    document.getElementById('prev-iva').textContent   = fmt(iva);
    document.getElementById('prev-total').textContent = fmt(totalFinal);
}

function guardarFactura() {
    var clienteNombre = document.getElementById('n-cliente').value.trim();
    if (!clienteNombre) { resaltar('n-cliente'); return; }
    if (!itemsFac.length) { alert('Agrega al menos un producto o servicio'); return; }

    var btn = document.getElementById('btn-guardar-fac');
    btn.disabled = true; btn.textContent = 'Guardando...';

    var fd = new FormData();
    fd.append('nombre_cliente', clienteNombre);
    fd.append('id_cliente',     clienteSelId || '');
    fd.append('uso_cfdi',       document.getElementById('n-cfdi').value);
    fd.append('notas',          document.getElementById('n-notas').value.trim());
    fd.append('items',          JSON.stringify(itemsFac));

    fetch('controllers/add_factura.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                cerrarNueva(); cargar();
                setTimeout(function(){ verFactura(res.id); }, 400);
            } else {
                alert('Error: ' + res.msg);
                btn.disabled = false; btn.textContent = 'Guardar factura';
            }
        });
}

function verFactura(id) {
    fetch('controllers/get_factura.php?id=' + id)
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (!res.ok) { alert('Error al cargar'); return; }
            facActivaId   = id;
            facActivaData = res.data;
            document.getElementById('ver-titulo').textContent = res.data.folio;
            document.getElementById('ver-body').innerHTML = renderTicketFactura(res.data);
            document.getElementById('modal-ver').classList.add('open');
        });
}

function renderTicketFactura(f) {
    var fecha    = new Date(f.created_at);
    var fechaStr = fecha.toLocaleDateString('es-MX', {weekday:'long', day:'2-digit', month:'long', year:'numeric'});
    var horaStr  = fecha.toLocaleTimeString('es-MX', {hour:'2-digit', minute:'2-digit'});

    var filas = '';
    f.items.forEach(function(i) {
        filas += '<tr>' +
            '<td><span class="tipo-badge ' + (i.tipo==='servicio'?'srv':'') + '">' + (i.tipo==='servicio'?'Servicio':'Producto') + '</span></td>' +
            '<td style="color:#0f172a;">' + esc(i.nombre) + '</td>' +
            '<td style="text-align:center;color:#0f172a;">' + i.cantidad + '</td>' +
            '<td style="color:#0f172a;">' + fmt(i.precio_unitario) + '</td>' +
            '<td style="color:#0f172a;text-align:right;font-weight:600;">' + fmt(i.subtotal) + '</td>' +
        '</tr>';
    });

    var base = parseFloat(f.total) / 1.16;
    var iva  = parseFloat(f.total) - base;

    var notasHtml = f.notas ? '<div class="ticket-notas"><strong>Notas:</strong> ' + esc(f.notas) + '</div>' : '';

    return '<div style="padding:20px;">' +
    '<div class="ticket" id="factura-print">' +
        '<div class="ticket-header">' +
            '<div class="ticket-logo-area">' +
                '<div class="ticket-logo-ring"><img src="assets/logo/logovttr.png" alt="VT"></div>' +
                '<div><div class="ticket-empresa">Villa Tecnia Tampico<small>Materiales para alberca</small></div></div>' +
            '</div>' +
            '<div class="ticket-meta">' +
                '<div class="ticket-folio">' + esc(f.folio) + '</div>' +
                '<div style="font-size:0.65rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;margin-top:4px;">CFDI: ' + esc(f.uso_cfdi||'G03') + '</div>' +
                '<div class="ticket-fecha">' + fechaStr + '<br>' + horaStr + ' hrs</div>' +
            '</div>' +
        '</div>' +

        '<div class="ticket-partes">' +
            '<div class="ticket-parte">' +
                '<div class="ticket-parte-label">Emisor</div>' +
                '<div class="ticket-parte-nombre">Villa Tecnia Tampico</div>' +
                '<div class="ticket-parte-sub">Materiales para alberca</div>' +
            '</div>' +
            '<div class="ticket-parte">' +
                '<div class="ticket-parte-label">Receptor</div>' +
                '<div class="ticket-parte-nombre">' + esc(f.razon_social || f.nombre_cliente) + '</div>' +
                '<div class="ticket-parte-sub">RFC: ' + esc(f.rfc || '—') + '</div>' +
                '<div class="ticket-parte-sub">CP: ' + esc(f.codigo_postal || '—') + '</div>' +
                '<div class="ticket-parte-sub">' + esc(f.regimen_fiscal || '—') + '</div>' +
            '</div>' +
        '</div>' +

        '<table class="ticket-table">' +
            '<thead><tr><th>Tipo</th><th>Descripción</th><th style="text-align:center">Cant.</th><th>P. Unit.</th><th>Subtotal</th></tr></thead>' +
            '<tbody>' + filas + '</tbody>' +
        '</table>' +

        '<div class="ticket-totales">' +
            '<table>' +
                '<tr><td style="color:#475569;padding:5px 8px;">Subtotal (base)</td><td style="color:#0f172a;font-weight:600;text-align:right;padding:5px 8px;">' + fmt(base) + '</td></tr>' +
                '<tr><td style="color:#475569;padding:5px 8px;">IVA (16%, incluido)</td><td style="color:#0f172a;font-weight:600;text-align:right;padding:5px 8px;">' + fmt(iva) + '</td></tr>' +
                '<tr class="total-row"><td style="color:#0f172a;font-weight:700;font-size:1rem;border-top:2px solid #eab308;padding-top:10px;">Total</td><td style="color:#0f172a;font-weight:700;font-size:1rem;border-top:2px solid #eab308;padding-top:10px;text-align:right;">' + fmt(f.total) + '</td></tr>' +
            '</table>' +
        '</div>' +

        notasHtml +

        '<div style="margin-top:20px;padding:10px 16px;background:#fef9c3;border-radius:8px;font-size:0.72rem;color:#854d0e;text-align:center;">' +
            '⚠️ Este documento es un borrador — aún no ha sido timbrado ante el SAT' +
        '</div>' +

        '<div class="ticket-footer">Factura generada por Villa Tecnia Tampico · ' + fechaStr + '</div>' +
    '</div></div>';
}

function cerrarVer() { document.getElementById('modal-ver').classList.remove('open'); }

function eliminarFactura() {
    if (!facActivaId) return;
    if (!confirm('¿Eliminar esta factura?')) return;
    var fd = new FormData();
    fd.append('id', facActivaId);
    fetch('controllers/delete_factura.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarVer(); cargar(); }
            else alert('Error: ' + res.msg);
        });
}

function abrirCompartir()  { document.getElementById('modal-compartir').classList.add('open'); }
function cerrarCompartir() { document.getElementById('modal-compartir').classList.remove('open'); }

function textoResumen() {
    if (!facActivaData) return '';
    var f = facActivaData;
    var txt = '*Factura ' + f.folio + ' - Villa Tecnia Tampico*\n';
    txt += 'Cliente: ' + f.nombre_cliente + '\n';
    txt += 'RFC: ' + (f.rfc || '—') + '\n\n';
    f.items.forEach(function(i){ txt += '• ' + i.nombre + ' x' + i.cantidad + ' — ' + fmt(i.subtotal) + '\n'; });
    txt += '\n*Total (IVA incluido): ' + fmt(f.total) + '*';
    return txt;
}

function compartirWhatsapp() {
    window.open('https://wa.me/?text=' + encodeURIComponent(textoResumen()), '_blank');
}

function compartirEmail() {
    if (!facActivaData) return;
    var asunto = encodeURIComponent('Factura ' + facActivaData.folio + ' - Villa Tecnia Tampico');
    var cuerpo = encodeURIComponent(textoResumen().replace(/\*/g,''));
    window.open('mailto:?subject=' + asunto + '&body=' + cuerpo, '_blank');
}

function descargarPDF() {
    var ticket = document.getElementById('factura-print');
    if (!ticket) return;
    var estilos = [
        'body{font-family:Poppins,sans-serif;margin:0;padding:20px;}',
        '.ticket{max-width:680px;margin:0 auto;padding:36px 40px;}',
        '.ticket-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;padding-bottom:20px;border-bottom:2px solid #eab308;}',
        '.ticket-logo-ring{width:48px;height:48px;border-radius:50%;border:2px solid #eab308;overflow:hidden;background:#0f172a;display:flex;align-items:center;justify-content:center;}',
        '.ticket-logo-ring img{width:75%;height:75%;object-fit:contain;}',
        '.ticket-logo-area{display:flex;align-items:center;gap:12px;}',
        '.ticket-empresa{font-size:1rem;font-weight:700;color:#0f172a;line-height:1.2;}',
        '.ticket-empresa small{font-size:0.65rem;color:#64748b;font-weight:400;display:block;}',
        '.ticket-folio{font-size:1rem;font-weight:700;color:#eab308;}',
        '.ticket-fecha{font-size:0.72rem;color:#64748b;margin-top:4px;}',
        '.ticket-meta{text-align:right;}',
        '.ticket-partes{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px;}',
        '.ticket-parte{padding:12px 16px;background:#f8fafc;border-radius:8px;border-left:3px solid #eab308;}',
        '.ticket-parte-label{font-size:0.6rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;}',
        '.ticket-parte-nombre{font-size:0.88rem;font-weight:600;color:#0f172a;margin-top:3px;}',
        '.ticket-parte-sub{font-size:0.72rem;color:#64748b;margin-top:2px;}',
        'table{width:100%;border-collapse:collapse;font-size:0.82rem;margin-bottom:20px;}',
        'thead th{text-align:left;padding:8px 10px;background:#0f172a;color:#f1f5f9;font-size:0.68rem;font-weight:600;text-transform:uppercase;}',
        'thead th:last-child{text-align:right;}',
        'tbody td{padding:10px;border-bottom:1px solid #e2e8f0;color:#0f172a;}',
        'tbody td:last-child{text-align:right;font-weight:600;}',
        '.tipo-badge{font-size:0.6rem;padding:2px 6px;border-radius:999px;font-weight:600;background:#dbeafe;color:#1d4ed8;}',
        '.tipo-badge.srv{background:#f3e8ff;color:#7c3aed;}',
        '.ticket-totales{margin-left:auto;width:240px;}',
        '.ticket-totales td{padding:5px 8px;color:#475569;}',
        '.ticket-totales td:last-child{text-align:right;font-weight:600;color:#0f172a;}',
        '.total-row td{font-size:1rem;font-weight:700;color:#0f172a;border-top:2px solid #eab308;padding-top:10px;}',
        '.ticket-notas{margin-top:20px;padding:12px 16px;background:#f8fafc;border-radius:8px;font-size:0.78rem;color:#475569;}',
        '.ticket-footer{margin-top:28px;padding-top:16px;border-top:1px solid #e2e8f0;text-align:center;font-size:0.7rem;color:#94a3b8;}'
    ].join('');
    var win = window.open('', '_blank');
    win.document.write('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' + (facActivaData ? facActivaData.folio : 'Factura') + '</title>' +
        '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">' +
        '<style>' + estilos + '</style></head><body>' + ticket.outerHTML + '</body></html>');
    win.document.close();
    setTimeout(function(){ win.print(); }, 800);
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

cargar();
</script>
</body>
</html>