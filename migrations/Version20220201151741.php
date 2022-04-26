<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220201151741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New database generation based on updated database chart';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE assoc_capteur_station (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', numero_capteur_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', station_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', capteur_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', code_capteur VARCHAR(255) NOT NULL, INDEX IDX_CFB00E2777F53F8A (numero_capteur_id), INDEX IDX_CFB00E2721BDB235 (station_id), INDEX IDX_CFB00E271708A229 (capteur_id), UNIQUE INDEX UNIQ_CFB00E2721BDB2351708A229 (station_id, capteur_id), UNIQUE INDEX UNIQ_CFB00E2721BDB23577F53F8A (station_id, numero_capteur_id), UNIQUE INDEX UNIQ_CFB00E2721BDB23563ED5209 (station_id, code_capteur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assoc_station_verger (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', station_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', verger_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_CB22A34121BDB235 (station_id), INDEX IDX_CB22A3419A5972C6 (verger_id), UNIQUE INDEX UNIQ_CB22A34121BDB2359A5972C6 (station_id, verger_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE capteur (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', capteur_name VARCHAR(255) NOT NULL, unite VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_5B4A16954BD022B7 (capteur_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mesure (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', station_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', numero_capteur_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', valeur DOUBLE PRECISION DEFAULT NULL, date_time DATETIME DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_5F1B6E7021BDB235 (station_id), INDEX IDX_5F1B6E7077F53F8A (numero_capteur_id), UNIQUE INDEX UNIQ_5F1B6E7021BDB23577F53F8A4F4A11B1 (station_id, numero_capteur_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE numero_capteur (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', numero INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_9D1E0834F55AE19E (numero), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE station (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', station_name VARCHAR(255) NOT NULL, station_code VARCHAR(255) DEFAULT NULL, liste_capteurs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_9F39F8B1B010593B (station_name), UNIQUE INDEX UNIQ_9F39F8B1992617A5 (station_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE verger (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', id_verger VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_17448D02317338EF (id_verger), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assoc_capteur_station ADD CONSTRAINT FK_CFB00E2777F53F8A FOREIGN KEY (numero_capteur_id) REFERENCES numero_capteur (id)');
        $this->addSql('ALTER TABLE assoc_capteur_station ADD CONSTRAINT FK_CFB00E2721BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE assoc_capteur_station ADD CONSTRAINT FK_CFB00E271708A229 FOREIGN KEY (capteur_id) REFERENCES capteur (id)');
        $this->addSql('ALTER TABLE assoc_station_verger ADD CONSTRAINT FK_CB22A34121BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE assoc_station_verger ADD CONSTRAINT FK_CB22A3419A5972C6 FOREIGN KEY (verger_id) REFERENCES verger (id)');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E7021BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E7077F53F8A FOREIGN KEY (numero_capteur_id) REFERENCES numero_capteur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assoc_capteur_station DROP FOREIGN KEY FK_CFB00E271708A229');
        $this->addSql('ALTER TABLE assoc_capteur_station DROP FOREIGN KEY FK_CFB00E2777F53F8A');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E7077F53F8A');
        $this->addSql('ALTER TABLE assoc_capteur_station DROP FOREIGN KEY FK_CFB00E2721BDB235');
        $this->addSql('ALTER TABLE assoc_station_verger DROP FOREIGN KEY FK_CB22A34121BDB235');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E7021BDB235');
        $this->addSql('ALTER TABLE assoc_station_verger DROP FOREIGN KEY FK_CB22A3419A5972C6');
        $this->addSql('DROP TABLE assoc_capteur_station');
        $this->addSql('DROP TABLE assoc_station_verger');
        $this->addSql('DROP TABLE capteur');
        $this->addSql('DROP TABLE mesure');
        $this->addSql('DROP TABLE numero_capteur');
        $this->addSql('DROP TABLE station');
        $this->addSql('DROP TABLE verger');
    }
}
