<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1162cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        form {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.5s ease-out;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 28px;
            animation: slideIn 0.8s ease-out;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3184e4;
            box-shadow: 0 0 8px rgba(49, 132, 228, 0.5);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        .input-icon input {
            padding-left: 45px;
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #156ca7 0%, #2575fc 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }
        
        .forgot-password a {
            color: #2575fc;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .forgot-password a:hover {
            color: #1352c7;
            text-decoration: underline;
        }
        
        .error {
            background-color: #ffebee;
            color: #d32f2f;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #d32f2f;
            display: none;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(-30px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @media (max-width: 500px) {
            form {
                padding: 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .form-group input, .btn {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
   <form action="validar.php" method="post">
        <h1>Inicie Sesión</h1>
        
        <div id="errorMsg" class="error"></div>

        <div class="form-group">
            <label for="correo">Correo Electrónico:</label>
            <div class="input-icon">
                <i class="fas fa-envelope"></i>
                <input type="text" id="correo" placeholder="Ingrese su correo electrónico" name="correo" required>
            </div>
        </div>

        <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <div class="input-icon">
                <i class="fas fa-lock"></i>
                <input type="password" id="contrasena" placeholder="Ingrese su contraseña" name="contrasena" required>
            </div>
        </div>
        
        <button type="submit" class="btn">Ingresar</button>
        
        <div class="forgot-password">
            <a href="recuperar_contrasena.php">¿Olvidaste tu contraseña?</a>
        </div>
   </form> 
   
   <script>
        // Mostrar mensajes de error si existen en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        
        if (error) {
            document.getElementById('errorMsg').textContent = decodeURIComponent(error);
            document.getElementById('errorMsg').style.display = 'block';
        }
   </script>
</body>
</html>
