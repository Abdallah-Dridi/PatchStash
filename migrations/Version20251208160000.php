<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251208160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update role_permission_mappings join table foreign key constraints.';
    }

    public function up(Schema $schema): void
    {
        // Drop existing foreign key constraints
        $this->addSql('ALTER TABLE role_permission_mappings DROP FOREIGN KEY FK_7F5AF3D4F1D79E23');
        $this->addSql('ALTER TABLE role_permission_mappings DROP FOREIGN KEY FK_7F5AF3D4FED90CCA');
        
        // Drop the table and recreate it with proper column names
        $this->addSql('DROP TABLE role_permission_mappings');
        
        // Create the table with correct mappings
        $this->addSql('CREATE TABLE role_permission_mappings (
            role_permission_id INT NOT NULL,
            permission_id INT NOT NULL,
            INDEX IDX_7F5AF3D4F1D79E23 (role_permission_id),
            INDEX IDX_7F5AF3D4FED90CCA (permission_id),
            PRIMARY KEY(role_permission_id, permission_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        // Add back the foreign key constraints with proper column names
        $this->addSql('ALTER TABLE role_permission_mappings ADD CONSTRAINT FK_7F5AF3D4F1D79E23 FOREIGN KEY (role_permission_id) REFERENCES role_permissions (role_permission_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_permission_mappings ADD CONSTRAINT FK_7F5AF3D4FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (permission_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE role_permission_mappings DROP FOREIGN KEY FK_7F5AF3D4F1D79E23');
        $this->addSql('ALTER TABLE role_permission_mappings DROP FOREIGN KEY FK_7F5AF3D4FED90CCA');
        $this->addSql('DROP TABLE role_permission_mappings');
        
        $this->addSql('CREATE TABLE role_permission_mappings (
            role_permission_id INT NOT NULL,
            permission_id INT NOT NULL,
            INDEX IDX_7F5AF3D4F1D79E23 (role_permission_id),
            INDEX IDX_7F5AF3D4FED90CCA (permission_id),
            PRIMARY KEY(role_permission_id, permission_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE role_permission_mappings ADD CONSTRAINT FK_7F5AF3D4F1D79E23 FOREIGN KEY (role_permission_id) REFERENCES role_permissions (role_permission_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_permission_mappings ADD CONSTRAINT FK_7F5AF3D4FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (permission_id) ON DELETE CASCADE');
    }
}
