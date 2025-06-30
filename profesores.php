<?php
require 'conexion.php';
$pdo = getPDO();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Eliminar un solo profesor
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $dni = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM Profesor WHERE DNI = :dni");
    $stmt->execute([':dni' => $dni]);
    header('Location: profesores.php');
    exit;
}

// Eliminar múltiples profesores
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_multiple'])) {
    $dnisToDelete = $_POST['seleccionados'] ?? [];
    if (!empty($dnisToDelete) && is_array($dnisToDelete)) {
        $placeholders = implode(',', array_fill(0, count($dnisToDelete), '?'));
        $stmt = $pdo->prepare("DELETE FROM Profesor WHERE DNI IN ($placeholders)");
        $stmt->execute($dnisToDelete);
    }
    header('Location: profesores.php');
    exit();
}

// Añadir o modificar profesor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['eliminar_multiple'])) {
    $dni = strtoupper($_POST['DNI']);
    $nombre = $_POST['Nombre'];
    $apellido = $_POST['Apellido'];
    $telefono = $_POST['Teléfono'];
    $fechaAlta = $_POST['Fecha_Alta'];
    $estado = strtoupper($_POST['Estado_Profesor']);
    $fechaBaja = $_POST['Fecha_Baja'] ?: null;
    $is_edit = $_POST['is_edit'] ?? '';

    if ($is_edit) {
        $sql = "UPDATE Profesor SET Nombre = :nombre, Apellido = :apellido, Telefono = :telefono, fecha_alta = :fecha_alta, Estado_Profesor = :estado, fecha_baja = :fecha_baja WHERE DNI = :dni";
    } else {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM Profesor WHERE DNI = :dni");
        $stmt_check->execute([':dni' => $dni]);
        if ($stmt_check->fetchColumn() > 0) {
            echo "<script>alert('El DNI ya existe. No se puede registrar el profesor.'); window.history.back();</script>";
            exit;
        }

        $sql = "INSERT INTO Profesor (DNI, Nombre, Apellido, Telefono, fecha_alta, Estado_Profesor, fecha_baja, Cargo)
                VALUES (:dni, :nombre, :apellido, :telefono, :fecha_alta, :estado, :fecha_baja, 'Profesor')";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':dni' => $dni,
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':telefono' => $telefono,
        ':fecha_alta' => $fechaAlta,
        ':estado' => $estado,
        ':fecha_baja' => $fechaBaja
    ]);

    header('Location: profesores.php');
    exit;
}

$search = $_GET['search_dni'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM Profesor WHERE DNI LIKE :dni OR Nombre LIKE :nombre OR Apellido LIKE :apellido");
    $stmt->execute([
        ':dni' => "%$search%",
        ':nombre' => "%$search%",
        ':apellido' => "%$search%"
    ]);
} else {
    $stmt = $pdo->query("SELECT * FROM Profesor");
}
$profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Profesores</title>
    <link rel="stylesheet" href="css/instituto.css">
    <style>
        .main-header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
    </style>
</head>

