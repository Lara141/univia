<!DOCTYPE html>

<?php 
/*
   - form: facilita la creación de formularios
   - url: manejo de rutas (site_url, site_url)
   - session: acceso a datos de sesión (errores, mensajes)
 */ 
helper(['form', 'url', 'session']);
/* Obtiene errores de validación guardados en flashdata */
$errors = session()->getFlashdata('errors') ?? [];
?>

<html lang="es">
<head>
    <meta charset="UTF-8">

    <!-- Configuración responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Título de la página 
    Framework CSS para estilos y componentes-->
    <title>Iniciar Sesión — Univia</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts
    - Playfair: títulos
    - Source Sans: texto general-->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Variables  CSS (paleta de colores y estilos) */
        :root {
            --navy:      #0f2244;
            --blue:      #2563eb;
            --blue-dark: #1e40af;
            --blue-soft: #eff6ff;
            --text-main: #0f2244;
            --text-muted:#64748b;
            --border:    #e2e8f0;
            --bg-input:  #f8fafc;
            --radius-card: 18px;
            --radius-input: 10px;
        }

        /* ─── Layout general 
         - Fondo oscuro con textura
         - Centrado vertical y horizontal */
        body {
            font-family: 'Source Sans 3', sans-serif;
            min-height: 100vh;
            background-color: var(--navy);
            /* Rejilla geométrica sutil como textura de fondo */
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 40px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* ─── Wrapper centrado  */
         /* Contenedor principal centrado */
        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        /* Cabecera con logo */
        .brand-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--blue), var(--blue-dark));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 4px 20px rgba(37,99,235,.4);
        }
        .brand-icon svg {
            width: 26px;
            height: 26px;
            fill: #fff;
        }
        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: #fff;
            letter-spacing: .3px;
        }
        .brand-sub {
            font-size: 12px;
            color: rgba(255,255,255,.5);
            letter-spacing: .8px;
        }

        /* Tarjeta principal */
        .login-card {
            background: #fff;
            border-radius: var(--radius-card);
            padding: 36px 32px 28px;
            box-shadow: 0 24px 60px rgba(0,0,0,.35);
        }

        /* Inputs, labels y botones ya estilizados */
        .login-card .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--text-main);
            margin-bottom: 4px;
        }
        .login-card .card-subtitle {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        /* ─── Labels ─────────────────────────────────────────── */
        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        /* ─── Inputs ─────────────────────────────────────────── */
        .form-control {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-input);
            background: var(--bg-input);
            font-family: 'Source Sans 3', sans-serif;
            font-size: 15px;
            color: var(--text-main);
            padding: 11px 14px;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .form-control:focus {
            border-color: var(--blue);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }
        .form-control::placeholder { color: #94a3b8; }

        /* Quitar flechas del input type=number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
        input[type=number] { appearance: textfield; -moz-appearance: textfield; }

        /* ─── Campo de contraseña con toggle ─────────────────── */
        .password-wrapper {
            position: relative;
        }
        .password-wrapper .form-control {
            padding-right: 44px;
        }
        .btn-toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: #94a3b8;
            transition: color .2s;
            line-height: 0;
        }
        .btn-toggle-pw:hover { color: var(--blue); }
        .btn-toggle-pw svg { width: 18px; height: 18px; display: block; }
        .icon-eye-closed { display: none; }

        /* ─── Botón principal ────────────────────────────────── */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--blue), var(--blue-dark));
            color: #fff;
            border: none;
            border-radius: var(--radius-input);
            font-family: 'Source Sans 3', sans-serif;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: .4px;
            cursor: pointer;
            margin-top: 8px;
            transition: opacity .2s, transform .1s, box-shadow .2s;
            box-shadow: 0 4px 14px rgba(37,99,235,.35);
        }
        .btn-login:hover {
            opacity: .91;
            box-shadow: 0 6px 20px rgba(37,99,235,.45);
        }
        .btn-login:active { transform: scale(.98); }

        /* ─── Divisor ────────────────────────────────────────── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0;
            color: #cbd5e1;
            font-size: 12px;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ─── Botón secundario (Registro) ────────────────────── */
        .btn-register {
            display: block;
            width: 100%;
            padding: 12px;
            background: transparent;
            color: var(--blue);
            border: 1.5px solid var(--blue);
            border-radius: var(--radius-input);
            font-family: 'Source Sans 3', sans-serif;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background .2s, color .2s;
        }
        .btn-register:hover {
            background: var(--blue-soft);
            color: var(--blue-dark);
        }

        /* ─── Pie de página ──────────────────────────────────── */
        .footer-note {
            font-size: 12px;
            color: rgba(255,255,255,.3);
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <!-- Cabecera / Branding -->
    <div class="text-center mb-4">
        <div class="brand-icon">
            <!-- Ícono de libro/graduación -->
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
            </svg>
        </div>
        <!-- Nombre del sistema -->
        <div class="brand-name">Univia</div>
        <div class="brand-sub mt-1">PLATAFORMA UNIVERSITARIA</div>
    </div>

    <!-- Tarjeta de Login -->
    <div class="login-card">

        <p class="card-title">Bienvenido de nuevo</p>
        <p class="card-subtitle">Ingresá tus credenciales para continuar</p>

        <!-- Mensajes de error de CodeIgniter (session flash) -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-4" role="alert" style="font-size:14px; border-radius:10px;">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulario — action apunta al controlador de CodeIgniter -->
        <?= form_open('auth/login', ['id' => 'loginForm']) ?>

            <!-- Campo DNI -->
            <div class="mb-3">
                <label for="dni" class="form-label">DNI</label>
                <input
                    type="number"
                    id="dni"
                    name="dni"
                    class="form-control <?= isset($errors['dni']) ? 'is-invalid' : '' ?>"
                    placeholder="Ej: 40123456"
                    value="<?= set_value('dni') ?>"
                    min="1000000"
                    max="99999999"
                    autocomplete="username"
                    required
                >
                <!-- Error específico -->
                <?php if (isset($errors['dni'])): ?>
                    <div class="invalid-feedback" style="font-size:13px;">
                        <?= esc($errors['dni']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Campo Contraseña con toggle -->
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                        placeholder="Tu contraseña"
                        autocomplete="current-password"
                        required
                    >
                    <!-- Botón de mostrar/ocultar contraseña -->
                    <button
                        type="button"
                        class="btn-toggle-pw"
                        id="togglePassword"
                        aria-label="Mostrar u ocultar contraseña"
                    >
                        <!-- Ícono: ojo abierto -->
                        <svg class="icon-eye-open" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <!-- Ícono: ojo cerrado -->
                        <svg class="icon-eye-closed" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback" style="font-size:13px;">
                            <?= esc($errors['password']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Botón principal -->
            <button type="submit" class="btn-login">
                Iniciar sesión
            </button>

        <?= form_close() ?>

        <!-- Divisor -->
        <div class="divider">¿No tenés cuenta?</div>

        <!-- Botón de Registro -->
         <div class="divider">
   <a href="<?= site_url('inicio/registro') ?>" class="btn btn-nueva">Registrarme</a>
</div>
    </div><!-- /login-card -->

    <!-- Pie -->
    <p class="footer-note text-center mt-4">
        © <?= date('Y') ?> Univia &middot; Todos los derechos reservados
    </p>

</div><!-- /login-wrapper -->


<!-- Bootstrap 5 JS (para dismiss de alertas) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Toggle mostrar/ocultar contraseña — JavaScript puro -->
<script>
    (function () {
        var toggle  = document.getElementById('togglePassword');
        var pwInput = document.getElementById('password');
        var iconOpen   = toggle.querySelector('.icon-eye-open');
        var iconClosed = toggle.querySelector('.icon-eye-closed');

        toggle.addEventListener('click', function () {
            // Cambia entre password y texto
            var isPassword = pwInput.type === 'password';

            // Alternar tipo de input
            pwInput.type = isPassword ? 'text' : 'password';

            // Alternar íconos
            iconOpen.style.display   = isPassword ? 'none'  : 'block';
            iconClosed.style.display = isPassword ? 'block' : 'none';

            // Accesibilidad: actualizar aria-label
            toggle.setAttribute(
                'aria-label',
                isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'
            );
        });
    })();
</script>

</body>
</html>