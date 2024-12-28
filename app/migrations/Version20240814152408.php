<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240814152408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'link rapport veterinaire w animalFeeding';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal_feeding_rapport_veterinaire (animal_feeding_id INT NOT NULL, rapport_veterinaire_id INT NOT NULL, INDEX IDX_4D4DF01225888241 (animal_feeding_id), INDEX IDX_4D4DF01282A908C2 (rapport_veterinaire_id), PRIMARY KEY(animal_feeding_id, rapport_veterinaire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal_feeding_rapport_veterinaire ADD CONSTRAINT FK_4D4DF01225888241 FOREIGN KEY (animal_feeding_id) REFERENCES animal_feeding (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal_feeding_rapport_veterinaire ADD CONSTRAINT FK_4D4DF01282A908C2 FOREIGN KEY (rapport_veterinaire_id) REFERENCES rapport_veterinaire (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal_feeding_rapport_veterinaire DROP FOREIGN KEY FK_4D4DF01225888241');
        $this->addSql('ALTER TABLE animal_feeding_rapport_veterinaire DROP FOREIGN KEY FK_4D4DF01282A908C2');
        $this->addSql('DROP TABLE animal_feeding_rapport_veterinaire');
    }
}
