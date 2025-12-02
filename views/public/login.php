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
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }
        .login-title {
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
            margin-bottom: 20px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            box-shadow: none;
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
        }
        .btn-gold:hover {
            transform: scale(1.02);
            background: linear-gradient(45deg, #f2d06b, #d4af37);
        }
        .text-light-muted {
            color: rgba(255, 255, 255, 0.6);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 class="login-title">Atlantic City</h2>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center py-2">
                Credenciales incorrectas
            </div>
        <?php endif; ?>
        <form action="../../auth/login_process.php" method="POST">
            <div class="mb-3">
                <label class="text-light-muted mb-1">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" required placeholder="ejemplo@atlantic.com">
            </div>
            <div class="mb-3">
                <label class="text-light-muted mb-1">Contraseña</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <div class="mb-3">
                <label class="text-light-muted mb-1">Tipo de Usuario</label>
                <select name="tipo_usuario" class="form-control text-white">
                    <option value="cliente" class="text-dark">Cliente</option>
                    <option value="empleado" class="text-dark">Empleado</option>
                </select>
            </div>
            <button type="submit" class="btn btn-gold mb-3">Ingresar</button>
            <div class="text-center">
                <a href="register.php" class="text-light-muted text-decoration-none">¿No tienes cuenta? <span class="text-warning">Regístrate aquí</span></a>
            </div>
        </form>
    </div>
</body>
</html>
