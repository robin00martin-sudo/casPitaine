-- Base de données du projet « casPitaine » (MySQL 5.7+/8, MariaDB 10.x)
CREATE DATABASE IF NOT EXISTS dbBat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dbBat;
SET NAMES utf8mb4;

-- Script rejouable : on repart d'un schéma vierge
DROP TABLE IF EXISTS POSSEDER;
DROP TABLE IF EXISTS EQUIPEMENT;
DROP TABLE IF EXISTS BATEAU;

-- Une seule table pour les deux types de bateaux (colonne « type » : 'v' voyageur, 'f' fret)
CREATE TABLE BATEAU (
  id       varchar(10)   NOT NULL,                 -- B1, B2... (généré par l'application)
  nom      varchar(100)  NOT NULL,
  longueur decimal(6,2)  NOT NULL,                 -- mètres
  largeur  decimal(6,2)  NOT NULL,                 -- mètres
  vitesse  decimal(6,2)  DEFAULT NULL,             -- noeuds (voyageurs)
  image    varchar(255)  DEFAULT NULL,             -- chemin relatif (voyageurs)
  poidsMax decimal(8,2)  DEFAULT NULL,             -- tonnes (fret)
  type     char(1)       NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_bateau_nom (nom),
  CONSTRAINT ck_bateau_type CHECK (type IN ('v', 'f'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE EQUIPEMENT (
  id  varchar(10)  NOT NULL,
  lib varchar(100) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_equipement_lib (lib)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE POSSEDER (
  idBat   varchar(10) NOT NULL,
  idEquip varchar(10) NOT NULL,
  PRIMARY KEY (idBat, idEquip),
  KEY idEquip (idEquip),
  CONSTRAINT POSSEDER_ibfk_1 FOREIGN KEY (idBat)   REFERENCES BATEAU (id)     ON DELETE CASCADE,
  CONSTRAINT POSSEDER_ibfk_2 FOREIGN KEY (idEquip) REFERENCES EQUIPEMENT (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NB : « Al xi » n'a pas encore de photo (image NULL) : la brochure affiche « Image indisponible ».
INSERT INTO BATEAU (id, nom, longueur, largeur, vitesse, image, poidsMax, type) VALUES
('B1', 'Luce isle',   37.20, 8.60, 26.00, '/images/luceisle.jpg',    NULL, 'v'),
('B2', 'Al xi',       25.00, 7.00, 16.00, NULL,                      NULL, 'v'),
('B3', 'Black Pearl', 40.00, 9.50, 30.00, '/images/blackpearl.jpg',  NULL, 'v');

INSERT INTO EQUIPEMENT (id, lib) VALUES
('E1', 'Accès Handicapé'),
('E2', 'Bar'),
('E3', 'Pont Promenade'),
('E4', 'Salon Vidéo');

INSERT INTO POSSEDER (idBat, idEquip) VALUES
('B1', 'E1'), ('B1', 'E2'), ('B1', 'E3'), ('B1', 'E4'),
('B2', 'E1'), ('B2', 'E3'),
('B3', 'E1'), ('B3', 'E2'), ('B3', 'E3');
