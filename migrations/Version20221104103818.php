<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221104103818 extends AbstractMigration
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
        $this->addSql('ALTER TABLE plateform DROP FOREIGN KEY FK_E82B067A76ED395');
        $this->addSql('ALTER TABLE work_plateform DROP FOREIGN KEY FK_6C8A1832BB3453DB');
        $this->addSql('ALTER TABLE work_plateform DROP FOREIGN KEY FK_6C8A1832CCAA542F');
        $this->addSql('DROP TABLE plateform');
        $this->addSql('DROP TABLE work_plateform');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plateform (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, url VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, picture_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX idx_search (name), INDEX IDX_E82B067A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE work_plateform (work_id INT NOT NULL, plateform_id INT NOT NULL, INDEX IDX_6C8A1832CCAA542F (plateform_id), INDEX IDX_6C8A1832BB3453DB (work_id), PRIMARY KEY(work_id, plateform_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE plateform ADD CONSTRAINT FK_E82B067A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE work_plateform ADD CONSTRAINT FK_6C8A1832BB3453DB FOREIGN KEY (work_id) REFERENCES work (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE work_plateform ADD CONSTRAINT FK_6C8A1832CCAA542F FOREIGN KEY (plateform_id) REFERENCES plateform (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE platform DROP FOREIGN KEY FK_3952D0CBA76ED395');
        $this->addSql('ALTER TABLE work_platform DROP FOREIGN KEY FK_ECE75A97BB3453DB');
        $this->addSql('ALTER TABLE work_platform DROP FOREIGN KEY FK_ECE75A97FFE6496F');
        $this->addSql('DROP TABLE platform');
        $this->addSql('DROP TABLE work_platform');
    }
}
