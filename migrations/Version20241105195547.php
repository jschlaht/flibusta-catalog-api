<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105195547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE autor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, autor_last_name VARCHAR(255) NOT NULL, autor_first_name VARCHAR(255) DEFAULT NULL, autor_middle_name VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, book_title VARCHAR(255) NOT NULL, book_subtitle VARCHAR(255) DEFAULT NULL, book_language VARCHAR(2) NOT NULL, book_year DATE NOT NULL, book_flibusta_id INTEGER NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE autor');
        $this->addSql('DROP TABLE book');
    }
}
