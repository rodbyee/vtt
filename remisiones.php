<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user'])) { header('Location: login.php'); exit; }
$pagina_actual = 'remisiones';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Notas de Remisión</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<link rel="stylesheet" href="css/dashboard.css">
<style>
/* ── Lista remisiones ── */
.rem-list { display:flex; flex-direction:column; gap:8px; }

.rem-item {
    background:var(--surface); border:1px solid var(--border);
    border-radius:var(--radius); display:flex; align-items:center;
    gap:16px; padding:14px 18px; transition:box-shadow 0.15s; cursor:pointer;
    position:relative; overflow:hidden;
}
.rem-item::before {
    content:''; position:absolute; left:0; top:0; bottom:0;
    width:4px; border-radius:4px 0 0 4px;
}
.rem-item.contado::before  { background:#22c55e; }
.rem-item.credito::before  { background:#eab308; }

.rem-item:hover { box-shadow:var(--shadow); background:var(--bg); }

.rem-folio { font-size:0.72rem; font-weight:700; color:var(--accent); letter-spacing:0.06em; white-space:nowrap; min-width:80px; }
.rem-info  { flex:1; min-width:0; }
.rem-cliente { font-size:0.88rem; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.rem-sub    { font-size:0.72rem; color:var(--muted); margin-top:2px; }
.rem-right  { display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0; }
.rem-total  { font-size:0.95rem; font-weight:700; color:var(--text); white-space:nowrap; }

.mod-badge { font-size:0.62rem; padding:2px 8px; border-radius:999px; font-weight:600; }
.mod-contado { background:rgba(34,197,94,0.12); color:#4ade80; }
.mod-credito { background:rgba(234,179,8,0.12); color:#fbbf24; }
[data-theme=""] .mod-contado { background:#dcfce7; color:#15803d; }
[data-theme=""] .mod-credito { background:#fef9c3; color:#854d0e; }

.prog-bar  { height:5px; border-radius:999px; background:var(--border); overflow:hidden; margin-top:4px; width:120px; }
.prog-fill { height:100%; border-radius:999px; background:linear-gradient(90deg,#22c55e,#16a34a); }

/* ── Pendiente destacado en modal ── */
.pendiente-destacado {
    background:rgba(239,68,68,0.08);
    border:2px solid rgba(239,68,68,0.3);
    border-radius:var(--radius-sm);
    padding:14px 18px; text-align:center; margin-bottom:12px;
}
.pendiente-destacado .pd-label {
    font-size:0.68rem; font-weight:700; text-transform:uppercase;
    letter-spacing:0.1em; color:#ef4444; margin-bottom:4px;
}
.pendiente-destacado .pd-monto {
    font-size:1.9rem; font-weight:800; color:#ef4444; line-height:1;
}

/* ── Selector cotización ── */
.cot-sel-item {
    display:flex; align-items:center; gap:12px;
    padding:10px 14px; border:1px solid var(--border);
    border-radius:var(--radius-sm); cursor:pointer;
    background:var(--surface); transition:background 0.1s;
}
.cot-sel-item:hover { background:var(--bg); border-color:var(--accent); }
.cot-sel-folio  { font-size:0.72rem; font-weight:700; color:var(--accent); min-width:72px; }
.cot-sel-info   { flex:1; min-width:0; }
.cot-sel-nombre { font-size:0.82rem; font-weight:600; color:var(--text); }
.cot-sel-fecha  { font-size:0.68rem; color:var(--muted); }
.cot-sel-total  { font-size:0.85rem; font-weight:700; color:var(--text); }

/* ── Abonos ── */
.abono-row {
    display:flex; align-items:center; gap:12px;
    padding:10px 14px; border:1px solid var(--border);
    border-radius:var(--radius-sm); background:var(--bg);
}
.abono-num   { font-size:0.72rem; font-weight:700; color:var(--muted); min-width:24px; }
.abono-info  { flex:1; font-size:0.82rem; color:var(--text); }
.abono-monto { font-size:0.88rem; font-weight:700; color:var(--text); }

/* ── Share ── */
.share-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(110px,1fr)); gap:10px; margin-top:14px; }
.share-btn  {
    display:flex; flex-direction:column; align-items:center; gap:8px;
    padding:14px 10px; border:1px solid var(--border); border-radius:var(--radius);
    cursor:pointer; background:var(--surface); color:var(--text);
    font-family:inherit; font-size:0.78rem; font-weight:600;
    transition:background 0.15s, border-color 0.15s; text-decoration:none;
}
.share-btn:hover { background:var(--bg); border-color:var(--accent); }
.share-btn svg { width:26px; height:26px; }
.share-btn.whatsapp svg { color:#25d366; }
.share-btn.email svg    { color:#4b8ef1; }
.share-btn.download svg { color:var(--accent); }

.toggle-modalidad { display:flex; gap:6px; margin-bottom:10px; }
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
            <span class="page-title">Notas de remisión</span>
        </div>
        <div class="topbar-right">
            <button class="btn-sm btn-primary" onclick="abrirNueva()">+ Nueva remisión</button>
        </div>
    </header>

    <div class="content">
        <div class="stats-grid stats-4" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="stat-label">Total remisiones</div>
                <div class="stat-value" id="stat-total">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="stat-label">De contado</div>
                <div class="stat-value" id="stat-contado">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="stat-label">A crédito activos</div>
                <div class="stat-value" id="stat-credito">—</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="stat-label">Por cobrar</div>
                <div class="stat-value" id="stat-pendiente">—</div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <span class="table-title">Historial de remisiones</span>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <select id="filtro-mod" onchange="filtrar()"
                        style="padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);outline:none;">
                        <option value="">Todas</option>
                        <option value="completo">Contado</option>
                        <option value="fijo">Crédito fijo</option>
                        <option value="variable">Crédito variable</option>
                    </select>
                    <input type="text" id="buscador" placeholder="Buscar cliente o folio..."
                        oninput="filtrar()"
                        style="padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:inherit;font-size:0.78rem;background:var(--bg);color:var(--text);outline:none;width:220px;max-width:100%;">
                </div>
            </div>
            <div style="padding:16px;" id="rem-contenedor">
                <p style="text-align:center;color:var(--muted);padding:40px 0;font-size:0.82rem;">Cargando...</p>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL NUEVA REMISIÓN ══ -->
<div class="modal-overlay" id="modal-nueva" onclick="clickOverlay(event)">
    <div class="modal" style="max-width:540px;">
        <div class="modal-header">
            <span class="modal-title">Nueva nota de remisión</span>
            <button class="modal-close" onclick="cerrarNueva()">&times;</button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom:16px;">
                <button class="btn-sm btn-outline" style="width:100%;" onclick="irACrearCotizacion()">
                    Crear cotizacion
                </button>
            </div>
            <!-- Paso 1: Cotización -->
            <div class="builder-section">
                <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted);margin-bottom:8px;">Cotización base *</div>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="n-cot-display" placeholder="Selecciona una cotización..." readonly
                        style="flex:1;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.82rem;cursor:pointer;"
                        onclick="abrirSelCot()">
                    <button class="btn-sm btn-outline" onclick="abrirSelCot()">Buscar</button>
                    <button class="btn-sm btn-outline" onclick="limpiarCot()">✕</button>
                </div>
                <div id="cot-preview" style="display:none;margin-top:10px;"></div>
            </div>

            <!-- Paso 2: Tipo de pago -->
            <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted);margin-bottom:8px;">Tipo de pago</div>
            <div class="toggle-modalidad">
                <button class="btn-sm btn-primary" id="mod-btn-completo" onclick="setMod('completo')">Contado</button>
                <button class="btn-sm btn-outline" id="mod-btn-fijo"     onclick="setMod('fijo')">Crédito fijo</button>
                <button class="btn-sm btn-outline" id="mod-btn-variable" onclick="setMod('variable')">Crédito variable</button>
            </div>
            <p id="mod-desc" style="font-size:0.72rem;color:var(--muted);margin-bottom:12px;">Pago completo al momento de la entrega.</p>

            <!-- Método de pago -->
            <div class="form-row">
                <div class="form-group">
                    <label>Método de pago *</label>
                    <select id="n-metodo" style="padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.85rem;outline:none;width:100%;">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Referencia (opcional)</label>
                    <input type="text" id="n-referencia" placeholder="Folio, últimos 4 dígitos...">
                </div>
            </div>

            <!-- Crédito fijo -->
            <div id="sec-fijo" style="display:none;">
                <div class="form-group">
                    <label>Número de mensualidades *</label>
                    <input type="number" id="n-mensualidades" placeholder="Ej. 3" min="2" max="60" step="1" oninput="calcularMens()">
                </div>
                <div id="mens-preview" style="display:none;margin-top:8px;"></div>
            </div>

            <!-- Crédito variable -->
            <div id="sec-variable" style="display:none;padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.78rem;color:var(--muted);margin-bottom:4px;">
                El cliente realizará abonos en montos variables. Se registrará el total de la cotización como saldo pendiente.
            </div>

            <div class="form-group">
                <label>Notas (opcional)</label>
                <input type="text" id="n-notas" placeholder="Ej. Entrega inmediata, pago a convenir...">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-sm btn-outline" onclick="cerrarNueva()">Cancelar</button>
            <button class="btn-sm btn-primary" id="btn-guardar-rem" onclick="guardarRemision()">Generar remisión</button>
        </div>
    </div>
</div>

<!-- ══ MODAL SELECTOR COTIZACIÓN ══ -->
<div class="modal-overlay" id="modal-sel-cot" onclick="if(event.target===this)cerrarSelCot()">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <span class="modal-title">Seleccionar cotización</span>
            <button class="modal-close" onclick="cerrarSelCot()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="text" id="cot-buscar" placeholder="Buscar cliente o folio..." oninput="filtrarCots()"
                style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.82rem;outline:none;margin-bottom:10px;">
            <div id="cot-lista" style="max-height:340px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;"></div>
        </div>
    </div>
</div>

<!-- ══ MODAL VER REMISIÓN ══ -->
<div class="modal-overlay" id="modal-ver" onclick="if(event.target===this)cerrarVer()">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <span class="modal-title" id="ver-titulo">Remisión</span>
            <button class="modal-close" onclick="cerrarVer()">&times;</button>
        </div>
        <div class="modal-body" id="ver-body"></div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-sm btn-outline" style="color:#ef4444;border-color:#fecaca;" onclick="eliminarRemision()">Eliminar</button>
            <div style="display:flex;gap:8px;">
                <button class="btn-sm btn-outline" onclick="cerrarVer()">Cerrar</button>
                <button class="btn-sm btn-outline" onclick="abrirEditar()">Editar</button>
                <button class="btn-sm btn-outline" onclick="abrirCompartir()">Compartir</button>
                <button class="btn-sm btn-primary" id="btn-abono" style="display:none;" onclick="abrirAbono()">+ Abono</button>
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
            <div id="abono-info" style="padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.82rem;margin-bottom:10px;"></div>
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
            <button class="btn-sm btn-primary" id="btn-guardar-abono" onclick="guardarAbono()">Guardar abono</button>
        </div>
    </div>
</div>

<!-- ══ MODAL COMPARTIR ══ -->
<div class="modal-overlay" id="modal-compartir" onclick="if(event.target===this)cerrarCompartir()">
    <div class="modal" style="max-width:380px;">
        <div class="modal-header">
            <span class="modal-title">Compartir remisión</span>
            <button class="modal-close" onclick="cerrarCompartir()">&times;</button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.82rem;color:var(--muted);">Elige cómo compartir esta nota de remisión:</p>
            <div class="share-grid">
                <button class="share-btn whatsapp" onclick="compartirWhatsapp()">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </button>
                <button class="share-btn email" onclick="compartirEmail()">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Correo
                </button>
                <button class="share-btn download" onclick="imprimirRemision()">
                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 15V3m0 12l-4-4m4 4l4-4M2 17l.621 2.485A2 2 0 004.561 21h14.878a2 2 0 001.94-1.515L22 17"/></svg>
                    PDF / Imprimir
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ══ MODAL EDITAR REMISIÓN ══ -->
<div class="modal-overlay" id="modal-editar" onclick="if(event.target===this)cerrarEditar()">
    <div class="modal" style="max-width:580px;">
        <div class="modal-header">
            <span class="modal-title" id="editar-titulo">Editar remisión</span>
            <button class="modal-close" onclick="cerrarEditar()">&times;</button>
        </div>
        <div class="modal-body">

            <!-- Pago -->
            <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted);margin-bottom:8px;">Tipo de pago</div>
            <div class="toggle-modalidad">
                <button class="btn-sm btn-primary" id="e-mod-btn-completo" onclick="setModEdit('completo')">Contado</button>
                <button class="btn-sm btn-outline" id="e-mod-btn-fijo"     onclick="setModEdit('fijo')">Crédito fijo</button>
                <button class="btn-sm btn-outline" id="e-mod-btn-variable" onclick="setModEdit('variable')">Crédito variable</button>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Método *</label>
                    <select id="e-metodo" style="padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.85rem;outline:none;width:100%;">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Referencia</label>
                    <input type="text" id="e-referencia" placeholder="Opcional">
                </div>
            </div>
            <div id="e-sec-fijo" style="display:none;">
                <div class="form-group">
                    <label>Mensualidades</label>
                    <input type="number" id="e-mensualidades" placeholder="Ej. 3" min="2" max="60">
                </div>
            </div>
            <div class="form-group">
                <label>Notas</label>
                <input type="text" id="e-notas" placeholder="Opcional">
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:16px 0;">

            <!-- Artículos -->
            <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted);margin-bottom:8px;">Artículos de la cotización</div>
            <div style="display:grid;grid-template-columns:1fr 60px 100px 32px;gap:8px;margin-bottom:6px;">
                <span style="font-size:0.68rem;color:var(--muted);font-weight:600;text-transform:uppercase;">Descripción</span>
                <span style="font-size:0.68rem;color:var(--muted);font-weight:600;text-transform:uppercase;">Cant.</span>
                <span style="font-size:0.68rem;color:var(--muted);font-weight:600;text-transform:uppercase;">Precio c/IVA</span>
                <span></span>
            </div>
            <div id="e-items-list" style="display:flex;flex-direction:column;gap:6px;margin-bottom:10px;"></div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button class="btn-sm btn-outline" onclick="eAbrirInventario()">+ Del inventario</button>
                <button class="btn-sm btn-outline" onclick="eAgregarItem('servicio')">+ Servicio</button>
                <button class="btn-sm btn-outline" onclick="eAbrirNuevoArticulo()">+ Nuevo artículo</button>
            </div>

            <div id="e-total-preview" style="margin-top:12px;padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);display:flex;justify-content:space-between;font-size:0.88rem;">
                <span style="color:var(--muted);">Nuevo total</span>
                <strong id="e-total-val" style="color:var(--text);">$0.00</strong>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-sm btn-outline" onclick="duplicarRemision()" title="Crear copia de esta remisión">Duplicar</button>
            <div style="display:flex;gap:8px;">
                <button class="btn-sm btn-outline" onclick="cerrarEditar()">Cancelar</button>
                <button class="btn-sm btn-primary" id="btn-guardar-edit" onclick="guardarEdicion()">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL CONFIRMAR COTIZACIÓN ══ -->
<div class="modal-overlay" id="modal-confirm-cot" onclick="if(event.target===this)cerrarConfirmCot()">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title">Artículos modificados</span>
            <button class="modal-close" onclick="cerrarConfirmCot()">&times;</button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.88rem;color:var(--text);margin-bottom:8px;">Los artículos cambiaron. ¿Qué deseas hacer con la cotización base?</p>
            <p style="font-size:0.78rem;color:var(--muted);">La remisión se actualizará en ambos casos.</p>
        </div>
        <div class="modal-footer" style="flex-direction:column;gap:8px;">
            <button class="btn-sm btn-primary" style="width:100%;" onclick="confirmarEdicion('actualizar')">Actualizar cotización original</button>
            <button class="btn-sm btn-outline" style="width:100%;" onclick="confirmarEdicion('duplicar')">Duplicar cotización y usar la nueva</button>
            <button class="btn-sm btn-outline" style="width:100%;color:var(--muted);" onclick="cerrarConfirmCot()">Cancelar</button>
        </div>
    </div>
</div>

<!-- ══ MODAL INVENTARIO (editar remisión) ══ -->
<div class="modal-overlay" id="e-modal-inv" onclick="if(event.target===this)eCerrarInventario()">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <span class="modal-title">Seleccionar producto</span>
            <button class="modal-close" onclick="eCerrarInventario()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="text" id="e-inv-buscar" placeholder="Buscar nombre o SKU..." oninput="eFiltrarInv()"
                style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
                       background:var(--bg);color:var(--text);font-family:inherit;font-size:0.82rem;outline:none;margin-bottom:10px;">
            <div id="e-inv-lista" style="max-height:280px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;"></div>
        </div>
    </div>
</div>

<!-- ══ MODAL NUEVO ARTÍCULO (editar remisión) ══ -->
<div class="modal-overlay" id="e-modal-art" onclick="if(event.target===this)eCerrarNuevoArt()">
    <div class="modal" style="max-width:360px;">
        <div class="modal-header">
            <span class="modal-title">Nuevo artículo</span>
            <button class="modal-close" onclick="eCerrarNuevoArt()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" id="e-art-nombre" placeholder="Nombre del artículo">
            </div>
            <div class="form-group">
                <label>Precio final (con IVA) *</label>
                <input type="number" id="e-art-precio" placeholder="0.00" min="0" step="0.01" oninput="ePreviewArt()">
            </div>
            <div id="e-art-preview" style="display:none;font-size:0.78rem;color:var(--muted);padding:8px 12px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);">
                Base: <strong id="e-art-base" style="color:var(--text);">$0.00</strong> + IVA: <strong id="e-art-iva" style="color:var(--text);">$0.00</strong>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-sm btn-outline" onclick="eUsarArtTemporal()">No guardar</button>
            <button class="btn-sm btn-primary" onclick="eGuardarNuevoArt()">Guardar en inventario</button>
        </div>
    </div>
</div>
<script>
var remisiones    = [];
var cotizaciones  = [];
var cotSel        = null;
var cotSelData    = null;
var modActual     = 'completo';
var remActivaId   = null;
var remActiva     = null;

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
        fetch('controllers/get_cotizaciones.php').then(function(r){ return r.json(); })
    ]).then(function(res) {
        // Filtrar solo los de tipo remisión
        remisiones   = res[0].filter(function(p){ return p.tipo === 'remision'; });
        cotizaciones = res[1];
        renderLista(remisiones);
        renderStats(remisiones);
    }).catch(function(e){ console.error(e); });
}

function renderStats(data) {
    var contado = data.filter(function(r){ return r.modalidad === 'completo'; }).length;
    var credito = data.filter(function(r){
        return (r.modalidad === 'fijo' || r.modalidad === 'variable') &&
               parseFloat(r.monto_cobrado||0) < parseFloat(r.monto);
    }).length;
    var pendiente = data.reduce(function(a,r){
        return a + Math.max(0, parseFloat(r.monto) - parseFloat(r.monto_cobrado||0));
    }, 0);
    document.getElementById('stat-total').textContent    = data.length;
    document.getElementById('stat-contado').textContent  = contado;
    document.getElementById('stat-credito').textContent  = credito;
    document.getElementById('stat-pendiente').textContent = fmt(pendiente);
}

function renderLista(lista) {
    var cont = document.getElementById('rem-contenedor');
    if (!lista.length) {
        cont.innerHTML = '<p style="text-align:center;color:var(--muted);padding:40px 0;font-size:0.82rem;">Sin notas de remisión registradas</p>';
        return;
    }
    var html = '<div class="rem-list">';
    lista.forEach(function(r) {
        var esCred  = r.modalidad !== 'completo';
        var cobrado = parseFloat(r.monto_cobrado||0);
        var total   = parseFloat(r.monto);
        var pct     = total > 0 ? Math.min(100, Math.round(cobrado/total*100)) : 100;
        var fecha = new Date((r.created_at || '').replace(' ', 'T'));
        var fStr    = fecha.toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'});
        var hStr    = fecha.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'});
        var modLabel = r.modalidad === 'completo' ? 'Contado' : r.modalidad === 'fijo' ? 'Crédito fijo' : 'Crédito variable';
        var claseItem = esCred ? 'credito' : 'contado';

        html += '<div class="rem-item ' + claseItem + '" onclick="verRemision(' + r.id + ')">' +
            '<div class="rem-folio">' + esc(r.folio_doc || 'REM') + '</div>' +
            '<div class="rem-info">' +
                '<div class="rem-cliente">' + esc(r.nombre_cliente||'—') + '</div>' +
                '<div class="rem-sub">' + fStr + ' · ' + hStr + '</div>' +
                (esCred ? '<div class="prog-bar"><div class="prog-fill" style="width:' + pct + '%"></div></div>' : '') +
            '</div>' +
            '<div class="rem-right">' +
                '<div class="rem-total">' + fmt(esCred ? cobrado : total) +
                    (esCred ? '<span style="font-size:0.68rem;color:var(--muted);font-weight:400;"> / ' + fmt(total) + '</span>' : '') +
                '</div>' +
                '<span class="mod-badge mod-' + (esCred?'credito':'contado') + '">' + modLabel + '</span>' +
            '</div>' +
        '</div>';
    });
    html += '</div>';
    cont.innerHTML = html;
}

function filtrar() {
    var q   = document.getElementById('buscador').value.toLowerCase();
    var mod = document.getElementById('filtro-mod').value;
    renderLista(remisiones.filter(function(r) {
        var mq = !mod || r.modalidad === mod;
        var qq = !q || (r.nombre_cliente||'').toLowerCase().includes(q) || (r.folio_doc||'').toLowerCase().includes(q);
        return mq && qq;
    }));
}

// ── Nueva remisión ──
function abrirNueva() {
    cotSel = null; cotSelData = null; modActual = 'completo';
    document.getElementById('n-cot-display').value = '';
    document.getElementById('cot-preview').style.display = 'none';
    document.getElementById('n-referencia').value = '';
    document.getElementById('n-notas').value = '';
    document.getElementById('n-mensualidades').value = '';
    document.getElementById('mens-preview').style.display = 'none';
    document.getElementById('btn-guardar-rem').disabled = false;
    document.getElementById('btn-guardar-rem').textContent = 'Generar remisión';
    setMod('completo');
    document.getElementById('modal-nueva').classList.add('open');
}

function cerrarNueva() { document.getElementById('modal-nueva').classList.remove('open'); }
function clickOverlay(e) { if (e.target === document.getElementById('modal-nueva')) cerrarNueva(); }

function setMod(mod) {
    modActual = mod;
    var descs = {
        completo: 'Pago completo al momento de la entrega.',
        fijo:     'Crédito dividido en mensualidades iguales.',
        variable: 'El cliente realizará abonos en montos variables.'
    };
    document.getElementById('mod-desc').textContent = descs[mod];
    ['completo','fijo','variable'].forEach(function(m) {
        document.getElementById('mod-btn-' + m).className = m === mod ? 'btn-sm btn-primary' : 'btn-sm btn-outline';
    });
    document.getElementById('sec-fijo').style.display     = mod === 'fijo'     ? 'block' : 'none';
    document.getElementById('sec-variable').style.display = mod === 'variable' ? 'block' : 'none';
}

// ── Selector cotización ──
function abrirSelCot() {
    document.getElementById('cot-buscar').value = '';
    filtrarCots();
    document.getElementById('modal-sel-cot').classList.add('open');
}
function cerrarSelCot() { document.getElementById('modal-sel-cot').classList.remove('open'); }

function filtrarCots() {
    var q = (document.getElementById('cot-buscar').value||'').toLowerCase();
    var lista = cotizaciones.filter(function(c) {
        return c.nombre_cliente.toLowerCase().includes(q) || c.folio.toLowerCase().includes(q);
    });
    var el = document.getElementById('cot-lista');
    if (!lista.length) { el.innerHTML = '<p style="text-align:center;color:var(--muted);font-size:0.78rem;padding:20px 0;">Sin resultados</p>'; return; }
    var html = '';
    lista.forEach(function(c) {
        var fecha = new Date(c.created_at).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'});
        html += '<div class="cot-sel-item" onclick="selCot(' + c.id + ')">' +
            '<div class="cot-sel-folio">' + esc(c.folio) + '</div>' +
            '<div class="cot-sel-info">' +
                '<div class="cot-sel-nombre">' + esc(c.nombre_cliente) + '</div>' +
                '<div class="cot-sel-fecha">' + fecha + '</div>' +
            '</div>' +
            '<div class="cot-sel-total">' + fmt(c.total) + '</div>' +
        '</div>';
    });
    el.innerHTML = html;
}

function selCot(id) {
    // Cargar detalle de cotización para items
    fetch('controllers/get_cotizacion.php?id=' + id)
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (!res.ok) { alert('Error al cargar cotización'); return; }
            cotSel     = id;
            cotSelData = res.data;

            document.getElementById('n-cot-display').value = cotSelData.folio + ' — ' + cotSelData.nombre_cliente;

            // Preview de la cotización seleccionada
            var items = cotSelData.items || [];
            var filas = items.map(function(i) {
                return '<tr style="font-size:0.78rem;">' +
                    '<td style="padding:5px 8px;border-bottom:1px solid #e2e8f0;color:var(--text);">' + esc(i.nombre) + '</td>' +
                    '<td style="padding:5px 8px;border-bottom:1px solid #e2e8f0;text-align:center;color:var(--text);">' + i.cantidad + '</td>' +
                    '<td style="padding:5px 8px;border-bottom:1px solid #e2e8f0;text-align:right;color:var(--text);">' + fmt(i.subtotal) + '</td>' +
                '</tr>';
            }).join('');

            var prev =
    '<div style="padding:12px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);margin-top:8px;">' +
        '<div style="display:flex;justify-content:space-between;margin-bottom:8px;">' +
            '<span style="font-size:0.72rem;font-weight:600;color:var(--muted);">COTIZACIÓN SELECCIONADA</span>' +
            '<span style="font-size:0.72rem;color:var(--accent);font-weight:700;">' + esc(cotSelData.folio) + '</span>' +
        '</div>' +
        '<div style="overflow-x:auto;">' +
            '<table style="width:100%;border-collapse:collapse;">' +
                '<thead><tr>' +
                    '<th style="font-size:0.65rem;text-align:left;padding:5px 8px;color:var(--muted);font-weight:600;text-transform:uppercase;">Artículo</th>' +
                    '<th style="font-size:0.65rem;text-align:center;padding:5px 8px;color:var(--muted);font-weight:600;text-transform:uppercase;">Cant.</th>' +
                    '<th style="font-size:0.65rem;text-align:right;padding:5px 8px;color:var(--muted);font-weight:600;text-transform:uppercase;">Subtotal</th>' +
                '</tr></thead>' +
                '<tbody>' + filas + '</tbody>' +
            '</table>' +
        '</div>' +
        '<div style="display:flex;justify-content:space-between;margin-top:8px;padding-top:8px;border-top:1px solid var(--border);">' +
            '<span style="font-size:0.82rem;color:var(--muted);">Total</span>' +
            '<strong style="font-size:0.92rem;color:var(--text);">' + fmt(cotSelData.total) + '</strong>' +
        '</div>' +
    '</div>';

            document.getElementById('cot-preview').innerHTML = prev;
            document.getElementById('cot-preview').style.display = 'block';
            cerrarSelCot();
        });
}

