<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis {{ $order->getQuoteNumber() }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; padding: 40px; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left, .header-right { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { text-align: right; }
        .doc-title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 5px; }
        .doc-number { font-size: 14px; color: #7f8c8d; margin-bottom: 20px; }
        .info-block { margin-bottom: 20px; }
        .info-block h3 { font-size: 11px; text-transform: uppercase; color: #7f8c8d; margin-bottom: 5px; letter-spacing: 1px; }
        .info-block p { margin-bottom: 2px; line-height: 1.5; }
        .separator { border: none; border-top: 2px solid #2c3e50; margin: 20px 0; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.items thead th { background-color: #2c3e50; color: #fff; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; }
        table.items tbody td { padding: 10px 12px; border-bottom: 1px solid #e0e0e0; }
        table.items tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .total-section { margin-top: 20px; display: table; width: 100%; }
        .total-box { display: table-cell; width: 50%; text-align: right; vertical-align: top; }
        .total-box table { margin-left: auto; }
        .total-box table td { padding: 5px 12px; }
        .total-label { font-weight: bold; text-align: right; }
        .total-value { text-align: right; min-width: 100px; }
        .total-final { font-size: 16px; font-weight: bold; color: #2c3e50; border-top: 2px solid #2c3e50; }
        .logos { display: table; width: 100%; margin-bottom: 20px; }
        .logo-left, .logo-right { display: table-cell; vertical-align: middle; }
        .logo-left { text-align: left; }
        .logo-right { text-align: right; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e0e0e0; font-size: 10px; color: #999; text-align: center; }
        .description-block { margin-top: 20px; padding: 15px; background-color: #f5f5f5; border-left: 3px solid #2c3e50; }
        .description-block h3 { font-size: 11px; text-transform: uppercase; color: #7f8c8d; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="logos">
        <div class="logo-left">
            <img src="{{ public_path('logo.png') }}" alt="Logo IUT" height="30">
        </div>
        <div class="logo-right">
            <img src="{{ public_path('logo_sorbonne_without_background.png') }}" alt="Logo Sorbonne" height="30">
        </div>
    </div>

    <div class="header">
        <div class="header-left">
            <div class="doc-title">DEVIS</div>
            <div class="doc-number">N° {{ $order->getQuoteNumber() ?? 'Non renseigne' }}</div>
            <div class="info-block">
                <h3>Fournisseur</h3>
                <p><strong>{{ $order->getSupplier()->getCompanyName() }}</strong></p>
                <p>SIRET : {{ $order->getSupplier()->getSiret() }}</p>
                @if($order->getSupplier()->getAttribute('address'))
                    <p>{{ $order->getSupplier()->getAttribute('address') }}</p>
                @endif
                @if($order->getSupplier()->getAttribute('email'))
                    <p>{{ $order->getSupplier()->getAttribute('email') }}</p>
                @endif
                @if($order->getSupplier()->getAttribute('phone_number'))
                    <p>{{ $order->getSupplier()->getAttribute('phone_number') }}</p>
                @endif
            </div>
        </div>
        <div class="header-right">
            <div class="info-block">
                <h3>Date</h3>
                <p>{{ \Carbon\Carbon::parse($order->getCreationDate())->format('d/m/Y') }}</p>
            </div>
            <div class="info-block">
                <h3>Departement</h3>
                <p>{{ $order->getDepartment()->getName() }}</p>
            </div>
            <div class="info-block">
                <h3>N° Commande</h3>
                <p>{{ $order->getOrderNumber() }}</p>
            </div>
        </div>
    </div>

    <hr class="separator">

    <div class="info-block">
        <h3>Designation</h3>
        <p><strong>{{ $order->getTitle() }}</strong></p>
    </div>

    @if($order->getDescription())
        <div class="description-block">
            <h3>Description</h3>
            <p>{{ $order->getDescription() }}</p>
        </div>
    @endif

    @if($order->getPackages()->count() > 0)
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 65%;">Designation</th>
                    <th style="width: 30%;" class="text-right">Cout</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->getPackages() as $index => $package)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $package->getName() }}</td>
                        <td class="text-right">{{ $package->getCostFormatted() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="total-section">
        <div style="display: table-cell; width: 50%;"></div>
        <div class="total-box">
            <table>
                <tr>
                    <td class="total-label total-final">TOTAL</td>
                    <td class="total-value total-final">{{ $order->getCostFormatted() }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Document genere le {{ \Carbon\Carbon::now()->format('d/m/Y \à H:i') }}
    </div>
</body>
</html>
