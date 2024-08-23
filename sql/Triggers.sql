USE Proyecto_BDM;

-- ++++++++++TABLA USUARIO++++++++++ --
DELIMITER //
CREATE TRIGGER UsuarioFechaIngreso
BEFORE INSERT ON USUARIO
FOR EACH ROW
BEGIN
    SET NEW.FechaIngreso = NOW();
END;
// DELIMITER ;

-- ++++++++++TABLA PRODUCTO++++++++++ --
DELIMITER //
CREATE TRIGGER ProductoValoresDefault
BEFORE INSERT ON PRODUCTO
FOR EACH ROW
BEGIN
    SET NEW.Calificacion = 0.0;
    SET NEW.Aprobado = false;
    SET NEW.IdAdminAprobador = NULL;
END;
// DELIMITER ;

-- ++++++++++TABLA VALORACION++++++++++ --
DELIMITER //
CREATE TRIGGER ValoracionFechaPublicacion
BEFORE INSERT ON VALORACION
FOR EACH ROW
BEGIN
    SET NEW.FechaHora = NOW();
END;
// DELIMITER ;

DELIMITER //
CREATE TRIGGER ActualizarCalificacionProducto
AFTER INSERT ON VALORACION
FOR EACH ROW
BEGIN
    DECLARE Promedio DECIMAL(3, 2);

    -- Calcular el nuevo promedio de calificación para el producto, basado en todas las valoraciones (incluida la recién insertada)
    SELECT AVG(Puntuacion) INTO Promedio FROM VALORACION WHERE IdProducto = NEW.IdProducto;

    -- Actualizar la calificación del producto en la tabla PRODUCTO
    UPDATE PRODUCTO SET Calificacion = Promedio WHERE Id = NEW.IdProducto;
END; 
// DELIMITER ;

DELIMITER //
CREATE TRIGGER ActualizarFinalizadoEnMensaje
AFTER UPDATE ON cotizacion
FOR EACH ROW
BEGIN
    -- Verificar si el Estado cambió de 0 a 1
    IF OLD.Estado = 0 AND NEW.Estado = 1 THEN
        -- Actualizar la columna Finalizado a 1 en la tabla mensaje donde Finalizado es 0
        UPDATE mensaje
        SET Finalizado = 1
        WHERE Finalizado = 0;
    END IF;
END; 
// DELIMITER ;