<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513190931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE channel (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, sort INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, proposed_organization_details_id INT DEFAULT NULL, accepted_organization_details_id INT DEFAULT NULL, owner_id INT NOT NULL, logo_file_name VARCHAR(255) DEFAULT NULL, send_batch_mail_notification TINYINT(1) NOT NULL, gold_sponsor TINYINT(1) DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_C1EE637CAFC27071 (proposed_organization_details_id), UNIQUE INDEX UNIQ_C1EE637C18A056CF (accepted_organization_details_id), INDEX IDX_C1EE637C7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE organization_detail (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, contact_name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, jobs_url VARCHAR(255) DEFAULT NULL, facebook_url VARCHAR(255) DEFAULT NULL, twitter_url VARCHAR(255) DEFAULT NULL, youtube_url VARCHAR(255) DEFAULT NULL, instagram_url VARCHAR(255) DEFAULT NULL, linkedin_url VARCHAR(255) DEFAULT NULL, fediverse_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, draft_details_id INT NOT NULL, proposed_details_id INT DEFAULT NULL, accepted_details_id INT DEFAULT NULL, organization_id INT NOT NULL, start DATETIME DEFAULT NULL, stop DATETIME DEFAULT NULL, cancelled TINYINT(1) NOT NULL, highlight TINYINT(1) DEFAULT 0 NOT NULL, accepted_at DATETIME DEFAULT NULL, status VARCHAR(255) DEFAULT 'Created' NOT NULL, draft_notification_due_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_D044D5D4844F7689 (draft_details_id), UNIQUE INDEX UNIQ_D044D5D4F9D283C (proposed_details_id), UNIQUE INDEX UNIQ_D044D5D4768D045C (accepted_details_id), INDEX IDX_D044D5D432C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE session_detail (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, start1 DATETIME DEFAULT NULL, stop1 DATETIME DEFAULT NULL, start2 DATETIME DEFAULT NULL, start3 DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, short_description LONGTEXT DEFAULT NULL, long_description LONGTEXT DEFAULT NULL, location_lat DOUBLE PRECISION DEFAULT NULL, location_lng DOUBLE PRECISION DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, online_only TINYINT(1) DEFAULT 0 NOT NULL, location_name VARCHAR(255) DEFAULT NULL, location_street_no VARCHAR(255) DEFAULT NULL, location_zipcode VARCHAR(255) DEFAULT NULL, location_city VARCHAR(255) DEFAULT NULL, location_is_accessible TINYINT(1) NOT NULL, INDEX IDX_416D75CA72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE token (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_5F37A13B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, registration_complete TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organization ADD CONSTRAINT FK_C1EE637CAFC27071 FOREIGN KEY (proposed_organization_details_id) REFERENCES organization_detail (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C18A056CF FOREIGN KEY (accepted_organization_details_id) REFERENCES organization_detail (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D4844F7689 FOREIGN KEY (draft_details_id) REFERENCES session_detail (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D4F9D283C FOREIGN KEY (proposed_details_id) REFERENCES session_detail (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D4768D045C FOREIGN KEY (accepted_details_id) REFERENCES session_detail (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session_detail ADD CONSTRAINT FK_416D75CA72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE token ADD CONSTRAINT FK_5F37A13B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637CAFC27071
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637C18A056CF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637C7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4844F7689
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4F9D283C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4768D045C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP FOREIGN KEY FK_D044D5D432C8A3DE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session_detail DROP FOREIGN KEY FK_416D75CA72F5A1AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE token DROP FOREIGN KEY FK_5F37A13B7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE channel
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE organization
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE organization_detail
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE session
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE session_detail
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE token
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
    }
}
