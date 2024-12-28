<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712100550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add created_at column to the service table';
    }

    public function up(Schema $schema): void
    {
        // Adding the created_at column
        $this->addSql('ALTER TABLE service ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // Dropping the created_at column
        $this->addSql('ALTER TABLE service DROP created_at');
    }
}