function limpiarCot() {
    cotSel = null; cotSelData = null;
    document.getElementById('n-cot-display').value = '';
    document.getElementById('cot-preview').style.display = 'none';
}

function calcularMens() {
    if (!cotSelData) return;
    var total = parseFloat(cotSelData.total) || 0;
    var meses = parseInt(document.getElementById('n-mensualidades').value) || 0;
    var prev  = document.getElementById('mens-preview');
    if (!meses || meses < 2) { prev.style.display = 'none'; return; }
    var mm     = Math.floor((total/meses)*100)/100;
    var ultimo = parseFloat((total - mm*(meses-1)).toFixed(2));
    var html   = '<div style="display:flex;flex-direction:column;gap:4px;">';
    for (var i = 1; i <= meses; i++) {
        var m = i === meses ? ultimo : mm;
        html += '<div style="display:flex;justify-content:space-between;align-items:center;padding:7px 12px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.82rem;">' +
            '<span style="color:var(--muted);">Mes ' + i + '</span>' +
            '<span style="font-weight:600;">' + fmt(m) + '</span>' +
            '<span style="font-size:0.68rem;color:var(--muted);">Pendiente</span>' +
        '</div>';
    }
    html += '</div>';
    document.getElementById('mens-preview').innerHTML = html;
    prev.style.display = 'block';
}

