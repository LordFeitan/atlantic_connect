<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Atlantic City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle at center, #0a1f35 0%, #000000 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            overflow: hidden;
        }
        
        /* Optional: Add a subtle texture or overlay if desired, but keeping it clean as requested */
        
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        /* Decorative gold line at top */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .login-header h2 {
            color: #d4af37;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .login-header p {
            color: #aaa;
            font-weight: 300;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .form-control {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid #444;
            color: #fff;
            height: 50px;
            border-radius: 10px;
            padding-left: 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background: rgba(0, 0, 0, 0.6);
            border-color: #d4af37;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.15);
        }
        .form-label {
            color: #ccc;
            font-weight: 400;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .btn-gold {
            background: linear-gradient(135deg, #d4af37 0%, #b48f26 100%);
            border: none;
            color: #000;
            font-weight: 600;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: all 0.3s ease;
            margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #f2d06b 0%, #d4af37 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.5);
        }

        .user-type-selector {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 2rem;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .form-check {
            margin: 0;
            padding-left: 0;
            position: relative;
        }
        
        .form-check-input {
            display: none; /* Hide default radio */
        }
        
        .form-check-label {
            cursor: pointer;
            padding: 8px 20px;
            border-radius: 20px;
            color: #888;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .form-check-input:checked + .form-check-label {
            background-color: rgba(212, 175, 55, 0.2);
            color: #d4af37;
            border: 1px solid rgba(212, 175, 55, 0.5);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.3);
            color: #ff6b6b;
            border-radius: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h2>Atlantic City</h2>
            <p>Bienvenido a la Excelencia</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center mb-4" role="alert">
                Credenciales incorrectas
            </div>
        <?php endif; ?>

        <form action="../auth/login_process.php" method="POST">
            <div class="user-type-selector">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_usuario" id="cliente" value="cliente" checked>
                    <label class="form-check-label" for="cliente">Soy Cliente</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_usuario" id="empleado" value="empleado">
                    <label class="form-check-label" for="empleado">Soy Colaborador</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="ejemplo@correo.com">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-gold">Ingresar</button>
        </form>
    </div>
</body>
</html>
