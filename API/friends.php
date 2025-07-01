<?php
header('Content-Type: application/json');
include '../config/db.php';

if ($_GET['action'] === 'list_users') {
  $userId = $_GET['user_id'];
  $stmt = $pdo->prepare("
    SELECT id, firstname, lastname FROM users
    WHERE id != ?
    AND id NOT IN (
      SELECT friend_id FROM friends WHERE user_id = ?
      UNION
      SELECT user_id FROM friends WHERE friend_id = ?
    )
  ");
  $stmt->execute([$userId, $userId, $userId]);
  echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  exit;
}

if ($_GET['action'] === 'send') {
  $data = json_decode(file_get_contents("php://input"), true);
  $stmt = $pdo->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'pending')");
  $stmt->execute([$data['user_id'], $data['friend_id']]);
  echo json_encode(["success" => true]);
  exit;
}

if ($_GET['action'] === 'list_invites') {
  $userId = $_GET['user_id'];
  $stmt = $pdo->prepare("
    SELECT f.id, u.firstname, u.lastname
    FROM friends f
    JOIN users u ON u.id = f.user_id
    WHERE f.friend_id = ? AND f.status = 'pending'
  ");
  $stmt->execute([$userId]);
  echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  exit;
}

if ($_GET['action'] === 'accept') {
  $data = json_decode(file_get_contents("php://input"), true);
  $pdo->prepare("UPDATE friends SET status='accepted' WHERE id=?")
      ->execute([$data['invite_id']]);
  echo json_encode(["success" => true]);
  exit;
}

if ($_GET['action'] === 'refuse') {
  $data = json_decode(file_get_contents("php://input"), true);
  $pdo->prepare("UPDATE friends SET status='refused' WHERE id=?")
      ->execute([$data['invite_id']]);
  echo json_encode(["success" => true]);
  exit;
}