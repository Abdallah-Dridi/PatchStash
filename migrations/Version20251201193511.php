<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251201193511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE asset (id INT AUTO_INCREMENT NOT NULL, module_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(100) NOT NULL, environment VARCHAR(100) NOT NULL, status VARCHAR(100) NOT NULL, info LONGTEXT DEFAULT NULL, INDEX IDX_2AF5A5CAFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(100) NOT NULL, info LONGTEXT DEFAULT NULL, INDEX IDX_C242628166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patch_cycle (id INT AUTO_INCREMENT NOT NULL, asset_id INT DEFAULT NULL, cycle_id VARCHAR(50) NOT NULL, status VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, deadline DATE NOT NULL, applied_date DATE DEFAULT NULL, cvss DOUBLE PRECISION NOT NULL, info LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_6B8E27FE5EC1162 (cycle_id), INDEX IDX_6B8E27FE5DA1941 (asset_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(100) NOT NULL, info LONGTEXT DEFAULT NULL, INDEX IDX_2FB3D0EEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (report_id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, generated_date DATE NOT NULL, INDEX IDX_C42F7784166D1F9C (project_id), PRIMARY KEY(report_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, data LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vulnerability (id INT AUTO_INCREMENT NOT NULL, patch_cycle_id INT DEFAULT NULL, cve_id VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, severity VARCHAR(50) NOT NULL, cvss DOUBLE PRECISION NOT NULL, info LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_6C4E4047DCC8D22B (cve_id), INDEX IDX_6C4E40479DE87C3 (patch_cycle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5CAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE module ADD CONSTRAINT FK_C242628166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE patch_cycle ADD CONSTRAINT FK_6B8E27FE5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE vulnerability ADD CONSTRAINT FK_6C4E40479DE87C3 FOREIGN KEY (patch_cycle_id) REFERENCES patch_cycle (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5CAFC2B591');
        $this->addSql('ALTER TABLE module DROP FOREIGN KEY FK_C242628166D1F9C');
        $this->addSql('ALTER TABLE patch_cycle DROP FOREIGN KEY FK_6B8E27FE5DA1941');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEA76ED395');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784166D1F9C');
        $this->addSql('ALTER TABLE vulnerability DROP FOREIGN KEY FK_6C4E40479DE87C3');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE patch_cycle');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vulnerability');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
