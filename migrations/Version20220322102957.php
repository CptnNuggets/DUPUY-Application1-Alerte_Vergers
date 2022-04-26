<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220322102957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E70C5D2F37B');
        $this->addSql('DROP INDEX IDX_5F1B6E70C5D2F37B ON mesure');
        $this->addSql('DROP INDEX UNIQ_5F1B6E70C5D2F37B4F4A11B1 ON mesure');
        $this->addSql('ALTER TABLE mesure CHANGE asso_capteur_station_id assoc_capteur_station_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E70EA10B98 FOREIGN KEY (assoc_capteur_station_id) REFERENCES assoc_capteur_station (id)');
        $this->addSql('CREATE INDEX IDX_5F1B6E70EA10B98 ON mesure (assoc_capteur_station_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F1B6E70EA10B984F4A11B1 ON mesure (assoc_capteur_station_id, date_time)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E70EA10B98');
        $this->addSql('DROP INDEX IDX_5F1B6E70EA10B98 ON mesure');
        $this->addSql('DROP INDEX UNIQ_5F1B6E70EA10B984F4A11B1 ON mesure');
        $this->addSql('ALTER TABLE mesure CHANGE assoc_capteur_station_id asso_capteur_station_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E70C5D2F37B FOREIGN KEY (asso_capteur_station_id) REFERENCES assoc_capteur_station (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5F1B6E70C5D2F37B ON mesure (asso_capteur_station_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F1B6E70C5D2F37B4F4A11B1 ON mesure (asso_capteur_station_id, date_time)');
    }
}
