<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426072908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Removed valeurs_cumulees table (not to be used in public version)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE valeurs_cumulees');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE valeurs_cumulees (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', station_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', humectation_cumulee DOUBLE PRECISION DEFAULT NULL, temperature_cumulee DOUBLE PRECISION DEFAULT NULL, date_time DATETIME DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_F4900FB21BDB235 (station_id), UNIQUE INDEX UNIQ_F4900FB21BDB2354F4A11B1 (station_id, date_time), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE valeurs_cumulees ADD CONSTRAINT FK_F4900FB21BDB235 FOREIGN KEY (station_id) REFERENCES station (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
