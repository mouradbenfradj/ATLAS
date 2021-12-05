<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211205191924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE absence (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, debut DATE NOT NULL, fin DATE NOT NULL, INDEX IDX_765AE0C941CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE autorisation_sortie (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, date_autorisation DATE NOT NULL, valider TINYINT(1) DEFAULT NULL, refuser TINYINT(1) DEFAULT NULL, heur_autoriser TIME NOT NULL, INDEX IDX_AEA917E441CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE config (id INT AUTO_INCREMENT NOT NULL, debut_sold_conger DOUBLE PRECISION NOT NULL, inc_sold_conger DOUBLE PRECISION NOT NULL, debut_sold_as TIME NOT NULL, inc_autorisation_sortie TIME NOT NULL, reinitialisation_c TINYINT(1) NOT NULL, reinitialisation_as TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conger (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, debut DATE NOT NULL, fin DATE NOT NULL, type VARCHAR(255) NOT NULL, valider TINYINT(1) DEFAULT NULL, refuser TINYINT(1) DEFAULT NULL, demi_journer TINYINT(1) DEFAULT NULL, INDEX IDX_1420EEAC41CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dbf (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, userid DOUBLE PRECISION NOT NULL, badgenumbe INT NOT NULL, ssn VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, autosch VARCHAR(255) DEFAULT NULL, attdate DATE NOT NULL, schid DOUBLE PRECISION DEFAULT NULL, clockintim TIME DEFAULT NULL, clockoutti TIME DEFAULT NULL, starttime TIME DEFAULT NULL, endtime TIME DEFAULT NULL, workday DOUBLE PRECISION DEFAULT NULL, realworkda DOUBLE PRECISION DEFAULT NULL, late TIME DEFAULT NULL, early TIME DEFAULT NULL, absent DOUBLE PRECISION DEFAULT NULL, overtime TIME DEFAULT NULL, worktime TIME DEFAULT NULL, exceptioni VARCHAR(255) DEFAULT NULL, mustin VARCHAR(255) DEFAULT NULL, mustout VARCHAR(255) DEFAULT NULL, deptid DOUBLE PRECISION DEFAULT NULL, sspedaynor DOUBLE PRECISION DEFAULT NULL, sspedaywee DOUBLE PRECISION DEFAULT NULL, sspedayhol DOUBLE PRECISION DEFAULT NULL, atttime TIME DEFAULT NULL, attchktime LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_438577A641CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE horaire (id INT AUTO_INCREMENT NOT NULL, horaire VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, heur_debut_travaille TIME NOT NULL, heur_fin_travaille TIME NOT NULL, debut_pause_matinal TIME NOT NULL, fin_pause_matinal TIME NOT NULL, debut_pause_dejeuner TIME NOT NULL, fin_pause_dejeuner TIME NOT NULL, debut_pause_midi TIME NOT NULL, fin_pause_midi TIME NOT NULL, marge_du_retard TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jour_ferier (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, debut DATE NOT NULL, fin DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pointage (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, horaire_id INT DEFAULT NULL, conger_payer_id INT DEFAULT NULL, autorisation_sortie_id INT DEFAULT NULL, work_time_id INT DEFAULT NULL, absence_id INT DEFAULT NULL, date DATE NOT NULL, entrer TIME DEFAULT NULL, sortie TIME DEFAULT NULL, nbr_heur_travailler TIME DEFAULT NULL, retard_en_minute TIME DEFAULT NULL, depart_anticiper TIME DEFAULT NULL, retard_midi TIME DEFAULT NULL, totale_retard TIME NOT NULL, heur_normalement_travailler TIME NOT NULL, diff TIME NOT NULL, INDEX IDX_7591B2041CD9E7A (employer_id), INDEX IDX_7591B2058C54515 (horaire_id), INDEX IDX_7591B202C8AA373 (conger_payer_id), INDEX IDX_7591B2062232238 (autorisation_sortie_id), INDEX IDX_7591B208B216519 (work_time_id), INDEX IDX_7591B202DFF238F (absence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, badgenumbe INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, qualification VARCHAR(255) DEFAULT NULL, matricule INT DEFAULT NULL, debut_travaille DATE NOT NULL, demission DATE DEFAULT NULL, sold_autorisation_sortie TIME NOT NULL, sold_conger DOUBLE PRECISION NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_time (id INT AUTO_INCREMENT NOT NULL, employer_id INT NOT NULL, horaire_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, heur_debut_travaille TIME DEFAULT NULL, heur_fin_travaille TIME DEFAULT NULL, debut_pause_matinal TIME DEFAULT NULL, fin_pause_matinal TIME DEFAULT NULL, debut_pause_dejeuner TIME DEFAULT NULL, fin_pause_dejeuner TIME DEFAULT NULL, debut_pause_midi TIME DEFAULT NULL, fin_pause_midi TIME DEFAULT NULL, INDEX IDX_9657297D41CD9E7A (employer_id), INDEX IDX_9657297D58C54515 (horaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE xlsx (id INT AUTO_INCREMENT NOT NULL, horaire_id INT NOT NULL, autorisation_sortie_id INT DEFAULT NULL, conger_payer_id INT DEFAULT NULL, absence_id INT DEFAULT NULL, employer_id INT DEFAULT NULL, date DATE NOT NULL, entrer TIME DEFAULT NULL, sortie TIME DEFAULT NULL, nbr_heurs_travailler TIME DEFAULT NULL, retard_en_minute TIME DEFAULT NULL, depart_anticiper TIME DEFAULT NULL, retard_midi TIME DEFAULT NULL, total_retard TIME DEFAULT NULL, heur_normalement_travailler TIME DEFAULT NULL, diff TIME DEFAULT NULL, INDEX IDX_94CEB61058C54515 (horaire_id), INDEX IDX_94CEB61062232238 (autorisation_sortie_id), INDEX IDX_94CEB6102C8AA373 (conger_payer_id), INDEX IDX_94CEB6102DFF238F (absence_id), INDEX IDX_94CEB61041CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C941CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE autorisation_sortie ADD CONSTRAINT FK_AEA917E441CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE conger ADD CONSTRAINT FK_1420EEAC41CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dbf ADD CONSTRAINT FK_438577A641CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B2041CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B2058C54515 FOREIGN KEY (horaire_id) REFERENCES horaire (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B202C8AA373 FOREIGN KEY (conger_payer_id) REFERENCES conger (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B2062232238 FOREIGN KEY (autorisation_sortie_id) REFERENCES autorisation_sortie (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B208B216519 FOREIGN KEY (work_time_id) REFERENCES work_time (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B202DFF238F FOREIGN KEY (absence_id) REFERENCES absence (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297D41CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297D58C54515 FOREIGN KEY (horaire_id) REFERENCES horaire (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB61058C54515 FOREIGN KEY (horaire_id) REFERENCES horaire (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB61062232238 FOREIGN KEY (autorisation_sortie_id) REFERENCES autorisation_sortie (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB6102C8AA373 FOREIGN KEY (conger_payer_id) REFERENCES conger (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB6102DFF238F FOREIGN KEY (absence_id) REFERENCES absence (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB61041CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B202DFF238F');
        $this->addSql('ALTER TABLE xlsx DROP FOREIGN KEY FK_94CEB6102DFF238F');
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B2062232238');
        $this->addSql('ALTER TABLE xlsx DROP FOREIGN KEY FK_94CEB61062232238');
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B202C8AA373');
        $this->addSql('ALTER TABLE xlsx DROP FOREIGN KEY FK_94CEB6102C8AA373');
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B2058C54515');
        $this->addSql('ALTER TABLE work_time DROP FOREIGN KEY FK_9657297D58C54515');
        $this->addSql('ALTER TABLE xlsx DROP FOREIGN KEY FK_94CEB61058C54515');
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C941CD9E7A');
        $this->addSql('ALTER TABLE autorisation_sortie DROP FOREIGN KEY FK_AEA917E441CD9E7A');
        $this->addSql('ALTER TABLE conger DROP FOREIGN KEY FK_1420EEAC41CD9E7A');
        $this->addSql('ALTER TABLE dbf DROP FOREIGN KEY FK_438577A641CD9E7A');
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B2041CD9E7A');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE work_time DROP FOREIGN KEY FK_9657297D41CD9E7A');
        $this->addSql('ALTER TABLE xlsx DROP FOREIGN KEY FK_94CEB61041CD9E7A');
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B208B216519');
        $this->addSql('DROP TABLE absence');
        $this->addSql('DROP TABLE autorisation_sortie');
        $this->addSql('DROP TABLE config');
        $this->addSql('DROP TABLE conger');
        $this->addSql('DROP TABLE dbf');
        $this->addSql('DROP TABLE horaire');
        $this->addSql('DROP TABLE jour_ferier');
        $this->addSql('DROP TABLE pointage');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE work_time');
        $this->addSql('DROP TABLE xlsx');
    }
}
