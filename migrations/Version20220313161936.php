<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220313161936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'changed reference in mesure from station AND capteur to ONLY assoc_capteur_station';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E7021BDB235');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E7077F53F8A');
        $this->addSql('DROP INDEX IDX_5F1B6E7021BDB235 ON mesure');
        $this->addSql('DROP INDEX IDX_5F1B6E7077F53F8A ON mesure');
        $this->addSql('DROP INDEX UNIQ_5F1B6E7021BDB23577F53F8A4F4A11B1 ON mesure');
        $this->addSql('ALTER TABLE mesure ADD asso_capteur_station_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', DROP station_id, DROP numero_capteur_id');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E70C5D2F37B FOREIGN KEY (asso_capteur_station_id) REFERENCES assoc_capteur_station (id)');
        $this->addSql('CREATE INDEX IDX_5F1B6E70C5D2F37B ON mesure (asso_capteur_station_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E70C5D2F37B');
        $this->addSql('DROP INDEX IDX_5F1B6E70C5D2F37B ON mesure');
        $this->addSql('ALTER TABLE mesure ADD station_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD numero_capteur_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', DROP asso_capteur_station_id');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E7021BDB235 FOREIGN KEY (station_id) REFERENCES station (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E7077F53F8A FOREIGN KEY (numero_capteur_id) REFERENCES numero_capteur (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5F1B6E7021BDB235 ON mesure (station_id)');
        $this->addSql('CREATE INDEX IDX_5F1B6E7077F53F8A ON mesure (numero_capteur_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F1B6E7021BDB23577F53F8A4F4A11B1 ON mesure (station_id, numero_capteur_id, date_time)');
    }
}
