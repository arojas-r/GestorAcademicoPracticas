<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Área del Alumno</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/estilos.css">
</head>
<body>

<main>
    <div class="header-container">
        <h1 class="titulo-alumno">Área del Alumno</h1>
        <div class="btn-group">
            <button  class="btn-toggle" onclick="toggleSection('formAlta')">Dame de Alta</button>
            <button  class="btn-toggle" onclick="toggleSection('formGestion')">Gestionar Datos</button>
        </div>
    </div>

    <p class="descripcion-alumno">Bienvenido al área del alumno. Aquí puedes gestionar tus datos y ver tu progreso.</p>

    <section id="formAlta" class="hidden toggle-section form-alta">
        <form action="" method="post">
            <label for="nombre1">Nombre:</label>
            <input type="text" id="nombre1" name="Nombre" required>

            <label for="apellido1">Apellido:</label>
            <input type="text" id="apellido1" name="Apellido" required>

            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="DNI" required>

            <label for="fecha-inicio">Fecha de Inicio:</label>
            <input type="date" id="fecha-inicio" name="fecha-alta" required><br><br>

             <label for="fecha-fin">Fecha de fin:</label>
            <input type="date" id="fecha-fin" name="fecha-fin" required><br><br>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="Telefono" required>

            

            <button type="submit">Enviar</button>
        </form>
    </section>

    <section id="formGestion" class="hidden toggle-section form-gestion">
        <p class="descripcion-gestion">Aquí puedes gestionar tus datos.</p>
        
    <?php
    // Simulación de una base de datos con un array
    session_start();

    if (!isset($_SESSION['alumnos'])) {
        $_SESSION['alumnos'] = [
            [
                'DNI' => '123456789',
                'Nombre' => 'Anthony',
                'Apellidos' => 'García',
                'Telefono' => '123456789',
                'Fecha_inicio' => '2025-09-01',
                'Estado_del_curso' => 'Alta', // Modificado a 'Alta'
                'Fecha_final' => '2026-03-04',
                'Curso' => 'Desarrollo Web'
            ],
            [
                'DNI' => '234567890',
                'Nombre' => 'Juan',
                'Apellidos' => 'Pérez', 
                'Telefono' => '987654321',
                'Fecha_inicio' => '2025-10-15',
                'Estado_del_curso' => 'Alta', // Modificado a 'Alta'
                'Fecha_final' => '2026-02-25',
                'Curso' => 'Diseño Gráfico Avanzado'
            ],
            [
                'DNI' => '345678901',
                'Nombre' => 'Carlos',
                'Apellidos' => 'Ruíz',
                'Telefono' => '456789123',
                'Fecha_inicio' => '2025-08-01',
                'Estado_del_curso' => 'Baja', // Modificado a 'Baja'
                'Fecha_final' => '2024-12-06',
                'Curso' => 'Desarrollo Web'
            ]
        ];
        $_SESSION['next_id'] = 4;
    }

    // Lógica para eliminar un curso
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id_to_delete = (int)$_GET['id'];
        $_SESSION['alumnos'] = array_filter($_SESSION['alumnos'], function($alumno) use ($id_to_delete) {
            return $alumno['DNI'] !== $id_to_delete;
        });
        $_SESSION['alumnos'] = array_values($_SESSION['alumnos']);
        header('Location: cursos.php');
        exit();
    }

    // Lógica para añadir o modificar un curso
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $dni = isset($_POST['id_curso']) ? (int)$_POST['DNI'] : 0;
        $nombre = htmlspecialchars($_POST['nombre']);
        $apellidos = htmlspecialchars($_POST['apellidos']);
        $telefono = htmlspecialchars($_POST['telefono']);
        $fecha_inicio = htmlspecialchars($_POST['fecha_inicio']);
        // Validar que el estado del curso sea 'Alta' o 'Baja'
        $estado = in_array($_POST['estado_del_curso'], ['Alta', 'Baja']) ? htmlspecialchars($_POST['estado_del_curso']) : 'Alta';
        $fecha_final = htmlspecialchars($_POST['fecha_final']);
        $curso = htmlspecialchars($_POST['Curso']);

        if ($dni > 0) {
            // Modificar curso existente
            foreach ($_SESSION['alumnos'] as &$alumno) {
                if ($alumno['DNI'] === $dni) {
                    $alumno['Nombre'] = $nombre;
                    $alumno['Apellidos'] = $apellidos;
                    $alumno['Telefono'] = $telefono;
                    $alumno['Fecha_inicio'] = $fecha_inicio;
                    $alumno['Estado_del_curso'] = $estado;
                    $alumno['Fecha_final'] = $fecha_final;
                    $alumno['Curso'] = $curso;
                    break;
                }
            }
        } else {
            // Añadir nuevo curso
            $new_id = $_SESSION['next_id']++;
            $_SESSION['alumnos'][] = [
                'DNI' => $dni,
                'Nombre' => $nombre,
                'Apellidos' => $apellidos,
                'Telefono' => $telefono,
                'Fecha_inicio' => $fecha_inicio,
                'Estado_del_curso' => $estado,
                'Fecha_final' => $fecha_final,
                'Curso' => $curso
            ];
        }
        header('Location: alumnos.php');
        exit();
    }

    $alumnos_filtrados = $_SESSION['alumnos'];

    // Lógica para buscar alumnos
    if (isset($_GET['search_id']) && !empty($_GET['search_id'])) {
        $search_id = (int)$_GET['search_id'];
        $alumnos_filtrados = array_filter($_SESSION['alumnos'], function($alumno) use ($search_id) {
            return $alumno['DNI'] === $search_id;   
        });
    }
    ?>

    <div class="search-container">
        <form action="alumnos.php" method="GET" style="display: flex; width: 100%;">
            <input type="text" name="search_id" placeholder="Buscar por DNI..." value="<?php echo isset($_GET['search_id']) ? htmlspecialchars($_GET['search_id']) : ''; ?>">
            <button type="submit">Buscar</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Telefono</th>
                <th>Estado del alumno</th>
                <th>Fecha de alta</th>
                <th>Fecha de baja</th>
                <th>Curso</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($alumnos_filtrados)): ?>
                <tr>
                    <td colspan="9">No se encontraron alumnos.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($alumnos_filtrados as $alumno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($alumno['DNI']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Telefono']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Estado_del_curso']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Fecha_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Fecha_final']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Curso']); ?></td>
                        <td class="actions">
                            <button class="btn btn-delete" onclick="if(confirm('¿Estás seguro de eliminar este alumno?')) { window.location.href='alumnos.php?action=delete&id=<?php echo $alumno['DNI']; ?>'; }">Eliminar datos</button>
                            <button class="btn btn-modify" onclick="showForm(<?php echo htmlspecialchars(json_encode($alumno)); ?>)">Modificar datos</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

  

    <div id="modalOverlay" class="modal-overlay">
        <div class="modal-content">
            <span class="close-button" onclick="hideForm()">&times;</span>
            <h2>Añadir/Modificar Datos del Alumno</h2>
            <form id="courseForm" action="cursos.php" method="POST">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" value="">

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required>

                <label for="telefono">Telefono:</label>
                <input type="text" id="telefono" name="telefono" required>

                <label for="estado_del_curso">Estado del alumno:</label>
                <select id="estado_del_curso" name="estado_del_curso" required>
                    <option value="Alta">Alta</option>
                    <option value="Baja">Baja</option>
                </select>
                 <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="text" id="fecha_inicio" name="fecha_inicio" required>

                <label for="fecha_final">Fecha de baja:</label>
                <input type="text" id="fecha_final" name="fecha_final" required>

                <label for="curso">Curso:</label>
                <input type="text" id="curso" name="Curso" required>

                <button type="submit" id="submitButton">Guardar</button>
                <button type="button" class="btn btn-cancel" onclick="hideForm()">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function showForm(alumnos = null) {
            const modalOverlay = document.getElementById('modalOverlay');
            const form = document.getElementById('courseForm');
            const submitButton = document.getElementById('submitButton');

            modalOverlay.style.display = 'flex'; // Muestra el modal

            if (alumnos) {
                // Rellenar formulario para modificar
                document.getElementById('dni').value = alumnos.DNI;
                document.getElementById('nombre').value = alumnos.Nombre;
                document.getElementById('apellidos').value = alumnos.Apellidos;
                document.getElementById('telefono').value = alumnos.Telefono;
                document.getElementById('fecha_inicio').value = alumnos.Fecha_inicio;
                document.getElementById('estado_del_curso').value = alumnos.Estado_del_curso;
                document.getElementById('fecha_final').value = alumnos.Fecha_final;
                document.getElementById('curso').value = alumnos.Curso;
                submitButton.textContent = 'Modificar';
            } else {
                // Limpiar formulario para añadir
                form.reset();
                document.getElementById('dni').value = '';
                submitButton.textContent = 'Añadir';
                document.getElementById('estado_del_alumno').value = 'Alta'; // Valor por defecto
            }
        }   

        function hideForm() {
            const modalOverlay = document.getElementById('modalOverlay');
            modalOverlay.style.display = 'none'; // Oculta el modal
        }

        // Cerrar el modal al hacer clic fuera del contenido
        document.getElementById('modalOverlay').addEventListener('click', function(event) {
            if (event.target === this) {
                hideForm();
            }
        });
    </script>
    </section>
</main>

<script>
function toggleSection(sectionId) {
    const sections = document.querySelectorAll('.toggle-section');
    sections.forEach(section => {
        if (section.id === sectionId) {
            section.classList.toggle('hidden');
        } else {
            section.classList.add('hidden');
        }
    });
}
</script>

</body>
</html>
