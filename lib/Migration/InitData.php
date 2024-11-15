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

	public function get_metadata($file_name) {
		$data = [];
		$file = fopen(__DIR__ . '/../../csvfile/' . $file_name, 'r');
		while (($row = fgetcsv($file)) !== false) {
			$data[] = $row;
		}

		$headers = $data[0];
		$jsonArray = [];
		$rowCount = count($data);
		for ($i = 1; $i < $rowCount; $i++) {
			foreach ($data[$i] as $key => $column) {
				$jsonArray[$i][$headers[$key]] = $column;
				$jsonArray[$i]['filter_options'] = str_replace('""', '"', $jsonArray[$i]['filter_options']);
			}
		}
		fclose($file);
		return $jsonArray;
	}

	protected function storeInitialData() {
		$this->logger->info('Loading ontology data', ['app' => 'Machbarkeit']);
		$query = $this->db->getQueryBuilder();

		// add modules
		$module_data = $this->get_metadata('module_name.csv');

		for ($i = 1; $i <= count($module_data); $i++) {
			$query
				->insert('machbarkeit_modules')
				->values(
					[
						'id' => $this->db->quote($module_data[$i]['id']),
						'module_name' => $this->db->quote($module_data[$i]['module_name']),
						'version' => $this->db->quote($module_data[$i]['version']),
					]
				)->executeStatement();
		}

		// add concept
		$concept_data = $this->get_metadata('concept.csv');

		for ($i = 1; $i <= count($concept_data); $i++) {
			$query
				->insert('machbarkeit_concepts')
				->values(
					[
						'id' => $this->db->quote($concept_data[$i]['id']),
						'display' => $this->db->quote($concept_data[$i]['display']),
						'code' => $this->db->quote($concept_data[$i]['code']),
						'code_system' => $this->db->quote($concept_data[$i]['code_system']),
						'selectable' => $this->db->quote($concept_data[$i]['selectable']),
						'leaf' => $this->db->quote($concept_data[$i]['leaf']),
						'time_restriction_allowed' => $this->db->quote($concept_data[$i]['time_restriction_allowed']),
						'filter_name' => $this->db->quote($concept_data[$i]['filter_name']),
						'filter_type' => $this->db->quote($concept_data[$i]['filter_type']),
						'filter_options' => $query->createNamedParameter(json_encode(json_decode(strval($concept_data[$i]['filter_options'])))),
						'module_id' => $this->db->quote($concept_data[$i]['module_id']),
					]
				)->executeStatement();
		}

		// ontology filter_options
		/* $query->insert('machbarkeit_filter_options')->values(
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
			]
		)->executeStatement(); */
	}
}
