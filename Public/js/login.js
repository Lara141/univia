
    (function () {
        var toggle  = document.getElementById('togglePassword');
        var pwInput = document.getElementById('password');
        var iconOpen   = toggle.querySelector('.icon-eye-open');
        var iconClosed = toggle.querySelector('.icon-eye-closed');

        toggle.addEventListener('click', function () {
             
            var isPassword = pwInput.type === 'password';

            pwInput.type = isPassword ? 'text' : 'password';

            iconOpen.style.display   = isPassword ? 'none'  : 'block';
            iconClosed.style.display = isPassword ? 'block' : 'none';

            toggle.setAttribute(
                'aria-label',
                isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'
            );
        });
    })();
