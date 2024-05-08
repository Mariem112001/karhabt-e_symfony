<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240505135219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_dossier (id INT AUTO_INCREMENT NOT NULL, idu INT DEFAULT NULL, urlcin VARCHAR(255) NOT NULL, urlcerretenu VARCHAR(255) NOT NULL, urlatttravail VARCHAR(255) NOT NULL, urldecrevenu VARCHAR(255) NOT NULL, urlextnaissance VARCHAR(255) NOT NULL, INDEX IDX_DBF5B9899B902AD (idu), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dossier (id INT AUTO_INCREMENT NOT NULL, demande_dossier_id INT DEFAULT NULL, idu INT DEFAULT NULL, cin INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, region VARCHAR(255) NOT NULL, date DATE NOT NULL, montant INT NOT NULL, INDEX IDX_3D48E03797D006F7 (demande_dossier_id), INDEX IDX_3D48E03799B902AD (idu), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demande_dossier ADD CONSTRAINT FK_DBF5B9899B902AD FOREIGN KEY (idu) REFERENCES user (idU)');
        $this->addSql('ALTER TABLE dossier ADD CONSTRAINT FK_3D48E03797D006F7 FOREIGN KEY (demande_dossier_id) REFERENCES demande_dossier (id)');
        $this->addSql('ALTER TABLE dossier ADD CONSTRAINT FK_3D48E03799B902AD FOREIGN KEY (idu) REFERENCES user (idU)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_dossier DROP FOREIGN KEY FK_DBF5B9899B902AD');
        $this->addSql('ALTER TABLE dossier DROP FOREIGN KEY FK_3D48E03797D006F7');
        $this->addSql('ALTER TABLE dossier DROP FOREIGN KEY FK_3D48E03799B902AD');
        $this->addSql('DROP TABLE demande_dossier');
        $this->addSql('DROP TABLE dossier');
    }
}
