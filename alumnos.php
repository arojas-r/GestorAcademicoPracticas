<?php
require 'conexion.php';

$pdo = getPDO();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Eliminar un solo alumno (se mantiene por compatibilidad)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['dni'])) {
    $stmt = $pdo->prepare("DELETE FROM Alumno WHERE DNI = ?");
    $stmt->execute([$_GET['dni']]);
    header('Location: alumnos.php');
    exit();
}

// NUEVO: Procesar eliminación múltiple
if (isset($_GET['action'], $_GET['dnis']) && $_GET['action'] === 'delete_multiple') {
    $dnisToDelete = explode(',', $_GET['dnis']);
    if (!empty($dnisToDelete)) {
        $placeholders = implode(',', array_fill(0, count($dnisToDelete), '?'));
        $stmt = $pdo->prepare("DELETE FROM Alumno WHERE DNI IN ($placeholders)");
        $stmt->execute($dnisToDelete);
    }
    header('Location: alumnos.php');
    exit();
}


// Añadir o modificar (sin cambios en esta sección)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = strtoupper($_POST['DNI']);
    $nombre = $_POST['Nombre'];
    $apellido = $_POST['Apellido'];
    $telefono = $_POST['Telefono'];
    $fecha_alta = $_POST['Fecha_Alta'];
    $fecha_baja = $_POST['Fecha_Baja'] ?: null;
    $estado = strtoupper($_POST['Estado_Alumno']);
    $is_edit = $_POST['is_edit'] ?? '';

    if ($is_edit) {
        $sql = "UPDATE Alumno SET Nombre=?, Apellido=?, Telefono=?, fecha_alta=?, fecha_baja=?, Estado_Alumno=? WHERE DNI=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $telefono, $fecha_alta, $fecha_baja, $estado, $dni]);
    } else {
        // Validar duplicado
        $check = $pdo->prepare("SELECT COUNT(*) FROM Alumno WHERE DNI=?");
        $check->execute([$dni]);
        if ($check->fetchColumn() > 0) {
            echo "<script>alert('El DNI ya existe.'); window.history.back();</script>";
            exit;
        }

        $sql = "INSERT INTO Alumno (DNI, Cargo, Nombre, Apellido, Telefono, fecha_alta, fecha_baja, Estado_Alumno) VALUES (?, 'Estudiante', ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$dni, $nombre, $apellido, $telefono, $fecha_alta, $fecha_baja, $estado]);
    }

    header('Location: alumnos.php');
    exit();
}

