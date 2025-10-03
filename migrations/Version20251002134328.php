<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251002134328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_assignment_submission (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, assignment_id INT NOT NULL, score NUMERIC(5, 2) DEFAULT NULL, answer VARCHAR(255) DEFAULT NULL, INDEX IDX_25A27352A76ED395 (user_id), INDEX IDX_25A27352D19302F8 (assignment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_assignment_submission ADD CONSTRAINT FK_25A27352A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_assignment_submission ADD CONSTRAINT FK_25A27352D19302F8 FOREIGN KEY (assignment_id) REFERENCES assignment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_assignment_submission DROP FOREIGN KEY FK_25A27352A76ED395');
        $this->addSql('ALTER TABLE user_assignment_submission DROP FOREIGN KEY FK_25A27352D19302F8');
        $this->addSql('DROP TABLE user_assignment_submission');
    }
}
