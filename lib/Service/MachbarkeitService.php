<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Service;

use SebastianBergmann\RecursionContext\Context;

class MachbarkeitService {
	public function readOntology() {
		$json_files = [
			'Bioprobe.json', 
			'Diagnose.json', 
			'Einwilligung.json', 
			'Laboruntersuchung.json', 
			'Medikamentenverabreichung.json', 
			'Person.json', 
			'Prozedur.json'
		];
		// 'Prozedur.json', 'Diagnose.json', 'Medikamentenverabreichung.json',

		/* $json_bioprobe = file_get_contents(__DIR__ . '/../../ontology/ui_trees/Bioprobe.json');
		$json_diagnose = file_get_contents(__DIR__ . '/../../ontology/ui_trees/Diagnose.json');
		$json_einwilligung = file_get_contents(__DIR__ . '/../../ontology/ui_trees/Einwilligung.json');
		$json_laboruntersuchung = file_get_contents(__DIR__ . '/../../ontology/ui_trees/Laboruntersuchung.json');
		$json_medikamentenverabreichung = file_get_contents(__DIR__ . '/../../ontology/ui_trees/Medikamentenverabreichung.json');
		$json_person = file_get_contents(__DIR__ . '/../../ontology/ui_trees/Person.json');
		$json_prozedur = file_get_contents(__DIR__ . '/../../ontology/ui_trees/Prozedur.json');

		$json_prozedur = json_decode($json_bioprobe, true);
		$json_diagnose = json_decode($json_diagnose, true);
		$merged_file = array_merge(array($json_bioprobe), array($json_diagnose)); */

		$merged_file = [];

		foreach ($json_files as $file) {
			// Read JSON file contents
			$json_content = file_get_contents(__DIR__ . '/../../ontology/ui_trees/'.$file);
			// Decode JSON content into associative array
			$json_data = json_decode($json_content, true);
			// Merge data from current file into merged_file array
			$merged_file = array_merge($merged_file, array($json_data));
		};

		return $merged_file;
	}
	
	public function readCsv() {
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
}
