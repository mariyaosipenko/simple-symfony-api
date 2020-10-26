<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201017132124 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(1024) NOT NULL, sku VARCHAR(255) NOT NULL, category VARCHAR(1024) DEFAULT NULL, brand VARCHAR(1024) DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, length DOUBLE PRECISION DEFAULT NULL, height VARCHAR(255) NOT NULL, width DOUBLE PRECISION NOT NULL, images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', stock INT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, discount_price DOUBLE PRECISION DEFAULT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE product');
    }
}
