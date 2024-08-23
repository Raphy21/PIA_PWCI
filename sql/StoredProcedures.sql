USE Proyecto_BDM;

-- ++++++++++TABLA USUARIO++++++++++ --
DELIMITER //
CREATE PROCEDURE RegistrarUsuario (IN nombreUsuario VARCHAR(30), IN correo VARCHAR(30), IN contrasena VARCHAR(30), IN visibilidad BOOLEAN, IN rol INT,
IN nombres VARCHAR(50), IN apellidoPaterno VARCHAR(30), IN apellidoMaterno VARCHAR(30), IN fechaNacimiento DATE, IN imagenPerfil MEDIUMBLOB, IN sexo BOOLEAN,
OUT idNuevoUsuario INT)
BEGIN
	-- Insertar los datos del nuevo usuario a crear
    INSERT INTO USUARIO (NombreUsuario, Correo, Contrasena, Visibilidad, Rol, Nombres, ApellidoPaterno, ApellidoMaterno, FechaNacimiento, ImagenPerfil, Sexo) 
    VALUES (nombreUsuario, correo, contrasena, visibilidad, rol, nombres, apellidoPaterno, apellidoMaterno, fechaNacimiento, imagenPerfil, sexo);

	-- Devolver el Id del usuario recién creado
    SET IdNuevoUsuario = LAST_INSERT_ID();
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerUsuarioPorCredenciales (IN correo VARCHAR(30), IN contrasena VARCHAR(30))
BEGIN
    -- Realizar una selección para obtener los datos del usuario a partir de sus credenciales
    SELECT * FROM VistaUsuario WHERE VistaUsuario.Correo = correo AND VistaUsuario.Contrasena = contrasena;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerUsuarioPorId (IN idUsuario INT)
BEGIN
    -- Realizar una selección para obtener los datos del usuario a partir de su Id
    SELECT * FROM VistaUsuario WHERE VistaUsuario.Id = idUsuario;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ModificarUsuario (IN idUsuario INT, IN nombreUsuario VARCHAR(30), IN correo VARCHAR(30), IN contrasena VARCHAR(30), IN visibilidad BOOLEAN,
IN rol INT, IN nombres VARCHAR(50), IN apellidoPaterno VARCHAR(30), IN apellidoMaterno VARCHAR(30), IN fechaNacimiento DATE, IN imagenPerfil MEDIUMBLOB, IN sexo BOOLEAN)
BEGIN
    -- Actualizar los datos del usuario con el Id especificado
    UPDATE USUARIO SET NombreUsuario = nombreUsuario, Correo = correo, Contrasena = contrasena, Visibilidad = visibilidad, Rol = rol, Nombres = nombres,
    ApellidoPaterno = apellidoPaterno, ApellidoMaterno = apellidoMaterno, FechaNacimiento = fechaNacimiento, ImagenPerfil = imagenPerfil, Sexo = sexo
    WHERE Id = idUsuario;
END;
// DELIMITER ;

-- ++++++++++TABLA CATEGORIA++++++++++ --
DELIMITER //
CREATE PROCEDURE CrearCategoria (IN idCreador INT, IN nombre VARCHAR(30), IN descripcion TEXT, OUT idNuevaCategoria INT)
BEGIN
	-- Insertar los datos de la nueva categoría a crear
    INSERT INTO CATEGORIA (IdCreador, Nombre, Descripcion) VALUES (idCreador, nombre, descripcion);

	-- Devolver el Id de la categoría recién creada
    SET IdNuevaCategoria = LAST_INSERT_ID();
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerTodasCategorias()
BEGIN
	-- Realizar una selección de todas las categorías disponibles
	SELECT * FROM VistaCategoria;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE EliminarCategoriaPorNombre (IN nombreCategoria VARCHAR(30))
BEGIN
    DELETE FROM CATEGORIA WHERE Nombre = nombreCategoria;
END;
// DELIMITER ;

