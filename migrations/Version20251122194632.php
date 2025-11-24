<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251122194632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bon_client (id INT AUTO_INCREMENT NOT NULL, type_carburant_id INT DEFAULT NULL, client_station_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, amount INT NOT NULL, quantity DOUBLE PRECISION NOT NULL, is_paid TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', description VARCHAR(255) DEFAULT NULL, INDEX IDX_D3387C6FB5991721 (type_carburant_id), INDEX IDX_D3387C6FB1E98F74 (client_station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_station (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, fuel_price LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CD04BCCC21BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bon_client ADD CONSTRAINT FK_D3387C6FB5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE bon_client ADD CONSTRAINT FK_D3387C6FB1E98F74 FOREIGN KEY (client_station_id) REFERENCES client_station (id)');
        $this->addSql('ALTER TABLE client_station ADD CONSTRAINT FK_CD04BCCC21BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bon_client DROP FOREIGN KEY FK_D3387C6FB5991721');
        $this->addSql('ALTER TABLE bon_client DROP FOREIGN KEY FK_D3387C6FB1E98F74');
        $this->addSql('ALTER TABLE client_station DROP FOREIGN KEY FK_CD04BCCC21BDB235');
        $this->addSql('DROP TABLE bon_client');
        $this->addSql('DROP TABLE client_station');
    }
}
