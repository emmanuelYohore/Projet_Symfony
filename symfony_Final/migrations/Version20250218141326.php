<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218141326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT id, name, total_points FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, total_points BIGINT DEFAULT 0 NOT NULL)');
        $this->addSql('INSERT INTO groups (id, name, total_points) SELECT id, name, total_points FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
        $this->addSql('CREATE TEMPORARY TABLE __temp__habit_completions AS SELECT id, user_id, habit_id, completed_at FROM habit_completions');
        $this->addSql('DROP TABLE habit_completions');
        $this->addSql('CREATE TABLE habit_completions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, habit_id INTEGER NOT NULL, completed_at DATETIME NOT NULL, FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, FOREIGN KEY (habit_id) REFERENCES habits (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO habit_completions (id, user_id, habit_id, completed_at) SELECT id, user_id, habit_id, completed_at FROM __temp__habit_completions');
        $this->addSql('DROP TABLE __temp__habit_completions');
        $this->addSql('CREATE INDEX IDX_F0AE56A4A76ED395 ON habit_completions (user_id)');
        $this->addSql('CREATE INDEX IDX_F0AE56A4E7AEB3B2 ON habit_completions (habit_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__habits AS SELECT id, creator_id, group_id, name, description, difficulty, color, periodicity FROM habits');
        $this->addSql('DROP TABLE habits');
        $this->addSql('CREATE TABLE habits (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER DEFAULT NULL, group_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, difficulty INTEGER NOT NULL, color VARCHAR(20) DEFAULT NULL, periodicity VARCHAR(10) NOT NULL, FOREIGN KEY (creator_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, FOREIGN KEY (group_id) REFERENCES groups (id) ON UPDATE NO ACTION ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO habits (id, creator_id, group_id, name, description, difficulty, color, periodicity) SELECT id, creator_id, group_id, name, description, difficulty, color, periodicity FROM __temp__habits');
        $this->addSql('DROP TABLE __temp__habits');
        $this->addSql('CREATE INDEX IDX_A541213A61220EA6 ON habits (creator_id)');
        $this->addSql('CREATE INDEX IDX_A541213AFE54D947 ON habits (group_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__invitations AS SELECT id, sender_id, receiver_id, group_id, status FROM invitations');
        $this->addSql('DROP TABLE invitations');
        $this->addSql('CREATE TABLE invitations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, sender_id INTEGER NOT NULL, receiver_id INTEGER NOT NULL, group_id INTEGER NOT NULL, status VARCHAR(20) NOT NULL, FOREIGN KEY (sender_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, FOREIGN KEY (receiver_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, FOREIGN KEY (group_id) REFERENCES groups (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO invitations (id, sender_id, receiver_id, group_id, status) SELECT id, sender_id, receiver_id, group_id, status FROM __temp__invitations');
        $this->addSql('DROP TABLE __temp__invitations');
        $this->addSql('CREATE INDEX IDX_232710AEF624B39D ON invitations (sender_id)');
        $this->addSql('CREATE INDEX IDX_232710AECD53EDB6 ON invitations (receiver_id)');
        $this->addSql('CREATE INDEX IDX_232710AEFE54D947 ON invitations (group_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__points_log AS SELECT id, user_id, group_id, points_change, reason, timestamp FROM points_log');
        $this->addSql('DROP TABLE points_log');
        $this->addSql('CREATE TABLE points_log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, group_id INTEGER DEFAULT NULL, points_change INTEGER NOT NULL, reason CLOB DEFAULT NULL, timestamp DATETIME NOT NULL, FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, FOREIGN KEY (group_id) REFERENCES groups (id) ON UPDATE NO ACTION ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO points_log (id, user_id, group_id, points_change, reason, timestamp) SELECT id, user_id, group_id, points_change, reason, timestamp FROM __temp__points_log');
        $this->addSql('DROP TABLE __temp__points_log');
        $this->addSql('CREATE INDEX IDX_4FE55458A76ED395 ON points_log (user_id)');
        $this->addSql('CREATE INDEX IDX_4FE55458FE54D947 ON points_log (group_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_habits AS SELECT user_id, habit_id FROM user_habits');
        $this->addSql('DROP TABLE user_habits');
        $this->addSql('CREATE TABLE user_habits (user_id INTEGER NOT NULL, habit_id INTEGER NOT NULL, PRIMARY KEY(user_id, habit_id), FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, FOREIGN KEY (habit_id) REFERENCES habits (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_habits (user_id, habit_id) SELECT user_id, habit_id FROM __temp__user_habits');
        $this->addSql('DROP TABLE __temp__user_habits');
        $this->addSql('CREATE INDEX IDX_C0133A07A76ED395 ON user_habits (user_id)');
        $this->addSql('CREATE INDEX IDX_C0133A07E7AEB3B2 ON user_habits (habit_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, group_id, first_name, last_name, username, email, password, profile_picture, created_at, last_connection, points FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, group_id INTEGER DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, last_connection DATETIME DEFAULT NULL, points INTEGER DEFAULT 0 NOT NULL, FOREIGN KEY (group_id) REFERENCES groups (id) ON UPDATE NO ACTION ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO users (id, group_id, first_name, last_name, username, email, password, profile_picture, created_at, last_connection, points) SELECT id, group_id, first_name, last_name, username, email, password, profile_picture, created_at, last_connection, points FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE INDEX IDX_1483A5E9FE54D947 ON users (group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__groups AS SELECT id, name, total_points FROM groups');
        $this->addSql('DROP TABLE groups');
        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT DEFAULT NULL, name CLOB DEFAULT NULL, total_points BIGINT DEFAULT 0)');
        $this->addSql('INSERT INTO groups (id, name, total_points) SELECT id, name, total_points FROM __temp__groups');
        $this->addSql('DROP TABLE __temp__groups');
        $this->addSql('CREATE TEMPORARY TABLE __temp__habit_completions AS SELECT id, user_id, habit_id, completed_at FROM habit_completions');
        $this->addSql('DROP TABLE habit_completions');
        $this->addSql('CREATE TABLE habit_completions (id INTEGER PRIMARY KEY AUTOINCREMENT DEFAULT NULL, user_id INTEGER DEFAULT NULL, habit_id INTEGER DEFAULT NULL, completed_at DATETIME DEFAULT CURRENT_TIMESTAMP, CONSTRAINT FK_F0AE56A4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F0AE56A4E7AEB3B2 FOREIGN KEY (habit_id) REFERENCES habits (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO habit_completions (id, user_id, habit_id, completed_at) SELECT id, user_id, habit_id, completed_at FROM __temp__habit_completions');
        $this->addSql('DROP TABLE __temp__habit_completions');
        $this->addSql('CREATE INDEX IDX_F0AE56A4A76ED395 ON habit_completions (user_id)');
        $this->addSql('CREATE INDEX IDX_F0AE56A4E7AEB3B2 ON habit_completions (habit_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__habits AS SELECT id, creator_id, group_id, name, description, difficulty, color, periodicity FROM habits');
        $this->addSql('DROP TABLE habits');
        $this->addSql('CREATE TABLE habits (id INTEGER PRIMARY KEY AUTOINCREMENT DEFAULT NULL, creator_id INTEGER DEFAULT NULL, group_id INTEGER DEFAULT NULL, name CLOB DEFAULT NULL, description CLOB DEFAULT NULL, difficulty INTEGER DEFAULT NULL, color CLOB DEFAULT NULL, periodicity CLOB DEFAULT NULL, CONSTRAINT FK_A541213A61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A541213AFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO habits (id, creator_id, group_id, name, description, difficulty, color, periodicity) SELECT id, creator_id, group_id, name, description, difficulty, color, periodicity FROM __temp__habits');
        $this->addSql('DROP TABLE __temp__habits');
        $this->addSql('CREATE INDEX IDX_A541213A61220EA6 ON habits (creator_id)');
        $this->addSql('CREATE INDEX IDX_A541213AFE54D947 ON habits (group_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__invitations AS SELECT id, sender_id, receiver_id, group_id, status FROM invitations');
        $this->addSql('DROP TABLE invitations');
        $this->addSql('CREATE TABLE invitations (id INTEGER PRIMARY KEY AUTOINCREMENT DEFAULT NULL, sender_id INTEGER DEFAULT NULL, receiver_id INTEGER DEFAULT NULL, group_id INTEGER DEFAULT NULL, status CLOB DEFAULT NULL, CONSTRAINT FK_232710AEF624B39D FOREIGN KEY (sender_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_232710AECD53EDB6 FOREIGN KEY (receiver_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_232710AEFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO invitations (id, sender_id, receiver_id, group_id, status) SELECT id, sender_id, receiver_id, group_id, status FROM __temp__invitations');
        $this->addSql('DROP TABLE __temp__invitations');
        $this->addSql('CREATE INDEX IDX_232710AEF624B39D ON invitations (sender_id)');
        $this->addSql('CREATE INDEX IDX_232710AECD53EDB6 ON invitations (receiver_id)');
        $this->addSql('CREATE INDEX IDX_232710AEFE54D947 ON invitations (group_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__points_log AS SELECT id, user_id, group_id, points_change, reason, timestamp FROM points_log');
        $this->addSql('DROP TABLE points_log');
        $this->addSql('CREATE TABLE points_log (id INTEGER PRIMARY KEY AUTOINCREMENT DEFAULT NULL, user_id INTEGER DEFAULT NULL, group_id INTEGER DEFAULT NULL, points_change INTEGER DEFAULT NULL, reason CLOB DEFAULT NULL, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP, CONSTRAINT FK_4FE55458A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4FE55458FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO points_log (id, user_id, group_id, points_change, reason, timestamp) SELECT id, user_id, group_id, points_change, reason, timestamp FROM __temp__points_log');
        $this->addSql('DROP TABLE __temp__points_log');
        $this->addSql('CREATE INDEX IDX_4FE55458A76ED395 ON points_log (user_id)');
        $this->addSql('CREATE INDEX IDX_4FE55458FE54D947 ON points_log (group_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_habits AS SELECT user_id, habit_id FROM user_habits');
        $this->addSql('DROP TABLE user_habits');
        $this->addSql('CREATE TABLE user_habits (user_id INTEGER DEFAULT NULL, habit_id INTEGER DEFAULT NULL, PRIMARY KEY(user_id, habit_id), CONSTRAINT FK_C0133A07A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C0133A07E7AEB3B2 FOREIGN KEY (habit_id) REFERENCES habits (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_habits (user_id, habit_id) SELECT user_id, habit_id FROM __temp__user_habits');
        $this->addSql('DROP TABLE __temp__user_habits');
        $this->addSql('CREATE INDEX IDX_C0133A07A76ED395 ON user_habits (user_id)');
        $this->addSql('CREATE INDEX IDX_C0133A07E7AEB3B2 ON user_habits (habit_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, group_id, first_name, last_name, username, email, password, profile_picture, created_at, last_connection, points FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT DEFAULT NULL, group_id INTEGER DEFAULT NULL, first_name CLOB DEFAULT NULL, last_name CLOB DEFAULT NULL, username CLOB DEFAULT NULL, email CLOB DEFAULT NULL, password CLOB DEFAULT NULL, profile_picture CLOB DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, last_connection DATETIME DEFAULT NULL, points INTEGER DEFAULT 0, CONSTRAINT FK_1483A5E9FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO users (id, group_id, first_name, last_name, username, email, password, profile_picture, created_at, last_connection, points) SELECT id, group_id, first_name, last_name, username, email, password, profile_picture, created_at, last_connection, points FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE INDEX IDX_1483A5E9FE54D947 ON users (group_id)');
    }
}
