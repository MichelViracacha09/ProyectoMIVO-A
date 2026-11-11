CREATE DATABASE  IF NOT EXISTS `mivo_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `mivo_db`;
-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: localhost    Database: mivo_db
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `administrador`
--

DROP TABLE IF EXISTS `administrador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administrador` (
  `id_administrador` int NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(150) NOT NULL,
  `usuario_login` varchar(50) NOT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `permisos_asignados` varchar(255) DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_administrador`),
  UNIQUE KEY `usuario_login` (`usuario_login`),
  UNIQUE KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `fk_admin_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administrador`
--

LOCK TABLES `administrador` WRITE;
/*!40000 ALTER TABLE `administrador` DISABLE KEYS */;
INSERT INTO `administrador` VALUES (1,'Administrador Principal','admin','admin@mivo.com','total',NULL,1);
/*!40000 ALTER TABLE `administrador` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citas` (
  `id_citas` int NOT NULL AUTO_INCREMENT,
  `fecha_cita` date NOT NULL,
  `hora_cita` time NOT NULL,
  `servicio_a_realizar` varchar(255) DEFAULT NULL,
  `estado_cita` varchar(30) NOT NULL DEFAULT 'PENDIENTE',
  `fecha_creacion_cita` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_cliente` int NOT NULL,
  `id_servicio` int NOT NULL,
  PRIMARY KEY (`id_citas`),
  KEY `fk_citas_cliente` (`id_cliente`),
  KEY `fk_citas_servicio` (`id_servicio`),
  CONSTRAINT `fk_citas_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  CONSTRAINT `fk_citas_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas`
--

LOCK TABLES `citas` WRITE;
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
INSERT INTO `citas` VALUES (1,'2026-09-25','11:30:00','avisar si tiene que cambiar repuestos ','CONFIRMADA','2026-09-24 19:49:26',1,1);
/*!40000 ALTER TABLE `citas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(150) NOT NULL,
  `numero_identificacion` varchar(30) NOT NULL,
  `numero_celular` varchar(20) DEFAULT NULL,
  `correo_electronico` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `id_estado_cliente` int NOT NULL,
  `id_identificacion` int NOT NULL,
  `id_usuario` int DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `numero_identificacion` (`numero_identificacion`),
  UNIQUE KEY `id_usuario` (`id_usuario`),
  KEY `fk_cliente_estado` (`id_estado_cliente`),
  KEY `fk_cliente_tipoid` (`id_identificacion`),
  CONSTRAINT `fk_cliente_estado` FOREIGN KEY (`id_estado_cliente`) REFERENCES `estado_cliente` (`id_estado_cliente`),
  CONSTRAINT `fk_cliente_tipoid` FOREIGN KEY (`id_identificacion`) REFERENCES `tipo_identificacion` (`id_identificacion`),
  CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES (1,'Ana Sofia Hernandez ','1000225087','3133333333','anasofia@gmail.com','CRA 8 # 45 -56',NULL,1,1,2);
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_cliente`
--

DROP TABLE IF EXISTS `estado_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_cliente` (
  `id_estado_cliente` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`id_estado_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_cliente`
--

LOCK TABLES `estado_cliente` WRITE;
/*!40000 ALTER TABLE `estado_cliente` DISABLE KEYS */;
INSERT INTO `estado_cliente` VALUES (1,'Activo','Cliente con acceso habilitado','ACTIVO'),(2,'Inactivo','Cliente deshabilitado temporalmente','ACTIVO'),(3,'Bloqueado','Cliente bloqueado por alguna infraccion','ACTIVO');
/*!40000 ALTER TABLE `estado_cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facturas` (
  `id_facturas` int NOT NULL AUTO_INCREMENT,
  `n_orden` varchar(30) NOT NULL,
  `fecha` date NOT NULL,
  `total_a_pagar` decimal(12,2) NOT NULL,
  `servicio_asociado` varchar(150) DEFAULT NULL,
  `estado` varchar(30) NOT NULL DEFAULT 'PENDIENTE',
  `metodo_pago` varchar(50) DEFAULT NULL,
  `iva` decimal(5,2) DEFAULT NULL,
  `factura_electronica` varchar(255) DEFAULT NULL,
  `id_cliente` int NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_facturas`),
  UNIQUE KEY `n_orden` (`n_orden`),
  KEY `fk_facturas_cliente` (`id_cliente`),
  KEY `fk_facturas_usuario` (`id_usuario`),
  CONSTRAINT `fk_facturas_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  CONSTRAINT `fk_facturas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_de_servicios`
--

DROP TABLE IF EXISTS `historial_de_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_de_servicios` (
  `id_historialservicios` int NOT NULL AUTO_INCREMENT,
  `servicio` varchar(150) DEFAULT NULL,
  `cliente` varchar(150) DEFAULT NULL,
  `precio` decimal(12,2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` varchar(30) DEFAULT NULL,
  `placa_moto` varchar(15) DEFAULT NULL,
  `tecnico_responsable` varchar(150) DEFAULT NULL,
  `repuestos_utilizados` text,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `tipo_servicio_realizado` varchar(100) DEFAULT NULL,
  `id_citas` int NOT NULL,
  PRIMARY KEY (`id_historialservicios`),
  KEY `fk_historial_citas` (`id_citas`),
  CONSTRAINT `fk_historial_citas` FOREIGN KEY (`id_citas`) REFERENCES `citas` (`id_citas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_de_servicios`
--

LOCK TABLES `historial_de_servicios` WRITE;
/*!40000 ALTER TABLE `historial_de_servicios` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_de_servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulo`
--

DROP TABLE IF EXISTS `modulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulo` (
  `id_modulo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `ruta` varchar(100) NOT NULL,
  `icono` varchar(50) DEFAULT 'bi-box',
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulo`
--

LOCK TABLES `modulo` WRITE;
/*!40000 ALTER TABLE `modulo` DISABLE KEYS */;
INSERT INTO `modulo` VALUES (1,'Dashboard','home','bi-speedometer2','Vista principal del sistema',1),(2,'Clientes','clientes','bi-people','Gestión de clientes del taller',1),(3,'Motos','motos','bi-scooter','Gestión de motocicletas registradas',1),(4,'Servicios','servicios','bi-tools','Catálogo de servicios del taller',1),(5,'Citas','citas','bi-calendar-check','Agendamiento de citas',1),(6,'Facturas','facturas','bi-receipt','Gestión de facturación',1),(7,'Historial','historial','bi-clock-history','Historial de servicios realizados',1);
/*!40000 ALTER TABLE `modulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulo_rol`
--

DROP TABLE IF EXISTS `modulo_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulo_rol` (
  `id_modulo_rol` int NOT NULL AUTO_INCREMENT,
  `id_modulo` int NOT NULL,
  `id_rol` int NOT NULL,
  `puede_ver` tinyint(1) NOT NULL DEFAULT '1',
  `puede_crear` tinyint(1) NOT NULL DEFAULT '0',
  `puede_editar` tinyint(1) NOT NULL DEFAULT '0',
  `puede_eliminar` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_modulo_rol`),
  KEY `fk_mr_modulo` (`id_modulo`),
  KEY `fk_mr_rol` (`id_rol`),
  CONSTRAINT `fk_mr_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulo` (`id_modulo`) ON DELETE CASCADE,
  CONSTRAINT `fk_mr_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulo_rol`
--

LOCK TABLES `modulo_rol` WRITE;
/*!40000 ALTER TABLE `modulo_rol` DISABLE KEYS */;
INSERT INTO `modulo_rol` VALUES (1,1,1,1,1,1,1),(2,2,1,1,1,1,1),(3,3,1,1,1,1,1),(4,4,1,1,1,1,1),(5,5,1,1,1,1,1),(6,6,1,1,1,1,1),(7,7,1,1,1,1,1),(8,1,2,1,0,0,0),(9,3,2,1,1,1,0),(10,4,2,1,0,1,0),(11,5,2,1,1,1,0),(12,7,2,1,1,0,0),(13,1,3,1,0,0,0),(14,3,3,1,1,1,0),(15,5,3,1,1,0,0),(16,7,3,1,0,0,0);
/*!40000 ALTER TABLE `modulo_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `moto`
--

DROP TABLE IF EXISTS `moto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `moto` (
  `id_moto` int NOT NULL AUTO_INCREMENT,
  `placa` varchar(15) NOT NULL,
  `linea` varchar(100) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `cilindraje` varchar(20) DEFAULT NULL,
  `kilometraje` int DEFAULT NULL,
  `tipo_combustible` varchar(50) DEFAULT NULL,
  `proxima_fecha_mantenimiento` date DEFAULT NULL,
  `soat` varchar(50) DEFAULT NULL,
  `numero_tarjeta_propiedad` varchar(50) DEFAULT NULL,
  `observaciones` text,
  `id_cliente` int NOT NULL,
  `id_tipo_servicio` int DEFAULT NULL,
  PRIMARY KEY (`id_moto`),
  UNIQUE KEY `placa` (`placa`),
  KEY `fk_moto_cliente` (`id_cliente`),
  KEY `fk_moto_tiposervicio` (`id_tipo_servicio`),
  CONSTRAINT `fk_moto_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_moto_tiposervicio` FOREIGN KEY (`id_tipo_servicio`) REFERENCES `tipo_servicio` (`id_tipo_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `moto`
--

LOCK TABLES `moto` WRITE;
/*!40000 ALTER TABLE `moto` DISABLE KEYS */;
INSERT INTO `moto` VALUES (1,'EEO50F','AKT','2020','NEGRA','125',43663,NULL,NULL,NULL,NULL,NULL,1,NULL),(2,'WAS04G','SUZUKI','2022','NEGRO','160',20,NULL,NULL,NULL,NULL,NULL,1,NULL);
/*!40000 ALTER TABLE `moto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `moto_servicio`
--

DROP TABLE IF EXISTS `moto_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `moto_servicio` (
  `id_moto` int NOT NULL,
  `id_servicio` int NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_moto`,`id_servicio`,`fecha_asignacion`),
  KEY `fk_ms_servicio` (`id_servicio`),
  CONSTRAINT `fk_ms_moto` FOREIGN KEY (`id_moto`) REFERENCES `moto` (`id_moto`),
  CONSTRAINT `fk_ms_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `moto_servicio`
--

LOCK TABLES `moto_servicio` WRITE;
/*!40000 ALTER TABLE `moto_servicio` DISABLE KEYS */;
/*!40000 ALTER TABLE `moto_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES (1,'Administrador','Control total del sistema del taller'),(2,'Tecnico','Encargado de realizar los servicios y mantenimientos'),(3,'Cliente','Usuario que agenda citas y consulta el estado de su moto');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicio`
--

DROP TABLE IF EXISTS `servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servicio` (
  `id_servicio` int NOT NULL AUTO_INCREMENT,
  `nombre_servicio` varchar(150) NOT NULL,
  `descripcion_servicio` varchar(255) DEFAULT NULL,
  `precio_servicio` decimal(12,2) NOT NULL,
  `duracion_estimada` varchar(50) DEFAULT NULL,
  `garantia_servicio` varchar(100) DEFAULT NULL,
  `nivel_complejidad` varchar(50) DEFAULT NULL,
  `id_tipo_servicio` int NOT NULL,
  PRIMARY KEY (`id_servicio`),
  KEY `fk_servicio_tiposervicio` (`id_tipo_servicio`),
  CONSTRAINT `fk_servicio_tiposervicio` FOREIGN KEY (`id_tipo_servicio`) REFERENCES `tipo_servicio` (`id_tipo_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicio`
--

LOCK TABLES `servicio` WRITE;
/*!40000 ALTER TABLE `servicio` DISABLE KEYS */;
INSERT INTO `servicio` VALUES (1,'Cambio de aceite','Cambio de aceite de motor y filtro',45000.00,NULL,NULL,NULL,1),(2,'Revisión de frenos','Inspección y ajuste del sistema de frenos',60000.00,NULL,NULL,NULL,3),(3,'Cambio de aceite','Cambio de aceite de motor y filtro',45000.00,NULL,NULL,NULL,1),(4,'Revisión de frenos','Inspección y ajuste del sistema de frenos',60000.00,NULL,NULL,NULL,3),(5,'Cambio de pastillas de freno','cambiar pastillas de los frenos que lo requieran ',10000.00,'00:20','2 días','Bajo',2);
/*!40000 ALTER TABLE `servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taller`
--

DROP TABLE IF EXISTS `taller`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taller` (
  `id_taller` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `horario_atencion` varchar(100) DEFAULT NULL,
  `nit` varchar(30) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `correo_electronico` varchar(150) DEFAULT NULL,
  `id_administrador` int NOT NULL,
  PRIMARY KEY (`id_taller`),
  UNIQUE KEY `nit` (`nit`),
  KEY `fk_taller_admin` (`id_administrador`),
  CONSTRAINT `fk_taller_admin` FOREIGN KEY (`id_administrador`) REFERENCES `administrador` (`id_administrador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taller`
--

LOCK TABLES `taller` WRITE;
/*!40000 ALTER TABLE `taller` DISABLE KEYS */;
/*!40000 ALTER TABLE `taller` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_identificacion`
--

DROP TABLE IF EXISTS `tipo_identificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_identificacion` (
  `id_identificacion` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `abreviatura` varchar(10) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_identificacion`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_identificacion`
--

LOCK TABLES `tipo_identificacion` WRITE;
/*!40000 ALTER TABLE `tipo_identificacion` DISABLE KEYS */;
INSERT INTO `tipo_identificacion` VALUES (1,'Cedula de Ciudadania','CC','Documento de identidad para ciudadanos colombianos'),(2,'Tarjeta de Identidad','TI','Documento para menores de edad'),(3,'Cedula de Extranjeria','CE','Documento para extranjeros residentes'),(4,'Pasaporte','PAS','Documento de identidad internacional');
/*!40000 ALTER TABLE `tipo_identificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_servicio`
--

DROP TABLE IF EXISTS `tipo_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_servicio` (
  `id_tipo_servicio` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_servicio`
--

LOCK TABLES `tipo_servicio` WRITE;
/*!40000 ALTER TABLE `tipo_servicio` DISABLE KEYS */;
INSERT INTO `tipo_servicio` VALUES (1,'Mantenimiento preventivo','Revision periodica para prevenir fallas'),(2,'Mantenimiento correctivo','Reparacion de fallas ya presentadas'),(3,'Diagnostico general','Revision completa del estado de la moto');
/*!40000 ALTER TABLE `tipo_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `estado` varchar(20) NOT NULL DEFAULT 'ACTIVO',
  `id_rol` int NOT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `fk_usuario_rol` (`id_rol`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'$2y$10$v80GBvWgHYQPk9qf6diuMuGlvlFcl5A3ct7fJmG5koSKWjrJphBz2','2026-09-23 11:06:19',NULL,'ACTIVO',1),(2,'$2y$10$CHGkamuNbhPrRa63g5oeyehb84iuyjdY6Ix79lvWhXU9gsBYylh42','2026-09-23 11:29:13','2026-09-24 18:11:14','ACTIVO',3);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-10 19:59:41
