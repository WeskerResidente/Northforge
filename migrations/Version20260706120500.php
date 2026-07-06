<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260706120500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add registration fields to users table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD email VARCHAR(180) NOT NULL, ADD last_name VARCHAR(120) NOT NULL, ADD first_name VARCHAR(120) NOT NULL, ADD company_name VARCHAR(180) NOT NULL, ADD address VARCHAR(255) NOT NULL, ADD company_website VARCHAR(255) DEFAULT NULL, ADD siret VARCHAR(14) NOT NULL, ADD company_description LONGTEXT NOT NULL, ADD tags JSON NOT NULL, ADD roles JSON NOT NULL, ADD password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON users (email)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON users');
        $this->addSql('ALTER TABLE users DROP email, DROP last_name, DROP first_name, DROP company_name, DROP address, DROP company_website, DROP siret, DROP company_description, DROP tags, DROP roles, DROP password');
    }
}
