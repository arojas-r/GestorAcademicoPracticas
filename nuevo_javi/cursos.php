<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
$pdo = getPDO();

// MODIFICADO: Procesar eliminación única (se mantiene por si se usa en otro lado, pero la nueva lógica es delete_multiple)
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM Curso WHERE ID_Curso = ?");
    $stmt->execute([$_GET['id']]);
    header('Location: cursos.php');
    exit();
}

// NUEVO: Procesar eliminación múltiple
if (isset($_GET['action'], $_GET['ids']) && $_GET['action'] === 'delete_multiple') {
    $idsToDelete = explode(',', $_GET['ids']);
    // Nos aseguramos de que los IDs son válidos para evitar inyección SQL.
    if (!empty($idsToDelete)) {
        $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
        $stmt = $pdo->prepare("DELETE FROM Curso WHERE ID_Curso IN ($placeholders)");
        $stmt->execute($idsToDelete);
    }
    header('Location: cursos.php');
    exit();
}


// Procesar alta o modificación (Sin cambios en esta sección)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_curso'] ?? '';
    $Nombre_Curso = $_POST['Nombre_Curso'] ?? '';
    $Descripcion = $_POST['Descripcion'] ?? '';
    $DNIP = $_POST['DNIP'] ?? '';
    $Estado_Curso = strtoupper($_POST['Estado_Curso']) ?? 'ALTA';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';

    if (!empty($id)) {
        $stmt = $pdo->prepare("UPDATE Curso SET Nombre_Curso=?, Descripcion=?, DNIP=?, Estado_Curso=?, fecha_inicio=?, fecha_fin=? WHERE ID_Curso=?");
        $stmt->execute([$Nombre_Curso, $Descripcion, $DNIP, $Estado_Curso, $fecha_inicio, $fecha_fin, $id]);
    } else {
        $stmt = $pdo->query("SELECT ID_Curso FROM Curso WHERE ID_Curso LIKE 'IFCD%' ORDER BY ID_Curso DESC LIMIT 1");
        $lastId = $stmt->fetchColumn();
        $numero = ($lastId && preg_match('/^IFCD(\d{4})$/', $lastId, $m)) ? ((int)$m[1] + 1) : 1;
        $nuevo_id = 'IFCD' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare("INSERT INTO Curso (ID_Curso, Nombre_Curso, Descripcion, DNIP, Estado_Curso, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nuevo_id, $Nombre_Curso, $Descripcion, $DNIP, $Estado_Curso, $fecha_inicio, $fecha_fin]);
    }

    header('Location: cursos.php');
    exit();
}

// Listado cursos (Sin cambios en esta sección)
if (!empty($_GET['search_id'])) {
    $stmt = $pdo->prepare("SELECT Curso.*, CONCAT(Profesor.Nombre, ' ', Profesor.Apellido) AS Profesor_Nombre FROM Curso LEFT JOIN Profesor ON Curso.DNIP = Profesor.DNI WHERE Curso.ID_Curso = ?");
    $stmt->execute([$_GET['search_id']]);
} else {
    $stmt = $pdo->query("SELECT Curso.*, CONCAT(Profesor.Nombre, ' ', Profesor.Apellido) AS Profesor_Nombre FROM Curso LEFT JOIN Profesor ON Curso.DNIP = Profesor.DNI");
}
$cursos = $stmt->fetchAll();

// Profesores disponibles (Sin cambios en esta sección)
$stmt = $pdo->query("SELECT DNI, CONCAT(Nombre, ' ', Apellido) AS NombreCompleto FROM Profesor WHERE Estado_Profesor = 'ALTA'");
$profesoresDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Cursos</title>
    <link rel="stylesheet" href="instituto.css">
    <style>
        .main-header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            /* Para que sea responsive si no cabe */
        }
    </style>
</head>

