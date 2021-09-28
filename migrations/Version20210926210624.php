<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210926210624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE autorisation_sortie (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, date_autorisation DATE NOT NULL, time TIME NOT NULL, INDEX IDX_AEA917E441CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE autorisation_sortie ADD CONSTRAINT FK_AEA917E441CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pointage ADD autorisation_sortie_id INT DEFAULT NULL, DROP autorisation_sortie');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B2062232238 FOREIGN KEY (autorisation_sortie_id) REFERENCES autorisation_sortie (id)');
        $this->addSql('CREATE INDEX IDX_7591B2062232238 ON pointage (autorisation_sortie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B2062232238');
        $this->addSql('DROP TABLE autorisation_sortie');
        $this->addSql('DROP INDEX IDX_7591B2062232238 ON pointage');
        $this->addSql('ALTER TABLE pointage ADD autorisation_sortie TIME DEFAULT NULL, DROP autorisation_sortie_id');
    }
}
