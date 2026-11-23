

CREATE TABLE `BATEAU` (
  id varchar(10) NOT NULL,
  nom varchar(100) NOT NULL,
  longueur decimal(6,2) NOT NULL,
  largeur decimal(6,2) NOT NULL,
  vitesse decimal(6,2) DEFAULT NULL,
  image varchar(255) DEFAULT NULL,
  poidsMax decimal(8,2) DEFAULT NULL,
  type char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `EQUIPEMENT` (
  id varchar(10) NOT NULL,
  lib varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE POSSEDER (
  idBat varchar(10) NOT NULL,
  idEquip varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



ALTER TABLE `BATEAU`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `EQUIPEMENT`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `POSSEDER`
  ADD PRIMARY KEY (`idBat`,`idEquip`),
  ADD KEY `idEquip` (`idEquip`);


ALTER TABLE `POSSEDER`
  ADD CONSTRAINT `POSSEDER_ibfk_1` FOREIGN KEY (`idBat`) REFERENCES `BATEAU` (`id`),
  ADD CONSTRAINT `POSSEDER_ibfk_2` FOREIGN KEY (`idEquip`) REFERENCES `EQUIPEMENT` (`id`);



INSERT INTO BATEAU (id, nom, longueur, largeur, vitesse, image, poidsMax, type) VALUES
('B1', 'Luce isle', 37.20, 8.60, 26.00, '/images/luceisle.jpg', NULL, 'v'),
('B2', 'Al xi', 25.00, 7.00, 16.00, '/images/alxi.jpg', NULL, 'v'),
('B3', 'Black Pearl', 40.00, 9.50, 30.00, '/images/blackpearl.jpg', NULL, 'v');


INSERT INTO EQUIPEMENT (id, lib) VALUES
('E1', 'Accès Handicapé'),
('E2', 'Bar'),
('E3', 'Pont Promenade'),
('E4', 'Salon Vidéo');



INSERT INTO `POSSEDER` (`idBat`, `idEquip`) VALUES
('B1', 'E1'),
('B1', 'E2'),
('B1', 'E3'),
('B1', 'E4'),
('B2', 'E1'),
('B2', 'E3'),
('B3', 'E1'),
('B3', 'E2'),
('B3', 'E3');

COMMIT;

