/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.4.32-MariaDB : Database - db_proyecto_final
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_proyecto_final` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `db_proyecto_final`;

/*Table structure for table `cliente` */

DROP TABLE IF EXISTS `cliente`;

CREATE TABLE `cliente` (
  `Cedula_Rif` varchar(15) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) DEFAULT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  `Direccion` text DEFAULT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Fecha_Registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `Estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`Cedula_Rif`),
  KEY `idx_nombre` (`Nombre`),
  KEY `idx_telefono` (`Telefono`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cliente` */

LOCK TABLES `cliente` WRITE;

insert  into `cliente`(`Cedula_Rif`,`Nombre`,`Apellido`,`Telefono`,`Direccion`,`Correo`,`Fecha_Registro`,`Estado`) values ('12345678','Carlos','Rodríguez','04121234567','Av. Principal #123, Caracas','carlos@email.com','2025-07-04 19:56:40','Activo'),('23456789','Ana','Martínez','04167891234','Calle 5 con Av. 2, Maracay','ana.martinez@gmail.com','2025-07-04 19:56:40','Activo'),('34567890','Luis','Fernández','04145678901','Urbanización El Rosal, Casa 12','luis.fernandez@hotmail.com','2025-07-04 19:56:40','Activo'),('J123456789','Empresa ABC','C.A.','04129876543','Centro Comercial Plaza, Local 45','ventas@empresaabc.com','2025-07-04 19:56:40','Activo'),('J987654321','Distribuidora XYZ','S.R.L.','04123456789','Zona Industrial, Galpón 8','compras@distxyz.com','2025-07-04 19:56:40','Activo'),('VENTA_DIRECTA','Venta','Directa','','Sistema','','2025-07-04 19:56:40','Activo');

UNLOCK TABLES;

/*Table structure for table `detallepago` */

DROP TABLE IF EXISTS `detallepago`;

CREATE TABLE `detallepago` (
  `id_detalle_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_pago_venta` int(11) NOT NULL,
  `Monto` decimal(10,2) NOT NULL,
  `Metodo_Pago` enum('Efectivo','Transferencia','Tarjeta','Divisas','Cheque') NOT NULL,
  `Fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detalle_pago`),
  KEY `idx_pago` (`id_pago_venta`),
  KEY `idx_metodo` (`Metodo_Pago`),
  CONSTRAINT `detallepago_ibfk_1` FOREIGN KEY (`id_pago_venta`) REFERENCES `pago` (`id_pago_venta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `detallepago` */

LOCK TABLES `detallepago` WRITE;

insert  into `detallepago`(`id_detalle_pago`,`id_pago_venta`,`Monto`,`Metodo_Pago`,`Fecha`) values (1,1,4.00,'Efectivo','2025-07-07 09:05:24');

UNLOCK TABLES;

/*Table structure for table `detalleventa` */

DROP TABLE IF EXISTS `detalleventa`;

CREATE TABLE `detalleventa` (
  `id_detalle_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio_Unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle_venta`),
  KEY `idx_venta` (`id_venta`),
  KEY `idx_producto` (`id_producto`),
  CONSTRAINT `detalleventa_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalleventa_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `detalleventa` */

LOCK TABLES `detalleventa` WRITE;

insert  into `detalleventa`(`id_detalle_venta`,`id_venta`,`id_producto`,`Cantidad`,`Precio_Unitario`) values (1,1,3,1,4.00);

UNLOCK TABLES;

/*Table structure for table `deuda` */

DROP TABLE IF EXISTS `deuda`;

CREATE TABLE `deuda` (
  `id_deuda` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `Monto_Total` decimal(10,2) NOT NULL,
  `Monto_Pagado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Monto_Deuda` decimal(10,2) NOT NULL,
  `Estado` enum('Pendiente','Pagado') DEFAULT 'Pendiente',
  `Fecha_Creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `Fecha_Ultimo_Pago` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_deuda`),
  KEY `idx_venta` (`id_venta`),
  KEY `idx_estado` (`Estado`),
  KEY `idx_monto_deuda` (`Monto_Deuda`),
  KEY `idx_deuda_estado_monto` (`Estado`,`Monto_Deuda`),
  CONSTRAINT `deuda_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `deuda` */

LOCK TABLES `deuda` WRITE;

UNLOCK TABLES;

/*Table structure for table `pago` */

DROP TABLE IF EXISTS `pago`;

CREATE TABLE `pago` (
  `id_pago_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `Monto` decimal(10,2) NOT NULL,
  `Fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pago_venta`),
  KEY `idx_venta` (`id_venta`),
  KEY `idx_fecha` (`Fecha`),
  CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pago` */

LOCK TABLES `pago` WRITE;

insert  into `pago`(`id_pago_venta`,`id_venta`,`Monto`,`Fecha`) values (1,1,4.00,'2025-07-07 09:05:24');

UNLOCK TABLES;

/*Table structure for table `pagodeuda` */

DROP TABLE IF EXISTS `pagodeuda`;

CREATE TABLE `pagodeuda` (
  `id_pago_deuda` int(11) NOT NULL AUTO_INCREMENT,
  `id_deuda` int(11) NOT NULL,
  `Monto` decimal(10,2) NOT NULL,
  `Metodo_Pago` enum('Efectivo','Transferencia','Tarjeta','Divisas','Cheque') NOT NULL,
  `Fecha_Pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `Observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_pago_deuda`),
  KEY `idx_deuda` (`id_deuda`),
  KEY `idx_fecha` (`Fecha_Pago`),
  CONSTRAINT `pagodeuda_ibfk_1` FOREIGN KEY (`id_deuda`) REFERENCES `deuda` (`id_deuda`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pagodeuda` */

LOCK TABLES `pagodeuda` WRITE;

UNLOCK TABLES;

/*Table structure for table `producto` */

DROP TABLE IF EXISTS `producto`;

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Precio` decimal(10,2) NOT NULL,
  `Unidad` varchar(20) NOT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `Fecha_Registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `Estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `Umbral_Bajo` int(11) NOT NULL DEFAULT 15,
  `Umbral_Critico` int(11) NOT NULL DEFAULT 5,
  PRIMARY KEY (`id_producto`),
  KEY `idx_nombre` (`Nombre`),
  KEY `idx_precio` (`Precio`),
  KEY `idx_proveedor` (`id_proveedor`),
  KEY `idx_producto_proveedor_stock` (`id_proveedor`,`Estado`),
  CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `producto` */

LOCK TABLES `producto` WRITE;

insert  into `producto`(`id_producto`,`Nombre`,`Precio`,`Unidad`,`id_proveedor`,`Fecha_Registro`,`Estado`) values (1,'Botellón de Agua 20 Litros',15.00,'Botellón',1,'2025-07-04 19:56:40','Activo'),(2,'Agua Purificada 500ml',2.50,'Unidad',1,'2025-07-04 19:56:40','Activo'),(3,'Agua Purificada 1 Litro',4.00,'Unidad',1,'2025-07-04 19:56:40','Activo'),(4,'Agua Purificada 5 Litros',8.50,'Unidad',1,'2025-07-04 19:56:40','Activo'),(5,'Botellón Vacío 20L',45.00,'Unidad',2,'2025-07-04 19:56:40','Activo'),(6,'Botellón Vacío 10L',35.00,'Unidad',2,'2025-07-04 19:56:40','Activo'),(7,'Tapa para Botellón',3.50,'Unidad',6,'2025-07-04 19:56:40','Activo'),(8,'Etiqueta para Botellón',1.20,'Unidad',6,'2025-07-04 19:56:40','Activo'),(9,'Dispensador de Agua Fría/Caliente',450.00,'Unidad',3,'2025-07-04 19:56:40','Activo'),(10,'Dispensador de Mesa',180.00,'Unidad',3,'2025-07-04 19:56:40','Activo'),(11,'Soporte para Botellón',25.00,'Unidad',3,'2025-07-04 19:56:40','Activo'),(12,'Filtro de Sedimentos 10\"',12.00,'Unidad',4,'2025-07-04 19:56:40','Activo'),(13,'Filtro de Carbón Activado',18.00,'Unidad',4,'2025-07-04 19:56:40','Activo'),(14,'Membrana de Ósmosis Inversa',85.00,'Unidad',4,'2025-07-04 19:56:40','Activo'),(15,'Kit de Filtros Completo',120.00,'Unidad',4,'2025-07-04 19:56:40','Activo'),(16,'Cloro para Purificación 1L',8.50,'Litro',NULL,'2025-07-04 19:56:40','Activo'),(17,'Desinfectante para Equipos',15.00,'Litro',NULL,'2025-07-04 19:56:40','Activo'),(18,'Sal para Suavizador 25Kg',35.00,'Kilogramo',4,'2025-07-04 19:56:40','Activo'),(19,'Vaso Desechable 200ml',0.50,'Unidad',NULL,'2025-07-04 19:56:40','Activo'),(20,'Servilletas de Papel',2.00,'Paquete',NULL,'2025-07-04 19:56:40','Activo');

UNLOCK TABLES;

/*Table structure for table `proveedor` */

DROP TABLE IF EXISTS `proveedor`;

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL AUTO_INCREMENT,
  `RIF` varchar(15) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Contacto` varchar(100) DEFAULT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  `Direccion` text DEFAULT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Tipo_Producto` enum('Agua Purificada','Botellones','Dispensadores','Filtros','Químicos','Envases','Equipos','Otros') NOT NULL DEFAULT 'Agua Purificada',
  `Fecha_Registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `Estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`id_proveedor`),
  UNIQUE KEY `RIF` (`RIF`),
  KEY `idx_rif` (`RIF`),
  KEY `idx_nombre` (`Nombre`),
  KEY `idx_tipo` (`Tipo_Producto`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `proveedor` */

LOCK TABLES `proveedor` WRITE;

insert  into `proveedor`(`id_proveedor`,`RIF`,`Nombre`,`Contacto`,`Telefono`,`Direccion`,`Correo`,`Tipo_Producto`,`Fecha_Registro`,`Estado`) values (1,'J301234567','Agua Pura del Valle C.A.','Roberto Sánchez','04121111111','Zona Industrial Los Valles, Galpón 15','ventas@aguapura.com','Agua Purificada','2025-07-04 19:56:40','Activo'),(2,'J302345678','Botellones Premium S.A.','Carmen López','04122222222','Av. Industrial #456, Valencia','contacto@botellon.com','Botellones','2025-07-04 19:56:40','Activo'),(3,'J303456789','Equipos de Agua Total','Miguel Torres','04123333333','Centro Comercial Agua, Local 12','info@equipostotal.com','Dispensadores','2025-07-04 19:56:40','Activo'),(4,'J304567890','Filtros y Repuestos Aqua','Elena Morales','04124444444','Calle del Agua #789','ventas@filtrosaqua.com','Filtros','2025-07-04 19:56:40','Activo'),(6,'J306789012','Envases y Tapas del Centro','Lucía Herrera','04126666666','Av. Principal del Centro #321','lucia@envasescentro.com','Envases','2025-07-04 19:56:40','Activo');

UNLOCK TABLES;

/*Table structure for table `rol` */

DROP TABLE IF EXISTS `rol`;

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Fecha_Creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `rol` */

LOCK TABLES `rol` WRITE;

insert  into `rol`(`id_rol`,`Nombre`,`Descripcion`,`Fecha_Creacion`) values (1,'Administrador','Acceso completo al sistema','2025-07-04 19:56:40'),(2,'Vendedor','Acceso a ventas y consultas','2025-07-04 19:56:40');

UNLOCK TABLES;

/*Table structure for table `stock` */

DROP TABLE IF EXISTS `stock`;

CREATE TABLE `stock` (
  `id_stock` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL DEFAULT 0,
  `Fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_stock`),
  UNIQUE KEY `unique_producto` (`id_producto`),
  KEY `idx_cantidad` (`Cantidad`),
  CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `stock` */

LOCK TABLES `stock` WRITE;

insert  into `stock`(`id_stock`,`id_producto`,`Cantidad`,`Fecha_actualizacion`) values (1,1,150,'2025-07-04 19:56:40'),(2,2,200,'2025-07-04 19:56:40'),(3,3,179,'2025-07-07 09:05:24'),(4,4,120,'2025-07-04 19:56:40'),(5,5,25,'2025-07-04 19:56:40'),(6,6,30,'2025-07-04 19:56:40'),(7,7,100,'2025-07-04 19:56:40'),(8,8,150,'2025-07-04 19:56:40'),(9,9,8,'2025-07-04 19:56:40'),(10,10,12,'2025-07-04 19:56:40'),(11,11,20,'2025-07-04 19:56:40'),(12,12,45,'2025-07-04 19:56:40'),(13,13,35,'2025-07-04 19:56:40'),(14,14,15,'2025-07-04 19:56:40'),(15,15,10,'2025-07-04 19:56:40'),(16,16,25,'2025-07-04 19:56:40'),(17,17,18,'2025-07-04 19:56:40'),(18,18,5,'2025-07-14 14:16:07'),(19,19,500,'2025-07-04 19:56:40'),(20,20,80,'2025-07-04 19:56:40');

UNLOCK TABLES;

/*Table structure for table `tasacambio` */

DROP TABLE IF EXISTS `tasacambio`;

CREATE TABLE `tasacambio` (
  `id_tasa_cambio` int(11) NOT NULL AUTO_INCREMENT,
  `id_detalle_pago` int(11) NOT NULL,
  `Precio` decimal(10,2) NOT NULL,
  `Hora` time NOT NULL,
  `Monto_Divisas` decimal(10,2) DEFAULT NULL COMMENT 'Monto en divisas (USD) pagado por el cliente',
  `Fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_tasa_cambio`),
  KEY `idx_detalle_pago` (`id_detalle_pago`),
  KEY `idx_fecha` (`Fecha`),
  CONSTRAINT `tasacambio_ibfk_1` FOREIGN KEY (`id_detalle_pago`) REFERENCES `detallepago` (`id_detalle_pago`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tasacambio` */

LOCK TABLES `tasacambio` WRITE;

UNLOCK TABLES;

/*Table structure for table `usuario` */

DROP TABLE IF EXISTS `usuario`;

CREATE TABLE `usuario` (
  `id_usuario` varchar(20) NOT NULL,
  `Cedula` varchar(10) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `Contraseña` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `Fecha_Registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `Fecha_Ultimo_Registro` timestamp NULL DEFAULT NULL,
  `Estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `correo` varchar(150) NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `Cedula` (`Cedula`),
  KEY `id_rol` (`id_rol`),
  KEY `idx_cedula` (`Cedula`),
  KEY `idx_estado` (`Estado`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `usuario` */

LOCK TABLES `usuario` WRITE;

insert  into `usuario`(`id_usuario`,`Cedula`,`Nombre`,`Apellido`,`Contraseña`,`id_rol`,`Fecha_Registro`,`Fecha_Ultimo_Registro`,`Estado`,`correo`) values ('pablop','16052929','Pablo','Parada','16052928',1,'2025-07-04 19:56:40','2025-07-14 14:17:35','Activo','pablopparada@gmail.com'),('pedror','30912454','Pedro','Ron','30912454',2,'2025-07-04 19:56:40','2025-07-14 14:20:22','Activo','pedroroyal@gmail.com');

UNLOCK TABLES;

/*Table structure for table `venta` */

DROP TABLE IF EXISTS `venta`;

CREATE TABLE `venta` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `Cedula_Rif` varchar(15) NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  `Fecha_Emision` timestamp NOT NULL DEFAULT current_timestamp(),
  `Estado` enum('Completada','Cancelada','Pendiente') DEFAULT 'Completada',
  `No_Control` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_venta`),
  KEY `idx_fecha` (`Fecha_Emision`),
  KEY `idx_cliente` (`Cedula_Rif`),
  KEY `idx_total` (`Total`),
  KEY `idx_venta_fecha_cliente` (`Fecha_Emision`,`Cedula_Rif`),
  CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`Cedula_Rif`) REFERENCES `cliente` (`Cedula_Rif`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `venta` */

LOCK TABLES `venta` WRITE;

insert  into `venta`(`id_venta`,`Cedula_Rif`,`Total`,`Fecha_Emision`,`Estado`,`No_Control`) values (1,'VENTA_DIRECTA',4.00,'2025-07-07 09:05:24','Completada','');

UNLOCK TABLES;

/* Trigger structure for table `stock` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_stock_update_timestamp` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `tr_stock_update_timestamp` BEFORE UPDATE ON `stock` FOR EACH ROW BEGIN
    SET NEW.Fecha_actualizacion = NOW();
END */$$


DELIMITER ;

/* Trigger structure for table `stock` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `tr_stock_no_negativo` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `tr_stock_no_negativo` BEFORE UPDATE ON `stock` FOR EACH ROW BEGIN
    IF NEW.Cantidad < 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El stock no puede ser negativo';
    END IF;
END */$$


DELIMITER ;

/* Procedure structure for procedure `ActualizarStockVenta` */

/*!50003 DROP PROCEDURE IF EXISTS  `ActualizarStockVenta` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `ActualizarStockVenta`(
    IN p_id_producto INT,
    IN p_cantidad_vendida INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    UPDATE Stock 
    SET Cantidad = Cantidad - p_cantidad_vendida,
        Fecha_actualizacion = NOW()
    WHERE id_producto = p_id_producto;
    
    COMMIT;
END */$$
DELIMITER ;

/* Procedure structure for procedure `ObtenerProductosStockBajo` */

/*!50003 DROP PROCEDURE IF EXISTS  `ObtenerProductosStockBajo` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerProductosStockBajo`(
    IN p_umbral INT
)
BEGIN
    -- Se establece el valor por defecto del umbral si no se proporciona uno.
    IF p_umbral IS NULL THEN
        SET p_umbral = 10;
    END IF;
    SELECT 
        p.id_producto,
        p.Nombre,
        p.Precio,
        p.Unidad,
        s.Cantidad AS stock_actual,
        pr.Nombre AS proveedor,
        pr.Telefono AS telefono_proveedor
    FROM Producto p
    LEFT JOIN Stock s ON p.id_producto = s.id_producto
    LEFT JOIN Proveedor pr ON p.id_proveedor = pr.id_proveedor
    WHERE COALESCE(s.Cantidad, 0) <= p_umbral
    ORDER BY s.Cantidad ASC, p.Nombre ASC;
END */$$
DELIMITER ;

/*Table structure for table `vista_productos_completa` */

DROP TABLE IF EXISTS `vista_productos_completa`;

/*!50001 DROP VIEW IF EXISTS `vista_productos_completa` */;
/*!50001 DROP TABLE IF EXISTS `vista_productos_completa` */;

/*!50001 CREATE TABLE  `vista_productos_completa`(
 `id_producto` int(11) ,
 `producto_nombre` varchar(100) ,
 `Precio` decimal(10,2) ,
 `Unidad` varchar(20) ,
 `stock_actual` int(11) ,
 `proveedor_nombre` varchar(100) ,
 `proveedor_rif` varchar(15) ,
 `tipo_proveedor` enum('Agua Purificada','Botellones','Dispensadores','Filtros','Químicos','Envases','Equipos','Otros') ,
 `proveedor_telefono` varchar(15) ,
 `estado_stock` varchar(7) 
)*/;

/*Table structure for table `vista_productos_mas_vendidos` */

DROP TABLE IF EXISTS `vista_productos_mas_vendidos`;

/*!50001 DROP VIEW IF EXISTS `vista_productos_mas_vendidos` */;
/*!50001 DROP TABLE IF EXISTS `vista_productos_mas_vendidos` */;

/*!50001 CREATE TABLE  `vista_productos_mas_vendidos`(
 `id_producto` int(11) ,
 `producto_nombre` varchar(100) ,
 `total_vendido` decimal(32,0) ,
 `ingresos_generados` decimal(42,2) ,
 `numero_ventas` bigint(21) ,
 `precio_promedio` decimal(14,6) 
)*/;

/*Table structure for table `vista_ventas_completa` */

DROP TABLE IF EXISTS `vista_ventas_completa`;

/*!50001 DROP VIEW IF EXISTS `vista_ventas_completa` */;
/*!50001 DROP TABLE IF EXISTS `vista_ventas_completa` */;

/*!50001 CREATE TABLE  `vista_ventas_completa`(
 `id_venta` int(11) ,
 `Total` decimal(10,2) ,
 `Fecha_Emision` timestamp ,
 `estado_venta` enum('Completada','Cancelada','Pendiente') ,
 `Cedula_Rif` varchar(15) ,
 `cliente_nombre` varchar(101) ,
 `cliente_telefono` varchar(15) ,
 `estado_pago` varchar(9) 
)*/;

/*View structure for view vista_productos_completa */

/*!50001 DROP TABLE IF EXISTS `vista_productos_completa` */;
/*!50001 DROP VIEW IF EXISTS `vista_productos_completa` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_completa` AS select `p`.`id_producto` AS `id_producto`,`p`.`Nombre` AS `producto_nombre`,`p`.`Precio` AS `Precio`,`p`.`Unidad` AS `Unidad`,coalesce(`s`.`Cantidad`,0) AS `stock_actual`,`pr`.`Nombre` AS `proveedor_nombre`,`pr`.`RIF` AS `proveedor_rif`,`pr`.`Tipo_Producto` AS `tipo_proveedor`,`pr`.`Telefono` AS `proveedor_telefono`,case when coalesce(`s`.`Cantidad`,0) <= 5 then 'Crítico' when coalesce(`s`.`Cantidad`,0) <= 15 then 'Bajo' else 'Normal' end AS `estado_stock` from ((`producto` `p` left join `stock` `s` on(`p`.`id_producto` = `s`.`id_producto`)) left join `proveedor` `pr` on(`p`.`id_proveedor` = `pr`.`id_proveedor`)) where `p`.`Estado` = 'Activo' */;

/*View structure for view vista_productos_mas_vendidos */

/*!50001 DROP TABLE IF EXISTS `vista_productos_mas_vendidos` */;
/*!50001 DROP VIEW IF EXISTS `vista_productos_mas_vendidos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_mas_vendidos` AS select `p`.`id_producto` AS `id_producto`,`p`.`Nombre` AS `producto_nombre`,sum(`dv`.`Cantidad`) AS `total_vendido`,sum(`dv`.`Cantidad` * `dv`.`Precio_Unitario`) AS `ingresos_generados`,count(distinct `dv`.`id_venta`) AS `numero_ventas`,avg(`dv`.`Precio_Unitario`) AS `precio_promedio` from ((`producto` `p` join `detalleventa` `dv` on(`p`.`id_producto` = `dv`.`id_producto`)) join `venta` `v` on(`dv`.`id_venta` = `v`.`id_venta`)) where `v`.`Estado` = 'Completada' group by `p`.`id_producto`,`p`.`Nombre` order by sum(`dv`.`Cantidad`) desc */;

/*View structure for view vista_ventas_completa */

/*!50001 DROP TABLE IF EXISTS `vista_ventas_completa` */;
/*!50001 DROP VIEW IF EXISTS `vista_ventas_completa` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_ventas_completa` AS select `v`.`id_venta` AS `id_venta`,`v`.`Total` AS `Total`,`v`.`Fecha_Emision` AS `Fecha_Emision`,`v`.`Estado` AS `estado_venta`,`c`.`Cedula_Rif` AS `Cedula_Rif`,concat(`c`.`Nombre`,' ',coalesce(`c`.`Apellido`,'')) AS `cliente_nombre`,`c`.`Telefono` AS `cliente_telefono`,case when `d`.`Estado` = 'Pendiente' then 'No Pagado' when `d`.`Estado` = 'Pagado' then 'Pagado' when `d`.`id_deuda` is null then 'Pagado' else 'Pagado' end AS `estado_pago` from ((`venta` `v` join `cliente` `c` on(`v`.`Cedula_Rif` = `c`.`Cedula_Rif`)) left join `deuda` `d` on(`v`.`id_venta` = `d`.`id_venta`)) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
