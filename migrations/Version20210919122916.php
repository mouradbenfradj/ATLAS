<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210919122916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD user_id INT DEFAULT NULL, ADD badgenumbe INT NOT NULL, ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD qualification VARCHAR(255) DEFAULT NULL, ADD matricule INT DEFAULT NULL, ADD debut_travaille DATE NOT NULL, ADD demission DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP user_id, DROP badgenumbe, DROP first_name, DROP last_name, DROP qualification, DROP matricule, DROP debut_travaille, DROP demission');
    }
}