<body>
    <?php require 'header.php'; ?>
    <div class="breadcrumbs">
        <a href="index.php">Inicio</a> &raquo; <span>Gestión de los cursos</span>
    </div>

    <main>
        <h1>Gestión de Cursos</h1>

        <div class="search-container">
            <form method="get" class="global-actions">
                <input class="buscador-bar" type="text" name="search_id" placeholder="Buscar por ID">
                <button class="btn btn-search" type="submit">Buscar</button>
            </form>
            <div>
                <button id="btnModificarGlobal" class="btn btn-modify">Modificar</button>
                <button id="btnEliminarGlobal" class="btn btn-delete">Eliminar</button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Profesor</th>
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cursos)): ?>
                    <tr>
                        <td colspan="8">No se encontraron cursos.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cursos as $curso): ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="curso-checkbox"
                                    value="<?= htmlspecialchars($curso['ID_Curso']) ?>"
                                    data-id="<?= htmlspecialchars($curso['ID_Curso'], ENT_QUOTES) ?>"
                                    data-nombre="<?= htmlspecialchars($curso['Nombre_Curso'], ENT_QUOTES) ?>"
                                    data-descripcion="<?= htmlspecialchars($curso['Descripcion'], ENT_QUOTES) ?>"
                                    data-dnip="<?= htmlspecialchars($curso['DNIP'], ENT_QUOTES) ?>"
                                    data-estado="<?= htmlspecialchars($curso['Estado_Curso'], ENT_QUOTES) ?>"
                                    data-inicio="<?= htmlspecialchars($curso['fecha_inicio'], ENT_QUOTES) ?>"
                                    data-fin="<?= htmlspecialchars($curso['fecha_fin'], ENT_QUOTES) ?>">
                            </td>
                            <td><?= htmlspecialchars($curso['ID_Curso']) ?></td>
                            <td><?= htmlspecialchars($curso['Nombre_Curso']) ?></td>
                            <td><?= htmlspecialchars($curso['Descripcion']) ?></td>
                            <td><?= htmlspecialchars($curso['Profesor_Nombre']) ?></td>
                            <td><?= htmlspecialchars($curso['Estado_Curso']) ?></td>
                            <td><?= htmlspecialchars($curso['fecha_inicio']) ?></td>
                            <td><?= htmlspecialchars($curso['fecha_fin']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <button class="btn btn-add" onclick="document.getElementById('modalOverlay').style.display='flex'; resetForm();">Añadir Curso</button>

        <div id="modalOverlay" class="modal-overlay">
            <div class="modal-content">
                <span class="close-button" onclick="document.getElementById('modalOverlay').style.display='none';">&times;</span>
                <h2 id="formTitle">Añadir / Modificar Curso</h2>
                <form method="post" id="formCurso">
                    <input type="hidden" name="id_curso" id="id_curso">
                    <label>Nombre del Curso:<br><input type="text" name="Nombre_Curso" id="Nombre_Curso" required></label>
                    <label>Descripción:<br><input type="text" name="Descripcion" id="Descripcion" required></label>
                    <label>Profesor asignado:<br>
                        <select name="DNIP" id="DNIP" required>
                            <option value="">-- Selecciona un profesor --</option>
                            <?php foreach ($profesoresDisponibles as $prof): ?>
                                <option value="<?= $prof['DNI'] ?>"><?= $prof['NombreCompleto'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Estado:<br>
                        <select name="Estado_Curso" id="Estado_Curso">
                            <option value="Alta">Alta</option>
                            <option value="Baja">Baja</option>
                        </select>
                    </label>
                    <label>Fecha Inicio:<br><input type="date" name="fecha_inicio" id="fecha_inicio" required></label>
                    <label>Fecha Fin:<br><input type="date" name="fecha_fin" id="fecha_fin" required></label>
                    <button type="submit" id="submitButton">Guardar</button>
                    <button type="button" class="btn btn-cancel" onclick="document.getElementById('modalOverlay').style.display='none';">Cancelar</button>
                </form>
            </div>
        </div>
    </main>

    <footer><?php require 'footer.php'; ?></footer>

    <a class="btnUP" href="#nav"><img src="up.png" alt="haz click para ir a inicio de página"></a>

    <script>
        // Funciones existentes
        function editarCurso(id, nombre, descripcion, dnip, estado, inicio, fin) {
            document.getElementById('modalOverlay').style.display = 'flex';
            document.getElementById('formTitle').textContent = 'Modificar Curso';
            document.getElementById('id_curso').value = id;
            document.getElementById('Nombre_Curso').value = nombre;
            document.getElementById('Descripcion').value = descripcion;
            document.getElementById('DNIP').value = dnip;
            document.getElementById('Estado_Curso').value = estado;
            document.getElementById('fecha_inicio').value = inicio;
            document.getElementById('fecha_fin').value = fin;
        }

        function resetForm() {
            document.getElementById('formTitle').textContent = 'Añadir Curso';
            document.getElementById('formCurso').reset();
            document.getElementById('id_curso').value = '';
        }

        // NUEVO: Lógica para los botones globales
        document.addEventListener('DOMContentLoaded', function() {
            const btnModificar = document.getElementById('btnModificarGlobal');
            const btnEliminar = document.getElementById('btnEliminarGlobal');

            btnModificar.addEventListener('click', function() {
                const seleccionados = document.querySelectorAll('.curso-checkbox:checked');

                if (seleccionados.length !== 1) {
                    alert('Por favor, seleccione una única fila para modificar.');
                    return;
                }

                const checkbox = seleccionados[0];
                // Usamos la función existente 'editarCurso' con los datos del checkbox
                editarCurso(
                    checkbox.dataset.id,
                    checkbox.dataset.nombre,
                    checkbox.dataset.descripcion,
                    checkbox.dataset.dnip,
                    checkbox.dataset.estado,
                    checkbox.dataset.inicio,
                    checkbox.dataset.fin
                );
            });

            btnEliminar.addEventListener('click', function() {
                const seleccionados = document.querySelectorAll('.curso-checkbox:checked');

                if (seleccionados.length === 0) {
                    alert('Por favor, seleccione al menos una fila para eliminar.');
                    return;
                }

                const confirmacion = confirm(`¿Está seguro de que desea eliminar ${seleccionados.length} curso(s) seleccionado(s)?`);

                if (confirmacion) {
                    const ids = Array.from(seleccionados).map(cb => cb.value);
                    // Redirigimos a la página con los IDs a eliminar
                    window.location.href = `?action=delete_multiple&ids=${ids.join(',')}`;
                }
            });
        });
    </script>

</body>

</html>