-- ++++++++++TABLA PRODUCTO++++++++++ --
DELIMITER //
CREATE PROCEDURE CrearProducto (IN idVendedor INT, IN nombre VARCHAR(50), IN descripcion TEXT, IN modo BOOLEAN, IN precio DECIMAL(8, 2), IN existencia INT,
OUT idNuevoProducto INT)
BEGIN
	-- Insertar los datos del nuevo producto a crear
    INSERT INTO PRODUCTO (IdVendedor, Nombre, Descripcion, Modo, Precio, Existencia) VALUES (idVendedor, nombre, descripcion, modo, precio, existencia);

	-- Devolver el Id del producto recién creado
    SET IdNuevoProducto = LAST_INSERT_ID();
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerProductosSinAprobar()
BEGIN
	-- Realizar una selección de todas los productos que no estén marcados como aprobados
	SELECT * FROM VistaProductosSinAprobar;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerProductosAprobadosUsuario(IN idDelAdmin INT)
BEGIN
	-- Realizar una selección de todas los productos cuyo id de aprobador sea igual al especificado
	SELECT * FROM VistaProducto WHERE IdAdminAprobador = idDelAdmin;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerProductosPublicadosUsuario(IN idDelVendedor INT)
BEGIN
	-- Realizar una selección de todas los productos cuyo id de vendedor sea igual al especificado
	SELECT * FROM VistaProductosAprobados WHERE IdVendedor = idDelVendedor;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerProductosInteresantes()
BEGIN
	-- Realizar una selección de todas los productos de la vista de "productos interesantes"
	SELECT * FROM VistaProductosInteresantes;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE AprobarRechazarProducto(IN idDelProducto INT, IN aprobacion BOOLEAN, IN idDelAprobador INT)
BEGIN
    -- Actualizar el campo de "Aprobado" en el producto especificado
    UPDATE PRODUCTO SET Aprobado = aprobacion, IdAdminAprobador = idDelAprobador WHERE Id = idDelProducto;
    
	-- Si la aprobación es (no aprobado), eliminar registros relacionados al producto en las tablas de CATEGORIZACIONPRODUCTO, IMAGENPRODUCTO y VIDEOPRODUCTO
    -- y finalmente borrar el producto
    IF aprobacion = FALSE THEN
        DELETE FROM CATEGORIZACIONPRODUCTO WHERE IdProducto = idDelProducto;
        DELETE FROM IMAGENPRODUCTO WHERE IdProducto = idDelProducto;
        DELETE FROM VIDEOPRODUCTO WHERE IdProducto = idDelProducto;
        DELETE FROM PRODUCTO WHERE Id = idDelProducto;
    END IF;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE FiltrarProductosId (
    IN id_producto INT
)
BEGIN
    SELECT 
        p.id,
        p.idvendedor,
        p.nombre,
        p.descripcion,
        p.modo,
        p.precio,
        p.existencia,
        p.calificacion,
        p.aprobado,
        p.idadminaprobador,
        c.nombre AS categoria,
        GROUP_CONCAT(DISTINCT ip.imagen) AS imagenes,
        GROUP_CONCAT(DISTINCT vp.video) AS videos
    FROM 
        producto p
    JOIN 
        categorizacionproducto cp ON p.id = cp.idproducto
    JOIN 
        categoria c ON cp.idcategoria = c.id
    LEFT JOIN 
        imagenproducto ip ON p.id = ip.idproducto
    LEFT JOIN 
        videoproducto vp ON p.id = vp.idproducto
    WHERE
        p.id = id_producto
    GROUP BY 
        p.id;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE FiltrarProductos(
    IN p_nombre VARCHAR(255),
    IN p_precio_min DECIMAL(10, 2),
    IN p_precio_max DECIMAL(10, 2),
    IN p_categoria VARCHAR(255),
    IN p_calificacion INT
)
BEGIN
    SELECT 
        p.id,
        p.idvendedor,
        p.nombre,
        p.descripcion,
        p.modo,
        p.precio,
        p.existencia,
        p.calificacion,
        p.aprobado,
        p.idadminaprobador,
        c.nombre AS categoria,
        GROUP_CONCAT(DISTINCT ip.imagen) AS imagenes,
        GROUP_CONCAT(DISTINCT vp.video) AS videos
    FROM 
        producto p
    JOIN 
        categorizacionproducto cp ON p.id = cp.idproducto
    JOIN 
        categoria c ON cp.idcategoria = c.id
    LEFT JOIN 
        imagenproducto ip ON p.id = ip.idproducto
    LEFT JOIN 
        videoproducto vp ON p.id = vp.idproducto
    WHERE 
        (p_nombre IS NULL OR p.nombre COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_nombre COLLATE utf8mb4_unicode_ci, '%'))
        AND (p_precio_min IS NULL OR p.precio >= p_precio_min)
        AND (p_precio_max IS NULL OR p.precio <= p_precio_max)
        AND (p_categoria IS NULL OR c.nombre COLLATE utf8mb4_unicode_ci = p_categoria COLLATE utf8mb4_unicode_ci)
        AND (p_calificacion IS NULL OR ROUND(p.calificacion) = p_calificacion)
        AND (p.aprobado = true)
    GROUP BY 
        p.id, p.idvendedor, p.nombre, p.descripcion, p.modo, p.precio, p.existencia, p.calificacion, p.aprobado, p.idadminaprobador
    ORDER BY 
        p.nombre;
