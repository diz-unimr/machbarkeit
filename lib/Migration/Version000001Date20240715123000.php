<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Migration;

use Closure;
use OCA\Machbarkeit\Db\Module;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version000001Date20240715123000 extends SimpleMigrationStep
{

	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options)
	{
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		// ontology context
		if (!$schema->hasTable('ontology_context')) {
			$table = $schema->createTable('ontology_context');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('system', Types::STRING, [
				'notnull' => true,
				'length' => 200
			]);
			$table->addColumn('code', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('version', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('display', Types::STRING, [
				'notnull' => true,
				'length' => 300,
			]);


			$table->setPrimaryKey(['id']);
		}

		// ontology termcode
		if (!$schema->hasTable('ontology_termcode')) {
			$table = $schema->createTable('ontology_termcode');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('system', Types::STRING, [
				'notnull' => true,
				'length' => 200
			]);
			$table->addColumn('code', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('version', Types::STRING, [
				'notnull' => true,
				'length' => 200,
			]);
			$table->addColumn('display', Types::STRING, [
				'notnull' => true,
				'length' => 300,
			]);


			$table->setPrimaryKey(['id']);
		}

		// module
		if (!$schema->hasTable('module')) {
			$table = $schema->createTable('module');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 200
			]);
			$table->addColumn('ontology', Types::JSON, [
				'notnull' => true,
			]);

			$table->addColumn('ui_profile', Types::TEXT, [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
		}

		return $schema;
	}
}
