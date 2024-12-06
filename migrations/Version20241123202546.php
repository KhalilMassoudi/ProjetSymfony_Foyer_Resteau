<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241123202546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE boo (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_service (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plat DROP FOREIGN KEY FK_2038A20788BE1BC2');
        $this->addSql('ALTER TABLE plat ADD CONSTRAINT FK_2038A20788BE1BC2 FOREIGN KEY (categorie_plat_id) REFERENCES categorie_plat (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE reclamation ADD type_reclamation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064047BA88B4D FOREIGN KEY (type_reclamation_id) REFERENCES type_reclamation (id)');
        $this->addSql('CREATE INDEX IDX_CE6064047BA88B4D ON reclamation (type_reclamation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE boo');
        $this->addSql('DROP TABLE type_service');
        $this->addSql('ALTER TABLE plat DROP FOREIGN KEY FK_2038A20788BE1BC2');
        $this->addSql('ALTER TABLE plat ADD CONSTRAINT FK_2038A20788BE1BC2 FOREIGN KEY (categorie_plat_id) REFERENCES categorie_plat (id)');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064047BA88B4D');
        $this->addSql('DROP INDEX IDX_CE6064047BA88B4D ON reclamation');
        $this->addSql('ALTER TABLE reclamation DROP type_reclamation_id');
    }
}
