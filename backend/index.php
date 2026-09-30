<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

require_once "db.php";

$method = $_SERVER["REQUEST_METHOD"];
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

header("Content-Type: application/json");

require_once "db.php";

$method = $_SERVER["REQUEST_METHOD"];
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);


/*
|--------------------------------------------------------------------------
| GET /
|--------------------------------------------------------------------------
*/

if ($method === "GET" && $path === "/") {

    echo json_encode([
        "application" => "SQL Injection Demo",
        "version" => "1.0.0",
        "status" => "running"
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| GET /health
|--------------------------------------------------------------------------
*/

if ($method === "GET" && $path === "/health") {

    if ($conn->ping()) {

        http_response_code(200);

        echo json_encode([
            "status" => "healthy",
            "database" => "connected"
        ]);

    } else {

        http_response_code(503);

        echo json_encode([
            "status" => "unhealthy",
            "database" => "disconnected"
        ]);
    }

    exit;
}


/*
|--------------------------------------------------------------------------
| POST /login
|--------------------------------------------------------------------------
*/

if ($method === "POST" && $path === "/login") {

    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    $username = $input["username"] ?? "";
    $password = $input["password"] ?? "";
    $mode = $input["mode"] ?? "vulnerable";


    /*
    |--------------------------------------------------------------------------
    | Vulnerable mode
    |--------------------------------------------------------------------------
    */

    if ($mode === "vulnerable") {

        $query =
            "SELECT id, username, role
             FROM users
             WHERE username = '$username'
             AND password = '$password'";

        $result = $conn->query($query);

        if ($result === false) {

            echo json_encode([
                "success" => false,
                "mode" => "vulnerable",
                "query" => $query,
                "error" => $conn->error
            ]);

            exit;
        }

        if ($result->num_rows > 0) {

            $user = $result->fetch_assoc();

            echo json_encode([
                "success" => true,
                "mode" => "vulnerable",
                "message" => "Login successful",
                "user" => $user,
                "query" => $query
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "mode" => "vulnerable",
                "message" => "Invalid username or password",
                "query" => $query
            ]);
        }

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Secure mode
    |--------------------------------------------------------------------------
    */

    if ($mode === "secure") {

        $query =
            "SELECT id, username, role
             FROM users
             WHERE username = ?
             AND password = ?";

        $stmt = $conn->prepare($query);

        if (!$stmt) {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "Failed to prepare query"
            ]);

            exit;
        }

        $stmt->bind_param(
            "ss",
            $username,
            $password
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $user = $result->fetch_assoc();

            echo json_encode([
                "success" => true,
                "mode" => "secure",
                "message" => "Login success",
                "user" => $user,
                "query" => $query
            ]);

        } else {

            echo json_encode([
                "success" => false,
                "mode" => "secure",
                "message" => "Invalid username or password",
                "query" => $query
            ]);
        }

        $stmt->close();

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| 404
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo json_encode([
    "error" => "Endpoint not found"
]);
