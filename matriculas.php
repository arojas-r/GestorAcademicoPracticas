<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$pdo = getPDO();

// Inicializar mensaje
$mensaje = '';

// Procesar eliminación múltiple desde formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_multiple'])) {
    $idsToDelete = $_POST['seleccionados'] ?? [];
    if (!empty($idsToDelete) && is_array($idsToDelete)) {
        $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
        $stmt = $pdo->prepare("DELETE FROM Matricula WHERE ID_MAT IN ($placeholders)");
        $stmt->execute($idsToDelete);
    }
    header('Location: matriculas.php');
    exit();
}

// Procesar alta o modificación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['eliminar_multiple'])) {
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
    <link rel="stylesheet" href="css/instituto.css">
</head>

<body>
<?php require "header.php"; ?>
<div class="breadcrumbs">
    <a href="#">Inicio</a> &raquo; <span>Gestión de Matrículas</span>
</div>

<main>
    <h1>Gestión de Matrículas</h1>

    <div class="search-container">
        <form class="global-actions list" method="get">
            <label>Filtrar por alumno:</label>
            <select class="list" name="search_dni" onchange="this.form.submit()">
                <option value="">-- Mostrar todos --</option>
                <?php foreach ($alumnos as $al): ?>
                    <option value="<?= $al['DNI'] ?>" <?= (isset($_GET['search_dni']) && $_GET['search_dni'] === $al['DNI']) ? 'selected' : '' ?>>
                        <?= $al['NombreCompleto'] ?> (<?= $al['DNI'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <div>
            <form id="eliminarForm" method="POST" onsubmit="return confirmarEliminacion();">
                <input type="hidden" name="eliminar_multiple" value="1">
                <input type="hidden" name="seleccionados[]" id="idsSeleccionados">
                <button type="submit" class="btn btn-delete">Eliminar</button>
                <button type="button" class="btn btn-modify" onclick="modificarSeleccionado()">Modificar</button>
            </form>
        </div>
    </div>

    <form id="matriculasForm">
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Curso</th>
                    <th>Alumno</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($matriculas)): ?>
                    <tr>
                        <td colspan="5">No se encontraron matrículas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($matriculas as $m): ?>
                        <tr>
                            <td><input type="checkbox" name="selectedMatriculas[]" value="<?= htmlspecialchars($m['ID_MAT']) ?>"
                                       data-idcurso="<?= htmlspecialchars($m['IDCURSO']) ?>"
                                       data-dnialumno="<?= htmlspecialchars($m['DNIA']) ?>"
                                       data-estado="<?= htmlspecialchars($m['Estado_Matricula']) ?>">
                            </td>
                            <td><?= htmlspecialchars($m['ID_MAT']) ?></td>
                            <td><?= htmlspecialchars($m['Nombre_Curso']) ?></td>
                            <td><?= htmlspecialchars($m['Nombre_Alumno'] . ' ' . $m['Apellido']) . ' (' . $m['DNIA'] . ')' ?></td>
                            <td><?= htmlspecialchars($m['Estado_Matricula']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </form>

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

<footer><?php require "footer.php"; ?></footer>
 <a class="btnUP" href="#nav"><img src="img/up.png" alt="haz click para ir a inicio de página"></a>
<script>
    function confirmarEliminacion() {
        const seleccionados = Array.from(document.querySelectorAll('input[name="selectedMatriculas[]"]:checked')).map(cb => cb.value);
        if (seleccionados.length === 0) {
            alert('Por favor, selecciona al menos una matrícula para eliminar.');
            return false;
        }
        if (confirm(`¿Estás seguro de que deseas eliminar ${seleccionados.length} matrícula(s)?`)) {
            document.getElementById('idsSeleccionados').value = seleccionados.join(',');
            return true;
        }
        return false;
    }

    function modificarSeleccionado() {
        const seleccionados = document.querySelectorAll('input[name="selectedMatriculas[]"]:checked');

        if (seleccionados.length === 0) {
            alert('Por favor, selecciona una matrícula para modificar.');
            return;
        }
        if (seleccionados.length > 1) {
            alert('Solo se puede modificar una matrícula a la vez.');
            return;
        }

        const cb = seleccionados[0];
        document.getElementById('modalOverlay').style.display = 'flex';
        document.getElementById('formTitle').textContent = 'Modificar Matrícula';
        document.getElementById('id_matricula').value = cb.value;
        document.getElementById('IDCURSO').value = cb.dataset.idcurso;
        document.getElementById('DNIA').value = cb.dataset.dnialumno;
        document.getElementById('Estado_Matricula').value = cb.dataset.estado;
    }

    function resetForm() {
        document.getElementById('formTitle').textContent = 'Añadir Matrícula';
        document.getElementById('formMatricula').reset();
        document.getElementById('id_matricula').value = '';
    }
</script>

</body>
</html>
