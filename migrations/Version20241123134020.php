<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241123134020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boo (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_plat (id INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(100) NOT NULL, descr_categorie VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plat (id INT AUTO_INCREMENT NOT NULL, categorie_plat_id INT DEFAULT NULL, nom_plat VARCHAR(255) NOT NULL, desc_plat LONGTEXT DEFAULT NULL, prix_plat DOUBLE PRECISION NOT NULL, type_cuisine VARCHAR(255) NOT NULL, dispo_plat TINYINT(1) NOT NULL, INDEX IDX_2038A20788BE1BC2 (categorie_plat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_reclamation DATETIME NOT NULL, etat VARCHAR(255) NOT NULL, reponse VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_reclamation (id INT AUTO_INCREMENT NOT NULL, nom_type_reclamation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plat ADD CONSTRAINT FK_2038A20788BE1BC2 FOREIGN KEY (categorie_plat_id) REFERENCES categorie_plat (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE service ADD type_service_id INT NOT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2F05F7FC3 FOREIGN KEY (type_service_id) REFERENCES type_service (id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD2F05F7FC3 ON service (type_service_id)');
        $this->addSql('ALTER TABLE type_service ADD description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(180) NOT NULL, CHANGE address address VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plat DROP FOREIGN KEY FK_2038A20788BE1BC2');
        $this->addSql('DROP TABLE boo');
        $this->addSql('DROP TABLE categorie_plat');
        $this->addSql('DROP TABLE plat');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE type_reclamation');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2F05F7FC3');
        $this->addSql('DROP INDEX IDX_E19D9AD2F05F7FC3 ON service');
        $this->addSql('ALTER TABLE service DROP type_service_id');
        $this->addSql('ALTER TABLE type_service DROP description');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_USERNAME ON user');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(255) NOT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL');
    }
}
