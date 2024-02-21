<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Service;

class MachbarkeitService {
	public function readCsv(): string {
		return $test = 'TEST';
		/* $csvFile = fopen('../csvfile/diz_metadaten.csv', 'r');
		if($csvFile === TRUE){
			return $test = 'Test';
		} */
	}
}
