<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241120145914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_reclamation DATETIME NOT NULL, etat VARCHAR(255) NOT NULL, reponse VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_reclamation (id INT AUTO_INCREMENT NOT NULL, nom_type_reclamation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_service (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plat DROP FOREIGN KEY FK_2038A20788BE1BC2');
        $this->addSql('ALTER TABLE plat ADD CONSTRAINT FK_2038A20788BE1BC2 FOREIGN KEY (categorie_plat_id) REFERENCES categorie_plat (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE type_reclamation');
        $this->addSql('DROP TABLE type_service');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('ALTER TABLE plat DROP FOREIGN KEY FK_2038A20788BE1BC2');
        $this->addSql('ALTER TABLE plat ADD CONSTRAINT FK_2038A20788BE1BC2 FOREIGN KEY (categorie_plat_id) REFERENCES categorie_plat (id)');
    }
}
