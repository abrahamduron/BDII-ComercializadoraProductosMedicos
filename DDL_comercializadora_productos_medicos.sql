--ESQUEMA DB para proyecto DB2. grupo 3

CREATE DATABASE productos_medicos;
GO

USE productos_medicos;
GO

-- Tablas Maestras
CREATE TABLE bancos (
    id_banco INT IDENTITY(1,1) PRIMARY KEY,
    nombre_banco VARCHAR(100) NOT NULL,
    activo BIT DEFAULT 1
);

CREATE TABLE categorias_productos (
    id_categoria INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(200) NULL,
    tipo VARCHAR(20) CHECK (tipo IN ('PRODUCTO', 'MATERIA_PRIMA')) DEFAULT 'PRODUCTO'
);

CREATE TABLE clientes (
    id INT IDENTITY(1,1) PRIMARY KEY,
    codigo_cliente VARCHAR(20) NULL,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(200) NULL,
    telefono VARCHAR(20) NULL,
    correo VARCHAR(100) NULL,
    tipo_cliente VARCHAR(20) CHECK (tipo_cliente IN ('MAYORISTA', 'DETALLE')) DEFAULT 'DETALLE',
    limite_credito DECIMAL(15,2) DEFAULT 0.00,
    saldo_actual DECIMAL(15,2) DEFAULT 0.00,
    activo BIT DEFAULT 1,
    fecha_registro DATETIME DEFAULT GETDATE()
);

CREATE TABLE cuentas_bancarias (
    id_cuenta INT IDENTITY(1,1) PRIMARY KEY,
    id_banco INT NOT NULL,
    numero_cuenta VARCHAR(50) NOT NULL UNIQUE,
    tipo_cuenta VARCHAR(20) CHECK (tipo_cuenta IN ('CORRIENTE', 'AHORROS')) DEFAULT 'CORRIENTE',
    saldo DECIMAL(12,2) DEFAULT 0.00,
    activo BIT DEFAULT 1,
    FOREIGN KEY (id_banco) REFERENCES bancos(id_banco)
);

CREATE TABLE proveedores (
    id INT IDENTITY(1,1) PRIMARY KEY,
    codigo_proveedor VARCHAR(20) NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(200) NULL,
    telefono VARCHAR(20) NULL,
    correo VARCHAR(100) NULL,
    limite_credito DECIMAL(15,2) DEFAULT 0.00,
    saldo_actual DECIMAL(15,2) DEFAULT 0.00,
    activo BIT DEFAULT 1,
    fecha_registro DATETIME DEFAULT GETDATE()
);

CREATE TABLE bodegas (
    id_bodega INT IDENTITY(1,1) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(200) NULL,
    capacidad INT NULL,
    activo BIT DEFAULT 1
);

-- Tabla de Productos (Inventario)
CREATE TABLE inventario (
    id_producto INT IDENTITY(1,1) PRIMARY KEY,
    codigo_producto VARCHAR(50) NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(MAX) NULL,
    id_categoria INT NULL,
    tipo VARCHAR(20) CHECK (tipo IN ('PRODUCTO_TERMINADO', 'MATERIA_PRIMA')) DEFAULT 'PRODUCTO_TERMINADO',
    cantidad INT DEFAULT 0,
    stock_minimo INT DEFAULT 0,
    precio DECIMAL(10,2) DEFAULT 0.00,
    precio_compra DECIMAL(15,2) DEFAULT 0.00,
    precio_venta DECIMAL(15,2) DEFAULT 0.00,
    precio_mayorista DECIMAL(15,2) DEFAULT 0.00,
    requiere_receta BIT DEFAULT 0,
    activo BIT DEFAULT 1,
    FOREIGN KEY (id_categoria) REFERENCES categorias_productos(id_categoria)
);

