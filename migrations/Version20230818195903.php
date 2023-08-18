<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230818195903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, registration_code VARCHAR(20) NOT NULL, vat VARCHAR(20) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, mobile_phone VARCHAR(25) DEFAULT NULL, UNIQUE INDEX registrationCodeUnique (registration_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE turnover (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, year INT NOT NULL, non_current_assets VARCHAR(20) DEFAULT NULL, current_assets VARCHAR(20) DEFAULT NULL, equity VARCHAR(20) DEFAULT NULL, liabilities VARCHAR(20) DEFAULT NULL, sales_revenue VARCHAR(20) DEFAULT NULL, profit_before_taxes VARCHAR(20) DEFAULT NULL, profit_before_taxes_margin VARCHAR(20) DEFAULT NULL, net_profit VARCHAR(20) DEFAULT NULL, net_profit_margin VARCHAR(20) DEFAULT NULL, INDEX company_id (company_id), UNIQUE INDEX companyIdWithYearUnique (company_id, year), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE turnover ADD CONSTRAINT FK_638A62C979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE turnover DROP FOREIGN KEY FK_638A62C979B1AD6');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE turnover');
    }
}
