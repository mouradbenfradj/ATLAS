<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220104133333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dbf (id INT AUTO_INCREMENT NOT NULL, userid DOUBLE PRECISION NOT NULL, badgenumbe INT NOT NULL, ssn VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, autosch VARCHAR(255) DEFAULT NULL, attdate DATE NOT NULL, schid DOUBLE PRECISION DEFAULT NULL, clockintim TIME DEFAULT NULL, clockoutti TIME DEFAULT NULL, starttime TIME DEFAULT NULL, endtime TIME DEFAULT NULL, workday DOUBLE PRECISION DEFAULT NULL, realworkda DOUBLE PRECISION DEFAULT NULL, late TIME DEFAULT NULL, early TIME DEFAULT NULL, absent DOUBLE PRECISION DEFAULT NULL, overtime TIME DEFAULT NULL, worktime TIME DEFAULT NULL, exceptioni VARCHAR(255) DEFAULT NULL, mustin VARCHAR(255) DEFAULT NULL, mustout VARCHAR(255) DEFAULT NULL, deptid DOUBLE PRECISION DEFAULT NULL, sspedaynor DOUBLE PRECISION DEFAULT NULL, sspedaywee DOUBLE PRECISION DEFAULT NULL, sspedayhol DOUBLE PRECISION DEFAULT NULL, atttime TIME DEFAULT NULL, attchktime LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE dbf');
        $this->addSql('DROP TABLE reset_password_request');
    }
}
