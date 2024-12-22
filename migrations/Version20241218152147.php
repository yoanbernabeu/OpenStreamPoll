<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218152147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add poll, user, vote and messenger_messages tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE poll (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, short_code VARCHAR(255) NOT NULL, start_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , end_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , is_active BOOLEAN NOT NULL, question1 CLOB DEFAULT NULL, question2 CLOB DEFAULT NULL, question3 CLOB DEFAULT NULL, question4 CLOB DEFAULT NULL, question5 CLOB DEFAULT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON user (username)');
        $this->addSql('CREATE TABLE vote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, poll_id INTEGER NOT NULL, voter_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_5A1085643C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5A1085643C947C0F ON vote (poll_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE poll');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
