<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

// Initialisation du tableau d'erreurs
$errors = [];

// Vérification de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = ($_POST['numero']);
    $capacite = ($_POST['capacite']);

    if (empty($numero)) {
        $errors[] = "Le numéro de la chambre doit être renseigné.";
    } elseif (!is_numeric($numero)) {
        $errors[] = "Le numéro de la chambre doit être un numéro.";
    }

    if (empty($capacite)) {
        $errors[] = "La capacité de la chambre doit être renseignée.";
    } elseif (!is_numeric($capacite) || $capacite < 0) {
        $errors[] = "La capacité de la chambre doit être un nombre positif.";
    }

    if (empty($errors)) {
        $conn = openDatabaseConnection();

        // Vérifier si le numéro de chambre existe déjà
        $stmt = $conn->prepare("SELECT COUNT(*) FROM chambres WHERE numero = ?");
        $stmt->execute([$numero]);
        $existingRoom = $stmt->fetchColumn();

        if ($existingRoom > 0) {
            $errors[] = "Le numéro de chambre existe déjà.";
        }

        // Fermer la connexion à la base de données si une erreur est trouvée
        if (!empty($errors)) {
            closeDatabaseConnection($conn);
        }
    }

    // Si des erreurs existent, rediriger avec les erreurs
    if (!empty($errors)) {
        $encodedMessage = urlencode(implode(" ", $errors));
        header("Location: createChambre.php?message=$encodedMessage");
        exit;
    }

    // Si aucune erreur, procéder à l'insertion dans la base de données
    if (!isset($conn)) {
        $conn = openDatabaseConnection();
    }

    $stmt = $conn->prepare("INSERT INTO chambres (numero, capacite) VALUES (?, ?)");
    $result = $stmt->execute([$numero, $capacite]);

    // Fermer la connexion à la base de données
    closeDatabaseConnection($conn);

    // Vérifier si l'insertion a réussi
    if ($result) {
        $encodedMessage = urlencode("SUCCES : ajout effectuée.");
        header("Location: listChambres.php?message=$encodedMessage");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Chambre</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <?php include '../assets/navbar.php'; ?>

    <div class="container">
        <h1>Ajouter une Chambre</h1>
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
                <!-- Champ Numéro -->
                <div class="row mb-3">
                    <label for="numero" class="col-2 col-form-label text-end">Numéro</label>
                    <div class="col-4">
                        <input type="text" id="numero" name="numero" class="form-control"
                            placeholder="Entrez le numéro">
                    </div>
                </div>
                <!-- Champ Capacité -->
                <div class="row mb-3">
                    <label for="capacite" class="col-2 col-form-label text-end">Capacité</label>
                    <div class="col-4">
                        <input type="text" id="capacite" name="capacite" class="form-control"
                            placeholder="Entrez la capacité">
                    </div>
                </div>
                <div class="row">
                    <div class="col-2 text-end">
                        <!-- Bouton de retour -->
                        <a href="listChambres.php" class="btn btn-secondary">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
</body>

</html>