<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id_user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['log_error'] ?? '';
$_POST['correo'] = $_SESSION['log_correo'] ?? '';
unset($_SESSION['log_error'], $_SESSION['log_correo']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/login.css">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
<style>
/* ── Footer ── */
footer {
    background: #0a1628;
    border-top: 1px solid rgba(255,255,255,0.07);
    padding: 20px 40px;
}

.fet {
    max-width: 960px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}

.ft-left {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.ft-left a {
    color: #94a3b8;
    text-decoration: none;
    font-size: 0.78rem;
    font-family: 'Poppins', sans-serif;
    transition: color 0.15s;
}

.ft-left a:hover { color: #eab308; }

.ft-center p {
    margin: 0;
    font-size: 0.72rem;
    color: #475569;
    font-family: 'Poppins', sans-serif;
    text-align: center;
}

.ft-right {
    font-size: 0.78rem;
    color: #94a3b8;
    font-family: 'Poppins', sans-serif;
}

@media (max-width: 600px) {
    footer { padding: 20px 24px; }
    .fet { flex-direction: column; align-items: center; text-align: center; }
    .ft-left { justify-content: center; }
}
</style>
</head>
<body>

<div class="vt-screen">
    <div class="vt-bg"></div>
    <div class="vt-bg-overlay"></div>

    <div class="vt-card">
        <div class="vt-logo-ring">
            <img src="assets/logo/logovtt.jpg" alt="Villa Tecnia" class="vt-logo-img">
        </div>

        <h1 class="vt-brand">VILLA <span>TECNIA</span></h1>

        <div class="vt-divider"><span>Iniciar sesión</span></div>

        <form action="controllers/log_user.php" method="POST" class="vt-form" novalidate>
            <div class="vt-field">
                <label for="correo">Usuario</label>
                <div class="vt-input-wrap">
                    <svg class="vt-input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-10 5L2 7"></path></svg>
                    <input type="email" id="correo" name="correo" placeholder="Correo" autocomplete="email" value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
                </div>
            </div>

            <div class="vt-field">
                <label for="password">Contraseña</label>
                <div class="vt-input-wrap">
                    <svg class="vt-input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="m21 2-9.6 9.6"></path><path d="m15.5 7.5 3 3L22 7l-3-3"></path></svg>
                    <input type="password" id="password" name="password" placeholder="Contraseña" autocomplete="current-password">
                    <button type="button" class="vt-toggle-eye" onclick="vtTogglePassword()" aria-label="Mostrar contraseña">
                        <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>

            <?php if ($error): ?>
                <p class="vt-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <div class="vt-row">
                <label class="vt-checkbox">
                    <input type="checkbox" name="recordarme">
                    <span>Recordarme</span>
                </label>
                <a href="#" class="vt-forgot">¿Olvidaste tu contraseña?</a>
            </div>
            <button type="submit" class="vt-submit">Iniciar sesión</button>
        </form>
    </div>
</div>

<footer>
    <div class="fet">
        <div class="ft-left">
            <a href="about.html">Acerca de Nosotros</a>
            <a href="about.html#contacto">Contáctanos</a>
            <a href="about.html#contacto">Visítanos</a>
        </div>
        <div class="ft-center">
            <p>© 2025 Villa Tecnia. Todos los derechos reservados.</p>
        </div>
        <div class="ft-right">
            <a href="https://www.facebook.com/villa.tecnia.2025/photos?locale=es_LA" style="color:#94a3b8;text-decoration:none;font-size:0.78rem;display:flex;align-items:center;gap:6px;" onmouseover="this.style.color='#eab308'" onmouseout="this.style.color='#94a3b8'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                Facebook
            </a>
        </div>
    </div>
</footer>

<script src="config/login.js"></script>
</body>
</html>