END;
// DELIMITER ;

-- ++++++++++TABLA CATEGORIZACIONPRODUCTO++++++++++ --
DELIMITER //
CREATE PROCEDURE CategorizarProducto(IN idProducto INT, IN nombreCategoria VARCHAR(30))
BEGIN
    DECLARE idCategoria INT;

    -- Buscar el id de la categoria con el nombre especificado
    SELECT Id INTO idCategoria FROM CATEGORIA WHERE Nombre = nombreCategoria;

    -- Insertar la información para categorizar el producto
    INSERT INTO CATEGORIZACIONPRODUCTO (IdProducto, IdCategoria) VALUES (idProducto, idCategoria);
END;
// DELIMITER ;

-- ++++++++++TABLA IMAGENPRODUCTO++++++++++ --
DELIMITER //
CREATE PROCEDURE InsertarImagenProducto(IN idProducto INT, IN imagen MEDIUMBLOB)
BEGIN

    -- Insertar la información para asociar una imagen con un producto
    INSERT INTO IMAGENPRODUCTO (IdProducto, Imagen) VALUES (idProducto, imagen);
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerImagenesProducto(IN producto INT)
BEGIN

    -- Realizar una selección de las imagenes relacionadas a determinado producto
    SELECT * FROM IMAGENPRODUCTO WHERE IdProducto = producto;
END;
// DELIMITER ;

-- ++++++++++TABLA VIDEOPRODUCTO++++++++++ --
DELIMITER //
CREATE PROCEDURE InsertarVideoProducto(IN idProducto INT, IN video LONGBLOB)
BEGIN

    -- Insertar la información para asociar un video con un producto
    INSERT INTO VIDEOPRODUCTO (IdProducto, Video) VALUES (idProducto, video);
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerVideosProducto(IN producto INT)
BEGIN

    -- Realizar una selección de los videos relacionadas a determinado producto
    SELECT * FROM VIDEOPRODUCTO WHERE IdProducto = producto;
END;
// DELIMITER ;

