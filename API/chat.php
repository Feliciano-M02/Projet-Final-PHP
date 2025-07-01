<?php
header('Content-Type: application/json');
include '../config/db.php';

// liste des amis
if ($_GET['action'] === 'list_friends') {
    $userId = $_GET['user_id'];
    $stmt = $pdo->prepare("
        SELECT u.id, u.firstname, u.lastname
        FROM friends f
        JOIN users u ON (u.id = f.friend_id OR u.id = f.user_id)
        WHERE (f.user_id = ? OR f.friend_id = ?)
        AND f.status = 'accepted'
        AND u.id != ?
        GROUP BY u.id
    ");
    $stmt->execute([$userId, $userId, $userId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// démarrer une conversation
if ($_GET['action'] === 'start_conversation') {
    $user1 = $_GET['user_id'];
    $user2 = $_GET['friend_id'];
    $stmt = $pdo->prepare("
        SELECT id FROM conversations
        WHERE (user1_id=? AND user2_id=?) OR (user1_id=? AND user2_id=?)
    ");
    $stmt->execute([$user1, $user2, $user2, $user1]);
    $conv = $stmt->fetch();
    if ($conv) {
        $conversation_id = $conv['id'];
    } else {
        $pdo->prepare("INSERT INTO conversations (user1_id, user2_id) VALUES (?,?)")
            ->execute([$user1, $user2]);
        $conversation_id = $pdo->lastInsertId();
    }
    echo json_encode(["conversation_id"=>$conversation_id]);
    exit;
}

// récupérer messages
if ($_GET['action'] === 'get_messages') {
    $convId = $_GET['conversation_id'];
    $stmt = $pdo->prepare("
        SELECT m.*, u.firstname, u.lastname
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE conversation_id = ?
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([$convId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// envoyer message
if ($_GET['action'] === 'send') {
    $convId = $_POST['conversation_id'];
    $senderId = $_POST['sender_id'];
    $message = htmlspecialchars($_POST['message']);
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/" . $image);
    }
    $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, message, image) VALUES (?,?,?,?)");
    $stmt->execute([$convId, $senderId, $message, $image]);
    echo json_encode(["success"=>true]);
    exit;
}