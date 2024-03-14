<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Service;

class MachbarkeitService {
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

	public function readOntology() {
		$json_file = file_get_contents(__DIR__ . '/../../ontology/ui_trees/Bioprobe.json');
		$data = json_decode($json_file, true);
		return($data);
	}
}
