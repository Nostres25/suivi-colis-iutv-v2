<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background-color: #283253; color: #ffffff; padding: 20px 30px; }
        .header-logos { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; }
        .body { padding: 30px; color: #333333; line-height: 1.6; }
        .info-box { background-color: #f8f9fa; border-left: 4px solid #283253; padding: 15px; margin: 20px 0; border-radius: 0 4px 4px 0; }
        .info-box p { margin: 5px 0; }
        .label { font-weight: bold; color: #555555; }
        .footer { padding: 20px 30px; background-color: #f8f9fa; text-align: center; font-size: 12px; color: #888888; }
        .btn { display: inline-block; padding: 10px 24px; background-color: #283253; color: #ffffff; text-decoration: none; border-radius: 5px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-logos">
                <img src="{{ asset('logo.png') }}" alt="Logo IUT" height="30">
                <img src="{{ asset('217.png') }}" alt="Logo Sorbonne" height="30">
            </div>
            <h1>{{ $reason->getSubject($order->getTitle()) }}</h1>
        </div>
        <div class="body">
            <p>Bonjour {{ $recipient->getFullName() }},</p>
            <p>{{ $reason->getDescription() }}</p>

            <div class="info-box">
                <p><span class="label">Commande :</span> {{ $order->getTitle() }}</p>
                <p><span class="label">N° :</span> {{ $order->getOrderNumber() }}</p>
                <p><span class="label">Statut :</span> {{ $order->getStatus()->getDisplayName() }}</p>
                <p><span class="label">Département :</span> {{ $order->getDepartment()->getName() }}</p>
                <p><span class="label">Par :</span> {{ $actor->getFullName() }}</p>
            </div>

            @if($extraMessage)
                <p>{{ $extraMessage }}</p>
            @endif

            <a href="{{ route('orders.index') }}" class="btn">Voir les commandes</a>
        </div>
        <div class="footer">
            <p>Suivi de Colis — IUT de Villetaneuse, Sorbonne Paris Nord</p>
        </div>
    </div>
</body>
</html>
