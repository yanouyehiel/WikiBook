<?php

    session_start();
    include('includes/connectDB.php');

    $conn = $pdo->open();
    if (!isset($_SESSION['id'])) {
        exit;
    }

    $id_get = (int) $_POST['id'];

    if ($id_get <= 0) {
        exit;
    }

    //Avant d'afficher un message on veut savoir s'il y'a une relation actuellement entre les deux personnes
    $req = $conn->prepare("SELECT id FROM relation WHERE((id_demandeur, id_receveur) = (:id1, :id2) OR (id_demandeur, id_receveur) = (:id2, :id1)) AND status = :status");
    $req->execute(['id1' => $_SESSION['id'], 'id2' => $id_get, 'status' => 2]);
    $verif_relation = $req->fetch();

    if (!isset($verif_relation['id'])) {
        exit;
    }

    $req = $conn->prepare("SELECT *, FROM messagerie WHERE id_receveur = ? AND id_envoyeur = ? AND lu = ?");
    $req->execute([$_SESSION['id'], $id_get, 1]);
    $load_message = $req->fetchAll();

    //Mettre à jour les informations de la Table messagerie
    $req = $conn->prepare("UPDATE messagerie SET lu = ? WHERE  id_receveur = ? AND id_envoyeur = ?");
    $req->execute([0, $_SESSION['id'], $id_get]);

    foreach ($load_message as $message) {
        ?>
        <div>
            <?= nl2br($message['message']) ?>
        </div>
        <?php
    }

?>