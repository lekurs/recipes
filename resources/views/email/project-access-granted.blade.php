<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès accordé au projet</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px 20px;
            border-radius: 0 0 10px 10px;
        }
        .project-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .access-button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .access-button:hover {
            background: #5a6fd8;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>🎉 Accès accordé !</h1>
    <p>Vous avez été invité à collaborer sur un projet</p>
</div>

<div class="content">
    <p>Bonjour <strong>{{ $contact->name }}</strong>,</p>

    <p>Excellente nouvelle ! Vous avez reçu l'accès au projet suivant :</p>

    <div class="project-info">
        <h3>📋 {{ $project->name }}</h3>
        @if($project->description)
            <p><strong>Description :</strong> {{ $project->description }}</p>
        @endif
        <p><strong>Votre accès expire le :</strong> {{ $expiresAt->format('d/m/Y à H:i') }}</p>
    </div>

    <p>Avec cet accès, vous pourrez :</p>
    <ul>
        <li>✅ Consulter les détails du projet</li>
        <li>✅ Créer de nouvelles recettes</li>
        <li>✅ Répondre aux recettes existantes</li>
        <li>✅ Collaborer avec l'équipe projet</li>
    </ul>

    <div style="text-align: center;">
        <a href="{{ $accessUrl }}" class="access-button">
            🚀 Accéder au projet
        </a>
    </div>

    <div class="warning">
        <strong>⚠️ Important :</strong> Ce lien est personnel et confidentiel. Ne le partagez pas.
        Votre accès expire le <strong>{{ $expiresAt->format('d/m/Y à H:i') }}</strong>.
    </div>

    <p>Si vous avez des questions ou rencontrez des difficultés, n'hésitez pas à nous contacter.</p>

    <p>
        Cordialement,<br>
        L'équipe {{ config('app.name') }}
    </p>
</div>

<div class="footer">
    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
</div>
</body>
</html>
