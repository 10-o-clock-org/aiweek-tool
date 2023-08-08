<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230808121958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE channel ADD sort INT NOT NULL');
        $this->addSql('UPDATE channel SET sort = CASE WHEN id = 7 THEN 100 ELSE id END');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE channel DROP sort');
    }
}
