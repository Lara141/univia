<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Univia — Registro</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Sora:wght@400;600;700&display=swap" rel="stylesheet"/>

  <style>
    :root {
      --azul-oscuro:  #0d1b3e;
      --azul-medio:   #1a3a6e;
      --azul-vivo:    #1e5fd4;
      --azul-claro:   #e8f0fe;
      --acento:       #4a9eff;
      --blanco:       #ffffff;
      --gris-texto:   #6b7a99;
      --borde:        #d0d9f0;
      /* ── Colores de error: naranja/ámbar, NO rojo ── */
      --error:        #c46a00;
      --error-bg:     #fff7ed;
      --error-borde:  #f5a623;
      /* ── Color de campo válido ── */
      --exito:        #1a7f4b;
      --exito-bg:     #f0faf5;
      --exito-borde:  #34c479;
    }

    * { box-sizing: border-box; }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, var(--azul-oscuro) 0%, var(--azul-medio) 50%, #153d7a 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 16px;
      position: relative;
      overflow-x: hidden;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background:
        radial-gradient(ellipse 60% 40% at 80% 20%, rgba(74,158,255,0.18) 0%, transparent 60%),
        radial-gradient(ellipse 40% 50% at 10% 80%, rgba(30,95,212,0.20) 0%, transparent 60%);
      pointer-events: none;
      z-index: 0;
    }

    .deco-circle { position: fixed; border-radius: 50%; pointer-events: none; z-index: 0; }
    .deco-circle-1 { width: 350px; height: 350px; border: 1px solid rgba(74,158,255,0.12); top: -80px; right: -80px; }
    .deco-circle-2 { width: 200px; height: 200px; border: 1px solid rgba(74,158,255,0.10); bottom: 60px; left: -60px; }
    .deco-circle-3 { width: 120px; height: 120px; background: rgba(74,158,255,0.06); bottom: 120px; right: 80px; }

    /* ── Wrapper ── */
    .main-wrapper {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 620px;
      animation: fadeUp 0.65s cubic-bezier(0.16,1,0.3,1);
    }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(30px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Marca ── */
    .brand-header { text-align: center; margin-bottom: 28px; }
    .brand-logo-wrap { display: inline-flex; align-items: center; gap: 12px; margin-bottom: 6px; }
    .brand-icon {
      width: 48px; height: 48px;
      background: linear-gradient(135deg, var(--azul-vivo), var(--acento));
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 4px 20px rgba(30,95,212,0.4);
    }
    .brand-icon i { font-size: 1.5rem; color: white; }
    .brand-nombre {
      font-family: 'Sora', sans-serif;
      font-size: 2.2rem; font-weight: 700;
      color: var(--blanco); letter-spacing: -0.5px;
    }
    .brand-tagline { font-size: 0.875rem; color: rgba(255,255,255,0.6); font-weight: 300; letter-spacing: 0.04em; }

    /* ── Card ── */
    .card-registro {
      background: rgba(255,255,255,0.97);
      border-radius: 24px;
      padding: 44px 48px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05), 0 20px 60px rgba(0,0,0,0.25), 0 0 0 1px rgba(255,255,255,0.1);
      backdrop-filter: blur(10px);
    }
    .card-titulo { font-family: 'Sora', sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--azul-oscuro); margin-bottom: 4px; }
    .card-subtitulo { font-size: 0.875rem; color: var(--gris-texto); margin-bottom: 28px; }

    /* ── Separador de sección ── */
    .seccion-label {
      font-size: 0.72rem; font-weight: 700; color: var(--azul-vivo);
      text-transform: uppercase; letter-spacing: 0.1em;
      display: flex; align-items: center; gap: 10px;
      margin: 18px 0 16px;
    }
    .seccion-label::after { content: ''; flex: 1; height: 1.5px; background: linear-gradient(to right, var(--azul-claro), transparent); }

    /* ── Labels ── */
    .form-label { font-size: 0.8rem; font-weight: 600; color: #2c3e6b; margin-bottom: 6px; letter-spacing: 0.01em; }

    /* ── Inputs ── */
    .input-group-custom { position: relative; }

    .input-icon {
      position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
      color: var(--gris-texto); font-size: 0.95rem; z-index: 5;
      pointer-events: none; transition: color 0.2s;
    }
    .input-group-custom:focus-within .input-icon { color: var(--azul-vivo); }

    .form-control, .form-select {
      padding-left: 38px;
      border: 1.5px solid var(--borde);
      border-radius: 10px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 0.9rem; color: var(--azul-oscuro);
      background: #f8faff;
      transition: border-color 0.22s, box-shadow 0.22s, background 0.22s;
      height: 44px;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--azul-vivo);
      box-shadow: 0 0 0 3px rgba(30,95,212,0.12);
      background: #ffffff; outline: none;
    }

    /* ══════════════════════════════════════════════
       ESTADO INVÁLIDO — borde naranja + fondo cálido
       + ícono de advertencia + shake animation
    ══════════════════════════════════════════════ */
    .form-control.is-invalid,
    .form-select.is-invalid {
      border-color: var(--error-borde) !important;
      background: var(--error-bg) !important;
      background-image: none !important;
      box-shadow: 0 0 0 3px rgba(245,166,35,0.18) !important;
      animation: shake 0.38s cubic-bezier(.36,.07,.19,.97);
    }
    @keyframes shake {
      0%,100% { transform: translateX(0); }
      15%      { transform: translateX(-5px); }
      35%      { transform: translateX(5px); }
      55%      { transform: translateX(-4px); }
      75%      { transform: translateX(4px); }
      90%      { transform: translateX(-2px); }
    }

    /* Banderita de error dentro del campo */
    .form-control.is-invalid ~ .input-icon,
    .form-select.is-invalid ~ .input-icon {
      color: var(--error-borde);
    }

    /* ESTADO VÁLIDO — borde verde suave */
    .form-control.is-valid {
      border-color: var(--exito-borde) !important;
      background: var(--exito-bg) !important;
      background-image: none !important;
      box-shadow: 0 0 0 2px rgba(52,196,121,0.12) !important;
    }
    .form-select.is-valid {
      border-color: var(--exito-borde) !important;
      background-image: none !important;
    }

    /* Sobreescribir colores Bootstrap que pisan el rojo */
    .was-validated .form-control:invalid,
    .was-validated .form-select:invalid { border-color: var(--error-borde); }

    /* ── Toggle contraseña ── */
    .btn-pass-toggle {
      position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
      background: none; border: none; color: var(--gris-texto);
      cursor: pointer; padding: 4px 6px; z-index: 5; font-size: 1rem;
      transition: color 0.2s;
    }
    .btn-pass-toggle:hover { color: var(--azul-vivo); }

    /* ── Fuerza contraseña ── */
    .password-strength { margin-top: 6px; }
    .strength-bar { height: 4px; background: var(--borde); border-radius: 4px; overflow: hidden; margin-bottom: 3px; }
    .strength-fill { height: 100%; border-radius: 4px; transition: width 0.4s ease, background 0.4s; width: 0%; }
    .strength-text { font-size: 0.72rem; color: var(--gris-texto); }

    /* ── Botón submit ── */
    .btn-registrar {
      background: linear-gradient(135deg, var(--azul-vivo) 0%, var(--acento) 100%);
      border: none; border-radius: 12px; color: white;
      font-family: 'Sora', sans-serif; font-size: 1rem; font-weight: 600;
      height: 50px; width: 100%; cursor: pointer;
      transition: transform 0.15s, box-shadow 0.2s, opacity 0.2s;
      box-shadow: 0 4px 20px rgba(30,95,212,0.35);
      letter-spacing: 0.02em; position: relative; overflow: hidden;
    }
    .btn-registrar:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(30,95,212,0.45); }
    .btn-registrar:active { transform: scale(0.985); }
    .btn-registrar:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }
    .btn-registrar .spinner-border { width: 1.1rem; height: 1.1rem; border-width: 2px; vertical-align: middle; margin-right: 6px; }

    /* ── Alerta CI ── */
    .alert-ci { border-radius: 12px; font-size: 0.875rem; border: none; padding: 12px 16px; }

    /* ── Link login ── */
    .login-link { text-align: center; margin-top: 20px; font-size: 0.875rem; color: rgba(255,255,255,0.65); }
    .login-link a { color: var(--acento); text-decoration: none; font-weight: 600; }
    .login-link a:hover { text-decoration: underline; }

    /* ── Modal éxito ── */
    .modal-content { border-radius: 20px; border: none; }
    .modal-exito-icon {
      width: 72px; height: 72px;
      background: linear-gradient(135deg, #e8f4fd, var(--azul-claro));
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 16px;
    }
    .modal-exito-icon i { font-size: 2rem; color: var(--azul-vivo); }

    /* ── Contador DNI ── */
    .dni-contador {
      font-size: 0.72rem;
      color: var(--gris-texto);
      text-align: right;
      margin-top: 3px;
      transition: color 0.2s;
    }
    .dni-contador.ok  { color: var(--exito); font-weight: 600; }
    .dni-contador.mal { color: var(--error); }

    @media (max-width: 540px) {
      .card-registro { padding: 28px 20px; }
    }
  </style>
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
      <a href="#" class="btn btn-registrar mt-2">Ir al inicio de sesión</a> </div>
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
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

  <form id="formRegistro" method="POST" action="<?= site_url('inicio/procesar_registro') ?>" novalidate>
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
// ═══════════════════════════════════════════════════════
//  CARRERAS POR UNIVERSIDAD
// ═══════════════════════════════════════════════════════
const CARRERAS = {
  uba:     ['Abogacía','Administración','Arquitectura','Ciencias de la Comunicación','Ciencias Económicas','Diseño Gráfico','Farmacia','Ingeniería Civil','Ingeniería Industrial','Medicina','Psicología','Sociología'],
  unc:     ['Arquitectura','Bioquímica','Ciencias Económicas','Derecho','Filosofía','Ingeniería Civil','Ingeniería en Computación','Medicina','Odontología','Psicología'],
  unl:     ['Abogacía','Agronomía','Arquitectura','Bioquímica','Ciencias Económicas','Ingeniería Industrial','Medicina Veterinaria','Sistemas de Información'],
  unlp:    ['Arquitectura','Astronomía','Bellas Artes','Ciencias Naturales','Derecho','Informática','Ingeniería','Medicina','Odontología','Periodismo'],
  unne:    ['Abogacía','Agronomía','Arquitectura','Ciencias Económicas','Derecho','Ingeniería Electrica','Medicina','Odontología', 'Lisenciatura en Sistemas', 'otro'],
  utn:     ['Ingeniería Civil','Ingeniería Eléctrica','Ingeniería en Sistemas de Información','Ingeniería Industrial','Ingeniería Mecánica','Tecnicatura en Programación'],
  udesa:   ['Administración de Empresas','Ciencias Políticas','Comunicación','Economía','Educación','Historia'],
  austral: ['Administración','Comunicación','Derecho','Ingeniería Biomédica','Ingeniería Industrial','Medicina','Psicología'],
  usal:    ['Ciencias de la Comunicación','Derecho','Filosofía','Historia','Letras','Psicología','Relaciones Internacionales'],
  otra:    ['Administración','Arquitectura','Contabilidad','Derecho','Diseño','Economía','Educación','Ingeniería','Medicina','Otra carrera'],
};

const carreraPreseleccionada = ''; /* '' */

function actualizarCarreras() {
  const uniSelect     = document.getElementById('universidad');
  const carreraSelect = document.getElementById('carrera');
  const val           = uniSelect.value;
  carreraSelect.innerHTML = '';

  if (val && CARRERAS[val]) {
    carreraSelect.appendChild(new Option('Seleccioná tu carrera', ''));
    CARRERAS[val].forEach(c => {
      const slug = c.toLowerCase().replace(/\s+/g, '-');
      const opt  = new Option(c, slug);
      if (slug === carreraPreseleccionada) opt.selected = true;
      carreraSelect.appendChild(opt);
    });
    carreraSelect.disabled = false;
  } else {
    carreraSelect.appendChild(new Option('Primero seleccioná una universidad', ''));
    carreraSelect.disabled = true;
  }
  limpiarError('universidad');
}

window.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('universidad').value) actualizarCarreras();
});

