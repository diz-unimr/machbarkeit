/* SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
SPDX-License-Identifier: AGPL-3.0-or-later */

export const useDebounce = function(func, delay = 300) {
	let timeoutId

	return function(...args) {
		clearTimeout(timeoutId)
		timeoutId = setTimeout(() => func.apply(this, args), delay)
	}
}