function guardarRemision() {
    if (!cotSel || !cotSelData) { alert('Selecciona una cotización primero'); return; }

    var btn = document.getElementById('btn-guardar-rem');
    btn.disabled = true; btn.textContent = 'Guardando...';

    var total  = parseFloat(cotSelData.total);
    var menses = modActual === 'fijo' ? parseInt(document.getElementById('n-mensualidades').value)||0 : 0;
    if (modActual === 'fijo' && menses < 2) {
        alert('Ingresa al menos 2 mensualidades');
        btn.disabled = false; btn.textContent = 'Generar remisión';
        return;
    }
    var mm = menses > 0 ? Math.round((total/menses)*100)/100 : 0;

    var fd = new FormData();
    fd.append('tipo',              'remision');
    fd.append('modalidad',         modActual);
    fd.append('nombre_cliente',    cotSelData.nombre_cliente);
    fd.append('id_cotizacion',     cotSel);
    fd.append('folio_doc',         cotSelData.folio);
    fd.append('monto',             total);
    fd.append('metodo',            document.getElementById('n-metodo').value);
    fd.append('referencia',        document.getElementById('n-referencia').value.trim());
    fd.append('notas',             document.getElementById('n-notas').value.trim());
    fd.append('total_diferido',    modActual !== 'completo' ? total : 0);
    fd.append('mensualidades',     menses);
    fd.append('monto_mensualidad', mm);
    // Si es contado, monto_cobrado = total desde el inicio
    fd.append('monto_cobrado',     modActual === 'completo' ? total : 0);

    fetch('controllers/add_pago.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                cerrarNueva();
                cargar();
                setTimeout(function(){ verRemision(res.id); }, 400);
            } else {
                alert('Error: ' + res.msg);
                btn.disabled = false; btn.textContent = 'Generar remisión';
            }
        });
}

