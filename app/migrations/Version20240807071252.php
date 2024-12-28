<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240807071252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'addition table animal feeding and relations to animal and users.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal_feeding (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', nourriture VARCHAR(255) NOT NULL, nourriture_grammage_emp INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE animal_feeding_animal (animal_feeding_id INT NOT NULL, animal_id INT NOT NULL, INDEX IDX_C023E40D25888241 (animal_feeding_id), INDEX IDX_C023E40D8E962C16 (animal_id), PRIMARY KEY(animal_feeding_id, animal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE animal_feeding_user (animal_feeding_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E2A791F25888241 (animal_feeding_id), INDEX IDX_E2A791FA76ED395 (user_id), PRIMARY KEY(animal_feeding_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal_feeding_animal ADD CONSTRAINT FK_C023E40D25888241 FOREIGN KEY (animal_feeding_id) REFERENCES animal_feeding (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal_feeding_animal ADD CONSTRAINT FK_C023E40D8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal_feeding_user ADD CONSTRAINT FK_E2A791F25888241 FOREIGN KEY (animal_feeding_id) REFERENCES animal_feeding (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal_feeding_user ADD CONSTRAINT FK_E2A791FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal_feeding_animal DROP FOREIGN KEY FK_C023E40D25888241');
        $this->addSql('ALTER TABLE animal_feeding_animal DROP FOREIGN KEY FK_C023E40D8E962C16');
        $this->addSql('ALTER TABLE animal_feeding_user DROP FOREIGN KEY FK_E2A791F25888241');
        $this->addSql('ALTER TABLE animal_feeding_user DROP FOREIGN KEY FK_E2A791FA76ED395');
        $this->addSql('DROP TABLE animal_feeding');
        $this->addSql('DROP TABLE animal_feeding_animal');
        $this->addSql('DROP TABLE animal_feeding_user');
    }
}
