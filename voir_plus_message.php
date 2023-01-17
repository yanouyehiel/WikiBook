<?php
    session_start();
    include('includes/connectDB.php');

    if (!isset($_SESSION['id'])) {
        exit;
    }

    $limit = (int) trim($_POST['req']);
    $id_get = (int) trim($_POST['id']);

    if ($limit <= 0 || $id_get <= 0) {
        exit;
    }

    $req = $conn->prepare("SELECT id FROM relation WHERE((id_demandeur, id_receveur) = (:id1, :id2) OR (id_demandeur, id_receveur) = (:id2, :id1)) AND status = :status");
    $req->execute(['id1' => $_SESSION['id'], 'id2' => $id_get, 'status' => 2]);
    $verif_relation = $req->fetch();

    if (!isset($verif_relation['id'])) {
        header('Location: messagerie.php');
        exit;
    }

    //C'est le nombre total de message à afficher
    $nbre_total_message = 3;
    $min_limit = 0;
    $max_limit = 0;

    //Affichage des messages entre les deux utilisateurs amis
    $req = $conn->prepare("SELECT COUNT(id) AS nbMessage FROM messagerie WHERE((id_envoyeur, id_receveur) = (:id1, :id2) OR (id_envoyeur, id_receveur) = (:id2, :id1))");
    $req->execute(['id1' => $_SESSION['id'], 'id2' => $id_get]);
    $nbre_message = $req->fetch();

    $min_limit = $nbre_message['nbMessage'] - $limit;

    if ($min_limit > $nbre_total_message) {
        $max_limit = $nbre_total_message;
        $min_limit = $min_limit - $nbre_total_message;
    } else {
        if ($min_limit > 0) {
            $max_limit = $min_limit;
        } else {
            $max_limit = 0; 
        }
        $min_limit = 0;
    }

    $req = $conn->prepare("SELECT * FROM messagerie WHERE ((id_envoyeur, id_receveur) = (:id1, :id2) OR (id_envoyeur, id_receveur) = (:id2, :id1)) ORDER BY date_message LIMIT $min_limit, $max_limit");
    $req->execute(['id1' => $_SESSION['id'], 'id2' => $id_get]);
    $afficher_message = $req->fetchAll();

    /*$if (count($afficher_messge) < 25) {
        ?>
        <script>
            $('.see-more').addClass('see-more-display');
        </script>
        <?php
    }*/
?>
<div id="voir-plus-message"></div>

<?php
    foreach ($afficher_message as $message) {
        if ($message['id_envoyeur'] == $_SESSION['id']) {
            ?>
            <div style="background: #333; color: white;">
                <?= nl2br($message['message']) ?>
            </div>
            <?php
        } else {
            ?>
            <div>
                <?= nl2br($message['message']) ?>
            </div>
            <?php
        }
    }

?>