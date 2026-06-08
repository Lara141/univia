

function actualizarCarreras() {
  const uniSelect     = document.getElementById('universidad');
  const carreraSelect = document.getElementById('carrera');
  const idUniversidad = uniSelect.value;
  carreraSelect.innerHTML = '';
  carreraSelect.disabled = true;
 
  if (!idUniversidad) {
    carreraSelect.appendChild(new Option('Primero seleccioná una universidad', ''));
    return;
  }

  carreraSelect.innerHTML = '<option>Cargando carreras...</option>';

  fetch(`${site_url}/api/carreras/${idUniversidad}`)
    .then(response => response.json())
    .then(carreras => {
      carreraSelect.innerHTML = '<option value="">Seleccioná tu carrera</option>';
      carreras.forEach(c => {
        const opt = new Option(c.nombre_carrera, c.id_carrera);
        carreraSelect.appendChild(opt);
      });
      carreraSelect.disabled = false;
    })
    .catch(error => {
      console.error('Error al cargar carreras:', error);
      carreraSelect.innerHTML = '<option>Error al cargar</option>';
    });

  limpiarError('universidad');
}

function cargarUniversidades() {
    const uniSelect = document.getElementById('universidad');
    fetch(`${site_url}/api/universidades`)
        .then(response => response.json())
        .then(universidades => {
            universidades.forEach(u => {
                // Usamos el ID de la tabla como valor y el slug como data-attribute
                const opt = new Option(u.nombre_universidad, u.id_universidad);
                uniSelect.appendChild(opt);
            });
        });
}

window.addEventListener('DOMContentLoaded', () => {
  cargarUniversidades();
});


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


function togglePass(fieldId, btn) {
  const input  = document.getElementById(fieldId);
  const icon   = btn.querySelector('i');
  const esPass = input.type === 'password';
  input.type     = esPass ? 'text' : 'password';
  icon.className = esPass ? 'bi bi-eye-slash' : 'bi bi-eye';
}


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
    
    document.getElementById('btnTexto').classList.add('d-none');
    document.getElementById('btnSpinner').classList.remove('d-none');
    document.getElementById('btnRegistrar').disabled = true;

   
    setTimeout(() => {
        document.getElementById('formRegistro').submit();
    }, 1000);
  }
  
  document.getElementById('btnTexto').classList.add('d-none');
  document.getElementById('btnSpinner').classList.remove('d-none');
  document.getElementById('btnRegistrar').disabled = true;
});
