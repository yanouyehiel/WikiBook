<?php
    session_start();
	include 'includes/connectDB.php';
	$conn = $pdo->open();

    if (isset($_SESSION['id'])) {
        header('Location: index.php');
        exit;
    }

	if(isset($_POST['connexion'])){
		
		$pseudo = $_POST['pseudo'];
		$password = $_POST['password'];

		try{
			$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM utilisateur WHERE pseudo = :pseudo");
			$stmt->execute(['pseudo'=>$pseudo]);
			$row = $stmt->fetch();
			if($row['numrows'] > 0){				
                if(password_verify($password, $row['password'])){
                    $date_connexion = date('Y-m-d H:i:s');
                    if($row['new_mdp'] == 1){
                        $update = $conn->prepare("UPDATE utilisateur SET new_mdp = 0 WHERE id=:id");
                        $update->execute(['id'=>$row['id']]);
                    }
                    $update = $conn->prepare("UPDATE utilisateur SET date_connexion=:date_connexion WHERE id=:id");
                    $update->execute(['date_connexion'=>$date_connexion, 'id'=>$row['id']]);

                    $_SESSION['id'] = $row['id'];
                    $_SESSION['nom'] = $row['nom'];
                    $_SESSION['prenom'] = $row['prenom'];
                    $_SESSION['mail'] = $row['mail'];
                    $_SESSION['pseudo'] = $row['pseudo'];
                    $_SESSION['tel'] = $row['telephone'];
                    $_SESSION['univ'] = $row['universite'];

                    header('Location: index.php');
                }
                else{
                    echo 'Mot de passe incorrect';
                }
			}
			else{
				echo 'Pas d\'utilisateur sous ce pseudo';
			}
		}
		catch(PDOException $e){
			echo "Il y'a quelques problèmes de connexion: " . $e->getMessage();
		}

	}
	else{
		echo 'Inserez d\'abord vos identifiants';
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
        <title>Connexion</title>
    </head>
    <body>
        <?php include 'includes/menu.php'; ?>

        <h1>Connexion</h1>

        <form action="connexion.php" method="POST">
            <div class="information">
                <div class="contenu">
                    <label for="pseudo">Pseudo</label>
                    <input type="text" name="pseudo" required>
                </div>
                <div class="contenu">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" required>
                </div>
                <div class="contenu">
                    <input type="submit" name="connexion" value="Connexion">
                </div>
            </div>
        </form>

        <!-- footer -->
        <div class="footer">
            <p>&copy; 2022 Tous droits réservés | Deigné par <a href="https://oncheckcm.com/" target="_blank">Yehiel Yanou</a></p>
        </div>
        <!-- footer -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <!-- fontawesome v5-->
    <script src="js/fontawesome.js"></script>
    </body>
</html>