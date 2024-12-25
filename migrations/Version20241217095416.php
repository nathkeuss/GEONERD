<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241217095416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tutorial_part ADD tutorial_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tutorial_part ADD CONSTRAINT FK_36C9F78B89366B7B FOREIGN KEY (tutorial_id) REFERENCES tutorial (id)');
        $this->addSql('CREATE INDEX IDX_36C9F78B89366B7B ON tutorial_part (tutorial_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tutorial_part DROP FOREIGN KEY FK_36C9F78B89366B7B');
        $this->addSql('DROP INDEX IDX_36C9F78B89366B7B ON tutorial_part');
        $this->addSql('ALTER TABLE tutorial_part DROP tutorial_id');
    }
}
