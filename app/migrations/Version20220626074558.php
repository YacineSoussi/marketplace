<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220626074558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notifier (id INT AUTO_INCREMENT NOT NULL, is_viewed TINYINT(1) DEFAULT NULL, content VARCHAR(255) DEFAULT NULL, seller_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_like (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_218B62124584665A (product_id), INDEX IDX_218B6212A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_like ADD CONSTRAINT FK_218B62124584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_like ADD CONSTRAINT FK_218B6212A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE wish_list');
        $this->addSql('ALTER TABLE product ADD sold SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE is_actif is_actif TINYINT(1) DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE wish_list (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, products_id INT NOT NULL, INDEX IDX_5B8739BD6C8A81A9 (products_id), UNIQUE INDEX UNIQ_5B8739BDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BD6C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE notifier');
        $this->addSql('DROP TABLE product_like');
        $this->addSql('ALTER TABLE product DROP sold');
        $this->addSql('ALTER TABLE user CHANGE is_actif is_actif TINYINT(1) DEFAULT NULL');
    }
}
