<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adjust vulnerability uniqueness to be per-asset.
 */
final class Version20251223170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make vulnerability cve_id unique per asset instead of globally.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_6C4E4047DCC8D22B ON vulnerability');
        $this->addSql('CREATE UNIQUE INDEX uniq_vulnerability_asset_cve ON vulnerability (asset_id, cve_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_vulnerability_asset_cve ON vulnerability');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C4E4047DCC8D22B ON vulnerability (cve_id)');
    }
}
