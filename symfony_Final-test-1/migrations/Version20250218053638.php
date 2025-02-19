<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218053638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE groupe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, total_points BIGINT NOT NULL)');
        $this->addSql('CREATE TABLE habit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id_id INTEGER NOT NULL, group_id_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, difficulty INTEGER NOT NULL, color VARCHAR(255) NOT NULL, periodicity VARCHAR(255) NOT NULL, CONSTRAINT FK_44FE2172F05788E9 FOREIGN KEY (creator_id_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_44FE21722F68B530 FOREIGN KEY (group_id_id) REFERENCES groupe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_44FE2172F05788E9 ON habit (creator_id_id)');
        $this->addSql('CREATE INDEX IDX_44FE21722F68B530 ON habit (group_id_id)');
        $this->addSql('CREATE TABLE habit_completion (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id_id INTEGER NOT NULL, habit_id_id INTEGER NOT NULL, completed_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_AEAF90C59D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_AEAF90C55E1D9C3D FOREIGN KEY (habit_id_id) REFERENCES habit (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_AEAF90C59D86650F ON habit_completion (user_id_id)');
        $this->addSql('CREATE INDEX IDX_AEAF90C55E1D9C3D ON habit_completion (habit_id_id)');
        $this->addSql('CREATE TABLE invitation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, sender_id_id INTEGER NOT NULL, receiver_id_id INTEGER NOT NULL, group_id_id INTEGER NOT NULL, status VARCHAR(255) NOT NULL, CONSTRAINT FK_F11D61A26061F7CF FOREIGN KEY (sender_id_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F11D61A2BE20CAB0 FOREIGN KEY (receiver_id_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F11D61A22F68B530 FOREIGN KEY (group_id_id) REFERENCES groupe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F11D61A26061F7CF ON invitation (sender_id_id)');
        $this->addSql('CREATE INDEX IDX_F11D61A2BE20CAB0 ON invitation (receiver_id_id)');
        $this->addSql('CREATE INDEX IDX_F11D61A22F68B530 ON invitation (group_id_id)');
        $this->addSql('CREATE TABLE points_log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id_id INTEGER NOT NULL, group_id_id INTEGER NOT NULL, points_change INTEGER NOT NULL, reason VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_4FE554589D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4FE554582F68B530 FOREIGN KEY (group_id_id) REFERENCES groupe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4FE554589D86650F ON points_log (user_id_id)');
        $this->addSql('CREATE INDEX IDX_4FE554582F68B530 ON points_log (group_id_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , last_connection DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , points INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE user_habit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id_id INTEGER NOT NULL, habit_id_id INTEGER NOT NULL, CONSTRAINT FK_A63CDA2A9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A63CDA2A5E1D9C3D FOREIGN KEY (habit_id_id) REFERENCES habit (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A63CDA2A9D86650F ON user_habit (user_id_id)');
        $this->addSql('CREATE INDEX IDX_A63CDA2A5E1D9C3D ON user_habit (habit_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE habit');
        $this->addSql('DROP TABLE habit_completion');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('DROP TABLE points_log');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_habit');
    }
}
