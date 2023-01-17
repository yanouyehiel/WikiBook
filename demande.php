<?php 
    session_start();
    include('includes/connectDB.php');

    if (!isset($_SESSION['id'])) {
        header('Location: index.php');
        exit;
    }

    $conn = $pdo->open();
    
    //Affichage de toutes les demandes d'amitiées
    $req = $conn->prepare("SELECT r.id, u.pseudo, u.id id_utilisateur FROM relation r INNER JOIN utilisateur u ON u.id = r.id_demandeur WHERE r.id_receveur = ? AND r.status = ?");
    $req->execute(array($_SESSION['id'], 1 ));
    $afficher_demandes = $req->fetchAll();

    if (!empty($_POST)) {
        extract($_POST);

        //Programme qui accepte une demande d'amitié
        if (isset($_POST['accepter'])) {

            $id_relation = (int) $id_relation;

            if ($id_relation > 0) {
                $req = $conn->prepare("SELECT id FROM relation WHERE id = ? AND status = 1");
                $req->execute(array($id_relation));
                $row = $req->fetch();

                if(isset($row['id'])){
                    try {
                        $req = $conn->prepare("UPDATE relation SET status = 2 WHERE id = ? AND id_receveur = ?");
                        $req->execute(array($id_relation, $_SESSION['id']));
                        //echo "La demande d'ami a été envoyé";
                    } catch (Exception $e) {
                        //echo 'La demande d\'ami a échoué, veuillez recommencer. Erreur: '.$e->getMessage();
                    }
                }
            }
            
            header('Location: demande.php');
            exit;
        }

        //Programme qui refuse une demnde d'amitié et donc le supprime de l'interface
        if (isset($_POST['refuser'])) {
            $id_relation = (int) $id_relation;

            if ($id_relation > 0) {
                $req = $conn->prepare("DELETE FROM relation WHERE id = ? AND id_receveur = ?");
		        $req->execute(array($id_relation, $_SESSION['id']));
            }
            //echo "L'utilisateur a été supprimé de vos amis";
            header('Location: demande.php');
            exit;
        }
    }
    $pdo->close();
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        <title>Demande d'amis</title>
    </head>
    <body>
        <?php include('includes/menu.php'); ?>

        <div class="container">
            <div class="row">
                <?php
                    foreach ($afficher_demandes as $row) {
                        ?>
                        <div class="col-sm-3">
                            <div class="body-membre">
                                <div>
                                    <?= $row['pseudo']; ?>
                                </div>
                                <div class="membre-btn">
                                    <a href="voir_profil.php?id=<?= $row['id_utilisateur']; ?>" class="btn-voir">Voir</a>
                                </div> 
                                <div>
                                    <form method="post">
                                        <input type="hidden" name="id_relation" value="<?= $row['id']; ?>">
                                        <input type="submit" name="accepter" value="Accepter">
                                        <input type="submit" name="refuser" value="Refuser">
                                    </form>
                                </div>                         
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    </body>
</html>