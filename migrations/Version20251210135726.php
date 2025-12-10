<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251210135726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_885dbafa5e237e06 ON permissions');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DEDCC6F5E237E06 ON permissions (name)');
        $this->addSql('DROP INDEX uniq_3b760e0657698a6a ON role_permissions');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1FBA94E657698A6A ON role_permissions (role)');
        $this->addSql('ALTER TABLE role_permission_mappings DROP FOREIGN KEY FK_7F5AF3D4F1D79E23');
        $this->addSql('ALTER TABLE role_permission_mappings DROP FOREIGN KEY FK_7F5AF3D4FED90CCA');
        $this->addSql('DROP INDEX idx_7f5af3d4f1d79e23 ON role_permission_mappings');
        $this->addSql('CREATE INDEX IDX_75367C277128C459 ON role_permission_mappings (role_permission_id)');
        $this->addSql('DROP INDEX idx_7f5af3d4fed90cca ON role_permission_mappings');
        $this->addSql('CREATE INDEX IDX_75367C27FED90CCA ON role_permission_mappings (permission_id)');
        $this->addSql('ALTER TABLE role_permission_mappings ADD CONSTRAINT FK_7F5AF3D4F1D79E23 FOREIGN KEY (role_permission_id) REFERENCES role_permissions (role_permission_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_permission_mappings ADD CONSTRAINT FK_7F5AF3D4FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (permission_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_2dedcc6f5e237e06 ON permissions');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_885DBAFA5E237E06 ON permissions (name)');
        $this->addSql('DROP INDEX uniq_1fba94e657698a6a ON role_permissions');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3B760E0657698A6A ON role_permissions (role)');
        $this->addSql('ALTER TABLE role_permission_mappings DROP FOREIGN KEY FK_75367C277128C459');
        $this->addSql('ALTER TABLE role_permission_mappings DROP FOREIGN KEY FK_75367C27FED90CCA');
        $this->addSql('DROP INDEX idx_75367c277128c459 ON role_permission_mappings');
        $this->addSql('CREATE INDEX IDX_7F5AF3D4F1D79E23 ON role_permission_mappings (role_permission_id)');
        $this->addSql('DROP INDEX idx_75367c27fed90cca ON role_permission_mappings');
        $this->addSql('CREATE INDEX IDX_7F5AF3D4FED90CCA ON role_permission_mappings (permission_id)');
        $this->addSql('ALTER TABLE role_permission_mappings ADD CONSTRAINT FK_75367C277128C459 FOREIGN KEY (role_permission_id) REFERENCES role_permissions (role_permission_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_permission_mappings ADD CONSTRAINT FK_75367C27FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (permission_id) ON DELETE CASCADE');
    }
}
