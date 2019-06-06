<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190605084559 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE donation_user (donation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9C00FD634DC1279C (donation_id), INDEX IDX_9C00FD63A76ED395 (user_id), PRIMARY KEY(donation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE donation_user ADD CONSTRAINT FK_9C00FD634DC1279C FOREIGN KEY (donation_id) REFERENCES donation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE donation_user ADD CONSTRAINT FK_9C00FD63A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE donation_product');
        $this->addSql('ALTER TABLE product ADD donation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD4DC1279C FOREIGN KEY (donation_id) REFERENCES donation (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD4DC1279C ON product (donation_id)');
        $this->addSql('ALTER TABLE user CHANGE phone_number phone_number VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE address CHANGE zip_code zip_code INT NOT NULL');
        $this->addSql('ALTER TABLE donation DROP FOREIGN KEY FK_31E581A0670BAFFE');
        $this->addSql('ALTER TABLE donation DROP FOREIGN KEY FK_31E581A075BD1D29');
        $this->addSql('DROP INDEX IDX_31E581A0670BAFFE ON donation');
        $this->addSql('DROP INDEX IDX_31E581A075BD1D29 ON donation');
        $this->addSql('ALTER TABLE donation DROP giver_id, DROP collector_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE donation_product (donation_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_F9E6F81D4584665A (product_id), INDEX IDX_F9E6F81D4DC1279C (donation_id), PRIMARY KEY(donation_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE donation_product ADD CONSTRAINT FK_F9E6F81D4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE donation_product ADD CONSTRAINT FK_F9E6F81D4DC1279C FOREIGN KEY (donation_id) REFERENCES donation (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE donation_user');
        $this->addSql('ALTER TABLE address CHANGE zip_code zip_code SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE donation ADD giver_id INT DEFAULT NULL, ADD collector_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A0670BAFFE FOREIGN KEY (collector_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A075BD1D29 FOREIGN KEY (giver_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_31E581A0670BAFFE ON donation (collector_id)');
        $this->addSql('CREATE INDEX IDX_31E581A075BD1D29 ON donation (giver_id)');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD4DC1279C');
        $this->addSql('DROP INDEX IDX_D34A04AD4DC1279C ON product');
        $this->addSql('ALTER TABLE product DROP donation_id');
        $this->addSql('ALTER TABLE user CHANGE phone_number phone_number INT NOT NULL');
    }
}
