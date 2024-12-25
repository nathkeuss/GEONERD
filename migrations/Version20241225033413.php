<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241225033413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_forum ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post_forum ADD CONSTRAINT FK_12303222A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_12303222A76ED395 ON post_forum (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_forum DROP FOREIGN KEY FK_12303222A76ED395');
        $this->addSql('DROP INDEX IDX_12303222A76ED395 ON post_forum');
        $this->addSql('ALTER TABLE post_forum DROP user_id');
    }
}
