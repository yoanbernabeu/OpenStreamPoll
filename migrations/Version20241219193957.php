<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219193957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove is_active column from poll table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__poll AS SELECT id, title, description, short_code, start_at, end_at, question1, question2, question3, question4, question5 FROM poll');
        $this->addSql('DROP TABLE poll');
        $this->addSql('CREATE TABLE poll (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, short_code VARCHAR(255) NOT NULL, start_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , end_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , question1 CLOB DEFAULT NULL, question2 CLOB DEFAULT NULL, question3 CLOB DEFAULT NULL, question4 CLOB DEFAULT NULL, question5 CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO poll (id, title, description, short_code, start_at, end_at, question1, question2, question3, question4, question5) SELECT id, title, description, short_code, start_at, end_at, question1, question2, question3, question4, question5 FROM __temp__poll');
        $this->addSql('DROP TABLE __temp__poll');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE poll ADD COLUMN is_active BOOLEAN NOT NULL');
    }
}
