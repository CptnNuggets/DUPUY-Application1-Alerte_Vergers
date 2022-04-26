<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220421081855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_alerte CHANGE alerte_level alerte_level VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3489E8352BF3BB94 ON message_alerte (alerte_level)');
        $this->addSql('ALTER TABLE verger DROP FOREIGN KEY FK_17448D024622A01F');
        $this->addSql('ALTER TABLE verger ADD CONSTRAINT FK_17448D024622A01F FOREIGN KEY (message_alerte_id) REFERENCES message_alerte (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_3489E8352BF3BB94 ON message_alerte');
        $this->addSql('ALTER TABLE message_alerte CHANGE alerte_level alerte_level DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE verger DROP FOREIGN KEY FK_17448D024622A01F');
        $this->addSql('ALTER TABLE verger ADD CONSTRAINT FK_17448D024622A01F FOREIGN KEY (message_alerte_id) REFERENCES message_alerte (id) ON UPDATE CASCADE ON DELETE SET NULL');
    }
}
