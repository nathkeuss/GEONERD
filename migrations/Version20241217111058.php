<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217111058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE admin_user');
    }

    public function up(Schema $schema): void
    {
        // Suppression des tables admin_user et user
        $this->addSql('DROP TABLE admin_user');
        $this->addSql('DROP TABLE user');
    }
}
