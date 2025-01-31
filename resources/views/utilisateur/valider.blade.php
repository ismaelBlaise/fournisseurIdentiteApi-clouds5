<!-- resources/views/utilisateur/valider.blade.php -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de Compte</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 500px;
            text-align: center;
        }

        .title {
            font-size: 24px;
            font-weight: 500;
            color: #4caf50;
            margin-bottom: 20px;
        }

        .message {
            font-size: 18px;
            color: #4caf50;
            margin-bottom: 20px;
        }

        .user-info {
            text-align: left;
            margin-top: 20px;
            padding-left: 30px;
            color: #333;
        }

        .user-info p {
            font-size: 16px;
            line-height: 1.5;
        }

        .user-info p span {
            font-weight: bold;
        }

        .back-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 20px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="title">Compte Validé</div>
    <div class="message">{{ $message }}</div>
    
    <div class="user-info">
        <p><span>Email:</span> {{ $utilisateur->email }}</p>
        <p><span>Nom:</span> {{ $utilisateur->nom }}</p>
        <p><span>Prénom:</span> {{ $utilisateur->prenom }}</p>
        <p><span>Date de Naissance:</span> {{ $utilisateur->date_naissance }}</p>
        <p><span>Sexe:</span> {{ $utilisateur->sexe->sexe ?? 'Non spécifié' }}</p>
    </div>
    
    <a href="{{ url('/') }}" class="back-btn">Retour à l'accueil</a>
</div>

</body>
</html>
