<?php 
    session_start();
    include('includes/connectDB.php');

    $conn = $pdo->open();
    if (isset($_SESSION['id'])) {
        $afficher_membres = $conn->prepare("SELECT * FROM utilisateur WHERE id != :id");
        $afficher_membres->execute(['id' => $_SESSION['id']]);
    }
    else {
        $afficher_membres = $conn->prepare("SELECT * FROM utilisateur");
        $afficher_membres->execute();
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
        <title>Les membres !</title>
    </head>
    <body>
        <?php include('includes/menu.php'); ?>

        <div class="container">
            <div class="row">
                <?php
                    foreach ($afficher_membres as $row) {
                        ?>
                        <div class="col-sm-3">
                            <div class="body-membre">
                                <div>
                                    <img src="<?php echo (!empty($row['photo'])) ? 'images/'.$row['photo'] : 'images/noimage.jpg'; ?>" width="100px" height="100px" class="img-actu" alt="Photo de profile">
                                </div>
                                <div>
                                    <?= $row['pseudo']; ?>
                                </div>
                                <div class="membre-btn">
                                    <a href="voir_profil.php?id=<?= $row['id']; ?>" class="btn-voir">Voir</a>
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