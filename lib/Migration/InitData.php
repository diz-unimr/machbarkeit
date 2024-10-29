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

class InitData implements IRepairStep {

	/** @var LoggerInterface */
	protected $logger;
	/** @var IConfig */
	protected $config;
	/** @var IDBConnection */
	protected $db;

	public function __construct(LoggerInterface $logger, IConfig $config, IDBConnection $db) {
		$this->logger = $logger;
		$this->db = $db;
		$this->config = $config;
	}

	/**
	 * Returns the step's name
	 */
	public function getName() {
		return 'Init data step!';
	}

	/**
	 * @param IOutput $output
	 */
	public function run(IOutput $output) {
		$this->storeInitialData();
		/* $previousVersion = $this->config->getAppValue('machbarkeit', 'installed_version', false);
		if (!$previousVersion) {
			$this->storeInitialData();
		} */
	}

	protected function storeInitialData() {
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

		// ontology filter_options
		$query->insert('machbarkeit_filter_options')->values(
			[
				'id' => $this->db->quote(1),
				'filter_name' => $this->db->quote('Wertbereich'),
				'type' => $this->db->quote('quantity'),
				'filter_options' => $query->createNamedParameter(json_encode(json_decode('[
					{
						"code": "a",
						"display": "a",
						"system": "http://unitsofmeasure.org",
						"version": null
					},
					{
						"code": "mo",
						"display": "mo",
						"system": "http://unitsofmeasure.org",
						"version": null
					},
					{
						"code": "wk",
						"display": "wk",
						"system": "http://unitsofmeasure.org",
						"version": null
					},
					{
						"code": "d",
						"display": "d",
						"system": "http://unitsofmeasure.org",
						"version": null
					}
	
				]', true)), IQueryBuilder::PARAM_STR),
				'optional' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
			]
		)->executeStatement();

		$query->insert('machbarkeit_filter_options')->values(
			[
				'id' => $this->db->quote(2),
				'filter_name' => $this->db->quote('Wertbereich'),
				'type' => $this->db->quote('concept'),
				'filter_options' => $query->createNamedParameter(json_encode(json_decode('[
					{
						"code": "female",
						"display": "Female",
						"system": "http://hl7.org/fhir/administrative-gender",
						"version": "4.0.1"
					},
					{
						"code": "male",
						"display": "Male",
						"system": "http://hl7.org/fhir/administrative-gender",
						"version": "4.0.1"
					},
					{
						"code": "other",
						"display": "Other",
						"system": "http://hl7.org/fhir/administrative-gender",
						"version": "4.0.1"
					},
					{
						"code": "unknown",
						"display": "Unknown",
						"system": "http://hl7.org/fhir/administrative-gender",
						"version": "4.0.1"
					}
				]', true)), IQueryBuilder::PARAM_STR),
				'optional' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
			]
		)->executeStatement();

		// ontology concepts
		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(1),
				'display' => $this->db->quote('Gegenwärtiges chronologisches Alter'),
				'term_codes' => $query->createNamedParameter(json_encode(json_decode('
						{
							"code": "424144002",
							"display": "Gegenw\u00e4rtiges chronologisches Alter",
							"system": "http://snomed.info/sct"
						}'))),
				/* 'term_code' => $query->createNamedParameter(json_encode([
					"code" => $this->db->quote('424144002'),
					"display" => $this->db->quote('Gegenw\u00e4rtiges chronologisches Alter'),
					"system" => $this->db->quote('http://snomed.info/sct'),
				]), IQueryBuilder::PARAM_STR), */
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'time_restriction_allowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type_quantity' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'filter_options_id' => $this->db->quote(1),
				'module_id' => $this->db->quote(1),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(2),
				'display' => $this->db->quote('Geschlecht'),
				'term_codes' => $query->createNamedParameter(json_encode(json_decode('
						{
							"code": "263495000",
							"display": "Geschlecht",
							"system": "http://snomed.info/sct"
						}'))),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'time_restriction_allowed' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'type_quantity' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'filter_options_id' => $this->db->quote(2),
				'module_id' => $this->db->quote(1),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(3),
				'display' => $this->db->quote('Bildgebende Diagnostik'),
				'term_codes' => $query->createNamedParameter(json_encode(json_decode('
						{
							"code": "263495000",
							"display": "Geschlecht",
							"system": "http://snomed.info/sct"
						}'))),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'time_restriction_allowed' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'type_quantity' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(4),
				'display' => $this->db->quote('Andere bildgebende Verfahren'),
				'term_codes' => $query->createNamedParameter(json_encode(json_decode('
						{
							"code": "3-90",
							"display": "Andere bildgebende Verfahren",
							"system": "http://fhir.de/CodeSystem/bfarm/ops",
							"version": "2023"
						}'))),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(3),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(5),
				'display' => $this->db->quote('Elektrische Impedanztomographie'),
				'term_codes' => $query->createNamedParameter(json_encode(json_decode('
						{
							"code": "3-903",
							"display": "Elektrische Impedanztomographie",
							"system": "http://fhir.de/CodeSystem/bfarm/ops",
							"version": "2023"
						}'))),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(4),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(6),
				'display' => $this->db->quote('Elektroimpedanzspektroskopie der Haut'),
				'term_codes' => $query->createNamedParameter(json_encode(json_decode('
						{
							"code": "3-901",
							"display": "Elektroimpedanzspektroskopie der Haut",
							"system": "http://fhir.de/CodeSystem/bfarm/ops",
							"version": "2023"
						}'))),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(4),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(7),
				'display' => $this->db->quote('Knochendichtemessung (alle Verfahren)'),
				'term_codes' => $query->createNamedParameter(json_encode(json_decode('
						{
							"code": "3-900",
							"display": "Knochendichtemessung (alle Verfahren)",
							"system": "http://fhir.de/CodeSystem/bfarm/ops",
							"version": "2023"
						}'))),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(4),
			]
		)->executeStatement();


		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(8),
				'display' => $this->db->quote('Radiofrequenzspektroskopie von Brustgewebe'),
				'term_codes' => $query->createNamedParameter(json_encode(json_decode('
						{
							"code": "3-902",
							"display": "Radiofrequenzspektroskopie von Brustgewebe",
							"system": "http://fhir.de/CodeSystem/bfarm/ops",
							"version": "2023"
						}'))),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'leaf' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(4),
			],
		)->executeStatement();
	}
}
