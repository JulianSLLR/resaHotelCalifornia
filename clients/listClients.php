<?php
    require_once '../config/db_connect.php';
    require_once '../auth/authFunctions.php';
    $conn = openDatabaseConnection();
    $stmt = $conn->query("SELECT * FROM clients ORDER BY id");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    closeDatabaseConnection($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Clients</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include_once '../assets/gestionMessage.php'; ?>
    <?php include_once '../assets/navbar.php'; ?>
    
    <div class="container">
        <h1>Liste des Clients</h1>
        <div class="actions">
            <a href="createClient.php" class="btn btn-success">Ajouter un Client</a>
        </div>
        <table class="table table-striped" style="width: 60%; min-width: 400px; margin: 0 auto;">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Nom</th>
                <th scope="col">Email</th>
                <th scope="col">Telephone</th>
                <th scope="col">Nombre de personnes</th>

            </tr>
            <?php foreach($clients as $client): ?>
            <tr>
                <td><?php echo $client['id'] ?></td>
                <td><?= $client['nom'] ?></td>
                <td><?= $client['email'] ?></td>
                <td><?= $client['telephone'] ?></td>
                <td><?= $client['nombre_personnes'] ?></td>

                <td>
                    <a href="editClient.php?id=<?= $client['id'] ?>"><i class="fas fa-pen"></i></a>
                    <a href="deleteClient.php?id=<?= $client['id'] ?>"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>