-- ++++++++++TABLA LISTA++++++++++ --
DELIMITER //
CREATE PROCEDURE CrearLista (IN idCreador INT, IN nombreLista VARCHAR(30), IN descripcionLista TEXT, IN visibilidadLista BOOLEAN, OUT idNuevaLista INT)
BEGIN
	-- Insertar los datos de la nueva lista a crear
    INSERT INTO LISTA (IdPropietario, Nombre, Descripcion, Visibilidad) VALUES (idCreador, nombreLista, descripcionLista, visibilidadLista);

	-- Devolver el Id de la lista recién creada
    SET IdNuevaLista = LAST_INSERT_ID();
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE InsertarImagenLista(IN idLista INT, IN imagen MEDIUMBLOB)
BEGIN

    -- Insertar la información para asociar una imagen con una lista
    INSERT INTO IMAGENLISTA (IdLista, Imagen) VALUES (idLista, imagen);
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerImagenesLista(IN idListaObtener INT)
BEGIN

    -- Obtener las imagenes de la lista
    SELECT * FROM IMAGENLISTA WHERE IdLista = idListaObtener;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE EliminarTodasImagenesLista(IN idListaEliminar INT)
BEGIN

    -- Eliminar las imagenes de la lista
    DELETE FROM IMAGENLISTA WHERE IdLista = idListaEliminar;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerTodasListasUsuario(IN idUsuario INT)
BEGIN

    -- Obtener las listas pertenecientes al usuario
    SELECT * FROM VistaLista WHERE IdPropietario = idUsuario;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerListaPorId(IN idLista INT)
BEGIN

    -- Obtener la lista indicada
    SELECT * FROM VistaLista WHERE Id = idLista;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE AgregarProductoALista(IN idProductoAgregar INT, IN idListaAgregar INT)
BEGIN
    -- Asignar un producto a una determinada lista
    INSERT INTO PRODUCTOENLISTADO (IdProducto, IdLista) VALUES (idProductoAgregar, idListaAgregar);
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerProductosLista(IN idLista INT)
BEGIN
    -- Obtener los productos pertenecientes a una determinada lista
    SELECT 
        p.Id,
        p.IdVendedor,
        p.Nombre,
        p.Descripcion,
        p.Modo,
        p.Precio,
        p.Existencia,
        p.Calificacion,
        p.Aprobado,
        p.IdAdminAprobador
    FROM 
        PRODUCTO p
    JOIN 
        PRODUCTOENLISTADO pe ON p.Id = pe.IdProducto
    WHERE 
        pe.IdLista = idLista;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE EditarLista (IN idLista INT, IN nombreLista VARCHAR(30), IN descripcionLista TEXT, IN visibilidadLista BOOLEAN)
BEGIN
	-- Actualizar los datos de la lista especificada
    UPDATE LISTA SET Nombre = nombreLista, Descripcion = descripcionLista, Visibilidad = visibilidadLista WHERE Id = idLista;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE EliminarProductoDeLista (IN idProductoEliminar INT, IN idListaEliminar INT)
BEGIN
	DELETE FROM PRODUCTOENLISTADO WHERE IdProducto = idProductoEliminar AND IdLista = idListaEliminar;
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE EliminarLista (IN idListaEliminar INT)
BEGIN
	DELETE FROM PRODUCTOENLISTADO WHERE IdLista = idListaEliminar;
	DELETE FROM IMAGENLISTA WHERE IdLista = idListaEliminar;
    DELETE FROM LISTA WHERE Id = idListaEliminar;
END;
// DELIMITER ;

-- ++++++++++TABLA VALORACION++++++++++ --
DELIMITER //
CREATE PROCEDURE CrearValoracion(IN idAutor INT, IN idProductoValorar INT, IN puntos INT, IN titulacion VARCHAR(100), IN contenido TEXT)
BEGIN

    -- Insertar la información del comentario en la tabla de valoraciones
    INSERT INTO VALORACION (IdUsuario, IdProducto, Puntuacion, Titulo, Comentario) VALUES (idAutor, idProductoValorar, puntos, titulacion, contenido);
END;
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerTodasValoracionesProducto(IN idProductoValorado INT)
BEGIN

    -- Insertar la información del comentario en la tabla de valoraciones
    SELECT * FROM VistaValoracion WHERE IdProducto = idProductoValorado;
