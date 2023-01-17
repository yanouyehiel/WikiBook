<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <img src="../images/logo.png" alt="" width="30" height="24">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="membres.php" class="nav-link">Membres</a>
                </li>
            </ul>
            <div class="header">
                <ul class="navbar-nav ml-md-auto header">
                    <?php
                        if (isset($_SESSION['id'])) {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal1">Ajouter une actualité</a>
                            </li>
                            <li class="nav-item">
                                <a href="messagerie.php" class="nav-link">Messagerie</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal">Mon profil</a>
                            </li>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Paramètres</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <li>
                                                <a href="profil.php">Mon Profil</a>
                                            </li>
                                            <li>
                                                <a href="demande.php">Mes demandes</a>
                                            </li>
                                            <li>
                                                <a href="profil_edit.php">Editer mon profil</a>
                                            </li>
                                            <li>
                                                <a href="deconnexion.php">Se Déconnecter</a>
                                            </li>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Publier un post</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="ajout-actu">
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        else {
                            ?>
                            <li class="nav-item">
                                <a href="inscription.php" class="nav-link">S'inscrire</a>
                            </li>
                            <li class="nav-item">
                                <a href="connexion.php" class="nav-link">Se connecter</a>
                            </li>
                            <?php
                        }
                    ?>                             
                </ul>
            </div>
        </div>
    </div>
</nav>