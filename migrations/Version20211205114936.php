<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211205114936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD badgenumbe INT NOT NULL, ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD qualification VARCHAR(255) DEFAULT NULL, ADD matricule INT DEFAULT NULL, ADD debute_a DATE NOT NULL, ADD demission DATE DEFAULT NULL, CHANGE id id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP badgenumbe, DROP first_name, DROP last_name, DROP qualification, DROP matricule, DROP debute_a, DROP demission, CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
