<?php 
    session_start();
    include('includes/connectDB.php');

    $id_utilisateur = (int) $_SESSION['id']; 

    $conn = $pdo->open();
    if (empty($id_utilisateur)) {
        header('Location: membres.php');
        exit;
    }

    $req = $conn->prepare("SELECT * FROM utilisateur WHERE id=:id");
    $req->execute(['id'=>$id_utilisateur]);
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
                        <div class="image">
                            <img src="<?php echo (!empty($voir_utilisateur['photo'])) ? 'images/'.$voir_utilisateur['photo'] : 'images/noimage.jpg'; ?>" width="150px" height="200px" class="image">
                        </div>
                        <div class="infos">
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
                    </div>
                    <a href="profil_edit.php">Modifier mon profile</a>
                </div>
            </div>
        </div>
        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    </body>
</html>