// ── Ver remisión ──
function verRemision(id) {
    fetch('controllers/get_pago.php?id=' + id)
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (!res.ok) { alert('Error al cargar'); return; }
            remActivaId = id;
            remActiva   = res.data;

            // Cargar items de la cotización asociada
            var idCot = remActiva.id_cotizacion;
            if (idCot) {
                fetch('controllers/get_cotizacion.php?id=' + idCot)
                    .then(function(r){ return r.json(); })
                    .then(function(cr) {
                        remActiva._cotItems = (cr.ok && cr.data) ? cr.data.items : [];
                        renderVerRem(remActiva);
                        document.getElementById('modal-ver').classList.add('open');
                    });
            } else {
                remActiva._cotItems = [];
                renderVerRem(remActiva);
                document.getElementById('modal-ver').classList.add('open');
            }
        });
}

function renderVerRem(r) {
    var esCred  = r.modalidad !== 'completo';
    var total   = parseFloat(r.monto);
    var cobrado = parseFloat(r.monto_cobrado||0);
    var pend    = Math.max(0, total - cobrado);
    var pct     = total > 0 ? Math.min(100, Math.round(cobrado/total*100)) : 100;
    var modLabel = r.modalidad==='completo' ? 'Contado' : r.modalidad==='fijo' ? 'Crédito fijo' : 'Crédito variable';

    document.getElementById('ver-titulo').textContent = 'Remisión — ' + (r.folio_doc||'');
    document.getElementById('btn-abono').style.display = (esCred && pend > 0) ? 'inline-flex' : 'none';

    var html = '';

    // Tipo de pago
    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">' +
        '<div style="padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);">' +
            '<div style="font-size:0.65rem;color:var(--muted);text-transform:uppercase;font-weight:600;">Tipo de pago</div>' +
            '<div style="font-weight:600;color:var(--text);margin-top:2px;">' + modLabel + '</div>' +
        '</div>' +
        '<div style="padding:10px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);">' +
            '<div style="font-size:0.65rem;color:var(--muted);text-transform:uppercase;font-weight:600;">Cliente</div>' +
            '<div style="font-weight:600;color:var(--text);margin-top:2px;">' + esc(r.nombre_cliente||'—') + '</div>' +
        '</div>' +
    '</div>';

    // Artículos de la cotización
    if (r._cotItems && r._cotItems.length) {
        html += '<div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted);margin-bottom:6px;">Artículos</div>';
        html += '<div class="table-scroll" style="margin-bottom:14px;">' +
            '<table style="min-width:300px;">' +
            '<thead><tr>' +
                '<th style="font-size:0.65rem;padding:7px 10px;background:var(--bg);color:var(--muted);font-weight:600;text-transform:uppercase;text-align:left;border-bottom:1px solid var(--border);">Artículo</th>' +
                '<th style="font-size:0.65rem;padding:7px 10px;background:var(--bg);color:var(--muted);font-weight:600;text-transform:uppercase;text-align:center;border-bottom:1px solid var(--border);">Cant.</th>' +
                '<th style="font-size:0.65rem;padding:7px 10px;background:var(--bg);color:var(--muted);font-weight:600;text-transform:uppercase;text-align:right;border-bottom:1px solid var(--border);">Subtotal</th>' +
            '</tr></thead><tbody>';
        r._cotItems.forEach(function(i) {
            html += '<tr>' +
                '<td style="padding:8px 10px;border-bottom:1px solid var(--border);font-size:0.82rem;">' + esc(i.nombre) + '</td>' +
                '<td style="padding:8px 10px;border-bottom:1px solid var(--border);font-size:0.82rem;text-align:center;">' + i.cantidad + '</td>' +
                '<td style="padding:8px 10px;border-bottom:1px solid var(--border);font-size:0.82rem;text-align:right;font-weight:600;">' + fmt(i.subtotal) + '</td>' +
            '</tr>';
        });
        html += '</tbody></table></div>';
    }

    // Resumen financiero
    if (esCred) {
        html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">' +
            '<div style="padding:12px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);">' +
                '<div style="font-size:0.65rem;color:var(--muted);text-transform:uppercase;font-weight:600;">Total acordado</div>' +
                '<div style="font-size:1.2rem;font-weight:700;color:var(--text);margin-top:4px;">' + fmt(total) + '</div>' +
            '</div>' +
            '<div style="padding:12px 14px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);">' +
                '<div style="font-size:0.65rem;color:var(--muted);text-transform:uppercase;font-weight:600;">Total pagado</div>' +
                '<div style="font-size:1.2rem;font-weight:700;color:#16a34a;margin-top:4px;">' + fmt(cobrado) + '</div>' +
            '</div>' +
        '</div>' +
        // Pendiente en ROJO GRANDE
        '<div class="pendiente-destacado">' +
            '<div class="pd-label">⚠ Pendiente por pagar</div>' +
            '<div class="pd-monto">' + fmt(pend) + '</div>' +
        '</div>' +
        '<div style="margin-bottom:14px;">' +
            '<div class="prog-bar" style="height:8px;width:100%;"><div class="prog-fill" style="width:' + pct + '%"></div></div>' +
            '<div style="display:flex;justify-content:space-between;margin-top:5px;font-size:0.72rem;">' +
                '<span style="color:var(--muted);">' + pct + '% pagado</span>' +
                '<span style="color:var(--muted);">' + (100-pct) + '% pendiente</span>' +
            '</div>' +
        '</div>';
    } else {
        html += '<div style="padding:12px 16px;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.3);border-radius:var(--radius-sm);margin-bottom:14px;display:flex;justify-content:space-between;align-items:center;">' +
            '<span style="font-size:0.82rem;font-weight:600;color:#16a34a;">✓ Pagado de contado</span>' +
            '<span style="font-size:1.1rem;font-weight:700;color:var(--text);">' + fmt(total) + '</span>' +
        '</div>';
    }

    // Abonos
    if (r.abonos && r.abonos.length) {
        html += '<div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted);margin-bottom:8px;">Abonos registrados</div>';
        html += '<div style="display:flex;flex-direction:column;gap:6px;">';
        r.abonos.forEach(function(a) {
            var fa = new Date(a.created_at).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'});
            html += '<div class="abono-row">' +
                '<span class="abono-num">#' + a.numero_abono + '</span>' +
                '<div class="abono-info">' + fa + (a.referencia ? ' · ' + esc(a.referencia) : '') + '</div>' +
                '<span class="metodo-badge badge-' + a.metodo + '">' + a.metodo + '</span>' +
                '<span class="abono-monto">' + fmt(a.monto) + '</span>' +
            '</div>';
        });
        html += '</div>';
    }

    if (r.notas) {
        html += '<div style="font-size:0.78rem;color:var(--muted);margin-top:10px;">Notas: ' + esc(r.notas) + '</div>';
    }

    document.getElementById('ver-body').innerHTML = html;
}

