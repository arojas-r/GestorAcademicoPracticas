<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$pdo = getPDO();

// Obtener todos los cursos para el desplegable
$cursos = $pdo->query("SELECT ID_Curso, Nombre_Curso FROM Curso ORDER BY Nombre_Curso ASC")->fetchAll(PDO::FETCH_ASSOC);

$cursoSeleccionado = null;
$alumnos = [];

if (!empty($_GET['buscar'])) {
    $idCurso = $_GET['buscar'];

    // Detalles del curso
    $stmt = $pdo->prepare("SELECT C.*, CONCAT(P.Nombre, ' ', P.Apellido) AS Nombre_Profesor FROM Curso C LEFT JOIN Profesor P ON C.DNIP = P.DNI WHERE C.ID_Curso = ?");
    $stmt->execute([$idCurso]);
    $cursoSeleccionado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Alumnos matriculados
    $stmt2 = $pdo->prepare("SELECT A.Nombre, A.Apellido, A.DNI FROM Matricula M JOIN Alumno A ON M.DNIA = A.DNI WHERE M.IDCURSO = ?");
    $stmt2->execute([$idCurso]);
    $alumnos = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Consulta de Cursos</title>
    <link rel="stylesheet" href="css/instituto.css">

</head>

<body>

<?php require "header.php"; ?>

    <div class="breadcrumbs">
        <a href="index.php">Inicio</a> &raquo; <span>Listado por cursos</span>
    </div>

    <main>
        <h1>Consulta de Cursos</h1>

        <form method="get">
            <label class="list" for="buscar">Selecciona un curso:</label>
            <select class="list" name="buscar" id="buscar" onchange="this.form.submit()">
                <option value="">-- Selecciona un curso --</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?= $curso['ID_Curso'] ?>" <?= (isset($_GET['buscar']) && $_GET['buscar'] == $curso['ID_Curso']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($curso['Nombre_Curso']) ?> (<?= $curso['ID_Curso'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($cursoSeleccionado): ?>
            <div class="resumen-curso">
                <h2><?= htmlspecialchars($cursoSeleccionado['Nombre_Curso']) ?></h2>
                <span><strong>PROFESOR DEL CURSO:</strong> <?= htmlspecialchars($cursoSeleccionado['Nombre_Profesor'] ?? 'No asignado') ?></span>
                <span><strong>Fecha de inicio:</strong> <?= $cursoSeleccionado['fecha_inicio'] ?></span>
                <span><strong>Fecha final:</strong> <?= $cursoSeleccionado['fecha_fin'] ?? 'No asignada' ?></span>
            </div>
            <h3>ALUMNOS MATRICULADOS:</h3>

            <?php if (count($alumnos) > 0): ?>
                <table class="little">
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                    </tr>
                    <?php foreach ($alumnos as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['Nombre']) ?></td>
                            <td><?= htmlspecialchars($a['Apellido']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No hay alumnos matriculados en este curso.</p>
            <?php endif; ?>
        <?php elseif (isset($_GET['buscar'])): ?>
            <p><strong>Curso no encontrado.</strong></p>
        <?php endif; ?>

    </main>

 <footer><?php require "footer.php"; ?></footer>

     <a class="btnUP" href="#nav"><img src="img/up.png" alt="haz click para ir a inicio de página"></a>
    
</body>

</html>
