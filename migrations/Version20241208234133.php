<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<<< HEAD:migrations/Version20241206145555.php
final class Version20241206145555 extends AbstractMigration
========
final class Version20241208234133 extends AbstractMigration
>>>>>>>> main:migrations/Version20241208234133.php
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20241206145555.php
        $this->addSql('DROP TABLE boo');
========
        $this->addSql('ALTER TABLE demande_service ADD status VARCHAR(20) NOT NULL');
>>>>>>>> main:migrations/Version20241208234133.php
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20241206145555.php

========
        $this->addSql('ALTER TABLE demande_service DROP status');
>>>>>>>> main:migrations/Version20241208234133.php
    }
}