function cerrarVer() { document.getElementById('modal-ver').classList.remove('open'); }

function eliminarRemision() {
    if (!remActivaId) return;
    if (!confirm('¿Eliminar esta nota de remisión?')) return;
    var fd = new FormData();
    fd.append('id', remActivaId);
    fetch('controllers/delete_pago.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) { cerrarVer(); cargar(); }
            else alert('Error: ' + res.msg);
        });
}

// ── Abono ──
function abrirAbono() {
    if (!remActiva) return;
    var cobrado = parseFloat(remActiva.monto_cobrado||0);
    var total   = parseFloat(remActiva.monto);
    var pend    = Math.max(0, total - cobrado);
    document.getElementById('abono-info').innerHTML =
        'Total: <strong>' + fmt(total) + '</strong> · ' +
        'Pagado: <strong>' + fmt(cobrado) + '</strong> · ' +
        'Pendiente: <strong style="color:#ef4444;font-size:1.05rem;">' + fmt(pend) + '</strong>';
    document.getElementById('ab-monto').value = '';
    document.getElementById('ab-monto').placeholder = 'Pendiente: ' + fmt(pend);
    document.getElementById('ab-referencia').value = '';
    document.getElementById('ab-notas').value = '';
    document.getElementById('btn-guardar-abono').disabled = false;
    document.getElementById('btn-guardar-abono').textContent = 'Guardar abono';
    document.getElementById('modal-abono').classList.add('open');
}

function cerrarAbono() { document.getElementById('modal-abono').classList.remove('open'); }

function guardarAbono() {
    var monto = parseFloat(document.getElementById('ab-monto').value)||0;
    if (!monto) { resaltar('ab-monto'); return; }
    var btn = document.getElementById('btn-guardar-abono');
    btn.disabled = true; btn.textContent = 'Guardando...';
    var fd = new FormData();
    fd.append('id_pago',    remActivaId);
    fd.append('monto',      monto);
    fd.append('metodo',     document.getElementById('ab-metodo').value);
    fd.append('referencia', document.getElementById('ab-referencia').value.trim());
    fd.append('notas',      document.getElementById('ab-notas').value.trim());
    fetch('controllers/add_abono.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                cerrarAbono(); cargar();
                setTimeout(function(){ verRemision(remActivaId); }, 300);
            } else {
                alert('Error: ' + res.msg);
                btn.disabled = false; btn.textContent = 'Guardar abono';
            }
        });
}

// ── Compartir ──
function abrirCompartir()  { document.getElementById('modal-compartir').classList.add('open'); }
function cerrarCompartir() { document.getElementById('modal-compartir').classList.remove('open'); }

function textoResumen() {
    if (!remActiva) return '';
    var r       = remActiva;
    var esCred  = r.modalidad !== 'completo';
    var total   = parseFloat(r.monto);
    var cobrado = parseFloat(r.monto_cobrado||0);
    var pend    = Math.max(0, total - cobrado);
    var modLabel = r.modalidad==='completo' ? 'Contado' : r.modalidad==='fijo' ? 'Crédito fijo' : 'Crédito variable';

    var txt = '*Nota de Remisión — Villa Tecnia Tampico*\n';
    txt += 'Folio: ' + (r.folio_doc||'—') + '\n';
    txt += 'Cliente: ' + (r.nombre_cliente||'—') + '\n';
    txt += 'Tipo de pago: ' + modLabel + '\n\n';

    if (r._cotItems && r._cotItems.length) {
        txt += '*Artículos:*\n';
        r._cotItems.forEach(function(i) {
            txt += '• ' + i.nombre + ' x' + i.cantidad + ' — ' + fmt(i.subtotal) + '\n';
        });
        txt += '\n';
    }

    txt += 'Total: ' + fmt(total) + '\n';
    if (esCred) {
        txt += 'Pagado: ' + fmt(cobrado) + '\n';
        txt += '*Pendiente por pagar: ' + fmt(pend) + '*\n';
    } else {
        txt += '✓ Pagado de contado\n';
    }

    if (r.notas) txt += '\nNotas: ' + r.notas;
    return txt;
}

function compartirWhatsapp() {
    window.open('https://wa.me/?text=' + encodeURIComponent(textoResumen()), '_blank');
}

function compartirEmail() {
    if (!remActiva) return;
    var asunto = encodeURIComponent('Nota de Remisión ' + (remActiva.folio_doc||'') + ' — Villa Tecnia Tampico');
    var cuerpo = encodeURIComponent(textoResumen().replace(/\*/g,''));
    window.open('mailto:?subject=' + asunto + '&body=' + cuerpo, '_blank');
}

function imprimirRemision() {
    if (!remActiva) return;
    var html = generarHTMLImpresion(remActiva);
    var win  = window.open('', '_blank');
    win.document.write('<!DOCTYPE html><html><head>' +
        '<meta charset="UTF-8">' +
        '<title>Remisión — ' + esc(remActiva.folio_doc||'') + '</title>' +
        '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">' +
        '<style>' + cssPrint() + '</style>' +
        '</head><body>' + html + '</body></html>');
    win.document.close();
    setTimeout(function(){ win.print(); }, 800);
}

