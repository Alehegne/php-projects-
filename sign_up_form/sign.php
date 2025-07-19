<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include 'connect.php';
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($name) || empty($password)) {
        $_SESSION['feedback'] = [
            'type' => 'danger',
            'msg' => 'Please provide both name and password.'
        ];
        header('Location: sign.php');
        exit;
    }

    //check wether user name is taken or not
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `Registration` WHERE `Username`=?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $_SESSION['feedback'] = [
            'type' => 'danger',
            'msg' => "user name already taken, try another one"
        ];
        header("Location:sign.php");
        exit;
    }


    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO `registration` (`username`, `password`) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $name, $hashedPassword);
        if ($stmt->execute()) {
            $_SESSION['feedback'] = [
                'type' => 'success',
                'msg' => 'User registration successful.'
            ];
            header('Location:./login.php');
            exit;
        } else {
            $_SESSION['feedback'] = [
                'type' => 'danger',
                'msg' => 'Registration failed: ' . $stmt->error
            ];
        }
        $stmt->close();
        header('Location: sign.php');
        exit;
    } else {
        $_SESSION['feedback'] = [
            'type' => 'danger',
            'msg' => 'Database error: ' . $conn->error
        ];
        header('Location: sign.php');
        exit;
    }
}


?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>sign up registration form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center">Sign Up Page</h1>
        <?php
        if (isset($_SESSION['feedback'])) {
            $type = $_SESSION['feedback']['type'];
            $msg = $_SESSION['feedback']['msg'];
            echo "<div class='alert alert-$type alert-dismissible fade show' role='alert'>";
            echo htmlspecialchars($msg);
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            unset($_SESSION['feedback']);
        }
        ?>
        <form action="sign.php" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input name="name" type="text" class="form-control" id="name" aria-describedby="name help">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input name="password" type="password" class="form-control" id="password">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Remember the password</label>
            </div>
            <div class="d-flex flex-row">
                <button type="submit" class="btn btn-primary mr-10">sign in</button>
                <a href="./login.php">log in</a>
            </div>

        </form>
    </div>
</body>

</html>