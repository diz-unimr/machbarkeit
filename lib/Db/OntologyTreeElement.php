<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use JsonSerializable;

class OntologyTreeElement implements JsonSerializable {
	public int $id;
	public string $display = '';
	public ?int $module_id = null;
	public array $children = [];
	public bool $selectable = false;
	public bool $leaf = false;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'display' => $this->display,
			'module_id' => $this->module_id,
			'parent_id' => $this->children,
			'selectable' => $this->selectable,
			'leaf' => $this->leaf
		];
	}
}
