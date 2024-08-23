USE Proyecto_BDM;

-- ++++++++++TABLA USUARIO++++++++++ --
CREATE VIEW VistaUsuario AS
SELECT Id, NombreUsuario, Correo, Contrasena, Visibilidad, Rol, Nombres, ApellidoPaterno, ApellidoMaterno, FechaNacimiento, ImagenPerfil, Sexo, FechaIngreso
FROM USUARIO;

-- ++++++++++TABLA PRODUCTO++++++++++ --
CREATE VIEW VistaProducto AS
SELECT Id, IdVendedor, Nombre, Descripcion, Modo, Precio, Existencia, Calificacion, Aprobado, IdAdminAprobador
FROM PRODUCTO;

CREATE VIEW VistaProductosSinAprobar AS
SELECT Id, IdVendedor, Nombre, Descripcion, Modo, Precio, Existencia, Calificacion, Aprobado, IdAdminAprobador
FROM PRODUCTO WHERE Aprobado = false;

CREATE VIEW VistaProductosAprobados AS
SELECT Id, IdVendedor, Nombre, Descripcion, Modo, Precio, Existencia, Calificacion, Aprobado, IdAdminAprobador
FROM PRODUCTO WHERE Aprobado = true;

CREATE VIEW VistaProductosInteresantes AS
SELECT Id, IdVendedor, Nombre, Descripcion, Modo, Precio, Existencia, Calificacion, Aprobado, IdAdminAprobador
FROM PRODUCTO WHERE Aprobado = true ORDER BY Id DESC LIMIT 3;

-- ++++++++++TABLA CATEGORIA++++++++++ --
CREATE VIEW VistaCategoria AS
SELECT Id, IdCreador, Nombre, Descripcion
FROM CATEGORIA;

-- ++++++++++TABLA VALORACION++++++++++ --
CREATE VIEW VistaValoracion AS
SELECT V.Id, V.IdUsuario, V.IdProducto, V.Puntuacion, V.Titulo, V.Comentario, V.FechaHora, U.NombreUsuario
FROM VALORACION V JOIN USUARIO U ON V.IdUsuario = U.Id;


-- ++++++++++TABLA CARRITO++++++++++ --
CREATE VIEW VistaCarritosFinalizados AS
SELECT Id, IdPropietario, Terminado, FechaHoraFin
FROM CARRITO Where Terminado = true;

-- ++++++++++TABLA LISTA++++++++++ --
CREATE VIEW VistaLista AS
SELECT Id, IdPropietario, Nombre, Descripcion, Visibilidad
FROM LISTA;
