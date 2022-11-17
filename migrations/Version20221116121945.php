<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221116121945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE platform (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, url VARCHAR(100) NOT NULL, picture_path VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3952D0CBA76ED395 (user_id), INDEX idx_search (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_platform (work_id INT NOT NULL, platform_id INT NOT NULL, INDEX IDX_ECE75A97BB3453DB (work_id), INDEX IDX_ECE75A97FFE6496F (platform_id), PRIMARY KEY(work_id, platform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE platform ADD CONSTRAINT FK_3952D0CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE work_platform ADD CONSTRAINT FK_ECE75A97BB3453DB FOREIGN KEY (work_id) REFERENCES work (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE work_platform ADD CONSTRAINT FK_ECE75A97FFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE plateform');
        $this->addSql('DROP TABLE work_plateform');
        $this->addSql('ALTER TABLE anime CHANGE release_year release_year INT NOT NULL');
        $this->addSql('ALTER TABLE anime ADD CONSTRAINT FK_13045942A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE anime ADD CONSTRAINT FK_13045942BB3453DB FOREIGN KEY (work_id) REFERENCES work (id)');
        $this->addSql('ALTER TABLE ban ADD CONSTRAINT FK_62FED0E5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE calendar_event ADD CONSTRAINT FK_57FA09C9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52E8FD80EEA FOREIGN KEY (volume_id) REFERENCES volume (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDA4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE light_novel CHANGE release_year release_year INT NOT NULL');
        $this->addSql('ALTER TABLE light_novel ADD CONSTRAINT FK_8BFD0BDAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE light_novel ADD CONSTRAINT FK_8BFD0BDABB3453DB FOREIGN KEY (work_id) REFERENCES work (id)');
        $this->addSql('ALTER TABLE manga CHANGE release_year release_year INT NOT NULL');
        $this->addSql('ALTER TABLE manga ADD CONSTRAINT FK_765A9E03A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE manga ADD CONSTRAINT FK_765A9E03BB3453DB FOREIGN KEY (work_id) REFERENCES work (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF6C43E79 FOREIGN KEY (user_sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F64482423 FOREIGN KEY (user_receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE movie CHANGE release_year release_year INT NOT NULL');
        $this->addSql('ALTER TABLE movie ADD CONSTRAINT FK_1D5EF26FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE movie ADD CONSTRAINT FK_1D5EF26FBB3453DB FOREIGN KEY (work_id) REFERENCES work (id)');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX idx_search ON news (title)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F246A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F246BB3453DB FOREIGN KEY (work_id) REFERENCES work (id)');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F39A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9794BBE89 FOREIGN KEY (anime_id) REFERENCES anime (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE volume ADD CONSTRAINT FK_B99ACDDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE volume ADD CONSTRAINT FK_B99ACDDE7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id)');
        $this->addSql('ALTER TABLE work ADD CONSTRAINT FK_534E6880A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE work_tag ADD CONSTRAINT FK_79E7E01FBB3453DB FOREIGN KEY (work_id) REFERENCES work (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE work_tag ADD CONSTRAINT FK_79E7E01FBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE work_user ADD CONSTRAINT FK_74506771BB3453DB FOREIGN KEY (work_id) REFERENCES work (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE work_user ADD CONSTRAINT FK_74506771A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE work_news ADD CONSTRAINT FK_E4102868A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE work_news ADD CONSTRAINT FK_E4102868BB3453DB FOREIGN KEY (work_id) REFERENCES work (id)');
        $this->addSql('CREATE INDEX idx_search ON work_news (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plateform (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, url VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, picture_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_search (name), INDEX IDX_E82B067A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE work_plateform (work_id INT NOT NULL, plateform_id INT NOT NULL, INDEX IDX_6C8A1832CCAA542F (plateform_id), INDEX IDX_6C8A1832BB3453DB (work_id), PRIMARY KEY(work_id, plateform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE platform DROP FOREIGN KEY FK_3952D0CBA76ED395');
        $this->addSql('ALTER TABLE work_platform DROP FOREIGN KEY FK_ECE75A97BB3453DB');
        $this->addSql('ALTER TABLE work_platform DROP FOREIGN KEY FK_ECE75A97FFE6496F');
        $this->addSql('DROP TABLE platform');
        $this->addSql('DROP TABLE work_platform');
        $this->addSql('ALTER TABLE anime DROP FOREIGN KEY FK_13045942A76ED395');
        $this->addSql('ALTER TABLE anime DROP FOREIGN KEY FK_13045942BB3453DB');
        $this->addSql('ALTER TABLE anime CHANGE release_year release_year DATETIME NOT NULL');
        $this->addSql('ALTER TABLE ban DROP FOREIGN KEY FK_62FED0E5A76ED395');
        $this->addSql('ALTER TABLE calendar_event DROP FOREIGN KEY FK_57FA09C9A76ED395');
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52EA76ED395');
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52E8FD80EEA');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDAA76ED395');
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDA4EC001D1');
        $this->addSql('ALTER TABLE light_novel DROP FOREIGN KEY FK_8BFD0BDAA76ED395');
        $this->addSql('ALTER TABLE light_novel DROP FOREIGN KEY FK_8BFD0BDABB3453DB');
        $this->addSql('ALTER TABLE light_novel CHANGE release_year release_year DATETIME NOT NULL');
        $this->addSql('ALTER TABLE manga DROP FOREIGN KEY FK_765A9E03A76ED395');
        $this->addSql('ALTER TABLE manga DROP FOREIGN KEY FK_765A9E03BB3453DB');
        $this->addSql('ALTER TABLE manga CHANGE release_year release_year DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF6C43E79');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F64482423');
        $this->addSql('ALTER TABLE movie DROP FOREIGN KEY FK_1D5EF26FA76ED395');
        $this->addSql('ALTER TABLE movie DROP FOREIGN KEY FK_1D5EF26FBB3453DB');
        $this->addSql('ALTER TABLE movie CHANGE release_year release_year DATETIME NOT NULL');
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD39950A76ED395');
        $this->addSql('DROP INDEX idx_search ON news');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY FK_2201F246A76ED395');
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY FK_2201F246BB3453DB');
        $this->addSql('ALTER TABLE rate DROP FOREIGN KEY FK_DFEC3F39A76ED395');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784A76ED395');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA9A76ED395');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA9794BBE89');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783A76ED395');
        $this->addSql('ALTER TABLE volume DROP FOREIGN KEY FK_B99ACDDEA76ED395');
        $this->addSql('ALTER TABLE volume DROP FOREIGN KEY FK_B99ACDDE7B6461');
        $this->addSql('ALTER TABLE work DROP FOREIGN KEY FK_534E6880A76ED395');
        $this->addSql('ALTER TABLE work_news DROP FOREIGN KEY FK_E4102868A76ED395');
        $this->addSql('ALTER TABLE work_news DROP FOREIGN KEY FK_E4102868BB3453DB');
        $this->addSql('DROP INDEX idx_search ON work_news');
        $this->addSql('ALTER TABLE work_tag DROP FOREIGN KEY FK_79E7E01FBB3453DB');
        $this->addSql('ALTER TABLE work_tag DROP FOREIGN KEY FK_79E7E01FBAD26311');
        $this->addSql('ALTER TABLE work_user DROP FOREIGN KEY FK_74506771BB3453DB');
        $this->addSql('ALTER TABLE work_user DROP FOREIGN KEY FK_74506771A76ED395');
    }
}
