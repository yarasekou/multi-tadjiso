<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021121326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE type_carburant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, unit_price INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_carburant_station (type_carburant_id INT NOT NULL, station_id INT NOT NULL, INDEX IDX_132324B8B5991721 (type_carburant_id), INDEX IDX_132324B821BDB235 (station_id), PRIMARY KEY(type_carburant_id, station_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE type_carburant_station ADD CONSTRAINT FK_132324B8B5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE type_carburant_station ADD CONSTRAINT FK_132324B821BDB235 FOREIGN KEY (station_id) REFERENCES station (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE station ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE structure CHANGE logo logo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_carburant_station DROP FOREIGN KEY FK_132324B8B5991721');
        $this->addSql('ALTER TABLE type_carburant_station DROP FOREIGN KEY FK_132324B821BDB235');
        $this->addSql('DROP TABLE type_carburant');
        $this->addSql('DROP TABLE type_carburant_station');
        $this->addSql('ALTER TABLE structure CHANGE logo logo LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE station DROP created_at, DROP updated_at');
    }
}
