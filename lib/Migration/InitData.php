<?php

// SPDX-FileCopyrightText: 2024 Sebastian Stöcker <sebastian.stoecker@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Migration;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;
use Psr\Log\LoggerInterface;

class InitData implements IRepairStep
{

	/** @var LoggerInterface */
	protected $logger;
	/** @var IConfig */
	protected $config;
	/** @var IDBConnection */
	protected $db;

	public function __construct(LoggerInterface $logger, IConfig $config, IDBConnection $db)
	{
		$this->logger = $logger;
		$this->db = $db;
		$this->config = $config;
	}

	/**
	 * Returns the step's name
	 */
	public function getName()
	{
		return 'Init data step!';
	}

	/**
	 * @param IOutput $output
	 */
	public function run(IOutput $output)
	{
		$this->storeInitialData();
		/* $previousVersion = $this->config->getAppValue('machbarkeit', 'installed_version', false);
		if (!$previousVersion) {
			$this->storeInitialData();
		} */
	}

	protected function storeInitialData()
	{
		$this->logger->info('Loading ontology data', ['app' => 'Machbarkeit']);

		$query = $this->db->getQueryBuilder();

		// add modules first
		$query
			->insert('machbarkeit_modules')
			->values(
				[
					'id' => $this->db->quote(1),
					'display' => $this->db->quote('Person'),
					'version' => $this->db->quote('1.0.0')
				]
			)->executeStatement();

		$query
			->insert('machbarkeit_modules')
			->values(
				[
					'id' => $this->db->quote(2),
					'display' => $this->db->quote('Prozedur'),
					'version' => $this->db->quote('1.0.0'),
				]
			)->executeStatement();

		// ontology concepts
		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(1),
				'display' => $this->db->quote('Gegenwärtiges chronologisches Alter'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(1),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(2),
				'display' => $this->db->quote('Geschlecht'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(1),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(3),
				'display' => $this->db->quote('Bildgebende Diagnostik'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(4),
				'display' => $this->db->quote('Andere bildgebende Verfahren'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(2),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(5),
				'display' => $this->db->quote('Elektrische Impedanztomographie'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(3),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(6),
				'display' => $this->db->quote('Elektroimpedanzspektroskopie der Haut'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(3),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(7),
				'display' => $this->db->quote('Knochendichtemessung (alle Verfahren)'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(3),
			]
		)->executeStatement();


		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(8),
				'display' => $this->db->quote('Radiofrequenzspektroskopie von Brustgewebe'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(3),
			],
		)->executeStatement();

		// ontology filters
		/* $query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(1),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'attributeDefinitions' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'timeRestrictionAllowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'valueDefinition' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'allowedUnits' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				
			]
		)->executeStatement(); */

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(1),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
			],
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(2),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'code' => $this->db->quote('a'),
				'display' => $this->db->quote('a'),
				'system' => $this->db->quote('http://unitsofmeasure.org'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(3),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'code' => $this->db->quote('a'),
				'display' => $this->db->quote('a'),
				'system' => $this->db->quote('http://unitsofmeasure.org'),
				'type' => $this->db->quote('quantity'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(4),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'code' => $this->db->quote('c'),
				'display' => $this->db->quote('a'),
				'system' => $this->db->quote('http://unitsofmeasure.org'),
				'optional' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(5),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'code' => $this->db->quote('a'),
				'display' => $this->db->quote('a'),
				'system' => $this->db->quote('http://unitsofmeasure.org'),
				'allowedUnits' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('quantity'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(6),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'code' => $this->db->quote('a'),
				'display' => $this->db->quote('a'),
				'system' => $this->db->quote('http://unitsofmeasure.org'),
				'allowedUnits' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('quantity'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(7),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'code' => $this->db->quote('a'),
				'display' => $this->db->quote('a'),
				'system' => $this->db->quote('http://unitsofmeasure.org'),
				'selectableConcepts' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'optional' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('quantity'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(2),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'code' => $this->db->quote('a'),
				'display' => $this->db->quote('a'),
				'system' => $this->db->quote('http://unitsofmeasure.org'),
				'allowedUnits' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'selectableConcepts' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'timeRestrictionAllowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'optional' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('quantity'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(2),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'code' => $this->db->quote('mo'),
				'display' => $this->db->quote('mo'),
				'system' => $this->db->quote('http://unitsofmeasure.org'),
				'allowedUnits' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'selectableConcepts' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'timeRestrictionAllowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'optional' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('quantity'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(3),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'code' => $this->db->quote('wk'),
				'display' => $this->db->quote('wk'),
				'system' => $this->db->quote('http://unitsofmeasure.org'),
				'allowedUnits' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'selectableConcepts' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'timeRestrictionAllowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'optional' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('quantity'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(4),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(1),
				'code' => $this->db->quote('d'),
				'display' => $this->db->quote('d'),
				'system' => $this->db->quote('http://unitsofmeasure.org'),
				'allowedUnits' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'selectableConcepts' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'timeRestrictionAllowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'optional' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('quantity'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(5),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(2),
				'code' => $this->db->quote('female'),
				'display' => $this->db->quote('Female'),
				'system' => $this->db->quote('http://hl7.org/fhir/administrative-gender'),
				'version' => $this->db->quote('4.0.1'),
				'allowedUnits' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'selectableConcepts' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'timeRestrictionAllowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'optional' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('concept'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(6),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(2),
				'code' => $this->db->quote('male'),
				'display' => $this->db->quote('Male'),
				'system' => $this->db->quote('http://hl7.org/fhir/administrative-gender'),
				'version' => $this->db->quote('4.0.1'),
				'allowedUnits' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'selectableConcepts' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'timeRestrictionAllowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'optional' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('concept'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(7),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(2),
				'code' => $this->db->quote('other'),
				'display' => $this->db->quote('Other'),
				'system' => $this->db->quote('http://hl7.org/fhir/administrative-gender'),
				'version' => $this->db->quote('4.0.1'),
				'allowedUnits' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'selectableConcepts' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'timeRestrictionAllowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'optional' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('concept'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(8),
				'module_id' => $this->db->quote(1),
				'concept_id' => $this->db->quote(2),
				'code' => $this->db->quote('unknown'),
				'display' => $this->db->quote('Unknown'),
				'system' => $this->db->quote('http://hl7.org/fhir/administrative-gender'),
				'version' => $this->db->quote('4.0.1'),
				'allowedUnits' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'selectableConcepts' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'timeRestrictionAllowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'optional' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type' => $this->db->quote('concept'),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filters')->values(
			[
				'id' => $this->db->quote(9),
				'module_id' => $this->db->quote(2),
				/* 'concept_id' => $this->db->quote(2), */
				'allowedUnits' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'selectableConcepts' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'timeRestrictionAllowed' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				/* 'optional' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL), */
				'type' => $this->db->quote('concept'),
			]
		)->executeStatement();
	}
}
