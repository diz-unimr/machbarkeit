export async function login() {

    const loginUrl = process.env.BACKEND_API_BASE + "/login?next=http://localhost:8080/index.php/apps/machbarkeit"
    const res = await fetch(loginUrl, {
        credentials: "include",
        redirect: "manual"
    })

    console.log("Login request", res)

    if (res.type === "opaqueredirect") {
        window.location.href = res.url
    }

}