# Proyecto de Clase - Base de Datos 2
## Sistema para Comercializadora de Productos Médicos

## Descripción del Proyecto

Sistema de información desarrollado para una empresa dedicada a la compra, elaboración y venta de productos farmacéuticos y similares. El sistema maneja procesos de compras, ventas, inventario y gestión de proveedores.

## Objetivo

Automatizar los procesos manuales actuales basados en hojas electrónicas, proporcionando un sistema integral que optimice las operaciones diarias de la comercializadora.

## Funcionalidades Principales

### Gestión de Compras
- Verificación automática de niveles de inventario
- Generación de órdenes de compra por crédito
- Control de límites crediticios con proveedores
- Recepción y verificación de productos

### Control de Inventarios
- Verificación de productos recibidos (fecha vencimiento, cantidad, etc.)
- Gestión de diferencias en recepción
- Almacenamiento de productos
- Control de elaboración de nuevos productos

### Gestión de Ventas
- **Ventas al por mayor**: Verificación de estado de cuenta y existencias
- **Ventas al detalle**: Facturación manual y cobro en caja
- Devoluciones por vencimiento
- Elaboración de productos

### Gestión Financiera
- Pagos a proveedores mediante cheques/transferencias
- Recepción de pagos de clientes
- Estados de cuenta de proveedores y clientes
- Reportes mensuales de saldos

## Tecnologías Utilizadas

- **XAMPP** - Entorno de desarrollo local
- **APACHE** - Servidor web
- **PHP - TRANSACT-SQL** - Entorno de programación backend
- **HTML-CSS-PHP-JavaScript** - Entorno de programación frontend
- **SQLSERVER** - Base de datos
- **SQL Server Management Studio** - Gestor de base de datos

## Equipo de Desarrollo

| Docente | Asignatura |
|--------|--------|
| Guillermo Enrique Borjas Rodriguez  | IS601 Base de datos 2 |


| Nombre | No. Cuenta |
|--------|--------|
| Ana Gabriela Zapata | 20161003654 |
| Cristian Rivera | 20191004778 |
| Miguel Hernandez | 20181033312 |
| Abraham Durón | 20131001422 |
| Hermes Aguilera | |

## Estructura del Proyecto
comercializadora_medicos/
│
├── index.php
├── config/
│   └── database.php
├── modules/
│   ├── clientes/
│   ├── proveedores/
│   ├── productos/
│   ├── compras/
│   ├── ventas/
│   ├── inventario/
│   └── reportes/
├── css/
│   └── styles.css
└── js/
    └── scripts.js