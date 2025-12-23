<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251223160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patch_cycle ADD module_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patch_cycle ADD CONSTRAINT FK_F29624DFAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_F29624DFAFC2B591 ON patch_cycle (module_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patch_cycle DROP FOREIGN KEY FK_F29624DFAFC2B591');
        $this->addSql('DROP INDEX IDX_F29624DFAFC2B591 ON patch_cycle');
        $this->addSql('ALTER TABLE patch_cycle DROP module_id');
    }
}
