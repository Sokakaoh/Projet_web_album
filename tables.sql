-- noinspection SqlDialectInspectionForFile
-- noinspection SqlNoDataSourceInspectionForFile
DROP TABLE  IF EXISTS paniers,commandes, album, users, typeAlbum, etats;

-- --------------------------------------------------------
-- Structure de la table typeAlbum
--
CREATE TABLE IF NOT EXISTS typeAlbum (
  id int(10) NOT NULL,
  libelle varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
)  DEFAULT CHARSET=utf8;
-- Contenu de la table typeproduits
INSERT INTO typeAlbum (id, libelle) VALUES
(1, 'Rap'),
(2, 'Dubstep'),
(3, 'Pop'),
(4, 'AfroTrap'),
(5, 'Classique'),
(6, 'R&B'),
(7, 'Reggae'),
(8, 'Hip Hop'),
(9, 'Spoken Word');

-- --------------------------------------------------------
-- Structure de la table etats

CREATE TABLE IF NOT EXISTS etats (
  id int(11) NOT NULL AUTO_INCREMENT,
  libelle varchar(20) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8 ;
-- Contenu de la table etats
INSERT INTO etats (id, libelle) VALUES
(1, 'A préparer'),
(2, 'Expédié');

-- --------------------------------------------------------
-- Structure de la table album

CREATE TABLE IF NOT EXISTS album (
  id int(10) NOT NULL AUTO_INCREMENT,
  typeAlbum_id int(10) DEFAULT NULL,
  nom varchar(50) DEFAULT NULL,
  artiste varchar(50) DEFAULT NULL,
  prix float(6,2) DEFAULT NULL,
  photo varchar(50) DEFAULT NULL,
  dispo tinyint(4) NOT NULL,
  stock int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_album_typeAlbum FOREIGN KEY (typeAlbum_id) REFERENCES typeAlbum (id)
) DEFAULT CHARSET=utf8 ;

INSERT INTO album (typeAlbum_id, nom,artiste, prix,photo, dispo,stock) VALUES
(1, 'Feu', 'Nekfeu', '35', 'nekfeu.jpeg', 1, 20),
(1, 'Nero Nemesis', 'B2O', '10', 'booba.jpeg', 1, 20),
(2, 'Kiko', 'Panda Eyes', '15', 'panda_eyes.jpeg', 1, 20),
(4, 'MHD', 'MHD', '10', 'mhd.jpeg', 1, 20),
(1, 'A7', 'SCH', '20', 'sch.jpeg', 1, 20),
(2, 'New Gore Order', 'Borgore', '23', 'borgore.jpeg', 1, 0),
(3, 'Fearless', 'Taylor Swift', '8', 'taylor_swift.jpeg', 1, 20),
(5, 'The Mozart Collection', 'Mozart', '35', 'mozart.jpeg', 1, 20),
(6, 'Confessions', 'Usher', '18', 'usher.jpeg', 1, 20),
(6, 'Tatoos', 'Jason Derulo', '16', 'JasonDerulo.jpeg', 1, 20),
(7, 'Best Of Marley', 'Bob Marley', '30', 'marley.jpeg', 1, 20),
(7, 'Street Tape Vol.2', 'Taïro', '21', 'tairo.jpeg', 1, 0),
(2, 'Bangarang', 'Skrillex', '15', 'skrillex.jpg', 1, 10),
(1, "L'Empereur du Sale", 'Lorenzo', '20', 'lorenzo.jpg', 1, 30),
(3, 'Lemonade', 'Beyonce', '10', 'beyonce.jpg', 1, 5),
(8, 'Views', 'Drake', '40', 'drake.jpg', 1, 15),
(9, '150 900', 'Fauve', '32', 'fauve.jpg', 1, 50);


-- --------------------------------------------------------
-- Structure de la table user
-- valide permet de rendre actif le compte (exemple controle par email )

CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  login varchar(255) NOT NULL,
  nom varchar(255) NOT NULL,
  prenom varchar(255) NOT NULL,
  code_postal varchar(255) NOT NULL,
  ville varchar(255) NOT NULL,
  adresse varchar(255) NOT NULL,
  valide tinyint NOT NULL,
  droit varchar(255) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;

-- Contenu de la table users
INSERT INTO users (id, prenom, nom, code_postal, ville, adresse, login,password,email,valide,droit) VALUES
(1, 'Quentin', 'Evanzyker', '77163', 'Mortcerf', '6 rue du 27 août', 'admin', 'admin', 'admin@gmail.com',1,'DROITadmin'),
(2, 'Loan', 'S0kah', '90000', 'Belfort', 'X rue de Madagascar', 'vendeur', 'vendeur', 'vendeur@gmail.com',1,'DROITadmin'),
(3, 'Tristan', 'Sniperz', '75011', 'Paris', '15 place de la République', 'client', 'client', 'client@gmail.com',1,'DROITclient'),
(4, 'Thomas', 'BTK', '75006', 'Paris', '36 bis rue de la Fonse', 'client2', 'client2', 'client2@gmail.com',1,'DROITclient'),
(5, 'Ahmed', 'Medah', '75002', 'Paris', '2 Avenue des Champs Elysées', 'client3', 'client3', 'client3@gmail.com','1','DROITclient');



-- --------------------------------------------------------
-- Structure de la table commandes
CREATE TABLE IF NOT EXISTS commandes (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  prix float(6,2) NOT NULL,
  date TIMESTAMP NOT NULL Default CURRENT_TIMESTAMP ,
  etat_id int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_commandes_users FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_commandes_etats FOREIGN KEY (etat_id) REFERENCES etats (id)
) DEFAULT CHARSET=utf8 ;




-- --------------------------------------------------------
-- Structure de la table paniers
CREATE TABLE IF NOT EXISTS paniers (
  id int(11) NOT NULL AUTO_INCREMENT,
  quantite int(11) NOT NULL,
  prix float(6,2) NOT NULL,
  user_id int(11) NOT NULL,
  album_id int(11) NOT NULL,
  commande_id int(11) DEFAULT NULL,
  PRIMARY KEY (id, album_id)/*,
 CONSTRAINT fk_paniers_users FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_paniers_produits FOREIGN KEY (produit_id) REFERENCES produits (id),
  CONSTRAINT fk_paniers_commandes FOREIGN KEY (commande_id) REFERENCES commandes */
) DEFAULT CHARSET=utf8;
INSERT INTO paniers (id,quantite,prix,user_id,album_id,commande_id ) VALUES
(1, 2, '5.5',3,2,1),
(2, 1, '100',4,1,1),
(3, 5, '10',5,4,2);
