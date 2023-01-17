<?php
    session_start();
    include('includes/connectDB.php');

    if (!isset($_SESSION['id'])) {
        echo "Veuillez vous connecter !";
        header('Location: connexion.php');
    }
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        <title>Bienvenu sur Wikibook !</title>
    </head>
    <body>
        <?php include('includes/menu.php'); ?>

        <h1>Bienvenu sur <em>Wikibook</em> votre nouveau réseau social pour étudiants</h1>
        <h3>Soyez à l'affut des dernières actualités de votre campus</h3>
        <?php
            if (isset($_SESSION['id'])) {
                ?>
                <h4>Ravi de te revoir <?= $_SESSION['pseudo']; ?></h4>
                <?php
            }
        ?>

        <div class="actualites">
            <?php
                $conn = $pdo->open();
                $afficher_actus = $conn->prepare("SELECT * FROM actualites ORDER BY id DESC LIMIT 10");
                $afficher_actus->execute();

                foreach ($afficher_actus as $actus) {
                    ?>
                    <div class="contenu">
                        <h3 class="titre-actu">Titre : <?= $actus['titre']; ?></h3>
                        <img src="<?php echo (!empty($actus['image'])) ? 'images_actualites/'.$actus['image'] : 'images_actualites/noimage.jpg'; ?>" width="150px" height="200px" class="img-actu">
                        <p class="description-actu">Description : <?= $actus['description']; ?></p>
                        <a href="liker.php" class="liens"><p><?= $actus['nb_likes'] ?></p> liker</a>
                        <a href="commenter.php" class="liens">commenter</a>
                    </div>
                    <?php
                }
            ?>
        </div>

        <!--div class="ajout-actu">
            <p>Ajouter une actualié</p>
            <div class="ajout">
                <form method="Post" action="insert_actu.php" enctype="multipart/form-data">
                    <div class="contenu">
                        <label for="titre">Titre :</label>
                        <input type="text" name="titre" required>
                    </div>
                    <div class="contenu">
                        <label for="image">Image :</label>
                        <input type="file" id="photo" name="photo">
                    </div>
                    <div class="contenu">
                        <label for="titre">Description :</label>
                        <textarea name="description" id="description"></textarea>
                    </div>
                    <div class="contenu">
                        <input type="submit" name="ajout-actu" value="Envoyer">
                    </div>
                </form>
            </div>
        </div-->
        <footer>
            <p>&copy; 2022 - Réseau Social. Developpé par <a href="">Yehiel Yanou</a></p>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    </body>
</html>