<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251222140339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset ADD software_name VARCHAR(255) DEFAULT NULL, ADD software_version VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE vulnerability ADD asset_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vulnerability ADD CONSTRAINT FK_6C4E40475DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6C4E40475DA1941 ON vulnerability (asset_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset DROP software_name, DROP software_version');
        $this->addSql('ALTER TABLE vulnerability DROP FOREIGN KEY FK_6C4E40475DA1941');
        $this->addSql('DROP INDEX IDX_6C4E40475DA1941 ON vulnerability');
        $this->addSql('ALTER TABLE vulnerability DROP asset_id');
    }
}
