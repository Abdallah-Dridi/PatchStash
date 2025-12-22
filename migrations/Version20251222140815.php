<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251222140815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset CHANGE description description LONGTEXT DEFAULT NULL, CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE environment environment VARCHAR(100) DEFAULT NULL, CHANGE status status VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset CHANGE description description LONGTEXT NOT NULL, CHANGE type type VARCHAR(100) NOT NULL, CHANGE environment environment VARCHAR(100) NOT NULL, CHANGE status status VARCHAR(100) NOT NULL');
    }
}
