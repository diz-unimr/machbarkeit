export async function login() {

    const res = await fetch("https://machbarkeit.diz.uni-marburg.de/login?next=http://localhost:8080/index.php/apps/machbarkeit", {
        credentials: "include",
        redirect: "manual"
    })

    console.log("Login request", res)

    if (res.type === "opaqueredirect") {
        window.location.href = res.url
    }

}