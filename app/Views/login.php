<!DOCTYPE html>

<?php 

helper(['form', 'url', 'session']);
/* Obtiene errores de validación guardados en flashdata */
$errors = session()->getFlashdata('errors') ?? [];

// Extraer errores específicos para evitar problemas de análisis estático
$dni_error = (string) ($errors['dni'] ?? '');
$password_error = (string) ($errors['password'] ?? '');
?>


<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Univia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('Public/css/login.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">

   

</head>
<body>

<div class="login-wrapper">

    <!-- Cabecera -->
    <div class="text-center mb-4">
        <div class="brand-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
            </svg>
        </div>
        <div class="brand-name">Univia</div>
        <div class="brand-sub mt-1">PLATAFORMA UNIVERSITARIA</div>
    </div>

    
    <div class="login-card">

        <p class="card-title">Bienvenido de nuevo</p>
        <p class="card-subtitle">Ingresá tus credenciales para continuar</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-4" role="alert" style="font-size:14px; border-radius:10px;">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        
        <?= form_open('auth/login', ['id' => 'loginForm']) ?>

            
            <div class="mb-3">
                <label for="dni" class="form-label">DNI</label>
                <input
                    type="number"
                    id="dni"
                    name="dni"
                    class="form-control <?= !empty($dni_error) ? 'is-invalid' : '' ?>"
                    placeholder="Ej: 40123456"
                    value="<?= set_value('dni') ?>"
                    min="1000000"
                    max="99999999"
                    autocomplete="username"
                    required
                >
                <?php if (!empty($dni_error)): ?>
                    <div class="invalid-feedback" style="font-size:13px;">
                        <?= esc($dni_error) ?>
                    </div>
                <?php endif; ?>
            </div>

       
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control <?= !empty($password_error) ? 'is-invalid' : '' ?>"
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
                    <?php if (!empty($password_error)): ?>
                        <div class="invalid-feedback" style="font-size:13px;">
                            <?= esc($password_error) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Boton de iniciar sesion -->
            <button type="submit" class="btn-login">
                Iniciar sesión
            </button>

        <?= form_close() ?>

        
        <div class="divider">¿No tenés cuenta?</div>

        <!-- Botón de Registro -->
         <div class="divider">
   <a href="<?= site_url('auth/registro') ?>" class="btn btn-nueva">Registrarme</a>
</div>
    </div>
    
    <p class="footer-note text-center mt-4">
        © <?= date('Y') ?> Univia &middot; Todos los derechos reservados
    </p>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('Public/js/login.js') ?>"></script>

</body>
</html>  