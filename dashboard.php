<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id_user'])) { header('Location: login.php'); exit; }
$nombre = $_SESSION['nombre'] ?? 'Usuario';
$id_rol = $_SESSION['id_rol'] ?? 2;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<link rel="stylesheet" href="css/dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

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
        <a class="nav-item active" href="dashboard.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>
        <div class="nav-section">Ventas</div>
        <a class="nav-item" href="cotizaciones.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Cotizaciones
        </a>
        <a class="nav-item" href="remisiones.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
            Notas de Remision
        </a>
        <div class="nav-section">Operaciones</div>
        <a class="nav-item" href="contactos.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Contactos
        </a>
        <a class="nav-item" href="inventario.php">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            Inventario
        </a>
        <?php if ($id_rol == 1): ?>
        <div class="nav-section">Administración</div>
        <a class="nav-item" href="usuarios.php">
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
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    Modo oscuro
                </span>
                <div class="toggle-track" id="toggle-track"><div class="toggle-thumb"></div></div>
            </button>
            <a href="controllers/logout.php" class="config-item logout-item">
                <span class="config-item-left">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
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
            <span class="page-title">Dashboard</span>
        </div>
    </header>

    <div class="content">
        <div class="section-header">
            <div class="section-title">Bienvenido, <?= htmlspecialchars(explode(' ', $nombre)[0]) ?> 👋</div>
            <div class="section-sub">Resumen general del negocio</div>
        </div>

        <!-- Stats -->
        <div class="stats-grid" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="stat-label">Ingresos del mes</div>
                <div class="stat-value" id="stat-ingresos">—</div>
                <div class="stat-change" id="stat-ingresos-sub">Cargando...</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="stat-label">Cotizaciones</div>
                <div class="stat-value" id="stat-cots">—</div>
                <div class="stat-change" id="stat-cots-sub">Cargando...</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="stat-label">Contactos</div>
                <div class="stat-value" id="stat-contactos">—</div>
                <div class="stat-change" id="stat-contactos-sub">Cargando...</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
                <div class="stat-label">Productos en stock</div>
                <div class="stat-value" id="stat-productos">—</div>
                <div class="stat-change" id="stat-productos-sub">Cargando...</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid" style="margin-bottom:24px;">
            <div class="chart-card">
                <div class="chart-title">Ingresos mensuales</div>
                <div class="chart-sub" id="chart-ingresos-sub">Últimos 12 meses</div>
                <div class="chart-wrap"><canvas id="chartIngresos"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">Estado de cotizaciones</div>
                <div class="chart-sub">Distribución actual</div>
                <div class="chart-wrap"><canvas id="chartCotizaciones"></canvas></div>
            </div>
        </div>

        <!-- Actividad reciente -->
        <div class="table-card">
            <div class="table-header">
                <span class="table-title">Actividad reciente</span>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr><th>Folio</th><th>Cliente</th><th>Módulo</th><th>Total</th><th>Fecha</th></tr>
                    </thead>
                    <tbody id="tbody-actividad">
                        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:32px;">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
var chartIngresos, chartCotizaciones;

