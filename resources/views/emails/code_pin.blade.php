<!DOCTYPE html>
<html>
<head>
    <title>Code PIN de Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
        }

        .code {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
            font-weight: bold;
            border-radius: 4px;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #888;
        }

        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Code PIN de Confirmation</h1>
        <p>Bonjour {{ $prenom }} {{ $nom }},</p>
        <p>Votre code PIN de confirmation est :</p>
        <p class="code">{{ $code }}</p>
        <p>Merci de l'utiliser pour confirmer votre action.</p>

        <div class="footer">
            <p>Si vous n'avez pas demandé cette action, veuillez ignorer ce message.</p>
            <p>Merci !</p>
        </div>
    </div>
</body>
</html>
