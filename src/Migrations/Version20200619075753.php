<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200619075753 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commande CHANGE prixht prixht DOUBLE PRECISION DEFAULT NULL, CHANGE prixttc prixttc DOUBLE PRECISION DEFAULT NULL, CHANGE adresse_livraison adresse_livraison VARCHAR(255) DEFAULT NULL, CHANGE paiement paiement TINYINT(1) DEFAULT NULL, CHANGE envoi envoi TINYINT(1) DEFAULT NULL, CHANGE annulation_remboursement annulation_remboursement TINYINT(1) DEFAULT NULL, CHANGE stripe_id_session stripe_id_session VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commande CHANGE prixht prixht DOUBLE PRECISION NOT NULL, CHANGE prixttc prixttc DOUBLE PRECISION NOT NULL, CHANGE adresse_livraison adresse_livraison VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE paiement paiement TINYINT(1) NOT NULL, CHANGE envoi envoi TINYINT(1) NOT NULL, CHANGE annulation_remboursement annulation_remboursement TINYINT(1) NOT NULL, CHANGE stripe_id_session stripe_id_session VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