// ═══════════════════════════════════════════════════════
//  DNI — solo números, máximo 8, contador en tiempo real
// ═══════════════════════════════════════════════════════
const dniInput     = document.getElementById('dni');
const dniContador  = document.getElementById('dniContador');

dniInput.addEventListener('input', function () {
  this.value = this.value.replace(/\D/g, '').slice(0, 8);
  const len  = this.value.length;

  dniContador.textContent = `${len} / 8 dígitos`;
  dniContador.className   = 'dni-contador';

  if (len === 8)      dniContador.classList.add('ok');
  else if (len > 0)   dniContador.classList.add('mal');

  if (this.classList.contains('is-invalid')) validarCampo('dni');
});

// ═══════════════════════════════════════════════════════
//  FUERZA DE CONTRASEÑA
// ═══════════════════════════════════════════════════════
document.getElementById('password').addEventListener('input', function () {
  const val  = this.value;
  const fill = document.getElementById('strengthFill');
  const text = document.getElementById('strengthText');
  let score  = 0;
  if (val.length >= 8)           score++;
  if (/[A-Z]/.test(val))         score++;
  if (/[0-9]/.test(val))         score++;
  if (/[^A-Za-z0-9]/.test(val))  score++;

  const niveles = [
    { pct: '0%',   color: '',        label: '' },
    { pct: '25%',  color: '#e07b00', label: 'Muy débil' },
    { pct: '50%',  color: '#f5a623', label: 'Débil' },
    { pct: '75%',  color: '#ffc107', label: 'Regular' },
    { pct: '100%', color: '#1a7f4b', label: 'Segura ✓' },
  ];
  fill.style.width      = niveles[score].pct;
  fill.style.background = niveles[score].color;
  text.textContent      = val.length ? niveles[score].label : '';
  text.style.color      = niveles[score].color;
});

