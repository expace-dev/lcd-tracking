<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260209175436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE intervention (id INT AUTO_INCREMENT NOT NULL, business_date DATE NOT NULL, exit_on_time TINYINT DEFAULT NULL, instructions_respected TINYINT DEFAULT NULL, exit_comment LONGTEXT DEFAULT NULL, check_bed_made TINYINT DEFAULT 0 NOT NULL, check_floor_clean TINYINT DEFAULT 0 NOT NULL, check_bathroom_ok TINYINT DEFAULT 0 NOT NULL, check_kitchen_ok TINYINT DEFAULT 0 NOT NULL, check_linen_changed TINYINT DEFAULT 0 NOT NULL, cleaning_comment LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, property_id INT NOT NULL, created_by_id INT NOT NULL, INDEX IDX_INTERVENTION_PROPERTY (property_id), INDEX IDX_INTERVENTION_CREATOR (created_by_id), INDEX IDX_INTERVENTION_BUSINESS_DATE (business_date), UNIQUE INDEX UNIQ_INTERVENTION_PROPERTY_DATE (property_id, business_date), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE intervention_photo (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, intervention_id INT NOT NULL, INDEX IDX_INTERVENTION_PHOTO_INTERVENTION (intervention_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, assigned_worker_id INT DEFAULT NULL, INDEX IDX_PROPERTY_OWNER (owner_id), INDEX IDX_PROPERTY_ASSIGNED_WORKER (assigned_worker_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(80) NOT NULL, last_name VARCHAR(80) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_worker (user_id INT NOT NULL, worker_id INT NOT NULL, INDEX IDX_FAE0A45FA76ED395 (user_id), INDEX IDX_FAE0A45F6B20BA36 (worker_id), PRIMARY KEY (user_id, worker_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE worker (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(80) NOT NULL, last_name VARCHAR(80) NOT NULL, phone VARCHAR(10) NOT NULL, email VARCHAR(180) DEFAULT NULL, access_token VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_WORKER_PHONE (phone), UNIQUE INDEX UNIQ_WORKER_TOKEN (access_token), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABB03A8386 FOREIGN KEY (created_by_id) REFERENCES worker (id)');
        $this->addSql('ALTER TABLE intervention_photo ADD CONSTRAINT FK_61D2989C8EAE3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE12810F94 FOREIGN KEY (assigned_worker_id) REFERENCES worker (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_worker ADD CONSTRAINT FK_FAE0A45FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_worker ADD CONSTRAINT FK_FAE0A45F6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB549213EC');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABB03A8386');
        $this->addSql('ALTER TABLE intervention_photo DROP FOREIGN KEY FK_61D2989C8EAE3863');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE7E3C61F9');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE12810F94');
        $this->addSql('ALTER TABLE user_worker DROP FOREIGN KEY FK_FAE0A45FA76ED395');
        $this->addSql('ALTER TABLE user_worker DROP FOREIGN KEY FK_FAE0A45F6B20BA36');
        $this->addSql('DROP TABLE intervention');
        $this->addSql('DROP TABLE intervention_photo');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_worker');
        $this->addSql('DROP TABLE worker');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
