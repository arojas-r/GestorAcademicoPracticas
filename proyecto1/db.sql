-- LA BBDD
CREATE DATABASE IF NOT EXISTS centro;

USE centro;

-- TABLA ALUMNOS
CREATE TABLE Alumnos (
    DNI_Alumno VARCHAR(10) PRIMARY KEY,
    Nombre_Alumno VARCHAR(50) NOT NULL,
    Apellido_Alumno VARCHAR(50) NOT NULL,
    Teléfono_Alumno VARCHAR(15),
    Estado_Alumno ENUM('activo', 'baja') NOT NULL DEFAULT 'activo',
    Fecha_Alta_Alumno DATE NOT NULL,
    Fecha_Baja_Alumno DATE
);

-- INSERTO ALUMNOS DE EJEMPLO
INSERT INTO
    Alumnos (
        DNI_Alumno,
        Nombre_Alumno,
        Apellido_Alumno,
        Teléfono_Alumno,
        Estado_Alumno,
        Fecha_Alta_Alumno,
        Fecha_Baja_Alumno
    )
VALUES (
        '12345678A',
        'Laura',
        'Martínez',
        '600123456',
        'activo',
        '2023-09-01',
        NULL
    ),
    (
        '23456789B',
        'Carlos',
        'Gómez',
        '600234567',
        'baja',
        '2022-01-15',
        '2023-06-30'
    ),
    (
        '34567890C',
        'Lucía',
        'Fernández',
        '600345678',
        'activo',
        '2024-02-10',
        NULL
    ),
    (
        '45678901D',
        'David',
        'López',
        '600456789',
        'baja',
        '2021-03-22',
        '2022-12-01'
    ),
    (
        '56789012E',
        'Ana',
        'Sánchez',
        '600567890',
        'activo',
        '2024-09-10',
        NULL
    );

USE centro;

-- Tabla Profesores
CREATE TABLE IF NOT EXISTS Profesores (
    DNI_Profesor VARCHAR(10) PRIMARY KEY,
    Nombre_Profesor VARCHAR(50) NOT NULL,
    Apellido_Profesor VARCHAR(50) NOT NULL,
    Telefono_Profesor VARCHAR(15),
    Estado_Profesor ENUM('activo', 'baja') NOT NULL DEFAULT 'activo',
    Fecha_Alta_Profesor DATE NOT NULL,
    Fecha_Baja_Profesor DATE
);

-- INSERTO 5 profesores de ejemplo
INSERT INTO
    Profesores (
        DNI_Profesor,
        Nombre_Profesor,
        Apellido_Profesor,
        Telefono_Profesor,
        Estado_Profesor,
        Fecha_Alta_Profesor,
        Fecha_Baja_Profesor
    )
VALUES (
        '11111111P',
        'Miguel',
        'Ruiz',
        '611111111',
        'activo',
        '2022-09-01',
        NULL
    ),
    (
        '22222222Q',
        'Sofía',
        'Lara',
        '622222222',
        'baja',
        '2020-01-10',
        '2023-02-15'
    ),
    (
        '33333333R',
        'Alberto',
        'Navarro',
        '633333333',
        'activo',
        '2023-05-20',
        NULL
    ),
    (
        '44444444S',
        'Elena',
        'Domínguez',
        '644444444',
        'baja',
        '2019-11-01',
        '2021-06-30'
    ),
    (
        '55555555T',
        'Raquel',
        'Cano',
        '655555555',
        'activo',
        '2024-03-12',
        NULL
    );

USE centro;

-- Tabla CURSOS
CREATE TABLE IF NOT EXISTS CURSOS (
    ID_CURSO INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Curso VARCHAR(100) NOT NULL,
    Descripcion_Curso TEXT,
    DNI_PROFESOR VARCHAR(10) NOT NULL,
    NOMBRE_PROFESOR VARCHAR(100) NOT NULL,
    ESTADO_CURSO ENUM('Activo', 'Cerrado') NOT NULL DEFAULT 'Activo',
    FECHA_INICIO_CURSO DATE NOT NULL,
    FECHA_FINAL_CURSO DATE,
    FOREIGN KEY (DNI_PROFESOR) REFERENCES Profesores (DNI_Profesor) ON UPDATE CASCADE ON DELETE RESTRICT
);
-- Inserto Cursos de ejemplo
INSERT INTO
    CURSOS (
        Nombre_Curso,
        Descripcion_Curso,
        DNI_PROFESOR,
        NOMBRE_PROFESOR,
        ESTADO_CURSO,
        FECHA_INICIO_CURSO,
        FECHA_FINAL_CURSO
    )
VALUES (
        'Programación Básica',
        'Curso introductorio a la programación en Python.',
        '11111111P',
        'Miguel Ruiz',
        'Activo',
        '2024-01-10',
        '2024-04-10'
    ),
    (
        'Diseño Web',
        'HTML, CSS y fundamentos de diseño responsive.',
        '22222222Q',
        'Sofía Lara',
        'Cerrado',
        '2023-02-01',
        '2023-06-15'
    ),
    (
        'Bases de Datos',
        'Modelado, SQL y optimización.',
        '33333333R',
        'Alberto Navarro',
        'Activo',
        '2024-03-01',
        '2024-07-01'
    ),
    (
        'Machine Learning',
        'Introducción al aprendizaje automático.',
        '55555555T',
        'Raquel Cano',
        'Activo',
        '2024-05-05',
        NULL
    ),
    (
        'JavaScript Avanzado',
        'DOM, eventos y frameworks.',
        '11111111P',
        'Miguel Ruiz',
        'Cerrado',
        '2022-09-01',
        '2023-01-15'
    );

-- Tabla Matriculas
CREATE TABLE IF NOT EXISTS Matriculas (
    ID_Matricula INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Curso VARCHAR(100) NOT NULL,
    DNI_Alumno VARCHAR(10) NOT NULL,
    Nombre_Alumno VARCHAR(50) NOT NULL,
    Apellido_Alumno VARCHAR(50) NOT NULL,
    Estado_Matricula ENUM('activa', 'desactivada') NOT NULL DEFAULT 'activa',
    FOREIGN KEY (DNI_Alumno) REFERENCES Alumnos (DNI_Alumno) ON UPDATE CASCADE ON DELETE RESTRICT
);
--INSERTO matriculas de ejemplo
INSERT INTO
    Matriculas (
        Nombre_Curso,
        DNI_Alumno,
        Nombre_Alumno,
        Apellido_Alumno,
        Estado_Matricula
    )
VALUES (
        'Programación Básica',
        '12345678A',
        'Laura',
        'Martínez',
        'activa'
    ),
    (
        'Bases de Datos',
        '23456789B',
        'Carlos',
        'Gómez',
        'activa'
    ),
    (
        'Diseño Web',
        '34567890C',
        'Lucía',
        'Fernández',
        'desactivada'
    ),
    (
        'Machine Learning',
        '45678901D',
        'David',
        'López',
        'activa'
    ),
    (
        'JavaScript Avanzado',
        '56789012E',
        'Ana',
        'Sánchez',
        'desactivada'
    );