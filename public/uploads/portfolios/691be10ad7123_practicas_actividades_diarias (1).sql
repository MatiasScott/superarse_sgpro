/*
 Navicat Premium Data Transfer

 Source Server         : PRUEBAS DOCKER
 Source Server Type    : MySQL
 Source Server Version : 80041 (8.0.41)
 Source Host           : 192.168.18.77:3306
 Source Schema         : conectados_superarse

 Target Server Type    : MySQL
 Target Server Version : 80041 (8.0.41)
 File Encoding         : 65001

 Date: 11/11/2025 23:35:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for actividades_diarias
-- ----------------------------
DROP TABLE IF EXISTS `actividades_diarias`;
CREATE TABLE `actividades_diarias`  (
  `id_actividad_diaria` int NOT NULL AUTO_INCREMENT,
  `practica_id` int NOT NULL COMMENT 'FK a practicas_estudiantes',
  `actividad_realizada` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `horas_invertidas` decimal(4, 2) NOT NULL COMMENT 'Maximo 6.00 horas por dia',
  `fecha_actividad` date NOT NULL,
  `hora_inicio` time NULL DEFAULT NULL,
  `hora_fin` time NULL DEFAULT NULL,
  PRIMARY KEY (`id_actividad_diaria`) USING BTREE,
  INDEX `practica_id`(`practica_id` ASC) USING BTREE,
  CONSTRAINT `actividades_diarias_ibfk_1` FOREIGN KEY (`practica_id`) REFERENCES `practicas_estudiantes` (`id_practica`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `actividades_diarias_chk_1` CHECK (`horas_invertidas` <= 6.00)
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = 'Reporte diario de actividades y horas de la práctica.' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of actividades_diarias
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
