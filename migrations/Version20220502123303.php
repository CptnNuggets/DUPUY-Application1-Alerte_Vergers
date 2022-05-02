<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220502123303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'added a cascade behavior to the assoc_capteur_station table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assoc_capteur_station DROP FOREIGN KEY FK_CFB00E2721BDB235');
        $this->addSql('ALTER TABLE assoc_capteur_station ADD CONSTRAINT FK_CFB00E2721BDB235 FOREIGN KEY (station_id) REFERENCES station (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assoc_capteur_station DROP FOREIGN KEY FK_CFB00E2721BDB235');
        $this->addSql('ALTER TABLE assoc_capteur_station ADD CONSTRAINT FK_CFB00E2721BDB235 FOREIGN KEY (station_id) REFERENCES station (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
