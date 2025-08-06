<?php
// ===== IMPRESSION_DEVIS.PHP - Impression PDF GSN ProDevis360° =====
// Page d'impression optimisée pour PDF avec design professionnel

require_once 'functions.php';

$pdo = getDbConnection();
$projet_id = intval($_GET['projet_id'] ?? 0);
$devis_id = intval($_GET['devis_id'] ?? 0);

if ($projet_id <= 0 || $devis_id <= 0) {
    header("Location: liste_projets.php");
    exit();
}

// Récupération des informations complètes
try {
    $stmt = $pdo->prepare("
        SELECT p.nom as projet_nom, p.client, p.adresse, p.description as projet_description,
               d.numero as devis_numero, d.description as devis_description, d.statut, d.date_creation
        FROM projets p 
        JOIN devis d ON p.id = d.projet_id 
        WHERE p.id = ? AND d.id = ?
    ");
    $stmt->execute([$projet_id, $devis_id]);
    $infos = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$infos) {
        die("Projet ou devis non trouvé");
    }
} catch (PDOException $e) {
    die("Erreur de récupération des informations : " . $e->getMessage());
}

// Récupération du récapitulatif complet
try {
    $stmt = $pdo->prepare("
        SELECT * FROM recapitulatif 
        WHERE projet_id = ? AND devis_id = ? 
        ORDER BY categorie
    ");
    $stmt->execute([$projet_id, $devis_id]);
    $recapitulatif = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recapitulatif = [];
}

// Calcul des totaux généraux
$totaux_generaux = calculerTotauxDevis($pdo, $projet_id, $devis_id);

// Modules détaillés
$modules = [
    'materiaux_base' => 'Matériaux de Base',
    'plomberie' => 'Plomberie', 
    'menuiserie' => 'Menuiserie',
    'electricite' => 'Électricité',
    'peinture' => 'Peinture',
    'charpenterie' => 'Charpenterie',
    'carrelage' => 'Carrelage',
    'ferraillage' => 'Ferraillage',
    'ferronnerie' => 'Ferronnerie'
];

// Récupération des détails de chaque module pour l'annexe
$details_modules = [];
foreach ($modules as $module_key => $module_nom) {
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$module_key]);
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("
                SELECT designation, unite, quantite, pu, pt, transport, observation 
                FROM `$module_key` 
                WHERE projet_id = ? AND devis_id = ? 
                ORDER BY designation
            ");
            $stmt->execute([$projet_id, $devis_id]);
            $elements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($elements)) {
                $details_modules[$module_key] = [
                    'nom' => $module_nom,
                    'elements' => $elements
                ];
            }
        }
    } catch (PDOException $e) {
        // Module non trouvé, on continue
    }
}

