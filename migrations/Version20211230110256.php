<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211230110256 extends AbstractMigration
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
        $this->addSql('CREATE TABLE conger (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, debut DATE NOT NULL, fin DATE NOT NULL, type VARCHAR(255) NOT NULL, valider TINYINT(1) DEFAULT NULL, refuser TINYINT(1) DEFAULT NULL, demi_journer TINYINT(1) DEFAULT NULL, INDEX IDX_1420EEAC41CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE horaire (id INT AUTO_INCREMENT NOT NULL, horaire VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, heur_debut_travaille TIME NOT NULL, heur_fin_travaille TIME NOT NULL, debut_pause_matinal TIME NOT NULL, fin_pause_matinal TIME NOT NULL, debut_pause_dejeuner TIME NOT NULL, fin_pause_dejeuner TIME NOT NULL, debut_pause_midi TIME NOT NULL, fin_pause_midi TIME NOT NULL, marge_du_retard TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jour_ferier (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, debut DATE NOT NULL, fin DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pointage (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, horaire_id INT DEFAULT NULL, conger_payer_id INT DEFAULT NULL, autorisation_sortie_id INT DEFAULT NULL, absence_id INT DEFAULT NULL, date DATE NOT NULL, entrer TIME DEFAULT NULL, sortie TIME DEFAULT NULL, nbr_heur_travailler TIME DEFAULT NULL, retard_en_minute TIME DEFAULT NULL, depart_anticiper TIME DEFAULT NULL, retard_midi TIME DEFAULT NULL, totale_retard TIME NOT NULL, heur_normalement_travailler TIME NOT NULL, diff TIME NOT NULL, INDEX IDX_7591B2041CD9E7A (employer_id), INDEX IDX_7591B2058C54515 (horaire_id), INDEX IDX_7591B202C8AA373 (conger_payer_id), INDEX IDX_7591B2062232238 (autorisation_sortie_id), INDEX IDX_7591B202DFF238F (absence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE xlsx (id INT AUTO_INCREMENT NOT NULL, horaire_id INT NOT NULL, autorisation_sortie_id INT DEFAULT NULL, conger_payer_id INT DEFAULT NULL, absence_id INT DEFAULT NULL, employer_id INT DEFAULT NULL, date DATE NOT NULL, entrer TIME DEFAULT NULL, sortie TIME DEFAULT NULL, retard_en_minute TIME DEFAULT NULL, depart_anticiper TIME DEFAULT NULL, retard_midi TIME DEFAULT NULL, total_retard TIME DEFAULT NULL, heur_normalement_travailler TIME DEFAULT NULL, diff TIME DEFAULT NULL, nbr_heur_travailler TIME DEFAULT NULL, INDEX IDX_94CEB61058C54515 (horaire_id), INDEX IDX_94CEB61062232238 (autorisation_sortie_id), INDEX IDX_94CEB6102C8AA373 (conger_payer_id), INDEX IDX_94CEB6102DFF238F (absence_id), INDEX IDX_94CEB61041CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C941CD9E7A FOREIGN KEY (employer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE autorisation_sortie ADD CONSTRAINT FK_AEA917E441CD9E7A FOREIGN KEY (employer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE conger ADD CONSTRAINT FK_1420EEAC41CD9E7A FOREIGN KEY (employer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B2041CD9E7A FOREIGN KEY (employer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B2058C54515 FOREIGN KEY (horaire_id) REFERENCES horaire (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B202C8AA373 FOREIGN KEY (conger_payer_id) REFERENCES conger (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B2062232238 FOREIGN KEY (autorisation_sortie_id) REFERENCES autorisation_sortie (id)');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B202DFF238F FOREIGN KEY (absence_id) REFERENCES absence (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB61058C54515 FOREIGN KEY (horaire_id) REFERENCES horaire (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB61062232238 FOREIGN KEY (autorisation_sortie_id) REFERENCES autorisation_sortie (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB6102C8AA373 FOREIGN KEY (conger_payer_id) REFERENCES conger (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB6102DFF238F FOREIGN KEY (absence_id) REFERENCES absence (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB61041CD9E7A FOREIGN KEY (employer_id) REFERENCES `user` (id)');
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
        $this->addSql('ALTER TABLE xlsx DROP FOREIGN KEY FK_94CEB61058C54515');
        $this->addSql('DROP TABLE absence');
        $this->addSql('DROP TABLE autorisation_sortie');
        $this->addSql('DROP TABLE conger');
        $this->addSql('DROP TABLE horaire');
        $this->addSql('DROP TABLE jour_ferier');
        $this->addSql('DROP TABLE pointage');
        $this->addSql('DROP TABLE xlsx');
    }
}
