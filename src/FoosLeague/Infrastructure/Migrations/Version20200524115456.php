<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200524115456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $sql = <<<SQL
create table league_list
(
	id serial constraint league_list_pk primary key,
	owner_id varchar(250),
	league_name varchar(250)
);
SQL;
        $this->addSql($sql);

        $sql = <<<SQL
create index league_list_owner_id_index
	on league_list (owner_id);
SQL;
        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
