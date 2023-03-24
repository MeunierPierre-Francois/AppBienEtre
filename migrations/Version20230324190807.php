<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230324190807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE proposer (id INT AUTO_INCREMENT NOT NULL, prestataire_id INT DEFAULT NULL, categorie_service_id INT DEFAULT NULL, INDEX IDX_21866C15BE3DB2B7 (prestataire_id), INDEX IDX_21866C157395634A (categorie_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proposer ADD CONSTRAINT FK_21866C15BE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES prestataire (id)');
        $this->addSql('ALTER TABLE proposer ADD CONSTRAINT FK_21866C157395634A FOREIGN KEY (categorie_service_id) REFERENCES categorie_de_services (id)');
        $this->addSql('ALTER TABLE utilisateur CHANGE inscription inscription DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proposer DROP FOREIGN KEY FK_21866C15BE3DB2B7');
        $this->addSql('ALTER TABLE proposer DROP FOREIGN KEY FK_21866C157395634A');
        $this->addSql('DROP TABLE proposer');
        $this->addSql('ALTER TABLE utilisateur CHANGE inscription inscription DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
