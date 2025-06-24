<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de profesores</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <div class="logo"><a href="index.php">📚 Instituto</a></div>
        <nav>
            <a href="alumnos.php">Alumnos</a>
            <a href="profesores.php">Profesores</a>
            <a href="cursos.php">Cursos</a>
            <a href="matriculas.php">Matrículas</a>
            <a href="consultas.php">Consultar</a>
        </nav>
    </header>
    <h1>Gestión de Profesores</h1>

    <?php
    session_start();

    if (!isset($_SESSION['profesor'])) {
        $_SESSION['profesor'] = [
            [
                'DNI' => "45983322C",
                'Nombre' => 'Benito',
                'Apellido' => 'Cuerdo',
                'Teléfono' => '655732493',
                'Estado_Profesor' => 'Alta',
                'Fecha_Alta' => '2026-03-01',
                'Fecha_Baja' => ''
            ],
            [
                'DNI' => "12345678A",
                'Nombre' => 'Ana',
                'Apellido' => 'García',
                'Teléfono' => '600112233',
                'Estado_Profesor' => 'Alta',
                'Fecha_Alta' => '2025-09-01',
                'Fecha_Baja' => ''
            ]
        ];
    }

    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id_to_delete = $_GET['id'];
        $profesores_actualizados = [];
        foreach ($_SESSION['profesor'] as $profesor) {
            if ($profesor['DNI'] !== $id_to_delete) {
                $profesores_actualizados[] = $profesor;
            }
        }
        $_SESSION['profesor'] = $profesores_actualizados;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $dni = htmlspecialchars($_POST['DNI']);
        $nombre = htmlspecialchars($_POST['Nombre']);
        $apellido = htmlspecialchars($_POST['Apellido']);
        $telefono = htmlspecialchars($_POST['Teléfono']);
        $fecha_alta = htmlspecialchars($_POST['Fecha_Alta']);
        $estado_profesor = in_array($_POST['Estado_Profesor'], ['Alta', 'Baja']) ? $_POST['Estado_Profesor'] : 'Alta';
        $fecha_baja = htmlspecialchars($_POST['Fecha_Baja']);
        
        $is_edit = isset($_POST['is_edit']) && !empty($_POST['is_edit']);
        
        if ($is_edit) {
            foreach ($_SESSION['profesor'] as &$profesor) {
                if ($profesor['DNI'] === $dni) {
                    $profesor['Nombre'] = $nombre;
                    $profesor['Apellido'] = $apellido;
                    $profesor['Teléfono'] = $telefono;
                    $profesor['Fecha_Alta'] = $fecha_alta;
                    $profesor['Estado_Profesor'] = $estado_profesor;
                    $profesor['Fecha_Baja'] = $fecha_baja;
                    break;
                }
            }
            unset($profesor);
        } else {
            $_SESSION['profesor'][] = [
                'DNI' => $dni,
                'Nombre' => $nombre,
                'Apellido' => $apellido,
                'Teléfono' => $telefono,
                'Fecha_Alta' => $fecha_alta,
                'Estado_Profesor' => $estado_profesor,
                'Fecha_Baja' => $fecha_baja
            ];
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    $profesores_filtrados = $_SESSION['profesor'];

    if (isset($_GET['search_dni']) && !empty($_GET['search_dni'])) {
        $search_dni = $_GET['search_dni'];
        $profesores_filtrados = array_filter($_SESSION['profesor'], function($profesor) use ($search_dni) {
            return stristr($profesor['DNI'], $search_dni) || stristr($profesor['Nombre'], $search_dni) || stristr($profesor['Apellido'], $search_dni);
        });
    }
    ?>

    <div class="search-container">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" style="display: flex; width: 100%;">
            <input type="text" name="search_dni" placeholder="Buscar profesor por DNI, Nombre o Apellido..." value="<?php echo isset($_GET['search_dni']) ? htmlspecialchars($_GET['search_dni']) : ''; ?>">
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
                <th>Estado del Profesor</th>
                <th>Fecha de Baja</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($profesores_filtrados)): ?>
                <tr>
                    <td colspan="8">No se encontraron profesores.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($profesores_filtrados as $profesor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($profesor['DNI']); ?></td>
                        <td><?php echo htmlspecialchars($profesor['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($profesor['Apellido']); ?></td>
                        <td><?php echo htmlspecialchars($profesor['Teléfono'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($profesor['Fecha_Alta']); ?></td>
                        <td><?php echo htmlspecialchars($profesor['Estado_Profesor']); ?></td>
                        <td><?php echo htmlspecialchars($profesor['Fecha_Baja'] ?? '-'); ?></td>
                        <td class="actions">
                            <button class="btn btn-delete" onclick="if(confirm('¿Estás seguro de eliminar este profesor?')) { window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>?action=delete&id=<?php echo $profesor['DNI']; ?>'; }">Eliminar datos</button>
                            <button class="btn btn-modify" onclick="showForm(<?php echo htmlspecialchars(json_encode($profesor)); ?>)">Modificar datos</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <button class="btn btn-add" onclick="showForm()">Dar de Alta</button>

    <div id="modalOverlay" class="modal-overlay">
        <div class="modal-content">
            <span class="close-button" onclick="hideForm()">&times;</span>
            <h2 id="formTitle">Gestionar profesorado</h2>
            <form id="profesorForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <input type="hidden" id="is_edit" name="is_edit" value="">

                <label for="DNI">DNI:</label>
                <input type="text" id="DNI" name="DNI" required>

                <label for="Nombre">Nombre:</label>
                <input type="text" id="Nombre" name="Nombre" required>

                <label for="Apellido">Apellido:</label>
                <input type="text" id="Apellido" name="Apellido" required>
                
                <label for="Teléfono">Teléfono:</label>
                <input type="text " id="Teléfono" name="Teléfono" required>

                <label for="Fecha_Alta">Fecha de Alta:</label>
                <input type="date" id="Fecha_Alta" name="Fecha_Alta" required>

                <label for="Estado_Profesor">Estado del Profesor:</label>
                <select id="Estado_Profesor" name="Estado_Profesor" required>
                    <option value="Alta">Alta</option>
                    <option value="Baja">Baja</option>
                </select>

                <label for="Fecha_Baja">Fecha de Baja:</label>
                <input type="date" id="Fecha_Baja" name="Fecha_Baja">

                <button type="submit" id="submitButton">Guardar</button>
                <button type="button" class="btn btn-cancel" onclick="hideForm()">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function showForm(profesor = null) {
            const modalOverlay = document.getElementById('modalOverlay');
            const form = document.getElementById('profesorForm');
            const submitButton = document.getElementById('submitButton');
            const formTitle = document.getElementById('formTitle');
            const dniInput = document.getElementById('DNI');

            modalOverlay.style.display = 'flex';

            if (profesor) {
                formTitle.textContent = 'Modificar Profesor';
                submitButton.textContent = 'Modificar';
                
                document.getElementById('is_edit').value = profesor.DNI;
                dniInput.value = profesor.DNI;
                dniInput.readOnly = true;
                
                document.getElementById('Nombre').value = profesor.Nombre;
                document.getElementById('Apellido').value = profesor.Apellido;
                document.getElementById('Teléfono').value = profesor.Teléfono ?? '';
                document.getElementById('Fecha_Alta').value = profesor.Fecha_Alta;
                document.getElementById('Estado_Profesor').value = profesor.Estado_Profesor;
                document.getElementById('Fecha_Baja').value = profesor.Fecha_Baja ?? '';
                
            } else {
                formTitle.textContent = 'Añadir Profesor';
                submitButton.textContent = 'Añadir';
                form.reset();
                document.getElementById('is_edit').value = '';
                dniInput.readOnly = false;
                document.getElementById('Estado_Profesor').value = 'Alta';
            }
        }

        function hideForm() {
            document.getElementById('modalOverlay').style.display = 'none';
        }

        document.getElementById('modalOverlay').addEventListener('click', function(event) {
            if (event.target === this) {
                hideForm();
            }
        });
    </script>
    <footer>
    &copy; <?php echo date("Y"); ?> Instituto de Educación Superior. Todos los derechos reservados.
    </footer>
</body>


</html>