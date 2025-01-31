CREATE TABLE sexes(
   id_sexe SERIAL,
   sexe VARCHAR(50)  NOT NULL,
   PRIMARY KEY(Id_sexe)
);

CREATE TABLE configurations(
   id_Configurations SERIAL,
   cle VARCHAR(50)  NOT NULL,
   valeur VARCHAR(50)  NOT NULL,
   PRIMARY KEY(Id_Configurations)
);

CREATE TABLE utilisateurs(
   id_utilisateurs SERIAL,
   email VARCHAR(100)  NOT NULL,
   nom VARCHAR(150)  NOT NULL,
   prenom VARCHAR(150)  NOT NULL,
   date_naissance DATE NOT NULL,
   mot_de_passe VARCHAR(250)  NOT NULL,
   etat BOOLEAN,
   nb_tentative INTEGER,
   id_sexe INTEGER NOT NULL,
   PRIMARY KEY(id_utilisateurs),
   UNIQUE(email),
   FOREIGN KEY(id_sexe) REFERENCES sexes(id_sexe)
);

CREATE TABLE tokens(
   id_token SERIAL,
   token VARCHAR(250)  NOT NULL,
   date_expiration TIMESTAMP NOT NULL,
   id_utilisateurs INTEGER NOT NULL,
   PRIMARY KEY(id_token),
   UNIQUE(token),
   FOREIGN KEY(id_utilisateurs) REFERENCES utilisateurs(id_utilisateurs)
);

CREATE TABLE code_pin(
   id_code_pin SERIAL,
   codepin INTEGER NOT NULL,
   date_expiration TIMESTAMP NOT NULL,
   id_utilisateurs INTEGER NOT NULL,
   PRIMARY KEY(id_code_pin),
   UNIQUE(codepin),
   FOREIGN KEY(id_utilisateurs) REFERENCES utilisateurs(id_utilisateurs)
);

INSERT INTO sexes(sexe) VALUES
('masculin'),
('feminin');

INSERT INTO configurations(cle,valeur) VALUES
('nbtentative','3'),
('token_lifetime','2000000000');