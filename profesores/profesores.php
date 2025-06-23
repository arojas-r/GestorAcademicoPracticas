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
            <button onclick="toggleSection('listado')">Listado Profesores</button>
            <button onclick="toggleSection('alta')">Alta Profesor</button>
        </nav>
    </header>

    <?php
    // Conexión a la base de datos
    $conn = new mysqli('localhost', 'usuario', 'contraseña', 'Instituto');

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    ?>

    <!-- Listado Profesores (Sección predeterminada) -->
    <section id="listado">
        <h2>Listado de Profesores</h2>
        <form method="POST" action="">
            <label for="buscar_dni_listado">Introducir DNI del Profesor:</label>
            <input type="text" id="buscar_dni_listado" name="buscar_dni_listado">
            <button type="submit" name="buscar_listado">Buscar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Estado Profesor</th>
                </tr>
            </thead>
            <tbody>
                <!-- Mostrar listado de profesores con los primeros 20 registros de la tabla Profesor -->
                <?php
                $query = "SELECT DNI, Nombre, Apellido, Estado_Profesor FROM Profesor LIMIT 20";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $estado_color = $row['Estado_Profesor'] === 'Activo' ? 'green' : 'red';
                        echo "<tr>";
                        echo "<td><a href='?dni=" . $row['DNI'] . "'>" . $row['DNI'] . "</a></td>";
                        echo "<td>" . $row['Nombre'] . "</td>";
                        echo "<td>" . $row['Apellido'] . "</td>";
                        echo "<td style='color: $estado_color;'>" . $row['Estado_Profesor'] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </section>

    <!-- Ficha de Profesor -->
    <section id="ficha_profesor" class="hidden">
        <h2>Ficha del Profesor</h2>
        <div id="detalle_profesor">
            <!-- Mostrar los datos del profesor seleccionado -->
            <?php
            if (isset($_GET['dni'])) {
                $dni = $_GET['dni'];
                $query = "SELECT * FROM Profesor WHERE DNI='$dni'";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                    $profesor = $result->fetch_assoc();
                    foreach ($profesor as $key => $value) {
                        if ($key === 'Estado_Profesor') {
                            $estado_color = $value === 'Activo' ? 'green' : 'red';
                            echo "<p><strong>$key:</strong> <span style='color: $estado_color;'>$value</span></p>";
                        } else {
                            echo "<p><strong>$key:</strong> $value</p>";
                        }
                    }
                }
            }
            ?>
        </div>

        <button onclick="toggleSection('modificar')">Modificar Profesor</button>
        <button onclick="toggleSection('baja')">Baja Profesor</button>
    </section>

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

            <input type="hidden" name="estado_profesor" value="Activo">

            <button type="submit" name="guardar_alta">Guardar Cambios</button>
        </form>
    </section>

    <!-- Baja Profesor -->
    <section id="baja" class="hidden">
        <h2>Baja de Profesor</h2>
        <p>¿Estás seguro de que quieres dar de baja a este profesor?</p>
        <form method="POST" action="">
            <input type="hidden" id="dni_baja" name="dni_baja" value="">
            <input type="hidden" name="fecha_baja" value="<?php echo date('Y-m-d'); ?>">
            <input type="hidden" name="estado_profesor" value="Baja">
            <button type="submit" name="confirmar_baja">Confirmar Baja</button>
        </form>
    </section>

    <!-- Modificar Profesor -->
    <section id="modificar" class="hidden">
        <h2>Modificar Profesor</h2>
        <form method="POST" action="">
            <label for="cargo">Cargo:</label>
            <input type="text" id="cargo_modificar" name="cargo" required>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre_modificar" name="nombre" required>

            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido_modificar" name="apellido" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono_modificar" name="telefono" required>

            <label for="fecha_alta">Fecha Alta:</label>
            <input type="date" id="fecha_alta_modificar" name="fecha_alta" required>

            <label for="fecha_baja">Fecha Baja:</label>
            <input type="date" id="fecha_baja_modificar" name="fecha_baja">

            <p>Estado: <span id="estado_profesor_modificar" style="color: red;">Baja</span></p>

            <button type="submit" name="guardar_modificacion">Guardar Cambios</button>
        </form>
    </section>

    <footer>
        <p>Gestión de Profesores &copy; 2025</p>
    </footer>
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
table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f4f4f4; }
footer { text-align: center; padding: 1rem; background: #f1f1f1; margin-top: 2rem; }
</style>
