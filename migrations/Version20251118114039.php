<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118114039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pistolets (id INT AUTO_INCREMENT NOT NULL, pompe_id INT DEFAULT NULL, type_carburant_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, index_pistolet INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3BD281796CCC95AD (pompe_id), INDEX IDX_3BD28179B5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pompes (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E5D44D521BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pistolets ADD CONSTRAINT FK_3BD281796CCC95AD FOREIGN KEY (pompe_id) REFERENCES pompes (id)');
        $this->addSql('ALTER TABLE pistolets ADD CONSTRAINT FK_3BD28179B5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE pompes ADD CONSTRAINT FK_E5D44D521BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pistolets DROP FOREIGN KEY FK_3BD281796CCC95AD');
        $this->addSql('ALTER TABLE pistolets DROP FOREIGN KEY FK_3BD28179B5991721');
        $this->addSql('ALTER TABLE pompes DROP FOREIGN KEY FK_E5D44D521BDB235');
        $this->addSql('DROP TABLE pistolets');
        $this->addSql('DROP TABLE pompes');
    }
}
