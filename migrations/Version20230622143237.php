<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230622143237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, phase_id INT DEFAULT NULL, answer LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', points INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DADD4A25A76ED395 (user_id), INDEX IDX_DADD4A2599091188 (phase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meeting (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, programmed_date DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phase (id INT AUTO_INCREMENT NOT NULL, scenary_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(1000) NOT NULL, num_phase INT NOT NULL, type VARCHAR(255) NOT NULL, correct_answer LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_B1BDD6CB1715EE8F (scenary_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scenary (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, num_scenary INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE used_tracks (id INT AUTO_INCREMENT NOT NULL, phase_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FBEB09BF99091188 (phase_id), INDEX IDX_FBEB09BFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A2599091188 FOREIGN KEY (phase_id) REFERENCES phase (id)');
        $this->addSql('ALTER TABLE phase ADD CONSTRAINT FK_B1BDD6CB1715EE8F FOREIGN KEY (scenary_id) REFERENCES scenary (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE used_tracks ADD CONSTRAINT FK_FBEB09BF99091188 FOREIGN KEY (phase_id) REFERENCES phase (id)');
        $this->addSql('ALTER TABLE used_tracks ADD CONSTRAINT FK_FBEB09BFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user DROP final_time_1, DROP final_time_2, DROP final_time_3, DROP final_time_4, DROP final_time_5');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25A76ED395');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A2599091188');
        $this->addSql('ALTER TABLE phase DROP FOREIGN KEY FK_B1BDD6CB1715EE8F');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE used_tracks DROP FOREIGN KEY FK_FBEB09BF99091188');
        $this->addSql('ALTER TABLE used_tracks DROP FOREIGN KEY FK_FBEB09BFA76ED395');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE meeting');
        $this->addSql('DROP TABLE phase');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE scenary');
        $this->addSql('DROP TABLE used_tracks');
        $this->addSql('ALTER TABLE user ADD final_time_1 BIGINT NOT NULL, ADD final_time_2 BIGINT NOT NULL, ADD final_time_3 BIGINT NOT NULL, ADD final_time_4 BIGINT NOT NULL, ADD final_time_5 BIGINT NOT NULL');
    }
}
