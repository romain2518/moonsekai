<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024191316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anime CHANGE release_year release_year DATETIME NOT NULL');
        $this->addSql('ALTER TABLE light_novel CHANGE release_year release_year DATETIME NOT NULL');
        $this->addSql('ALTER TABLE manga CHANGE release_year release_year DATETIME NOT NULL');
        $this->addSql('ALTER TABLE movie CHANGE release_year release_year DATETIME NOT NULL');
        $this->addSql('ALTER TABLE plateform CHANGE url url VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE work CHANGE original_name original_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anime CHANGE release_year release_year INT NOT NULL');
        $this->addSql('ALTER TABLE light_novel CHANGE release_year release_year INT NOT NULL');
        $this->addSql('ALTER TABLE manga CHANGE release_year release_year INT NOT NULL');
        $this->addSql('ALTER TABLE movie CHANGE release_year release_year INT NOT NULL');
        $this->addSql('ALTER TABLE plateform CHANGE url url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE work CHANGE original_name original_name VARCHAR(255) NOT NULL');
    }
}
