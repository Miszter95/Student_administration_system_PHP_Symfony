<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210814023114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, sex LONGTEXT NOT NULL, place_of_birth LONGTEXT NOT NULL, date_of_birth DATE NOT NULL, email_address LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE students_study_groups (student_id INT NOT NULL, study_group_id INT NOT NULL, INDEX IDX_B86ED29ACB944F1A (student_id), INDEX IDX_B86ED29A5DDDCCCE (study_group_id), PRIMARY KEY(student_id, study_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study_group (id INT AUTO_INCREMENT NOT NULL, groupname LONGTEXT NOT NULL, groupleader VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, datetime_of_sgroup DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE students_study_groups ADD CONSTRAINT FK_B86ED29ACB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE students_study_groups ADD CONSTRAINT FK_B86ED29A5DDDCCCE FOREIGN KEY (study_group_id) REFERENCES study_group (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE students_study_groups DROP FOREIGN KEY FK_B86ED29ACB944F1A');
        $this->addSql('ALTER TABLE students_study_groups DROP FOREIGN KEY FK_B86ED29A5DDDCCCE');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE students_study_groups');
        $this->addSql('DROP TABLE study_group');
    }
}
