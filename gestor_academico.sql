create database if not exists Instituto;

Use Instituto;
/*LA TABLA DE PROFESOR CON SUS DATOS BASES*/
Create table if not exists Profesor(
    DNI VARCHAR(9) PRIMARY KEY NOT NULL, 
    Cargo VARCHAR(15) NOT NULL,
    Nombre VARCHAR(30) NOT NULL,
    Apellido VARCHAR(60) NOT NULL,
    Telefono VARCHAR(15) NOT NULL,
    fecha_alta DATE NOT NULL,
    fecha_baja DATE,
    Estado_Profesor VARCHAR (40) NOT NULL
);
/*LA TABLA DE ALUMNOS CON SUS DATOS BASES*/
Create table if not exists Alumno(
    DNI VARCHAR(9) PRIMARY KEY NOT NULL,
    Cargo VARCHAR(15) NOT NULL,
    Nombre VARCHAR(30) NOT NULL,
    Apellido VARCHAR(60) NOT NULL,
    Telefono VARCHAR(15) NOT NULL,
    fecha_alta DATE NOT NULL,
    fecha_baja DATE,
    Estado_Alumno VARCHAR (40) NOT NULL
);
/*TABLA DE CURSO, EL DNIP HACE REFERENCIA AL DNI DEL PROFESOR PARA QUE SE PUEDA HACER LA CONSULTA DEL PROFESOR ASIGNADO EN LOS CURSOS*/
Create table if not exists Curso(
    ID_Curso INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    Descripcion VARCHAR(80),
    Nombre_Curso VARCHAR(50),
    Estado_Curso VARCHAR (40) NOT NULL,
    DNIP VARCHAR(9),
    fecha_inicio DATE,
    fecha_fin DATE,
    FOREIGN KEY (DNIP) REFERENCES Profesor(DNI) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE

);
/*TABLA DE MATRICULA EL CUAL CONECTA LOS DATOS DEL ALUMNO CON EL CURSO*/
Create table if not exists Matricula(
    ID_MAT VARCHAR(9) PRIMARY KEY NOT NULL,
    DNIA VARCHAR(9) NOT NULL,
    IDCURSO INT NOT NULL,
    Estado_Matricula VARCHAR (40) NOT NULL,
    FOREIGN KEY (DNIA) REFERENCES Alumno(DNI) 
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    FOREIGN KEY (IDCURSO) REFERENCES Curso(ID_Curso)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);


INSERT INTO Profesor (DNI, Cargo, Nombre, Apellido, Telefono, fecha_alta, fecha_baja) VALUES
('12345678A', 'Profesor', 'Luis', 'García Pérez', '600123456', '2020-09-01', NULL),
('23456789B', 'Profesor', 'Ana', 'López Díaz', '611234567', '2021-01-15', NULL),
('34567890C', 'Profesor', 'Jorge', 'Martín Ruiz', '622345678', '2019-06-10', NULL),
('45678901D', 'Profesor', 'María', 'Sánchez Gómez', '633456789', '2022-02-01', '2023-01-30'),
('56789012E', 'Profesor', 'Carlos', 'Fernández Romero', '644567890', '2018-03-22', NULL),
('67890123F', 'Profesor', 'Elena', 'Jiménez Torres', '655678901', '2021-09-01', NULL),
('78901234G', 'Profesor', 'Pedro', 'Morales Herrera', '666789012', '2017-11-10', NULL),
('89012345H', 'Profesor', 'Lucía', 'Ramos Vázquez', '677890123', '2023-03-15', '2024-06-01'),
('90123456I', 'Profesor', 'Raúl', 'Castro Navarro', '688901234', '2016-01-01', NULL),
('01234567J', 'Profesor', 'Clara', 'Molina Gil', '699012345', '2020-04-05', NULL),
('11234567K', 'Profesor', 'Ismael', 'Ortega León', '610123456', '2015-10-12', NULL),
('12234567L', 'Profesor', 'Sara', 'Guerrero Peña', '620234567', '2022-10-01', '2023-07-15'),
('13234567M', 'Profesor', 'Tomás', 'Vega Cano', '630345678', '2013-09-01', NULL),
('14234567N', 'Profesor', 'Natalia', 'Reyes Soto', '640456789', '2021-12-12', NULL),
('15234567O', 'Profesor', 'Hugo', 'Cabrera Márquez', '650567890', '2014-06-01', NULL),
('16234567P', 'Profesor', 'Andrea', 'Serrano Rubio', '660678901', '2023-01-15', '2023-09-30'),
('17234567Q', 'Profesor', 'Javier', 'Arias Medina', '670789012', '2020-02-20', NULL),
('18234567R', 'Profesor', 'Inés', 'Delgado Paredes', '680890123', '2011-08-25', NULL),
('19234567S', 'Profesor', 'David', 'Esteban Lozano', '690901234', '2023-05-05', NULL),
('20234567T', 'Profesor', 'Silvia', 'Benítez Molina', '600012345', '2012-03-30', NULL),
('21234567U', 'Profesor', 'Óscar', 'Salas Núñez', '611123456', '2022-11-01', NULL),
('22234567V', 'Profesor', 'Beatriz', 'Corral Sáez', '622234567', '2010-04-10', NULL),
('23234567W', 'Profesor', 'Joaquín', 'Campos Ríos', '633345678', '2023-08-01', NULL),
('24234567X', 'Profesor', 'Patricia', 'Nieto Valdés', '644456789', '2016-12-01', NULL),
('25234567Y', 'Profesor', 'Sergio', 'Pérez Lara', '655567890', '2021-07-20', NULL);

