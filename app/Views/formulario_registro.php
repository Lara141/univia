<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Univia — Registro</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= base_url('Public/css/formulario_registro.css') ?>">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Sora:wght@400;600;700&display=swap" rel="stylesheet"/>
</head>
<body>

<div class="deco-circle deco-circle-1"></div>
<div class="deco-circle deco-circle-2"></div>
<div class="deco-circle deco-circle-3"></div>

<div class="modal fade" id="modalExito" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content text-center p-4">
      <div class="modal-exito-icon">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <h5 class="fw-bold" style="color:var(--azul-oscuro); font-family:'Sora',sans-serif;">¡Registro exitoso!</h5>
      <p class="text-muted small">Tu cuenta en Univia fue creada. Ya podés explorar y compartir material académico.</p>
      <a href="<?= site_url('/') ?>" class="btn btn-registrar mt-2">Ir al inicio de sesión</a> </div>
  </div>
</div>

<div class="main-wrapper">

  <div class="brand-header">
    <div class="brand-logo-wrap">
      <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
      <span class="brand-nombre">Univia</span>
    </div>
    <p class="brand-tagline">Plataforma para compartir material académico universitario</p>
  </div>

  <div class="card-registro">
    <h1 class="card-titulo">Crear cuenta</h1>
    <p class="card-subtitulo">Completá tus datos para unirte a la comunidad.</p>

    <?php $errores = session()->getFlashdata('errores_registro'); ?>
<?php if ($errores): ?>
    <div class="alert alert-danger alert-ci mb-4" role="alert" style="background-color: var(--error-bg); border-color: var(--error-borde); color: var(--error);">
        <ul class="mb-0 ps-3">
            <?php foreach ($errores as $error): ?>
                <li><?= esc(is_array($error) ? implode(', ', $error) : (string) $error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

  <form id="formRegistro" method="POST" action="<?= site_url('auth/procesar_registro') ?>" novalidate>
     <div class="seccion-label"><i class="bi bi-person me-1"></i> Datos personales</div>

      <div class="row g-3">

        <div class="col-sm-6">
          <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
          <div class="input-group-custom">
            <i class="bi bi-person input-icon"></i>
            <input type="text" class="form-control" id="nombre" name="nombre" 
       placeholder="Ej: Lucía" 
       value="<?= old('nombre') ?>" autocomplete="given-name"/>
          </div>
        </div>

        <div class="col-sm-6">
          <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
          <div class="input-group-custom">
            <i class="bi bi-person input-icon"></i>
            <input type="text"
                   class="form-control" id="apellido" name="apellido"
                   placeholder="Ej: González"
                   value="<?= old('apellido') ?>" autocomplete="family-name"/>
          </div>
        </div>

        <div class="col-sm-6">
          <label for="dni" class="form-label">DNI <span class="text-danger">*</span></label>
          <div class="input-group-custom">
            <i class="bi bi-credit-card input-icon"></i>
            <input type="text"
                   class="form-control" id="dni" name="dni"
                   placeholder="8 dígitos exactos"
                   value="<?= old('dni') ?>" inputmode="numeric" maxlength="8" autocomplete="off"/>
          </div>
          <div class="dni-contador" id="dniContador">0 / 8 dígitos</div>
        </div>

        <div class="col-sm-6">
          <label for="correo" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
          <div class="input-group-custom">
            <i class="bi bi-envelope input-icon"></i>
            <input type="email"
                   class="form-control" id="correo" name="correo"
                   placeholder="nombre@universidad.edu"
                   value="<?= old('correo') ?>" autocomplete="email"/>
          </div>
        </div>

        <div class="col-sm-6">
          <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
          <div class="input-group-custom">
            <i class="bi bi-lock input-icon"></i>
            <input type="password"
                   class="form-control" id="password" name="password"
                   placeholder="Mínimo 8 caracteres"
                   autocomplete="new-password"/>
            <button type="button" class="btn-pass-toggle" onclick="togglePass('password', this)">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <div class="password-strength">
            <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
            <span class="strength-text" id="strengthText"></span>
          </div>
        </div>

        <div class="col-sm-6">
          <label for="password_confirm" class="form-label">Repetir contraseña <span class="text-danger">*</span></label>
          <div class="input-group-custom">
            <i class="bi bi-lock-fill input-icon"></i>
            <input type="password"
                   class="form-control" id="password_confirm" name="password_confirm"
                   placeholder="Repetí tu contraseña"
                   autocomplete="new-password"/>
            <button type="button" class="btn-pass-toggle" onclick="togglePass('password_confirm', this)">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

      </div><div class="seccion-label"><i class="bi bi-building me-1"></i> Información académica</div>

      <div class="row g-3">

        <div class="col-sm-6">
          <label for="universidad" class="form-label">Universidad <span class="text-danger">*</span></label>
          <div class="input-group-custom">
            <i class="bi bi-house-door input-icon"></i>
            <select class="form-select" id="universidad" name="universidad"
                    onchange="actualizarCarreras()">
              <option value="">Seleccioná tu universidad</option>
              
              <option value="uba">Universidad de Buenos Aires (UBA)</option>
              <option value="unc">Universidad Nacional de Córdoba (UNC)</option>
              <option value="unl">Universidad Nacional del Litoral (UNL)</option>
              <option value="unlp">Universidad Nacional de La Plata (UNLP)</option>
              <option value="unne">Universidad Nacional del Nordeste (UNNE)</option>
              <option value="utn">Universidad Tecnológica Nacional (UTN)</option>
              <option value="udesa">Universidad de San Andrés</option>
              <option value="austral">Universidad Austral</option>
              <option value="usal">Universidad del Salvador</option>
              <option value="otra">Otra universidad</option>
              
              </select>
          </div>
        </div>

        <div class="col-sm-6">
          <label for="carrera" class="form-label">Carrera <span class="text-danger">*</span></label>
          <div class="input-group-custom">
            <i class="bi bi-book input-icon"></i>
            <select class="form-select" id="carrera" name="carrera" disabled>
              <option value="">Primero seleccioná una universidad</option>
            </select>
          </div>
        </div>

      </div><div class="mt-4">
        <button type="submit" class="btn-registrar" id="btnRegistrar">
          <span id="btnTexto"><i class="bi bi-person-plus me-2"></i>Registrarme</span>
          <span id="btnSpinner" class="d-none">
            <span class="spinner-border" role="status"></span> Procesando...
          </span>
        </button>
      </div>

      

    </form>
  </div>
 <p class="login-link mt-3">¿Ya tenés cuenta?  <a href="<?= site_url() ?>">Iniciá sesión</a></p>
 </div>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const site_url = "<?= site_url() ?>";
</script>
<script src="<?= base_url('Public/js/formulario_registro.js') ?>"></script>
</body>
</html>  