<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251231111000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mission_skills (mission_id INT NOT NULL, skills_id INT NOT NULL, INDEX IDX_8DC9CC3EBE6CAE90 (mission_id), INDEX IDX_8DC9CC3E7FF61858 (skills_id), PRIMARY KEY(mission_id, skills_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mission_skills ADD CONSTRAINT FK_8DC9CC3EBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mission_skills ADD CONSTRAINT FK_8DC9CC3E7FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employer CHANGE company_name company_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mission_skills DROP FOREIGN KEY FK_8DC9CC3EBE6CAE90');
        $this->addSql('ALTER TABLE mission_skills DROP FOREIGN KEY FK_8DC9CC3E7FF61858');
        $this->addSql('DROP TABLE mission_skills');
        $this->addSql('ALTER TABLE employer CHANGE company_name company_name VARCHAR(100) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
