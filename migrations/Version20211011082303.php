<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211011082303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE work_time (id INT AUTO_INCREMENT NOT NULL, employer_id INT NOT NULL, horaire_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, heur_debut_travaille TIME DEFAULT NULL, heur_fin_travaille TIME DEFAULT NULL, debut_pause_matinal TIME DEFAULT NULL, fin_pause_matinal TIME DEFAULT NULL, debut_pause_dejeuner TIME DEFAULT NULL, fin_pause_dejeuner TIME DEFAULT NULL, debut_pause_midi TIME DEFAULT NULL, fin_pause_midi TIME DEFAULT NULL, INDEX IDX_9657297D41CD9E7A (employer_id), INDEX IDX_9657297D58C54515 (horaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297D41CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297D58C54515 FOREIGN KEY (horaire_id) REFERENCES horaire (id)');
        $this->addSql('ALTER TABLE pointage ADD work_time_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B208B216519 FOREIGN KEY (work_time_id) REFERENCES work_time (id)');
        $this->addSql('CREATE INDEX IDX_7591B208B216519 ON pointage (work_time_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B208B216519');
        $this->addSql('DROP TABLE work_time');
        $this->addSql('DROP INDEX IDX_7591B208B216519 ON pointage');
        $this->addSql('ALTER TABLE pointage DROP work_time_id');
    }
}
