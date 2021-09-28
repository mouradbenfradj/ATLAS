<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210926204754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pointage ADD conger_payer_id INT DEFAULT NULL, DROP conger_payer');
        $this->addSql('ALTER TABLE pointage ADD CONSTRAINT FK_7591B202C8AA373 FOREIGN KEY (conger_payer_id) REFERENCES conger (id)');
        $this->addSql('CREATE INDEX IDX_7591B202C8AA373 ON pointage (conger_payer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pointage DROP FOREIGN KEY FK_7591B202C8AA373');
        $this->addSql('DROP INDEX IDX_7591B202C8AA373 ON pointage');
        $this->addSql('ALTER TABLE pointage ADD conger_payer DOUBLE PRECISION DEFAULT NULL, DROP conger_payer_id');
    }
}
