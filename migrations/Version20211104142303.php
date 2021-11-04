<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211104142303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dbf ADD employer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dbf ADD CONSTRAINT FK_438577A641CD9E7A FOREIGN KEY (employer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_438577A641CD9E7A ON dbf (employer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dbf DROP FOREIGN KEY FK_438577A641CD9E7A');
        $this->addSql('DROP INDEX IDX_438577A641CD9E7A ON dbf');
        $this->addSql('ALTER TABLE dbf DROP employer_id');
    }
}
