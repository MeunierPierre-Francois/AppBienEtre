<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509152245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_CC94AC37CC94AC37 ON code_postal');
        $this->addSql('DROP INDEX UNIQ_E2E2D1EEE2E2D1EE ON commune');
        $this->addSql('DROP INDEX UNIQ_F5D7E4A9F5D7E4A9 ON localite');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CC94AC37CC94AC37 ON code_postal (code_postal)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E2E2D1EEE2E2D1EE ON commune (commune)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F5D7E4A9F5D7E4A9 ON localite (localite)');
    }
}
