<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture N° <?php echo htmlspecialchars($invoice['invoice_number']); ?></title>
    <style>
        /* Styles CSS pour le PDF */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            width: 100%;
            margin: auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .invoice-details, .client-details {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .invoice-details {
            float: right;
            text-align: right;
        }
        .client-details {
            float: left;
            text-align: left;
        }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total-amount {
            text-align: right;
            margin-top: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Facture</h1>
            <p><strong>N°:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?></p>
        </div>

        <div class="client-details">
            <div class="section-title">Informations du Client</div>
            <p><strong>Nom:</strong> <?php echo htmlspecialchars($invoice['client_name']); ?></p>
            <?php if (isset($client['address'])): ?>
                <p><strong>Adresse:</strong> <?php echo htmlspecialchars($client['address']); ?></p>
            <?php endif; ?>
            <?php if (isset($client['phone'])): ?>
                <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($client['phone']); ?></p>
            <?php endif; ?>
            <?php if (isset($client['email'])): ?>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($client['email']); ?></p>
            <?php endif; ?>
        </div>

        <div class="invoice-details">
            <div class="section-title">Détails de la Facture</div>
            <p><strong>Date de Facturation:</strong> <?php echo htmlspecialchars($invoice['invoice_date']); ?></p>
            <p><strong>Date d'Échéance:</strong> <?php echo htmlspecialchars($invoice['due_date']); ?></p>
            <p><strong>Statut:</strong> <?php echo htmlspecialchars($invoice['status']); ?></p>
            <p><strong>ID Facture:</strong> <?php echo htmlspecialchars($invoice['id']); ?></p>
        </div>

        <div style="clear: both;"></div>

        <div class="section-title">Lignes de Facture</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($invoice_lines)): ?>
                    <?php foreach ($invoice_lines as $line): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($line['description']); ?></td>
                            <td><?php echo htmlspecialchars($line['quantity']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($line['unit_price'], 2, ',', ' ')); ?> XAF</td>
                            <td><?php echo htmlspecialchars(number_format($line['quantity'] * $line['unit_price'], 2, ',', ' ')); ?> XAF</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Aucune ligne de facture.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="total-amount">
            Montant Total: <?php echo htmlspecialchars(number_format($invoice['total_amount'], 2, ',', ' ')); ?> XAF
        </div>

        <div class="footer">
            <p>Merci pour votre confiance !</p>
            <p>Généré le <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>
</body>
</html>