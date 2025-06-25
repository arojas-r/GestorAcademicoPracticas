<?php include 'conexion.php';

// Añadir profesor
if (isset($_POST['add'])) {
    $stmt = $conexion->prepare("INSERT INTO Profesores (DNI_Profesor, Nombre_Profesor, Apellido_Profesor, Telefono_Profesor, Estado_Profesor, Fecha_Alta_Profesor) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $_POST['dni'], $_POST['nombre'], $_POST['apellido'], $_POST['telefono'], $_POST['estado'], $_POST['fecha_alta']);
    $stmt->execute();
}

// Actualizar profesor
if (isset($_POST['update'])) {
    $stmt = $conexion->prepare("UPDATE Profesores SET Nombre_Profesor=?, Apellido_Profesor=?, Telefono_Profesor=?, Estado_Profesor=?, Fecha_Alta_Profesor=?, Fecha_Baja_Profesor=? WHERE DNI_Profesor=?");
    $stmt->bind_param("sssssss", $_POST['nombre'], $_POST['apellido'], $_POST['telefono'], $_POST['estado'], $_POST['fecha_alta'], $_POST['fecha_baja'], $_POST['dni']);
    $stmt->execute();
}

// Eliminar profesor
if (isset($_POST['delete'])) {
    $stmt = $conexion->prepare("DELETE FROM Profesores WHERE DNI_Profesor=?");
    $stmt->bind_param("s", $_POST['dni']);
    $stmt->execute();
}

// Buscar
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$query = "SELECT * FROM Profesores WHERE DNI_Profesor LIKE '%$busqueda%'";
$resultado = $conexion->query($query);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Gestión de Profesores</title>
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
        <a href="index.php">Inicio</a> &raquo; <span>Gestión del profesorado.</span>
    </div>

    <h2>Gestión de Profesores</h2>

    <!-- Buscar por DNI -->
    <form method="get">
        <input type="text" name="buscar" placeholder="Buscar por DNI" value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
    </form>

    <!-- Formulario para añadir profesor -->
    <h3>Añadir Profesor</h3>
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
        <?php while ($profesor = $resultado->fetch_assoc()): ?>
            <tr>
                <form method="post">
                    <td><input type="text" name="dni" value="<?= $profesor['DNI_Profesor'] ?>" readonly></td>
                    <td><input type="text" name="nombre" value="<?= $profesor['Nombre_Profesor'] ?>"></td>
                    <td><input type="text" name="apellido" value="<?= $profesor['Apellido_Profesor'] ?>"></td>
                    <td><input type="text" name="telefono" value="<?= $profesor['Telefono_Profesor'] ?>"></td>
                    <td>
                        <select name="estado">
                            <option value="activo" <?= $profesor['Estado_Profesor'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="baja" <?= $profesor['Estado_Profesor'] == 'baja' ? 'selected' : '' ?>>Baja</option>
                        </select>
                    </td>
                    <td><input type="date" name="fecha_alta" value="<?= $profesor['Fecha_Alta_Profesor'] ?>"></td>
                    <td><input type="date" name="fecha_baja" value="<?= $profesor['Fecha_Baja_Profesor'] ?>"></td>
                    <td>
                        <button type="submit" name="update">Modificar</button>
                        <button type="submit" name="delete" onclick="return confirm('¿Eliminar este profesor?')">Eliminar</button>
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