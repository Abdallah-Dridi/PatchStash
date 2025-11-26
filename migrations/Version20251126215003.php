<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126215003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patch_cycle DROP FOREIGN KEY FK_6B8E27FE3D3CDAA0');
        $this->addSql('DROP INDEX IDX_6B8E27FE3D3CDAA0 ON patch_cycle');
        $this->addSql('ALTER TABLE patch_cycle DROP vulnerability_cve_id');
        $this->addSql('ALTER TABLE vulnerability ADD patch_cycle_id VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE vulnerability ADD CONSTRAINT FK_6C4E40479DE87C3 FOREIGN KEY (patch_cycle_id) REFERENCES patch_cycle (cycle_id)');
        $this->addSql('CREATE INDEX IDX_6C4E40479DE87C3 ON vulnerability (patch_cycle_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patch_cycle ADD vulnerability_cve_id VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE patch_cycle ADD CONSTRAINT FK_6B8E27FE3D3CDAA0 FOREIGN KEY (vulnerability_cve_id) REFERENCES vulnerability (cve_id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6B8E27FE3D3CDAA0 ON patch_cycle (vulnerability_cve_id)');
        $this->addSql('ALTER TABLE vulnerability DROP FOREIGN KEY FK_6C4E40479DE87C3');
        $this->addSql('DROP INDEX IDX_6C4E40479DE87C3 ON vulnerability');
        $this->addSql('ALTER TABLE vulnerability DROP patch_cycle_id');
    }
}
