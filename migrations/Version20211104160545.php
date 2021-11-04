<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211104160545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abscence (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, debut DATE NOT NULL, fin DATE NOT NULL, INDEX IDX_BD71CDA41CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abscence ADD CONSTRAINT FK_BD71CDA41CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pointage ADD abscence_id INT DEFAULT NULL, DROP abscence');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B20189F6FF7 FOREIGN KEY (abscence_id) REFERENCES abscence (id)');
        $this->addSql('CREATE INDEX IDX_7591B20189F6FF7 ON pointage (abscence_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B20189F6FF7');
        $this->addSql('DROP TABLE abscence');
        $this->addSql('DROP INDEX IDX_7591B20189F6FF7 ON pointage');
        $this->addSql('ALTER TABLE pointage ADD abscence DOUBLE PRECISION DEFAULT NULL, DROP abscence_id');
    }
}
