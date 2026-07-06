<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260706105000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create exchange object table for featured home cards.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE exchange_object (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, purchase_value NUMERIC(10, 2) NOT NULL, image_filename VARCHAR(255) DEFAULT NULL, proposed_by VARCHAR(120) NOT NULL, is_featured TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE INDEX IDX_EXCHANGE_OBJECT_FEATURED_CREATED_AT ON exchange_object (is_featured, created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE exchange_object');
    }
}
