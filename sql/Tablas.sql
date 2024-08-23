CREATE DATABASE Proyecto_BDM;
USE Proyecto_BDM;

CREATE TABLE USUARIO (
    Id INT AUTO_INCREMENT,
    NombreUsuario VARCHAR(30),
    Correo VARCHAR(30),
    Contrasena VARCHAR(30),
    Visibilidad BOOLEAN,
    Rol INT,
    Nombres VARCHAR(50),
    ApellidoPaterno VARCHAR(30),
    ApellidoMaterno VARCHAR(30),
    FechaNacimiento DATE,
    ImagenPerfil MEDIUMBLOB,
    Sexo BOOLEAN,
    FechaIngreso DATETIME,
    
    PRIMARY KEY (Id),
    
    INDEX IdxNombre (NombreUsuario),
    INDEX IdxCorreo (Correo),
    INDEX IdxContrasena (Contrasena)
);

CREATE TABLE MENSAJE (
    Id INT AUTO_INCREMENT,
    IdArticulo INT,
    IdEmisor INT,
    IdReceptor INT,
    Contenido TEXT,
    FechaHora DATETIME,
    Finalizado INT,
    
    FOREIGN KEY (IdEmisor) REFERENCES USUARIO(Id),
    FOREIGN KEY (IdReceptor) REFERENCES USUARIO(Id),
    PRIMARY KEY (Id, IdEmisor, IdReceptor),
    
    INDEX IdxEmisor (IdEmisor),
    INDEX IdxReceptor (IdReceptor)
);

CREATE TABLE LISTA (
    Id INT AUTO_INCREMENT,
    IdPropietario INT,
    Nombre VARCHAR(30),
    Descripcion TEXT,
    Visibilidad BOOLEAN,
    
    FOREIGN KEY (IdPropietario) REFERENCES USUARIO(Id),
    PRIMARY KEY (Id, IdPropietario),
    
    INDEX IdxPropietario (IdPropietario)
);

CREATE TABLE IMAGENLISTA (
    Id INT AUTO_INCREMENT,
    IdLista INT,
    Imagen MEDIUMBLOB,
    
    FOREIGN KEY (IdLista) REFERENCES LISTA(Id),
    PRIMARY KEY (Id, IdLista),
    
    INDEX IdxLista (IdLista)
);

CREATE TABLE PRODUCTO (
    Id INT AUTO_INCREMENT,
    IdVendedor INT,
    Nombre VARCHAR(50),
    Descripcion TEXT,
    Modo BOOLEAN,
    Precio DECIMAL(8, 2),
    Existencia INT,
    Calificacion DECIMAL(3, 2),
    Aprobado BOOLEAN,
    IdAdminAprobador INT,
    
    FOREIGN KEY (IdVendedor) REFERENCES USUARIO(Id),
    FOREIGN KEY (IdAdminAprobador) REFERENCES USUARIO(Id),
    PRIMARY KEY (Id, IdVendedor),
    
    INDEX IdxVendedor (IdVendedor)
);

CREATE TABLE IMAGENPRODUCTO (
    Id INT AUTO_INCREMENT,
    IdProducto INT,
    Imagen MEDIUMBLOB,
    
    FOREIGN KEY (IdProducto) REFERENCES PRODUCTO(Id),
    PRIMARY KEY (Id, IdProducto),
    
    INDEX IdxProducto (IdProducto)
);

CREATE TABLE VIDEOPRODUCTO (
    Id INT AUTO_INCREMENT,
    IdProducto INT,
    Video LONGBLOB,
    
    FOREIGN KEY (IdProducto) REFERENCES PRODUCTO(Id),
    PRIMARY KEY (Id, IdProducto),
    
    INDEX IdxProducto (IdProducto)
);

CREATE TABLE PRODUCTOENLISTADO (
    IdProducto INT,
    IdLista INT,
    
    FOREIGN KEY (IdProducto) REFERENCES PRODUCTO(Id),
    FOREIGN KEY (IdLista) REFERENCES LISTA(Id),
    PRIMARY KEY (IdProducto, IdLista),
    
    INDEX IdxProducto (IdProducto),
    INDEX IdxLista (IdLista)
);

CREATE TABLE VALORACION (
	Id INT AUTO_INCREMENT,
    IdUsuario INT,
    IdProducto INT,
    Puntuacion INT,
    Titulo VARCHAR(100),
    Comentario TEXT,
    FechaHora DATETIME,
    
    FOREIGN KEY (IdUsuario) REFERENCES USUARIO(Id),
    FOREIGN KEY (IdProducto) REFERENCES PRODUCTO(Id),
    PRIMARY KEY (Id, IdUsuario, IdProducto),
    
    INDEX IdxUsuario (IdUsuario),
    INDEX IdxProducto (IdProducto)
);

CREATE TABLE CARRITO (
    Id INT AUTO_INCREMENT,
    IdPropietario INT,
    Terminado BOOLEAN,
    FechaHoraFin DATETIME,
    
    FOREIGN KEY (IdPropietario) REFERENCES USUARIO(Id),
    PRIMARY KEY (Id, IdPropietario),
    
    INDEX IdxPropietario (IdPropietario)
);

CREATE TABLE CONTENIDOCARRITO (
    IdCarrito INT,
    IdProducto INT,
    Cantidad INT,
    Precio DECIMAL(8, 2),
    
    FOREIGN KEY (IdCarrito) REFERENCES CARRITO(Id),
    FOREIGN KEY (IdProducto) REFERENCES PRODUCTO(Id),
    PRIMARY KEY (IdCarrito, IdProducto),
    
    INDEX IdxCarrito (IdCarrito),
    INDEX IdxProducto (IdProducto)
);

CREATE TABLE CATEGORIA (
    Id INT AUTO_INCREMENT,
    IdCreador INT,
    Nombre VARCHAR(30),
    Descripcion TEXT,
    
    FOREIGN KEY (IdCreador) REFERENCES USUARIO(Id),
    PRIMARY KEY (Id),
    
    INDEX IdxCreador (IdCreador)
);

CREATE TABLE CATEGORIZACIONPRODUCTO (
    IdProducto INT,
    IdCategoria INT,
    
    FOREIGN KEY (IdProducto) REFERENCES PRODUCTO(Id),
    FOREIGN KEY (IdCategoria) REFERENCES CATEGORIA(Id),
    PRIMARY KEY (IdProducto, IdCategoria),
    
    INDEX IdxProducto (IdProducto),
    INDEX IdxCategoria (IdCategoria)
);

CREATE TABLE COTIZACION (
  Id INT NOT NULL AUTO_INCREMENT,
  IdProducto INT,
  Nombre TEXT,
  Descripcion TEXT,
  Precio DECIMAL(8, 2),
  Estado INT,
  Cantidad INT,

  FOREIGN KEY (IdProducto) REFERENCES PRODUCTO(Id),
  PRIMARY KEY (Id, IdProducto),

  INDEX IdxProducto (IdProducto)
);
