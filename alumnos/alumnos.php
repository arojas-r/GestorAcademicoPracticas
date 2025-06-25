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
            <button  class="btn-toggle" onclick="toggleSection('formAlta')">Dar de Alta</button>
            <button  class="btn-toggle" onclick="toggleSection('formGestion')">Gestionar Datos</button>
        </div>
    </div>

    <p class="descripcion-alumno">"Bienvenido al área de alumnos. Aquí puedes gestionar los datos de los estudiantes y consultar su progreso."</p>

    <section id="formAlta" class="hidden toggle-section form-alta">
        <form action="" method="post">
            <label for="DNI">DNI:</label>
            <input type="text" id="DNI" name="DNI" pattern="[0-9][a-zA-Z]{7,8}" title="DNI debe tener entre 8 y 9 caracteres, comenzando con un número" required>

            <label for="Nombre">Nombre:</label>
            <input type="text" id="Nombre" name="Nombre" required>

            <label for="Apellido">Apellido:</label>
            <input type="text" id="Apellido" name="Apellido" required>

            <label for="Cargo">Cargo:</label>
            <select id="Cargo" name="Cargo" required>
                <option value="Alumno">Alumno</option>
                <option value="Profesor">Profesor</option>
            </select><br><br>

            <label for="fecha-inicio">Fecha de Inicio:</label>
            <input type="date" id="fecha-inicio" name="fecha-alta" required><br><br>

             <label for="fecha-fin">Fecha de fin:</label>
            <input type="date" id="fecha-fin" name="fecha-fin" required><br><br>

            <label for="Telefono">Teléfono:</label>
            <input type="text" id="Telefono" name="Telefono" pattern="[0-9]{9}" title="Teléfono debe tener 9 cifras" required>

            <button type="submit">Enviar</button>
        </form>
    </section>

    <section id="formGestion" class="hidden toggle-section form-gestion">
        <p class="descripcion-gestion">Aquí puedes gestionar los datos de los estudiantes.</p>
        
    <?php
    // Simulación de una base de datos con un array
    session_start();

    if (!isset($_SESSION['alumnos'])) {
        $_SESSION['alumnos'] = [
            [
                'DNI' => '123456789',
                'Nombre' => 'Anthony',
                'Apellido' => 'García',
                'Telefono' => '123456789',
                'fecha_alta' => '2025-09-01',
                'Estado_Alumno' => 'Alta', // Modificado a 'Alta'
                'fecha_baja' => '2026-03-04',   
                'Curso' => 'Desarrollo Web'
            ],
            [
                'DNI' => '234567890',
                'Nombre' => 'Juan',
                'Apellido' => 'Pérez', 
                'Telefono' => '987654321',
                'fecha_alta' => '2025-10-15',
                'Estado_Alumno' => 'Alta', // Modificado a 'Alta'
                'fecha_baja' => '2026-02-25',
                'Curso' => 'Diseño Gráfico Avanzado'
            ],
            [
                'DNI' => '345678901',
                'Nombre' => 'Carlos',
                'Apellido' => 'Ruíz',
                'Telefono' => '456789123',
                'fecha_alta' => '2025-08-01',
                'Estado_Alumno' => 'Baja', // Modificado a 'Baja'
                'fecha_baja' => '2024-12-06',
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
        header('Location: alumnos.php');
        exit();
    }

    // Lógica para añadir o modificar un curso
     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dni = isset($_POST['DNI']) ? trim($_POST['DNI']) : '';
            $nombre = htmlspecialchars($_POST['Nombre']);
            $apellido = htmlspecialchars($_POST['Apellido']);
            $telefono = htmlspecialchars($_POST['Telefono']);
            $fecha_alta = htmlspecialchars($_POST['fecha_alta']);
            $estado = in_array($_POST['Estado_Alumno'], ['Alta', 'Baja']) ? htmlspecialchars($_POST['Estado_Alumno']) : 'Alta';
            $fecha_baja = htmlspecialchars($_POST['fecha_baja']);
            $curso = htmlspecialchars($_POST['Curso']);

          if (!empty($dni)) {
                foreach ($_SESSION['alumnos'] as &$alumno) {
                    if ($alumno['DNI'] === $dni) {
                        $alumno['Nombre'] = $nombre;
                        $alumno['Apellido'] = $apellido;
                        $alumno['Telefono'] = $telefono;
                        $alumno['fecha_alta'] = $fecha_alta;
                        $alumno['Estado_Alumno'] = $estado;
                        $alumno['fecha_baja'] = $fecha_baja;
                        $alumno['Curso'] = $curso;
                        break;
                    }
                }
            } else {
                $_SESSION['alumnos'][] = [
                    'DNI' => $dni,
                    'Nombre' => $nombre,
                    'Apellido' => $apellido,
                    'Telefono' => $telefono,
                    'fecha_alta' => $fecha_alta,
                    'Estado_Alumno' => $estado,
                    'fecha_baja' => $fecha_baja,
                    'Curso' => $curso
                ];
            }
            header('Location: alumnos.php');
            exit();
        }

   $alumnos_filtrados = $_SESSION['alumnos'];

        if (isset($_GET['search_id']) && !empty($_GET['search_id'])) {
            $search_id = $_GET['search_id'];
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
                    <tr><td colspan="9">No se encontraron alumnos.</td></tr>
                <?php else: ?>
                    <?php foreach ($alumnos_filtrados as $alumno): ?>
                        <tr>
                            <td><?= htmlspecialchars($alumno['DNI']) ?></td>
                            <td><?= htmlspecialchars($alumno['Nombre']) ?></td>
                            <td><?= htmlspecialchars($alumno['Apellido']) ?></td>
                            <td><?= htmlspecialchars($alumno['Telefono']) ?></td>
                            <td><?= htmlspecialchars($alumno['Estado_Alumno']) ?></td>
                            <td><?= htmlspecialchars($alumno['fecha_alta']) ?></td>
                            <td><?= htmlspecialchars($alumno['fecha_baja']) ?></td>
                            <td><?= htmlspecialchars($alumno['Curso']) ?></td>
                            <td class="actions">
                                <button class="btn btn-delete" onclick="if(confirm('¿Estás seguro de eliminar este alumno?')) { window.location.href='alumnos.php?action=delete&id=<?= $alumno['DNI'] ?>'; }">Eliminar datos</button>
                                <button class="btn btn-modify" onclick='showForm(<?= json_encode($alumno) ?>)'>Modificar datos</button>
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
            <form id="courseForm" action="" method="POST">
                <label for="DNI">DNI:</label>
                <input type="text" id="DNI" name="DNI" value="">

                <label for="Nombre">Nombre:</label>
                <input type="text" id="Nombre" name="Nombre" required>

                <label for="Apellido">Apellidos:</label>
                <input type="text" id="Apellido" name="Apellido" required>

                <label for="Telefono">Telefono:</label>
                <input type="text" id="Telefono" name="Telefono" required>

                <label for="Estado_Alumno">Estado del alumno:</label>
                <select id="Estado_Alumno" name="Estado_Alumno" required>
                    <option value="Alta">Alta</option>
                    <option value="Baja">Baja</option>
                </select>
                <label for="fecha_alta">Fecha de alta:</label>
                <input type="text" id="fecha_alta" name="fecha_alta" required>

                <label for="fecha_baja">Fecha de baja:</label>
                <input type="text" id="fecha_baja" name="fecha_baja" required>

                <label for="Curso">Curso:</label>
                <input type="text" id="Curso" name="Curso" required>

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
                document.getElementById('DNI').value = alumnos.DNI;
                document.getElementById('Nombre').value = alumnos.Nombre;
                document.getElementById('Apellido').value = alumnos.Apellido;
                document.getElementById('Telefono').value = alumnos.Telefono;
                document.getElementById('fecha_alta').value = alumnos.fecha_alta;
                document.getElementById('Estado_Alumno').value = alumnos.Estado_Alumno;
                document.getElementById('fecha_baja').value = alumnos.fecha_baja;
                document.getElementById('Curso').value = alumnos.Curso;
                submitButton.textContent = 'Modificar';
            } else {
                // Limpiar formulario para añadir
                form.reset();
                document.getElementById('DNI').value = '';
                submitButton.textContent = 'Añadir';
                document.getElementById('Estado_Alumno').value = 'Alta'; // Valor por defecto
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
