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
		$headers = $result[0];
		$jsonArray = array();
		$rowCount = count($result);
		for ($i=1; $i<$rowCount; $i++) {
			foreach ($result[$i] as $key => $column) {
				$jsonArray[$i][$headers[$key]] = $column;
			}
		}
		//$jsonObject = json_encode($jsonArray, JSON_PRETTY_PRINT);

		return $jsonArray;
	}
}