<body>
<?php require "header.php"; ?>
    <div class="breadcrumbs">
        <a href="index.php">Inicio</a> &raquo; <span>Gestión del alumnado</span>
    </div>
    <main>

        <h1>Gestión de Profesores</h1>

        <div class="search-container">
            <form action="" method="GET" class="global-actions">
                    <input class="buscador-bar" type="text" name="search_dni" placeholder="Buscar profesor..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-search" type="submit">Buscar</button>
            </form>
            <div>
                <button id="btnModificarGlobal" class="btn btn-modify">Modificar</button>
                <form method="POST" id="formEliminarMultiple" style="display:inline;">
                    <input type="hidden" name="eliminar_multiple" value="1">
                    <input type="hidden" name="seleccionados[]" id="seleccionados">
                    <button type="submit" class="btn btn-delete">Eliminar</button>
                </form>
            </div>

        </div>

        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Teléfono</th>
                    <th>Fecha Alta</th>
                    <th>Estado</th>
                    <th>Fecha Baja</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($profesores)): ?>
                    <tr>
                        <td colspan="9">No se encontraron profesores.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($profesores as $p): ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="profesor-checkbox"
                                    value="<?= htmlspecialchars($p['DNI']) ?>"
                                    data-dni="<?= htmlspecialchars($p['DNI'], ENT_QUOTES) ?>"
                                    data-nombre="<?= htmlspecialchars($p['Nombre'], ENT_QUOTES) ?>"
                                    data-apellido="<?= htmlspecialchars($p['Apellido'], ENT_QUOTES) ?>"
                                    data-telefono="<?= htmlspecialchars($p['Telefono'], ENT_QUOTES) ?>"
                                    data-fechaalta="<?= htmlspecialchars($p['fecha_alta'], ENT_QUOTES) ?>"
                                    data-estado="<?= htmlspecialchars($p['Estado_Profesor'], ENT_QUOTES) ?>"
                                    data-fechabaja="<?= htmlspecialchars($p['fecha_baja'], ENT_QUOTES) ?>">
                            </td>
                            <td><?= htmlspecialchars($p['DNI']) ?></td>
                            <td><?= htmlspecialchars($p['Nombre']) ?></td>
                            <td><?= htmlspecialchars($p['Apellido']) ?></td>
                            <td><?= htmlspecialchars($p['Telefono']) ?></td>
                            <td><?= htmlspecialchars($p['fecha_alta']) ?></td>
                            <td><?= htmlspecialchars($p['Estado_Profesor']) ?></td>
                            <td><?= htmlspecialchars($p['fecha_baja'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <button class="btn btn-add" onclick="showForm()">Ingresar nuevo profesor</button>

        <div id="modalOverlay" class="modal-overlay">
            <div class="modal-content">
                <span class="close-button" onclick="hideForm()">&times;</span>
                <h2 id="formTitle">Añadir/Modificar Profesor</h2>
                <form id="profesorForm" method="POST">
                    <input type="hidden" id="is_edit" name="is_edit">

                    <label for="DNI">DNI:</label>
                    <input type="text" id="DNI" name="DNI" required pattern="^[X-Z,x-z,0-9][0-9]{7}[A-Z]$" required>

                    <label for="Nombre">Nombre:</label>
                    <input type="text" id="Nombre" name="Nombre" required>

                    <label for="Apellido">Apellido:</label>
                    <input type="text" id="Apellido" name="Apellido" required>

                    <label for="Teléfono">Teléfono:</label>
                    <input type="text" id="Teléfono" name="Teléfono" required pattern="[0-9]{9}" required>

                    <label for="Fecha_Alta">Fecha de Alta:</label>
                    <input type="date" id="Fecha_Alta" name="Fecha_Alta" required min="2025-06-30">

                    <label for="Estado_Profesor">Estado:</label>
                    <select id="Estado_Profesor" name="Estado_Profesor" required>
                        <option value="Alta">ALTA</option>
                        <option value="Baja">BAJA</option>
                    </select>

                    <label for="Fecha_Baja">Fecha de Baja:</label>
                    <input type="date" id="Fecha_Baja" name="Fecha_Baja" min="2025-06-30">

                    <button type="submit" id="submitButton">Guardar</button>
                    <button type="button" class="btn btn-cancel" onclick="hideForm()">Cancelar</button>
                </form>
            </div>
        </div>

    </main>

 <footer><?php require "footer.php"; ?></footer>

    <a class="btnUP" href="#nav"><img src="img/up.png" alt="haz click para ir a inicio de página"></a>

    <!-- Dentro del mismo archivo que ya tienes, justo antes del cierre de </body> -->

<script>
    function showForm(p = null) {
        document.getElementById('modalOverlay').style.display = 'flex';
        const form = document.getElementById('profesorForm');
        form.reset();
        document.getElementById('is_edit').value = '';

        if (p) {
            document.getElementById('formTitle').textContent = 'Modificar Profesor';
            document.getElementById('submitButton').textContent = 'Modificar';
            document.getElementById('is_edit').value = p.dni;
            document.getElementById('DNI').value = p.dni;
            document.getElementById('DNI').readOnly = true;
            document.getElementById('Nombre').value = p.nombre;
            document.getElementById('Apellido').value = p.apellido;
            document.getElementById('Teléfono').value = p.telefono;
            document.getElementById('Fecha_Alta').value = p.fechaalta;
            document.getElementById('Estado_Profesor').value = p.estado;
            document.getElementById('Fecha_Baja').value = p.fechabaja;
        } else {
            document.getElementById('formTitle').textContent = 'Añadir Profesor';
            document.getElementById('submitButton').textContent = 'Añadir';
            document.getElementById('DNI').readOnly = false;
        }

        // Actualizar restricción de Fecha_Baja cuando se abre el formulario
        actualizarRestriccionFechaBaja();
    }

    function hideForm() {
        document.getElementById('modalOverlay').style.display = 'none';
    }

    document.getElementById('modalOverlay').addEventListener('click', function(e) {
        if (e.target === this) hideForm();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const btnModificar = document.getElementById('btnModificarGlobal');
        const checkboxes = document.querySelectorAll('.profesor-checkbox');

        btnModificar.addEventListener('click', function () {
            const seleccionados = Array.from(checkboxes).filter(cb => cb.checked);
            if (seleccionados.length !== 1) {
                alert('Por favor, seleccione una única fila para modificar.');
                return;
            }

            const cb = seleccionados[0];
            showForm({
                dni: cb.dataset.dni,
                nombre: cb.dataset.nombre,
                apellido: cb.dataset.apellido,
                telefono: cb.dataset.telefono,
                fechaalta: cb.dataset.fechaalta,
                estado: cb.dataset.estado,
                fechabaja: cb.dataset.fechabaja
            });
        });

        const formEliminar = document.getElementById('formEliminarMultiple');
        formEliminar.addEventListener('submit', function(e) {
            const seleccionados = Array.from(checkboxes).filter(cb => cb.checked);
            if (seleccionados.length === 0) {
                alert('Por favor, seleccione al menos un profesor para eliminar.');
                e.preventDefault();
                return;
            }

            const confirmacion = confirm('¿Está seguro de que desea eliminar los profesores seleccionados?');
            if (!confirmacion) {
                e.preventDefault();
                return;
            }

            const inputHidden = document.getElementById('seleccionados');
            inputHidden.value = seleccionados.map(cb => cb.value);
        });

        // === VALIDACIÓN DE FECHAS ===
        const fechaAltaInput = document.getElementById('Fecha_Alta');
        const fechaBajaInput = document.getElementById('Fecha_Baja');

        fechaAltaInput.addEventListener('change', actualizarRestriccionFechaBaja);

        function actualizarRestriccionFechaBaja() {
            const fechaAlta = fechaAltaInput.value;
            if (fechaAlta) {
                fechaBajaInput.min = fechaAlta;
            } else {
                fechaBajaInput.min = "2025-06-30";
            }
        }
    });
</script>

</body>

</html>
