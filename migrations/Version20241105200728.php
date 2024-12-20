<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105200728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, book_title, book_subtitle, book_language, book_year, book_flibusta_id FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, autor_id INTEGER DEFAULT NULL, book_title VARCHAR(255) NOT NULL, book_subtitle VARCHAR(255) DEFAULT NULL, book_language VARCHAR(2) NOT NULL, book_year DATE NOT NULL, book_flibusta_id INTEGER NOT NULL, CONSTRAINT FK_CBE5A33114D45BBE FOREIGN KEY (autor_id) REFERENCES autor (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO book (id, book_title, book_subtitle, book_language, book_year, book_flibusta_id) SELECT id, book_title, book_subtitle, book_language, book_year, book_flibusta_id FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
        $this->addSql('CREATE INDEX IDX_CBE5A33114D45BBE ON book (autor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, book_title, book_subtitle, book_language, book_year, book_flibusta_id FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, book_title VARCHAR(255) NOT NULL, book_subtitle VARCHAR(255) DEFAULT NULL, book_language VARCHAR(2) NOT NULL, book_year DATE NOT NULL, book_flibusta_id INTEGER NOT NULL)');
        $this->addSql('INSERT INTO book (id, book_title, book_subtitle, book_language, book_year, book_flibusta_id) SELECT id, book_title, book_subtitle, book_language, book_year, book_flibusta_id FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
    }
}
