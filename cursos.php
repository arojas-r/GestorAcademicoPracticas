<?php include 'conexion.php';

// Obtener profesores para el formulario desplegable
$profesores = $conexion->query("SELECT DNI_Profesor, CONCAT(Nombre_Profesor, ' ', Apellido_Profesor) AS NombreCompleto FROM Profesores");

// Añadir curso
if (isset($_POST['add'])) {
    $stmt = $conexion->prepare("INSERT INTO CURSOS (Nombre_Curso, Descripcion_Curso, DNI_PROFESOR, NOMBRE_PROFESOR, ESTADO_CURSO, FECHA_INICIO_CURSO, FECHA_FINAL_CURSO)
                                 VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $_POST['nombre'], $_POST['descripcion'], $_POST['dni_prof'], $_POST['nombre_prof'], $_POST['estado'], $_POST['fecha_inicio'], $_POST['fecha_final']);
    $stmt->execute();
}

// Actualizar curso
if (isset($_POST['update'])) {
    $stmt = $conexion->prepare("UPDATE CURSOS SET Nombre_Curso=?, Descripcion_Curso=?, DNI_PROFESOR=?, NOMBRE_PROFESOR=?, ESTADO_CURSO=?, FECHA_INICIO_CURSO=?, FECHA_FINAL_CURSO=? WHERE ID_CURSO=?");
    $stmt->bind_param("sssssssi", $_POST['nombre'], $_POST['descripcion'], $_POST['dni_prof'], $_POST['nombre_prof'], $_POST['estado'], $_POST['fecha_inicio'], $_POST['fecha_final'], $_POST['id_curso']);
    $stmt->execute();
}

// Eliminar curso
if (isset($_POST['delete'])) {
    $stmt = $conexion->prepare("DELETE FROM CURSOS WHERE ID_CURSO=?");
    $stmt->bind_param("i", $_POST['id_curso']);
    $stmt->execute();
}

// Buscar
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$query = "SELECT * FROM CURSOS WHERE Nombre_Curso LIKE '%$busqueda%'";
$cursos = $conexion->query($query);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Gestión de Cursos</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }

        form {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <header>
        <?php require 'header.php'; ?>
    </header>

    <div class="breadcrumbs">
        <a href="index.php">Inicio</a> &raquo; <span>Gestión de los cursos.</span>
    </div>

    <h2>Gestión de Cursos</h2>

    <!-- Buscar por nombre -->
    <form method="get">
        <input type="text" name="buscar" placeholder="Buscar por nombre del curso" value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
    </form>

    <!-- Añadir curso -->
    <h3>Añadir Curso</h3>
    <form method="post">
        <input type="hidden" name="add" value="1">
        Nombre: <input type="text" name="nombre" required>
        Descripción: <input type="text" name="descripcion">
        Profesor:
        <select name="dni_prof" onchange="this.form.nombre_prof.value=this.options[this.selectedIndex].text">
            <?php while ($prof = $profesores->fetch_assoc()): ?>
                <option value="<?= $prof['DNI_Profesor'] ?>"><?= $prof['NombreCompleto'] ?></option>
            <?php endwhile; ?>
        </select>
        <input type="hidden" name="nombre_prof" value="">
        Estado:
        <select name="estado">
            <option value="Activo">Activo</option>
            <option value="Cerrado">Cerrado</option>
        </select>
        Fecha Inicio: <input type="date" name="fecha_inicio" required>
        Fecha Final: <input type="date" name="fecha_final">
        <button type="submit">Añadir</button>
    </form>

    <!-- Tabla de cursos -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>DNI Profesor</th>
            <th>Nombre Profesor</th>
            <th>Estado</th>
            <th>Inicio</th>
            <th>Final</th>
            <th>Acciones</th>
        </tr>
        <?php while ($curso = $cursos->fetch_assoc()): ?>
            <tr>
                <form method="post">
                    <td><?= $curso['ID_CURSO'] ?><input type="hidden" name="id_curso" value="<?= $curso['ID_CURSO'] ?>"></td>
                    <td><input type="text" name="nombre" value="<?= $curso['Nombre_Curso'] ?>"></td>
                    <td><input type="text" name="descripcion" value="<?= $curso['Descripcion_Curso'] ?>"></td>
                    <td><input type="text" name="dni_prof" value="<?= $curso['DNI_PROFESOR'] ?>"></td>
                    <td><input type="text" name="nombre_prof" value="<?= $curso['NOMBRE_PROFESOR'] ?>"></td>
                    <td>
                        <select name="estado">
                            <option value="Activo" <?= $curso['ESTADO_CURSO'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="Cerrado" <?= $curso['ESTADO_CURSO'] == 'Cerrado' ? 'selected' : '' ?>>Cerrado</option>
                        </select>
                    </td>
                    <td><input type="date" name="fecha_inicio" value="<?= $curso['FECHA_INICIO_CURSO'] ?>"></td>
                    <td><input type="date" name="fecha_final" value="<?= $curso['FECHA_FINAL_CURSO'] ?>"></td>
                    <td>
                        <button type="submit" name="update">Modificar</button>
                        <button type="submit" name="delete" onclick="return confirm('¿Eliminar este curso?')">Eliminar</button>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </table>

    <script>
        // autocompletar nombre profesor al seleccionar
        document.querySelector('select[name="dni_prof"]').addEventListener('change', function() {
            document.querySelector('input[name="nombre_prof"]').value = this.options[this.selectedIndex].text;
        });
    </script>

    <footer>
        <?php require 'footer.php'; ?>
    </footer>


</body>

</html>