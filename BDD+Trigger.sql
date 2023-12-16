CREATE TABLE Medecins (
    ID_Medecin INT AUTO_INCREMENT PRIMARY KEY,
    Civilité VARCHAR(10),
    Nom VARCHAR(50),
    Prénom VARCHAR(50),
    UNIQUE(Civilité, Nom, Prénom)
);

CREATE TABLE Usagers (
    ID_Usager INT AUTO_INCREMENT PRIMARY KEY,
    Civilité CHAR(1) CHECK (Civilité IN ('F', 'M')),
    Nom VARCHAR(50),
    Prénom VARCHAR(50),
    Adresse VARCHAR(100),
    Ville VARCHAR(100),
    Cp CHAR(5),
    DateNaissance DATE,
    LieuNaissance VARCHAR(50),
    NumSecuSociale VARCHAR(15) UNIQUE,
    MédecinRéférent INT,
    FOREIGN KEY (MédecinRéférent) REFERENCES Medecins(ID_Medecin),
    UNIQUE(Nom, Prénom, DateNaissance)
);

CREATE TABLE RendezVous (
    ID_RendezVous INT AUTO_INCREMENT PRIMARY KEY,
    DateConsultation DATE,
    HeureConsultation TIME,
    DuréeConsultation INT DEFAULT 30,
    ID_Usager INT,
    ID_Medecin INT,
    FOREIGN KEY (ID_Usager) REFERENCES Usagers(ID_Usager),
    FOREIGN KEY (ID_Medecin) REFERENCES Medecins(ID_Medecin),
    UNIQUE (ID_Usager, DateConsultation, HeureConsultation)
);

CREATE TABLE Utilisateurs (
    ID_Utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    NomUtilisateur VARCHAR(50) UNIQUE NOT NULL,
    MotDePasse VARCHAR(255) NOT NULL
);

------------------------------------------------------------------
------------------------- MEDECINS -------------------------------
------------------------------------------------------------------



------------------------------------------------------------------
------------------------- USAGERS --------------------------------
------------------------------------------------------------------
DELIMITER //
CREATE TRIGGER t_bi_usager_medecin_self
BEFORE INSERT ON Usagers FOR EACH ROW
BEGIN
    IF (NEW.ID_Usager  = NEW.MédecinRéférent) THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Un usager ne peut pas etre son propre médecin réferrent';
    END IF;
END;
//
DELIMITER ;

DELIMITER //
CREATE TRIGGER t_bu_usager_medecin_self
BEFORE UPDATE ON Usagers FOR EACH ROW
BEGIN
    IF (NEW.ID_Usager  = NEW.MédecinRéférent) THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Un usager ne peut pas etre son propre médecin réferrent';
    END IF;
END;
//
DELIMITER ;

-----------------------

DELIMITER //
CREATE TRIGGER before_insert_usagers
BEFORE INSERT ON Usagers
FOR EACH ROW
BEGIN
    -- Vérifier le format du numéro de sécurité sociale
    IF NEW.NumSecuSociale IS NOT NULL AND NEW.NumSecuSociale NOT REGEXP '^[1-37-8][0-9]{12}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Format invalide pour le numéro de sécurité sociale.';
    END IF;
END;
//
DELIMITER ;

DELIMITER //
CREATE TRIGGER before_update_usagers
BEFORE UPDATE ON Usagers
FOR EACH ROW
BEGIN
    -- Vérifier le format du numéro de sécurité sociale
    IF NEW.NumSecuSociale IS NOT NULL AND NEW.NumSecuSociale NOT REGEXP '^[1-37-8][0-9]{12}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Format invalide pour le numéro de sécurité sociale.';
    END IF;
END;
//
DELIMITER ;



------------------------------------------------------------------
------------------------ RENDEZ-VOUS -----------------------------
------------------------------------------------------------------
DELIMITER //
CREATE TRIGGER t_bi_HeureRDV
BEFORE INSERT ON RendezVous
FOR EACH ROW
BEGIN
    IF (NEW.HeureConsultation < '08:00:00' OR NEW.HeureConsultation > '18:00:00') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ce n''est pas une heure de rendez-vous valable';
    END IF;
END;
//
DELIMITER ;


DELIMITER //
CREATE TRIGGER t_bu_HeureRDV
BEFORE UPDATE ON RendezVous
FOR EACH ROW
BEGIN
    IF (NEW.HeureConsultation < '08:00:00' OR NEW.HeureConsultation > '18:00:00') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ce n''est pas une heure de rendez-vous valable';
    END IF;
END;
//
DELIMITER ;

-----------------------
DELIMITER //
CREATE TRIGGER t_bi_DateRDV
BEFORE INSERT ON RendezVous
FOR EACH ROW
BEGIN
    IF (DAYOFWEEK(NEW.DateConsultation) IN (1, 7)) THEN
        SIGNAL SQLSTATE '45001'
        SET MESSAGE_TEXT = 'Ce n''est pas une date de rendez-vous valable (samedi ou dimanche)';
    END IF;
END;
//
DELIMITER ;


DELIMITER //
CREATE TRIGGER t_bu_DateRDV
BEFORE UPDATE ON RendezVous
FOR EACH ROW
BEGIN
    IF (DAYOFWEEK(NEW.DateConsultation) IN (1, 7)) THEN
        SIGNAL SQLSTATE '45001'
        SET MESSAGE_TEXT = 'Ce n''est pas une date de rendez-vous valable (samedi ou dimanche)';
    END IF;
END;
//
DELIMITER ;
------------------------

DELIMITER //
CREATE TRIGGER t_bi_DuréeRDV_Superieur
BEFORE INSERT ON RendezVous
FOR EACH ROW
BEGIN
    IF (NEW.DuréeConsultation > 60) THEN
        SIGNAL SQLSTATE '45002'
        SET MESSAGE_TEXT = 'Un rendez-vous ne peut pas dépasser une durée d''une heure';
    END IF;
END;
//
DELIMITER ;


DELIMITER //
CREATE TRIGGER t_bu_DuréeRDV_Superieur
BEFORE UPDATE ON RendezVous
FOR EACH ROW
BEGIN
    IF (NEW.DuréeConsultation > 60) THEN
        SIGNAL SQLSTATE '45002'
        SET MESSAGE_TEXT = 'Un rendez-vous ne peut pas dépasser une durée d''une heure';
    END IF;
END;
//
DELIMITER ;

-------------------------

DELIMITER //
CREATE TRIGGER t_bi_DuréeRDV_Inferieur
BEFORE INSERT ON RendezVous
FOR EACH ROW
BEGIN
    IF (NEW.DuréeConsultation < 15) THEN
        SIGNAL SQLSTATE '45003'
        SET MESSAGE_TEXT = 'Un rendez-vous ne peut pas durer moins de 15 min';
    END IF;
END;
//
DELIMITER ;


DELIMITER //
CREATE TRIGGER t_bu_DuréeRDV_Inferieur
BEFORE UPDATE ON RendezVous
FOR EACH ROW
BEGIN
    IF (NEW.DuréeConsultation < 15) THEN
        SIGNAL SQLSTATE '45003'
        SET MESSAGE_TEXT = 'Un rendez-vous ne peut pas durer moins de 15 min';
    END IF;
END;
//
DELIMITER ;
