<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241123220858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre ADD id INT AUTO_INCREMENT NOT NULL, DROP id_ch_b, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE equipement ADD chambre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F39B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('CREATE INDEX IDX_B8B4C6F39B177F54 ON equipement (chambre_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON chambre');
        $this->addSql('ALTER TABLE chambre ADD id_ch_b INT NOT NULL, DROP id');
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F39B177F54');
        $this->addSql('DROP INDEX IDX_B8B4C6F39B177F54 ON equipement');
        $this->addSql('ALTER TABLE equipement DROP chambre_id');
    }
}
