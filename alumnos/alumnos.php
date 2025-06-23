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
            <input type="text" id="nombre1" name="nombre1" required>

            <label for="apellido1">Apellido:</label>
            <input type="text" id="apellido1" name="apellido1" required>

            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="dni" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <button type="submit">Enviar</button>
        </form>
    </section>

    <section id="formGestion" class="hidden toggle-section form-gestion">
        <p class="descripcion-gestion">Aquí puedes gestionar tus datos.</p>
        
    <?php
    // Simulación de una base de datos con un array
    session_start();

    if (!isset($_SESSION['cursos'])) {
        $_SESSION['cursos'] = [
            [
                'ID_Curso' => 1,
                'Nombre' => 'Desarrollo Web Full Stack',
                'Profesor_asignado' => 'Juan Pérez',
                'Fecha_inicio' => '2025-09-01',
                'Estado_del_curso' => 'Alta', // Modificado a 'Alta'
                'Fecha_final' => '2026-03-01'
            ],
            [
                'ID_Curso' => 2,
                'Nombre' => 'Introducción a la Ciencia de Datos',
                'Profesor_asignado' => 'María García',
                'Fecha_inicio' => '2025-10-15',
                'Estado_del_curso' => 'Alta', // Modificado a 'Alta'
                'Fecha_final' => '2026-02-15'
            ],
            [
                'ID_Curso' => 3,
                'Nombre' => 'Diseño Gráfico Avanzado',
                'Profesor_asignado' => 'Carlos Ruíz',
                'Fecha_inicio' => '2025-08-01',
                'Estado_del_curso' => 'Baja', // Modificado a 'Baja'
                'Fecha_final' => '2024-12-01'
            ]
        ];
        $_SESSION['next_id'] = 4;
    }

    // Lógica para eliminar un curso
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id_to_delete = (int)$_GET['id'];
        $_SESSION['cursos'] = array_filter($_SESSION['cursos'], function($curso) use ($id_to_delete) {
            return $curso['ID_Curso'] !== $id_to_delete;
        });
        $_SESSION['cursos'] = array_values($_SESSION['cursos']);
        header('Location: cursos.php');
        exit();
    }

    // Lógica para añadir o modificar un curso
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = isset($_POST['id_curso']) ? (int)$_POST['id_curso'] : 0;
        $nombre = htmlspecialchars($_POST['nombre']);
        $profesor = htmlspecialchars($_POST['profesor_asignado']);
        $fecha_inicio = htmlspecialchars($_POST['fecha_inicio']);
        // Validar que el estado del curso sea 'Alta' o 'Baja'
        $estado = in_array($_POST['estado_del_curso'], ['Alta', 'Baja']) ? htmlspecialchars($_POST['estado_del_curso']) : 'Alta';
        $fecha_final = htmlspecialchars($_POST['fecha_final']);

        if ($id > 0) {
            // Modificar curso existente
            foreach ($_SESSION['cursos'] as &$curso) {
                if ($curso['ID_Curso'] === $id) {
                    $curso['Nombre'] = $nombre;
                    $curso['Profesor_asignado'] = $profesor;
                    $curso['Fecha_inicio'] = $fecha_inicio;
                    $curso['Estado_del_curso'] = $estado;
                    $curso['Fecha_final'] = $fecha_final;
                    break;
                }
            }
        } else {
            // Añadir nuevo curso
            $new_id = $_SESSION['next_id']++;
            $_SESSION['cursos'][] = [
                'ID_Curso' => $new_id,
                'Nombre' => $nombre,
                'Profesor_asignado' => $profesor,
                'Fecha_inicio' => $fecha_inicio,
                'Estado_del_curso' => $estado,
                'Fecha_final' => $fecha_final
            ];
        }
        header('Location: cursos.php');
        exit();
    }

    $cursos_filtrados = $_SESSION['cursos'];

    // Lógica para buscar cursos
    if (isset($_GET['search_id']) && !empty($_GET['search_id'])) {
        $search_id = (int)$_GET['search_id'];
        $cursos_filtrados = array_filter($_SESSION['cursos'], function($curso) use ($search_id) {
            return $curso['ID_Curso'] === $search_id;
        });
    }
    ?>

    <div class="search-container">
        <form action="cursos.php" method="GET" style="display: flex; width: 100%;">
            <input type="text" name="search_id" placeholder="Buscar curso por ID..." value="<?php echo isset($_GET['search_id']) ? htmlspecialchars($_GET['search_id']) : ''; ?>">
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
                <th>Curso</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($cursos_filtrados)): ?>
                <tr>
                    <td colspan="7">No se encontraron alumnos.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($cursos_filtrados as $curso): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($curso['ID_Curso']); ?></td>
                        <td><?php echo htmlspecialchars($curso['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($curso['Profesor_asignado']); ?></td>
                        <td><?php echo htmlspecialchars($curso['Fecha_inicio']); ?></td>
                        <td><?php echo htmlspecialchars($curso['Estado_del_curso']); ?></td>
                        <td><?php echo htmlspecialchars($curso['Fecha_final']); ?></td>
                        <td class="actions">
                            <button class="btn btn-delete" onclick="if(confirm('¿Estás seguro de eliminar este alumno?')) { window.location.href='cursos.php?action=delete&id=<?php echo $curso['ID_Curso']; ?>'; }">Eliminar datos</button>
                            <button class="btn btn-modify" onclick="showForm(<?php echo htmlspecialchars(json_encode($curso)); ?>)">Modificar datos</button>
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
                <label for="id_curso">DNI:</label>
                <input type="text" id="id_curso" name="id_curso" value="">

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="profesor_asignado">Apellidos:</label>
                <input type="text" id="profesor_asignado" name="profesor_asignado" required>

                <label for="fecha_inicio">Telefono:</label>
                <input type="text" id="fecha_inicio" name="fecha_inicio" required>

                
                <label for="estado_del_curso">Estado del alumno:</label>
                <select id="estado_del_curso" name="estado_del_curso" required>
                    <option value="Alta">Alta</option>
                    <option value="Baja">Baja</option>
                </select>

                <label for="fecha_final">Curso:</label> 
                <input type="text" id="fecha_final" name="fecha_final" required>

                <button type="submit" id="submitButton">Guardar</button>
                <button type="button" class="btn btn-cancel" onclick="hideForm()">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function showForm(curso = null) {
            const modalOverlay = document.getElementById('modalOverlay');
            const form = document.getElementById('courseForm');
            const submitButton = document.getElementById('submitButton');

            modalOverlay.style.display = 'flex'; // Muestra el modal

            if (curso) {
                // Rellenar formulario para modificar
                document.getElementById('id_curso').value = curso.ID_Curso;
                document.getElementById('nombre').value = curso.Nombre;
                document.getElementById('profesor_asignado').value = curso.Profesor_asignado;
                document.getElementById('fecha_inicio').value = curso.Fecha_inicio;
                document.getElementById('estado_del_curso').value = curso.Estado_del_curso;
                document.getElementById('fecha_final').value = curso.Fecha_final;
                submitButton.textContent = 'Modificar';
            } else {
                // Limpiar formulario para añadir
                form.reset();
                document.getElementById('id_curso').value = '';
                submitButton.textContent = 'Añadir';
                document.getElementById('estado_del_curso').value = 'Alta'; // Valor por defecto
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
