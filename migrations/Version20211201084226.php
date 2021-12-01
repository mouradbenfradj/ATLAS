<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211201084226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE xlsx (id INT AUTO_INCREMENT NOT NULL, horaire_id INT NOT NULL, autorisation_sortie_id INT DEFAULT NULL, conger_payer_id INT DEFAULT NULL, absence_id INT DEFAULT NULL, date DATE NOT NULL, entrer TIME DEFAULT NULL, sortie TIME DEFAULT NULL, nbr_heurs_travailler TIME DEFAULT NULL, retard_en_minute TIME DEFAULT NULL, depart_anticiper TIME DEFAULT NULL, retard_midi TIME DEFAULT NULL, total_retard TIME DEFAULT NULL, heur_normalement_travailler TIME DEFAULT NULL, diff TIME DEFAULT NULL, INDEX IDX_94CEB61058C54515 (horaire_id), INDEX IDX_94CEB61062232238 (autorisation_sortie_id), INDEX IDX_94CEB6102C8AA373 (conger_payer_id), INDEX IDX_94CEB610514412A4 (absence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB61058C54515 FOREIGN KEY (horaire_id) REFERENCES horaire (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB61062232238 FOREIGN KEY (autorisation_sortie_id) REFERENCES autorisation_sortie (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB6102C8AA373 FOREIGN KEY (conger_payer_id) REFERENCES conger (id)');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB610514412A4 FOREIGN KEY (absence_id) REFERENCES absence (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE xlsx');
    }
}
