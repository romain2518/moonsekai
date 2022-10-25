<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221025220558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE anime (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, work_id INT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, state VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, author VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, animation_studio VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, release_year DATETIME NOT NULL, picture_path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_13045942BB3453DB (work_id), INDEX idx_advanced_search (name, state, author, animation_studio, release_year), INDEX IDX_13045942A76ED395 (user_id), INDEX idx_search (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE ban (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, message VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_62FED0E5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE calendar_event (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, start DATETIME NOT NULL, end DATETIME NOT NULL, target_table VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, target_id INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_57FA09C9A76ED395 (user_id), INDEX idx_search (target_table, target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE chapter (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, volume_id INT NOT NULL, number VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F981B52EA76ED395 (user_id), INDEX IDX_F981B52E8FD80EEA (volume_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, parent_id INT DEFAULT NULL, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, target_table VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, target_id INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9474526C727ACA70 (parent_id), INDEX idx_search (target_table, target_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE contact_request (id INT AUTO_INCREMENT NOT NULL, applicant_email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_important TINYINT(1) NOT NULL, is_processed TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE episode (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, season_id INT NOT NULL, number VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_DDAA1CDAA76ED395 (user_id), INDEX IDX_DDAA1CDA4EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE light_novel (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, work_id INT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, author VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, editor VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, release_year DATETIME NOT NULL, picture_path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_advanced_search (name, author, editor, release_year), INDEX IDX_8BFD0BDABB3453DB (work_id), INDEX IDX_8BFD0BDAA76ED395 (user_id), INDEX idx_search (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE manga (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, work_id INT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, state VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, release_regularity VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, author VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, designer VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, editor VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, release_year DATETIME NOT NULL, picture_path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_765A9E03BB3453DB (work_id), INDEX idx_advanced_search (name, state, release_regularity, author, designer, editor, release_year), INDEX IDX_765A9E03A76ED395 (user_id), INDEX idx_search (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, user_sender_id INT DEFAULT NULL, user_receiver_id INT DEFAULT NULL, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B6BD307FF6C43E79 (user_sender_id), INDEX IDX_B6BD307F64482423 (user_receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, work_id INT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, duration INT NOT NULL, animation_studio VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, release_year DATETIME NOT NULL, picture_path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1D5EF26FBB3453DB (work_id), INDEX idx_advanced_search (name, duration, animation_studio, release_year), INDEX IDX_1D5EF26FA76ED395 (user_id), INDEX idx_search (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, picture_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1DD39950A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_read TINYINT(1) NOT NULL, target_table VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, target_id INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_search (target_table, target_id), INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE plateform (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, url VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, picture_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_search (name), INDEX IDX_E82B067A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE progress (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, work_id INT NOT NULL, progress VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2201F246A76ED395 (user_id), INDEX IDX_2201F246BB3453DB (work_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE rate (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, target_table VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, target_id INT UNSIGNED NOT NULL, rate INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_search (target_table, target_id), INDEX IDX_DFEC3F39A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, is_processed TINYINT(1) NOT NULL, is_important TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C42F7784A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, anime_id INT NOT NULL, number VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, picture_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F0E45BA9794BBE89 (anime_id), INDEX IDX_F0E45BA9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_389B783A76ED395 (user_id), INDEX idx_search (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles JSON NOT NULL, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, pseudo VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, picture_path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, banner_path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, biography LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, notification_setting JSON NOT NULL, is_notification_redirection_enabled TINYINT(1) NOT NULL, is_muted TINYINT(1) NOT NULL, is_account_confirmed TINYINT(1) NOT NULL, is_subscribed_newsletter TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_newsletter (is_subscribed_newsletter), INDEX idx_pseudo (pseudo), INDEX idx_email (email), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE volume (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, manga_id INT NOT NULL, number VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, picture_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B99ACDDEA76ED395 (user_id), INDEX IDX_B99ACDDE7B6461 (manga_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE work (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, native_country VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, original_name VARCHAR(190) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, alternative_name JSON DEFAULT NULL, picture_path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_search (name, original_name), INDEX idx_advanced_search (name, type, native_country, original_name), INDEX IDX_534E6880A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE work_news (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, work_id INT NOT NULL, title VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, picture_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E4102868BB3453DB (work_id), INDEX IDX_E4102868A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE work_plateform (work_id INT NOT NULL, plateform_id INT NOT NULL, INDEX IDX_6C8A1832CCAA542F (plateform_id), INDEX IDX_6C8A1832BB3453DB (work_id), PRIMARY KEY(work_id, plateform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE work_tag (work_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_79E7E01FBAD26311 (tag_id), INDEX IDX_79E7E01FBB3453DB (work_id), PRIMARY KEY(work_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('CREATE TABLE work_user (work_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_74506771A76ED395 (user_id), INDEX IDX_74506771BB3453DB (work_id), PRIMARY KEY(work_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE anime');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE ban');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE calendar_event');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE chapter');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE comment');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE contact_request');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE episode');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE light_novel');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE manga');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE message');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE messenger_messages');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE movie');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE news');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE notification');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE plateform');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE progress');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE rate');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE report');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE season');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE tag');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE user');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE volume');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE work');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE work_news');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE work_plateform');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE work_tag');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL57Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL57Platform'."
        );

        $this->addSql('DROP TABLE work_user');
    }
}
