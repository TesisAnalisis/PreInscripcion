 <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Pre-Inscripción</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>
<section class="inscripcion">  
    <header>
        <img src="IMG/Frontal.png" alt="Logo de la Facultad">
        <h1>Formulario de Pre-Inscripción</h1>
    </header>  

    <form action="procesar_inscripcion.php" method="post" enctype="multipart/form-data">

        <!-- ================= DATOS PERSONALES ================= -->
        <h2>Datos Personales y de Contacto</h2>

        <div class="form-group">
            <label for="nombre">Nombre Completo:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="apellido">Apellido Completo:</label>
            <input type="text" class="form-control" id="apellido" name="apellido" required>
        </div>

        <div class="form-group">
            <label for="cedula_numero">Número de Cédula de Identidad:</label>
            <input type="text" class="form-control" id="cedula_numero" name="cedula_numero" required>
        </div>

        <div class="form-group">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" required>
        </div>

        <div class="form-group">
            <label for="telefono_emergencia">Teléfono de Emergencia:</label>
            <input type="tel" class="form-control" id="telefono_emergencia" name="telefono_emergencia" required>
        </div>

        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input type="text" class="form-control" id="direccion" name="direccion" required>
        </div>

        <div class="form-group">
            <label for="distrito">Distrito de Procedencia:</label>
            <select class="form-control" id="distrito" name="distrito" required>
                <option value="">Seleccione su distrito</option>
                <option value="Alberdi">Alberdi</option>
                <option value="Cerrito">Cerrito</option>
                <option value="Desmochados">Desmochados</option>
                <option value="General José de Eduvigis Díaz">General José de Eduvigis Díaz</option>
                <option value="Guazú Cuá">Guazú Cuá</option>
                <option value="Humaitá">Humaitá</option>
                <option value="Isla Umbú">Isla Umbú</option>
                <option value="Laureles">Laureles</option>
                <option value="Mayor José de Jesús Martínez">Mayor José de Jesús Martínez</option>
                <option value="Paso de Patria">Paso de Patria</option>
                <option value="Pilar">Pilar</option>
                <option value="San Juan Bautista del Ñeembucú">San Juan Bautista del Ñeembucú</option>
                <option value="Tacuaras">Tacuaras</option>
                <option value="Villa Franca">Villa Franca</option>
                <option value="Villa Oliva">Villa Oliva</option>
                <option value="Villalbín">Villalbín</option>
                <option value="Otros">Otros</option>
            </select>
        </div>

        <div class="form-group">
            <label for="nacionalidad">Nacionalidad:</label>
            <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" required>
        </div>

        <div class="form-group">
            <label for="sexo">Sexo:</label>
            <select class="form-control" id="sexo" name="sexo" required>
                <option value="">Seleccione una opción</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Prefiero Omitir">Prefiero Omitir</option>
            </select>
        </div>

        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
        </div>

        <!-- ================= DATOS ACADÉMICOS ================= -->
        <h2>Datos Académicos</h2>

        <div class="form-group">
            <label for="anio_egreso">Año de Egreso:</label>
            <select class="form-control" id="anio_egreso" name="anio_egreso" required>
                <option value="">Seleccione el año</option>
                <option value="anteriores">Años anteriores a 2010</option>
                <?php
                    for ($i = 2010; $i <= 2030; $i++) {
                        echo "<option value=\"$i\">$i</option>";
                    }
                ?>
                <option value="posteriores">Años posteriores a 2030</option>
            </select>
        </div>

         <div class="form-group">
    <label for="colegio_egresado">Institución de Procedencia:</label>
    <select class="form-control" id="colegio_egresado" name="colegio_egresado" required>
        <option value="">Seleccione su colegio</option>
        <option value="Col. Nac. E.M.D. ITALIANO SANTO TOMAS (Pilar)">Col. Nac. E.M.D. ITALIANO SANTO TOMAS (Pilar)</option>
        <option value="COLEGIO TECNICO JUAN XXIII (Pilar)">COLEGIO TECNICO JUAN XXIII (Pilar)</option>
        <option value="COL. NAC. SAN LORENZO (Pilar)">COL. NAC. SAN LORENZO (Pilar)</option>
        <option value="CENTRO REGIONAL DE EDUCACIÓN MCAL. FRANCISCO S. LOPEZ (Pilar)">CENTRO REGIONAL DE EDUCACIÓN MCAL. FRANCISCO S. LOPEZ (Pilar)</option>
        <option value="COL. NAC. 6º COMPAÑIA MEDINA (Pilar)">COL. NAC. 6º COMPAÑIA MEDINA (Pilar)</option>
        <option value="COL.PRIV.SUBV. VIRGEN DE FATIMA (Pilar)">COL.PRIV.SUBV. VIRGEN DE FATIMA (Pilar)</option>
        <option value="COL. NAC. PILAR (Pilar)">COL. NAC. PILAR (Pilar)</option>
        <option value="COLEGIO SAN FRANCISCO DE ASIS (Pilar)">COLEGIO SAN FRANCISCO DE ASIS (Pilar)</option>
        <option value="COL. NAC. DE LOMAS (Alberdi)">COL. NAC. DE LOMAS (Alberdi)</option>
        <option value="COL. NAC. JUAN BAUTISTA ALBERDI (Alberdi)">COL. NAC. JUAN BAUTISTA ALBERDI (Alberdi)</option>
        <option value="COLEGIO NACIONAL CERRITO (Cerrito)">COLEGIO NACIONAL CERRITO (Cerrito)</option>
        <option value="COL. NAC. TACURUTY (Cerrito)">COL. NAC. TACURUTY (Cerrito)</option>
        <option value="COLEGIO NACIONAL VIRGEN DEL CARMEN (Desmochados)">COLEGIO NACIONAL VIRGEN DEL CARMEN (Desmochados)</option>
        <option value="COL. NAC. GRAL. JOSE EDUVIGIS DIAZ (General Diaz)">COL. NAC. GRAL. JOSE EDUVIGIS DIAZ (General Diaz)</option>
        <option value="COL. NAC. LOMA GUAZU (General Diaz)">COL. NAC. LOMA GUAZU (General Diaz)</option>
        <option value="COL. NAC. RIGOBERTO CABALLERO (Guazu Cua)">COL. NAC. RIGOBERTO CABALLERO (Guazu Cua)</option>
        <option value="COL. NAC. SAN CARLOS (Humaita)">COL. NAC. SAN CARLOS (Humaita)</option>
        <option value="COL. NAC. NUESTRA SEÑORA DE LAS MERCEDES (Humaita)">COL. NAC. NUESTRA SEÑORA DE LAS MERCEDES (Humaita)</option>
        <option value="COL. NAC. CONTRAL. RAMON ENRIQUE MARTINO (Isla Umbu)">COL. NAC. CONTRAL. RAMON ENRIQUE MARTINO (Isla Umbu)</option>
        <option value="COLEGIO NACIONAL DE ISLERIA (Isla Umbu)">COLEGIO NACIONAL DE ISLERIA (Isla Umbu)</option>
        <option value="COL. NAC. VIRGEN DEL ROSARIO (Los Laureles)">COL. NAC. VIRGEN DEL ROSARIO (Los Laureles)</option>
        <option value="COLEGIO NACIONAL APIPE (Los Laureles)">COLEGIO NACIONAL APIPE (Los Laureles)</option>
        <option value="COL. NAC. GRAL. BERNARDINO CABALLERO (Mayor Martinez)">COL. NAC. GRAL. BERNARDINO CABALLERO (Mayor Martinez)</option>
        <option value="COL. NAC. CNEL. MANUEL W. CHAVEZ (Mayor Martinez)">COL. NAC. CNEL. MANUEL W. CHAVEZ (Mayor Martinez)</option>
        <option value="COLEGIO NACIONAL YATAITY (Mayor Martinez)">COLEGIO NACIONAL YATAITY (Mayor Martinez)</option>
        <option value="COL. NAC. SAN PATRICIO (Paso de Patria)">COL. NAC. SAN PATRICIO (Paso de Patria)</option>
        <option value="COL. NAC. SAN JUAN BAUTISTA DE ÑEEMBUCU (San Juan B. de Ñeembucú)">COL. NAC. SAN JUAN BAUTISTA DE ÑEEMBUCU (San Juan B. de Ñeembucú)</option>
        <option value="COL. NAC. CARLOS ANTONIO LOPEZ (San Juan B. de Ñeembucú)">COL. NAC. CARLOS ANTONIO LOPEZ (San Juan B. de Ñeembucú)</option>
        <option value="COL. NAC. SAGRADO CORAZON DE JESUS (San Juan B. de Ñeembucú)">COL. NAC. SAGRADO CORAZON DE JESUS (San Juan B. de Ñeembucú)</option>
        <option value="COL. NAC. COSTA ROSADO (San Juan B. de Ñeembucú)">COL. NAC. COSTA ROSADO (San Juan B. de Ñeembucú)</option>
        <option value="COL. NAC. MCAL. FRANCISCO SOLANO LOPEZ (Tacuaras)">COL. NAC. MCAL. FRANCISCO SOLANO LOPEZ (Tacuaras)</option>
        <option value="COL. NAC. COLONIA MBURICA (Tacuaras)">COL. NAC. COLONIA MBURICA (Tacuaras)</option>
        <option value="COL. NAC. VILLA FRANCA (Villa Franca)">COL. NAC. VILLA FRANCA (Villa Franca)</option>
        <option value="COL. NAC. LILIAN SOLALINDE (Villa Oliva)">COL. NAC. LILIAN SOLALINDE (Villa Oliva)</option>
        <option value="COLEGIO NACIONAL DE ZANJITA (Villa Oliva)">COLEGIO NACIONAL DE ZANJITA (Villa Oliva)</option>
        <option value="COL. NAC. RIO PARAGUAY (Villa Oliva)">COL. NAC. RIO PARAGUAY (Villa Oliva)</option>
        <option value="COL. NAC. SAN FRANCISCO (Villalbin)">COL. NAC. SAN FRANCISCO (Villalbin)</option>
        <option value="COLEGIO NACIONAL SAN RAMON (Villalbin)">COLEGIO NACIONAL SAN RAMON (Villalbin)</option>
        <option value="COLEGIO NACIONAL TENIENTE SANCHEZ (Villalbin)">COLEGIO NACIONAL TENIENTE SANCHEZ (Villalbin)</option>
        <option value="Otro Departamento">Otro Departamento</option>
    </select>