-- Módulo de Compras
CREATE TABLE ordenes_compra (
    id INT IDENTITY(1,1) PRIMARY KEY,
    numero_orden VARCHAR(50) NULL UNIQUE,
    id_proveedor INT NOT NULL,
    fecha_orden DATETIME DEFAULT GETDATE(),
    fecha_esperada_entrega DATE NULL,
    estado VARCHAR(20) CHECK (estado IN ('PENDIENTE', 'APROBADA', 'RECIBIDA', 'CANCELADA')) DEFAULT 'PENDIENTE',
    subtotal DECIMAL(15,2) DEFAULT 0.00,
    iva DECIMAL(15,2) DEFAULT 0.00,
    total DECIMAL(15,2) DEFAULT 0.00,
    observaciones VARCHAR(500) NULL,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id)
);

CREATE TABLE detalle_orden_compra (
    id_detalle_orden INT IDENTITY(1,1) PRIMARY KEY,
    id_orden_compra INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad_solicitada INT NOT NULL,
    cantidad_recibida INT DEFAULT 0,
    precio_unitario DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (id_orden_compra) REFERENCES ordenes_compra(id),
    FOREIGN KEY (id_producto) REFERENCES inventario(id_producto)
);

-- Recepción de Mercadería
CREATE TABLE recepciones_mercaderia (
    id_recepcion INT IDENTITY(1,1) PRIMARY KEY,
    id_orden_compra INT NOT NULL,
    numero_recepcion VARCHAR(50) NOT NULL UNIQUE,
    fecha_recepcion DATETIME DEFAULT GETDATE(),
    id_bodega INT NOT NULL,
    estado VARCHAR(20) CHECK (estado IN ('PARCIAL', 'COMPLETA', 'RECHAZADA')) DEFAULT 'PARCIAL',
    observaciones VARCHAR(500) NULL,
    FOREIGN KEY (id_orden_compra) REFERENCES ordenes_compra(id),
    FOREIGN KEY (id_bodega) REFERENCES bodegas(id_bodega)
);

CREATE TABLE detalle_recepcion (
    id_detalle_recepcion INT IDENTITY(1,1) PRIMARY KEY,
    id_recepcion INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad_recibida INT NOT NULL,
    cantidad_aceptada INT NOT NULL,
    cantidad_rechazada INT DEFAULT 0,
    motivo_rechazo VARCHAR(200) NULL,
    lote VARCHAR(100) NULL,
    fecha_vencimiento DATE NULL,
    FOREIGN KEY (id_recepcion) REFERENCES recepciones_mercaderia(id_recepcion),
    FOREIGN KEY (id_producto) REFERENCES inventario(id_producto)
);

-- Inventario por Bodegas
CREATE TABLE inventario_bodegas (
    id_inventario INT IDENTITY(1,1) PRIMARY KEY,
    id_bodega INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad_disponible INT DEFAULT 0,
    cantidad_reservada INT DEFAULT 0,
    lote VARCHAR(100) NULL,
    fecha_vencimiento DATE NULL,
    fecha_actualizacion DATETIME DEFAULT GETDATE(),
    UNIQUE (id_bodega, id_producto, lote),
    FOREIGN KEY (id_bodega) REFERENCES bodegas(id_bodega),
    FOREIGN KEY (id_producto) REFERENCES inventario(id_producto)
);

