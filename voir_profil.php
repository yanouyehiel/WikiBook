<?php 
    session_start();
    include('includes/connectDB.php');

    // status = 1 : Demande en attente
    // status = 2 : Confirmation de la demande
    // status = 3 : Une personne vous a bloqué

    $id_utilisateur = (int) trim($_GET['id']);

    $conn = $pdo->open();
    if (empty($id_utilisateur)) {
        header('Location: membres.php');
        exit;
    }

    if (isset($_SESSION['id'])) {
        $req = $conn->prepare("SELECT u.*, r.id_demandeur, r.id_receveur, r.status, r.id_bloqueur FROM utilisateur u LEFT JOIN relation r ON (id_receveur=u.id AND id_demandeur=:id2) OR (id_receveur=:id2 AND id_demandeur=u.id) WHERE u.id=:id1");
        $req->execute(array('id1'=>$id_utilisateur, 'id2'=>$_SESSION['id']));
    }else {
        $req = $conn->prepare("SELECT u.*, FROM utilisateur u WHERE u.id=:id1");
        $req->execute(array('id1'=>$id_utilisateur));
    }
    $voir_utilisateur = $req->fetch();

    if (!isset($voir_utilisateur['id'])) {
      header('Location: membres.php');
      exit;
    }

    function age($date){
        $age = date('Y') - date('Y', strtotime($date));
        if (date('md') < date('md', strtotime($date))) {
            return $age - 1;
        }
        return $age;
    }

    if (!empty($_POST)) {
        extract($_POST);

        if (isset($_POST['ajouter'])) {
            $req = $conn->prepare("SELECT id FROM relation WHERE (id_receveur = ? AND id_demandeur = ?) OR (id_receveur = ? AND id_demandeur = ?)");
			$req->execute(array($voir_utilisateur['id'], $_SESSION['id'], $_SESSION['id'], $voir_utilisateur['id']));
			$row = $req->fetch();

            //Ce programme s'exécute ssi le demandeur n'a pas encore de relation avec le receveur
            if(!isset($row['id'])){
				try {
                    $req = $conn->prepare("INSERT INTO relation (id_demandeur, id_receveur, status) VALUES (?, ?, ?)");
					$req->execute(array($_SESSION['id'], $voir_utilisateur['id'], 1));
                    echo "La demande d'ami a été envoyé";
                } catch (Exception $e) {
                    echo 'La demande d\'ami a échoué, veuillez recommencer. Erreur: '.$e->getMessage();
                }
			}
        }
        elseif (isset($_POST['supprimer'])) {
            $req = $conn->prepare("DELETE FROM relation WHERE (id_receveur = ? AND id_demandeur = ?) OR (id_receveur = ? AND id_demandeur = ?)");
		    $req->execute(array($voir_utilisateur['id'], $_SESSION['id'], $_SESSION['id'], $voir_utilisateur['id']));

            echo "L'utilisateur a été supprimé de vos amis";
        }
        elseif (isset($_POST['bloquer'])) {
            $req = $conn->prepare("SELECT id FROM relation WHERE (id_receveur = :id1 AND id_demandeur = :id2) OR (id_receveur = :id2 AND id_demandeur = :id1)");
            $req->execute(array('id1' => $voir_utilisateur['id'], 'id2' => $_SESSION['id']));
            $row = $req->fetch();

            if (isset($row['id'])) {
                $req = $conn->prepare("UPDATE relation SET id_bloqueur = ? WHERE id = ?");
                $req->execute(array($voir_utilisateur['id'], $row['id']));
            } else {
                $req = $conn->prepare("INSERT INTO relation (id_demandeur, id_receveur, status, id_bloqueur) VALUES (?, ?, ?, ?)");
			    $req->execute(array($_SESSION['id'], $voir_utilisateur['id'], 3, $voir_utilisateur['id']));
            }

            echo "L'utilisateur a été bloqué";
        }
        elseif (isset($_POST['debloquer'])) {
            $req = $conn->prepare("SELECT id, status FROM relation WHERE (id_receveur = :id1 AND id_demandeur = :id2) OR (id_receveur = :id2 AND id_demandeur = :id1)");
            $req->execute(array('id1' => $voir_utilisateur['id'], 'id2' => $_SESSION['id']));
            $row = $req->fetch();

            if (isset($row['id'])) {
                if ($row['status'] == 3) {
                    $req = $conn->prepare("DELETE FROM relation WHERE id = ?");
		            $req->execute(array($voir_utilisateur['id']));
                } else {
                    $req = $conn->prepare("UPDATE relation SET id_bloqueur = ? WHERE id = ?");
                    $req->execute(array(NULL, $row['id']));
                }
            }
        }

        header('Location: voir_profil.php?id=' . $voir_utilisateur['id']);
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
        <title>Profil de <?= $voir_utilisateur['pseudo'] ?></title>
    </head>
    <body>
        <?php include('includes/menu.php'); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="body-membre">
                        <div>
                            <b>Pseudo :</b> <?= $voir_utilisateur['pseudo']; ?>
                        </div>
                        <div>
                            <b>Nom et Prénom :</b> <?= $voir_utilisateur['nom'] .' ' . $voir_utilisateur['prenom']; ?>
                        </div>
                        <div>
                            <b>Université :</b> <?= $voir_utilisateur['universite']; ?>
                        </div>
                        <div>
                            <b>Age :</b> <?= age($voir_utilisateur['date_naissance']); ?> ans
                        </div>
                        <div>
                            <b>Téléphone :</b> <?= $voir_utilisateur['telephone']; ?>
                        </div>                         
                    </div>
                    <?php
                        if (isset($_SESSION['id'])) {
                    ?>
                    <div>
                        <form method="POST">
                            <?php
                                if (!isset($voir_utilisateur['status'])) {
                            ?>
                            <input type="submit" name="ajouter" value="Ajouter">
                            <?php
                                }elseif (isset($voir_utilisateur['status']) && $voir_utilisateur['id_demandeur'] == $_SESSION['id'] && !isset($voir_utilisateur['id_bloqueur']) && $voir_utilisateur['status'] <> 2) {
                            ?>
                            <div>Demande en attente...</div>
                            <?php
                                }elseif (isset($voir_utilisateur['status']) && $voir_utilisateur['id_receveur'] == $_SESSION['id'] && !isset($voir_utilisateur['id_bloqueur']) && $voir_utilisateur['status'] <> 2) {
                            ?>
                            <div>Vous avez une demande à accepter</div>
                            <!-- <input type="submit" name="accepter" value="Accepter"> -->
                            <?php
                                }elseif (isset($voir_utilisateur['status']) && $voir_utilisateur['status'] == 2 && !isset($voir_utilisateur['id_bloqueur'])) {
                            ?>
                            <div>Vous etes amis</div>
                            <?php
                                }
                                if (isset($voir_utilisateur['status']) && !isset($voir_utilisateur['id_bloqueur']) && $voir_utilisateur['id_demandeur'] == $_SESSION['id'] && $voir_utilisateur['status'] <> 2) {
                            ?>
                            <input type="submit" name="supprimer" value="Supprimer">
                            <?php
                                }
                                if ((isset($voir_utilisateur['status']) || $voir_utilisateur['status'] == NULL) && !isset($voir_utilisateur['id_bloqueur'])) {
                            ?>
                            <input type="submit" name="bloquer" value="Bloquer">
                            <?php
                                }elseif ($voir_utilisateur['id_bloqueur'] <> $_SESSION['id']) {
                            ?>
                            <input type="submit" name="debloquer" value="Débloquer">
                            <?php
                                }else {
                            ?>
                            <div>Vous avez été bloqué par cet utilisateur</div>
                            <?php
                                }
                            ?>
                        </form>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    </body>
</html>