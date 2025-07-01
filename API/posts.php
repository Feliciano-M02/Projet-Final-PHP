<?php
header('Content-Type: application/json');
include '../config/db.php';

// liste des posts
if ($_GET['action'] === 'list') {
    $stmt = $pdo->query("
        SELECT p.*, u.firstname, u.lastname, u.avatar,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND is_like=1) AS like_count,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = 1 AND is_like=1) AS liked_by_user
        FROM posts p
        JOIN users u ON u.id = p.user_id
        ORDER BY p.created_at DESC
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($posts);
    exit;
}

// gérer les likes
if ($_GET['action'] === 'like') {
    $data = json_decode(file_get_contents("php://input"), true);
    $postId = $data['post_id'];
    $userId = $data['user_id'];
    $liked = $data['liked'];

    // supprimer l'ancien
    $pdo->prepare("DELETE FROM likes WHERE post_id=? AND user_id=?")
        ->execute([$postId, $userId]);

    if (!$liked) {
        $pdo->prepare("INSERT INTO likes (post_id, user_id, is_like) VALUES (?, ?, 1)")
            ->execute([$postId, $userId]);
    }
    echo json_encode(["success" => true]);
    exit;
}

// récupérer les commentaires
if ($_GET['action'] === 'comments') {
    $postId = $_GET['post_id'];
    $stmt = $pdo->prepare("
        SELECT c.*, u.firstname, u.lastname
        FROM comments c
        JOIN users u ON u.id = c.user_id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$postId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ajouter un commentaire
if ($_GET['action'] === 'add_comment') {
    $data = json_decode(file_get_contents("php://input"), true);
    $pdo->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)")
        ->execute([$data['post_id'], $data['user_id'], $data['comment']]);
    echo json_encode(["success" => true]);
    exit;
}