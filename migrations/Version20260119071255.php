<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119071255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE availability (id INT AUTO_INCREMENT NOT NULL, candidate_id INT NOT NULL, day_id INT NOT NULL, start_time_id INT NOT NULL, end_time_id INT NOT NULL, INDEX IDX_3FB7A2BF91BD8781 (candidate_id), INDEX IDX_3FB7A2BF9C24126 (day_id), INDEX IDX_3FB7A2BFAE2F81E8 (start_time_id), INDEX IDX_3FB7A2BFCB258CD2 (end_time_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidate (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidate_skills (candidate_id INT NOT NULL, skills_id INT NOT NULL, INDEX IDX_610248AC91BD8781 (candidate_id), INDEX IDX_610248AC7FF61858 (skills_id), PRIMARY KEY(candidate_id, skills_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidate_intervention_area (candidate_id INT NOT NULL, intervention_area_id INT NOT NULL, INDEX IDX_3FB3CD6791BD8781 (candidate_id), INDEX IDX_3FB3CD67F6E134F9 (intervention_area_id), PRIMARY KEY(candidate_id, intervention_area_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE day (id INT AUTO_INCREMENT NOT NULL, day VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_E5A02990E5A02990 (day), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employer (id INT NOT NULL, company_name VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intervention_area (id INT AUTO_INCREMENT NOT NULL, city VARCHAR(100) NOT NULL, postal_code VARCHAR(5) NOT NULL, UNIQUE INDEX UNIQ_D82B25AA2D5B0234 (city), UNIQUE INDEX UNIQ_D82B25AAEA98E376 (postal_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, recipent_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B6BD307FF675F31B (author_id), INDEX IDX_B6BD307FBF433F1C (recipent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mission (id INT AUTO_INCREMENT NOT NULL, wage_scale_id INT DEFAULT NULL, area_location_id INT DEFAULT NULL, employer_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9067F23CADD43BA (wage_scale_id), INDEX IDX_9067F23C2D768BFE (area_location_id), INDEX IDX_9067F23C41CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mission_skills (mission_id INT NOT NULL, skills_id INT NOT NULL, INDEX IDX_8DC9CC3EBE6CAE90 (mission_id), INDEX IDX_8DC9CC3E7FF61858 (skills_id), PRIMARY KEY(mission_id, skills_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name_role VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill_category (id INT AUTO_INCREMENT NOT NULL, name_category VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_44E474332A9DEC0F (name_category), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skills (id INT AUTO_INCREMENT NOT NULL, skill_category_id INT NOT NULL, name_skill VARCHAR(100) NOT NULL, INDEX IDX_D5311670AC58042E (skill_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE time (id INT AUTO_INCREMENT NOT NULL, hour VARCHAR(5) NOT NULL, UNIQUE INDEX UNIQ_6F949845701E114E (hour), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, email VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, lastname VARCHAR(100) DEFAULT NULL, firstname VARCHAR(100) DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, user_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6496B01BC5B (phone_number), INDEX IDX_8D93D649D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wage_scale (id INT AUTO_INCREMENT NOT NULL, niveau VARCHAR(100) NOT NULL, level INT NOT NULL, hourly_rate NUMERIC(5, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BF91BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BF9C24126 FOREIGN KEY (day_id) REFERENCES day (id)');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BFAE2F81E8 FOREIGN KEY (start_time_id) REFERENCES time (id)');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BFCB258CD2 FOREIGN KEY (end_time_id) REFERENCES time (id)');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E44BF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_skills ADD CONSTRAINT FK_610248AC91BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_skills ADD CONSTRAINT FK_610248AC7FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_intervention_area ADD CONSTRAINT FK_3FB3CD6791BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_intervention_area ADD CONSTRAINT FK_3FB3CD67F6E134F9 FOREIGN KEY (intervention_area_id) REFERENCES intervention_area (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employer ADD CONSTRAINT FK_DE4CF066BF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FBF433F1C FOREIGN KEY (recipent_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23CADD43BA FOREIGN KEY (wage_scale_id) REFERENCES wage_scale (id)');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23C2D768BFE FOREIGN KEY (area_location_id) REFERENCES intervention_area (id)');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23C41CD9E7A FOREIGN KEY (employer_id) REFERENCES employer (id)');
        $this->addSql('ALTER TABLE mission_skills ADD CONSTRAINT FK_8DC9CC3EBE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mission_skills ADD CONSTRAINT FK_8DC9CC3E7FF61858 FOREIGN KEY (skills_id) REFERENCES skills (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skills ADD CONSTRAINT FK_D5311670AC58042E FOREIGN KEY (skill_category_id) REFERENCES skill_category (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BF91BD8781');
        $this->addSql('ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BF9C24126');
        $this->addSql('ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BFAE2F81E8');
        $this->addSql('ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BFCB258CD2');
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E44BF396750');
        $this->addSql('ALTER TABLE candidate_skills DROP FOREIGN KEY FK_610248AC91BD8781');
        $this->addSql('ALTER TABLE candidate_skills DROP FOREIGN KEY FK_610248AC7FF61858');
        $this->addSql('ALTER TABLE candidate_intervention_area DROP FOREIGN KEY FK_3FB3CD6791BD8781');
        $this->addSql('ALTER TABLE candidate_intervention_area DROP FOREIGN KEY FK_3FB3CD67F6E134F9');
        $this->addSql('ALTER TABLE employer DROP FOREIGN KEY FK_DE4CF066BF396750');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF675F31B');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FBF433F1C');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23CADD43BA');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23C2D768BFE');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23C41CD9E7A');
        $this->addSql('ALTER TABLE mission_skills DROP FOREIGN KEY FK_8DC9CC3EBE6CAE90');
        $this->addSql('ALTER TABLE mission_skills DROP FOREIGN KEY FK_8DC9CC3E7FF61858');
        $this->addSql('ALTER TABLE skills DROP FOREIGN KEY FK_D5311670AC58042E');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('DROP TABLE availability');
        $this->addSql('DROP TABLE candidate');
        $this->addSql('DROP TABLE candidate_skills');
        $this->addSql('DROP TABLE candidate_intervention_area');
        $this->addSql('DROP TABLE day');
        $this->addSql('DROP TABLE employer');
        $this->addSql('DROP TABLE intervention_area');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE mission');
        $this->addSql('DROP TABLE mission_skills');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE skill_category');
        $this->addSql('DROP TABLE skills');
        $this->addSql('DROP TABLE time');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE wage_scale');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
