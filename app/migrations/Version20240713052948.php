<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240713052948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'change animal table, delete created at and change habitat description to 80 in VARCHAR';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE habitat CHANGE description description VARCHAR(80) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE habitat CHANGE description description VARCHAR(50) NOT NULL');
    }
}
