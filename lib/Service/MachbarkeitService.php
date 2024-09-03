<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Service;

use OCA\Machbarkeit\Db\ModuleMapper;

class MachbarkeitService
{
	private $mapper;

	public function __construct(ModuleMapper $mapper)
	{
		$this->mapper = $mapper;
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
		return $this->mapper->findModules();
	}
}
