<?php
// profesores.php

// Este archivo contiene las funcionalidades de Alta, Baja, Modificar y Listado de Profesores.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Profesores</title>
    <link rel="stylesheet" href="styles.css"> <!-- CSS externo -->
    <script src="script.js" defer></script> <!-- JS externo -->
</head>
<body>
    <header>
        <h1>Gestión de Profesores</h1>
        <nav>
            <button onclick="toggleSection('alta')">Alta Profesor</button>
            <button onclick="toggleSection('baja')">Baja Profesor</button>
            <button onclick="toggleSection('modificar')">Modificar Profesor</button>
            <button onclick="toggleSection('listado')">Listado Profesores</button>
        </nav>
    </header>

    <!-- Alta Profesor -->
    <section id="alta" class="hidden">
        <h2>Alta de Profesor</h2>
        <form method="POST" action="">
            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="dni" required>

            <label for="cargo">Cargo:</label>
            <input type="text" id="cargo" name="cargo" required>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="fecha_alta">Fecha Alta:</label>
            <input type="date" id="fecha_alta" name="fecha_alta" required>

            <label for="fecha_baja">Fecha Baja:</label>
            <input type="date" id="fecha_baja" name="fecha_baja">

            <button type="submit" name="guardar_alta">Guardar Cambios</button>
        </form>
    </section>

    <!-- Baja Profesor -->
    <section id="baja" class="hidden">
        <h2>Baja de Profesor</h2>
        <form method="POST" action="">
            <label for="buscar_dni_baja">Buscar por DNI:</label>
            <input type="text" id="buscar_dni_baja" name="buscar_dni_baja" required>
            <button type="submit" name="buscar_baja">Buscar</button>
        </form>

        <div id="resultado_baja">
            <!-- Mostrar datos del profesor encontrado -->
            <!-- Incluir botón para confirmar baja -->
        </div>
    </section>

    <!-- Modificar Profesor -->
    <section id="modificar" class="hidden">
        <h2>Modificar Profesor</h2>
        <form method="POST" action="">
            <label for="buscar_dni_modificar">Buscar por DNI:</label>
            <input type="text" id="buscar_dni_modificar" name="buscar_dni_modificar" required>
            <button type="submit" name="buscar_modificar">Buscar</button>
        </form>

        <div id="resultado_modificar">
            <!-- Mostrar formulario con datos del profesor para modificar -->
        </div>
    </section>

    <!-- Listado Profesores -->
    <section id="listado" class="hidden">
        <h2>Listado de Profesores</h2>
        <form method="POST" action="">
            <label for="buscar_dni_listado">Buscar por DNI:</label>
            <input type="text" id="buscar_dni_listado" name="buscar_dni_listado">
            <button type="submit" name="buscar_listado">Buscar</button>
        </form>

        <div id="tabla_listado">
            <!-- Mostrar listado de profesores -->
        </div>
    </section>

</body>
</html>

<script>
// Mostrar/Ocultar secciones
function toggleSection(sectionId) {
    const sections = document.querySelectorAll('section');
    sections.forEach(section => section.classList.add('hidden'));
    document.getElementById(sectionId).classList.remove('hidden');
}
</script>

<style>
/* Estilos básicos */
body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
header { background: #007bff; color: white; padding: 1rem; }
nav button { margin-right: 10px; padding: 10px; cursor: pointer; }
section { padding: 1rem; margin-top: 1rem; }
.hidden { display: none; }
form { margin-top: 10px; }
footer { text-align: center; padding: 1rem; background: #f1f1f1; margin-top: 2rem; }
</style>