END;
// DELIMITER ;

-- ++++++++++TABLA CARRITO++++++++++ --
DELIMITER //
CREATE PROCEDURE InsertarCarritoContenido(
    IN p_IdPropietario INT,
    IN p_Terminado BOOLEAN,
    IN p_IdProducto INT,
    IN p_Cantidad INT
)
BEGIN
    DECLARE v_IdCarrito INT;

    -- Insertar en la tabla carrito
    INSERT INTO carrito (IdPropietario, Terminado, FechaHoraFin)
    VALUES (p_IdPropietario, p_Terminado, NULL);

    -- Obtener el Id del último carrito insertado
    SET v_IdCarrito = LAST_INSERT_ID();

    -- Insertar en la tabla contenidocarrito con el Precio de la tabla producto
    INSERT INTO contenidocarrito (IdCarrito, IdProducto, Cantidad, Precio)
    VALUES (v_IdCarrito, p_IdProducto, p_Cantidad, (SELECT Precio FROM producto WHERE Id = p_IdProducto));
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE InsertarEnCarritoConPrecio(
    IN p_IdPropietario INT,
    IN p_Terminado BOOLEAN,
    IN p_IdProducto INT,
    IN p_Cantidad INT,
    IN p_Precio DECIMAL(10, 2)
)
BEGIN
    DECLARE v_IdCarrito INT;

    -- Insertar en la tabla carrito
    INSERT INTO carrito (IdPropietario, Terminado, FechaHoraFin)
    VALUES (p_IdPropietario, p_Terminado, Now());

    -- Obtener el Id del último carrito insertado
    SET v_IdCarrito = LAST_INSERT_ID();

    -- Insertar en la tabla contenidocarrito con el Precio proporcionado como parámetro
    INSERT INTO contenidocarrito (IdCarrito, IdProducto, Cantidad, Precio)
    VALUES (v_IdCarrito, p_IdProducto, p_Cantidad, p_Precio);
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerArticulosPendientes(
    IN p_IdPropietario INT
)
BEGIN
    SELECT 
        cc.IdProducto,
        cc.Cantidad,
        p.Nombre,
        cc.Precio  -- Obtener el precio de la tabla contenidocarrito
    FROM 
        carrito c
    JOIN 
        contenidocarrito cc ON c.Id = cc.IdCarrito
    JOIN 
        producto p ON cc.IdProducto = p.Id
    WHERE 
        c.Terminado = 0
        AND c.IdPropietario = p_IdPropietario;
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE EliminarCarritoContenido(
    IN p_IdPropietario INT,
    IN p_IdProducto INT
)
BEGIN
    DECLARE v_IdCarrito INT;
    
    -- Cursor para recorrer los carritos del propietario
    DECLARE done INT DEFAULT 0;
    DECLARE carrito_cursor CURSOR FOR
        SELECT Id FROM carrito WHERE IdPropietario = p_IdPropietario;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Abrir el cursor
    OPEN carrito_cursor;

    -- Recorrer los carritos
    read_loop: LOOP
        FETCH carrito_cursor INTO v_IdCarrito;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Eliminar de la tabla contenidocarrito
        DELETE FROM contenidocarrito
        WHERE IdCarrito = v_IdCarrito AND IdProducto = p_IdProducto;
    END LOOP;

    -- Cerrar el cursor
    CLOSE carrito_cursor;

    -- Se elimina contenido del carrito
    DELETE FROM carrito
    WHERE IdPropietario = p_IdPropietario AND NOT EXISTS (
        SELECT 1 FROM contenidocarrito WHERE IdCarrito = carrito.Id
    );
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE MarcarCarritoTerminado(
    IN p_IdPropietario INT
)
BEGIN
    -- Declarar variables para el cursor
    DECLARE v_IdProducto INT;
    DECLARE v_Cantidad INT;
    DECLARE done INT DEFAULT 0;

    -- Cursor para obtener los productos y cantidades del carrito
    DECLARE cur CURSOR FOR
        SELECT cc.IdProducto, cc.Cantidad
        FROM carrito c
        JOIN contenidocarrito cc ON c.Id = cc.IdCarrito
        WHERE c.Terminado = 0 AND c.IdPropietario = p_IdPropietario;

    -- Handler para el cursor
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Abrir el cursor
    OPEN cur;

    -- Leer el cursor y actualizar existencias
    read_loop: LOOP
        FETCH cur INTO v_IdProducto, v_Cantidad;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Actualizar existencias en la tabla producto
        UPDATE producto
        SET Existencia = Existencia - v_Cantidad
        WHERE Id = v_IdProducto AND Modo = false;
    END LOOP;

    -- Cerrar el cursor
    CLOSE cur;

    -- Actualizar Terminado y FechaHoraFin en la tabla carrito
    UPDATE carrito
    SET Terminado = 1,
        FechaHoraFin = NOW()
    WHERE IdPropietario = p_IdPropietario;
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE InsertarMensaje(
    IN p_IdArticulo INT,
    IN p_IdEmisor INT,
    IN p_IdReceptor INT,
    IN p_Contenido TEXT    
)
BEGIN
    INSERT INTO mensaje (IdEmisor, IdReceptor, Contenido, FechaHora, Finalizado, IdArticulo)
    VALUES (p_IdEmisor, p_IdReceptor, p_Contenido, NOW(), 0, p_IdArticulo);
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE MostrarMensajes(
    IN p_IdEmisor INT
)
BEGIN
    SELECT Id, IdEmisor, IdReceptor, Contenido, FechaHora, Finalizado, IdArticulo
    FROM mensaje
    WHERE (IdEmisor = p_IdEmisor OR IdReceptor = p_IdEmisor)
      AND Finalizado = 0;
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerCotizacionesPorIdProducto(
    IN p_IdProducto INT
)
BEGIN
    SELECT 
        Id,
        IdProducto,
        Nombre,
        Descripcion,
        Precio,
        Estado,
        Cantidad
    FROM 
        cotizacion
    WHERE 
        IdProducto = p_IdProducto
        AND Estado = 0;
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE InsertarCotizacion(
    IN p_IdProducto INT,
    IN p_Nombre VARCHAR(255),
    IN p_Descripcion TEXT,
    IN p_Precio DECIMAL(10, 2),
    IN p_Estado BOOLEAN,
    IN p_Cantidad INT
)
BEGIN
    DECLARE productoExistente INT;

    -- Comprobar si IdProducto ya existe y su Estado es 0
    SELECT COUNT(*) INTO productoExistente
    FROM cotizacion
    WHERE IdProducto = p_IdProducto AND Estado = 0;

    -- Si no existe o su Estado es 1, insertar el nuevo registro
    IF productoExistente = 0 THEN
        INSERT INTO cotizacion (IdProducto, Nombre, Descripcion, Precio, Estado, Cantidad)
        VALUES (p_IdProducto, p_Nombre, p_Descripcion, p_Precio, p_Estado, p_Cantidad);
    END IF;
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ActualizarPrecioCotizacion(
    IN p_IdProducto INT,
    IN p_Precio DECIMAL(10,2),
    IN p_cantidad INT
)
BEGIN
    UPDATE cotizacion
    SET Precio = p_Precio,
        cantidad = p_cantidad
    WHERE IdProducto = p_IdProducto;
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ActualizarEstadoCotizacion(
    IN p_IdProducto INT
)
BEGIN
    UPDATE cotizacion
    SET Estado = 1
    WHERE IdProducto = p_IdProducto;
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerDetalleCarritoMejorado(
    IN parametroIdPropietario INT,
    IN fechaInicio DATE,
    IN fechaFin DATE,
    IN nombreCategoria VARCHAR(255)
)
BEGIN
    SELECT 
        `carrito`.`FechaHoraFin`, 
        `contenidocarrito`.`Precio`, 
        `producto`.`Nombre` AS 'NombreProducto', 
        `producto`.`Calificacion`, 
        `categorizacionproducto`.`IdProducto`, 
        `categorizacionproducto`.`IdCategoria`, 
        `categoria`.`Nombre` AS 'NombreCategoria'
    FROM 
        `carrito`
    LEFT JOIN 
        `contenidocarrito` ON `contenidocarrito`.`IdCarrito` = `carrito`.`Id` 
    LEFT JOIN 
        `producto` ON `contenidocarrito`.`IdProducto` = `producto`.`Id` 
    LEFT JOIN 
        `categorizacionproducto` ON `categorizacionproducto`.`IdProducto` = `producto`.`Id` 
    LEFT JOIN 
        `categoria` ON `categorizacionproducto`.`IdCategoria` = `categoria`.`Id`
    WHERE 
        `carrito`.`IdPropietario` = parametroIdPropietario
        AND `carrito`.`Terminado` = true
        AND (
            (fechaInicio IS NOT NULL AND fechaFin IS NOT NULL AND `carrito`.`FechaHoraFin` BETWEEN fechaInicio AND fechaFin)
            OR (fechaInicio IS NULL AND fechaFin IS NULL)
            OR (fechaInicio IS NOT NULL AND fechaFin IS NULL AND `carrito`.`FechaHoraFin` >= fechaInicio)
            OR (fechaInicio IS NULL AND fechaFin IS NOT NULL AND `carrito`.`FechaHoraFin` <= fechaFin)
        )
        AND (
            nombreCategoria IS NULL 
            OR `categoria`.`Nombre` = nombreCategoria
        );
