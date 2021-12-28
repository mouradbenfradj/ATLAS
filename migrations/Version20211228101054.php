<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211228101054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dbf (id INT AUTO_INCREMENT NOT NULL, employer_id INT NOT NULL, userid DOUBLE PRECISION NOT NULL, badgenumbe INT NOT NULL, ssn VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, autosch VARCHAR(255) DEFAULT NULL, attdate DATE NOT NULL, schid DOUBLE PRECISION DEFAULT NULL, clockintim TIME DEFAULT NULL, clockoutti TIME DEFAULT NULL, starttime TIME DEFAULT NULL, endtime TIME DEFAULT NULL, workday DOUBLE PRECISION DEFAULT NULL, realworkda DOUBLE PRECISION DEFAULT NULL, late TIME DEFAULT NULL, early TIME DEFAULT NULL, absent DOUBLE PRECISION DEFAULT NULL, overtime TIME DEFAULT NULL, worktime TIME DEFAULT NULL, exceptioni VARCHAR(255) DEFAULT NULL, mustin VARCHAR(255) DEFAULT NULL, mustout VARCHAR(255) DEFAULT NULL, deptid DOUBLE PRECISION DEFAULT NULL, sspedaynor DOUBLE PRECISION DEFAULT NULL, sspedaywee DOUBLE PRECISION DEFAULT NULL, sspedayhol DOUBLE PRECISION DEFAULT NULL, atttime TIME DEFAULT NULL, attchktime LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_438577A641CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, badgenumbe INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, qualification VARCHAR(255) DEFAULT NULL, matricule INT DEFAULT NULL, debut_travaille DATE NOT NULL, demission DATE DEFAULT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dbf ADD CONSTRAINT FK_438577A641CD9E7A FOREIGN KEY (employer_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dbf DROP FOREIGN KEY FK_438577A641CD9E7A');
        $this->addSql('DROP TABLE dbf');
        $this->addSql('DROP TABLE `user`');
    }
}
