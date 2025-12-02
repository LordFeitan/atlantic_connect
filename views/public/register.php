<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Atlantic City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }
        .register-title {
            color: #d4af37;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #fff;
            padding: 12px;
            margin-bottom: 5px; /* Reduced for validation msg space */
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            box-shadow: none;
        }
        .form-control:disabled {
            background: rgba(255, 255, 255, 0.05);
            color: #aaa;
        }
        .btn-gold {
            background: linear-gradient(45deg, #d4af37, #f2d06b);
            border: none;
            color: #000;
            font-weight: 600;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            transition: transform 0.2s;
            margin-top: 20px;
        }
        .btn-gold:hover {
            transform: scale(1.02);
            background: linear-gradient(45deg, #f2d06b, #d4af37);
        }
        .text-light-muted {
            color: rgba(255, 255, 255, 0.6);
        }
        .form-label {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }
        /* Validation Styles */
        .is-invalid {
            border: 1px solid #dc3545 !important;
        }
        .is-valid {
            border: 1px solid #198754 !important;
        }
        .invalid-feedback {
            font-size: 0.85rem;
            margin-bottom: 10px;
        }
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <h2 class="register-title">Registro de Cliente</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center py-2">
                <?php 
                if($_GET['error'] == 'dni_exists') echo 'El DNI ya está registrado.';
                elseif($_GET['error'] == 'email_exists') echo 'El correo ya está registrado.';
                else echo 'Error al registrar. Intente nuevamente.';
                ?>
            </div>
        <?php endif; ?>

        <form action="../../auth/register_process.php" method="POST" id="registerForm" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">DNI</label>
                    <div class="input-group">
                        <input type="text" name="dni" id="dni" class="form-control" required maxlength="8" pattern="\d{8}" placeholder="8 dígitos">
                        <span class="input-group-text bg-transparent border-0" id="dniSpinner" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                        </span>
                    </div>
                    <div class="invalid-feedback" id="dniFeedback">DNI inválido o ya registrado.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" required>
                    <div class="invalid-feedback">Debe ser mayor de 18 años.</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo letras">
                <div class="invalid-feedback">Ingrese un nombre válido (solo letras).</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control" placeholder="Av. Principal 123">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" maxlength="9" pattern="\d{9}" placeholder="9 dígitos">
                    <div class="invalid-feedback">Teléfono debe tener 9 dígitos.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" required placeholder="ejemplo@email.com">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input type="password" id="confirm_password" class="form-control" required placeholder="Repetir contraseña">
                    <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                </div>
            </div>

            <button type="submit" class="btn btn-gold">Registrarse</button>
            <div class="text-center mt-3">
                <a href="login.php" class="text-light-muted text-decoration-none">¿Ya tienes cuenta? <span class="text-warning">Inicia Sesión</span></a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const dobInput = document.getElementById('fecha_nacimiento');
            const dniInput = document.getElementById('dni');
            const telefonoInput = document.getElementById('telefono');
            const nombreInput = document.getElementById('nombre');
            const dniSpinner = document.getElementById('dniSpinner');
            const dniFeedback = document.getElementById('dniFeedback');

            // 1. Input Restrictions (Numbers only)
            ['dni', 'telefono'].forEach(id => {
                document.getElementById(id).addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });

            // 2. Real-time DNI Validation
            dniInput.addEventListener('input', function() {
                if (this.value.length === 8) {
                    validateDNI(this.value);
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                    enableFields(true); // Ensure fields are enabled if DNI is incomplete
                }
            });

            function validateDNI(dni) {
                // Show spinner, disable fields
                dniSpinner.style.display = 'block';
                enableFields(false);

                const formData = new FormData();
                formData.append('field', 'dni');
                formData.append('value', dni);

                fetch('../../auth/check_duplicate.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    dniSpinner.style.display = 'none';
                    if (data.exists) {
                        dniInput.classList.add('is-invalid');
                        dniInput.classList.remove('is-valid');
                        dniFeedback.textContent = 'Este DNI ya está registrado en el sistema.';
                        enableFields(false); // Keep disabled if invalid
                    } else {
                        dniInput.classList.remove('is-invalid');
                        dniInput.classList.add('is-valid');
                        enableFields(true); // Enable if valid
                    }
                })
                .catch(err => {
                    console.error(err);
                    dniSpinner.style.display = 'none';
                    enableFields(true); // Fallback
                });
            }

            function enableFields(enable) {
                const fields = form.querySelectorAll('input:not(#dni), button[type="submit"]');
                fields.forEach(f => f.disabled = !enable);
            }

            // 3. Form Submission Validation
            form.addEventListener('submit', function(event) {
                let isValid = true;

                // Password Match
                if (password.value !== confirmPassword.value) {
                    confirmPassword.classList.add('is-invalid');
                    isValid = false;
                } else {
                    confirmPassword.classList.remove('is-invalid');
                }

                // Age Validation (18+)
                if (dobInput.value) {
                    const dob = new Date(dobInput.value);
                    const today = new Date();
                    let age = today.getFullYear() - dob.getFullYear();
                    const m = today.getMonth() - dob.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                        age--;
                    }
                    if (age < 18) {
                        dobInput.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        dobInput.classList.remove('is-invalid');
                    }
                }

                // DNI Length
                if (dniInput.value.length !== 8) {
                    dniInput.classList.add('is-invalid');
                    dniFeedback.textContent = 'DNI debe tener 8 dígitos.';
                    isValid = false;
                }

                if (!form.checkValidity() || !isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            });
        });
    </script>
</body>
</html>
