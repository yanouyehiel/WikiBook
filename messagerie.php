<?php 
    session_start();
    include('includes/connectDB.php');

    $conn = $pdo->open();
    if (!isset($_SESSION['id'])) {
        header('Location: membres.php');
        exit;
    }

    $req = $conn->prepare("SELECT COUNT(id) AS nbr_amis FROM relation WHERE (id_demandeur = :id OR id_receveur = :id) AND status = 2");
    $req->execute(['id'=> $_SESSION['id']]);
    $nbr_conversations = $req->fetch();
    //echo $nbr_conversations['nbr_amis'];

    $req = $conn->prepare("SELECT u.pseudo, u.id, m.message, m.date_message, m.id_envoyeur, m.lu 
        FROM (
        SELECT IF(r.id_demandeur = :id, r.id_receveur, r.id_demandeur) id_utilisateur, MAX(m.id) max_id 
        FROM relation r 
        LEFT JOIN messagerie m ON ((m.id_envoyeur, m.id_receveur) = (r.id_demandeur, r.id_receveur) OR (m.id_envoyeur, m.id_receveur) = (r.id_receveur, r.id_demandeur)) 
        WHERE (r.id_demandeur = :id OR r.id_receveur = :id) AND r.status = 2 
        GROUP BY IF(m.id_envoyeur = :id, m.id_receveur, m.id_envoyeur), r.id) AS DM 
        LEFT JOIN messagerie m ON m.id = DM.max_id 
        LEFT JOIN utilisateur u ON u.id = DM.id_utilisateur 
        ORDER BY m.date_message DESC")
    ;
    $req->execute(['id' => $_SESSION['id']]);
    $afficher_conversations = $req->fetchAll();
    
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        <title>Messagerie</title>
    </head>
    <body>
        <?php include('includes/menu.php'); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <table>
                        <?php
                            foreach ($afficher_conversations as $conversation) {
                                ?>
                                <tr>
                                    <td>
                                        <a href="message.php?id=<?= $conversation['id'] ?>"><?= $conversation['pseudo'] ?></a>
                                    </td>
                                    <td>
                                        <?php
                                            if ($conversation['id_envoyeur'] != $_SESSION['id'] && $conversation['lu'] == 1) {
                                        ?>
                                            Nouveau
                                        <?php
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            if (isset($conversation['message'])) {
                                                echo $conversation['message'];
                                            } else {
                                                echo '<em>Bonjour !</em>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            if (isset($conversation['date_message'])) {
                                                echo date('d-m-Y H:i:s', strtotime($conversation['date_message']));
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </table>
                </div>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    </body>
</html>