<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210920143610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE horaire (id INT AUTO_INCREMENT NOT NULL, horaire VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, heur_debut_travaille TIME NOT NULL, heur_fin_travaille TIME NOT NULL, debut_pause_matinal TIME NOT NULL, fin_pause_matinal TIME NOT NULL, debut_pause_dejeuner TIME NOT NULL, fin_pause_dejeuner TIME NOT NULL, debut_pause_midi TIME NOT NULL, fin_pause_midi TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jour_ferier (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, debut DATE NOT NULL, fin DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pointage (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, horaire_id INT DEFAULT NULL, date DATE NOT NULL, entrer TIME DEFAULT NULL, sortie TIME DEFAULT NULL, nbr_heur_travailler TIME DEFAULT NULL, retard_en_minute TIME DEFAULT NULL, depart_anticiper TIME DEFAULT NULL, retard_midi TIME DEFAULT NULL, totale_retard TIME NOT NULL, autorisation_sortie TIME DEFAULT NULL, conger_payer DOUBLE PRECISION DEFAULT NULL, abscence DOUBLE PRECISION DEFAULT NULL, heur_normalement_travailler TIME NOT NULL, diff TIME NOT NULL, INDEX IDX_7591B2041CD9E7A (employer_id), INDEX IDX_7591B2058C54515 (horaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, user_id INT DEFAULT NULL, badgenumbe INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, qualification VARCHAR(255) DEFAULT NULL, matricule INT DEFAULT NULL, debut_travaille DATE NOT NULL, demission DATE DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B2041CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B2058C54515 FOREIGN KEY (horaire_id) REFERENCES horaire (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B2058C54515');
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B2041CD9E7A');
        $this->addSql('DROP TABLE horaire');
        $this->addSql('DROP TABLE jour_ferier');
        $this->addSql('DROP TABLE pointage');
        $this->addSql('DROP TABLE user');
    }
}