// ═══════════════════════════════════════════════════════
//  TOGGLE VER/OCULTAR CONTRASEÑA
// ═══════════════════════════════════════════════════════
function togglePass(fieldId, btn) {
  const input  = document.getElementById(fieldId);
  const icon   = btn.querySelector('i');
  const esPass = input.type === 'password';
  input.type     = esPass ? 'text' : 'password';
  icon.className = esPass ? 'bi bi-eye-slash' : 'bi bi-eye';
}

// ═══════════════════════════════════════════════════════
//  HELPERS DE VALIDACIÓN
// ═══════════════════════════════════════════════════════
function marcarError(id, msg) {
  const el  = document.getElementById(id);
  const wrap = el.closest('.input-group-custom') || el.parentElement;
  const fb   = wrap.querySelector('.invalid-feedback span') || wrap.querySelector('.invalid-feedback');

  el.classList.add('is-invalid');
  el.classList.remove('is-valid');

  el.style.animation = 'none';
  el.offsetHeight; 
  el.style.animation = '';

  if (fb) fb.textContent = msg;
  return false;
}

function limpiarError(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('is-invalid');
}

function marcarValido(id) {
  const el = document.getElementById(id);
  el.classList.remove('is-invalid');
  el.classList.add('is-valid');
}

// ═══════════════════════════════════════════════════════
//  VALIDACIÓN POR CAMPO
// ═══════════════════════════════════════════════════════
const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

