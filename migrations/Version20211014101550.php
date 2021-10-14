<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211014101550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE autorisation_sortie ADD valider TINYINT(1) DEFAULT NULL, ADD refuser TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE conger CHANGE demi_journer demi_journer TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE autorisation_sortie DROP valider, DROP refuser');
        $this->addSql('ALTER TABLE conger CHANGE demi_journer demi_journer TINYINT(1) NOT NULL');
    }
}