// Obtener listado (sin cambios en esta sección)
$search = $_GET['search_dni'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM Alumno WHERE DNI LIKE ? OR Nombre LIKE ? OR Apellido LIKE ?");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM Alumno");
}
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Alumnos</title>
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
<?php require 'header.php'; ?>

    <div class="breadcrumbs">
        <a href="index.php">Inicio</a> &raquo; <span>Gestión del alumnado</span>
    </div>

    <main>
        <h1>Gestión de Alumnos</h1>

        <div class="search-container">
            <form method="GET" class="global-actions">
                <input class="buscador-bar" type="text" name="search_dni" placeholder="Buscar alumno..." value="<?= htmlspecialchars($search) ?>">
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
                <?php if (empty($alumnos)): ?>
                    <tr>
                        <td colspan="9">No se encontraron alumnos.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($alumnos as $a): ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="alumno-checkbox"
                                    value="<?= htmlspecialchars($a['DNI']) ?>"
                                    data-dni="<?= htmlspecialchars($a['DNI'], ENT_QUOTES) ?>"
                                    data-nombre="<?= htmlspecialchars($a['Nombre'], ENT_QUOTES) ?>"
                                    data-apellido="<?= htmlspecialchars($a['Apellido'], ENT_QUOTES) ?>"
                                    data-telefono="<?= htmlspecialchars($a['Telefono'], ENT_QUOTES) ?>"
                                    data-fechaalta="<?= htmlspecialchars($a['fecha_alta'], ENT_QUOTES) ?>"
                                    data-estado="<?= htmlspecialchars($a['Estado_Alumno'], ENT_QUOTES) ?>"
                                    data-fechabaja="<?= htmlspecialchars($a['fecha_baja'], ENT_QUOTES) ?>">
                            </td>
                            <td><?= htmlspecialchars($a['DNI']) ?></td>
                            <td><?= htmlspecialchars($a['Nombre']) ?></td>
                            <td><?= htmlspecialchars($a['Apellido']) ?></td>
                            <td><?= htmlspecialchars($a['Telefono']) ?></td>
                            <td><?= htmlspecialchars($a['fecha_alta']) ?></td>
                            <td><?= htmlspecialchars($a['Estado_Alumno']) ?></td>
                            <td><?= htmlspecialchars($a['fecha_baja'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <button class="btn btn-add" onclick="mostrarFormulario()">Añadir Alumno</button>

        <div id="modalOverlay" class="modal-overlay" style="display:none;">
            <div class="modal-content">
                <span class="close-button" onclick="ocultarFormulario()">&times;</span>
                <h2 id="formTitle">Añadir Alumno</h2>
                <form method="post" id="formAlumno">
                    <input type="hidden" name="is_edit" id="is_edit">
                    <label>DNI:<br><input type="text" name="DNI" id="DNI" required pattern="^[X-Zx-z0-9][0-9]{7}[A-Z,a-z]$"></label>
                    <label>Nombre:<br><input type="text" name="Nombre" id="Nombre" required></label>
                    <label>Apellido:<br><input type="text" name="Apellido" id="Apellido" required></label>
                    <label>Teléfono:<br><input type="tel" name="Telefono" id="Telefono" required pattern="[0-9]{9,15}"></label>
                    <label>Fecha Alta:<br><input type="date" name="Fecha_Alta" id="Fecha_Alta" required min="2025-06-30"></label>
                    <label>Estado:<br>
                        <select name="Estado_Alumno" id="Estado_Alumno">
                            <option value="ALTA">ALTA</option>
                            <option value="BAJA">BAJA</option>
                        </select>
                    </label>
                    <label>Fecha Baja:<br><input type="date" name="Fecha_Baja" id="Fecha_Baja" min="2025-06-30"></label>
                    <button type="submit">Guardar</button>
                    <button type="button" class="btn btn-cancel" onclick="ocultarFormulario()">Cancelar</button>
                </form>
            </div>
        </div>
    </main>

 <footer><?php require "footer.php"; ?></footer>

 <a class="btnUP" href="#nav"><img src="img/up.png" alt="haz click para ir a inicio de página"></a>

<script>
    // Funciones existentes
    function mostrarFormulario() {
        document.getElementById('modalOverlay').style.display = 'flex';
        document.getElementById('formTitle').textContent = 'Añadir Alumno';
        document.getElementById('is_edit').value = '';
        document.getElementById('DNI').readOnly = false;
        document.querySelector('form').reset();
    }

    function editarAlumno(dni, nombre, apellido, telefono, fechaAlta, estado, fechaBaja) {
        mostrarFormulario();
        document.getElementById('formTitle').textContent = 'Modificar Alumno';
        document.getElementById('is_edit').value = dni;
        document.getElementById('DNI').value = dni;
        document.getElementById('DNI').readOnly = true;
        document.getElementById('Nombre').value = nombre;
        document.getElementById('Apellido').value = apellido;
        document.getElementById('Telefono').value = telefono;
        document.getElementById('Fecha_Alta').value = fechaAlta;
        document.getElementById('Estado_Alumno').value = estado;
        document.getElementById('Fecha_Baja').value = fechaBaja;
    }

    function ocultarFormulario() {
        document.getElementById('modalOverlay').style.display = 'none';
    }

    // Validación y lógica global
    document.addEventListener('DOMContentLoaded', function() {
        const btnModificar = document.getElementById('btnModificarGlobal');
        const btnEliminar = document.getElementById('btnEliminarGlobal');
        const formAlumno = document.getElementById('formAlumno');
        const fechaAltaInput = document.getElementById('Fecha_Alta');
        const fechaBajaInput = document.getElementById('Fecha_Baja');

        btnModificar.addEventListener('click', function() {
            const seleccionados = document.querySelectorAll('.alumno-checkbox:checked');

            if (seleccionados.length !== 1) {
                alert('Por favor, seleccione una única fila para modificar.');
                return;
            }

            const checkbox = seleccionados[0];
            editarAlumno(
                checkbox.dataset.dni,
                checkbox.dataset.nombre,
                checkbox.dataset.apellido,
                checkbox.dataset.telefono,
                checkbox.dataset.fechaalta,
                checkbox.dataset.estado,
                checkbox.dataset.fechabaja
            );
        });

        btnEliminar.addEventListener('click', function() {
            const seleccionados = document.querySelectorAll('.alumno-checkbox:checked');

            if (seleccionados.length === 0) {
                alert('Por favor, seleccione al menos una fila para eliminar.');
                return;
            }

            const confirmacion = confirm(`¿Está seguro de que desea eliminar ${seleccionados.length} alumno(s) seleccionado(s)?`);

            if (confirmacion) {
                const dnis = Array.from(seleccionados).map(cb => cb.value);
                window.location.href = `?action=delete_multiple&dnis=${dnis.join(',')}`;
            }
        });

        // 🔒 Restricción dinámica: Fecha Baja no puede ser menor que Fecha Alta
        fechaAltaInput.addEventListener('change', function () {
            if (fechaAltaInput.value) {
                const minimoAbsoluto = '2025-01-01';
                fechaBajaInput.min = (fechaAltaInput.value > minimoAbsoluto) ? fechaAltaInput.value : minimoAbsoluto;
            }
        });

        formAlumno.addEventListener('submit', function (e) {
            const alta = new Date(fechaAltaInput.value);
            const baja = new Date(fechaBajaInput.value);
            const limite = new Date('2025-01-01');

            if (fechaBajaInput.value) {
                if (baja < limite) {
                    alert('La Fecha de Baja no puede ser anterior al 1 de enero de 2025.');
                    e.preventDefault();
                } else if (baja < alta) {
                    alert('La Fecha de Baja no puede ser anterior a la Fecha de Alta.');
                    e.preventDefault();
                }
            }
        });
    });
</script>

</body>
</html>
