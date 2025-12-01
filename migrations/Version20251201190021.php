<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251201190021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patch_cycle ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6B8E27FE5EC1162 ON patch_cycle (cycle_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE vulnerability DROP FOREIGN KEY FK_6C4E40479DE87C3');
        $this->addSql('ALTER TABLE vulnerability ADD id INT AUTO_INCREMENT NOT NULL, CHANGE patch_cycle_id patch_cycle_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE vulnerability ADD CONSTRAINT FK_6C4E40479DE87C3 FOREIGN KEY (patch_cycle_id) REFERENCES patch_cycle (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C4E4047DCC8D22B ON vulnerability (cve_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE patch_cycle MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_6B8E27FE5EC1162 ON patch_cycle');
        $this->addSql('DROP INDEX `PRIMARY` ON patch_cycle');
        $this->addSql('ALTER TABLE patch_cycle DROP id');
        $this->addSql('ALTER TABLE patch_cycle ADD PRIMARY KEY (cycle_id)');
        $this->addSql('ALTER TABLE vulnerability MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE vulnerability DROP FOREIGN KEY FK_6C4E40479DE87C3');
        $this->addSql('DROP INDEX UNIQ_6C4E4047DCC8D22B ON vulnerability');
        $this->addSql('DROP INDEX `PRIMARY` ON vulnerability');
        $this->addSql('ALTER TABLE vulnerability DROP id, CHANGE patch_cycle_id patch_cycle_id VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE vulnerability ADD CONSTRAINT FK_6C4E40479DE87C3 FOREIGN KEY (patch_cycle_id) REFERENCES patch_cycle (cycle_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE vulnerability ADD PRIMARY KEY (cve_id)');
    }
}
