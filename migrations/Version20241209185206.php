<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209185206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_service ADD service_id INT NOT NULL, ADD user_id INT NOT NULL, ADD status VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE demande_service ADD CONSTRAINT FK_D16A217DED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE demande_service ADD CONSTRAINT FK_D16A217DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D16A217DED5CA9E6 ON demande_service (service_id)');
        $this->addSql('CREATE INDEX IDX_D16A217DA76ED395 ON demande_service (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_service DROP FOREIGN KEY FK_D16A217DED5CA9E6');
        $this->addSql('ALTER TABLE demande_service DROP FOREIGN KEY FK_D16A217DA76ED395');
        $this->addSql('DROP INDEX IDX_D16A217DED5CA9E6 ON demande_service');
        $this->addSql('DROP INDEX IDX_D16A217DA76ED395 ON demande_service');
        $this->addSql('ALTER TABLE demande_service DROP service_id, DROP user_id, DROP status');
    }
}
