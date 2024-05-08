<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240505162747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messagerie (idMessage INT AUTO_INCREMENT NOT NULL, contenu VARCHAR(255) NOT NULL, dateEnvoie DATE NOT NULL, vu TINYINT(1) NOT NULL, deleted TINYINT(1) NOT NULL, Sender INT DEFAULT NULL, Receiver INT DEFAULT NULL, INDEX IDX_14E8F60C58AC4FF9 (Sender), INDEX IDX_14E8F60CC4CEEEC0 (Receiver), PRIMARY KEY(idMessage)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (idR INT AUTO_INCREMENT NOT NULL, sujet VARCHAR(255) NOT NULL, description TEXT NOT NULL, dateReclamation DATE NOT NULL, emailUser VARCHAR(255) NOT NULL, idU INT DEFAULT NULL, INDEX IDX_CE606404A2D72265 (idU), PRIMARY KEY(idR)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponsereclamation (idReponseR INT AUTO_INCREMENT NOT NULL, contenuReponse VARCHAR(255) NOT NULL, DateReponseR DATE NOT NULL, idR INT DEFAULT NULL, INDEX IDX_B052BA703CB3B7C6 (idR), PRIMARY KEY(idReponseR)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE messagerie ADD CONSTRAINT FK_14E8F60C58AC4FF9 FOREIGN KEY (Sender) REFERENCES user (idU)');
        $this->addSql('ALTER TABLE messagerie ADD CONSTRAINT FK_14E8F60CC4CEEEC0 FOREIGN KEY (Receiver) REFERENCES user (idU)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A2D72265 FOREIGN KEY (idU) REFERENCES user (idU)');
        $this->addSql('ALTER TABLE reponsereclamation ADD CONSTRAINT FK_B052BA703CB3B7C6 FOREIGN KEY (idR) REFERENCES reclamation (idR)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messagerie DROP FOREIGN KEY FK_14E8F60C58AC4FF9');
        $this->addSql('ALTER TABLE messagerie DROP FOREIGN KEY FK_14E8F60CC4CEEEC0');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A2D72265');
        $this->addSql('ALTER TABLE reponsereclamation DROP FOREIGN KEY FK_B052BA703CB3B7C6');
        $this->addSql('DROP TABLE messagerie');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reponsereclamation');
    }
}
