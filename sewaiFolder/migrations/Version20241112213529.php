<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241112213529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, question_id_id INT NOT NULL, answer_text LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_DADD4A254FAF8F53 (question_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, lesson_number INT NOT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, lesson_id_id INT NOT NULL, title VARCHAR(255) NOT NULL, answer_type VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B6F7494E35A24AD0 (lesson_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, profile_pic VARCHAR(255) DEFAULT NULL, streak_count INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_tracking (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, status VARCHAR(255) NOT NULL, completed_at DATETIME NOT NULL, started_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_tracking_user (user_tracking_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_3132E9DCD41E622B (user_tracking_id), INDEX IDX_3132E9DCA76ED395 (user_id), PRIMARY KEY(user_tracking_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_tracking_course (user_tracking_id INT NOT NULL, course_id INT NOT NULL, INDEX IDX_ACCE807AD41E622B (user_tracking_id), INDEX IDX_ACCE807A591CC992 (course_id), PRIMARY KEY(user_tracking_id, course_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_tracking_lesson (user_tracking_id INT NOT NULL, lesson_id INT NOT NULL, INDEX IDX_42249B30D41E622B (user_tracking_id), INDEX IDX_42249B30CDF80196 (lesson_id), PRIMARY KEY(user_tracking_id, lesson_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A254FAF8F53 FOREIGN KEY (question_id_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E35A24AD0 FOREIGN KEY (lesson_id_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE user_tracking_user ADD CONSTRAINT FK_3132E9DCD41E622B FOREIGN KEY (user_tracking_id) REFERENCES user_tracking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tracking_user ADD CONSTRAINT FK_3132E9DCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tracking_course ADD CONSTRAINT FK_ACCE807AD41E622B FOREIGN KEY (user_tracking_id) REFERENCES user_tracking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tracking_course ADD CONSTRAINT FK_ACCE807A591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tracking_lesson ADD CONSTRAINT FK_42249B30D41E622B FOREIGN KEY (user_tracking_id) REFERENCES user_tracking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tracking_lesson ADD CONSTRAINT FK_42249B30CDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A254FAF8F53');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E35A24AD0');
        $this->addSql('ALTER TABLE user_tracking_user DROP FOREIGN KEY FK_3132E9DCD41E622B');
        $this->addSql('ALTER TABLE user_tracking_user DROP FOREIGN KEY FK_3132E9DCA76ED395');
        $this->addSql('ALTER TABLE user_tracking_course DROP FOREIGN KEY FK_ACCE807AD41E622B');
        $this->addSql('ALTER TABLE user_tracking_course DROP FOREIGN KEY FK_ACCE807A591CC992');
        $this->addSql('ALTER TABLE user_tracking_lesson DROP FOREIGN KEY FK_42249B30D41E622B');
        $this->addSql('ALTER TABLE user_tracking_lesson DROP FOREIGN KEY FK_42249B30CDF80196');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_tracking');
        $this->addSql('DROP TABLE user_tracking_user');
        $this->addSql('DROP TABLE user_tracking_course');
        $this->addSql('DROP TABLE user_tracking_lesson');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
