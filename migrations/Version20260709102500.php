<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260709102500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create inventory movement tables and seed current transactions.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE inventory_movement (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, reference VARCHAR(40) NOT NULL, movement_date DATE NOT NULL, quality VARCHAR(80) NOT NULL, carrier VARCHAR(120) NOT NULL, categories JSON NOT NULL, quantity INT NOT NULL, movement_value NUMERIC(10, 2) NOT NULL, balance_direction VARCHAR(20) NOT NULL, balance_amount NUMERIC(10, 2) NOT NULL, INDEX IDX_INVENTORY_MOVEMENT_OWNER (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inventory_movement_item (id INT AUTO_INCREMENT NOT NULL, movement_id INT NOT NULL, side VARCHAR(20) NOT NULL, reference VARCHAR(40) NOT NULL, name VARCHAR(180) NOT NULL, quantity INT NOT NULL, item_value NUMERIC(10, 2) NOT NULL, INDEX IDX_INVENTORY_MOVEMENT_ITEM_MOVEMENT (movement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventory_movement ADD CONSTRAINT FK_INVENTORY_MOVEMENT_OWNER FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inventory_movement_item ADD CONSTRAINT FK_INVENTORY_MOVEMENT_ITEM_MOVEMENT FOREIGN KEY (movement_id) REFERENCES inventory_movement (id) ON DELETE CASCADE');

        $this->addSql('INSERT INTO users (email, last_name, first_name, company_name, address, company_website, siret, company_description, tags, roles, password) SELECT \'contact@chronoforge.test\', \'Martin\', \'Claire\', \'Chronoforge\', \'18 rue des Transporteurs, 75010 Paris\', NULL, \'12345678900013\', \'Partenaire de test pour les transactions Northforge.\', \'["transport", "logistique"]\', \'["ROLE_USER"]\', \'$2y$13$exampleexampleexampleexampleexampleexampleexampleexampleexamplee\' WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = \'contact@chronoforge.test\')');

        $this->addSql('INSERT INTO inventory_product (owner_id, name, description, quantity, product_condition, unit_price, photo, categories, created_at) SELECT id, \'Lot de scotch\', \'Cartons de rouleaux adhésifs industriels\', 5, \'excellent\', 750.00, NULL, \'["Emballage"]\', NOW() FROM users WHERE email = \'contact@chronoforge.test\' AND NOT EXISTS (SELECT 1 FROM inventory_product WHERE owner_id = users.id AND name = \'Lot de scotch\')');
        $this->addSql('INSERT INTO inventory_product (owner_id, name, description, quantity, product_condition, unit_price, photo, categories, created_at) SELECT id, \'Palette de carton\', \'Palettes de cartons renforcés\', 60, \'Bon\', 62.50, NULL, \'["Emballage"]\', NOW() FROM users WHERE email = \'contact@chronoforge.test\' AND NOT EXISTS (SELECT 1 FROM inventory_product WHERE owner_id = users.id AND name = \'Palette de carton\')');
        $this->addSql('INSERT INTO inventory_product (owner_id, name, description, quantity, product_condition, unit_price, photo, categories, created_at) SELECT id, \'Palette de carton\', \'Palettes de cartons standards\', 10, \'excellent\', 375.00, NULL, \'["Protection"]\', NOW() FROM users WHERE email = \'contact@chronoforge.test\' AND NOT EXISTS (SELECT 1 FROM inventory_product WHERE owner_id = users.id AND description = \'Palettes de cartons standards\')');

        $this->addSql('INSERT INTO inventory_movement (owner_id, reference, movement_date, quality, carrier, categories, quantity, movement_value, balance_direction, balance_amount) SELECT id, \'TRA-024\', \'2024-01-20\', \'excellent\', \'Chronopost\', \'["Emballage", "Protection"]\', 25, 3750.00, \'pay\', 200.00 FROM users WHERE email = \'normanbelaid@gmail.com\' AND NOT EXISTS (SELECT 1 FROM inventory_movement WHERE owner_id = users.id AND reference = \'TRA-024\' AND balance_direction = \'pay\')');
        $this->addSql('INSERT INTO inventory_movement (owner_id, reference, movement_date, quality, carrier, categories, quantity, movement_value, balance_direction, balance_amount) SELECT id, \'TRA-024\', \'2024-01-20\', \'excellent\', \'Chronopost\', \'["Emballage", "Protection"]\', 25, 3750.00, \'receive\', 1500.00 FROM users WHERE email = \'normanbelaid@gmail.com\' AND NOT EXISTS (SELECT 1 FROM inventory_movement WHERE owner_id = users.id AND reference = \'TRA-024\' AND balance_direction = \'receive\')');

        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'offered\', \'AAA-024\', \'Lot stockage industriel\', 125, 3750.00 FROM inventory_movement WHERE reference = \'TRA-024\' AND balance_direction = \'pay\'');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'offered\', \'AAA-025\', \'Lot stockage industriel\', 125, 3750.00 FROM inventory_movement WHERE reference = \'TRA-024\' AND balance_direction = \'pay\'');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'received\', \'AAA-045\', \'Lot de scotch\', 5, 3750.00 FROM inventory_movement WHERE reference = \'TRA-024\' AND balance_direction = \'pay\'');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'received\', \'AAA-126\', \'Palette de carton\', 60, 3750.00 FROM inventory_movement WHERE reference = \'TRA-024\' AND balance_direction = \'pay\'');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'received\', \'AAA-254\', \'Palette de carton\', 10, 3750.00 FROM inventory_movement WHERE reference = \'TRA-024\' AND balance_direction = \'pay\'');

        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'offered\', \'AAA-024\', \'Lot stockage industriel\', 125, 3750.00 FROM inventory_movement WHERE reference = \'TRA-024\' AND balance_direction = \'receive\'');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'offered\', \'AAA-025\', \'Lot stockage industriel\', 125, 3750.00 FROM inventory_movement WHERE reference = \'TRA-024\' AND balance_direction = \'receive\'');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'received\', \'AAA-045\', \'Lot de scotch\', 5, 3750.00 FROM inventory_movement WHERE reference = \'TRA-024\' AND balance_direction = \'receive\'');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'received\', \'AAA-126\', \'Palette de carton\', 60, 3750.00 FROM inventory_movement WHERE reference = \'TRA-024\' AND balance_direction = \'receive\'');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'received\', \'AAA-254\', \'Palette de carton\', 10, 3750.00 FROM inventory_movement WHERE reference = \'TRA-024\' AND balance_direction = \'receive\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inventory_movement_item DROP FOREIGN KEY FK_INVENTORY_MOVEMENT_ITEM_MOVEMENT');
        $this->addSql('ALTER TABLE inventory_movement DROP FOREIGN KEY FK_INVENTORY_MOVEMENT_OWNER');
        $this->addSql('DROP TABLE inventory_movement_item');
        $this->addSql('DROP TABLE inventory_movement');
    }
}
