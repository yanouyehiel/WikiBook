<?php
    session_start();
    include('includes/connectDB.php');
    $conn = $pdo->open();

    if (isset($_SESSION['id'])){

        if (isset($_POST['ajout-actu'])) {
            $titre = $_POST['titre'];
            $photo = $_FILES['photo']['name'];
            $description = $_POST['description'];
    
            if(!empty($photo)){
                move_uploaded_file($_FILES['photo']['tmp_name'], 'images_actualites/'.$photo);
                $filename = $photo;
    
                $stmt = $conn->prepare("INSERT INTO actualites (id_user, titre, image, description, nb_likes) VALUES (:id, :titre, :image, :description, :like)");
                $stmt->execute(['id' => $_SESSION['id'], 'titre' => $titre, 'image' => $filename, 'description' => $description, 'like' => 0]);
            } else {
                echo "L'image n'est pas importée, veuillez réessayer !";
            }
        }
    } else{
        echo "Veuillez vous connecter pour publier une actualité";
    }
    $pdo->close();
    
    header("Location: index.php");
?>