function generarHTMLImpresion(r) {
    var esCred  = r.modalidad !== 'completo';
    var total   = parseFloat(r.monto);
    var cobrado = parseFloat(r.monto_cobrado||0);
    var pend    = Math.max(0, total - cobrado);
    var pct     = total > 0 ? Math.min(100, Math.round(cobrado/total*100)) : 100;
    var fecha   = new Date(r.created_at);
    var fStr    = fecha.toLocaleDateString('es-MX',{weekday:'long',day:'2-digit',month:'long',year:'numeric'});
    var hStr    = fecha.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'});
    var modLabel = r.modalidad==='completo' ? 'Contado' : r.modalidad==='fijo' ? 'Crédito fijo en mensualidades' : 'Crédito con abonos variables';
    var metodoLabel = { efectivo:'Efectivo', transferencia:'Transferencia bancaria', tarjeta:'Tarjeta' };

    // Artículos
    var filasArt = '';
    if (r._cotItems && r._cotItems.length) {
        r._cotItems.forEach(function(i) {
            var pu = parseFloat(i.precio_unitario||0);
            filasArt += '<tr>' +
                '<td>' + esc(i.nombre) + '</td>' +
                '<td class="c">' + i.cantidad + '</td>' +
                '<td class="r">' + fmt(pu) + '</td>' +
                '<td class="r">' + fmt(i.subtotal) + '</td>' +
            '</tr>';
        });
    }

    // Abonos
    var filasAbonos = '';
    if (r.abonos && r.abonos.length) {
        r.abonos.forEach(function(a) {
            var fa   = new Date(a.created_at);
            var faS  = fa.toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'});
            var faH  = fa.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'});
            filasAbonos += '<tr>' +
                '<td>Abono #' + a.numero_abono + '</td>' +
                '<td>' + faS + ' ' + faH + '</td>' +
                '<td>' + (metodoLabel[a.metodo]||a.metodo) + '</td>' +
                '<td>' + (a.referencia ? esc(a.referencia) : '—') + '</td>' +
                '<td class="r">' + fmt(a.monto) + '</td>' +
            '</tr>';
        });
    }

    // Mensualidades (fijo)
    var secMens = '';
    if (r.modalidad === 'fijo' && r.mensualidades) {
        var mens  = parseInt(r.mensualidades);
        var mm    = parseFloat(r.monto_mensualidad || (total/mens));
        var pags  = parseInt(r.abonos_pagados||0);
        secMens   = '<div class="section-title">Plan de mensualidades</div>' +
            '<table><thead><tr><th>Mes</th><th class="r">Monto</th><th>Estado</th></tr></thead><tbody>';
        for (var i = 1; i <= mens; i++) {
            var ul   = (i===mens) ? parseFloat((total-mm*(mens-1)).toFixed(2)) : mm;
            var paid = i <= pags;
            secMens += '<tr class="' + (paid?'mens-pagada':'') + '">' +
                '<td>Mes ' + i + '</td>' +
                '<td class="r">' + fmt(ul) + '</td>' +
                '<td class="' + (paid?'est-pagado':'est-pend') + '">' + (paid?'✓ Pagado':'Pendiente') + '</td>' +
            '</tr>';
        }
        secMens += '</tbody></table>';
    }

    return '<div class="doc">' +
        // Encabezado
        '<div class="header">' +
            '<div class="logo-wrap">' +
                '<div class="logo-ring"><img src="assets/logo/logovttr.png" alt="VT"></div>' +
                '<div>' +
                    '<div class="empresa">Villa Tecnia Tampico</div>' +
                    '<div class="empresa-sub">Materiales para alberca</div>' +
                '</div>' +
            '</div>' +
            '<div class="meta">' +
                '<div class="doc-title">NOTA DE REMISIÓN</div>' +
                '<div class="folio">' + esc(r.folio_doc||'—') + '</div>' +
                '<div class="fecha">' + fStr + '<br>' + hStr + ' hrs</div>' +
            '</div>' +
        '</div>' +

        // Info cliente + pago
        '<div class="partes">' +
            '<div class="parte">' +
                '<div class="parte-label">Cliente</div>' +
                '<div class="parte-nombre">' + esc(r.nombre_cliente||'—') + '</div>' +
            '</div>' +
            '<div class="parte">' +
                '<div class="parte-label">Tipo de pago</div>' +
                '<div class="parte-nombre">' + modLabel + '</div>' +
                '<div class="parte-sub">Método: ' + (metodoLabel[r.metodo]||r.metodo) + '</div>' +
                (r.referencia ? '<div class="parte-sub">Ref: ' + esc(r.referencia) + '</div>' : '') +
            '</div>' +
        '</div>' +

        // Artículos
        (filasArt ? '<div class="section-title">Artículos</div>' +
        '<table><thead><tr><th>Descripción</th><th class="c">Cant.</th><th class="r">P.Unit.</th><th class="r">Subtotal</th></tr></thead>' +
        '<tbody>' + filasArt + '</tbody>' +
        '<tfoot><tr><td colspan="3" class="r" style="font-weight:600;">Total</td><td class="r" style="font-weight:700;">' + fmt(total) + '</td></tr></tfoot>' +
        '</table>' : '') +

        // Resumen financiero
        '<div class="resumen">' +
            '<div class="res-item">' +
                '<div class="res-label">Total acordado</div>' +
                '<div class="res-val">' + fmt(total) + '</div>' +
            '</div>' +
            '<div class="res-item">' +
                '<div class="res-label">Total pagado</div>' +
                '<div class="res-val cobrado">' + fmt(cobrado) + '</div>' +
            '</div>' +
            // PENDIENTE EN ROJO GRANDE
            '<div class="res-item pend-box">' +
                '<div class="res-label" style="color:#dc2626;">⚠ Pendiente por pagar</div>' +
                '<div class="res-val pendiente">' + fmt(pend) + '</div>' +
            '</div>' +
        '</div>' +

        // Barra de progreso
        (esCred ? '<div class="prog-wrap"><div class="prog-bar"><div class="prog-fill" style="width:' + pct + '%"></div></div><div class="prog-label">' + pct + '% pagado · ' + (100-pct) + '% pendiente</div></div>' : '') +

        // Historial abonos
        (filasAbonos ? '<div class="section-title">Historial de abonos</div>' +
        '<table><thead><tr><th>Concepto</th><th>Fecha</th><th>Método</th><th>Ref.</th><th class="r">Monto</th></tr></thead>' +
        '<tbody>' + filasAbonos + '</tbody>' +
        '<tfoot><tr><td colspan="4" class="r" style="font-weight:600;">Total abonado</td><td class="r" style="font-weight:700;">' + fmt(cobrado) + '</td></tr></tfoot>' +
        '</table>' : '') +

        secMens +

        (r.notas ? '<div class="notas"><strong>Notas:</strong> ' + esc(r.notas) + '</div>' : '') +
        '<div class="footer">Gracias por su confianza · Villa Tecnia Tampico</div>' +
    '</div>';
}

function cssPrint() {
    return [
        'body{font-family:Poppins,sans-serif;margin:0;padding:20px;background:#f8fafc;}',
        '.doc{max-width:720px;margin:0 auto;background:#fff;padding:36px 40px;border-radius:12px;border:1px solid #e2e8f0;}',
        '.header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;padding-bottom:20px;border-bottom:2px solid #eab308;}',
        '.logo-wrap{display:flex;align-items:center;gap:12px;}',
        '.logo-ring{width:46px;height:46px;border-radius:50%;border:2px solid #eab308;overflow:hidden;background:#0f172a;display:flex;align-items:center;justify-content:center;}',
        '.logo-ring img{width:75%;height:75%;object-fit:contain;}',
        '.empresa{font-size:1rem;font-weight:700;color:#0f172a;}',
        '.empresa-sub{font-size:0.65rem;color:#64748b;}',
        '.meta{text-align:right;}',
        '.doc-title{font-size:0.62rem;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:#64748b;}',
        '.folio{font-size:1rem;font-weight:700;color:#eab308;margin-top:2px;}',
        '.fecha{font-size:0.7rem;color:#64748b;margin-top:4px;}',
        '.partes{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;}',
        '.parte{padding:12px 16px;background:#f8fafc;border-radius:8px;border-left:3px solid #eab308;}',
        '.parte-label{font-size:0.6rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;}',
        '.parte-nombre{font-size:0.9rem;font-weight:600;color:#0f172a;margin-top:3px;}',
        '.parte-sub{font-size:0.72rem;color:#64748b;margin-top:2px;}',
        '.section-title{font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#64748b;margin:18px 0 8px;}',
        'table{width:100%;border-collapse:collapse;font-size:0.8rem;margin-bottom:14px;}',
        'thead th{text-align:left;padding:8px 10px;background:#0f172a;color:#f1f5f9;font-size:0.65rem;font-weight:600;text-transform:uppercase;}',
        'tbody td{padding:9px 10px;border-bottom:1px solid #e2e8f0;color:#0f172a;}',
        'tfoot td{padding:9px 10px;border-top:2px solid #eab308;}',
        '.mens-pagada td{background:#f0fdf4;}',
        '.est-pagado{color:#16a34a;font-weight:600;}',
        '.est-pend{color:#94a3b8;}',
        '.c{text-align:center;}.r{text-align:right;font-weight:600;}',
        /* Resumen */
        '.resumen{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin:16px 0;}',
        '.res-item{padding:12px 14px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;}',
        '.res-label{font-size:0.6rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;}',
        '.res-val{font-size:1.05rem;font-weight:700;color:#0f172a;margin-top:4px;}',
        '.res-val.cobrado{color:#16a34a;}',
        /* PENDIENTE ROJO GRANDE */
        '.pend-box{background:#fff5f5;border:2px solid #fca5a5;}',
        '.res-val.pendiente{color:#dc2626;font-size:1.5rem;font-weight:800;}',
        /* Barra */
        '.prog-wrap{margin-bottom:16px;}',
        '.prog-bar{height:8px;background:#e2e8f0;border-radius:999px;overflow:hidden;margin-bottom:4px;}',
        '.prog-fill{height:100%;background:linear-gradient(90deg,#22c55e,#16a34a);border-radius:999px;}',
        '.prog-label{font-size:0.68rem;color:#64748b;text-align:right;}',
        '.notas{font-size:0.78rem;color:#475569;padding:10px 14px;background:#f8fafc;border-radius:8px;margin-top:14px;}',
        '.footer{margin-top:24px;padding-top:14px;border-top:1px solid #e2e8f0;text-align:center;font-size:0.68rem;color:#94a3b8;}',
        '@media print{body{padding:0;background:#fff;}.doc{border:none;border-radius:0;}}'
    ].join('');
}

