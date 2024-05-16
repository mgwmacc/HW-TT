<?php

declare(strict_types=1);

namespace migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240511072747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE `group` (
                id INT AUTO_INCREMENT NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                
                PRIMARY KEY(id),
                
                INDEX name_ind (name)
            ) 
            
            DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` 
            ENGINE = InnoDB'
        );

        $this->addSql('
            CREATE TABLE `user` (
                id INT AUTO_INCREMENT NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                email VARCHAR(255) NOT NULL, 
                group_id INT NOT NULL,
                
                PRIMARY KEY(id),
                                
                INDEX name_ind (name),
                INDEX group_id_ind (group_id),
                
                UNIQUE INDEX email_ind (email),
                
                FOREIGN KEY (group_id)
                    REFERENCES `group`(id)
                    ON DELETE CASCADE
            ) 
                
            DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` 
            ENGINE = InnoDB'
        );

        //Auto generated:
        $this->addSql('
            CREATE TABLE messenger_messages (
                id BIGINT AUTO_INCREMENT NOT NULL, 
                body LONGTEXT NOT NULL, 
                headers LONGTEXT NOT NULL, 
                queue_name VARCHAR(190) NOT NULL, 
                created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
                available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
                delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
                INDEX IDX_75EA56E0FB7336F0 (queue_name), 
                INDEX IDX_75EA56E0E3BD61CE (available_at), 
                INDEX IDX_75EA56E016BA31DB (delivered_at), 
                PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 
                COLLATE `utf8mb4_unicode_ci` 
                ENGINE = InnoDB'
        );
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE group');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
