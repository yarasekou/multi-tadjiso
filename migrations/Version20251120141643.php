<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120141643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vente_pistolet (id INT AUTO_INCREMENT NOT NULL, pistolet_id INT DEFAULT NULL, quantity DOUBLE PRECISION NOT NULL, amount BIGINT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9B87699D77248C26 (pistolet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vente_pistolet ADD CONSTRAINT FK_9B87699D77248C26 FOREIGN KEY (pistolet_id) REFERENCES pistolet (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vente_pistolet DROP FOREIGN KEY FK_9B87699D77248C26');
        $this->addSql('DROP TABLE vente_pistolet');
    }
}
