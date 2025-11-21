<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120112104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vente_cuve (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity DOUBLE PRECISION NOT NULL, purchase_amount BIGINT NOT NULL, sale_amount BIGINT NOT NULL, profit BIGINT NOT NULL, INDEX IDX_E0160F19FB71B08 (cuve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vente_cuve ADD CONSTRAINT FK_E0160F19FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vente_cuve DROP FOREIGN KEY FK_E0160F19FB71B08');
        $this->addSql('DROP TABLE vente_cuve');
    }
}
