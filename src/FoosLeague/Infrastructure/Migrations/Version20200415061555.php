<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200415061555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $sql = <<<SQL
create table event_store
(
    event_id       serial         not null PRIMARY KEY,
    event_body     varchar(65000) not null,
    event_type     varchar(250)   not null,
    stream_name    varchar(250)   not null UNIQUE,
    stream_version int            not null,
    UNIQUE (stream_version, stream_name)
);
SQL;
        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
