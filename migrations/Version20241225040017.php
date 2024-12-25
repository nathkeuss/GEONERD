<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241225040017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_forum ADD parent_post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post_forum ADD CONSTRAINT FK_1230322239C1776A FOREIGN KEY (parent_post_id) REFERENCES post_forum (id)');
        $this->addSql('CREATE INDEX IDX_1230322239C1776A ON post_forum (parent_post_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_forum DROP FOREIGN KEY FK_1230322239C1776A');
        $this->addSql('DROP INDEX IDX_1230322239C1776A ON post_forum');
        $this->addSql('ALTER TABLE post_forum DROP parent_post_id');
    }
}
