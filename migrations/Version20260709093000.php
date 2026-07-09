<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260709093000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create inventory products table and seed Norman test products.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS inventory_product (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(180) NOT NULL, description LONGTEXT NOT NULL, quantity INT NOT NULL, product_condition VARCHAR(80) NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, photo VARCHAR(255) DEFAULT NULL, categories JSON NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_INVENTORY_PRODUCT_OWNER (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventory_product ADD CONSTRAINT FK_INVENTORY_PRODUCT_OWNER FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO inventory_product (owner_id, name, description, quantity, `condition`, unit_price, photo, categories, created_at) SELECT id, \'Lot stockage industriel\', \'Palette complète de cartons de déménagement\', 25, \'excellent\', 150.00, NULL, \'["Emballage", "Protection"]\', NOW() FROM users WHERE email = \'normanbelaid@gmail.com\' AND NOT EXISTS (SELECT 1 FROM inventory_product WHERE owner_id = users.id AND name = \'Lot stockage industriel\' AND description = \'Palette complète de cartons de déménagement\')');
        $this->addSql('INSERT INTO inventory_product (owner_id, name, description, quantity, `condition`, unit_price, photo, categories, created_at) SELECT id, \'Lot stockage industriel\', \'Rouleau de scotch industriel 50m\', 120, \'Bon\', 5.00, NULL, \'["Emballage"]\', NOW() FROM users WHERE email = \'normanbelaid@gmail.com\' AND NOT EXISTS (SELECT 1 FROM inventory_product WHERE owner_id = users.id AND name = \'Lot stockage industriel\' AND description = \'Rouleau de scotch industriel 50m\')');
        $this->addSql('INSERT INTO inventory_product (owner_id, name, description, quantity, `condition`, unit_price, photo, categories, created_at) SELECT id, \'Film étirable\', \'Film étirable transparent pour palettes\', 40, \'excellent\', 35.00, NULL, \'["Protection"]\', NOW() FROM users WHERE email = \'normanbelaid@gmail.com\' AND NOT EXISTS (SELECT 1 FROM inventory_product WHERE owner_id = users.id AND name = \'Film étirable\' AND description = \'Film étirable transparent pour palettes\')');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inventory_product DROP FOREIGN KEY FK_INVENTORY_PRODUCT_OWNER');
        $this->addSql('DROP TABLE inventory_product');
    }
}
