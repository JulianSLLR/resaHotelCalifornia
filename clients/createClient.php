<?php
session_start(); // Démarrer la session
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

// Initialisation du tableau d'erreurs
$errors = [];

// Vérification de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['tel']; // Correction ici
    $nombre_personnes = $_POST['nombre_personnes'];

    if (empty($nom)) {
        $errors[] = "Le nom du client doit être renseigné.";
    }

    if (empty($email)) {
        $errors[] = "L'email du client doit être renseigné.";
    }

    if (empty($telephone)) {
        $errors[] = "Le numéro de téléphone du client doit être renseigné.";
    } elseif (!is_numeric($telephone)) {
        $errors[] = "Le numéro de téléphone du client doit être un numéro.";
    }

    if (empty($nombre_personnes)) {
        $errors[] = "Le nombre de personnes doit être renseigné.";
    } elseif (!is_numeric($nombre_personnes)) {
        $errors[] = "Le nombre de personnes doit être un numéro.";
    }

    if ($nombre_personnes < 0) {
        $errors[] = "Le nombre de personnes doit supérieur à 0.";
    }

    if (empty($errors)) {
        $conn = openDatabaseConnection();

        // Préparation de la requête d'insertion
        $stmt = $conn->prepare("INSERT INTO clients (nom, email, telephone, nombre_personnes) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$nom, $email, $telephone, $nombre_personnes]);

        // Fermer la connexion à la base de données
        closeDatabaseConnection($conn);

        // Vérifier si l'insertion a réussi
        if ($result) {
            $_SESSION['message'] = "SUCCES : ajout effectué.";
            header("Location: listClients.php");
            exit;
        } else {
            $_SESSION['message'] = "ERREUR : l'ajout a échoué.";
            header("Location: createClient.php");
            exit;
        }
    } else {
        // Stocker les erreurs dans la session
        $_SESSION['errors'] = $errors;
        header("Location: createClient.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Client</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets.style.css">
</head>

<body>
    <?php include '../assets/navbar.php'; ?>

    <div class="container">
        <h1>Ajouter un Client</h1>
        <div class="container mt-3">
        <?php
            // Gestion des messages d'erreurs
            if (isset($_GET['message'])) {
                $message = htmlspecialchars(urldecode($_GET['message'])); // limiter les injections XSS
            
                if (strpos($message, 'ERREUR') !== false) {
                    echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>"
                        . $message
                        . "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>"
                        . "</button></div>";
                } else {
                    echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>"
                        . $message
                        . "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'>"
                        . "</button></div>";
                }
            }
            ?>
            <!-- Formulaire -->
            <form method="POST">
                <!-- Champ nom -->
                <div class="row mb-3 ">
                    <label for="nom" class="col-2 col-form-label text-end">Nom</label>
                    <div class="col-4">
                        <input type="text" id="nom" name="nom" class="form-control" placeholder="Entrez le nom">
                    </div>
                </div>

                <!-- Champ email -->
                <div class="row mb-3 ">
                    <label for="email" class="col-2 col-form-label text-end">Email</label>
                    <div class="col-4">
                        <input type="email" id="email" name="email" class="form-control" placeholder="Entrez l'email">
                    </div>
                </div>

                <!-- Champ telephone -->
                <div class="row mb-3 ">
                    <label for="tel" class="col-2 col-form-label text-end">Téléphone</label>
                    <div class="col-4">
                        <input type="tel" id="tel" name="tel" class="form-control" placeholder="Entrez le téléphone">
                    </div>
                </div>

                <!-- Champ nombre_personnes -->
                <div class="row mb-3 ">
                    <label for="nombre_personnes" class="col-2 col-form-label text-end">Nombre de personnes</label>
                    <div class="col-4">
                        <input type="number" id="nombre_personnes" name="nombre_personnes" class="form-control"
                            placeholder="Entrez le nombre de personnes">
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 text-end">
                        <!-- Bouton de retour -->
                        <a href="listClients.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                    <div class="col-4 text-end">
                        <!-- Bouton -->
                        <button type="submit" class="btn btn-primary">Valider</button>
                    </div>
                </div>
            </form>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"
            referrerpolicy="no-referrer"></script>
</body>

</html>