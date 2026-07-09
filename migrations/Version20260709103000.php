<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260709103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed a third current inventory movement.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO inventory_movement (owner_id, reference, movement_date, quality, carrier, categories, quantity, movement_value, balance_direction, balance_amount) SELECT id, \'TRA-025\', \'2024-01-22\', \'Bon\', \'Chronopost\', \'["Transport", "Logistique"]\', 12, 1800.00, \'pay\', 120.00 FROM users WHERE email = \'normanbelaid@gmail.com\' AND NOT EXISTS (SELECT 1 FROM inventory_movement WHERE owner_id = users.id AND reference = \'TRA-025\')');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'offered\', \'FIL-003\', \'Film étirable\', 40, 1400.00 FROM inventory_movement WHERE reference = \'TRA-025\'');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'offered\', \'EMB-004\', \'Cartons renforcés\', 20, 400.00 FROM inventory_movement WHERE reference = \'TRA-025\'');
        $this->addSql('INSERT INTO inventory_movement_item (movement_id, side, reference, name, quantity, item_value) SELECT id, \'received\', \'LOG-017\', \'Bacs logistiques\', 12, 1680.00 FROM inventory_movement WHERE reference = \'TRA-025\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE item FROM inventory_movement_item item INNER JOIN inventory_movement movement ON movement.id = item.movement_id WHERE movement.reference = \'TRA-025\'');
        $this->addSql('DELETE FROM inventory_movement WHERE reference = \'TRA-025\'');
    }
}
