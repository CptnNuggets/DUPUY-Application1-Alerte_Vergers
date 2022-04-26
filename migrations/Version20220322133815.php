<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220322133815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capteur_pour_maths (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', capteur_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', nom_raccourci VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_83255FB8EE9AE881 (nom_raccourci), UNIQUE INDEX UNIQ_83255FB81708A229 (capteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE capteur_pour_maths ADD CONSTRAINT FK_83255FB81708A229 FOREIGN KEY (capteur_id) REFERENCES capteur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE capteur_pour_maths');
    }
}
