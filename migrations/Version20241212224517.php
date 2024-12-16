<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212224517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_plat (id INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(100) NOT NULL, descr_categorie VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chambre (id INT AUTO_INCREMENT NOT NULL, numero_ch_b VARCHAR(50) NOT NULL, etage_ch_b INT NOT NULL, capacite_ch_b INT NOT NULL, statut_ch_b VARCHAR(20) NOT NULL, image VARCHAR(255) DEFAULT NULL, prix_ch_b DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demande_plat (id INT AUTO_INCREMENT NOT NULL, plat_id INT NOT NULL, user_id INT NOT NULL, date_demande DATETIME NOT NULL, description VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, telephone INT DEFAULT NULL, email VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_479DD0D2D73DB560 (plat_id), INDEX IDX_479DD0D2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demande_service (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, user_id INT NOT NULL, date_demande DATETIME NOT NULL, description VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, telephone INT DEFAULT NULL, email VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_D16A217DED5CA9E6 (service_id), INDEX IDX_D16A217DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement (id_equipement_b INT AUTO_INCREMENT NOT NULL, chambre_id INT DEFAULT NULL, nom_equipement_b VARCHAR(100) NOT NULL, etat_equipement_b VARCHAR(50) NOT NULL, image VARCHAR(255) DEFAULT NULL, date_dernier_entretien_equipement_b DATE NOT NULL, INDEX IDX_B8B4C6F39B177F54 (chambre_id), PRIMARY KEY(id_equipement_b)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plat (id INT AUTO_INCREMENT NOT NULL, categorie_plat_id INT DEFAULT NULL, nom_plat VARCHAR(255) DEFAULT NULL, desc_plat LONGTEXT DEFAULT NULL, prix_plat DOUBLE PRECISION NOT NULL, type_cuisine VARCHAR(255) NOT NULL, dispo_plat TINYINT(1) NOT NULL, image VARCHAR(255) DEFAULT NULL, quantite INT NOT NULL, INDEX IDX_2038A20788BE1BC2 (categorie_plat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_reclamation DATETIME NOT NULL, etat VARCHAR(255) NOT NULL, reponse VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, chambre_id INT NOT NULL, user_id INT NOT NULL, date_reservation DATETIME NOT NULL, date_arrivee DATETIME NOT NULL, date_depart DATETIME NOT NULL, nom_etudiant VARCHAR(255) NOT NULL, email_etudiant VARCHAR(255) NOT NULL, telephone_etudiant VARCHAR(255) DEFAULT NULL, statut VARCHAR(50) DEFAULT \'En attente\' NOT NULL, INDEX IDX_42C849559B177F54 (chambre_id), UNIQUE INDEX UNIQ_42C84955A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, type_service_id INT NOT NULL, nom VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, date_fin DATETIME NOT NULL, description VARCHAR(255) DEFAULT NULL, prix DOUBLE PRECISION DEFAULT NULL, INDEX IDX_E19D9AD2F05F7FC3 (type_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_de_chambre (id_type_ch_b INT AUTO_INCREMENT NOT NULL, type_chambre_b VARCHAR(50) NOT NULL, description_chambre_b LONGTEXT DEFAULT NULL, prix_chambre_b DOUBLE PRECISION NOT NULL, PRIMARY KEY(id_type_ch_b)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_reclamation (id INT AUTO_INCREMENT NOT NULL, nom_type_reclamation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_service (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demande_plat ADD CONSTRAINT FK_479DD0D2D73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id)');
        $this->addSql('ALTER TABLE demande_plat ADD CONSTRAINT FK_479DD0D2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE demande_service ADD CONSTRAINT FK_D16A217DED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE demande_service ADD CONSTRAINT FK_D16A217DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F39B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('ALTER TABLE plat ADD CONSTRAINT FK_2038A20788BE1BC2 FOREIGN KEY (categorie_plat_id) REFERENCES categorie_plat (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849559B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2F05F7FC3 FOREIGN KEY (type_service_id) REFERENCES type_service (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_plat DROP FOREIGN KEY FK_479DD0D2D73DB560');
        $this->addSql('ALTER TABLE demande_plat DROP FOREIGN KEY FK_479DD0D2A76ED395');
        $this->addSql('ALTER TABLE demande_service DROP FOREIGN KEY FK_D16A217DED5CA9E6');
        $this->addSql('ALTER TABLE demande_service DROP FOREIGN KEY FK_D16A217DA76ED395');
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F39B177F54');
        $this->addSql('ALTER TABLE plat DROP FOREIGN KEY FK_2038A20788BE1BC2');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849559B177F54');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2F05F7FC3');
        $this->addSql('DROP TABLE categorie_plat');
        $this->addSql('DROP TABLE chambre');
        $this->addSql('DROP TABLE demande_plat');
        $this->addSql('DROP TABLE demande_service');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE plat');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE type_de_chambre');
        $this->addSql('DROP TABLE type_reclamation');
        $this->addSql('DROP TABLE type_service');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
