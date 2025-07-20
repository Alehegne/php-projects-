<?php
include('../config/connect.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $status = isset($_POST['status']) && $_POST['status'] === 'completed' ? 'completed' : 'not_completed';
    $stmt = $conn->prepare("UPDATE `tasks` SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}