function validarCampo(id) {
  const el  = document.getElementById(id);
  const val = el.value.trim();

  if (id === 'nombre' || id === 'apellido') {
    if (!val) return marcarError(id, 'Este campo es obligatorio.');
    marcarValido(id);
  }

  if (id === 'dni') {
    if (!val)              return marcarError(id, 'El DNI es obligatorio.');
    if (!/^\d{8}$/.test(val)) return marcarError(id, 'El DNI debe tener exactamente 8 dígitos.');
    marcarValido(id);
  }

  if (id === 'correo') {
    if (!val)               return marcarError(id, 'El correo es obligatorio.');
    if (!emailRe.test(val)) return marcarError(id, 'Ingresá un correo válido (ej: nombre@mail.com).');
    marcarValido(id);
  }

  if (id === 'password') {
    if (!val)           return marcarError(id, 'La contraseña es obligatoria.');
    if (val.length < 8) return marcarError(id, 'Debe tener al menos 8 caracteres.');
    marcarValido(id);
  }

  if (id === 'password_confirm') {
    const pass = document.getElementById('password').value;
    if (!val)         return marcarError(id, 'Repetí tu contraseña.');
    if (val !== pass) return marcarError(id, 'Las contraseñas no coinciden.');
    marcarValido(id);
  }
  return true;
}

