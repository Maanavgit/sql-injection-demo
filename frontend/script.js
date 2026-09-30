async function login() {

    const username =
        document.getElementById("username").value;

    const password =
        document.getElementById("password").value;

    const mode =
        document.getElementById("mode").value;

    const result =
        document.getElementById("result");

    result.textContent = "Sending request...";

    try {

        const response = await fetch(
            "http://localhost:8086/login",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    username: username,
                    password: password,
                    mode: mode
                })
            }
        );

        const data = await response.json();

        result.textContent =
            JSON.stringify(data, null, 4);

    } catch (error) {

        result.textContent =
            "Request failed:\n\n" +
            error.message;
    }
}