// ── Utilidades ──
function fmt(n) { return '$' + parseFloat(n).toFixed(2); }
function resaltar(id) {
    var el = document.getElementById(id);
    el.style.borderColor = '#ef4444'; el.focus();
    el.addEventListener('input', function(){ el.style.borderColor = ''; }, { once:true });
}
function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
// ══ EDICIÓN ══
var editItems    = [];
var editModActual = 'completo';
var editOrigItems = [];

function abrirEditar() {
    if (!remActiva) return;
    var r = remActiva;
    editModActual = r.modalidad || 'completo';
    editItems = (r._cotItems || []).map(function(i) {
        return { tipo: i.tipo, nombre: i.nombre, cantidad: parseInt(i.cantidad), precio_unitario: parseFloat(i.precio_unitario) };
    });
    editOrigItems = JSON.parse(JSON.stringify(editItems));

    document.getElementById('editar-titulo').textContent = 'Editar — ' + (r.folio_doc||'');
    document.getElementById('e-metodo').value    = r.metodo || 'efectivo';
    document.getElementById('e-referencia').value = r.referencia || '';
    document.getElementById('e-notas').value      = r.notas || '';
    document.getElementById('e-mensualidades').value = r.mensualidades || '';
    setModEdit(editModActual);
    renderEditItems();
    cerrarVer();
    document.getElementById('modal-editar').classList.add('open');
}

function cerrarEditar() { document.getElementById('modal-editar').classList.remove('open'); }

function setModEdit(mod) {
    editModActual = mod;
    ['completo','fijo','variable'].forEach(function(m) {
        document.getElementById('e-mod-btn-' + m).className = m === mod ? 'btn-sm btn-primary' : 'btn-sm btn-outline';
    });
    document.getElementById('e-sec-fijo').style.display = mod === 'fijo' ? 'block' : 'none';
}

function renderEditItems() {
    var el = document.getElementById('e-items-list');
    if (!editItems.length) {
        el.innerHTML = '<p style="font-size:0.78rem;color:var(--muted);">Sin artículos.</p>';
        actualizarTotalEdit();
        return;
    }
    el.innerHTML = editItems.map(function(item, i) {
        var precio = parseFloat(item.precio_unitario)||0;
        var base   = precio / 1.16;
        return '<div style="display:grid;grid-template-columns:1fr 60px 100px 32px;gap:8px;align-items:center;">' +
            '<div>' +
                '<input type="text" value="' + esc(item.nombre) + '" placeholder="Descripción" ' +
                    'onchange="editItems[' + i + '].nombre=this.value" ' +
                    'style="padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.8rem;outline:none;width:100%;">' +
                '<div style="font-size:0.65rem;color:var(--muted);margin-top:2px;">base: ' + fmt(base) + ' + IVA: ' + fmt(precio-base) + '</div>' +
            '</div>' +
            '<input type="number" value="' + item.cantidad + '" min="1" ' +
                'onchange="editItems[' + i + '].cantidad=parseInt(this.value)||1;actualizarTotalEdit()" ' +
                'style="padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.8rem;outline:none;width:100%;">' +
            '<input type="number" value="' + precio.toFixed(2) + '" min="0" step="0.01" ' +
                'onchange="editItems[' + i + '].precio_unitario=parseFloat(this.value)||0;actualizarTotalEdit();renderEditItems()" ' +
                'style="padding:7px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);color:var(--text);font-family:inherit;font-size:0.8rem;outline:none;width:100%;">' +
            '<button onclick="editItems.splice(' + i + ',1);renderEditItems()" ' +
                'style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:1.1rem;padding:4px;transition:color 0.15s;" ' +
                'onmouseover="this.style.color=\'#ef4444\'" onmouseout="this.style.color=\'var(--muted)\'">&times;</button>' +
        '</div>';
    }).join('');
    actualizarTotalEdit();
}

function eAgregarItem(tipo) {
    editItems.push({ tipo: tipo, nombre: '', cantidad: 1, precio_unitario: 0 });
    renderEditItems();
}

function actualizarTotalEdit() {
    var total = editItems.reduce(function(a,i) {
        return a + (parseFloat(i.precio_unitario)||0) * (parseInt(i.cantidad)||1);
    }, 0);
    document.getElementById('e-total-val').textContent = fmt(total);
}

function itemsModificados() {
    if (editItems.length !== editOrigItems.length) return true;
    for (var i = 0; i < editItems.length; i++) {
        var a = editItems[i], b = editOrigItems[i];
        if (a.nombre !== b.nombre || a.cantidad !== b.cantidad || a.precio_unitario !== b.precio_unitario) return true;
    }
    return false;
}

function guardarEdicion() {
    if (!remActiva) return;
    if (itemsModificados()) {
        document.getElementById('modal-confirm-cot').classList.add('open');
    } else {
        confirmarEdicion(null);
    }
}

function cerrarConfirmCot() { document.getElementById('modal-confirm-cot').classList.remove('open'); }

function confirmarEdicion(accionCot) {
    cerrarConfirmCot();
    var btn = document.getElementById('btn-guardar-edit');
    btn.disabled = true; btn.textContent = 'Guardando...';

    var r      = remActiva;
    var total  = editItems.reduce(function(a,i){ return a+(parseFloat(i.precio_unitario)||0)*(parseInt(i.cantidad)||1); }, 0);
    var menses = editModActual === 'fijo' ? parseInt(document.getElementById('e-mensualidades').value)||0 : 0;

    var tareas = [];

    // 1. Actualizar o duplicar cotización si hubo cambios
    if (accionCot === 'actualizar') {
        tareas.push(function() {
            var fd = new FormData();
            fd.append('id',             r.id_cotizacion);
            fd.append('nombre_cliente', r.nombre_cliente);
            fd.append('items',          JSON.stringify(editItems));
            return fetch('controllers/update_cotizacion.php', { method:'POST', body:fd }).then(function(r){ return r.json(); });
        });
    } else if (accionCot === 'duplicar') {
        tareas.push(function() {
            var fd = new FormData();
            fd.append('nombre_cliente', r.nombre_cliente);
            fd.append('items',          JSON.stringify(editItems));
            return fetch('controllers/add_cotizacion.php', { method:'POST', body:fd })
                .then(function(res){ return res.json(); })
                .then(function(res) {
                    if (res.ok) {
                        // Actualizar folio_doc en el pago
                        var fd2 = new FormData();
                        fd2.append('id',            remActivaId);
                        fd2.append('id_cotizacion', res.id);
                        fd2.append('folio_doc',     res.folio);
                        return fetch('controllers/update_pago_cot.php', { method:'POST', body:fd2 }).then(function(r){ return r.json(); });
                    }
                });
        });
    }

    // 2. Actualizar pago
    tareas.push(function() {
        var fd = new FormData();
        fd.append('id',           remActivaId);
        fd.append('modalidad',    editModActual);
        fd.append('metodo',       document.getElementById('e-metodo').value);
        fd.append('referencia',   document.getElementById('e-referencia').value.trim());
        fd.append('notas',        document.getElementById('e-notas').value.trim());
        fd.append('monto',        total);
        fd.append('mensualidades', menses);
        return fetch('controllers/update_pago.php', { method:'POST', body:fd }).then(function(r){ return r.json(); });
    });

    // Ejecutar en serie
    tareas.reduce(function(p, t){ return p.then(t); }, Promise.resolve())
        .then(function() {
            cerrarEditar();
            cargar();
            btn.disabled = false; btn.textContent = 'Guardar cambios';
        })
        .catch(function(e) {
            alert('Error: ' + e.message);
            btn.disabled = false; btn.textContent = 'Guardar cambios';
        });
}

