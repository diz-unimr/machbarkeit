/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/

export async function login() {

    const loginUrl = process.env.BACKEND_API_BASE + "/login?next=" + process.env.BASE_URL + "/index.php/apps/machbarkeit"
    const res = await fetch(loginUrl, {
        credentials: "include",
        redirect: "manual"
    })

    console.log("Login request", res)

    if (res.type === "opaqueredirect") {
        window.location.href = res.url
    }

}