-- Módulo de Ventas
CREATE TABLE facturas (
    id_factura INT IDENTITY(1,1) PRIMARY KEY,
    numero_factura VARCHAR(50) NOT NULL UNIQUE,
    id_cliente INT NOT NULL,
    fecha_factura DATETIME DEFAULT GETDATE(),
    tipo_venta VARCHAR(20) CHECK (tipo_venta IN ('CONTADO', 'CREDITO')) DEFAULT 'CONTADO',
    subtotal DECIMAL(15,2) DEFAULT 0.00,
    iva DECIMAL(15,2) DEFAULT 0.00,
    total DECIMAL(15,2) DEFAULT 0.00,
    saldo_pendiente DECIMAL(15,2) DEFAULT 0.00,
    estado VARCHAR(20) CHECK (estado IN ('PENDIENTE', 'PAGADA', 'CANCELADA')) DEFAULT 'PENDIENTE',
    observaciones VARCHAR(500) NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

CREATE TABLE detalle_factura (
    id_detalle_factura INT IDENTITY(1,1) PRIMARY KEY,
    id_factura INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(15,2) NOT NULL,
    descuento DECIMAL(15,2) DEFAULT 0.00,
    subtotal DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (id_factura) REFERENCES facturas(id_factura),
    FOREIGN KEY (id_producto) REFERENCES inventario(id_producto)
);

-- Sistema de Pagos
CREATE TABLE recibos_pago (
    id_recibo_pago INT IDENTITY(1,1) PRIMARY KEY,
    numero_recibo VARCHAR(50) NOT NULL UNIQUE,
    id_cliente INT NOT NULL,
    fecha_recibo DATETIME DEFAULT GETDATE(),
    monto_total DECIMAL(15,2) NOT NULL,
    forma_pago VARCHAR(20) CHECK (forma_pago IN ('EFECTIVO', 'TARJETA', 'TRANSFERENCIA')) DEFAULT 'EFECTIVO',
    observaciones VARCHAR(500) NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

CREATE TABLE aplicacion_pagos (
    id_aplicacion INT IDENTITY(1,1) PRIMARY KEY,
    id_recibo_pago INT NOT NULL,
    id_factura INT NOT NULL,
    monto_aplicado DECIMAL(15,2) NOT NULL,
    fecha_aplicacion DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (id_recibo_pago) REFERENCES recibos_pago(id_recibo_pago),
    FOREIGN KEY (id_factura) REFERENCES facturas(id_factura)
);

CREATE TABLE recibos (
    id_recibo INT IDENTITY(1,1) PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha DATE NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

-- Módulo Bancos
CREATE TABLE depositos (
    id_deposito INT IDENTITY(1,1) PRIMARY KEY,
    id_cuenta INT NOT NULL,
    fecha DATE NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_cuenta) REFERENCES cuentas_bancarias(id_cuenta)
);

CREATE TABLE pagos_proveedores (
    id_pago INT IDENTITY(1,1) PRIMARY KEY,
    id_proveedor INT NOT NULL,
    id_cuenta INT NOT NULL,
    numero_pago VARCHAR(50) NOT NULL UNIQUE,
    fecha_pago DATETIME DEFAULT GETDATE(),
    tipo_pago VARCHAR(20) CHECK (tipo_pago IN ('CHEQUE', 'TRANSFERENCIA')) DEFAULT 'TRANSFERENCIA',
    monto_total DECIMAL(15,2) NOT NULL,
    referencia VARCHAR(100) NULL,
    observaciones VARCHAR(500) NULL,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id),
    FOREIGN KEY (id_cuenta) REFERENCES cuentas_bancarias(id_cuenta)
);

CREATE TABLE detalle_pago_proveedor (
    id_detalle_pago INT IDENTITY(1,1) PRIMARY KEY,
    id_pago INT NOT NULL,
    id_orden_compra INT NOT NULL,
    monto_aplicado DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (id_pago) REFERENCES pagos_proveedores(id_pago),
    FOREIGN KEY (id_orden_compra) REFERENCES ordenes_compra(id)
);

-- Devoluciones
CREATE TABLE devoluciones_proveedor (
    id INT IDENTITY(1,1) PRIMARY KEY,
    numero_devolucion VARCHAR(50) NULL UNIQUE,
    orden_id INT NOT NULL,
    id_proveedor INT NOT NULL,
    fecha_devolucion DATETIME DEFAULT GETDATE(),
    motivo VARCHAR(200) NULL,
    total_devolucion DECIMAL(15,2) DEFAULT 0.00,
    estado VARCHAR(20) CHECK (estado IN ('PENDIENTE', 'PROCESADA')) DEFAULT 'PENDIENTE',
    FOREIGN KEY (orden_id) REFERENCES ordenes_compra(id),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id)
);

