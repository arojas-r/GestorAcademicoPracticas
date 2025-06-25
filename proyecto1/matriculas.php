<?php include 'conexion.php';

// Obtener cursos y alumnos
$cursos = $conexion->query("SELECT Nombre_Curso FROM CURSOS");
$alumnos = $conexion->query("SELECT DNI_Alumno, Nombre_Alumno, Apellido_Alumno FROM Alumnos");

// Añadir matrícula
if (isset($_POST['add'])) {
    $stmt = $conexion->prepare("INSERT INTO Matriculas (Nombre_Curso, DNI_Alumno, Nombre_Alumno, Apellido_Alumno, Estado_Matricula)
                                VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $_POST['curso'], $_POST['dni_alumno'], $_POST['nombre_alumno'], $_POST['apellido_alumno'], $_POST['estado']);
    $stmt->execute();
}

// Actualizar matrícula
if (isset($_POST['update'])) {
    $stmt = $conexion->prepare("UPDATE Matriculas SET Nombre_Curso=?, DNI_Alumno=?, Nombre_Alumno=?, Apellido_Alumno=?, Estado_Matricula=? WHERE ID_Matricula=?");
    $stmt->bind_param("sssssi", $_POST['curso'], $_POST['dni_alumno'], $_POST['nombre_alumno'], $_POST['apellido_alumno'], $_POST['estado'], $_POST['id']);
    $stmt->execute();
}

// Eliminar matrícula
if (isset($_POST['delete'])) {
    $stmt = $conexion->prepare("DELETE FROM Matriculas WHERE ID_Matricula=?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
}

// Buscar por DNI seleccionado
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$matriculas = $conexion->query("SELECT * FROM Matriculas WHERE DNI_Alumno LIKE '%$busqueda%'");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Gestión de Matrículas</title>
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
    <script>
        const alumnos = {};
        <?php
        $alumnosJS = $conexion->query("SELECT DNI_Alumno, Nombre_Alumno, Apellido_Alumno FROM Alumnos");
        while ($a = $alumnosJS->fetch_assoc()) {
            echo "alumnos['{$a['DNI_Alumno']}'] = {nombre: '{$a['Nombre_Alumno']}', apellido: '{$a['Apellido_Alumno']}'};\n";
        }
        ?>

        function autocompletarAlumno(select) {
            const dni = select.value;
            document.querySelector('input[name=\"nombre_alumno\"]').value = alumnos[dni]?.nombre || '';
            document.querySelector('input[name=\"apellido_alumno\"]').value = alumnos[dni]?.apellido || '';
        }
    </script>
</head>

<body>

    <header>
        <?php require 'header.php'; ?>
    </header>

    <div class="breadcrumbs">
        <a href="index.php">Inicio</a> &raquo; <span>Gestión de matrículas.</span>
    </div>

    <h2>Gestión de Matrículas</h2>

    <!-- 🔍 Buscar por DNI de alumno (select) -->
    <form method="get">
        <label for="buscar">Filtrar por alumno:</label>
        <select name="buscar" id="buscar" onchange="this.form.submit()">
            <option value="">-- Mostrar todos --</option>
            <?php
            $alumnos->data_seek(0);
            while ($a = $alumnos->fetch_assoc()):
                $seleccionado = ($busqueda === $a['DNI_Alumno']) ? 'selected' : '';
            ?>
                <option value="<?= $a['DNI_Alumno'] ?>" <?= $seleccionado ?>>
                    <?= $a['DNI_Alumno'] ?> - <?= $a['Nombre_Alumno'] ?> <?= $a['Apellido_Alumno'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <!-- ➕ Añadir matrícula -->
    <h3>Añadir Matrícula</h3>
    <form method="post">
        <input type="hidden" name="add" value="1">
        Curso:
        <select name="curso" required>
            <?php while ($c = $cursos->fetch_assoc()): ?>
                <option value="<?= $c['Nombre_Curso'] ?>"><?= $c['Nombre_Curso'] ?></option>
            <?php endwhile; ?>
        </select>
        Alumno:
        <select name="dni_alumno" onchange="autocompletarAlumno(this)" required>
            <option value="">-- Selecciona un alumno --</option>
            <?php
            $alumnos->data_seek(0);
            while ($a = $alumnos->fetch_assoc()): ?>
                <option value="<?= $a['DNI_Alumno'] ?>"><?= $a['DNI_Alumno'] ?> - <?= $a['Nombre_Alumno'] ?> <?= $a['Apellido_Alumno'] ?></option>
            <?php endwhile; ?>
        </select>
        Nombre: <input type="text" name="nombre_alumno" readonly>
        Apellido: <input type="text" name="apellido_alumno" readonly>
        Estado:
        <select name="estado">
            <option value="activa">Activa</option>
            <option value="desactivada">Desactivada</option>
        </select>
        <button type="submit">Añadir</button>
    </form>

    <!-- 📋 Tabla de matrículas -->
    <table>
        <tr>
            <th>ID</th>
            <th>Curso</th>
            <th>DNI Alumno</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php while ($m = $matriculas->fetch_assoc()): ?>
            <tr>
                <form method="post">
                    <td><?= $m['ID_Matricula'] ?><input type="hidden" name="id" value="<?= $m['ID_Matricula'] ?>"></td>
                    <td><input type="text" name="curso" value="<?= $m['Nombre_Curso'] ?>"></td>
                    <td><input type="text" name="dni_alumno" value="<?= $m['DNI_Alumno'] ?>"></td>
                    <td><input type="text" name="nombre_alumno" value="<?= $m['Nombre_Alumno'] ?>"></td>
                    <td><input type="text" name="apellido_alumno" value="<?= $m['Apellido_Alumno'] ?>"></td>
                    <td>
                        <select name="estado">
                            <option value="activa" <?= $m['Estado_Matricula'] == 'activa' ? 'selected' : '' ?>>Activa</option>
                            <option value="desactivada" <?= $m['Estado_Matricula'] == 'desactivada' ? 'selected' : '' ?>>Desactivada</option>
                        </select>
                    </td>
                    <td>
                        <button type="submit" name="update">Modificar</button>
                        <button type="submit" name="delete" onclick="return confirm('¿Eliminar esta matrícula?')">Eliminar</button>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </table>
    <footer>
        <?php require 'footer.php'; ?>
    </footer>


</body>

</html>