<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220420132100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE verger ADD message_alerte_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE verger ADD CONSTRAINT FK_17448D024622A01F FOREIGN KEY (message_alerte_id) REFERENCES message_alerte (id) ON UPDATE CASCADE ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_17448D024622A01F ON verger (message_alerte_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE verger DROP FOREIGN KEY FK_17448D024622A01F');
        $this->addSql('DROP INDEX IDX_17448D024622A01F ON verger');
        $this->addSql('ALTER TABLE verger DROP message_alerte_id');
    }
}
