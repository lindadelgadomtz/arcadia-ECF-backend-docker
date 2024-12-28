<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240726220108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapport_veterinaire DROP FOREIGN KEY FK_CE729CDEED766068');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3D60322AC');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('ALTER TABLE gallery ADD habitat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783AAFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id)');
        $this->addSql('CREATE INDEX IDX_472B783AAFFE2D26 ON gallery (habitat_id)');
        $this->addSql('DROP INDEX IDX_CE729CDEED766068 ON rapport_veterinaire');
        $this->addSql('ALTER TABLE rapport_veterinaire DROP username_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, username VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, nom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_1D1C63B3D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE gallery DROP FOREIGN KEY FK_472B783AAFFE2D26');
        $this->addSql('DROP INDEX IDX_472B783AAFFE2D26 ON gallery');
        $this->addSql('ALTER TABLE gallery DROP habitat_id');
        $this->addSql('ALTER TABLE rapport_veterinaire ADD username_id INT NOT NULL');
        $this->addSql('ALTER TABLE rapport_veterinaire ADD CONSTRAINT FK_CE729CDEED766068 FOREIGN KEY (username_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_CE729CDEED766068 ON rapport_veterinaire (username_id)');
    }
}
