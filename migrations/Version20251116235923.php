<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251116235923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE global_stockage ADD type_carburant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE global_stockage ADD CONSTRAINT FK_1DDAE2BCB5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('CREATE INDEX IDX_1DDAE2BCB5991721 ON global_stockage (type_carburant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE global_stockage DROP FOREIGN KEY FK_1DDAE2BCB5991721');
        $this->addSql('DROP INDEX IDX_1DDAE2BCB5991721 ON global_stockage');
        $this->addSql('ALTER TABLE global_stockage DROP type_carburant_id');
    }
}
