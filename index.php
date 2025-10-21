<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inscripción Académica – Facultad de Ciencias Aplicadas</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <header>
        <img src="imagenes/Frontal.png" alt="Logo de la Facultad">
        <h1>Sistema de Inscripción Académica – Facultad de Ciencias Aplicadas </h1>

    </header>
    <main>
       <section class="hero">
           <img src="imagenes/FT.webp" alt="Imagen de fondo">
           <section class="botones">
           <button type="button" class="btn btn-primary" onclick="location.href='Formulario/inscribirse.php'">Inscribirse</button>
           <button type="button" class="btn btn-primary" onclick="location.href='Sesion/inicio.php'">Iniciar Sesión</button>
        </section>
               <h2>¡Construye tu futuro en la Facultad de Ciencias Aplicadas!</h2>
               <p class="texto-principal">Elige la carrera que más te apasiona y da el primer paso hacia una formación académica de excelencia.</p>
</section>

<section class="historia">
    <div class="carousel">
        <button class="prev">&#10094;</button>
        <img src="imagenes/FCA.png" alt="Imagen 1 de la carrera" class="active">
        <img src="imagenes/Analisis.png" alt="Imagen 2 de la carrera">
        <img src="imagenes/Industriales.png" alt="Imagen 3 de la carrera">
        <img src="imagenes/ISEF.png" alt="Imagen 4 de la carrera">
        <button class="next">&#10095;</button>
    </div>
</section>
        <section class="comentarios">
            <h2>Deja tu consulta</h2>
            <form action="conex.php" method="POST">

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="comentario" class="form-label">Comentario:</label>
                    <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        </section>
    </main>

    <footer>
  <div class="contacto">
    <h2>Contacto</h2>
    <p>Dirección: Tte. 1° José María Cano c/ Dr. Narciso González Romero.
    Campus Universitario – Barrio Ytororo – Ñeembucú – Pilar, PY.</p>
    <p>Teléfono: <a href="tel:+595786230019">+595.786.230019</a> | <a href="tel:+595975681103">+595.975.681.103</a></p>
    <p>Email: <a href="mailto:nfernandez@aplicadas.edu.py">nfernandez@aplicadas.edu.py</a> | <a href="mailto:analisis@aplicadas.edu.py">analisis@aplicadas.edu.py</a></p>
  </div>
  <p>&copy; Derechos reservados. <a href="terminos.html">Lic. en Análisis de Sistemas - Cuarto Curso 2025</a>.</p>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
const images = document.querySelectorAll('.carousel img');
const prev = document.querySelector('.carousel .prev');
const next = document.querySelector('.carousel .next');
let current = 0;

function showImage(index) {
    images.forEach((img, i) => img.classList.remove('active'));
    images[index].classList.add('active');
}

prev.addEventListener('click', () => {
    current = (current - 1 + images.length) % images.length;
    showImage(current);
});

next.addEventListener('click', () => {
    current = (current + 1) % images.length;
    showImage(current);
});

// Opcional: cambiar imagen automáticamente cada 5 segundos
setInterval(() => {
    current = (current + 1) % images.length;
    showImage(current);
}, 5000);
</script>
</body>
