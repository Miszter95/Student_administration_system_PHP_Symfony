<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210818073914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student CHANGE dateofbirth date_of_birth DATE NOT NULL, CHANGE emailaddress email_address LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE study_group CHANGE datetime_of_sgroup datetime_of_study_group DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student CHANGE date_of_birth dateofbirth DATE NOT NULL, CHANGE email_address emailaddress LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE study_group CHANGE datetime_of_study_group datetime_of_sgroup DATETIME NOT NULL');
    }
}