['nombre','apellido','dni','correo','password','password_confirm'].forEach(id => {
  const el = document.getElementById(id);
  if (!el) return;
  el.addEventListener('blur',  () => validarCampo(id));
  el.addEventListener('input', () => { if (el.classList.contains('is-invalid')) validarCampo(id); });
});

['universidad','carrera'].forEach(id => {
  const el = document.getElementById(id);
  if (!el) return;
  el.addEventListener('change', () => {
    if (el.value) marcarValido(id);
  });
});

// ═══════════════════════════════════════════════════════
//  SUBMIT CON VALIDACIÓN COMPLETA
// ═══════════════════════════════════════════════════════
document.getElementById('formRegistro').addEventListener('submit', function (e) {
  const campos = ['nombre','apellido','dni','correo','password','password_confirm'];
  let valido   = true;

  campos.forEach(id => { if (!validarCampo(id)) valido = false; });

  const uni     = document.getElementById('universidad');
  const carrera = document.getElementById('carrera');

  if (!uni.value) {
    marcarError('universidad', 'Seleccioná tu universidad.');
    valido = false;
  } else { limpiarError('universidad'); }

  if (!carrera.value) {
    marcarError('carrera', 'Seleccioná tu carrera.');
    valido = false;
  } else { limpiarError('carrera'); }

  if (!valido) {
    e.preventDefault();
    const primerError = document.querySelector('.is-invalid');
    if (primerError) primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  if (valido) {
    // Mostrar el spinner visual
    document.getElementById('btnTexto').classList.add('d-none');
    document.getElementById('btnSpinner').classList.remove('d-none');
    document.getElementById('btnRegistrar').disabled = true;

    // Esperar un segundo para que se vea la animación y luego enviar el formulario a PHP
    setTimeout(() => {
        document.getElementById('formRegistro').submit();
    }, 1000);
  }
  // Mostrar spinner
  document.getElementById('btnTexto').classList.add('d-none');
  document.getElementById('btnSpinner').classList.remove('d-none');
  document.getElementById('btnRegistrar').disabled = true;
});

// Mostrar modal si CI devolvió éxito 
/*
  if (isset($exito)):
    const modalExito = new bootstrap.Modal(document.getElementById('modalExito'));
    modalExito.show();
  endif; 
*/
</script>
</body>
</html>