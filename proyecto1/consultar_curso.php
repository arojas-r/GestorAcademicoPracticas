<?php include 'conexion.php';

// Obtener todos los cursos para el desplegable
$cursos = $conexion->query("SELECT Nombre_Curso FROM CURSOS ORDER BY Nombre_Curso ASC");

// Inicializar datos
$cursoSeleccionado = null;
$alumnos = [];

if (isset($_GET['buscar'])) {
    $nombreCurso = $_GET['buscar'];

    // Obtener detalles del curso
    $stmt = $conexion->prepare("SELECT * FROM CURSOS WHERE Nombre_Curso = ?");
    $stmt->bind_param("s", $nombreCurso);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $cursoSeleccionado = $resultado->fetch_assoc();

    // Obtener alumnos del curso
    if ($cursoSeleccionado) {
        $stmt2 = $conexion->prepare("
            SELECT a.Nombre_Alumno, a.Apellido_Alumno
            FROM Matriculas m
            JOIN Alumnos a ON m.DNI_Alumno = a.DNI_Alumno
            WHERE m.Nombre_Curso = ?
        ");
        $stmt2->bind_param("s", $nombreCurso);
        $stmt2->execute();
        $alumnos = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Consultar Curso</title>
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
        <a href="index.php">Inicio</a> &raquo; <span>Listado por cursos.</span>
    </div>
    <h2>Consulta de Cursos</h2>

    <!-- 🔽 Buscador desplegable de cursos -->
    <form method="get">
        <label for="buscar">Selecciona un curso:</label>
        <select name="buscar" id="buscar" onchange="this.form.submit()">
            <option value="">-- Selecciona --</option>
            <?php while ($curso = $cursos->fetch_assoc()):
                $selected = (isset($_GET['buscar']) && $_GET['buscar'] == $curso['Nombre_Curso']) ? 'selected' : '';
            ?>
                <option value="<?= $curso['Nombre_Curso'] ?>" <?= $selected ?>>
                    <?= $curso['Nombre_Curso'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <!-- 🎓 Información del curso -->
    <?php if ($cursoSeleccionado): ?>
        <h3><?= htmlspecialchars($cursoSeleccionado['Nombre_Curso']) ?></h3>
        <p><strong>Profesor:</strong> <?= htmlspecialchars($cursoSeleccionado['NOMBRE_PROFESOR']) ?></p>
        <h5>Inicio: <?= $cursoSeleccionado['FECHA_INICIO_CURSO'] ?> | Final: <?= $cursoSeleccionado['FECHA_FINAL_CURSO'] ?? 'No asignada' ?></h5>

        <!-- 👥 Alumnos del curso -->
        <h4>Alumnos matriculados</h4>
        <?php if (count($alumnos) > 0): ?>
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                </tr>
                <?php foreach ($alumnos as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['Nombre_Alumno']) ?></td>
                        <td><?= htmlspecialchars($a['Apellido_Alumno']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No hay alumnos matriculados en este curso.</p>
        <?php endif; ?>
    <?php elseif (isset($_GET['buscar'])): ?>
        <p><strong>Curso no encontrado.</strong></p>
    <?php endif; ?>

    <footer>
        <?php require 'footer.php'; ?>
    </footer>


</body>

</html>