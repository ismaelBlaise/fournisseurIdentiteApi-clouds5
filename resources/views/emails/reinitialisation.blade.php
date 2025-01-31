<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du Nombre de Tentatives</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            color: #007BFF;
        }
        p {
            margin: 1rem 0;
            color: #555;
        }
        a {
            display: inline-block;
            padding: 0.8rem 1.2rem;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Réinitialisation du Nombre de Tentatives</h1>
        <p>Merci d'utiliser nos services. Veuillez cliquer sur le lien ci-dessous pour réinitialiser le nombre de tentatives :</p>
        <a href="{{ $resetUrl }}">Réinitialiser</a>
        <div class="footer">
            <p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
        </div>
    </div>
</body>
</html>
