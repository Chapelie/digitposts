<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: white; margin: 0;">DigitPosts</h1>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px;">
        <h2 style="color: #1f2937; margin-top: 0;">Réinitialisation de votre mot de passe</h2>
        
        <p>Bonjour {{ $user->firstname }},</p>
        
        <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" 
               style="display: inline-block; background: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Réinitialiser mon mot de passe
            </a>
        </div>
        
        <p style="font-size: 12px; color: #6b7280;">
            Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :<br>
            <a href="{{ $resetUrl }}" style="color: #3b82f6; word-break: break-all;">{{ $resetUrl }}</a>
        </p>
        
        <p style="font-size: 12px; color: #6b7280; margin-top: 30px;">
            <strong>Important :</strong> Ce lien est valide pendant 60 minutes. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
        </p>
        
        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #6b7280; margin: 0;">
            Cordialement,<br>
            L'équipe DigitPosts
        </p>
    </div>
</body>
</html>