END; 
// DELIMITER ;

DELIMITER //
CREATE PROCEDURE ObtenerVentas(
    IN parametroIdVendedor INT,
    IN fechaInicio DATE,
    IN fechaFin DATE,
    IN nombreCategoria VARCHAR(255)
)
BEGIN
    SELECT 
        `carrito`.`FechaHoraFin`, 
        `contenidocarrito`.`Precio`, 
        `producto`.`Nombre` AS 'NombreProducto', 
        `producto`.`Calificacion`, 
        `categorizacionproducto`.`IdProducto`, 
        `categorizacionproducto`.`IdCategoria`, 
        `categoria`.`Nombre` AS 'NombreCategoria',
        `producto`.`Existencia` AS 'Existencia'
    FROM 
        `carrito`
    LEFT JOIN 
        `contenidocarrito` ON `contenidocarrito`.`IdCarrito` = `carrito`.`Id` 
    LEFT JOIN 
        `producto` ON `contenidocarrito`.`IdProducto` = `producto`.`Id` 
    LEFT JOIN 
        `categorizacionproducto` ON `categorizacionproducto`.`IdProducto` = `producto`.`Id` 
    LEFT JOIN 
        `categoria` ON `categorizacionproducto`.`IdCategoria` = `categoria`.`Id`
    WHERE 
        `producto`.`IdVendedor` = parametroIdVendedor
        AND `carrito`.`Terminado` = true
        AND (
            (fechaInicio IS NOT NULL AND fechaFin IS NOT NULL AND `carrito`.`FechaHoraFin` BETWEEN fechaInicio AND fechaFin)
            OR (fechaInicio IS NULL AND fechaFin IS NULL)
            OR (fechaInicio IS NOT NULL AND fechaFin IS NULL AND `carrito`.`FechaHoraFin` >= fechaInicio)
            OR (fechaInicio IS NULL AND fechaFin IS NOT NULL AND `carrito`.`FechaHoraFin` <= fechaFin)
        )
        AND (
            nombreCategoria IS NULL 
            OR `categoria`.`Nombre` = nombreCategoria
        );
END;
// DELIMITER ;
