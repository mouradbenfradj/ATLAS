<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211201095952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence RENAME INDEX idx_bd71cda41cd9e7a TO IDX_765AE0C941CD9E7A');
        $this->addSql('ALTER TABLE pointage RENAME INDEX idx_7591b20189f6ff7 TO IDX_7591B202DFF238F');
        $this->addSql('ALTER TABLE xlsx ADD employer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE xlsx ADD CONSTRAINT FK_94CEB61041CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_94CEB61041CD9E7A ON xlsx (employer_id)');
        $this->addSql('ALTER TABLE xlsx RENAME INDEX idx_94ceb610514412a4 TO IDX_94CEB6102DFF238F');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence RENAME INDEX idx_765ae0c941cd9e7a TO IDX_BD71CDA41CD9E7A');
        $this->addSql('ALTER TABLE pointage RENAME INDEX idx_7591b202dff238f TO IDX_7591B20189F6FF7');
        $this->addSql('ALTER TABLE xlsx DROP FOREIGN KEY FK_94CEB61041CD9E7A');
        $this->addSql('DROP INDEX IDX_94CEB61041CD9E7A ON xlsx');
        $this->addSql('ALTER TABLE xlsx DROP employer_id');
        $this->addSql('ALTER TABLE xlsx RENAME INDEX idx_94ceb6102dff238f TO IDX_94CEB610514412A4');
    }
}
