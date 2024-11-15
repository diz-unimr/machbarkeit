<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Service;

use OCA\Machbarkeit\Db\FilterMapper;
use OCA\Machbarkeit\Db\ModuleMapper;
use OCA\Machbarkeit\Db\OntologyConceptMapper;
use Dotenv\Dotenv;

class MachbarkeitService
{
	private $moduleMapper;
	private $ontologyConceptMapper;
	private $filterMapper;

	public function __construct(ModuleMapper $moduleMapper, OntologyConceptMapper $ontologyConceptMapper, FilterMapper $filterMapper)
	{
		$this->moduleMapper = $moduleMapper;
		$this->ontologyConceptMapper = $ontologyConceptMapper;
		$this->filterMapper = $filterMapper;
	}

	public function readCsv()
	{
		$file = fopen(__DIR__ . '/../../csvfile/diz_metadaten.csv', 'r');
		$data = [];
		/* fgetcsv() parses the line it reads for fields in CSV format and returns an array containing the fields read. */
		while (($row = fgetcsv($file)) !== false) {
			$data[] = $row;
		}

		$headers = $data[0];
		$jsonArray = [];
		$rowCount = count($data);
		for ($i = 1; $i < $rowCount; $i++) {
			foreach ($data[$i] as $key => $column) {
				$jsonArray[$i][$headers[$key]] = $column;
			}
		}

		fclose($file);
		return array_values($jsonArray);
	}

	public function readOntology()
	{
		$json_files = [
			'Person.json',
			// 'test.json',
			// 'Diagnose.json',
			'test.json',
			// 'Laboruntersuchung.json',
			'Prozedur.json'
		];
		$merged_file = [];

		foreach ($json_files as $file) {
			// Read JSON file contents
			$json_content = file_get_contents(__DIR__ . '/../../ontology/ui_trees/' . $file);
			// Decode JSON content into associative array (decode string to json)
			$json_data = json_decode($json_content, true);
			// Merge data from current file into merged_file array
			$merged_file = array_merge($merged_file, [$json_data]);
		};

		return $merged_file;
	}

	public function readUiProfile()
	{
		$ui_profile = file_get_contents(__DIR__ . '/../../ontology/ui_profile.json');
		$json_ui_profile = json_decode($ui_profile, true);
		return $json_ui_profile;
	}

	public function getModules()
	{
		return $this->moduleMapper->findModules();
	}

	public function getConcepts($moduleId)
	{
		return $this->ontologyConceptMapper->find($moduleId);
	}

	public function getOntology($moduleId)
	{
		return $this->ontologyConceptMapper->findAll($moduleId);
	}

	public function buildTree(array $elements, $id)
	{
		$branch = [];
		foreach ($elements as $element) {
			if ($element->parentId == $id) {
				$children = $this->buildTree($elements, $element->id);
				if ($children) {
					$element = (array)$element;
					$element['children'] = $children;
				}

				$branch[] = $element; // Append the object to the branch
			}
		}
		return $branch;
	}

	public function getSearchOntology(string $textSeach, int $module_id)
	{
		return $this->ontologyConceptMapper->searchOntology($textSeach, $module_id);
	}

	public function getFilters($filter_options_ids)
	{
		return $this->filterMapper->filters($filter_options_ids);
	}

	public function getFhirRequest($criteria)
	{
		require_once __DIR__ . '/../../vendor/autoload.php';
		$dotenv =  Dotenv::createImmutable(__DIR__);
		$dotenv->load();
		//$username = $_ENV['FHIR_USERNAME'];
		//$password = $_ENV['FHIR_PASSWORD'];

		$data = json_decode('{
			"version": "http://to_be_decided.com/draft-1/schema#",
			"display": "",
			"inclusionCriteria": [
				[
					{
						"termCodes": [
							{
								"code": "263495000",
								"system": "http://snomed.info/sct",
								"display": "Geschlecht"
							}
						],
						"context": {
							"code": "Patient",
							"system": "fdpg.mii.cds",
							"version": "1.0.0",
							"display": "Patient"
						},
						"valueFilter": {
							"selectedConcepts": [
								{
									"code": "female",
									"display": "Female",
									"system": "http://hl7.org/fhir/administrative-gender"
								}
							],
							"type": "concept"
						}
					}
				]
			]
		}', true);

		// Initialize cURL session
		$url = 'http://feasibility.diz.uni-marburg.de:8084/query/execute';
		$ch = curl_init($url);

		// Set options for cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // Use Basic Authentication
		//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); // Set the username and password
		curl_setopt($ch, CURLOPT_POST, true); // Set request method to POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Attach the data as JSON in the body
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json', // Set content type to JSON
		]);

		// Execute the request
		$response = curl_exec($ch);
		// Close the cURL session
		curl_close($ch);
		return $response;
	}
}