function duplicarRemision() {
    if (!remActiva) return;
    if (!confirm('¿Crear una copia de esta remisión?')) return;
    var r  = remActiva;
    var fd = new FormData();
    fd.append('tipo',           'remision');
    fd.append('modalidad',      r.modalidad);
    fd.append('nombre_cliente', r.nombre_cliente);
    fd.append('id_cotizacion',  r.id_cotizacion);
    fd.append('folio_doc',      r.folio_doc || '');
    fd.append('monto',          r.monto);
    fd.append('metodo',         r.metodo);
    fd.append('notas',          r.notas || '');
    fd.append('monto_cobrado',  r.modalidad === 'completo' ? r.monto : 0);
    fd.append('mensualidades',  r.mensualidades || 0);
    fetch('controllers/add_pago.php', { method:'POST', body:fd })
        .then(function(res){ return res.json(); })
        .then(function(res) {
            if (res.ok) { cerrarEditar(); cargar(); alert('Remisión duplicada correctamente'); }
            else alert('Error: ' + res.msg);
        });
}

(function() {
    var params = new URLSearchParams(window.location.search);
    var cotId  = params.get('cot_id');
    if (!cotId) return;

    // Esperar a que carguen las cotizaciones y abrir automáticamente
    var intentos = 0;
    var intervalo = setInterval(function() {
        intentos++;
        if (cotizaciones.length || intentos > 20) {
            clearInterval(intervalo);
            if (cotId) {
                abrirNueva();
                setTimeout(function() {
                    selCot(parseInt(cotId));
                }, 200);
            }
        }
    }, 150);
})();

// ── Inventario en editar remisión ──
var eProductosInv = [];

function eAbrirInventario() {
    document.getElementById('e-inv-buscar').value = '';
    document.getElementById('e-inv-lista').innerHTML = '<p style="text-align:center;color:var(--muted);font-size:0.78rem;padding:20px 0;">Cargando...</p>';
    document.getElementById('e-modal-inv').classList.add('open');

    fetch('controllers/get_inventario.php')
        .then(function(r){ return r.json(); })
        .then(function(data){
            eProductosInv = data;
            console.log('primer producto:', data[0]);
            eFiltrarInv();
        });
}

function eCerrarInventario() { document.getElementById('e-modal-inv').classList.remove('open'); }

function eFiltrarInv() {
    var q  = (document.getElementById('e-inv-buscar').value || '').toLowerCase();
    var lista = eProductosInv.filter(function(p){
        return p.nombre.toLowerCase().includes(q) || (p.sku||'').toLowerCase().includes(q);
    });
    var el = document.getElementById('e-inv-lista');
    if (!lista.length) {
        el.innerHTML = '<p style="text-align:center;color:var(--muted);font-size:0.78rem;padding:20px 0;">Sin resultados</p>';
        return;
    }
    el.innerHTML = lista.map(function(p) {
        var precio = parseFloat(p.precio_final);
        return '<div onclick="eSelProducto(\'' + esc(p.nombre) + '\',' + precio.toFixed(2) + ')" ' +
            'style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;' +
            'border:1px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;background:var(--surface);" ' +
            'onmouseover="this.style.background=\'var(--bg)\'" onmouseout="this.style.background=\'var(--surface)\'">' +
            '<div>' +
                '<div style="font-size:0.82rem;font-weight:600;color:var(--text);">' + esc(p.nombre) + '</div>' +
                (p.sku ? '<div style="font-size:0.68rem;color:var(--muted);">SKU: ' + esc(p.sku) + '</div>' : '') +
            '</div>' +
            '<div style="font-size:0.85rem;font-weight:700;color:var(--text);">' + fmt(precio) + '</div>' +
        '</div>';
    }).join('');
}

function eSelProducto(nombre, precio) {
    editItems.push({ tipo:'producto', nombre:nombre, cantidad:1, precio_unitario:precio });
    renderEditItems();
    eCerrarInventario();
}

// ── Nuevo artículo en editar remisión ──
function eAbrirNuevoArticulo() {
    document.getElementById('e-art-nombre').value = '';
    document.getElementById('e-art-precio').value = '';
    document.getElementById('e-art-preview').style.display = 'none';
    document.getElementById('e-modal-art').classList.add('open');
    setTimeout(function(){ document.getElementById('e-art-nombre').focus(); }, 100);
}

function eCerrarNuevoArt() { document.getElementById('e-modal-art').classList.remove('open'); }

function ePreviewArt() {
    var val = parseFloat(document.getElementById('e-art-precio').value);
    var prev = document.getElementById('e-art-preview');
    if (!isNaN(val) && val > 0) {
        var base = val / 1.16;
        document.getElementById('e-art-base').textContent = fmt(base);
        document.getElementById('e-art-iva').textContent  = fmt(val - base);
        prev.style.display = 'block';
    } else {
        prev.style.display = 'none';
    }
}

function eUsarArtTemporal() {
    var nombre = document.getElementById('e-art-nombre').value.trim();
    var precio = parseFloat(document.getElementById('e-art-precio').value) || 0;
    if (!nombre) { document.getElementById('e-art-nombre').style.borderColor='#ef4444'; return; }
    editItems.push({ tipo:'producto', nombre:nombre, cantidad:1, precio_unitario:precio });
    renderEditItems();
    eCerrarNuevoArt();
}

function eGuardarNuevoArt() {
    var nombre = document.getElementById('e-art-nombre').value.trim();
    var precio = parseFloat(document.getElementById('e-art-precio').value) || 0;
    if (!nombre || !precio) return;

    var fd = new FormData();
    fd.append('nombre',         nombre);
    fd.append('sku',            '');
    fd.append('precio_sin_iva', (precio / 1.16).toFixed(2));
    fd.append('descripcion',    '');

    fetch('controllers/add_inventario.php', { method:'POST', body:fd })
        .then(function(r){ return r.json(); })
        .then(function(res) {
            if (res.ok) {
                eProductosInv = []; // forzar recarga
                editItems.push({ tipo:'producto', nombre:nombre, cantidad:1, precio_unitario:precio });
                renderEditItems();
                eCerrarNuevoArt();
            } else {
                alert('Error: ' + res.msg);
            }
        });
}
function irACrearCotizacion() {
    window.location.href = 'cotizaciones.php?desde=remision';
}
(function () {
    const params = new URLSearchParams(window.location.search);

    const cotId = parseInt(params.get('cot_id'), 10);
    const openId = parseInt(params.get('open'), 10);

    window._openRemId = !isNaN(openId) ? openId : null;

    if (isNaN(cotId)) return;

    let intentos = 0;

    const intervalo = setInterval(function () {
        intentos++;

        if ((window.cotizaciones && cotizaciones.length) || intentos > 20) {
            clearInterval(intervalo);

            abrirNueva();

            setTimeout(function () {

                selCot(cotId);

                // Abrir la remisión después de seleccionar la cotización
                if (window._openRemId) {
                    setTimeout(function () {
                        verRemision(window._openRemId);
                        window._openRemId = null;
                    }, 200);
                }

            }, 200);
        }
    }, 150);

})();
(function () {
    const params = new URLSearchParams(window.location.search);
    const cotId  = params.get('cot_id');
    const openId = parseInt(params.get('open'), 10);

    cargar().then(function() {
        if (cotId) {
            abrirNueva();
            setTimeout(function() { selCot(parseInt(cotId)); }, 200);
        }
        if (!isNaN(openId)) {
            verRemision(openId);
        }
    });
})();

function cargar() {
    return Promise.all([
        fetch('controllers/get_pagos.php').then(r => r.json()),
        fetch('controllers/get_cotizaciones.php').then(r => r.json()),
        fetch('controllers/get_clientes.php').then(r => r.json())
    ]).then(function(results) {
        remisiones    = results[0].filter(function(p){ return p.tipo === 'remision'; });
        cotizaciones  = results[1];
        clientesLista = results[2];
        renderLista(remisiones);
        renderStats(remisiones);
    }).catch(function(err){
        console.error(err);
    });
}
</script>
</body>
</html>