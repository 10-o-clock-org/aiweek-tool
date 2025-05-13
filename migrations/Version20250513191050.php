<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513191050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $channels = [
            [ 'name' => 'Tech & Science', 'sort' => 10 ],
            [ 'name' => 'Digitale Gesellschaft & Nachhaltigkeit', 'sort' => 20 ],
            [ 'name' => 'Business & New Work', 'sort' => 30 ],
            [ 'name' => 'Gründen/Start-up', 'sort' => 40 ],
            [ 'name' => 'Kultur', 'sort' => 50 ],
        ];

        foreach ($channels as $channel) {
            $this->addSql('INSERT INTO channel SET name = :name, sort = :sort', $channel);
        }
    }

    public function down(Schema $schema): void
    {
    }
}
