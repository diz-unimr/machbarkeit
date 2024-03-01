<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Service;

header('Content-Type: application/json; charset=UTF-8');
class MachbarkeitService {
	public function readCsv() {
		$csvFile = file_get_contents(__DIR__ .'/../../csvfile/diz_metadaten.csv');
		$csvArray = explode("\n", $csvFile);
		$result = array_map("str_getcsv", $csvArray);
		/* $headers = $result[0];
		$jsonArray = array();
		$rowCount = count($result);
		for ($i=1; $i<$rowCount; $i++) {
			foreach ($result[$i] as $key => $column) {
				$jsonArray[$i][$headers[$key]] = $column;
			}
		} */
		//$jsonObject = json_encode($jsonArray);

		return $csvFile;
	}


	public function readCsv1() {
		$csvFile = fopen(__DIR__ .'/../../csvfile/diz_metadaten.csv', 'r');
		
		
		
		$data = [];
		// Read each line of the CSV file until the end
		while (($row = fgets($csvFile)) !== false) {
			// Process the row data
			$data[] = $row;
		}

		/* $arr = [];
		foreach($arr as $i => $csvFile) {
			$arr[$i] = json_encode($data);
		} */
		fclose($csvFile);

		/* $keys = array_shift($data);
		$data2 = [];
		foreach ($data as $row) {
			$data2[] = array_combine($keys, $row);
		} */

		return $data;
	}
}
