<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120135203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE indexations (id INT AUTO_INCREMENT NOT NULL, pistolet_id INT DEFAULT NULL, val_index DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', difference DOUBLE PRECISION NOT NULL, INDEX IDX_7FE1FDFB77248C26 (pistolet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE indexations ADD CONSTRAINT FK_7FE1FDFB77248C26 FOREIGN KEY (pistolet_id) REFERENCES pistolet (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE indexations DROP FOREIGN KEY FK_7FE1FDFB77248C26');
        $this->addSql('DROP TABLE indexations');
    }
}
