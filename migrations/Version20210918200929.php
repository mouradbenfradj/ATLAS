<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210918200929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pointage (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, entrer TIME DEFAULT NULL, sortie TIME DEFAULT NULL, nbr_heur_travailler TIME DEFAULT NULL, retard_en_minute TIME DEFAULT NULL, depart_anticiper TIME DEFAULT NULL, retard_midi TIME DEFAULT NULL, totale_retard TIME NOT NULL, autorisation_sortie TIME DEFAULT NULL, conger_payer DOUBLE PRECISION DEFAULT NULL, abscence DOUBLE PRECISION DEFAULT NULL, heur_normalement_travailler TIME NOT NULL, diff TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE horaire ADD pointage_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE horaire ADD CONSTRAINT FK_BBC83DB6E58DA11D FOREIGN KEY (pointage_id) REFERENCES pointage (id)');
        $this->addSql('CREATE INDEX IDX_BBC83DB6E58DA11D ON horaire (pointage_id)');
        $this->addSql('ALTER TABLE user ADD pointage_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649E58DA11D FOREIGN KEY (pointage_id) REFERENCES pointage (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649E58DA11D ON user (pointage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE horaire DROP FOREIGN KEY FK_BBC83DB6E58DA11D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E58DA11D');
        $this->addSql('DROP TABLE pointage');
        $this->addSql('DROP INDEX IDX_BBC83DB6E58DA11D ON horaire');
        $this->addSql('ALTER TABLE horaire DROP pointage_id');
        $this->addSql('DROP INDEX IDX_8D93D649E58DA11D ON user');
        $this->addSql('ALTER TABLE user DROP pointage_id');
    }
}