function toggleTheme() {
    var isDark = document.body.getAttribute('data-theme') === 'dark';
    document.body.setAttribute('data-theme', isDark ? '' : 'dark');
    localStorage.setItem('vt-theme', isDark ? 'light' : 'dark');
    document.getElementById('toggle-track').classList.toggle('on', !isDark);
    updateCharts();
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

function fmt(n) { return '$' + parseFloat(n).toFixed(2); }

function esc(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function getChartColors() {
    var dark = document.body.getAttribute('data-theme') === 'dark';
    return {
        grid: dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)',
        text: dark ? '#94a3b8' : '#64748b'
    };
}

function buildCharts(ingresosData, estCots) {
    var c = getChartColors();
    var meses = [];
    var hoy = new Date();
    for (var i = 11; i >= 0; i--) {
        var d = new Date(hoy.getFullYear(), hoy.getMonth() - i, 1);
        meses.push(d.toLocaleDateString('es-MX', {month:'short'}));
    }

    if (chartIngresos) chartIngresos.destroy();
    chartIngresos = new Chart(document.getElementById('chartIngresos').getContext('2d'), {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [{
                label: 'Ingresos ($)',
                data: ingresosData,
                backgroundColor: 'rgba(234,179,8,0.18)',
                borderColor: '#eab308',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: c.grid }, ticks: { color: c.text, font: { family:'Poppins', size:11 } } },
                y: { grid: { color: c.grid }, ticks: { color: c.text, font: { family:'Poppins', size:11 } } }
            }
        }
    });

    if (chartCotizaciones) chartCotizaciones.destroy();
    chartCotizaciones = new Chart(document.getElementById('chartCotizaciones').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Pagadas','Parciales','Pendientes'],
            datasets: [{
                data: [estCots.pagado || 0, estCots.parcial || 0, estCots.pendiente || 0],
                backgroundColor: ['#22c55e','#eab308','#ef4444'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: c.text, font: { family:'Poppins', size:11 }, padding:16, boxWidth:12 }
                }
            }
        }
    });
}

function updateCharts() {
    var c = getChartColors();
    if (chartIngresos) {
        chartIngresos.options.scales.x.grid.color = c.grid;
        chartIngresos.options.scales.y.grid.color = c.grid;
        chartIngresos.options.scales.x.ticks.color = c.text;
        chartIngresos.options.scales.y.ticks.color = c.text;
        chartIngresos.update();
    }
    if (chartCotizaciones) {
        chartCotizaciones.options.plugins.legend.labels.color = c.text;
        chartCotizaciones.update();
    }
}

function cargar() {
    fetch('controllers/get_dashboard.php')
        .then(function(r){ return r.json(); })
        .then(function(d) {
            if (!d.ok) return;

            // Stats
            document.getElementById('stat-ingresos').textContent     = fmt(d.ingresos_mes);
            document.getElementById('stat-ingresos-sub').textContent  = 'Cobrado este mes';
            document.getElementById('stat-ingresos-sub').className    = 'stat-change';

            document.getElementById('stat-cots').textContent         = d.total_cots;
            document.getElementById('stat-cots-sub').textContent      = d.cots_mes + ' este mes';

            document.getElementById('stat-contactos').textContent     = d.total_contactos;
            document.getElementById('stat-contactos-sub').textContent = 'Registrados';

            document.getElementById('stat-productos').textContent     = d.total_productos;
            document.getElementById('stat-productos-sub').textContent = 'En inventario';

            // Charts
            buildCharts(d.ingresos_chart, d.est_cots);

            // Actividad reciente
            var tbody = document.getElementById('tbody-actividad');
            if (!d.actividad.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:32px;font-size:0.78rem;">Sin actividad reciente</td></tr>';
                return;
            }
            var html = '';
            d.actividad.forEach(function(a) {
                var fecha = new Date(a.created_at).toLocaleDateString('es-MX',{day:'2-digit',month:'short',year:'numeric'});
                var modulo = a.tipo === 'cotizacion'
                    ? '<span class="badge badge-blue">Cotización</span>'
                    : '<span class="badge badge-green">Factura</span>';
                html += '<tr>' +
                    '<td style="font-weight:600;color:var(--accent);">' + esc(a.folio) + '</td>' +
                    '<td>' + esc(a.nombre_cliente) + '</td>' +
                    '<td>' + modulo + '</td>' +
                    '<td style="font-weight:600;">' + fmt(a.total) + '</td>' +
                    '<td style="color:var(--muted);font-size:0.78rem;">' + fecha + '</td>' +
                '</tr>';
            });
            tbody.innerHTML = html;
        })
        .catch(function(err){ console.error('Error dashboard:', err); });
}

cargar();
</script>
</body>
</html>