$('#register-form').submit(e => {
    e.preventDefault(); // Previene el envío por defecto del formulario.

    const user = {
        username: document.getElementById('username').value.trim(),
        email: document.getElementById('email').value.trim(),
        password: document.getElementById('password').value.trim(),
        confirm_password: document.getElementById('confirm_password').value.trim(),
        role: document.getElementById('role').value
    };

 
    if (user.username === '') {
        alert('El nombre de usuario es obligatorio.');
        return;
    }

    if (user.email === '') {
        alert('El correo electrónico es obligatorio.');
        return;
    }

    // Validar formato del correo electrónico.
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(user.email)) {
        alert('Por favor ingresa un correo electrónico válido.');
        return;
    }

    if (user.password === '') {
        alert('La contraseña es obligatoria.');
        return;
    }

    if (user.password.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres.');
        return;
    }

    if (user.password !== user.confirm_password) {
        alert('Las contraseñas no coinciden.');
        return;
    }

    if (user.role !== 'alumno' && user.role !== 'profesor') {
        alert('Por favor selecciona un rol válido.');
        return;
    }

    const url = user.role=== 'alumno'? '../controlador/estudiante/registrar-estudiante.php' : '../controlador/profesor/registrar-profesor.php';

    // Si todas las validaciones pasan, enviar el formulario por AJAX.
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            username: user.username,
            email: user.email,
            password: user.password,
        },
        success: function (response) {
            //manejar la respuesta del servidor.
            console.log(response);
            const msj = JSON.parse(response);
            alert(msj.message);
            //Redirigir a otra página.
            // window.location.href = 'dashboard.php';
        },
        error: function () {
            alert('Hubo un error al procesar el registro.');
        }
    });
});