// Informations de l'entreprise (à personnaliser)
$entreprise = [
    'nom' => 'GSN EXPERTISES',
    'adresse' => 'Abidjan, Côte d\'Ivoire',
    'telephone' => '+225 XX XX XX XX XX',
    'email' => 'contact@gsnexpertises.com',
    'site_web' => 'www.gsnexpertises.com',
    'siret' => 'SIRET : XXXXXXXXXXXXXX',
    'tva' => 'N° TVA : CIXXXXXXXXXXX'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis <?= htmlspecialchars($infos['devis_numero']) ?> - <?= htmlspecialchars($infos['client']) ?></title>
    
    <style>
        /* ===== STYLES OPTIMISÉS POUR L'IMPRESSION ===== */
        :root {
            --orange: #FF6B35;
            --orange-dark: #E65100;
            --blue: #2196F3;
            --green: #4CAF50;
            --red: #F44336;
            --dark: #1A1A1A;
            --gray: #757575;
            --light-gray: #F5F5F5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            color: var(--dark);
            background: white;
        }

        /* En-tête entreprise */
        .header-entreprise {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--orange);
        }

        .logo-entreprise {
            flex: 1;
        }

        .logo-entreprise h1 {
            color: var(--orange);
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .logo-entreprise .slogan {
            color: var(--gray);
            font-style: italic;
            font-size: 11pt;
        }

        .info-entreprise {
            flex: 1;
            text-align: right;
            font-size: 10pt;
            color: var(--gray);
        }

        .info-entreprise div {
            margin-bottom: 3px;
        }

        /* Titre du devis */
        .titre-devis {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: white;
            border-radius: 8px;
        }

        .titre-devis h2 {
            font-size: 18pt;
            margin-bottom: 10px;
        }

        .titre-devis .numero {
            font-size: 14pt;
            font-weight: normal;
        }

        /* Informations client et projet */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-client, .info-projet {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 0 5px;
        }

        .info-client h3, .info-projet h3 {
            color: var(--orange);
            font-size: 12pt;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-client p, .info-projet p {
            margin-bottom: 5px;
            font-size: 11pt;
        }

        /* Tableau récapitulatif */
        .tableau-recapitulatif {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            font-size: 11pt;
        }

        .tableau-recapitulatif th {
            background: var(--orange);
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .tableau-recapitulatif td {
            padding: 10px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .tableau-recapitulatif tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .tableau-recapitulatif tbody tr:hover {
            background-color: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Totaux */
        .section-totaux {
            margin: 30px 0;
            padding: 20px;
            background: var(--light-gray);
            border-radius: 8px;
            border: 2px solid var(--orange);
        }

        .totaux-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: white;
            border-radius: 5px;
        }

        .total-item.highlight {
            background: var(--orange);
            color: white;
            font-weight: bold;
            font-size: 14pt;
        }

        .total-label {
            font-weight: 600;
        }

        .total-value {
            font-weight: bold;
        }

        /* Montant en lettres */
        .montant-lettres {
            margin: 20px 0;
            padding: 15px;
            background: white;
            border: 2px solid var(--green);
            border-radius: 8px;
            text-align: center;
        }

        .montant-lettres h4 {
            color: var(--green);
            margin-bottom: 10px;
        }

        .montant-lettres p {
            font-style: italic;
            font-size: 13pt;
            font-weight: bold;
        }

        /* Conditions */
        .conditions {
            margin: 30px 0;
            font-size: 10pt;
        }

        .conditions h3 {
            color: var(--orange);
            margin-bottom: 15px;
            font-size: 12pt;
        }

        .conditions ul {
            list-style-type: disc;
            margin-left: 20px;
        }

        .conditions li {
            margin-bottom: 5px;
        }

        /* Signature */
        .section-signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
            border: 1px solid #ddd;
            padding: 20px;
            min-height: 80px;
        }

        .signature-box h4 {
            margin-bottom: 15px;
            color: var(--dark);
        }

        /* Annexes */
        .annexes {
            page-break-before: always;
            margin-top: 40px;
        }

        .annexes h2 {
            color: var(--orange);
            margin-bottom: 20px;
            font-size: 16pt;
            border-bottom: 2px solid var(--orange);
            padding-bottom: 10px;
        }

        .module-detail {
            margin-bottom: 30px;
            break-inside: avoid;
        }

        .module-detail h3 {
            color: var(--blue);
            margin-bottom: 15px;
            font-size: 14pt;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 20px;
        }

        .detail-table th {
            background: var(--blue);
            color: white;
            padding: 8px 6px;
            font-size: 9pt;
            text-align: left;
        }

        .detail-table td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 9pt;
        }

        .detail-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Pied de page */
        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 9pt;
            color: var(--gray);
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Styles d'impression */
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .footer {
                position: fixed;
                bottom: 0;
            }
            
            /* Forcer les couleurs en impression */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* Styles pour écran */
        @media screen {
            body {
                padding: 40px;
                background: #f5f5f5;
            }
            
            .container {
                max-width: 210mm;
                margin: 0 auto;
                background: white;
                padding: 40px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                border-radius: 8px;
            }
            
            .print-actions {
                text-align: center;
                margin-bottom: 30px;
                padding: 20px;
                background: var(--light-gray);
                border-radius: 8px;
            }
            
            .btn {
                display: inline-block;
                padding: 12px 24px;
                margin: 0 10px;
                background: var(--orange);
                color: white;
                text-decoration: none;
                border-radius: 6px;
                font-weight: bold;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                font-size: 12pt;
            }
            
            .btn:hover {
                background: var(--orange-dark);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            
            .btn-secondary {
                background: var(--gray);
            }
            
            .btn-secondary:hover {
                background: #555;
            }
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 20px;
            }
            
            .info-section {
                flex-direction: column;
            }
            
            .info-client, .info-projet {
                margin: 5px 0;
            }
            
            .totaux-grid {
                grid-template-columns: 1fr;
            }
            
            .section-signature {
                flex-direction: column;
            }
            
            .signature-box {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Actions d'impression (masquées à l'impression) -->
        <div class="print-actions no-print">
            <h3 style="margin-bottom: 15px; color: var(--dark);">
                <i class="fas fa-print"></i> Impression du Devis
            </h3>
            <button onclick="window.print()" class="btn">
                🖨️ Imprimer / Sauvegarder PDF
            </button>
            <a href="devis_detail.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-secondary">
                ← Retour au Devis
            </a>
            <button onclick="downloadPDF()" class="btn btn-secondary">
                📄 Télécharger PDF
            </button>
        </div>

        <!-- En-tête entreprise -->
        <div class="header-entreprise">
            <div class="logo-entreprise">
                <h1><?= htmlspecialchars($entreprise['nom']) ?></h1>
                <div class="slogan">Solutions professionnelles de devis et expertises</div>
            </div>
            <div class="info-entreprise">
                <div><strong><?= htmlspecialchars($entreprise['adresse']) ?></strong></div>
                <div>Tél: <?= htmlspecialchars($entreprise['telephone']) ?></div>
                <div>Email: <?= htmlspecialchars($entreprise['email']) ?></div>
                <div>Web: <?= htmlspecialchars($entreprise['site_web']) ?></div>
                <div style="margin-top: 10px; font-size: 9pt;">
                    <?= htmlspecialchars($entreprise['siret']) ?><br>
                    <?= htmlspecialchars($entreprise['tva']) ?>
                </div>
            </div>
        </div>

        <!-- Titre du devis -->
        <div class="titre-devis">
            <h2>DEVIS</h2>
            <div class="numero"><?= htmlspecialchars($infos['devis_numero']) ?></div>
        </div>

        <!-- Informations client et projet -->
        <div class="info-section">
            <div class="info-client">
                <h3>📋 INFORMATIONS CLIENT</h3>
                <p><strong>Client :</strong> <?= htmlspecialchars($infos['client']) ?></p>
                <p><strong>Adresse :</strong> <?= htmlspecialchars($infos['adresse'] ?? 'Adresse à préciser') ?></p>
                <p><strong>Date du devis :</strong> <?= date('d/m/Y', strtotime($infos['date_creation'])) ?></p>
                <p><strong>Statut :</strong> <?= ucfirst(str_replace('_', ' ', $infos['statut'])) ?></p>
            </div>
            
            <div class="info-projet">
                <h3>🏗️ INFORMATIONS PROJET</h3>
                <p><strong>Projet :</strong> <?= htmlspecialchars($infos['projet_nom']) ?></p>
                <p><strong>Description :</strong> <?= htmlspecialchars($infos['projet_description'] ?: 'Aucune description') ?></p>
                <p><strong>Devis :</strong> <?= htmlspecialchars($infos['devis_description'] ?: 'Aucune description') ?></p>
                <p><strong>Validité :</strong> 30 jours à compter de la date d'émission</p>
            </div>
        </div>

        <!-- Tableau récapitulatif -->
        <table class="tableau-recapitulatif">
            <thead>
                <tr>
                    <th style="width: 30%;">Catégorie</th>
                    <th style="width: 15%;">Matériaux (FCFA)</th>
                    <th style="width: 15%;">Transport (FCFA)</th>
                    <th style="width: 15%;">Main d'œuvre (FCFA)</th>
                    <th style="width: 12%;">TVA (18%)</th>
                    <th style="width: 13%;">Total TTC (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recapitulatif)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--gray); padding: 30px;">
                        <em>Aucune donnée disponible dans le récapitulatif</em>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($recapitulatif as $ligne): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($ligne['nom_categorie'] ?? ucfirst(str_replace('_', ' ', $ligne['categorie']))) ?></strong>
                            <?php if (isset($ligne['nb_elements']) && $ligne['nb_elements'] > 0): ?>
                                <br><small style="color: var(--gray);">(<?= $ligne['nb_elements'] ?> élément<?= $ligne['nb_elements'] > 1 ? 's' : '' ?>)</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-right"><?= number_format($ligne['total_materiaux'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format($ligne['total_transport'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format($ligne['main_oeuvre'], 0, ',', ' ') ?></td>
                        <td class="text-right"><?= number_format($ligne['montant_tva'], 0, ',', ' ') ?></td>
                        <td class="text-right font-bold"><?= number_format($ligne['total_ttc'], 0, ',', ' ') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background: var(--orange); color: white; font-weight: bold; font-size: 12pt;">
                    <td><strong>TOTAUX GÉNÉRAUX</strong></td>
                    <td class="text-right"><?= number_format($totaux_generaux['total_materiaux'], 0, ',', ' ') ?></td>
                    <td class="text-right"><?= number_format($totaux_generaux['total_transport'], 0, ',', ' ') ?></td>
                    <td class="text-right"><?= number_format($totaux_generaux['total_main_oeuvre'], 0, ',', ' ') ?></td>
                    <td class="text-right"><?= number_format($totaux_generaux['total_tva'], 0, ',', ' ') ?></td>
                    <td class="text-right" style="font-size: 14pt;"><?= number_format($totaux_generaux['total_ttc'], 0, ',', ' ') ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- Section des totaux -->
        <div class="section-totaux">
            <div class="totaux-grid">
                <div class="total-item">
                    <span class="total-label">Sous-total HT :</span>
                    <span class="total-value"><?= number_format($totaux_generaux['total_ht'], 0, ',', ' ') ?> FCFA</span>
                </div>
                <div class="total-item">
                    <span class="total-label">TVA (18%) :</span>
                    <span class="total-value"><?= number_format($totaux_generaux['total_tva'], 0, ',', ' ') ?> FCFA</span>
                </div>
                <div class="total-item">
                    <span class="total-label">Remise éventuelle :</span>
                    <span class="total-value">0 FCFA</span>
                </div>
                <div class="total-item highlight">
                    <span class="total-label">TOTAL GÉNÉRAL TTC :</span>
                    <span class="total-value"><?= number_format($totaux_generaux['total_ttc'], 0, ',', ' ') ?> FCFA</span>
                </div>
            </div>
            
            <!-- Montant en lettres -->
            <div class="montant-lettres">
                <h4>💰 Montant en lettres</h4>
                <p><?= montantEnLettres($totaux_generaux['total_ttc']) ?></p>
            </div>
        </div>

        <!-- Conditions générales -->
        <div class="conditions">
            <h3>📋 CONDITIONS GÉNÉRALES</h3>
            <ul>
                <li><strong>Validité du devis :</strong> Ce devis est valable 30 jours à compter de sa date d'émission.</li>
                <li><strong>Modalités de paiement :</strong> 
                    <ul>
                        <li>30% d'acompte à la commande</li>
                        <li>40% en cours de réalisation</li>
                        <li>30% solde à la livraison</li>
                    </ul>
                </li>
                <li><strong>Délai d'exécution :</strong> À définir selon la complexité du projet et après validation du devis.</li>
                <li><strong>Garantie :</strong> Garantie décennale conformément aux normes en vigueur.</li>
                <li><strong>Assurance :</strong> Travaux couverts par notre assurance responsabilité civile professionnelle.</li>
                <li><strong>Révision de prix :</strong> Les prix peuvent être révisés en cas de fluctuation importante des coûts des matériaux (> 10%).</li>
                <li><strong>Livraison :</strong> Les frais de transport sont inclus dans un rayon de 50 km autour d'Abidjan.</li>
                <li><strong>Force majeure :</strong> GSN Expertises ne pourra être tenue responsable de retards dus à des cas de force majeure.</li>
            </ul>
        </div>

        <!-- Section signature -->
        <div class="section-signature">
            <div class="signature-box">
                <h4>Le Client</h4>
                <p style="font-size: 10pt; margin-top: 15px;">Bon pour accord</p>
                <p style="font-size: 10pt;">Date et signature :</p>
            </div>
            <div class="signature-box">
                <h4>GSN EXPERTISES</h4>
                <p style="font-size: 10pt; margin-top: 15px;">Le Responsable</p>
                <p style="font-size: 10pt;">Date et signature :</p>
            </div>
        </div>

        <!-- Page d'annexes détaillées -->
        <?php if (!empty($details_modules)): ?>
        <div class="annexes">
            <h2>📎 ANNEXES - DÉTAIL PAR MODULE</h2>
            <p style="margin-bottom: 20px; font-size: 11pt; color: var(--gray);">
                <em>Détail des éléments composant chaque module du devis</em>
            </p>
            
            <?php foreach ($details_modules as $module_key => $module_data): ?>
            <div class="module-detail">
                <h3>
                    <?php
                    $icons = [
                        'materiaux_base' => '🧱',
                        'plomberie' => '🚿',
                        'menuiserie' => '🚪',
                        'electricite' => '⚡',
                        'peinture' => '🎨',
                        'charpenterie' => '🔨',
                        'carrelage' => '🏠',
                        'ferraillage' => '🔩',
                        'ferronnerie' => '🛡️'
                    ];
                    echo $icons[$module_key] ?? '📦';
                    ?>
                    <?= htmlspecialchars($module_data['nom']) ?>
                    <span style="font-size: 10pt; color: var(--gray); font-weight: normal;">
                        (<?= count($module_data['elements']) ?> élément<?= count($module_data['elements']) > 1 ? 's' : '' ?>)
                    </span>
                </h3>
                
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="width: 35%;">Désignation</th>
                            <th style="width: 8%;">Unité</th>
                            <th style="width: 10%;">Quantité</th>
                            <th style="width: 12%;">Prix Unit.</th>
                            <th style="width: 12%;">Sous-total</th>
                            <th style="width: 10%;">Transport</th>
                            <th style="width: 13%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($module_data['elements'] as $element): 
                            $sous_total = $element['quantite'] * $element['pu'];
                            $total_element = $sous_total + $element['transport'];
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($element['designation']) ?></strong>
                                <?php if (!empty($element['observation'])): ?>
                                    <br><small style="color: var(--gray);"><?= htmlspecialchars($element['observation']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($element['unite']) ?></td>
                            <td class="text-right"><?= number_format($element['quantite'], 2, ',', ' ') ?></td>
                            <td class="text-right"><?= number_format($element['pu'], 0, ',', ' ') ?></td>
                            <td class="text-right"><?= number_format($sous_total, 0, ',', ' ') ?></td>
                            <td class="text-right"><?= number_format($element['transport'], 0, ',', ' ') ?></td>
                            <td class="text-right font-bold"><?= number_format($total_element, 0, ',', ' ') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <?php
                        $total_module_materiaux = array_sum(array_map(function($e) { return $e['quantite'] * $e['pu']; }, $module_data['elements']));
                        $total_module_transport = array_sum(array_column($module_data['elements'], 'transport'));
                        $total_module_general = $total_module_materiaux + $total_module_transport;
                        ?>
                        <tr style="background: var(--blue); color: white; font-weight: bold;">
                            <td colspan="4"><strong>TOTAL <?= strtoupper($module_data['nom']) ?></strong></td>
                            <td class="text-right"><?= number_format($total_module_materiaux, 0, ',', ' ') ?></td>
                            <td class="text-right"><?= number_format($total_module_transport, 0, ',', ' ') ?></td>
                            <td class="text-right"><?= number_format($total_module_general, 0, ',', ' ') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Informations complémentaires -->
        <div style="margin-top: 40px; font-size: 10pt; color: var(--gray); border-top: 1px solid #ddd; padding-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p><strong>Document généré le :</strong> <?= date('d/m/Y à H:i') ?></p>
                    <p><strong>Par :</strong> ProDevis360° - GSN Expertises</p>
                    <p><strong>Version :</strong> <?= $infos['devis_numero'] ?></p>
                </div>
                <div style="text-align: right;">
                    <p><strong>Contact urgence :</strong> <?= htmlspecialchars($entreprise['telephone']) ?></p>
                    <p><strong>Email support :</strong> <?= htmlspecialchars($entreprise['email']) ?></p>
                    <p><strong>Site web :</strong> <?= htmlspecialchars($entreprise['site_web']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pied de page fixe (pour impression) -->
    <div class="footer">
        <?= htmlspecialchars($entreprise['nom']) ?> - <?= htmlspecialchars($entreprise['adresse']) ?> - 
        Tél: <?= htmlspecialchars($entreprise['telephone']) ?> - 
        Email: <?= htmlspecialchars($entreprise['email']) ?> - 
        <?= htmlspecialchars($entreprise['site_web']) ?>
    </div>

    <script>
        // ===== SCRIPTS POUR L'IMPRESSION ET PDF =====
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 Page d\'impression chargée');
            console.log('Devis:', '<?= $infos['devis_numero'] ?>');
            console.log('Client:', '<?= $infos['client'] ?>');
            console.log('Total TTC:', '<?= number_format($totaux_generaux['total_ttc'], 0, ',', ' ') ?> FCFA');
            
            // Vérifier si on arrive directement pour impression
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('auto_print') === '1') {
                setTimeout(() => {
                    window.print();
                }, 1000);
            }
            
            // Optimisation pour l'impression
            window.addEventListener('beforeprint', function() {
                console.log('🖨️ Début d\'impression...');
                document.title = 'Devis_<?= $infos['devis_numero'] ?>_<?= date('Y-m-d') ?>';
                
                // Masquer les éléments non imprimables
                const noPrintElements = document.querySelectorAll('.no-print');
                noPrintElements.forEach(el => el.style.display = 'none');
                
                // Ajuster les tailles pour l'impression
                document.body.style.fontSize = '11pt';
                document.body.style.lineHeight = '1.3';
            });
            
            window.addEventListener('afterprint', function() {
                console.log('✅ Impression terminée');
                
                // Restaurer l'affichage normal
                const noPrintElements = document.querySelectorAll('.no-print');
                noPrintElements.forEach(el => el.style.display = '');
                
                document.body.style.fontSize = '';
                document.body.style.lineHeight = '';
            });
        });
        
        // Fonction pour télécharger en PDF (utilise l'impression du navigateur)
        function downloadPDF() {
            // Changer le titre pour le nom du fichier PDF
            const originalTitle = document.title;
            document.title = `Devis_<?= $infos['devis_numero'] ?>_<?= htmlspecialchars($infos['client']) ?>_<?= date('Y-m-d') ?>`.replace(/[^a-zA-Z0-9_-]/g, '_');
            
            // Déclencher l'impression (qui permettra de sauvegarder en PDF)
            window.print();
            
            // Restaurer le titre original après un délai
            setTimeout(() => {
                document.title = originalTitle;
            }, 2000);
        }
        
        // Fonction pour envoyer le devis par email (simulation)
        function envoyerParEmail() {
            const sujet = encodeURIComponent(`Devis ${<?= json_encode($infos['devis_numero']) ?>} - ${<?= json_encode($infos['client']) ?>}`);
            const corps = encodeURIComponent(`
Bonjour,

Veuillez trouver ci-joint notre devis ${<?= json_encode($infos['devis_numero']) ?>} pour le projet "${<?= json_encode($infos['projet_nom']) ?>}".

Montant total TTC : ${<?= json_encode(number_format($totaux_generaux['total_ttc'], 0, ',', ' ')) ?>} FCFA

Ce devis est valable 30 jours à compter de sa date d'émission.

Pour toute question, n'hésitez pas à nous contacter.

Cordialement,
L'équipe GSN Expertises
            `);
            
            window.location.href = `mailto:?subject=${sujet}&body=${corps}`;
        }
        
        // Raccourcis clavier
        document.addEventListener('keydown', function(e) {
            // Ctrl + P pour imprimer
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            
            // Ctrl + S pour sauvegarder (déclenche l'impression)
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                downloadPDF();
            }
            
            // Échap pour retourner au devis
            if (e.key === 'Escape') {
                window.location.href = `devis_detail.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>`;
            }
        });
        
        // Ajout d'informations de debug (masquées à l'impression)
        if (window.location.search.includes('debug=1')) {
            const debugInfo = document.createElement('div');
            debugInfo.className = 'no-print';
            debugInfo.style.cssText = `
                position: fixed;
                top: 10px;
                right: 10px;
                background: rgba(0,0,0,0.8);
                color: white;
                padding: 10px;
                border-radius: 5px;
                font-size: 10pt;
                z-index: 1000;
            `;
            debugInfo.innerHTML = `
                <strong>Debug Info:</strong><br>
                Projet: <?= $projet_id ?><br>
                Devis: <?= $devis_id ?><br>
                Modules: <?= count($details_modules) ?><br>
                Total TTC: <?= $totaux_generaux['total_ttc'] ?><br>
                Générée: <?= date('H:i:s') ?>
            `;
            document.body.appendChild(debugInfo);
        }
        
        // Analytics simple (pour usage interne)
        const analytics = {
            devis_id: <?= $devis_id ?>,
            projet_id: <?= $projet_id ?>,
            client: <?= json_encode($infos['client']) ?>,
            total_ttc: <?= $totaux_generaux['total_ttc'] ?>,
            date_impression: new Date().toISOString(),
            user_agent: navigator.userAgent,
            modules_count: <?= count($details_modules) ?>
        };
        
        console.log('📊 Analytics:', analytics);
        
        // Envoyer les analytics (vous pouvez implémenter un endpoint)
        // fetch('/analytics/impression_devis.php', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify(analytics)
        // });
    </script>
</body>
</html>