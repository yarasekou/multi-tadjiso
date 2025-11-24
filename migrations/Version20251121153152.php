<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251121153152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cuve (id INT AUTO_INCREMENT NOT NULL, station_id INT NOT NULL, type_carburant_id INT NOT NULL, code VARCHAR(255) NOT NULL, capacity INT NOT NULL, stock INT NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_average_purchase_price INT DEFAULT NULL, average_purchase_price INT DEFAULT NULL, is_last TINYINT(1) DEFAULT NULL, INDEX IDX_1E5066ED21BDB235 (station_id), INDEX IDX_1E5066EDB5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cuve_mesure (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, level_cm INT NOT NULL, volume INT NOT NULL, INDEX IDX_92274D049FB71B08 (cuve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE global_stockage (id INT AUTO_INCREMENT NOT NULL, type_carburant_id INT DEFAULT NULL, quantity INT NOT NULL, purchase_price INT NOT NULL, missing_quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1DDAE2BCB5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indexation (id INT AUTO_INCREMENT NOT NULL, pistolet_id INT DEFAULT NULL, val_index DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', difference DOUBLE PRECISION NOT NULL, INDEX IDX_7FE1FDFB77248C26 (pistolet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jaugeage (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, quantity INT NOT NULL, is_last TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2BBCDDF59FB71B08 (cuve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pistolet (id INT AUTO_INCREMENT NOT NULL, pompe_id INT DEFAULT NULL, type_carburant_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, index_pistolet INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3BD281796CCC95AD (pompe_id), INDEX IDX_3BD28179B5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pompe (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E5D44D521BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE retour_cuve (id INT AUTO_INCREMENT NOT NULL, type_carburant_id INT DEFAULT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_236E0789B5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE station (id INT AUTO_INCREMENT NOT NULL, structure_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9F39F8B12534008B (structure_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stockage (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, gloabal_stockage_id INT DEFAULT NULL, quantity INT NOT NULL, purchase_price INT NOT NULL, missing_quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_last TINYINT(1) NOT NULL, INDEX IDX_CABCB4929FB71B08 (cuve_id), INDEX IDX_CABCB492B225A1E9 (gloabal_stockage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE structure (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, address VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', email VARCHAR(255) NOT NULL, website VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_carburant (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, unit_price INT NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_AC721A21BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, structure_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, enable TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', level INT NOT NULL, profile LONGTEXT DEFAULT NULL, INDEX IDX_8D93D6492534008B (structure_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user_role (user_id INT NOT NULL, user_role_id INT NOT NULL, INDEX IDX_2D084B47A76ED395 (user_id), INDEX IDX_2D084B478E0E3CA6 (user_role_id), PRIMARY KEY(user_id, user_role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente_cuve (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', quantity DOUBLE PRECISION NOT NULL, purchase_amount BIGINT NOT NULL, sale_amount BIGINT NOT NULL, profit BIGINT NOT NULL, INDEX IDX_E0160F19FB71B08 (cuve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente_pistolet (id INT AUTO_INCREMENT NOT NULL, pistolet_id INT DEFAULT NULL, quantity DOUBLE PRECISION NOT NULL, amount BIGINT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9B87699D77248C26 (pistolet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cuve ADD CONSTRAINT FK_1E5066ED21BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE cuve ADD CONSTRAINT FK_1E5066EDB5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE cuve_mesure ADD CONSTRAINT FK_92274D049FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
        $this->addSql('ALTER TABLE global_stockage ADD CONSTRAINT FK_1DDAE2BCB5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE indexation ADD CONSTRAINT FK_7FE1FDFB77248C26 FOREIGN KEY (pistolet_id) REFERENCES pistolet (id)');
        $this->addSql('ALTER TABLE jaugeage ADD CONSTRAINT FK_2BBCDDF59FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
        $this->addSql('ALTER TABLE pistolet ADD CONSTRAINT FK_3BD281796CCC95AD FOREIGN KEY (pompe_id) REFERENCES pompe (id)');
        $this->addSql('ALTER TABLE pistolet ADD CONSTRAINT FK_3BD28179B5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE pompe ADD CONSTRAINT FK_E5D44D521BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE retour_cuve ADD CONSTRAINT FK_236E0789B5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B12534008B FOREIGN KEY (structure_id) REFERENCES structure (id)');
        $this->addSql('ALTER TABLE stockage ADD CONSTRAINT FK_CABCB4929FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
        $this->addSql('ALTER TABLE stockage ADD CONSTRAINT FK_CABCB492B225A1E9 FOREIGN KEY (gloabal_stockage_id) REFERENCES global_stockage (id)');
        $this->addSql('ALTER TABLE type_carburant ADD CONSTRAINT FK_AC721A21BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492534008B FOREIGN KEY (structure_id) REFERENCES structure (id)');
        $this->addSql('ALTER TABLE user_user_role ADD CONSTRAINT FK_2D084B47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user_role ADD CONSTRAINT FK_2D084B478E0E3CA6 FOREIGN KEY (user_role_id) REFERENCES user_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vente_cuve ADD CONSTRAINT FK_E0160F19FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
        $this->addSql('ALTER TABLE vente_pistolet ADD CONSTRAINT FK_9B87699D77248C26 FOREIGN KEY (pistolet_id) REFERENCES pistolet (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cuve DROP FOREIGN KEY FK_1E5066ED21BDB235');
        $this->addSql('ALTER TABLE cuve DROP FOREIGN KEY FK_1E5066EDB5991721');
        $this->addSql('ALTER TABLE cuve_mesure DROP FOREIGN KEY FK_92274D049FB71B08');
        $this->addSql('ALTER TABLE global_stockage DROP FOREIGN KEY FK_1DDAE2BCB5991721');
        $this->addSql('ALTER TABLE indexation DROP FOREIGN KEY FK_7FE1FDFB77248C26');
        $this->addSql('ALTER TABLE jaugeage DROP FOREIGN KEY FK_2BBCDDF59FB71B08');
        $this->addSql('ALTER TABLE pistolet DROP FOREIGN KEY FK_3BD281796CCC95AD');
        $this->addSql('ALTER TABLE pistolet DROP FOREIGN KEY FK_3BD28179B5991721');
        $this->addSql('ALTER TABLE pompe DROP FOREIGN KEY FK_E5D44D521BDB235');
        $this->addSql('ALTER TABLE retour_cuve DROP FOREIGN KEY FK_236E0789B5991721');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B12534008B');
        $this->addSql('ALTER TABLE stockage DROP FOREIGN KEY FK_CABCB4929FB71B08');
        $this->addSql('ALTER TABLE stockage DROP FOREIGN KEY FK_CABCB492B225A1E9');
        $this->addSql('ALTER TABLE type_carburant DROP FOREIGN KEY FK_AC721A21BDB235');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492534008B');
        $this->addSql('ALTER TABLE user_user_role DROP FOREIGN KEY FK_2D084B47A76ED395');
        $this->addSql('ALTER TABLE user_user_role DROP FOREIGN KEY FK_2D084B478E0E3CA6');
        $this->addSql('ALTER TABLE vente_cuve DROP FOREIGN KEY FK_E0160F19FB71B08');
        $this->addSql('ALTER TABLE vente_pistolet DROP FOREIGN KEY FK_9B87699D77248C26');
        $this->addSql('DROP TABLE cuve');
        $this->addSql('DROP TABLE cuve_mesure');
        $this->addSql('DROP TABLE global_stockage');
        $this->addSql('DROP TABLE indexation');
        $this->addSql('DROP TABLE jaugeage');
        $this->addSql('DROP TABLE pistolet');
        $this->addSql('DROP TABLE pompe');
        $this->addSql('DROP TABLE retour_cuve');
        $this->addSql('DROP TABLE station');
        $this->addSql('DROP TABLE stockage');
        $this->addSql('DROP TABLE structure');
        $this->addSql('DROP TABLE type_carburant');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_user_role');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE vente_cuve');
        $this->addSql('DROP TABLE vente_pistolet');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
