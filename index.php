<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instituto</title>
    <link rel="stylesheet" href="style.css">
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
    <div class="breadcrumbs">
        <a href="index.php">Inicio</a> &raquo; <span>Panel Principal</span>
    </div>
    <main>
        <h1>Bienvenido al panel de gestión del Instituto</h1>
        <p>Selecciona una opción del menú para comenzar.</p>
    </main>

    <footer>
        &copy; <?php echo date("Y"); ?> Instituto de Educación Superior. Todos los derechos reservados.
    </footer>

</body>

</html>