<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['id_rol'] != 1) {
    header('Location: dashboard.php');
    exit;
}

$error   = $_SESSION['reg_error']   ?? '';
$success = $_SESSION['reg_success'] ?? false;
unset($_SESSION['reg_error'], $_SESSION['reg_success']);

$nombre_repob = $_SESSION['reg_nombre'] ?? '';
$correo_repob = $_SESSION['reg_correo'] ?? '';
unset($_SESSION['reg_nombre'], $_SESSION['reg_correo']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Villa Tecnia | Nuevo usuario</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/login.css">
<link rel="stylesheet" href="css/register.css">
<link rel="icon" type="image/png" href="assets/logo/logovttr.png">
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
        <p class="vt-tagline">Panel administrativo</p>

        <div class="vt-divider"><span>Nuevo usuario</span></div>

        <?php if ($success): ?>
        <div class="vt-success-box">
            <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="m9 12 2 2 4-4"></path>
            </svg>
            <h2>¡Usuario creado!</h2>
            <p>La cuenta fue creada correctamente.</p>
            <div style="display:flex;gap:10px;margin-top:1rem;justify-content:center;">
                <a href="register.php" class="vt-submit" style="display:inline-block;text-align:center;text-decoration:none;">
                    + Otro usuario
                </a>
                <a href="dashboard.php" class="vt-submit" style="display:inline-block;text-align:center;text-decoration:none;background:rgba(255,255,255,0.1);">
                    Volver al dashboard
                </a>
            </div>
        </div>
        <?php else: ?>
        <form action="controllers/reg_user.php" method="POST" class="vt-form" novalidate>

            <div class="vt-field">
                <label for="nombre">Nombre completo</label>
                <div class="vt-input-wrap">
                    <svg class="vt-input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <input type="text" id="nombre" name="nombre" placeholder="Nombre y apellido" autocomplete="name" value="<?= htmlspecialchars($nombre_repob) ?>">
                </div>
            </div>

            <div class="vt-field">
                <label for="correo">Correo</label>
                <div class="vt-input-wrap">
                    <svg class="vt-input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-10 5L2 7"></path></svg>
                    <input type="email" id="correo" name="correo" placeholder="Correo" autocomplete="email" value="<?= htmlspecialchars($correo_repob) ?>">
                </div>
            </div>

            <div class="vt-field">
                <label for="password">Contraseña</label>
                <div class="vt-input-wrap">
                    <svg class="vt-input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="m21 2-9.6 9.6"></path><path d="m15.5 7.5 3 3L22 7l-3-3"></path></svg>
                    <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                    <button type="button" class="vt-toggle-eye" onclick="vtTogglePassword('password', this)" aria-label="Mostrar contraseña">
                        <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>

            <div class="vt-field">
                <label for="confirm_password">Confirmar contraseña</label>
                <div class="vt-input-wrap">
                    <svg class="vt-input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="m21 2-9.6 9.6"></path><path d="m15.5 7.5 3 3L22 7l-3-3"></path></svg>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite la contraseña" autocomplete="new-password">
                    <button type="button" class="vt-toggle-eye" onclick="vtTogglePassword('confirm_password', this)" aria-label="Mostrar contraseña">
                        <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>

            <div class="vt-field">
                <label for="id_rol">Rol</label>
                <div class="vt-input-wrap">
                    <svg class="vt-input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <select id="id_rol" name="id_rol" style="width:100%;background:transparent;border:none;outline:none;font-family:inherit;font-size:0.9rem;color:inherit;padding:0 0 0 8px;cursor:pointer;">
                        <option value="2">Empleado</option>
                        <option value="1">Administrador</option>
                    </select>
                </div>
            </div>

            <?php if ($error): ?>
                <p class="vt-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <button type="submit" class="vt-submit">Crear usuario</button>

            <p class="vt-switch"><a href="dashboard.php">← Volver al dashboard</a></p>
        </form>
        <?php endif; ?>
    </div>
</div>

<script src="config/register.js"></script>
</body>
</html>