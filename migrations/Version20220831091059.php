<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220831091059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cryo_banner (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, subtitle LONGTEXT DEFAULT NULL, position INT DEFAULT NULL, page VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cryo_media (id INT AUTO_INCREMENT NOT NULL, cryo_banner_id INT DEFAULT NULL, picture VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, INDEX IDX_983866D24738C239 (cryo_banner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cryo_media ADD CONSTRAINT FK_983866D24738C239 FOREIGN KEY (cryo_banner_id) REFERENCES cryo_banner (id)');
        $this->addSql('ALTER TABLE cryo_user CHANGE role role VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cryo_media DROP FOREIGN KEY FK_983866D24738C239');
        $this->addSql('DROP TABLE cryo_banner');
        $this->addSql('DROP TABLE cryo_media');
        $this->addSql('ALTER TABLE cryo_user CHANGE role role VARCHAR(50) DEFAULT \'\' NOT NULL');
    }
}
