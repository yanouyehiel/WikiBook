<?php 
    session_start();
    include('includes/connectDB.php');

    $conn = $pdo->open();
    if (!isset($_SESSION['id'])) {
        header('Location: index.php');
        exit;
    }

    $id_get = (int) $_GET['id'];
    
    if ($id_get <= 0) {
        header('Location: messagerie.php');
        exit;
    }

    //Avant d'afficher un message on veut savoir s'il y'a une relation actuellement entre les deux personnes
    $req = $conn->prepare("SELECT id FROM relation WHERE((id_demandeur, id_receveur) = (:id1, :id2) OR (id_demandeur, id_receveur) = (:id2, :id1)) AND status = :status");
    $req->execute(['id1' => $_SESSION['id'], 'id2' => $id_get, 'status' => 2]);
    $verif_relation = $req->fetch();

    if (!isset($verif_relation['id'])) {
        header('Location: messagerie.php');
        exit;
    }

    //C'est le nombre total de message à afficher
    $nbre_total_message = 3;

    //Affichage des messages entre les deux utilisateurs amis
    $req = $conn->prepare("SELECT COUNT(id) AS nbMessage FROM messagerie WHERE((id_envoyeur, id_receveur) = (:id1, :id2) OR (id_envoyeur, id_receveur) = (:id2, :id1))");
    $req->execute(['id1' => $_SESSION['id'], 'id2' => $id_get]);
    $nbre_message = $req->fetch();

    $verif_nbre_message = 0;

    if (($nbre_message['nbMessage'] - $nbre_total_message > 0)) {
        $verif_nbre_message = ($nbre_message['nbMessage'] - $nbre_total_message);
    }

    $req = $conn->prepare("SELECT * FROM messagerie WHERE ((id_envoyeur, id_receveur) = (:id1, :id2) OR (id_envoyeur, id_receveur) = (:id2, :id1)) ORDER BY date_message LIMIT $verif_nbre_message, $nbre_total_message");
    $req->execute(['id1' => $_SESSION['id'], 'id2' => $id_get]);
    $afficher_message = $req->fetchAll();

    //Mise à jour de la Table messagerie
    $req = $conn->prepare("UPDATE messagerie SET lu = ? WHERE  id_receveur = ? AND id_envoyeur = ?");
    $req->execute([0, $_SESSION['id'], $id_get]);

    if (!empty($_POST)) {
        extract($_POST);

        if (isset($_POST['envoyer'])) {
            $message = (String) trim($_POST['message']);

            try {
                $date_message = date('Y-m-d H:i:s');
                $req = $conn->prepare("INSERT INTO messagerie (id_envoyeur, id_receveur, message, date_message, lu) VALUES (?, ?, ?, ?, ?)");
                $req->execute(array($_SESSION['id'], $id_get, $message, $date_message, 1));
            } catch (Exception $e) {
                echo 'Une erreur est survenue, veuillez recommencer. Erreur: '.$e->getMessage();
            }

            header('Location: message.php?id=' . $id_get);
            exit;
        }
    }
    
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        <title>Messages</title>
    </head>
    <body>
        <?php include('includes/menu.php'); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="body-message" id="message">
                        <?php
                            if ($nbre_message['nbMessage'] > $nbre_total_message) {
                                ?>
                                <button id="voir-plus">Voir plus</button>
                                <?php
                            }
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
                        <div id="afficher-message"></div>
                        <div id="load-message"></div>
                    </div>
                </div>
                
                <div class="col-sm-12" style="margin-top: 20px">
                    <form method="post" id="envoyer">
                        <!-- cols="30" rows="10" -->
                        <textarea name="message" id="message"></textarea>
                        <input type="submit" name="envoyer" value="Envoyer">
                    </form>
                </div>
            </div>
        </div>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>

        <script>
            $(document).ready(function(){

                document.getElementById('message').scrollTop() = document.getElementById('message').scrollHeight;

                $('#envoyer').on('submit', function(e){
                    e.preventDefault();

                    let id; 
                    let message;

                    id = <?= json_encode($id_get, JSON_UNESCAPED_UNICODE); ?>
                    message = document.getElementById('message').value;

                    //Suppression du message dans le textarea après le clic
                    document.getElementById('message').value = '';

                    if (id > 0 && message != '') {
                        $.ajax({
                            url: 'send_message.php',
                            method: 'POST',
                            dataType: 'html',
                            data: (id, message),

                            success: function(data){
                                $('afficher-message').append(data);
                                document.getElementById('message').scrollTop() = document.getElementById('message').scrollHeight;
                            },
                            error: function(e, xhr, s){
                                let error = e.responseJSON;
                                if (e.status == 403 && typeof error !== 'undefined') {
                                    alert('Erreur 403')
                                }else if (e.status == 404) {
                                    alert('Erreur 404');
                                }else if (e.status == 401) {
                                    alert('Erreur 401');
                                }else{
                                    alert('Erreur Ajax');
                                }
                            }
                        });
                    }
                });

                let load_message_auto = 0;
                load_message_auto = clearInterval(load_message_auto);
                load_message_auto = setInterval(loadMessageAuto, 2000);

                function loadMessageAuto(){

                    let id = <?= json_encode($id_get, JSON_UNESCAPED_UNICODE); ?> 

                    if (id > 0 && message != ''){
                        $.ajax({
                            url: 'load_message.php',
                            method: 'POST',
                            dataType: 'html',
                            data: id,

                            success: function(data){
                                if (data.trim() != '') {
                                    $('load-message').append(data);
                                    document.getElementById('message').scrollTop() = document.getElementById('message').scrollHeight;
                                }                              
                            },
                            error: function(e, xhr, s){
                                let error = e.responseJSON;
                                if (e.status == 403 && typeof error !== 'undefined') {
                                    alert('Erreur 403')
                                }else if (e.status == 404) {
                                    alert('Erreur 404');
                                }else if (e.status == 401) {
                                    alert('Erreur 401');
                                }else{
                                    alert('Erreur Ajax');
                                }
                            }
                        });
                    }
                }

                <?php
                    if ($nbre_message['nbMessage'] > $nbre_total_message) {
                ?>
                let req = 0;

                $('#voir-plus').click(function(){
                    let id;
                    let element;

                    req == <?= $nbre_total_message ?>
                    id = <?= json_encode($id_get, JSON_UNESCAPED_UNICODE); ?>

                    $.ajax({
                        url: 'voir_plus_message.php',
                        method: 'POST',
                        dataType: 'html',
                        data: (req, id),

                        success: function(data){
                            $(data).hide().appendTo('#voir-plus-message').fadeIn(2000);
                            document.getElementById('voir-plus-message').removeAttribute('id');                             
                        },
                        error: function(e, xhr, s){
                            let error = e.responseJSON;
                            if (e.status == 403 && typeof error !== 'undefined') {
                                alert('Erreur 403')
                            }else if (e.status == 404) {
                                alert('Erreur 404');
                            }else if (e.status == 401) {
                                alert('Erreur 401');
                            }else{
                                alert('Erreur Ajax');
                            }
                        }
                    });
                });
                <?php
                    }
                ?>
            });
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </body>
</html>