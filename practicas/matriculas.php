<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$pdo = getPDO();

// Inicializar mensaje
$mensaje = '';

// Procesar eliminación
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM Matricula WHERE ID_MAT = ?");
    $stmt->execute([$_GET['id']]);
    header('Location: matriculas.php');
    exit();
}

// Procesar alta o modificación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_matricula'] ?? '';
    $idCurso = $_POST['IDCURSO'] ?? '';
    $dniAlumno = $_POST['DNIA'] ?? '';
    $estado = strtoupper($_POST['Estado_Matricula']) ?? 'ALTA';

    if (!empty($id)) {
        $stmt = $pdo->prepare("UPDATE Matricula SET IDCURSO=?, DNIA=?, Estado_Matricula=? WHERE ID_MAT=?");
        $stmt->execute([$idCurso, $dniAlumno, $estado, $id]);
        header('Location: matriculas.php');
        exit();
    } else {
        // Evitar duplicado
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Matricula WHERE IDCURSO=? AND DNIA=?");
        $stmt->execute([$idCurso, $dniAlumno]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO Matricula (IDCURSO, DNIA, Estado_Matricula) VALUES (?, ?, ?)");
            $stmt->execute([$idCurso, $dniAlumno, $estado]);
            header('Location: matriculas.php');
            exit();
        } else {
            $mensaje = "⚠️ Este alumno ya está inscrito en este curso.";
        }
    }
}

// Filtro por alumno
if (!empty($_GET['search_dni'])) {
    $stmt = $pdo->prepare("SELECT M.*, C.Nombre_Curso, A.Nombre AS Nombre_Alumno, A.Apellido FROM Matricula M JOIN Curso C ON M.IDCURSO = C.ID_Curso JOIN Alumno A ON M.DNIA = A.DNI WHERE A.DNI = ?");
    $stmt->execute([$_GET['search_dni']]);
} else {
    $stmt = $pdo->query("SELECT M.*, C.Nombre_Curso, A.Nombre AS Nombre_Alumno, A.Apellido FROM Matricula M JOIN Curso C ON M.IDCURSO = C.ID_Curso JOIN Alumno A ON M.DNIA = A.DNI");
}
$matriculas = $stmt->fetchAll();

$cursos = $pdo->query("SELECT ID_Curso, Nombre_Curso FROM Curso")->fetchAll();
$alumnos = $pdo->query("SELECT DNI, CONCAT(Nombre, ' ', Apellido) AS NombreCompleto FROM Alumno WHERE Estado_Alumno = 'ALTA'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Matrículas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<header><?php require 'header.php'; ?></header>

<div class="breadcrumbs">
    <a href="#">Inicio</a> &raquo; <span>Gestión de Matrículas</span>
</div>

<main>
<h1>Gestión de Matrículas</h1>

<?php if (!empty($mensaje)): ?>
    <div class="alert" style="color:red; font-weight:bold;"> <?= htmlspecialchars($mensaje) ?> </div>
<?php endif; ?>

<div class="search-container">
    <form method="get">
        <label>Filtrar por alumno:</label>
        <select name="search_dni" onchange="this.form.submit()">
            <option value="">-- Mostrar todos --</option>
            <?php foreach ($alumnos as $al): ?>
                <option value="<?= $al['DNI'] ?>" <?= (isset($_GET['search_dni']) && $_GET['search_dni'] === $al['DNI']) ? 'selected' : '' ?>>
                    <?= $al['NombreCompleto'] ?> (<?= $al['DNI'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th><th>Curso</th><th>Alumno</th><th>Estado</th><th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($matriculas)): ?>
            <tr><td colspan="5">No se encontraron matrículas.</td></tr>
        <?php else: ?>
            <?php foreach ($matriculas as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['ID_MAT']) ?></td>
                    <td><?= htmlspecialchars($m['Nombre_Curso']) ?></td>
                    <td><?= htmlspecialchars($m['Nombre_Alumno'] . ' ' . $m['Apellido']). ' (' . $m['DNIA'] . ')' ?></td>
                    <td><?= htmlspecialchars($m['Estado_Matricula']) ?></td>
                    <td class="actions">
                        <a class="btn btn-delete" href="?action=delete&id=<?= $m['ID_MAT'] ?>" onclick="return confirm('¿Eliminar esta matrícula?')">Eliminar</a>
                        <button class="btn btn-modify" onclick="editarMatricula('<?= $m['ID_MAT'] ?>', '<?= $m['IDCURSO'] ?>', '<?= $m['DNIA'] ?>', '<?= $m['Estado_Matricula'] ?>')">Modificar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<button class="btn btn-add" onclick="document.getElementById('modalOverlay').style.display='flex'; resetForm();">Añadir Matrícula</button>

<div id="modalOverlay" class="modal-overlay">
    <div class="modal-content">
        <span class="close-button" onclick="document.getElementById('modalOverlay').style.display='none';">&times;</span>
        <h2 id="formTitle">Añadir / Modificar Matrícula</h2>
        <form method="post" id="formMatricula">
            <input type="hidden" name="id_matricula" id="id_matricula">
            <label>Curso:<br>
                <select name="IDCURSO" id="IDCURSO" required>
                    <option value="">-- Selecciona un curso --</option>
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['ID_Curso'] ?>"><?= $c['Nombre_Curso'] ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Alumno:<br>
                <select name="DNIA" id="DNIA" required>
                    <option value="">-- Selecciona un alumno --</option>
                    <?php foreach ($alumnos as $a): ?>
                        <option value="<?= $a['DNI'] ?>"><?= $a['NombreCompleto'] ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Estado:<br>
                <select name="Estado_Matricula" id="Estado_Matricula" required>
                    <option value="ALTA">ALTA</option>
                    <option value="BAJA">BAJA</option>
                </select>
            </label>
            <button type="submit">Guardar</button>
            <button type="button" class="btn btn-cancel" onclick="document.getElementById('modalOverlay').style.display='none';">Cancelar</button>
        </form>
    </div>
</div>
</main>

<footer>
    &copy; <?= date('Y') ?> Instituto. Todos los derechos reservados.
</footer>

<script>
function editarMatricula(id, curso, alumno, estado) {
    document.getElementById('modalOverlay').style.display = 'flex';
    document.getElementById('formTitle').textContent = 'Modificar Matrícula';
    document.getElementById('id_matricula').value = id;
    document.getElementById('IDCURSO').value = curso;
    document.getElementById('DNIA').value = alumno;
    document.getElementById('Estado_Matricula').value = estado;
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Añadir Matrícula';
    document.getElementById('formMatricula').reset();
    document.getElementById('id_matricula').value = '';
}
</script>

</body>
</html>
