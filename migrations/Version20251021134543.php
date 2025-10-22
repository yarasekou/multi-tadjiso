<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021134543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stations ADD structure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stations ADD CONSTRAINT FK_9F39F8B12534008B FOREIGN KEY (structure_id) REFERENCES structure (id)');
        $this->addSql('CREATE INDEX IDX_9F39F8B12534008B ON stations (structure_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stations DROP FOREIGN KEY FK_9F39F8B12534008B');
        $this->addSql('DROP INDEX IDX_9F39F8B12534008B ON stations');
        $this->addSql('ALTER TABLE stations DROP structure_id');
    }
}
