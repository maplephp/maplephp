<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use MaplePHP\Core\Support\Database\Migrations;

class Test extends Migrations
{
	public function up(Schema $schema): void
	{
		$table = $schema->createTable('tests');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('name', 'string', ['length' => 255]);
		$table->setPrimaryKey(['id']);
	}

	public function down(Schema $schema): void
	{
		$schema->dropTable('tests');
	}
}