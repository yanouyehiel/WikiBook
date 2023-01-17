<?php 
    session_start();
    include('includes/connectDB.php');

    if (!isset($_SESSION['id'])) {
        header('Location: membres.php');
        exit;
    }
    $id_utilisateur = (int) $_SESSION['id']; 

    $conn = $pdo->open();
    if (empty($id_utilisateur)) {
        header('Location: membres.php');
        exit;
    }

    $req = $conn->prepare("SELECT * FROM utilisateur WHERE id=:id");
    $req->execute(['id'=>$id_utilisateur]);
    $voir_profile = $req->fetch();

    if (!isset($voir_profile['id'])) {
        header('Location: membres.php');
        exit;
    }

    if (isset($_POST['modifier'])) {
        $pseudo = $_POST['pseudo'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $password = $_POST['password'];
        $mail = $_POST['mail'];
        $universite = $_POST['universite'];
        $telephone = $_POST['telephone'];

        // On vérifie que le mail est dans le bon format
        if (!preg_match("/^[a-z0-9\-_.]+@[a-z]+\.[a-z]{2,3}$/i", $mail)){
            echo "Le format du mail n'est pas valide";
        }
        else {
            try {
                $password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE utilisateur SET pseudo = ?, mail = ?, password = ?, nom = ?, prenom = ?, telephone = ?, universite = ? WHERE id = ?");
                $stmt->execute([$pseudo, $mail, $password, $nom, $prenom, $telephone, $universite, $_SESSION['id']]);

                header('Location: profil.php');
            } catch (PDOException $e) {
                echo 'La modification a échoué, veuillez recommencer. Erreur: '.$e->getMessage();
            }
        }
        $pdo->close();
    }
    else {
        echo 'Remplissez d\'abord les champs vides';
    }
    
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        <title>Editer mon profil</title>
    </head>
    <body>
        <?php include('includes/menu.php'); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="body-membre">
                        <form action="profil_edit.php" method="post">
                            <div>
                                <?php
                                    if (!isset($pseudo)) {
                                        $pseudo = $voir_profile['pseudo'];
                                    }
                                ?>
                                <label for="pseudo">pseudo</label>
                                <input type="text" name="pseudo" placeholder="<?= $pseudo ?>">
                            </div>
                            <div>
                                <?php
                                    if (!isset($nom)) {
                                        $nom = $voir_profile['nom'];
                                    }
                                ?>
                                <label for="nom">Nom</label>
                                <input type="text" name="nom" placeholder="<?= $nom ?>">
                            </div>
                            <div>
                                <?php
                                    if (!isset($prenom)) {
                                        $prenom = $voir_profile['prenom'];
                                    }
                                ?>
                                <label for="prenom">Prénom</label>
                                <input type="text" name="prenom" placeholder="<?= $prenom ?>">
                            </div>
                            <div>
                                <label for="password">Mot de passe</label>
                                <input type="password" name="password" placeholder="Mot de passe" placeholder="<?= $voir_profile['password'] ?>">
                            </div>
                            <div>
                                <?php
                                    if (!isset($universite)) {
                                        $universite = $voir_profile['universite'];
                                    }
                                ?>
                                <label for="universite">Université</label>
                                <input type="text" name="universite" placeholder="<?= $universite ?>">
                            </div>
                            <div>
                                <?php
                                    if (!isset($mail)) {
                                        $mail = $voir_profile['mail'];
                                    }
                                ?>
                                <label for="email">Email</label>
                                <input type="email" name="mail" placeholder="<?= $mail ?>">
                            </div> 
                            <div>
                                <?php
                                    if (!isset($telephone)) {
                                        $telephone = $voir_profile['telephone'];
                                    }
                                ?>
                                <label for="telephone">Téléphone</label>
                                <input type="number" name="telephone" placeholder="<?= $telephone ?>">
                            </div>
                            <div>
                                <input type="submit" name="modifier" value="Modifier">
                            </div>
                        </form>                            
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    </body>
</html>