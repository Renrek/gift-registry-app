<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228172915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gift_claim (id INT AUTO_INCREMENT NOT NULL, gift_request_id INT NOT NULL, claimer_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_B2418E9CEF8F7F4E (gift_request_id), INDEX IDX_B2418E9CDCEC9C2A (claimer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gift_claim ADD CONSTRAINT FK_B2418E9CEF8F7F4E FOREIGN KEY (gift_request_id) REFERENCES gift_request (id)');
        $this->addSql('ALTER TABLE gift_claim ADD CONSTRAINT FK_B2418E9CDCEC9C2A FOREIGN KEY (claimer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE gift_request ADD quantity INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gift_claim DROP FOREIGN KEY FK_B2418E9CEF8F7F4E');
        $this->addSql('ALTER TABLE gift_claim DROP FOREIGN KEY FK_B2418E9CDCEC9C2A');
        $this->addSql('DROP TABLE gift_claim');
        $this->addSql('ALTER TABLE gift_request DROP quantity');
    }
}
