<?php include('../config/connect.php'); ?>
<?php include('../partials/header.php'); ?>


<?php
session_start();
$search = $_GET['search'] ?? '';
if (isset($_SESSION["message"])) {
    $type = $_SESSION["message"]["type"];
    $msg = $_SESSION["message"]["msg"];
    echo "<div id='alertBox' class='alert alert-{$type}'>" . htmlspecialchars($msg) . "</div>";
    unset($_SESSION["message"]);
}

//delete the tasks
if (isset($_GET['delete']) && $_GET['delete'] == true && $_GET['id']) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("delete from `tasks` where id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION["message"] = [
            "type" => "danger",
            "msg" => "task deleted successfully"
        ];
    } else {
        $_SESSION["message"] = [
            "type" => "danger",
            "msg" => "failed to delete"
        ];
    }
    $stmt->close();
    header("Location:index.php");
    exit;
}
?>
<a href="create.php" class="button new">Add New Task</a>

<?php
//fetch tasks

$showCompleted = isset($_GET['show_completed']) && $_GET['show_completed'] == '1';
if ($showCompleted) {
    $stmt = $conn->prepare("select * from `tasks` where (title like ? or description like ?) and status='completed' order by created_at desc");
} else {
    $stmt = $conn->prepare("select * from `tasks` where title like ? or description like ? order by created_at desc");
}
$searchLike = "%$search%";
$stmt->bind_param("ss", $searchLike, $searchLike);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php if ($result->num_rows === 0): ?>
    <div>No tasks found</div>
<?php else: ?>
    <div>
        <div style="display: flex;gap:4rem; margin-top: 1rem;margin-bottom: 1rem;">
            <h2 style="margin: 0;">Task list</h2>
            <a href="?show_completed=1" style="border-radius: 10px;background-color: white; cursor: pointer; padding: 6px 12px; text-decoration: none;">Show Completed Tasks</a>
            <a href="index.php" style="border-radius: 10px;background-color: white; cursor: pointer; padding: 6px 12px; text-decoration: none;">Show All Tasks</a>
            <form method='get' style='display:flex;gap:1rem;'>
                <input style="border-radius: 10px;padding: 2px;" type='text' name='search' placeholder='Search tasks...' value='<?php echo htmlspecialchars($search) ?>' />
                <button style="border-radius:10px" type='submit'>Search</button>
            </form>
        </div>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class='task-item' style='margin-bottom:1rem; padding:1rem; border:1px solid #ddd; border-radius:8px;'>
                <div style="display: flex; gap: 1rem;justify-content: space-between;">
                    <h3 style="margin: 2px;"><?= htmlspecialchars($row['title']) ?> </h3>
                    <form action="./mark.php" method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <label>
                            <input style="width: 20px; height: 20px;" type="checkbox" name="status" value="completed" <?= $row['status'] == 'completed' ? 'checked' : '' ?> onchange="this.form.submit()">
                        </label>
                    </form>
                </div>
                <p><?= htmlspecialchars($row['description']) ?> </p>
                <small>Created at: <?= htmlspecialchars($row['created_at']) ?> </small>
                <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                    <div style="justify-content: space-between;">
                        <a href='index.php?id=" <?= $row['id'] ?> " &trash=true' style='margin-right:1rem;background-color:gray;font-size:1rem;border:1px;border-color:gray;border-radius:5px;padding:5px;text-decoration:none;color:white;margin-left:10px;'>
                            <i class="fa-solid fa-trash"></i>trash

                        </a>
                        <a href='create.php?id=" <?= $row['id'] ?> "&update=true' style='margin-right:1rem;background-color:green;font-size:1rem;border:1px;border-color:gray;border-radius:5px;padding:5px;text-decoration:none;color:white;'>update</a>
                    </div>
                    <a href='index.php?id=" <?= $row['id'] ?> " &delete=true' style='margin-right:1rem;background-color:red;font-size:1rem;border:1px;border-color:gray;border-radius:5px;padding:5px;text-decoration:none;color:white;margin-left:10px;'>Delete</a>

                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>
<?php
$stmt->close();
include('../partials/footer.php');
?>