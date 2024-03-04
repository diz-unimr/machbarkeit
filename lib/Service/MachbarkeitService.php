<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Service;

class MachbarkeitService {
	public function readCsv() {
		$result = array_map("str_getcsv", file(__DIR__ . '/../../csvfile/diz_metadaten.csv'));
		$headers = $result[0];

		$jsonArray = [];
		$rowCount = count($result);
		for ($i = 1; $i < $rowCount; $i++) {
			foreach ($result[$i] as $key => $column) {
				$jsonArray[$i][$headers[$key]] = $column;
			}
		}
		return array_values($jsonArray);
	}
}
