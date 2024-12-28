<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240707200843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image_animal (image_id INT NOT NULL, animal_id INT NOT NULL, INDEX IDX_C5B67DD73DA5256D (image_id), INDEX IDX_C5B67DD78E962C16 (animal_id), PRIMARY KEY(image_id, animal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_animal ADD CONSTRAINT FK_C5B67DD73DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_animal ADD CONSTRAINT FK_C5B67DD78E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_animal DROP FOREIGN KEY FK_C5B67DD73DA5256D');
        $this->addSql('ALTER TABLE image_animal DROP FOREIGN KEY FK_C5B67DD78E962C16');
        $this->addSql('DROP TABLE image_animal');
    }
}
