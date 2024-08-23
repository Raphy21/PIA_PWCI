USE Proyecto_BDM;

-- ++++++++++TABLA USUARIO++++++++++ --
DELIMITER //
CREATE FUNCTION CorreoEnExistencia (correo VARCHAR(30)) RETURNS BOOLEAN
BEGIN
    DECLARE existencia BOOLEAN;

    -- Verificar si el correo existe en la tabla USUARIO
    SELECT EXISTS (SELECT USUARIO.Correo FROM USUARIO WHERE USUARIO.Correo = correo) INTO existencia;

    RETURN existencia;
END;
// DELIMITER ;

DELIMITER //
CREATE FUNCTION NombreUsuarioEnExistencia (nombreUsuario VARCHAR(30)) RETURNS BOOLEAN
BEGIN
    DECLARE existencia BOOLEAN;

    -- Verificar si el nombre de usuario existe en la tabla USUARIO
    SELECT EXISTS (SELECT USUARIO.NombreUsuario FROM USUARIO WHERE USUARIO.NombreUsuario = nombreUsuario) INTO existencia;

    RETURN existencia;
END;
// DELIMITER ;

-- ++++++++++TABLA CATEGORIA++++++++++ --
DELIMITER //
CREATE FUNCTION CategoriaExistente (nombreCategoria VARCHAR(30)) RETURNS BOOLEAN
BEGIN
    DECLARE existencia BOOLEAN;

    -- Verificar si el correo existe en la tabla USUARIO
    SELECT EXISTS (SELECT CATEGORIA.Nombre FROM CATEGORIA WHERE CATEGORIA.Nombre = nombreCategoria) INTO existencia;

    RETURN existencia;
END;
// DELIMITER ;

DELIMITER //
CREATE FUNCTION CategoriaEnUso (nombreCategoria VARCHAR(30)) RETURNS BOOLEAN
BEGIN
    DECLARE enUso BOOLEAN;

    -- Verificar si la categoría está siendo utilizada por algún producto
    SELECT EXISTS (SELECT CATEGORIZACIONPRODUCTO.IdProducto FROM (CATEGORIZACIONPRODUCTO JOIN CATEGORIA ON CATEGORIA.Id = CATEGORIZACIONPRODUCTO.IdCategoria) WHERE CATEGORIA.Nombre = nombreCategoria)
    INTO enUso;

    RETURN enUso;
END;
// DELIMITER ;

-- ++++++++++TABLA PRODUCTO++++++++++ --
DELIMITER //
CREATE FUNCTION NombreProductoEnExistencia (nombreProducto VARCHAR(50)) RETURNS BOOLEAN
BEGIN
    DECLARE existencia BOOLEAN;

    -- Verificar si el nombre de producto ya existe en la tabla PRODUCTO
    SELECT EXISTS (SELECT PRODUCTO.Nombre FROM PRODUCTO WHERE PRODUCTO.Nombre = nombreProducto) INTO existencia;

    RETURN existencia;
END;
// DELIMITER ;

-- ++++++++++TABLA LISTA++++++++++ --
DELIMITER //
CREATE FUNCTION NombreListaEnExistencia (nombreLista VARCHAR(30), idUsuario INT) RETURNS BOOLEAN
BEGIN
    DECLARE existencia BOOLEAN;

    -- Verificar si el nombre de la lista existe en la tabla LISTA para el usuario especificado
    SELECT EXISTS (SELECT LISTA.Nombre FROM LISTA WHERE LISTA.Nombre = nombreLista AND LISTA.IdPropietario = idUsuario) INTO existencia;

    RETURN existencia;
END;
// DELIMITER ;

DELIMITER //
CREATE FUNCTION NombreListaEnExistenciaExcepto (nombreLista VARCHAR(30), idUsuario INT, idListaExcepcion INT) RETURNS BOOLEAN
BEGIN
    DECLARE existencia BOOLEAN;

    -- Verificar si el nombre de la lista existe en la tabla LISTA para el usuario especificado (exentando la lista especificada)
    SELECT EXISTS (SELECT LISTA.Nombre FROM LISTA WHERE LISTA.Nombre = nombreLista AND LISTA.IdPropietario = idUsuario AND LISTA.Id != idListaExcepcion) INTO existencia;

    RETURN existencia;
END;
// DELIMITER ;

DELIMITER //
CREATE FUNCTION ProductoExisteEnLista (idProductoComprobar INT, idListaComprobar INT) RETURNS BOOLEAN
BEGIN
    DECLARE existencia BOOLEAN;

    -- Verificar si el el producto ya esta asignado en la lista dada
    SELECT EXISTS (SELECT PRODUCTOENLISTADO.IdProducto FROM PRODUCTOENLISTADO WHERE PRODUCTOENLISTADO.IdProducto = idProductoComprobar AND PRODUCTOENLISTADO.IdLista = idListaComprobar) INTO existencia;

    RETURN existencia;
END;
// DELIMITER ;
