<?php
require 'conexion.php';
$pdo = getPDO();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Eliminar alumno
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['dni'])) {
    $stmt = $pdo->prepare("DELETE FROM Alumno WHERE DNI = ?");
    $stmt->execute([$_GET['dni']]);
    header('Location: alumnos.php');
    exit();
}

// Añadir o modificar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = $_POST['DNI'];
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

// Obtener listado
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
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<?php require 'header.php'; ?>

<h1>Gestión de Alumnos</h1>

<div class="search-container">
    <form method="GET">
        <input type="text" name="search_dni" placeholder="Buscar alumno..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Buscar</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Teléfono</th>
            <th>Fecha Alta</th>
            <th>Estado</th>
            <th>Fecha Baja</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($alumnos)): ?>
            <tr><td colspan="8">No se encontraron alumnos.</td></tr>
        <?php else: ?>
            <?php foreach ($alumnos as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['DNI']) ?></td>
                    <td><?= htmlspecialchars($a['Nombre']) ?></td>
                    <td><?= htmlspecialchars($a['Apellido']) ?></td>
                    <td><?= htmlspecialchars($a['Telefono']) ?></td>
                    <td><?= htmlspecialchars($a['fecha_alta']) ?></td>
                    <td><?= htmlspecialchars($a['Estado_Alumno']) ?></td>
                    <td><?= htmlspecialchars($a['fecha_baja'] ?? '-') ?></td>
                    <td class="actions">
                        <button class="btn btn-delete" onclick="if(confirm('¿Eliminar?')) location.href='?action=delete&dni=<?= $a['DNI'] ?>'">Eliminar</button>
                        <button class="btn btn-modify" onclick='editarAlumno("<?= $a['DNI'] ?>", "<?= $a['Nombre'] ?>", "<?= $a['Apellido'] ?>", "<?= $a['Telefono'] ?>", "<?= $a['fecha_alta'] ?>", "<?= $a['Estado_Alumno'] ?>", "<?= $a['fecha_baja'] ?>")'>Modificar</button>
                    </td>
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
        <form method="post">
            <input type="hidden" name="is_edit" id="is_edit">
            <label>DNI:<br><input type="text" name="DNI" id="DNI" required></label>
            <label>Nombre:<br><input type="text" name="Nombre" id="Nombre" required></label>
            <label>Apellido:<br><input type="text" name="Apellido" id="Apellido" required></label>
            <label>Teléfono:<br><input type="text" name="Telefono" id="Telefono" required></label>
            <label>Fecha Alta:<br><input type="date" name="Fecha_Alta" id="Fecha_Alta" required></label>
            <label>Estado:<br>
                <select name="Estado_Alumno" id="Estado_Alumno">
                    <option value="ALTA">ALTA</option>
                    <option value="BAJA">BAJA</option>
                </select>
            </label>
            <label>Fecha Baja:<br><input type="date" name="Fecha_Baja" id="Fecha_Baja"></label>
            <button type="submit">Guardar</button>
            <button type="button" onclick="ocultarFormulario()">Cancelar</button>
        </form>
    </div>
</div>

<script>
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
</script>

</body>
</html>
