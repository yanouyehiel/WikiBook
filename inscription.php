<?php
    session_start();
    include('includes/connectDB.php');

    if (isset($_SESSION['id'])) {
        header('Location: index.php');
        exit;
    }
?>

<?php 
    if (isset($_POST['inscription'])) {

        $pseudo = $_POST['pseudo'];
        $nom = $_POST['nom'];
		$prenom = $_POST['prenom'];
		$mail = $_POST['mail'];
        $telephone = $_POST['telephone'];
        $date_naissance = $_POST['naissance'];
		$password = $_POST['password'];
		$confirmdp = $_POST['cpassword'];
		$genre = $_POST['genre'];
        $universite = $_POST['universite'];
        $photo = $_FILES['photo']['name'];

        // On vérifie que le mail est dans le bon format
        if (!preg_match("/^[a-z0-9\-_.]+@[a-z]+\.[a-z]{2,3}$/i", $mail)){
            echo "Le format du mail n'est pas valide";
        }        
        if(!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)#", $password)){
            echo "Vous n'avez pas respecté les contraintes sur le mot de passe";
        }
		if($password != $confirmdp){
			echo 'Les mots de passe ne sont pas identiques';
		}
        if(empty($photo)){
            echo "L'image n'est pas importée, veuillez réessayer !";
        } else {
            move_uploaded_file($_FILES['photo']['tmp_name'], 'images/'.$photo);
            $filename = $photo;

            $conn = $pdo->open();
            $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM utilisateur WHERE mail=:mail");
			$stmt->execute(['mail'=>$mail]);
			$row = $stmt->fetch();
            if($row['numrows'] > 0){
				echo "Cette adresse e-mail existe déjà";
			}
            else{
                $date_inscription = date('Y-m-d H:i:s');
				$password = password_hash($password, PASSWORD_DEFAULT);

                //generate code
				//$set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				//$token = substr(str_shuffle($set), 0, 12);

                try {
                    $stmt = $conn->prepare("INSERT INTO utilisateur (pseudo, mail, password, nom, prenom, date_naissance, telephone, universite, photo, date_inscription, genre) VALUES (:pseudo, :mail, :password, :nom, :prenom, :date_naissance, :telephone, :universite, :photo, :date_inscription, :genre)");
					$stmt->execute(['pseudo'=>$pseudo, 'mail'=>$mail, 'password'=>$password, 'nom'=>$nom, 'prenom'=>$prenom, 'date_naissance'=>$date_naissance, 'telephone'=>$telephone, 'universite'=>$universite, 'photo' => $filename, 'date_inscription'=>$date_inscription, 'genre'=>$genre]);

                    //$mail_to = $_SESSION['mail'];

                    //mail($mail_to, 'Activation de votre compte', $contenu, $header);
                    
			        //unset($_SESSION['nom']);
			        //unset($_SESSION['prenom']);
			        //unset($_SESSION['mail']);

			        echo 'Félicitations! Votre compte a été créé avec success.Consultez votre boite mail pour l\'activer';

                } catch (PDOException $e) {
                    echo 'Le message ne peut pas etre envoyé. Erreur: '.$e->getMessage();
                }
            }
        }
        $pdo->close();
    }
    else {
        echo 'Remplissez d\'abord les champs vides';
    }
    //header('Location: inscription.php');
?>
<doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        <title>Inscription</title>
    </head>
    <body>
        <?php include 'includes/menu.php'; ?>

        <h1>Inscription</h1>

        <form action="inscription.php" method="POST" enctype="multipart/form-data">
            <div class="information">
                <div class="contenu">
                    <label for="pseudo">Pseudo :</label>
                    <input type="text" name="pseudo" required>
                </div>
                <div class="contenu">
                    <label for="nom">Nom :</label>
                    <input type="text" name="nom" required>
                </div>
                <div class="contenu">
                    <label for="prenom">Prénom :</label>
                    <input type="text" name="prenom" required>
                </div>
                <div class="contenu">
                    <label for="email">Email :</label>
                    <input type="email" name="mail" required>
                </div>
                <div class="contenu">
                    <label for="password">Mot de passe :</label>
                    <p>Le mot de passe doit comporter au moins 8 caractères, des chiffres et des caractères spéciaux (Ex: !, *, #, $, @...)</p>
                    <input type="password" name="password" required>
                </div>
                <div class="contenu">
                    <label for="password">Confirmer votre mot de passe :</label>
                    <input type="password" name="cpassword" required>
                </div>
                <div class="contenu">
                    <label for="naissance">Date de naissance :</label>
                    <input type="date" name="naissance"  value="XX-XX-XXXX" min="01-01-1990" max="31-12-2004" required/>
                </div>
                <div class="contenu">
                    <label for="telephone">Téléphone :</label>
                    <input type="number" name="telephone" required>
                </div>
                <div class="contenu">
                    <label for="universite">Université :</label>
                    <input type="text" name="universite" required>
                </div>
                <div class="contenu">
                    <label for="genre">Genre :</label>
                    <div>
                        <label for="male">Homme</label>
                        <input type="radio" name="genre" value="Masculin">
                    </div>
                    <div>
                        <label for="female">Femme</label>
                        <input type="radio" name="genre" value="Féminin">
                    </div>
                </div>
                <div class="contenu">
                    <label for="photo">Photo de profile :</label>
                    <input type="file" name="photo">
                </div>
            </div>
            <div class="contenu">
                <input type="submit" name="inscription" value="Envoyer">
            </div>           
        </form>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    </body>
</html>