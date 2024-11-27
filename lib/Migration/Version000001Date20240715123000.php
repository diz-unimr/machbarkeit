<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Migration;

use Closure;
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

		// modules
		if (!$schema->hasTable('machbarkeit_modules')) {
			$table = $schema->createTable('machbarkeit_modules');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('module_name', Types::STRING, [
				'notnull' => true,
			]);
			$table->addColumn('version', Types::STRING, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
		}

		// ontology_filter_options
		/* if (!$schema->hasTable('machbarkeit_filter_options')) {
			$table = $schema->createTable('machbarkeit_filter_options');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('filter_name', Types::STRING, [
				'notnull' => true,
			]);
			$table->addColumn('type', Types::STRING, [
				'notnull' => true,
			]);
			$table->addColumn('filter_options', Types::JSON, [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
		} */

		// ontology_concepts
		if (!$schema->hasTable('machbarkeit_concepts')) {
			$table = $schema->createTable('machbarkeit_concepts');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('display', Types::STRING, [
				'notnull' => true,
			]);
			$table->addColumn('term_codes', Types::JSON, [
				'notnull' => true,
			]);
			$table->addColumn('context', Types::JSON, [
				'notnull' => true,
			]);
			$table->addColumn('code', Types::STRING, [
				'notnull' => true,
			]);
			$table->addColumn('code_system', Types::STRING, [
				'notnull' => true,
			]);
			$table->addColumn('selectable', Types::BOOLEAN, [
				'notnull' => false,
				'default' => false
			]);
			$table->addColumn('leaf', Types::BOOLEAN, [
				'notnull' => false,
				'default' => false
			]);
			$table->addColumn('time_restriction_allowed', Types::BOOLEAN, [
				'notnull' => false,
				'default' => false
			]);
			$table->addColumn('filter_name', Types::STRING, [
				'notnull' => false,
			]);
			$table->addColumn('filter_type', Types::STRING, [
				'notnull' => false,
			]);
			$table->addColumn('filter_options', Types::JSON, [
				'notnull' => true,
			]);
			$table->addColumn('parent_id', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('module_id', Types::INTEGER, [
				'notnull' => true
			]);
			$table->setPrimaryKey(['id']);
			$table->addForeignKeyConstraint($schema->getTable('machbarkeit_modules'), ['module_id'], ['id'], [], 'fk_modules_id_concepts');
			$table->addForeignKeyConstraint($schema->getTable('machbarkeit_concepts'), ['parent_id'], ['id'], [], 'fk_concepts_id_concepts');
		}

		return $schema;
	}
}