INSERT INTO Alumno (DNI, Cargo, Nombre, Apellido, Telefono, fecha_alta, fecha_baja) VALUES
('11111111A', 'Estudiante', 'Valeria', 'Domínguez Alba', '600123100', '2022-09-01', NULL),
('22222222B', 'Estudiante', 'Mateo', 'Fuentes Bravo', '611234200', '2023-01-10', NULL),
('33333333C', 'Estudiante', 'Camila', 'Navarro Solís', '622345300', '2021-08-25', NULL),
('44444444D', 'Estudiante', 'Gabriel', 'Del Valle Antón', '633456400', '2020-10-05', NULL),
('55555555E', 'Estudiante', 'Emma', 'Vidal Cuenca', '644567500', '2022-02-15', NULL),
('66666666F', 'Estudiante', 'Martina', 'Pino Andrada', '655678600', '2023-04-12', NULL),
('77777777G', 'Estudiante', 'Thiago', 'Del Río Castaño', '666789700', '2021-11-20', NULL),
('88888888H', 'Estudiante', 'Zoe', 'Carrillo Franco', '677890800', '2020-09-01', NULL),
('99999999I', 'Estudiante', 'Leonardo', 'Roldán Muñoz', '688901900', '2022-06-18', NULL),
('10101010J', 'Estudiante', 'Luna', 'Gallego Moya', '699012010', '2023-03-30', NULL),
('12121212K', 'Estudiante', 'Gael', 'Peña Aguado', '610123111', '2021-07-01', NULL),
('13131313L', 'Estudiante', 'Sofía', 'Herrera Cebrián', '620234222', '2020-01-15', '2023-12-31'),
('14141414M', 'Estudiante', 'Dylan', 'Silva Prados', '630345333', '2022-09-01', NULL),
('15151515N', 'Estudiante', 'Aitana', 'Cortés Montiel', '640456444', '2021-10-10', NULL),
('16161616O', 'Estudiante', 'Bruno', 'Acosta Barragán', '650567555', '2023-05-05', NULL),
('17171717P', 'Estudiante', 'Claudia', 'Toledo Redondo', '660678666', '2020-03-03', NULL),
('18181818Q', 'Estudiante', 'Iker', 'Rivas Milla', '670789777', '2021-04-22', NULL),
('19191919R', 'Estudiante', 'Nora', 'Galán Tejada', '680890888', '2022-08-08', NULL),
('20202020S', 'Estudiante', 'Alejandro', 'Guirao Segura', '690901999', '2023-09-12', NULL),
('21212121T', 'Estudiante', 'Noa', 'Espejo Romero', '600012111', '2022-07-07', NULL),
('23232323U', 'Estudiante', 'Pablo', 'Jurado Vera', '611123222', '2020-05-05', NULL),
('24242424V', 'Estudiante', 'Vera', 'Camacho Ferrer', '622234333', '2021-06-16', NULL),
('25252525W', 'Estudiante', 'Enzo', 'Lozano Barrios', '633345444', '2023-01-01', NULL),
('26262626X', 'Estudiante', 'Mía', 'Soler Manzano', '644456555', '2021-12-24', NULL),
('27272727Y', 'Estudiante', 'Álvaro', 'Marín Rosales', '655567666', '2020-09-10', NULL),
('28282828Z', 'Estudiante', 'Julia', 'Saavedra Quintana', '666678888', '2023-06-06', NULL);



DROP DATABASE INSTITUTO;
