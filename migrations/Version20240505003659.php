<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240505003659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE voiture (idu INT DEFAULT NULL, idV INT AUTO_INCREMENT NOT NULL, marque VARCHAR(50) DEFAULT \'NULL\', modele VARCHAR(50) DEFAULT \'NULL\', couleur VARCHAR(20) DEFAULT \'NULL\', prix NUMERIC(10, 2) DEFAULT NULL, img VARCHAR(255) NOT NULL, description TEXT DEFAULT \'NULL\', INDEX IDX_E9E2810F99B902AD (idu), PRIMARY KEY(idV)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810F99B902AD FOREIGN KEY (idu) REFERENCES user (idU)');
        $this->addSql('ALTER TABLE actualite DROP FOREIGN KEY FK_5492819799B902AD');
        $this->addSql('DROP INDEX idx_54928197a2d72265 ON actualite');
        $this->addSql('CREATE INDEX IDX_5492819799B902AD ON actualite (idu)');
        $this->addSql('ALTER TABLE actualite ADD CONSTRAINT FK_5492819799B902AD FOREIGN KEY (idu) REFERENCES user (idU)');
        $this->addSql('ALTER TABLE arrivage ADD CONSTRAINT FK_C0793158B05317 FOREIGN KEY (idv) REFERENCES voiture (idV)');
        $this->addSql('ALTER TABLE arrivage ADD CONSTRAINT FK_C079315899B902AD FOREIGN KEY (idu) REFERENCES user (idU)');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC99B902AD');
        $this->addSql('DROP INDEX idx_67f068bca2d72265 ON commentaire');
        $this->addSql('CREATE INDEX IDX_67F068BC99B902AD ON commentaire (idu)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC99B902AD FOREIGN KEY (idu) REFERENCES user (idU)');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC799B902AD');
        $this->addSql('DROP INDEX idx_5fb6dec7a2d72265 ON reponse');
        $this->addSql('CREATE INDEX IDX_5FB6DEC799B902AD ON reponse (idu)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC799B902AD FOREIGN KEY (idu) REFERENCES user (idU)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE arrivage DROP FOREIGN KEY FK_C0793158B05317');
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810F99B902AD');
        $this->addSql('DROP TABLE voiture');
        $this->addSql('ALTER TABLE actualite DROP FOREIGN KEY FK_5492819799B902AD');
        $this->addSql('DROP INDEX idx_5492819799b902ad ON actualite');
        $this->addSql('CREATE INDEX IDX_54928197A2D72265 ON actualite (idu)');
        $this->addSql('ALTER TABLE actualite ADD CONSTRAINT FK_5492819799B902AD FOREIGN KEY (idu) REFERENCES user (idU)');
        $this->addSql('ALTER TABLE arrivage DROP FOREIGN KEY FK_C079315899B902AD');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC99B902AD');
        $this->addSql('DROP INDEX idx_67f068bc99b902ad ON commentaire');
        $this->addSql('CREATE INDEX IDX_67F068BCA2D72265 ON commentaire (idu)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC99B902AD FOREIGN KEY (idu) REFERENCES user (idU)');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC799B902AD');
        $this->addSql('DROP INDEX idx_5fb6dec799b902ad ON reponse');
        $this->addSql('CREATE INDEX IDX_5FB6DEC7A2D72265 ON reponse (idu)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC799B902AD FOREIGN KEY (idu) REFERENCES user (idU)');
    }
}
