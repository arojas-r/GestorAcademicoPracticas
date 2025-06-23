<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area del Alumno</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <main>
    <h1>Área del Alumno</h1>
    <p>Bienvenido al área del alumno. Aquí puedes gestionar tus datos y ver tu progreso.</p>
    </main>
    <div>
        
        <button onclick="mostrarFormulario()">Dame de Alta</button>
        <div id="formdiv">
            <form action="" method="post">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required>

                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required>

                <label for="email">Telefono:</label>
                <input type="text" id="telefono" name="telefono" required>

                <button type="submit">Enviar</button>
            </form>
        </div>
        <button>Gestionar Datos</button>
        <div>
            
        </div>
    </div>
    <script>
        function mostrarFormulario() {
            const formDiv = document.getElementById('formdiv');
            formDiv.style.display = formDiv.style.display === 'none' ? 'block' : 'none';
        }
    </script>
    
    <footer>
        <p>&copy; 2025 Proyecto</p>
    </footer>

    
</body>
</html>

