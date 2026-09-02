<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260902101450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow invitation.inviter_id to be NULL for system-issued invitations';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE invitation CHANGE inviter_id inviter_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE invitation CHANGE inviter_id inviter_id INT NOT NULL');
    }
}
