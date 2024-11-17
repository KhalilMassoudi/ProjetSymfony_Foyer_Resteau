<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113202252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chambre (id_ch_b INT AUTO_INCREMENT NOT NULL, numero_ch_b VARCHAR(50) NOT NULL, etage_ch_b INT NOT NULL, capacite_ch_b INT NOT NULL, statut_ch_b VARCHAR(20) NOT NULL, PRIMARY KEY(id_ch_b)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement (id_equipement_b INT AUTO_INCREMENT NOT NULL, nom_equipement_b VARCHAR(100) NOT NULL, etat_equipement_b VARCHAR(50) NOT NULL, date_dernier_entretien_equipement_b DATE NOT NULL, PRIMARY KEY(id_equipement_b)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_de_chambre (id_type_ch_b INT AUTO_INCREMENT NOT NULL, type_chambre_b VARCHAR(50) NOT NULL, description_chambre_b LONGTEXT DEFAULT NULL, prix_chambre_b DOUBLE PRECISION NOT NULL, PRIMARY KEY(id_type_ch_b)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE chambre');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE type_de_chambre');
    }
}
