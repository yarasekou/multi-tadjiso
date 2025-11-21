<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120002719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cuve_mesure (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, level_cm INT NOT NULL, volume INT NOT NULL, INDEX IDX_92274D049FB71B08 (cuve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cuve_mesure ADD CONSTRAINT FK_92274D049FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cuve_mesure DROP FOREIGN KEY FK_92274D049FB71B08');
        $this->addSql('DROP TABLE cuve_mesure');
    }
}