CREATE TABLE detalle_devolucion (
    id_detalle_devolucion INT IDENTITY(1,1) PRIMARY KEY,
    id_devolucion INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(200) NULL,
    lote VARCHAR(100) NULL,
    FOREIGN KEY (id_devolucion) REFERENCES devoluciones_proveedor(id),
    FOREIGN KEY (id_producto) REFERENCES inventario(id_producto)
);

-- Elaboración de Productos
CREATE TABLE elaboracion_productos (
    id_elaboracion INT IDENTITY(1,1) PRIMARY KEY,
    numero_elaboracion VARCHAR(50) NOT NULL UNIQUE,
    fecha_elaboracion DATETIME DEFAULT GETDATE(),
    id_producto_elaborado INT NOT NULL,
    cantidad_producida INT NOT NULL,
    costo_total DECIMAL(15,2) DEFAULT 0.00,
    estado VARCHAR(20) CHECK (estado IN ('PLANIFICADA', 'EN_PROCESO', 'COMPLETADA')) DEFAULT 'PLANIFICADA',
    FOREIGN KEY (id_producto_elaborado) REFERENCES inventario(id_producto)
);

CREATE TABLE detalle_elaboracion (
    id_detalle_elaboracion INT IDENTITY(1,1) PRIMARY KEY,
    id_elaboracion INT NOT NULL,
    id_materia_prima INT NOT NULL,
    cantidad_requerida DECIMAL(15,4) NOT NULL,
    cantidad_utilizada DECIMAL(15,4) NOT NULL,
    costo_unitario DECIMAL(15,4) DEFAULT 0.0000,
    costo_total DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (id_elaboracion) REFERENCES elaboracion_productos(id_elaboracion),
    FOREIGN KEY (id_materia_prima) REFERENCES inventario(id_producto)
);

-- Tablas de Auditoría y Control
CREATE TABLE movimientos_inventario (
    id_movimiento INT IDENTITY(1,1) PRIMARY KEY,
    fecha_movimiento DATETIME DEFAULT GETDATE(),
    tipo_movimiento VARCHAR(30) CHECK (tipo_movimiento IN ('COMPRA', 'VENTA', 'ELABORACION', 'DEVOLUCION_PROVEEDOR', 'AJUSTE_INVENTARIO', 'TRASLADO_BODEGA')) NOT NULL,
    id_producto INT NOT NULL,
    id_bodega INT NOT NULL,
    cantidad INT NOT NULL,
    cantidad_anterior INT NULL,
    cantidad_nueva INT NULL,
    referencia VARCHAR(100) NULL,
    usuario VARCHAR(100) NULL,
    observaciones VARCHAR(500) NULL,
    FOREIGN KEY (id_producto) REFERENCES inventario(id_producto),
    FOREIGN KEY (id_bodega) REFERENCES bodegas(id_bodega)
);

CREATE TABLE arqueos_caja (
    id_arqueo INT IDENTITY(1,1) PRIMARY KEY,
    fecha_arqueo DATE NOT NULL,
    usuario VARCHAR(100) NOT NULL,
    efectivo_inicial DECIMAL(15,2) DEFAULT 0.00,
    efectivo_final DECIMAL(15,2) DEFAULT 0.00,
    total_ventas_contado DECIMAL(15,2) DEFAULT 0.00,
    total_recibos DECIMAL(15,2) DEFAULT 0.00,
    diferencia DECIMAL(15,2) DEFAULT 0.00,
    observaciones VARCHAR(500) NULL,
    estado VARCHAR(20) CHECK (estado IN ('ABIERTA', 'CERRADA')) DEFAULT 'ABIERTA'
);

CREATE TABLE saldos_diarios (
    id_saldo INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL UNIQUE,
    total_ventas DECIMAL(15,2) DEFAULT 0.00,
    total_compras DECIMAL(15,2) DEFAULT 0.00,
    total_pagos_proveedores DECIMAL(15,2) DEFAULT 0.00,
    total_recibos_clientes DECIMAL(15,2) DEFAULT 0.00,
    saldo_clientes DECIMAL(15,2) DEFAULT 0.00,
    saldo_proveedores DECIMAL(15,2) DEFAULT 0.00
);

GO