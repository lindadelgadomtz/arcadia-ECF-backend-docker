<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240707150255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur ADD role_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B388987678 FOREIGN KEY (role_id_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_1D1C63B388987678 ON utilisateur (role_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B388987678');
        $this->addSql('DROP INDEX IDX_1D1C63B388987678 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP role_id_id');
    }
}
