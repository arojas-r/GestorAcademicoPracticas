<?php include 'conexion.php';

// Añadir alumno
if (isset($_POST['add'])) {
    $stmt = $conexion->prepare("INSERT INTO Alumnos (DNI_Alumno, Nombre_Alumno, Apellido_Alumno, Teléfono_Alumno, Estado_Alumno, Fecha_Alta_Alumno) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $_POST['dni'], $_POST['nombre'], $_POST['apellido'], $_POST['telefono'], $_POST['estado'], $_POST['fecha_alta']);
    $stmt->execute();
}

// Actualizar alumno
if (isset($_POST['update'])) {
    $stmt = $conexion->prepare("UPDATE Alumnos SET Nombre_Alumno=?, Apellido_Alumno=?, Teléfono_Alumno=?, Estado_Alumno=?, Fecha_Alta_Alumno=?, Fecha_Baja_Alumno=? WHERE DNI_Alumno=?");
    $stmt->bind_param("sssssss", $_POST['nombre'], $_POST['apellido'], $_POST['telefono'], $_POST['estado'], $_POST['fecha_alta'], $_POST['fecha_baja'], $_POST['dni']);
    $stmt->execute();
}

// Eliminar alumno
if (isset($_POST['delete'])) {
    $stmt = $conexion->prepare("DELETE FROM Alumnos WHERE DNI_Alumno=?");
    $stmt->bind_param("s", $_POST['dni']);
    $stmt->execute();
}

// Buscar
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$query = "SELECT * FROM Alumnos WHERE DNI_Alumno LIKE '%$busqueda%'";
$resultado = $conexion->query($query);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Gestión de Alumnos</title>
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
        <a href="index.php">Inicio</a> &raquo; <span>Gestión del alumnado.</span>
    </div>

    <h2>Gestión de Alumnos</h2>

    <!-- Buscar por DNI -->
    <form method="get">
        <input type="text" name="buscar" placeholder="Buscar por DNI" value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
    </form>

    <!-- Formulario para añadir alumno -->
    <h3>Añadir Alumno</h3>
    <form method="post">
        <input type="hidden" name="add" value="1">
        DNI: <input type="text" name="dni" required>
        Nombre: <input type="text" name="nombre" required>
        Apellido: <input type="text" name="apellido" required>
        Teléfono: <input type="text" name="telefono">
        Estado:
        <select name="estado">
            <option value="activo">Activo</option>
            <option value="baja">Baja</option>
        </select>
        Fecha Alta: <input type="date" name="fecha_alta" required>
        <button type="submit">Añadir</button>
    </form>

    <!-- Tabla de resultados -->
    <table>
        <tr>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th>Fecha Alta</th>
            <th>Fecha Baja</th>
            <th>Acciones</th>
        </tr>
        <?php while ($alumno = $resultado->fetch_assoc()): ?>
            <tr>
                <form method="post">
                    <td><input type="text" name="dni" value="<?= $alumno['DNI_Alumno'] ?>" readonly></td>
                    <td><input type="text" name="nombre" value="<?= $alumno['Nombre_Alumno'] ?>"></td>
                    <td><input type="text" name="apellido" value="<?= $alumno['Apellido_Alumno'] ?>"></td>
                    <td><input type="text" name="telefono" value="<?= $alumno['Teléfono_Alumno'] ?>"></td>
                    <td>
                        <select name="estado">
                            <option value="activo" <?= $alumno['Estado_Alumno'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="baja" <?= $alumno['Estado_Alumno'] == 'baja' ? 'selected' : '' ?>>Baja</option>
                        </select>
                    </td>
                    <td><input type="date" name="fecha_alta" value="<?= $alumno['Fecha_Alta_Alumno'] ?>"></td>
                    <td><input type="date" name="fecha_baja" value="<?= $alumno['Fecha_Baja_Alumno'] ?>"></td>
                    <td>
                        <button type="submit" name="update">Modificar</button>
                        <button type="submit" name="delete" onclick="return confirm('¿Seguro que quieres eliminar este alumno?')">Eliminar</button>
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