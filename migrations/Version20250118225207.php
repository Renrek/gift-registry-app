<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118225207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invitation (id INT AUTO_INCREMENT NOT NULL, inviter_id INT NOT NULL, email VARCHAR(255) NOT NULL, used TINYINT(1) NOT NULL, invitation_code VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_F11D61A2BA14FCCC (invitation_code), INDEX IDX_F11D61A2B79F4F04 (inviter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2B79F4F04 FOREIGN KEY (inviter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD invited_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A7B4A7E3 FOREIGN KEY (invited_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649A7B4A7E3 ON user (invited_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A2B79F4F04');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A7B4A7E3');
        $this->addSql('DROP INDEX IDX_8D93D649A7B4A7E3 ON user');
        $this->addSql('ALTER TABLE user DROP invited_by_id');
    }
}
