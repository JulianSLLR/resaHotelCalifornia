<?php
// Inclusion du fichier de connexion à la base de données
require_once '../config/db_connect.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Vérifier si l'ID est valide
if ($id <= 0) {
    header("Location: listClients.php");
    exit;
}

$conn = openDatabaseConnection();

// Traitement du formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $nombre_personnes = $_POST['nombre_personnes'];

    // Validation des données
    $errors = [];

    if (empty($id)) {
        $errors[] = "Le numéro de client est obligatoire.";
    }

    if ($id <= 0) {
        $errors[] = "L'ID doit être positif.";
    }

    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire";
    }

    if (empty($email)) {
        $errors[] = "L'email est obligatoire.";
    }

    if (empty($telephone)) {
        $errors[] = "Le numéro de téléphone est obligatoire";
    }

    if (empty($nombre_personnes)) {
        $errors[] = "Le nombre de personnes est obligatoire.";
    }

    if ($nombre_personnes <= 0) {
        $errors[] = "Le nombre de personnes doit être un nombre positif.";
    }

    // Si pas d'erreurs, mettre à jour les données
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE clients SET nom = ?, email = ?, telephone = ?, nombre_personnes = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $telephone, $nombre_personnes, $id]);

        // Rediriger vers la liste des clients
        $encodedMessage = urlencode("SUCCES : édition effectuée.");
        header("Location: listClients.php?message=$encodedMessage");
        exit;
    }
} else {
    // Récupérer les données du client
    $stmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si le client n'existe pas, rediriger
    if (!$client) {
        $encodedMessage = urlencode("ERREUR : le client n'existe pas");
        header("Location: listClients.php?message=$encodedMessage");
        exit;
    }
}

closeDatabaseConnection($conn);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Modifier un Client</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1>Modifier un Client</h1>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="id">ID du Client:</label>
                <input type="numero" id="id" name="id" value="<?= htmlspecialchars($client['id']) ?>" min="1" required>
            </div>

            <div class="form-group">
                <label for="nom">Nom du Client:</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($client['nom']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email du Client:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone du Client :</label>
                <input type="tel" id="telephone" name="telephone" value="<?= $client['telephone'] ?>" required>
            </div>

            <div class="form-group">
                <label for="nombre_personnes">Nombre de personnes :</label>
                <input type="text" id="nombre_personnes" name="nombre_personnes"
                    value="<?= $client['nombre_personnes'] ?>" min="1" required>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="listClients.php" class="btn btn-danger">Annuler</a>
            </div>
        </form>
    </div>
</body>

</html>