</div>

        <div class="form-group">
            <label for="carrera">Carrera a la que desea postularse:</label>
            <select class="form-control" id="carrera" name="carrera" required>
                <option value="">Seleccione una carrera</option>
                <option value="Licenciatura en Análisis de Sistemas">Licenciatura en Análisis de Sistemas</option>
                <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                <option value="Ingeniería Ambiental">Ingeniería Ambiental</option>
                <option value="Lic. en Educación Física y Entrenamiento Deportivo">Lic. en Educación Física y Entrenamiento Deportivo</option>
            </select>
        </div>

        <!-- ================= CONDICIÓN MÉDICA ================= -->
        <h2>Condición Médica</h2>
        <div class="form-group">
            <label for="condicion_medica">Seleccione su condición médica (si corresponde):</label>
            <select class="form-control" id="condicion_medica" name="condicion_medica">
                <option value="">Ninguna</option>
                <option value="Diabetes">Diabetes</option>
                <option value="Hipertensión">Hipertensión</option>
                <option value="Otra">Otra (especificar abajo)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="condicion_especifica">Si seleccionó "Otra", indique su condición:</label>
            <input type="text" class="form-control" id="condicion_especifica" name="condicion_especifica">
        </div>

        <!-- ================= OTROS DATOS ================= -->
        <h2>Otros Datos</h2>
        <div class="form-group">
            <label for="referencia">¿Cómo se enteró de la carrera?</label>
            <select class="form-control" id="referencia" name="referencia" required>
                <option value="">Seleccione una opción</option>
                <option value="Redes Sociales">Redes Sociales</option>
                <option value="Amigos / Familiares">Amigos / Familiares</option>
                <option value="Página Web">Página Web</option>
                <option value="Feria Educativa">Feria Educativa</option>
                <option value="Otros">Otros</option>
            </select>
        </div>

        <div class="form-group">
            <label for="foto_carnet">Foto tipo carnet actualizada:</label>
            <input type="file" class="form-control" id="foto_carnet" name="foto_carnet" required>
        </div>

        <!-- ================= DOCUMENTOS ================= -->
        <h2>Documentos Requeridos</h2>

        <div class="form-group">
            <label for="foto_anverso_cedula">Imagen Frontal de la Cédula de Identidad:</label>
            <input type="file" class="form-control" id="foto_anverso_cedula" name="foto_anverso_cedula" required>
        </div>

        <div class="form-group">
            <label for="foto_reverso_cedula">Imagen dorsal de la Cédula de Identidad:</label>
            <input type="file" class="form-control" id="foto_reverso_cedula" name="foto_reverso_cedula" required>
        </div>

        <div class="form-group">
            <label for="foto_anverso_certificado">Imagen Frontal del Certificado de Estudio:</label>
            <input type="file" class="form-control" id="foto_anverso_certificado" name="foto_anverso_certificado" required>
        </div>

        <div class="form-group">
            <label for="foto_reverso_certificado">Imagen dorsal del Certificado de Estudio:</label>
            <input type="file" class="form-control" id="foto_reverso_certificado" name="foto_reverso_certificado" required>
        </div>

        <div class="form-group">
            <label for="antecedente_policial">Imagen del Certificado del Antecedente Policial:</label>
            <input type="file" class="form-control" id="antecedente_policial" name="antecedente_policial" required>
        </div>

        <div class="form-group">
            <label for="cert_medic">Imagen del Certificado Médico:</label>
            <input type="file" class="form-control" id="cert_medic" name="cert_medic" required>
        </div>

        <div class="form-group">
            <label for="cert_nacim">Imagen del Certificado de Nacimiento:</label>
            <input type="file" class="form-control" id="cert_nacim" name="cert_nacim" required>
        </div>  

        <!-- ================= SEGURIDAD ================= -->
        <h2>Seguridad</h2>
        <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <div style="position: relative;">
                <input type="password" class="form-control" id="contrasena" name="contrasena" required style="padding-right: 40px;">
                <span onclick="mostrarOcultarContrasena()" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;">
                    <img id="icono-ojo" src="https://cdn-icons-png.flaticon.com/512/709/709612.png" width="20" alt="Mostrar">
                </span>
            </div>
        </div>

        <script>
            function mostrarOcultarContrasena() {
                const input = document.getElementById("contrasena");
                const icono = document.getElementById("icono-ojo");
                if (input.type === "password") {
                    input.type = "text";
                    icono.src = "https://cdn-icons-png.flaticon.com/512/709/709620.png"; 
                } else {
                    input.type = "password";
                    icono.src = "https://cdn-icons-png.flaticon.com/512/709/709612.png"; 
                }
            }
        </script>

        <!-- ================= DECLARACIONES ================= -->
        <h2>Declaraciones</h2>
        <div class="form-group">
            <label>
                <input type="checkbox" name="terminos" required>
                Acepto los <a href="terminos.php" target="_blank">términos y condiciones de uso del sistema</a>.
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="consentimiento_datos" required>
                Autorizo el tratamiento de mis datos personales conforme a la ley de protección de datos.
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="declaracion_veracidad" required>
                Declaro bajo juramento que los datos ingresados son verídicos.
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Enviar Inscripción</button>
    </form>
</section>
</body>
</html>
