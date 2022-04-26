<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220207135352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la table constructeur et des champs nom constructeur et version api';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE constructeur (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', constructeur_name VARCHAR(255) NOT NULL, version_api VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_71A7BD9E683B0279B6F0F834 (constructeur_name, version_api), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE station ADD constructeur_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B18815B605 FOREIGN KEY (constructeur_id) REFERENCES constructeur (id)');
        $this->addSql('CREATE INDEX IDX_9F39F8B18815B605 ON station (constructeur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B18815B605');
        $this->addSql('DROP TABLE constructeur');
        $this->addSql('DROP INDEX IDX_9F39F8B18815B605 ON station');
        $this->addSql('ALTER TABLE station DROP constructeur_id');
    }
}
