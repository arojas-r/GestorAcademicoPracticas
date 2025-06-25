<?php
require 'conexion.php';
$pdo = getPDO();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Eliminar profesor
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $dni = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM Profesor WHERE DNI = :dni");
    $stmt->execute([':dni' => $dni]);
    header('Location: profesores.php');
    exit;
}

// Añadir o modificar profesor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = $_POST['DNI'];
    $nombre = $_POST['Nombre'];
    $apellido = $_POST['Apellido'];
    $telefono = $_POST['Teléfono'];
    $fechaAlta = $_POST['Fecha_Alta'];
    $estado = strtoupper($_POST['Estado_Profesor']);
    $fechaBaja = $_POST['Fecha_Baja'] ?: null;
    $is_edit = $_POST['is_edit'] ?? '';

    if ($is_edit) {
        // Modificar
        $sql = "UPDATE Profesor SET Nombre = :nombre, Apellido = :apellido, Telefono = :telefono, fecha_alta = :fecha_alta, Estado_Profesor = :estado, fecha_baja = :fecha_baja WHERE DNI = :dni";
    } else {
        // Verificar si el DNI ya existe antes de insertar
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM Profesor WHERE DNI = :dni");
        $stmt_check->execute([':dni' => $dni]);
        if ($stmt_check->fetchColumn() > 0) {
            echo "<script>alert('El DNI ya existe. No se puede registrar el profesor.'); window.history.back();</script>";
            exit;
        }

        // Insertar nuevo
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

// Obtener listado
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
    <link rel="stylesheet" href="estilos.css">
</head>

<header><?php require "header.php"; ?> </header>

<body>
    <h1>Gestión de Profesores</h1>

    <div class="search-container">
        <form action="" method="GET" style="display: flex; width: 100%;">
            <input type="text" name="search_dni" placeholder="Buscar profesor por DNI, Nombre o Apellido..." value="<?= htmlspecialchars($search) ?>">
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
                <th>Fecha de Alta</th>
                <th>Estado</th>
                <th>Fecha de Baja</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($profesores)): ?>
                <tr><td colspan="8">No se encontraron profesores.</td></tr>
            <?php else: ?>
                <?php foreach ($profesores as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['DNI']) ?></td>
                        <td><?= htmlspecialchars($p['Nombre']) ?></td>
                        <td><?= htmlspecialchars($p['Apellido']) ?></td>
                        <td><?= htmlspecialchars($p['Telefono']) ?></td>
                        <td><?= htmlspecialchars($p['fecha_alta']) ?></td>
                        <td><?= htmlspecialchars($p['Estado_Profesor']) ?></td>
                        <td><?= htmlspecialchars($p['fecha_baja'] ?? '-') ?></td>
                        <td class="actions">
                            <button class="btn btn-delete" onclick="if(confirm('¿Seguro?')) location.href='?action=delete&id=<?= $p['DNI'] ?>'">Eliminar</button>
                            <button class="btn btn-modify" onclick='showForm(<?= json_encode($p) ?>)'>Modificar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <button class="btn btn-add" onclick="showForm()">Ingresar nuevo profesor</button>

    <!-- Modal -->
    <div id="modalOverlay" class="modal-overlay">
        <div class="modal-content">
            <span class="close-button" onclick="hideForm()">&times;</span>
            <h2 id="formTitle">Añadir/Modificar Profesor</h2>
            <form id="profesorForm" method="POST">
                <input type="hidden" id="is_edit" name="is_edit">

                <label for="DNI">DNI:</label>
                <input type="text" id="DNI" name="DNI" required>

                <label for="Nombre">Nombre:</label>
                <input type="text" id="Nombre" name="Nombre" required>

                <label for="Apellido">Apellido:</label>
                <input type="text" id="Apellido" name="Apellido" required>

                <label for="Teléfono">Teléfono:</label>
                <input type="text" id="Teléfono" name="Teléfono" required>

                <label for="Fecha_Alta">Fecha de Alta:</label>
                <input type="date" id="Fecha_Alta" name="Fecha_Alta" required>

                <label for="Estado_Profesor">Estado:</label>
                <select id="Estado_Profesor" name="Estado_Profesor" required>
                    <option value="Alta">ALTA</option>
                    <option value="Baja">BAJA</option>
                </select>

                <label for="Fecha_Baja">Fecha de Baja:</label>
                <input type="date" id="Fecha_Baja" name="Fecha_Baja">

                <button type="submit" id="submitButton">Guardar</button>
                <button type="button" class="btn btn-cancel" onclick="hideForm()">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function showForm(p = null) {
            document.getElementById('modalOverlay').style.display = 'flex';
            const form = document.getElementById('profesorForm');
            form.reset();

            if (p) {
                document.getElementById('formTitle').textContent = 'Modificar Profesor';
                document.getElementById('submitButton').textContent = 'Modificar';
                document.getElementById('is_edit').value = p.DNI;
                document.getElementById('DNI').value = p.DNI;
                document.getElementById('DNI').readOnly = true;
                document.getElementById('Nombre').value = p.Nombre;
                document.getElementById('Apellido').value = p.Apellido;
                document.getElementById('Teléfono').value = p.Telefono;
                document.getElementById('Fecha_Alta').value = p.fecha_alta;
                document.getElementById('Estado_Profesor').value = p.Estado_Profesor;
                document.getElementById('Fecha_Baja').value = p.fecha_baja;
            } else {
                document.getElementById('formTitle').textContent = 'Añadir Profesor';
                document.getElementById('submitButton').textContent = 'Añadir';
                document.getElementById('DNI').readOnly = false;
            }
        }

        function hideForm() {
            document.getElementById('modalOverlay').style.display = 'none';
        }

        document.getElementById('modalOverlay').addEventListener('click', function (e) {
            if (e.target === this) hideForm();
        });
    </script>
<div style="height: 50px;"></div>
</body>
<footer><?php require 'footer.php'; ?></footer>   
</html>
