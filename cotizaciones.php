<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user'])) { header('Location: index.php'); exit; }
$pagina_actual = 'cotizaciones';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Cotizaciones</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<link rel="stylesheet" href="css/dashboard.css">
<style>
.cot-list { display: flex; flex-direction: column; gap: 8px; }
.cot-item { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); display: flex; align-items: center; gap: 16px; padding: 14px 18px; transition: box-shadow 0.15s; cursor: pointer; }
.cot-item:hover { box-shadow: var(--shadow); background: var(--bg); }
.cot-folio { font-size: 0.72rem; font-weight: 700; color: var(--accent); letter-spacing: 0.06em; white-space: nowrap; min-width: 80px; }
.cot-info { flex: 1; min-width: 0; }
.cot-cliente { font-size: 0.88rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cot-fecha { font-size: 0.72rem; color: var(--muted); margin-top: 2px; }
.cot-total { font-size: 0.95rem; font-weight: 700; color: var(--text); white-space: nowrap; flex-shrink: 0; }

.ticket { background: #fff; color: #000; max-width: 680px; margin: 0 auto; padding: 36px 40px; border-radius: var(--radius); border: 1px solid #e2e8f0; font-family: 'Poppins', sans-serif; }
.ticket-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 2px solid #eab308; }
.ticket-logo-area { display: flex; align-items: center; gap: 12px; }
.ticket-logo-ring { width: 48px; height: 48px; border-radius: 50%; border: 2px solid #eab308; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #0f172a; }
.ticket-logo-ring img { width: 75%; height: 75%; object-fit: contain; }
.ticket-empresa { font-size: 1rem; font-weight: 700; color: #0f172a; line-height: 1.2; }
.ticket-empresa small { font-size: 0.65rem; color: #64748b; font-weight: 400; display: block; }
.ticket-meta { text-align: right; }
.ticket-folio { font-size: 1rem; font-weight: 700; color: #eab308; }
.ticket-fecha { font-size: 0.72rem; color: #64748b; margin-top: 4px; }
.ticket-cliente { margin-bottom: 24px; padding: 12px 16px; background: #f8fafc; border-radius: 8px; border-left: 3px solid #eab308; }
.ticket-cliente-label { font-size: 0.65rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; }
.ticket-cliente-nombre { font-size: 0.95rem; font-weight: 600; color: #0f172a; margin-top: 2px; }
.ticket-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.82rem; }
.ticket-table thead th { text-align: left; padding: 8px 10px; background: #0f172a; color: #f1f5f9; font-size: 0.68rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }
.ticket-table thead th:last-child { text-align: right; }
.ticket-table tbody td { padding: 10px; border-bottom: 1px solid #e2e8f0; color: #0f172a; vertical-align: middle; }
.ticket-table tbody td:last-child { text-align: right; font-weight: 600; }
.ticket-table tbody tr:last-child td { border-bottom: none; }
.ticket-table .tipo-badge { font-size: 0.6rem; padding: 2px 6px; border-radius: 999px; font-weight: 600; background: #dbeafe; color: #1d4ed8; white-space: nowrap; }
.ticket-table .tipo-badge.srv { background: #f3e8ff; color: #7c3aed; }
.ticket-totales { margin-left: auto; width: 240px; }
.ticket-totales table { width: 100%; font-size: 0.82rem; min-width: unset !important; }
.ticket-totales td { padding: 5px 8px; color: #475569; }
.ticket-totales td:last-child { text-align: right; font-weight: 600; color: #0f172a; }
.ticket-totales .total-row td { font-size: 1rem; font-weight: 700; color: #0f172a; border-top: 2px solid #eab308; padding-top: 10px; }
.ticket-notas { margin-top: 20px; padding: 12px 16px; background: #f8fafc; border-radius: 8px; font-size: 0.78rem; color: #475569; }
.ticket-footer { margin-top: 28px; padding-top: 16px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 0.7rem; color: #94a3b8; }
.ticket-table tbody tr:hover { background:transparent !important; }
#ticket-print tbody td { color:#0f172a !important; background:#fff !important; }
#ticket-print tbody tr:hover { background:#fff !important; }
.ticket table { min-width:unset !important; }
.ticket-totales table tbody td { padding:5px 8px !important; border-bottom:none !important; color:#475569 !important; }
.ticket-totales table tbody td:last-child { color:#0f172a !important; font-weight:600 !important; }
.ticket-totales .total-row td { color:#0f172a !important; font-weight:700 !important; border-top:2px solid #eab308 !important; }

.modal-xl { max-width: 780px; }
.builder-section { margin-bottom: 20px; }
.builder-section-title { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); margin-bottom: 10px; }
.items-list { display: flex; flex-direction: column; gap: 6px; margin-bottom: 10px; }
.item-row { display: grid; grid-template-columns: 1fr 60px 100px 32px; gap: 8px; align-items: center; }
.item-row input { padding: 7px 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg); color: var(--text); font-family: inherit; font-size: 0.8rem; outline: none; width: 100%; }
.item-row input:focus { border-color: var(--accent); }
.item-del { background: none; border: none; cursor: pointer; color: var(--muted); font-size: 1.1rem; line-height: 1; display: flex; align-items: center; justify-content: center; padding: 4px; transition: color 0.15s; }
.item-del:hover { color: #ef4444; }
.add-row-btns { display: flex; gap: 8px; flex-wrap: wrap; }

.iva-info-banner {
    display:flex; align-items:center; gap:10px;
    padding:10px 14px; background:rgba(34,197,94,0.08);
    border:1px solid rgba(34,197,94,0.2); border-radius:var(--radius-sm);
    margin-bottom:14px; font-size:0.78rem; color:#15803d;
}
[data-theme="dark"] .iva-info-banner { background:rgba(34,197,94,0.1); color:#4ade80; }
.iva-info-banner svg { width:16px;height:16px;flex-shrink:0; }

.totales-preview { background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 12px 16px; font-size: 0.82rem; }
.totales-preview .t-row { display: flex; justify-content: space-between; padding: 3px 0; color: var(--muted); }
.totales-preview .t-row.total { font-size: 1rem; font-weight: 700; color: var(--text); border-top: 1px solid var(--border); margin-top: 6px; padding-top: 8px; }

.iva-hint { font-size: 0.65rem; color: var(--muted); margin-top: 2px; white-space: nowrap; }
@media print { .iva-hint { display: none !important; } }

.share-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-top: 16px; }
.share-btn { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px 10px; border: 1px solid var(--border); border-radius: var(--radius); cursor: pointer; background: var(--surface); color: var(--text); font-family: inherit; font-size: 0.78rem; font-weight: 600; transition: background 0.15s, border-color 0.15s; text-decoration: none; }
.share-btn:hover { background: var(--bg); border-color: var(--accent); }
.share-btn svg { width: 28px; height: 28px; }
.share-btn.whatsapp svg { color: #25d366; }
.share-btn.email svg { color: #4b8ef1; }
.share-btn.download svg { color: var(--accent); }

@media (max-width: 600px) {
    .item-row { grid-template-columns: 1fr 50px 90px 28px; }
    .ticket { padding: 20px; }
    .ticket-header { flex-direction: column; gap: 12px; }
    .ticket-meta { text-align: left; }
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
            <span class="page-title">Cotizaciones</span>
        </div>
        <div class="topbar-right">
            <button class="btn-sm btn-primary" onclick="abrirNueva()">+ Nueva cotización</button>
        </div>
    </header>

    <div class="content">
        <div class="stats-grid stats-2" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="stat-label">Total cotizaciones</div>
                <div class="stat-value" id="stat-total">—</div>
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
                <span class="table-title">Historial de cotizaciones</span>
                <input type="text" id="buscador" placeholder="Buscar cliente o folio..."
                    oninput="filtrar()"
                    style="padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
                           font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);
                           outline:none;width:220px;max-width:100%;">
            </div>
            <div style="padding:16px;" id="cot-contenedor">
                <div class="cot-list"><p style="text-align:center;color:var(--muted);padding:40px 0;font-size:0.82rem;">Cargando...</p></div>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL NUEVA COTIZACIÓN ══ -->
<div class="modal-overlay" id="modal-nueva" onclick="clickOverlayNueva(event)">
    <div class="modal modal-xl">
        <div class="modal-header">
            <span class="modal-title" id="modal-nueva-titulo">Nueva cotización</span>
            <button class="modal-close" onclick="cerrarNueva()">&times;</button>
        </div>
        <div class="modal-body">

            <!-- Cliente -->
            <div class="builder-section">
                <div class="builder-section-title">Cliente</div>
                <div class="form-row">
                    <div class="form-group" style="position:relative;">
                        <label>Nombre del cliente *</label>
                        <input type="text" id="n-cliente" placeholder="Escribe o selecciona..." autocomplete="off" oninput="filtrarClientes()">
                        <div id="clientes-dropdown" style="display:none;position:absolute;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);z-index:100;max-height:200px;overflow-y:auto;width:100%;box-shadow:var(--shadow);"></div>
                    </div>
                    <div class="form-group">
                        <label>Notas (opcional)</label>
                        <input type="text" id="n-notas" placeholder="Ej. Válida por 15 días">
                    </div>
                </div>
            </div>

            <!-- IVA info banner (ya no es editable, solo informativo) -->
            <div class="iva-info-banner">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                Los precios ya incluyen IVA. El desglose es solo informativo y no se suma al total.
            </div>

            <!-- Items -->
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
                    <button class="btn-sm btn-outline" onclick="abrirNuevoArticulo()">+ Nuevo artículo</button>
                </div>
            </div>

            <!-- Totales preview -->
            <div class="totales-preview" id="totales-preview">
                <div class="t-row"><span>Subtotal (base sin IVA)</span><span id="prev-sub">$0.00</span></div>
                <div class="t-row"><span>IVA (16%, incluido)</span><span id="prev-iva">$0.00</span></div>
                <div class="t-row total"><span>Total a cobrar</span><span id="prev-total">$0.00</span></div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarNueva()">Cancelar</button>
            <button class="btn-sm btn-primary" id="btn-guardar-cot" onclick="guardarCotizacion()">Guardar cotización</button>
        </div>
    </div>
</div>

<!-- ══ MODAL NUEVO CLIENTE (desde cotización) ══ -->
<div class="modal-overlay" id="modal-cliente" onclick="if(event.target===this)cerrarNuevoCliente()">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <span class="modal-title">Nuevo cliente</span>
            <button class="modal-close" onclick="cerrarNuevoCliente()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" id="nc-nombre" placeholder="Nombre completo o empresa">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" id="nc-telefono" placeholder="Opcional">
                </div>
                <div class="form-group">
                    <label>Correo</label>
                    <input type="email" id="nc-correo" placeholder="Opcional">
                </div>
            </div>
            <p class="form-hint">Solo el nombre es obligatorio. Los datos fiscales se pueden completar después en Contactos.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarNuevoCliente()">Cancelar</button>
            <button class="btn-sm btn-primary" id="btn-guardar-cliente" onclick="guardarNuevoCliente()">Guardar cliente</button>
        </div>
    </div>
</div>

<!-- ══ MODAL NUEVO ARTÍCULO ══ -->
<div class="modal-overlay" id="modal-articulo" onclick="if(event.target===this)cerrarNuevoArticulo()">
    <div class="modal" style="max-width:380px;">
        <div class="modal-header">
            <span class="modal-title">Nuevo artículo</span>
            <button class="modal-close" onclick="cerrarNuevoArticulo()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" id="na-nombre" placeholder="Nombre del artículo">
            </div>
            <div class="form-group">
                <label>Precio final (con IVA) *</label>
                <input type="number" id="na-precio" placeholder="0.00" min="0" step="0.01" oninput="previewArticulo()">
            </div>
            <div class="precio-preview-articulo" id="na-preview" style="display:none;font-size:0.78rem;color:var(--muted);padding:8px 12px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);">
                Base: <strong id="na-base" style="color:var(--text);">$0.00</strong> + IVA: <strong id="na-iva" style="color:var(--text);">$0.00</strong>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-sm btn-outline" onclick="usarArticuloTemporal()">No guardar</button>
            <button class="btn-sm btn-primary" onclick="guardarNuevoArticulo()">Guardar en inventario</button>
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

<!-- ══ MODAL VER COTIZACIÓN ══ -->
<div class="modal-overlay" id="modal-ver" onclick="if(event.target===this)cerrarVer()">
    <div class="modal modal-xl">
        <div class="modal-header">
            <span class="modal-title" id="ver-titulo">Cotización</span>
            <button class="modal-close" onclick="cerrarVer()">&times;</button>
        </div>
        <div class="modal-body" id="ver-body" style="padding:0;"></div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-sm btn-outline" style="color:#ef4444;border-color:#fecaca;" onclick="eliminarCotizacion()">Eliminar</button>
            <div style="display:flex;gap:8px;">
                <button class="btn-sm btn-outline" onclick="cerrarVer()">Cerrar</button>
                <button class="btn-sm btn-outline" onclick="editarCotizacion()">Editar</button>
                <button class="btn-sm btn-outline" onclick="irARemisionDesdeCot()">Generar remisión</button>
                <button class="btn-sm btn-primary" onclick="abrirCompartir()">Compartir / Descargar</button>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL COMPARTIR ══ -->
<div class="modal-overlay" id="modal-compartir" onclick="if(event.target===this)cerrarCompartir()">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title">Compartir cotización</span>
            <button class="modal-close" onclick="cerrarCompartir()">&times;</button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.82rem;color:var(--muted);">Elige cómo deseas compartir esta cotización:</p>
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

<!-- ══ MODAL POST-GUARDAR ══ -->
<div class="modal-overlay" id="modal-post" onclick="if(event.target===this)cerrarPost()">
    <div class="modal" style="max-width:380px;">
        <div class="modal-header">
            <span class="modal-title">Cotización guardada</span>
            <button class="modal-close" onclick="cerrarPost()">&times;</button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.88rem;color:var(--text);margin-bottom:6px;">
                <strong id="post-folio"></strong> fue guardada correctamente.
            </p>
            <p style="font-size:0.8rem;color:var(--muted);">¿Deseas generar una nota de remisión para esta cotización?</p>
        </div>
        <div class="modal-footer" style="flex-direction:column;gap:8px;">
            <button class="btn-sm btn-primary" style="width:100%;justify-content:center;" onclick="irARemision()">
                Generar nota de remisión
            </button>
            <button class="btn-sm btn-outline" style="width:100%;justify-content:center;" onclick="cerrarPost()">
                Solo guardar
            </button>
        </div>
    </div>
</div>
<script>
var cotizaciones  = [];
var productosInv  = [];
var clientesLista = [];
var itemsCot      = [];
var cotActivaId   = null;
var cotActivaData = null;
var editandoId    = null; // si no es null, estamos editando esa cotización (creará copia y ofrecerá borrar la original)

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
        fetch('controllers/get_cotizaciones.php').then(function(r){ return r.json(); }),
        fetch('controllers/get_inventario.php').then(function(r){ return r.json(); }),
        fetch('controllers/get_clientes.php').then(function(r){ return r.json(); })
    ]).then(function(results) {
        cotizaciones  = results[0];
        productosInv  = results[1];
        clientesLista = results[2];
        renderLista(cotizaciones);
        renderStats(cotizaciones);
    }).catch(function(err) {
        console.error('Error al cargar datos:', err);
    });
}

function renderStats(data) {
    var hoy  = new Date();
    var mes  = hoy.getMonth();
    var anio = hoy.getFullYear();
    document.getElementById('stat-total').textContent = data.length;
    var esteMes = data.filter(function(c) {
        var f = new Date(c.created_at);
        return f.getMonth() === mes && f.getFullYear() === anio;
    }).length;
    document.getElementById('stat-mes').textContent = esteMes;
}

function renderLista(lista) {
    var cont = document.getElementById('cot-contenedor');
    if (!lista.length) {
        cont.innerHTML = '<p style="text-align:center;color:var(--muted);padding:40px 0;font-size:0.82rem;">Sin cotizaciones registradas</p>';
        return;
    }
    var html = '<div class="cot-list">';
    for (var i = 0; i < lista.length; i++) {
        var c = lista[i];
        var fecha = new Date(c.created_at);
        var fechaStr = fecha.toLocaleDateString('es-MX', {day:'2-digit',month:'short',year:'numeric'});
        var horaStr  = fecha.toLocaleTimeString('es-MX', {hour:'2-digit',minute:'2-digit'});
        html += '<div class="cot-item" onclick="verCotizacion(' + c.id + ')">' +
            '<div class="cot-folio">' + esc(c.folio) + '</div>' +
            '<div class="cot-info">' +
                '<div class="cot-cliente">' + esc(c.nombre_cliente) + '</div>' +
                '<div class="cot-fecha">' + fechaStr + ' · ' + horaStr + '</div>' +
            '</div>' +
            '<div class="cot-total">' + fmt(c.total) + '</div>' +
        '</div>';
    }
    html += '</div>';
    cont.innerHTML = html;
}

function filtrar() {
    var q = document.getElementById('buscador').value.toLowerCase();
    renderLista(cotizaciones.filter(function(c) {
        return c.nombre_cliente.toLowerCase().includes(q) || c.folio.toLowerCase().includes(q);
    }));
}

// ── Nueva cotización ──
function abrirNueva() {
    itemsCot = [];
    editandoId = null;
    document.getElementById('modal-nueva-titulo').textContent = 'Nueva cotización';
    document.getElementById('n-cliente').value = '';
    document.getElementById('n-notas').value   = '';
    renderItems();
    actualizarTotales();
    document.getElementById('btn-guardar-cot').disabled = false;
    document.getElementById('btn-guardar-cot').textContent = 'Guardar cotización';
    document.getElementById('modal-nueva').classList.add('open');
    setTimeout(function(){ document.getElementById('n-cliente').focus(); }, 100);
}

function cerrarNueva() { document.getElementById('modal-nueva').classList.remove('open'); }
function clickOverlayNueva(e) { if (e.target === document.getElementById('modal-nueva')) cerrarNueva(); }


// ── Cliente nuevo desde cotización ──
function abrirNuevoCliente() {
    document.getElementById('nc-nombre').value   = document.getElementById('n-cliente').value.trim();
    document.getElementById('nc-telefono').value = '';
    document.getElementById('nc-correo').value   = '';
    document.getElementById('btn-guardar-cliente').disabled = false;
    document.getElementById('btn-guardar-cliente').textContent = 'Guardar cliente';
    document.getElementById('modal-cliente').classList.add('open');
    setTimeout(function(){ document.getElementById('nc-nombre').focus(); }, 100);
}

function cerrarNuevoCliente() { document.getElementById('modal-cliente').classList.remove('open'); }

function guardarNuevoCliente() {
    var nombre = document.getElementById('nc-nombre').value.trim();
    if (!nombre) { resaltar('nc-nombre'); return; }

    var btn = document.getElementById('btn-guardar-cliente');
    btn.disabled = true; btn.textContent = 'Guardando...';

    var fd = new FormData();
    fd.append('nombre',    nombre);
    fd.append('telefono',  document.getElementById('nc-telefono').value.trim());
    fd.append('correo',    document.getElementById('nc-correo').value.trim());
    fd.append('direccion', '');
    fd.append('notas',     '');
    fd.append('alias', '');
    fd.append('razon_social', '');
    fd.append('rfc', '');
    fd.append('codigo_postal', '');
    fd.append('regimen_fiscal', '');

    fetch('controllers/add_cliente.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                cerrarNuevoCliente();
                fetch('controllers/get_clientes.php').then(function(r){ return r.json(); }).then(function(clts){
                    clientesLista = clts;
                    document.getElementById('n-cliente').value = nombre;
                });
            } else {
                alert('Error: ' + res.msg);
                btn.disabled = false; btn.textContent = 'Guardar cliente';
            }
        });
}

// ── Autocomplete clientes ──
function filtrarClientes() {
    var q  = document.getElementById('n-cliente').value.toLowerCase();
    var dd = document.getElementById('clientes-dropdown');
    if (!q) { dd.style.display = 'none'; return; }
    var matches = clientesLista.filter(function(c){ return c.nombre.toLowerCase().includes(q) || (c.alias||'').toLowerCase().includes(q); }).slice(0, 6);

    var html = '';
    matches.forEach(function(c) {
        html += '<div onclick="selCliente(\'' + esc(c.nombre).replace(/'/g, "\\'") + '\')" ' +
            'style="padding:8px 12px;cursor:pointer;font-size:0.82rem;color:var(--text);border-bottom:1px solid var(--border);" ' +
            'onmouseover="this.style.background=\'var(--bg)\'" onmouseout="this.style.background=\'\'">' +
            esc(c.alias || c.nombre) + '</div>';
    });
    html += '<div onclick="abrirNuevoCliente()" style="padding:8px 12px;cursor:pointer;font-size:0.82rem;color:var(--accent);font-weight:600;" ' +
        'onmouseover="this.style.background=\'var(--bg)\'" onmouseout="this.style.background=\'\'">+ Crear nuevo cliente "' + esc(document.getElementById('n-cliente').value) + '"</div>';

    dd.innerHTML = html;
    dd.style.display = 'block';
}

function selCliente(nombre) {
    document.getElementById('n-cliente').value = nombre;
    document.getElementById('clientes-dropdown').style.display = 'none';
}

document.addEventListener('click', function(e) {
    var dd = document.getElementById('clientes-dropdown');
    if (dd && !dd.contains(e.target) && e.target.id !== 'n-cliente') dd.style.display = 'none';
});

// ── Items ──
function agregarProducto() {
    document.getElementById('prod-buscar').value = '';
    document.getElementById('modal-prod').classList.add('open');
    filtrarProds();
}
function cerrarProd() { document.getElementById('modal-prod').classList.remove('open'); }

function filtrarProds() {
    var q    = (document.getElementById('prod-buscar').value || '').toLowerCase();
    var lista = productosInv.filter(function(p){
        return p.nombre.toLowerCase().includes(q) || (p.sku||'').toLowerCase().includes(q);
    });
    var el = document.getElementById('prod-lista');
    if (!lista.length) {
        el.innerHTML = '<p style="text-align:center;color:var(--muted);font-size:0.78rem;padding:20px 0;">Sin resultados</p>';
        return;
    }
    var html = '';
    lista.forEach(function(p) {
        // El precio del inventario YA incluye IVA — se usa tal cual, sin recalcular
        var precio = parseFloat(p.precio_final);
        html += '<div class="prod-item-row" data-nombre="' + esc(p.nombre) + '" data-precio="' + precio.toFixed(2) + '" ' +
            'onclick="selProducto(this)" ' +
            'style="display:flex;justify-content:space-between;align-items:center;' +
            'padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);' +
            'cursor:pointer;background:var(--surface);transition:background 0.1s;" ' +
            'onmouseover="this.style.background=\'var(--bg)\'" onmouseout="this.style.background=\'var(--surface)\'">' +
            '<div>' +
                '<div style="font-size:0.82rem;font-weight:600;color:var(--text);">' + esc(p.nombre) + '</div>' +
                (p.sku ? '<div style="font-size:0.68rem;color:var(--muted);">SKU: ' + esc(p.sku) + '</div>' : '') +
            '</div>' +
            '<div style="font-size:0.85rem;font-weight:700;color:var(--text);">' + fmt(precio) + '</div>' +
        '</div>';
    });
    el.innerHTML = html;
}

function selProducto(el) {
    var nombre = el.getAttribute('data-nombre');
    var precio = parseFloat(el.getAttribute('data-precio'));
    itemsCot.push({ tipo: 'producto', nombre: nombre, cantidad: 1, precio_unitario: precio });
    renderItems();
    actualizarTotales();
    cerrarProd();
}

function agregarServicio() {
    itemsCot.push({ tipo: 'servicio', nombre: '', cantidad: 1, precio_unitario: 0 });
    renderItems();
    actualizarTotales();
}

function eliminarItem(i) {
    itemsCot.splice(i, 1);
    renderItems();
    actualizarTotales();
}

function renderItems() {
    var el = document.getElementById('items-list');
    if (!itemsCot.length) {
        el.innerHTML = '<p style="font-size:0.78rem;color:var(--muted);padding:8px 0;">Sin productos aún. Agrega del inventario o un servicio.</p>';
        return;
    }
    var html = '';
    itemsCot.forEach(function(item, i) {
        var precio = parseFloat(item.precio_unitario) || 0;
        var base   = precio / 1.16;
        var ivaHint = '<div class="iva-hint">base: ' + fmt(base) + ' + IVA: ' + fmt(precio - base) + '</div>';

        html += '<div class="item-row">' +
            '<div>' +
                '<input type="text" value="' + esc(item.nombre) + '" placeholder="' + (item.tipo === 'servicio' ? 'Nombre del servicio' : 'Producto') + '" ' +
                    'onchange="itemsCot[' + i + '].nombre=this.value">' +
                ivaHint +
            '</div>' +
            '<input type="number" value="' + item.cantidad + '" min="1" ' +
                'onchange="itemsCot[' + i + '].cantidad=parseInt(this.value)||1;actualizarTotales()">' +
            '<input type="number" value="' + parseFloat(item.precio_unitario).toFixed(2) + '" min="0" step="0.01" ' +
                'onchange="itemsCot[' + i + '].precio_unitario=parseFloat(this.value)||0;actualizarTotales();renderItems()">' +
            '<button class="item-del" onclick="eliminarItem(' + i + ')">&times;</button>' +
        '</div>';
    });
    el.innerHTML = html;
}

// El total = suma de precios finales (ya con IVA incluido). El desglose es solo informativo.
function actualizarTotales() {
    var totalFinal = itemsCot.reduce(function(a, i) {
        return a + (parseFloat(i.precio_unitario) || 0) * (parseInt(i.cantidad) || 1);
    }, 0);
    var base = totalFinal / 1.16;
    var iva  = totalFinal - base;

    document.getElementById('prev-sub').textContent   = fmt(base);
    document.getElementById('prev-iva').textContent   = fmt(iva);
    document.getElementById('prev-total').textContent = fmt(totalFinal);
}

var idPendienteEliminar = null;

function editarCotizacion() {
    if (!cotActivaData) return;
    var c = cotActivaData;
    editandoId = c.id;

    itemsCot = c.items.map(function(i) {
        return { tipo: i.tipo, nombre: i.nombre, cantidad: parseInt(i.cantidad), precio_unitario: parseFloat(i.precio_unitario) };
    });

    document.getElementById('n-cliente').value = c.nombre_cliente;
    document.getElementById('n-notas').value   = c.notas || '';
    document.getElementById('modal-nueva-titulo').textContent = 'Editar — nueva versión de ' + c.folio;
    document.getElementById('btn-guardar-cot').disabled = false;
    document.getElementById('btn-guardar-cot').textContent = 'Guardar como nueva cotización';

    cerrarVer();
    renderItems();
    actualizarTotales();
    document.getElementById('modal-nueva').classList.add('open');
}

// ── Estas van FUERA de guardarCotizacion, junto a las demás variables globales ──
var ultimaCotId    = null;
var ultimaCotFolio = null;

function cerrarPost() {
    document.getElementById('modal-post').classList.remove('open');
}

function irARemision() {
    window.location.href = 'remisiones.php?cot_id=' + ultimaCotId;
}

function guardarCotizacion() {
    var cliente = document.getElementById('n-cliente').value.trim();
    if (!cliente) { resaltar('n-cliente'); return; }
    if (!itemsCot.length) { alert('Agrega al menos un producto o servicio'); return; }

    var btn = document.getElementById('btn-guardar-cot');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    var idAnterior = editandoId;
    editandoId = null;

    var fd = new FormData();
    fd.append('nombre_cliente', cliente);
    fd.append('notas',          document.getElementById('n-notas').value.trim());
    fd.append('items',          JSON.stringify(itemsCot));

    fetch('controllers/add_cotizacion.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                ultimaCotId    = res.id;
                ultimaCotFolio = res.folio;
                cerrarNueva();
                cargar();
                document.getElementById('post-folio').textContent = res.folio;
                setTimeout(function() {
                    document.getElementById('modal-post').classList.add('open');
                }, 300);
            } else {
                alert('Error: ' + res.msg);
                btn.disabled = false;
                btn.textContent = 'Guardar cotización';
            }
        });
}

function cerrarConfirmar() {
    document.getElementById('modal-confirmar').classList.remove('open');
    idPendienteEliminar = null;
}

function confirmarEliminarOriginal() {
    if (!idPendienteEliminar) return;
    var fd = new FormData();
    fd.append('id', idPendienteEliminar);
    fetch('controllers/delete_cotizacion.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(){ cerrarConfirmar(); cargar(); });
}

// ── Ver cotización ──
function verCotizacion(id) {
    console.log("Entró a verCotizacion:", id);
    fetch('controllers/get_cotizacion.php?id=' + id)
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (!res.ok) { alert('Error al cargar'); return; }
            cotActivaId   = id;
            cotActivaData = res.data;
            document.getElementById('ver-titulo').textContent = res.data.folio;
            document.getElementById('ver-body').innerHTML = renderTicket(res.data);
            document.getElementById('modal-ver').classList.add('open');
        });
}

function renderTicket(c) {
    var fecha    = new Date(c.created_at);
    var fechaStr = fecha.toLocaleDateString('es-MX', {weekday:'long', day:'2-digit', month:'long', year:'numeric'});
    var horaStr  = fecha.toLocaleTimeString('es-MX', {hour:'2-digit', minute:'2-digit'});
    var nombreCliente = c.nombre_cliente || '—';

    var filas = '';
    c.items.forEach(function(i) {
        filas += '<tr>' +
            '<td><span class="tipo-badge ' + (i.tipo === 'servicio' ? 'srv' : '') + '">' + (i.tipo === 'servicio' ? 'Servicio' : 'Producto') + '</span></td>' +
            '<td>' + esc(i.nombre) + '</td>' +
            '<td style="text-align:center">' + i.cantidad + '</td>' +
            '<td>' + fmt(i.precio_unitario) + '</td>' +
            '<td>' + fmt(i.subtotal) + '</td>' +
        '</tr>';
    });

    var base = parseFloat(c.total) / 1.16;
    var iva  = parseFloat(c.total) - base;

    var notasHtml = c.notas
        ? '<div class="ticket-notas"><strong>Notas:</strong> ' + esc(c.notas) + '</div>' : '';

    return '<div style="padding:20px;">' +
    '<div class="ticket" id="ticket-print">' +
        '<div class="ticket-header">' +
            '<div class="ticket-logo-area">' +
                '<div class="ticket-logo-ring">' +
                    '<img src="assets/logo/logovttr.png" alt="VT">' +
                '</div>' +
                '<div>' +
                    '<div class="ticket-empresa">Villa Tecnia Tampico<small>Materiales para alberca</small></div>' +
                '</div>' +
            '</div>' +
            '<div class="ticket-meta">' +
                '<div class="ticket-folio">' + esc(c.folio) + '</div>' +
                '<div class="ticket-fecha">' + fechaStr + '<br>' + horaStr + ' hrs</div>' +
            '</div>' +
        '</div>' +

        '<div class="ticket-cliente">' +
            '<div class="ticket-cliente-label">Cliente</div>' +
            '<div class="ticket-cliente-nombre">' + esc(nombreCliente) + '</div>' +
        '</div>' +

        '<table class="ticket-table">' +
            '<thead>' +
                '<tr><th>Tipo</th><th>Descripción</th><th style="text-align:center">Cant.</th><th>P. Unit.</th><th>Subtotal</th></tr>' +
            '</thead>' +
            '<tbody>' + filas + '</tbody>' +
        '</table>' +

        '<div class="ticket-totales">' +
            '<table>' +
                '<tr><td style="color:#475569;padding:5px 8px;">Subtotal (base)</td><td style="color:#0f172a;font-weight:600;text-align:right;padding:5px 8px;">' + fmt(base) + '</td></tr>' +
                '<tr><td style="color:#475569;padding:5px 8px;">IVA (16%, incluido)</td><td style="color:#0f172a;font-weight:600;text-align:right;padding:5px 8px;">' + fmt(iva) + '</td></tr>' +
                '<tr class="total-row"><td style="color:#0f172a;font-weight:700;font-size:1rem;border-top:2px solid #eab308;padding-top:10px;">Total</td><td style="color:#0f172a;font-weight:700;font-size:1rem;border-top:2px solid #eab308;padding-top:10px;text-align:right;">' + fmt(c.total) + '</td></tr>' +
            '</table>' +
        '</div>' +

        notasHtml +

        '<div class="ticket-footer">' +
            'Cotización generada por Villa Tecnia Tampico · ' + fechaStr +
        '</div>' +
    '</div>' +
    '</div>';
}

function cerrarVer() { document.getElementById('modal-ver').classList.remove('open'); }

function eliminarCotizacion() {
    if (!cotActivaId) return;
    if (!confirm('¿Eliminar esta cotización?')) return;
    var fd = new FormData();
    fd.append('id', cotActivaId);
    fetch('controllers/delete_cotizacion.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarVer(); cargar(); }
            else alert('Error: ' + res.msg);
        });
}

// ── Compartir ──
function abrirCompartir()  { document.getElementById('modal-compartir').classList.add('open'); }
function cerrarCompartir() { document.getElementById('modal-compartir').classList.remove('open'); }

function textoResumen() {
    if (!cotActivaData) return '';
    var c = cotActivaData;
    var txt = '*Cotización ' + c.folio + ' - Villa Tecnia Tampico*\n';
    txt += 'Cliente: ' + c.nombre_cliente + '\n\n';
    c.items.forEach(function(i) {
        txt += '• ' + i.nombre + ' x' + i.cantidad + ' — ' + fmt(i.subtotal) + '\n';
    });
    txt += '\n*Total (IVA incluido): ' + fmt(c.total) + '*';
    return txt;
}

function compartirWhatsapp() {
    var txt = encodeURIComponent(textoResumen());
    window.open('https://wa.me/?text=' + txt, '_blank');
}

function compartirEmail() {
    if (!cotActivaData) return;
    var c = cotActivaData;
    var asunto = encodeURIComponent('Cotización ' + c.folio + ' - Villa Tecnia Tampico');
    var cuerpo = encodeURIComponent(textoResumen().replace(/\*/g,''));
    window.open('mailto:?subject=' + asunto + '&body=' + cuerpo, '_blank');
}

function descargarPDF() {
    var ticket = document.getElementById('ticket-print');
    if (!ticket) return;

    var estilos = [
    'body { font-family: \'Poppins\', sans-serif; margin: 0; padding: 20px; font-weight: 400; }',
    '.ticket { max-width: 680px; margin: 0 auto; padding: 36px 40px; }',
    '.ticket-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; padding-bottom:20px; border-bottom:2px solid #eab308; }',
    '.ticket-logo-ring { width:48px; height:48px; border-radius:50%; border:2px solid #eab308; overflow:hidden; background:#0f172a; display:flex; align-items:center; justify-content:center; }',
    '.ticket-logo-ring img { width:75%; height:75%; object-fit:contain; }',
    '.ticket-logo-area { display:flex; align-items:center; gap:12px; }',
    '.ticket-empresa { font-size:1rem; font-weight:500; color:#0f172a; line-height:1.2; }',
    '.ticket-empresa small { font-size:0.65rem; color:#64748b; font-weight:400; display:block; }',
    '.ticket-folio { font-size:1rem; font-weight:500; color:#eab308; }',
    '.ticket-fecha { font-size:0.72rem; color:#64748b; margin-top:4px; font-weight:400; }',
    '.ticket-meta { text-align:right; }',
    '.ticket-cliente { margin-bottom:24px; padding:12px 16px; background:#f8fafc; border-radius:8px; border-left:3px solid #eab308; }',
    '.ticket-cliente-label { font-size:0.65rem; font-weight:500; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; }',
    '.ticket-cliente-nombre { font-size:0.95rem; font-weight:500; color:#0f172a; margin-top:2px; }',
    'table { width:100%; border-collapse:collapse; font-size:0.82rem; margin-bottom:20px; font-weight:400; }',
    'thead th { text-align:left; padding:8px 10px; background:#0f172a; color:#f1f5f9; font-size:0.68rem; font-weight:500; text-transform:uppercase; }',
    'thead th:last-child { text-align:right; }',
    'tbody td { padding:10px; border-bottom:1px solid #e2e8f0; color:#0f172a; font-weight:400; }',
    'tbody td:last-child { text-align:right; font-weight:500; }',
    '.tipo-badge { font-size:0.6rem; padding:2px 6px; border-radius:999px; font-weight:500; background:#dbeafe; color:#1d4ed8; }',
    '.tipo-badge.srv { background:#f3e8ff; color:#7c3aed; }',
    '.ticket-totales { margin-left:auto; width:240px; }',
    '.ticket-totales td { padding:5px 8px; color:#475569; font-weight:400; }',
    '.ticket-totales td:last-child { text-align:right; font-weight:500; color:#0f172a; }',
    '.total-row td { font-size:1rem; font-weight:600; color:#0f172a; border-top:2px solid #eab308; padding-top:10px; }',
    '.ticket-notas { margin-top:20px; padding:12px 16px; background:#f8fafc; border-radius:8px; font-size:0.78rem; color:#475569; font-weight:400; }',
    '.ticket-footer { margin-top:28px; padding-top:16px; border-top:1px solid #e2e8f0; text-align:center; font-size:0.7rem; color:#94a3b8; font-weight:400; }'
    ].join('\n');

    var win = window.open('', '_blank');
    win.document.write('<!DOCTYPE html><html><head>' +
        '<meta charset="UTF-8">' +
        '<title>' + (cotActivaData ? cotActivaData.folio : 'Cotizacion') + '</title>' +
        '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">' +
        '<style>' + estilos + '</style>' +
        '</head><body>' + ticket.outerHTML + '</body></html>');
    win.document.close();
    setTimeout(function(){ win.print(); }, 800);
}

// ── Utilidades ──
function fmt(n) { return '$' + parseFloat(n).toFixed(2); }

function resaltar(id) {
    var el = document.getElementById(id);
    el.style.borderColor = '#ef4444';
    el.focus();
    el.addEventListener('input', function(){ el.style.borderColor = ''; }, { once: true });
}

function esc(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}

// ── Nuevo artículo ──
function abrirNuevoArticulo() {
    document.getElementById('na-nombre').value = '';
    document.getElementById('na-precio').value = '';
    document.getElementById('na-preview').style.display = 'none';
    document.getElementById('modal-articulo').classList.add('open');
    setTimeout(function(){ document.getElementById('na-nombre').focus(); }, 100);
}

function cerrarNuevoArticulo() {
    document.getElementById('modal-articulo').classList.remove('open');
}

function previewArticulo() {
    var val = parseFloat(document.getElementById('na-precio').value);
    var prev = document.getElementById('na-preview');
    if (!isNaN(val) && val > 0) {
        var base = val / 1.16;
        document.getElementById('na-base').textContent = fmt(base);
        document.getElementById('na-iva').textContent   = fmt(val - base);
        prev.style.display = 'block';
    } else {
        prev.style.display = 'none';
    }
}

function usarArticuloTemporal() {
    var nombre = document.getElementById('na-nombre').value.trim();
    var precioFinal = parseFloat(document.getElementById('na-precio').value) || 0;
    if (!nombre) { resaltar('na-nombre'); return; }
    itemsCot.push({ tipo: 'producto', nombre: nombre, cantidad: 1, precio_unitario: precioFinal });
    renderItems();
    actualizarTotales();
    cerrarNuevoArticulo();
}

function guardarNuevoArticulo() {
    var nombre = document.getElementById('na-nombre').value.trim();
    var precioFinal = parseFloat(document.getElementById('na-precio').value) || 0;
    if (!nombre) { resaltar('na-nombre'); return; }
    if (!precioFinal) { resaltar('na-precio'); return; }

    var fd = new FormData();
    fd.append('nombre', nombre);
    fd.append('precio_final', precioFinal);

    fetch('controllers/add_inventario.php', { method: 'POST', body: fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                fetch('controllers/get_inventario.php').then(function(r){ return r.json(); }).then(function(prods){
                    productosInv = prods;
                });
                itemsCot.push({ tipo: 'producto', nombre: nombre, cantidad: 1, precio_unitario: precioFinal });
                renderItems();
                actualizarTotales();
                cerrarNuevoArticulo();
            } else {
                alert('Error: ' + res.msg);
            }
        });
}

function irARemisionDesdeCot() {
    if (!cotActivaId) return;
    window.location.href = 'remisiones.php?cot_id=' + cotActivaId;
}

(function () {
    const params = new URLSearchParams(window.location.search);
    console.log("URL:", window.location.href);
    console.log("OPEN:", params.get("open"));

    const desde = params.get('desde');
    const openId = parseInt(params.get('open'),10);

    cargar().then(function(){
        if (desde === 'remision') {
            abrirNueva();
        }

        if (!isNaN(openId)) {
            // AGREGA AQUÍ:
            console.log('llamando verCotizacion con id:', openId);
            console.log('cotizaciones disponibles:', cotizaciones.length);
            console.log('encontrada:', cotizaciones.find(function(c){ return c.id == openId; }));
            verCotizacion(openId);
        }
    });
})();

function cargar() {
    return Promise.all([
        fetch('controllers/get_cotizaciones.php').then(r => r.json()),
        fetch('controllers/get_inventario.php').then(r => r.json()),
        fetch('controllers/get_clientes.php').then(r => r.json())
    ]).then(function(results) {

        cotizaciones  = results[0];
        productosInv  = results[1];
        clientesLista = results[2];

        renderLista(cotizaciones);
        renderStats(cotizaciones);

        if (window._openCotId) {
            setTimeout(function() {
                verCotizacion(window._openCotId);
                window._openCotId = null;
            }, 200);
        }

    }).catch(function(err){
        console.error(err);
    });

    if (window._openCotId) {
    console.log('intentando abrir:', window._openCotId, 'cotizaciones cargadas:', cotizaciones.length);
    setTimeout(function() {
        verCotizacion(window._openCotId);
        window._openCotId = null;
    }, 200);
}
}
</script>
<!-- ══ MODAL CONFIRMAR ELIMINAR ORIGINAL ══ -->
<div class="modal-overlay" id="modal-confirmar" onclick="if(event.target===this)cerrarConfirmar()">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title">Cotización guardada</span>
            <button class="modal-close" onclick="cerrarConfirmar()">&times;</button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.88rem;color:var(--text);margin-bottom:8px;">
                La nueva versión fue guardada correctamente.
            </p>
            <p style="font-size:0.82rem;color:var(--muted);">
                ¿Deseas eliminar la cotización original <strong id="confirmar-folio"></strong>?
            </p>
        </div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-sm btn-outline" onclick="cerrarConfirmar()">Conservar original</button>
            <button class="btn-sm btn-primary" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;" onclick="confirmarEliminarOriginal()">Sí, eliminar original</button>
        </div>
    </div>
</div>
</body>
</html>