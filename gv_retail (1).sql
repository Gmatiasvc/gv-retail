-- phpMyAdmin SQL Dump
-- Base de datos: gv_retail

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ------------------------------------------------------
-- CREACIÓN DE LA BASE DE DATOS
-- ------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `gv_retail`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `gv_retail`;

-- ------------------------------------------------------
-- TABLA: CLIENTES
-- ------------------------------------------------------
CREATE TABLE `clientes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo_documento` ENUM('DNI','RUC') NOT NULL,
  `num_documento` VARCHAR(11) NOT NULL,
  `razon_social` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `telefono` VARCHAR(20) DEFAULT NULL,
  `puntos` INT DEFAULT 0,
  `activo` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_num_documento` (`num_documento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- TABLA: PRODUCTOS
-- ------------------------------------------------------
CREATE TABLE `productos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `barcode` VARCHAR(50) DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT NULL,
  `precio` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `stock_minimo` INT DEFAULT 10,
  `imagen` VARCHAR(255) DEFAULT NULL,
  `activo` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- TABLA: DESCUENTOS
-- ------------------------------------------------------
CREATE TABLE `descuentos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `tipo` ENUM('PORCENTAJE', 'MONTO') NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NOT NULL,
  `activo` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- TABLA: PRODUCTO DESCUENTOS (Relación muchos a muchos)
-- ------------------------------------------------------
CREATE TABLE `producto_descuentos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto_id` BIGINT UNSIGNED NOT NULL,
  `descuento_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_prod_desc_producto` FOREIGN KEY (`producto_id`)
    REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prod_desc_descuento` FOREIGN KEY (`descuento_id`)
    REFERENCES `descuentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- TABLA: USERS
-- ------------------------------------------------------
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- TABLA: MÉTODOS DE PAGO
-- ------------------------------------------------------
CREATE TABLE `metodos_pago` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `activo` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_nombre_pago` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- TABLA: CAJAS (Puntos de Venta Físicos)
-- ------------------------------------------------------
CREATE TABLE `cajas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `activa` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- TABLA: TURNOS_CAJA (Apertura, Cierre y Control de Dinero)
-- ------------------------------------------------------
CREATE TABLE `turnos_caja` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `caja_id` BIGINT UNSIGNED NOT NULL,
  `usuario_id` BIGINT UNSIGNED NOT NULL,
  `fecha_apertura` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `fecha_cierre` DATETIME DEFAULT NULL,
  `monto_apertura` DECIMAL(10,2) NOT NULL,
  `monto_ventas_efectivo` DECIMAL(10,2) DEFAULT 0.00,
  `monto_ventas_otros` DECIMAL(10,2) DEFAULT 0.00,
  `monto_ingresos` DECIMAL(10,2) DEFAULT 0.00,
  `monto_egresos` DECIMAL(10,2) DEFAULT 0.00,
  `monto_estimado_cierre` DECIMAL(10,2) GENERATED ALWAYS AS 
    ((`monto_apertura` + `monto_ventas_efectivo` + `monto_ingresos`) - `monto_egresos`) STORED,
  `monto_real_cierre` DECIMAL(10,2) DEFAULT NULL,
  `diferencia` DECIMAL(10,2) GENERATED ALWAYS AS 
    ((`monto_real_cierre` - `monto_estimado_cierre`)) STORED,
  `estado` ENUM('ABIERTO', 'CERRADO') DEFAULT 'ABIERTO',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_turnos_caja` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`),
  CONSTRAINT `fk_turnos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- TABLA: VENTAS
-- ------------------------------------------------------
CREATE TABLE `ventas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo_comprobante` ENUM('BOLETA', 'FACTURA') NOT NULL,
  `serie_comprobante` VARCHAR(4) NOT NULL,
  `numero_comprobante` INT UNSIGNED NOT NULL,
  `fecha_emision` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `cliente_id` BIGINT UNSIGNED NOT NULL,
  `cajero_id` BIGINT UNSIGNED NULL,
  `total` DECIMAL(10,2) DEFAULT 0.00,
  `descuento` DECIMAL(10,2) DEFAULT 0.00,
  `puntos_usados` INT DEFAULT 0,
  `pago_recibido` DECIMAL(10,2) DEFAULT 0.00,
  `cambio` DECIMAL(10,2) DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ventas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `fk_ventas_cajero` FOREIGN KEY (`cajero_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- TABLA: DETALLE VENTA
-- ------------------------------------------------------
CREATE TABLE `venta_detalles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `venta_id` BIGINT UNSIGNED NOT NULL,
  `producto_id` BIGINT UNSIGNED NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 1,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `descuento` DECIMAL(10,2) DEFAULT 0.00,
  `subtotal` DECIMAL(10,2) GENERATED ALWAYS AS (((`cantidad` * `precio_unitario`) - `descuento`)) STORED,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_detalles_venta` FOREIGN KEY (`venta_id`) 
    REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalles_producto` FOREIGN KEY (`producto_id`) 
    REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------
-- TABLA: MOVIMIENTOS PUNTOS
-- ------------------------------------------------------
CREATE TABLE `movimientos_puntos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` BIGINT UNSIGNED NOT NULL,
  `venta_id` BIGINT UNSIGNED DEFAULT NULL,
  `tipo` ENUM('GANADO', 'USADO') NOT NULL,
  `puntos` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_puntos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `fk_puntos_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;