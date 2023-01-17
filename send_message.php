<?php 
    session_start();
    include('includes/connectDB.php');

    $conn = $pdo->open();
    if (!isset($_SESSION['id'])) {
        exit;
    }

    $id_get = (int) $_POST['id'];
    $message_get = (String) urldecode(trim($_POST['message']));
    
    if ($id_get <= 0 || empty($message_get)) {
        exit;
    }

    //Avant d'afficher un message on veut savoir s'il y'a une relation actuellement entre les deux personnes
    $req = $conn->prepare("SELECT id FROM relation WHERE((id_demandeur, id_receveur) = (:id1, :id2) OR (id_demandeur, id_receveur) = (:id2, :id1)) AND status = :status");
    $req->execute(['id1' => $_SESSION['id'], 'id2' => $id_get, 'status' => 2]);
    $verif_relation = $req->fetch();

    if (!isset($verif_relation['id'])) {
        exit;
    }

    $date_message = date('Y-m-d H:i:s');
    $req = $conn->prepare("INSERT INTO messagerie (id_envoyeur, id_receveur, message, date_message, lu) VALUES (?, ?, ?, ?, ?)");
    $req->execute([$_SESSION['id'], $id_get, $message_get, $date_message, 1]);

?>

<div style="background: #333; color: white;">
    <?= nl2br($message_get) ?>
</div>