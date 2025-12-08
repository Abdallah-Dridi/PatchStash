<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251208150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create permissions and role_permissions tables for IAM.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE permissions (permission_id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT, category VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_885DBAFA5E237E06 (name), PRIMARY KEY(permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_permissions (role_permission_id INT AUTO_INCREMENT NOT NULL, role VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_3B760E0657698A6A (role), PRIMARY KEY(role_permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_permission_mappings (role_permission_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_7F5AF3D4F1D79E23 (role_permission_id), INDEX IDX_7F5AF3D4FED90CCA (permission_id), PRIMARY KEY(role_permission_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_permission_mappings ADD CONSTRAINT FK_7F5AF3D4F1D79E23 FOREIGN KEY (role_permission_id) REFERENCES role_permissions (role_permission_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_permission_mappings ADD CONSTRAINT FK_7F5AF3D4FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (permission_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE role_permission_mappings DROP FOREIGN KEY FK_7F5AF3D4F1D79E23');
        $this->addSql('ALTER TABLE role_permission_mappings DROP FOREIGN KEY FK_7F5AF3D4FED90CCA');
        $this->addSql('DROP TABLE role_permission_mappings');
        $this->addSql('DROP TABLE role_permissions');
        $this->addSql('DROP TABLE permissions');
    }
}
