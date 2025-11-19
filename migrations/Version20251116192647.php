<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251116192647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE global_stockage (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, purchase_price INT NOT NULL, missing_quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stockage (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, gloabal_stockage_id INT DEFAULT NULL, quantity INT NOT NULL, purchase_price INT NOT NULL, missing_quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_last TINYINT(1) NOT NULL, INDEX IDX_CABCB4929FB71B08 (cuve_id), INDEX IDX_CABCB492B225A1E9 (gloabal_stockage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stockage ADD CONSTRAINT FK_CABCB4929FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuves (id)');
        $this->addSql('ALTER TABLE stockage ADD CONSTRAINT FK_CABCB492B225A1E9 FOREIGN KEY (gloabal_stockage_id) REFERENCES global_stockage (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stockage DROP FOREIGN KEY FK_CABCB4929FB71B08');
        $this->addSql('ALTER TABLE stockage DROP FOREIGN KEY FK_CABCB492B225A1E9');
        $this->addSql('DROP TABLE global_stockage');
        $this->addSql('DROP TABLE stockage');
    }
}
