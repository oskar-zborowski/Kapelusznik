<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210406180631 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agreement (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, content VARCHAR(50) NOT NULL, is_required TINYINT(1) NOT NULL, date_added DATETIME NOT NULL, date_of_entry DATE NOT NULL, INDEX IDX_2E655A2461220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_agreement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, agreement_id INT NOT NULL, date_of_accepting DATETIME NOT NULL, cancellation_date DATETIME NOT NULL, INDEX IDX_2E85D7C0A76ED395 (user_id), INDEX IDX_2E85D7C024890B2B (agreement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agreement ADD CONSTRAINT FK_2E655A2461220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_agreement ADD CONSTRAINT FK_2E85D7C0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_agreement ADD CONSTRAINT FK_2E85D7C024890B2B FOREIGN KEY (agreement_id) REFERENCES agreement (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_agreement DROP FOREIGN KEY FK_2E85D7C024890B2B');
        $this->addSql('DROP TABLE agreement');
        $this->addSql('DROP TABLE user_agreement');
    }
}
