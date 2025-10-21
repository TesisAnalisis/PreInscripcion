<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos de Uso - Facultad de Ciencias Aplicadas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --success-color: #27ae60;
            --border-radius: 10px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
        }

        header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px 0;
            text-align: center;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,192C672,181,768,139,864,138.7C960,139,1056,181,1152,197.3C1248,213,1344,203,1392,197.3L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: center;
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            position: relative;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        header p {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }

        .logo {
            font-size: 2rem;
            margin-bottom: 15px;
            position: relative;
        }

        .terms-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            margin-bottom: 30px;
            transition: var(--transition);
        }

        .terms-container:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .terms-header {
            border-bottom: 2px solid var(--light-color);
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .terms-header h2 {
            color: var(--primary-color);
            font-size: 1.8rem;
        }

        .last-updated {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .terms-content {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 15px;
        }

        .terms-section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: var(--border-radius);
            background-color: #f9f9f9;
            transition: var(--transition);
        }

        .terms-section:hover {
            background-color: #f0f7ff;
            transform: translateY(-3px);
        }

        .terms-section h3 {
            color: var(--secondary-color);
            margin-bottom: 15px;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
        }

        .terms-section h3 i {
            margin-right: 12px;
            font-size: 1.3rem;
            background: var(--secondary-color);
            color: white;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .terms-section p {
            margin-bottom: 12px;
            text-align: justify;
        }

        .terms-section ul {
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .terms-section li {
            margin-bottom: 10px;
            position: relative;
            padding-left: 10px;
        }

        .terms-section li::before {
            content: "•";
            color: var(--secondary-color);
            font-weight: bold;
            position: absolute;
            left: -10px;
        }

        .highlight {
            background-color: rgba(52, 152, 219, 0.1);
            padding: 20px;
            border-left: 4px solid var(--secondary-color);
            margin: 20px 0;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
        }

        .acceptance-section {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            position: sticky;
            bottom: 20px;
            border-top: 3px solid var(--secondary-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: flex;
            align-items: flex-start;
            cursor: pointer;
            font-weight: 500;
            padding: 15px;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .form-group label:hover {
            background-color: #f0f7ff;
        }

        .form-group input[type="checkbox"] {
            margin-right: 15px;
            margin-top: 3px;
            transform: scale(1.3);
            accent-color: var(--secondary-color);
        }

        .form-group a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            border-bottom: 1px dotted var(--secondary-color);
        }

        .form-group a:hover {
            color: var(--primary-color);
            border-bottom: 1px solid var(--primary-color);
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, var(--secondary-color), #2980b9);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: var(--transition);
            text-align: center;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            background: linear-gradient(135deg, #2980b9, var(--secondary-color));
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #7f8c8d;
            font-size: 0.9rem;
            background-color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Scrollbar personalizado */
        .terms-content::-webkit-scrollbar {
            width: 8px;
        }

        .terms-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .terms-content::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 10px;
        }

        .terms-content::-webkit-scrollbar-thumb:hover {
            background: #2980b9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header h1 {
                font-size: 2rem;
            }
            
            .terms-container {
                padding: 20px;
            }
            
            .terms-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .terms-header h2 {
                font-size: 1.5rem;
                margin-bottom: 10px;
            }
            
            .terms-section h3 {
                font-size: 1.2rem;
            }
            
            .terms-section h3 i {
                width: 35px;
                height: 35px;
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
            
            header {
                padding: 20px 0;
            }
            
            header h1 {
                font-size: 1.7rem;
            }
            
            .terms-container {
                padding: 15px;
            }
            
            .terms-section {
                padding: 15px;
            }
            
            .btn {
                padding: 12px 20px;
                font-size: 1rem;
            }
            
            .form-group label {
                padding: 10px;
            }
        }

        /* Animaciones adicionales */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .terms-section {
            animation: fadeIn 0.5s ease forwards;
        }

        .terms-section:nth-child(1) { animation-delay: 0.1s; }
        .terms-section:nth-child(2) { animation-delay: 0.2s; }
        .terms-section:nth-child(3) { animation-delay: 0.3s; }
        .terms-section:nth-child(4) { animation-delay: 0.4s; }
        .terms-section:nth-child(5) { animation-delay: 0.5s; }
        .terms-section:nth-child(6) { animation-delay: 0.6s; }
        .terms-section:nth-child(7) { animation-delay: 0.7s; }

        /* Indicador de progreso de lectura */
        .progress-container {
            width: 100%;
            height: 5px;
            background: #e0e0e0;
            position: sticky;
            top: 0;
            z-index: 10;
            border-radius: 0 0 5px 5px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
            width: 0%;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="progress-container">
        <div class="progress-bar" id="progressBar"></div>
    </div>

    <div class="container">
        <header>
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>Facultad de Ciencias Aplicadas</h1>
            <p>Términos de Uso y Condiciones del Sistema de Inscripción</p>
        </header>

        <div class="terms-container">
            <div class="terms-header">
                <h2>Declaraciones y Términos de Uso</h2>
                <div class="last-updated">Actualizado: 01 de Noviembre, 2025</div>
            </div>
            
            <div class="terms-content" id="termsContent">
                <div class="terms-section">
                    <h3><i class="fas fa-copyright"></i> Propiedad Intelectual</h3>
                    <p>Todo el contenido, diseño, gráficos, compilaciones y otros elementos del sistema están protegidos por las leyes de derechos de autor y propiedad intelectual. La Facultad de Ciencias Aplicadas es propietaria o tiene licencia de todos los derechos de propiedad intelectual relacionados con el sistema.</p>
                    <p>Los usuarios reconocen que:</p>
                    <ul>
                        <li>No pueden copiar, modificar, distribuir, vender o alquilar cualquier parte del sistema sin autorización expresa.</li>
                        <li>Los materiales educativos proporcionados están destinados únicamente para uso personal y académico.</li>
                        <li>Cualquier infracción de los derechos de propiedad intelectual puede resultar en la terminación del acceso al sistema y acciones legales correspondientes.</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h3><i class="fas fa-lightbulb"></i> Patentes</h3>
                    <p>El sistema puede incorporar tecnologías y métodos que están protegidos por patentes. Los usuarios se comprometen a no:</p>
                    <ul>
                        <li>Intentar revertir la ingeniería, descompilar o desensamblar cualquier parte del sistema.</li>
                        <li>Utilizar el sistema para desarrollar productos o servicios competidores.</li>
                        <li>Infringir cualquier patente relacionada con las funcionalidades del sistema.</li>
                    </ul>
                    <div class="highlight">
                        <p><strong>Nota importante:</strong> Cualquier innovación o creación desarrollada por los usuarios durante el uso del sistema podría estar sujeta a las políticas de propiedad intelectual de la facultad, especialmente cuando se utilicen recursos institucionales.</p>
                    </div>
                </div>

                <div class="terms-section">
                    <h3><i class="fas fa-user-tie"></i> Responsabilidad Profesional</h3>
                    <p>Los usuarios del sistema, especialmente aquellos que se encuentran en programas profesionales, deben mantener los más altos estándares de conducta profesional, incluyendo:</p>
                    <ul>
                        <li>Utilizar el sistema de manera ética y responsable.</li>
                        <li>Respetar la confidencialidad de la información a la que tengan acceso.</li>
                        <li>Reportar cualquier vulnerabilidad o mal uso del sistema al administrador correspondiente.</li>
                        <li>Asumir responsabilidad por todas las actividades realizadas bajo su cuenta de usuario.</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h3><i class="fas fa-balance-scale"></i> Ética Profesional</h3>
                    <p>El uso del sistema debe ajustarse a las directrices y pautas de ética profesional establecidas por la facultad y los organismos reguladores correspondientes:</p>
                    <ul>
                        <li>Los usuarios deben evitar conflictos de interés en el uso del sistema.</li>
                        <li>Se prohíbe el uso del sistema para actividades ilegales, fraudulentas o que violen los derechos de terceros.</li>
                        <li>Se debe mantener la integridad académica, evitando el plagio y otras formas de deshonestidad académica.</li>
                        <li>Los usuarios deben respetar la diversidad y promover un ambiente inclusivo y respetuoso.</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h3><i class="fas fa-database"></i> Uso de Datos y Privacidad</h3>
                    <p>De acuerdo con las normativas internacionales de protección de datos (GDPR, CCPA, etc.), la facultad se compromete a:</p>
                    <ul>
                        <li>Procesar los datos personales de manera lícita, leal y transparente.</li>
                        <li>Recopilar datos solo para fines específicos, explícitos y legítimos.</li>
                        <li>Limitar la recopilación de datos a lo estrictamente necesario.</li>
                        <li>Mantener la exactitud de los datos y actualizarlos cuando sea necesario.</li>
                        <li>Almacenar los datos solo durante el tiempo necesario.</li>
                        <li>Garantizar la seguridad, integridad y confidencialidad de los datos.</li>
                    </ul>
                    <div class="highlight">
                        <p><strong>Consentimiento:</strong> Al utilizar este sistema, usted otorga su consentimiento explícito para el tratamiento de sus datos personales de acuerdo con nuestra Política de Privacidad, disponible en el sitio web de la facultad.</p>
                    </div>
                </div>

                <div class="terms-section">
                    <h3><i class="fas fa-exclamation-triangle"></i> Limitación de Responsabilidad</h3>
                    <p>La Facultad de Ciencias Aplicadas no será responsable por:</p>
                    <ul>
                        <li>Interrupciones temporales del servicio por mantenimiento o causas fuera de su control.</li>
                        <li>Daños indirectos, incidentales o consecuentes resultantes del uso o la imposibilidad de uso del sistema.</li>
                        <li>Pérdida de datos, siempre que se hayan seguido los protocolos de respaldo establecidos.</li>
                        <li>El uso indebido del sistema por parte de los usuarios en violación de estos términos.</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h3><i class="fas fa-sync-alt"></i> Modificaciones a los Términos</h3>
                    <p>La facultad se reserva el derecho de modificar estos términos en cualquier momento. Los usuarios serán notificados de cambios significativos y el uso continuado del sistema después de dichas modificaciones constituirá la aceptación de los nuevos términos.</p>
                </div>
            </div>
        </div>

        <div class="acceptance-section">
            <div class="form-group">
                <label>
                    <input type="checkbox" id="terminos" name="terminos" required>
                    <span>He leído y comprendo completamente los términos y condiciones de uso del sistema. Acepto cumplir con todas las disposiciones establecidas, incluyendo las relacionadas con Propiedad Intelectual, Patentes, Responsabilidad Profesional, Ética Profesional y el tratamiento de mis datos personales.</span>
                </label>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" id="privacidad" name="privacidad" required>
                    <span>Autorizo el tratamiento de mis datos personales de acuerdo con la <a href="#" target="_blank">Política de Privacidad</a> de la Facultad de Ciencias Aplicadas.</span>
                </label>
            </div>
            <button id="submit-btn" class="btn" onclick="window.location.href='inscribirse.php'" disabled>
  Continuar con la Inscripción
</button>
        </div>

        <footer>
            <p>Facultad de Ciencias Aplicadas &copy; 2025. Todos los derechos reservados.</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const terminosCheckbox = document.getElementById('terminos');
            const privacidadCheckbox = document.getElementById('privacidad');
            const submitBtn = document.getElementById('submit-btn');
            const termsContent = document.getElementById('termsContent');
            const progressBar = document.getElementById('progressBar');
            
            // Control del botón de envío
            function updateSubmitButton() {
                if (terminosCheckbox.checked && privacidadCheckbox.checked) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }
            
            terminosCheckbox.addEventListener('change', updateSubmitButton);
            privacidadCheckbox.addEventListener('change', updateSubmitButton);
            
            // Barra de progreso de lectura
            function updateProgressBar() {
                const scrollTop = termsContent.scrollTop;
                const scrollHeight = termsContent.scrollHeight - termsContent.clientHeight;
                const scrollPercentage = (scrollTop / scrollHeight) * 100;
                progressBar.style.width = scrollPercentage + '%';
            }
            
            termsContent.addEventListener('scroll', updateProgressBar);
            

            // Efecto de scroll suave para los enlaces
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html>
