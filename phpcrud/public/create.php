<?php
session_start();
include('../config/connect.php');

$task = ['title' => '', 'description' => ''];
$isUpdate = (isset($_GET['update']) && $_GET['update'] == true && isset($_GET['id']));

if ($isUpdate) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT title, description FROM `tasks` WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($title, $descr);
    if ($stmt->fetch()) {
        $task['title'] = $title;
        $task['description'] = $descr;
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $updateMode = (isset($_POST['update']) && $_POST['update'] == "true" && isset($_POST['id']));

    if (empty($title) || empty($desc)) {
        $_SESSION["message"] = [
            "type" => "danger",
            "msg" => "Please fill title and description."
        ];
        if ($updateMode) {
            header("Location: ./create.php?update=true&id=" . intval($_POST['id']));
        } else {
            header("Location: ./create.php");
        }
        exit;
    }


    if ($updateMode) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE `tasks` SET title=?, description=? WHERE id=?");
        $stmt->bind_param("ssi", $title, $desc, $id);
        $success = $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = [
            'type' => $success ? 'success' : 'danger',
            'msg' => $success ? 'Task updated successfully.' : 'Failed to update task.'
        ];
        header("Location: ./index.php");
        exit;
    } else {
        $stmt = $conn->prepare("INSERT INTO `tasks`(`title`,`description`) VALUES(?,?)");
        if ($stmt) {
            $stmt->bind_param("ss", $title, $desc);
            if ($stmt->execute()) {
                $_SESSION["message"] = [
                    "type" => "success",
                    "msg" => "Task created successfully."
                ];
            } else {
                $_SESSION["message"] = [
                    "type" => "danger",
                    "msg" => "Unable to create task, try again!"
                ];
            }
            $stmt->close();
        }
        header("Location: ./index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>

    </title>
    <link rel="stylesheet" href="../styles/forms.css">
    <style>
        .hide {
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="container">

        <h1>
            <?php
            if (isset($_GET['update']) && $_GET['update'] == true) {
                echo "update task";
            } else {
                echo "create task";
            }
            ?>
        </h1>
        <?php
        if (isset($_SESSION["message"])) {
            $type = $_SESSION["message"]["type"];
            $msg = $_SESSION["message"]["msg"];
            echo "<div id='alertBox' class='alert alert-{$type}'>" . htmlspecialchars($msg) . "</div>";
            unset($_SESSION["message"]);
        }
        ?>
        <form action="create.php" method="POST" class="task-form">
            <?php if ($isUpdate): ?>
                <input type="hidden" name="update" value="true">
                <input type="hidden" name="id" value="<?php echo intval($_GET['id']); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="title">Task Title</label>
                <input type="text" name="title" value="<?php echo $task['title'] ?>" id="title" placeholder="Enter task title">
            </div>

            <div class="form-group">
                <label for="description">Task Description</label>
                <textarea name="description" id="description" rows="4" placeholder="Enter task description"><?php echo htmlspecialchars($task['description']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" name="submit">
                    <?php
                    if (isset($_GET['update']) && $_GET['update'] == true) {
                        echo "update";
                    } else {
                        echo "create task";
                    }
                    ?>
                </button>
                <a href="index.php" class="back-link">← Back to List</a>
            </div>
        </form>
    </div>
</body>
<script>
    // Make alert disappear after 3 seconds
    setTimeout(() => {
        const alertBox = document.getElementById('alertBox');
        if (alertBox) {
            alertBox.classList.add('hide');
        }
    }, 3000);
</script>

</html>