<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210920001748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pointage ADD employer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B2041CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7591B2041CD9E7A ON pointage (employer_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E58DA11D');
        $this->addSql('DROP INDEX IDX_8D93D649E58DA11D ON user');
        $this->addSql('ALTER TABLE user DROP pointage_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B2041CD9E7A');
        $this->addSql('DROP INDEX IDX_7591B2041CD9E7A ON pointage');
        $this->addSql('ALTER TABLE pointage DROP employer_id');
        $this->addSql('ALTER TABLE user ADD pointage_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649E58DA11D FOREIGN KEY (pointage_id) REFERENCES pointage (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649E58DA11D ON user (pointage_id)');
    }
}
