<?php
// ===== DÉBUT MODULE RECAPITULATIF.PHP - PARTIE 1/3 =====
// Module Récapitulatif avec impression pour GSN ProDevis360°

// Activation des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion des fonctions communes
require_once 'functions.php';

// Connexion BDD
$pdo = getDbConnection();

// Vérification et récupération des paramètres
$projet_id = secureGetParam('projet_id', 'int', 0);
$devis_id = secureGetParam('devis_id', 'int', 0);

if ($projet_id <= 0 || $devis_id <= 0) {
    header("Location: index.php");
    exit();
}

// Récupération des infos projet et devis
$infos = getProjetDevisInfo($pdo, $projet_id, $devis_id);
if (!$infos) {
    die("Projet ou devis non trouvé - <a href='index.php'>Retour à l'accueil</a>");
}

// Configuration des catégories avec libellés, icônes et couleurs
$categories = [
    'plomberie' => ['icon' => 'fa-faucet', 'titre' => 'Plomberie', 'color' => '#3498db'],
    'menuiserie' => ['icon' => 'fa-door-open', 'titre' => 'Menuiserie', 'color' => '#8e44ad'],
    'electricite' => ['icon' => 'fa-bolt', 'titre' => 'Électricité', 'color' => '#f1c40f'],
    'peinture' => ['icon' => 'fa-paint-roller', 'titre' => 'Peinture', 'color' => '#e74c3c'],
    'charpenterie' => ['icon' => 'fa-hammer', 'titre' => 'Charpenterie', 'color' => '#d35400'],
    'carrelage' => ['icon' => 'fa-border-style', 'titre' => 'Carrelage', 'color' => '#16a085'],
    'ferraillage' => ['icon' => 'fa-grip-lines', 'titre' => 'Ferraillage', 'color' => '#7f8c8d'],
    'ferronnerie' => ['icon' => 'fa-shield-alt', 'titre' => 'Ferronnerie', 'color' => '#c0392b'],
    'materiaux_base' => ['icon' => 'fa-cubes', 'titre' => 'Matériaux de Base', 'color' => '#2c3e50']
];

// Initialisation des catégories dans le récapitulatif
initializeRecapitulatifCategories($pdo, $projet_id, $devis_id);

// Mise à jour automatique de tous les récapitulatifs
updateRecapitulatif($pdo, $projet_id, $devis_id);

// Traitement de la soumission du formulaire de main d'œuvre
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Réinitialisation de toutes les mains d'œuvre à 0 avant mise à jour
        $stmt = $pdo->prepare("UPDATE recapitulatif SET main_oeuvre = 0, main_oeuvre_maconnerie = 0 WHERE projet_id = ? AND devis_id = ?");
        $stmt->execute([$projet_id, $devis_id]);
        
        // Mise à jour des valeurs soumises
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'main_oeuvre_') === 0) {
                $categorie = str_replace('main_oeuvre_', '', $key);
                $montant = floatval($value);
                
                if ($montant > 0 && array_key_exists($categorie, $categories)) {
                    $stmt = $pdo->prepare("
                        UPDATE recapitulatif SET 
                            main_oeuvre = ?,
                            updated_at = NOW()
                        WHERE projet_id = ? AND devis_id = ? AND categorie = ?
                    ");
                    $stmt->execute([$montant, $projet_id, $devis_id, $categorie]);
                }
            } elseif (strpos($key, 'main_oeuvre_maconnerie_') === 0) {
                $categorie = str_replace('main_oeuvre_maconnerie_', '', $key);
                $montant = floatval($value);
                
                if ($montant > 0 && array_key_exists($categorie, $categories)) {
                    $stmt = $pdo->prepare("
                        UPDATE recapitulatif SET 
                            main_oeuvre_maconnerie = ?,
                            updated_at = NOW()
                        WHERE projet_id = ? AND devis_id = ? AND categorie = ?
                    ");
                    $stmt->execute([$montant, $projet_id, $devis_id, $categorie]);
                }
            }
        }
        
        // Recalcul des totaux HT et TTC pour toutes les lignes
        $stmt = $pdo->prepare("
            UPDATE recapitulatif SET
                total_ht = total_materiaux + total_transport + main_oeuvre + main_oeuvre_maconnerie,
                montant_tva = ROUND((total_materiaux + total_transport + main_oeuvre + main_oeuvre_maconnerie) * taux_tva / 100, 0),
                total_ttc = ROUND((total_materiaux + total_transport + main_oeuvre + main_oeuvre_maconnerie) * (1 + taux_tva / 100), 0)
            WHERE projet_id = ? AND devis_id = ?
        ");
        $stmt->execute([$projet_id, $devis_id]);
        
        $pdo->commit();
        
        header("Location: Recapitulatif.php?projet_id=$projet_id&devis_id=$devis_id&updated=1");
        exit();
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}

// Récupération des données du récapitulatif
try {
    $stmt = $pdo->prepare("
        SELECT * FROM recapitulatif 
        WHERE projet_id = ? AND devis_id = ? 
        ORDER BY 
            CASE categorie 
                WHEN 'materiaux_base' THEN 1
                WHEN 'plomberie' THEN 2
                WHEN 'menuiserie' THEN 3
                WHEN 'electricite' THEN 4
                WHEN 'peinture' THEN 5
                WHEN 'charpenterie' THEN 6
                WHEN 'carrelage' THEN 7
                WHEN 'ferraillage' THEN 8
                WHEN 'ferronnerie' THEN 9
                ELSE 10
            END
    ");
    $stmt->execute([$projet_id, $devis_id]);
    $recapitulatifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de récupération : " . $e->getMessage());
}

// Calcul des totaux généraux
$total_materiaux_general = 0;
$total_transport_general = 0;
$total_main_oeuvre_general = 0;
$total_main_oeuvre_maconnerie_general = 0;
$total_ht_general = 0;
$total_tva_general = 0;
$total_ttc_general = 0;

foreach ($recapitulatifs as $recap) {
    $total_materiaux_general += $recap['total_materiaux'];
    $total_transport_general += $recap['total_transport'];
    $total_main_oeuvre_general += $recap['main_oeuvre'];
    $total_main_oeuvre_maconnerie_general += $recap['main_oeuvre_maconnerie'];
    $total_ht_general += $recap['total_ht'];
    $total_tva_general += $recap['montant_tva'];
    $total_ttc_general += $recap['total_ttc'];
}

// Récupération des informations complètes du projet
try {
    $stmt = $pdo->prepare("
        SELECT p.*, d.numero as devis_numero, d.description as devis_description, 
               d.date_creation as devis_date_creation, d.statut as devis_statut
        FROM projets p 
        JOIN devis d ON p.id = d.projet_id 
        WHERE p.id = ? AND d.id = ?
    ");
    $stmt->execute([$projet_id, $devis_id]);
    $projet_info = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur récupération projet : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif - GSN ProDevis360°</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #FF7D33;
            --primary-light: #FF9E66;
            --secondary: #E64D00;
            --accent: #FFB833;
            --dark: #1a1a2e;
            --light: #fff5f0;
            --success: #33CC33;
            --warning: #FF9933;
            --danger: #FF3333;
            --gray: #7d7d7d;
            --light-gray: #f5f5f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Styles d'impression */
        @media print {
            body {
                background: white;
                color: black;
                font-size: 11pt;
                line-height: 1.4;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            .container {
                max-width: none;
                padding: 0;
                margin: 0;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .avoid-break {
                page-break-inside: avoid;
            }
            
            table {
                border-collapse: collapse;
                width: 100%;
            }
            
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }
            
            th {
                background-color: #f0f0f0;
                font-weight: bold;
            }
            
            .print-header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #000;
                padding-bottom: 20px;
            }
            
            .print-title {
                font-size: 18pt;
                font-weight: bold;
                margin-bottom: 10px;
            }
            
            .print-info {
                font-size: 12pt;
            }
            
            .totals-final {
                margin-top: 20px;
                border: 2px solid #000;
                padding: 15px;
            }
            
            .signature-section {
                margin-top: 50px;
                display: flex;
                justify-content: space-between;
            }
            
            .signature-box {
                width: 200px;
                text-align: center;
            }
            
            .signature-line {
                border-top: 1px solid #000;
                margin-top: 50px;
                padding-top: 5px;
            }
        }

        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-container img {
            height: 50px;
        }

        .nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.7rem 1.2rem;
            border-radius: 50px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .devis-info {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
        }

        .project-info {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .project-info-item {
            display: flex;
            flex-direction: column;
        }

        .project-info-label {
            font-weight: 600;
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .project-info-value {
            font-size: 1.1rem;
            color: var(--dark);
            font-weight: 500;
        }

        .actions-bar {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 4px 15px rgba(255, 125, 51, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 125, 51, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), #28a745);
            color: white;
            box-shadow: 0 4px 15px rgba(51, 204, 51, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(51, 204, 51, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning), #fd7e14);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 153, 51, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 153, 51, 0.4);
        }

        .print-only {
            display: none;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: rgba(51, 204, 51, 0.1);
            border: 1px solid rgba(51, 204, 51, 0.3);
            color: var(--success);
        }

        .alert-error {
            background: rgba(255, 51, 51, 0.1);
            border: 1px solid rgba(255, 51, 51, 0.3);
            color: var(--danger);
        }

        .fade-in {
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- En-tête pour impression -->
    <div class="print-only">
        <div class="print-header">
            <div class="print-title">GSN EXPERTISES GROUP</div>
            <div class="print-info">
                DEVIS N° <?= htmlspecialchars($projet_info['devis_numero']) ?><br>
                Projet: <?= htmlspecialchars($projet_info['nom']) ?><br>
                Client: <?= htmlspecialchars($projet_info['client']) ?><br>
                Date: <?= date('d/m/Y') ?>
            </div>
        </div>
    </div>

    <!-- En-tête navigation (non imprimé) -->
    

    <main class="container">
        <!-- Messages de notification -->
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success no-print">
                <i class="fas fa-check-circle"></i>
                Les données ont été mises à jour avec succès !
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error no-print">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Informations du projet -->
        <section class="project-info fade-in avoid-break">
            <div class="project-info-item">
                <div class="project-info-label">Projet</div>
                <div class="project-info-value"><?= htmlspecialchars($projet_info['nom']) ?></div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">Client</div>
                <div class="project-info-value"><?= htmlspecialchars($projet_info['client'] ?: 'Non défini') ?></div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">Adresse</div>
                <div class="project-info-value"><?= htmlspecialchars($projet_info['adresse'] ?: 'Non définie') ?></div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">Devis N°</div>
                <div class="project-info-value"><?= htmlspecialchars($projet_info['devis_numero']) ?></div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">Date de création</div>
                <div class="project-info-value"><?= date('d/m/Y', strtotime($projet_info['devis_date_creation'])) ?></div>
            </div>
            <div class="project-info-item">
                <div class="project-info-label">Statut</div>
                <div class="project-info-value">
                    <span class="status-badge status-<?= $projet_info['devis_statut'] ?>">
                        <?= ucfirst($projet_info['devis_statut']) ?>
                    </span>
                </div>
            </div>
        </section>

        <!-- Barre d'actions -->
        <section class="actions-bar no-print">
            <div>
                <h2 style="margin: 0; color: var(--dark);">
                    <i class="fas fa-calculator"></i>
                    Récapitulatif du devis
                </h2>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i>
                    Imprimer le devis
                </button>
                <button onclick="exportToPDF()" class="btn btn-warning">
                    <i class="fas fa-file-pdf"></i>
                    Exporter PDF
                </button>
                <button onclick="saveReport()" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    Sauvegarder
                </button>
            </div>
        </section>

        <!-- Formulaire de main d'œuvre -->
        <section class="no-print">
            <form method="POST" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: var(--dark); display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user-cog"></i>
                    Main d'œuvre par catégorie
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <?php foreach ($categories as $cat_key => $cat_info): ?>
                        <?php 
                        $recap_data = null;
                        foreach ($recapitulatifs as $recap) {
                            if ($recap['categorie'] === $cat_key) {
                                $recap_data = $recap;
                                break;
                            }
                        }
                        ?>
                        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 1rem; background: #fafafa;">
                            <h4 style="color: <?= $cat_info['color'] ?>; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                                <i class="fas <?= $cat_info['icon'] ?>"></i>
                                <?= $cat_info['titre'] ?>
                            </h4>
                            
                            <div style="display: grid; gap: 0.8rem;">
                                <div>
                                    <label style="display: block; font-size: 0.9rem; margin-bottom: 0.3rem;">Main d'œuvre (FCFA)</label>
                                    <input type="number" 
                                           name="main_oeuvre_<?= $cat_key ?>" 
                                           value="<?= $recap_data ? $recap_data['main_oeuvre'] : 0 ?>"
                                           min="0" 
                                           step="1000"
                                           style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                
                                <div>
                                    <label style="display: block; font-size: 0.9rem; margin-bottom: 0.3rem;">Main d'œuvre maçonnerie (FCFA)</label>
                                    <input type="number" 
                                           name="main_oeuvre_maconnerie_<?= $cat_key ?>" 
                                           value="<?= $recap_data ? $recap_data['main_oeuvre_maconnerie'] : 0 ?>"
                                           min="0" 
                                           step="1000"
                                           style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Mettre à jour les main d'œuvre
                    </button>
                </div>
            </form>
        </section>

        <!-- Tableau récapitulatif détaillé -->
        <section class="table-section avoid-break" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); margin-bottom: 2rem;">
            <div style="background: var(--primary); color: white; padding: 1.5rem; text-align: center;">
                <h2 style="margin: 0; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="fas fa-table"></i>
                    Détail du devis par catégorie
                </h2>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--light-gray);">
                            <th style="padding: 1rem; text-align: left; font-weight: 600; border-bottom: 2px solid #ddd;">Catégorie</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 600; border-bottom: 2px solid #ddd;">Matériaux</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 600; border-bottom: 2px solid #ddd;">Transport</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 600; border-bottom: 2px solid #ddd;">Main d'œuvre</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 600; border-bottom: 2px solid #ddd;">M.O. Maçonnerie</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 600; border-bottom: 2px solid #ddd;">Total HT</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 600; border-bottom: 2px solid #ddd;">TVA (18%)</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 600; border-bottom: 2px solid #ddd;">Total TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recapitulatifs as $recap): ?>
                            <?php 
                            $cat_info = $categories[$recap['categorie']] ?? ['titre' => ucfirst($recap['categorie']), 'icon' => 'fa-cube', 'color' => '#666'];
                            ?>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas <?= $cat_info['icon'] ?>" style="color: <?= $cat_info['color'] ?>;"></i>
                                        <strong><?= $cat_info['titre'] ?></strong>
                                    </div>
                                </td>
                                <td style="padding: 1rem; text-align: right;"><?= number_format($recap['total_materiaux']) ?> FCFA</td>
                                <td style="padding: 1rem; text-align: right;"><?= number_format($recap['total_transport']) ?> FCFA</td>
                                <td style="padding: 1rem; text-align: right;"><?= number_format($recap['main_oeuvre']) ?> FCFA</td>
                                <td style="padding: 1rem; text-align: right;"><?= number_format($recap['main_oeuvre_maconnerie']) ?> FCFA</td>
                                <td style="padding: 1rem; text-align: right; font-weight: 600;"><?= number_format($recap['total_ht']) ?> FCFA</td>
                                <td style="padding: 1rem; text-align: right;"><?= number_format($recap['montant_tva']) ?> FCFA</td>
                                <td style="padding: 1rem; text-align: right; font-weight: 600; color: var(--primary);"><?= number_format($recap['total_ttc']) ?> FCFA</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background: var(--primary); color: white; font-weight: bold;">
                            <td style="padding: 1.5rem; font-size: 1.1rem;">TOTAUX GÉNÉRAUX</td>
                            <td style="padding: 1.5rem; text-align: right; font-size: 1.1rem;"><?= number_format($total_materiaux_general) ?> FCFA</td>
                            <td style="padding: 1.5rem; text-align: right; font-size: 1.1rem;"><?= number_format($total_transport_general) ?> FCFA</td>
                            <td style="padding: 1.5rem; text-align: right; font-size: 1.1rem;"><?= number_format($total_main_oeuvre_general) ?> FCFA</td>
                            <td style="padding: 1.5rem; text-align: right; font-size: 1.1rem;"><?= number_format($total_main_oeuvre_maconnerie_general) ?> FCFA</td>
                            <td style="padding: 1.5rem; text-align: right; font-size: 1.2rem;"><?= number_format($total_ht_general) ?> FCFA</td>
                            <td style="padding: 1.5rem; text-align: right; font-size: 1.1rem;"><?= number_format($total_tva_general) ?> FCFA</td>
                            <td style="padding: 1.5rem; text-align: right; font-size: 1.3rem; background: var(--secondary);"><?= number_format($total_ttc_general) ?> FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <!-- Totaux finaux pour impression -->
        <section class="totals-final avoid-break">
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                <h3 style="text-align: center; margin-bottom: 2rem; color: var(--dark);">
                    <i class="fas fa-calculator"></i>
                    Montant total du devis
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="text-align: center; padding: 1.5rem; background: var(--light-gray); border-radius: 8px;">
                        <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 0.5rem;">Sous-total HT</div>
                        <div style="font-size: 1.5rem; font-weight: 600; color: var(--dark);"><?= number_format($total_ht_general) ?> FCFA</div>
                    </div>
                    
                    <div style="text-align: center; padding: 1.5rem; background: var(--light-gray); border-radius: 8px;">
                        <div style="font-size: 0.9rem; color: var(--gray); margin-bottom: 0.5rem;">TVA (18%)</div>
                        <div style="font-size: 1.5rem; font-weight: 600; color: var(--warning);"><?= number_format($total_tva_general) ?> FCFA</div>
                    </div>
                    
                    <div style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(255, 125, 51, 0.3);">
                        <div style="font-size: 1rem; margin-bottom: 0.5rem; opacity: 0.9;">TOTAL TTC</div>
                        <div style="font-size: 2rem; font-weight: 700;"><?= number_format($total_ttc_general) ?> FCFA</div>
                    </div>
                </div>
                
                <div style="text-align: center; font-style: italic; color: var(--gray); border-top: 1px solid #ddd; padding-top: 1rem;">
                    Montant en lettres : <?= convertirNombreEnLettres($total_ttc_general) ?> francs CFA
                </div>
            </div>
        </section>

        <!-- Mentions légales et signature pour impression -->
        <section class="signature-section avoid-break">
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); margin-top: 2rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start;">
                    <div>
                        <h4 style="margin-bottom: 1rem; color: var(--dark);">Conditions générales</h4>
                        <ul style="font-size: 0.9rem; color: var(--gray); line-height: 1.6;">
                            <li>Devis valable 30 jours</li>
                            <li>Acompte de 50% à la commande</li>
                            <li>Solde à la livraison</li>
                            <li>Matériaux conformes aux normes</li>
                            <li>Garantie selon conditions légales</li>
                        </ul>
                    </div>
                    
                    <div style="text-align: center;">
                        <div style="margin-bottom: 3rem;">
                            <strong>GSN EXPERTISES GROUP</strong><br>
                            <span style="font-size: 0.9rem; color: var(--gray);">
                                Date : <?= date('d/m/Y') ?><br>
                                Établi par : Direction Technique
                            </span>
                        </div>
                        
                        <div class="signature-box">
                            <div style="border-top: 1px solid #000; padding-top: 0.5rem; margin-top: 3rem;">
                                Signature et cachet
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = (index * 0.1) + 's';
            });
            
            // Ajouter les styles pour les badges de statut
            const style = document.createElement('style');
            style.textContent = `
                .status-badge {
                    padding: 0.3rem 0.8rem;
                    border-radius: 20px;
                    font-size: 0.8rem;
                    font-weight: 500;
                    text-transform: uppercase;
                }
                .status-brouillon { background: #ffc107; color: #000; }
                .status-validé { background: #28a745; color: #fff; }
                .status-facturé { background: #17a2b8; color: #fff; }
                .status-payé { background: #6f42c1; color: #fff; }
            `;
            document.head.appendChild(style);
        });

        // Fonction d'export PDF
        function exportToPDF() {
            // Simuler l'export PDF (à implémenter avec une vraie bibliothèque)
            alert('Fonctionnalité d\'export PDF en cours de développement.\nUtilisez "Imprimer" puis "Enregistrer au format PDF" pour le moment.');
        }

        // Fonction de sauvegarde
        async function saveReport() {
            const data = {
                projet_id: <?= $projet_id ?>,
                devis_id: <?= $devis_id ?>,
                date: '<?= date('Y-m-d H:i:s') ?>',
                total_ttc: <?= $total_ttc_general ?>,
                details: {
                    total_ht: <?= $total_ht_general ?>,
                    total_tva: <?= $total_tva_general ?>,
                    categories: <?= json_encode($recapitulatifs) ?>
                }
            };

            try {
                const response = await fetch('save_report.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Rapport sauvegardé avec succès !');
                } else {
                    alert('Erreur lors de la sauvegarde : ' + result.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur de communication avec le serveur');
            }
        }

        // Validation des montants
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 0) this.value = 0;
            });
        });

        // Amélioration de l'impression
        window.addEventListener('beforeprint', function() {
            document.body.classList.add('printing');
        });

        window.addEventListener('afterprint', function() {
            document.body.classList.remove('printing');
        });
    </script>
</body>
</html>

<?php
// Fonction pour convertir un nombre en lettres (simplifiée)
function convertirNombreEnLettres($nombre) {
    if ($nombre == 0) return "zéro";
    
    $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
    $dizaines = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
    
    // Conversion simplifiée pour les montants courants
    if ($nombre < 1000000) {
        $milliers = intval($nombre / 1000);
        $reste = $nombre % 1000;
        
        $resultat = '';
        
        if ($milliers > 0) {
            if ($milliers == 1) {
                $resultat .= 'mille ';
            } else {
                $resultat .= convertirNombreBasique($milliers) . ' mille ';
            }
        }
        
        if ($reste > 0) {
            $resultat .= convertirNombreBasique($reste);
        }
        
        return trim($resultat);
    }
    
    return "montant trop élevé pour conversion";
}

function convertirNombreBasique($nombre) {
    if ($nombre == 0) return '';
    if ($nombre < 10) return ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'][$nombre];
    if ($nombre < 20) return ['dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'][$nombre - 10];
    if ($nombre < 100) {
        $dizaine = intval($nombre / 10);
        $unite = $nombre % 10;
        $dizaines = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
        return $dizaines[$dizaine] . ($unite > 0 ? '-' . convertirNombreBasique($unite) : '');
    }
    if ($nombre < 1000) {
        $centaine = intval($nombre / 100);
        $reste = $nombre % 100;
        $resultat = ($centaine == 1 ? 'cent' : convertirNombreBasique($centaine) . ' cent');
        if ($reste > 0) $resultat .= ' ' . convertirNombreBasique($reste);
        return $resultat;
    }
    return 'nombre trop grand';
}
?>


<style>
/* Styles d'impression avancés pour le récapitulatif */
@media print {
    /* Masquer complètement les éléments non imprimables */
    .no-print, header, .actions-bar, form, .nav, .btn {
        display: none !important;
        visibility: hidden !important;
    }
    
    /* Afficher les éléments d'impression */
    .print-only {
        display: block !important;
        visibility: visible !important;
    }
    
    /* Optimisation de la mise en page */
    body {
        background: white !important;
        color: black !important;
        font-family: 'Times New Roman', serif !important;
        font-size: 11pt !important;
        line-height: 1.3 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .container {
        max-width: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    
    /* En-tête d'impression */
    .print-header {
        text-align: center;
        margin-bottom: 25px;
        border-bottom: 2px solid #000;
        padding-bottom: 15px;
        page-break-after: avoid;
    }
    
    .print-title {
        font-size: 20pt;
        font-weight: bold;
        margin-bottom: 8px;
        color: #000;
    }
    
    .print-info {
        font-size: 11pt;
        line-height: 1.4;
    }
    
    /* Informations du projet */
    .project-info {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #000;
        background: #f8f8f8 !important;
        page-break-inside: avoid;
    }
    
    .project-info-item {
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
    }
    
    .project-info-label {
        font-weight: bold;
        width: 30%;
    }
    
    .project-info-value {
        width: 70%;
        text-align: right;
    }
    
    /* Tableau principal */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-bottom: 20px;
        page-break-inside: auto;
        font-size: 10pt;
    }
    
    th, td {
        border: 1px solid #000 !important;
        padding: 6px 8px !important;
        text-align: left;
        vertical-align: top;
    }
    
    th {
        background-color: #e0e0e0 !important;
        font-weight: bold !important;
        font-size: 9pt;
        text-align: center;
    }
    
    td {
        background-color: white !important;
    }
    
    /* Lignes de totaux */
    tfoot tr {
        background-color: #d0d0d0 !important;
        font-weight: bold !important;
        border-top: 2px solid #000 !important;
    }
    
    tfoot td {
        background-color: #d0d0d0 !important;
        font-weight: bold !important;
    }
    
    /* Totaux finaux */
    .totals-final {
        margin-top: 25px;
        padding: 15px;
        border: 2px solid #000;
        background: #f0f0f0 !important;
        page-break-inside: avoid;
    }
    
    .totals-final h3 {
        text-align: center;
        margin-bottom: 15px;
        font-size: 14pt;
        text-decoration: underline;
    }
    
    /* Section signature */
    .signature-section {
        margin-top: 40px;
        page-break-inside: avoid;
        border-top: 1px solid #000;
        padding-top: 20px;
    }
    
    .signature-box {
        width: 250px;
        margin: 30px auto 0;
        text-align: center;
    }
    
    .signature-line {
        border-top: 1px solid #000;
        margin-top: 40px;
        padding-top: 5px;
        font-size: 9pt;
    }
    
    /* Éviter les coupures de page */
    .avoid-break {
        page-break-inside: avoid;
    }
    
    .page-break {
        page-break-before: always;
    }
    
    /* Colonnes numériques alignées à droite */
    td:nth-child(2), td:nth-child(3), td:nth-child(4), 
    td:nth-child(5), td:nth-child(6), td:nth-child(7), td:nth-child(8) {
        text-align: right !important;
    }
    
    /* Masquer les icônes en impression */
    .fas, .fab, .far {
        display: none !important;
    }
    
    /* Optimisation des marges pour l'impression */
    @page {
        margin: 20mm 15mm;
        size: A4;
    }
    
    /* Amélioration de la lisibilité */
    .montant-important {
        font-size: 12pt !important;
        font-weight: bold !important;
    }
    
    /* Gestion des longues désignations */
    .categorie-name {
        word-wrap: break-word;
        max-width: 150px;
    }
}
</style>

<script>
// Scripts finaux pour le module Récapitulatif

// Optimisation de l'impression
function optimiserImpression() {
    // Masquer tous les éléments non essentiels
    document.querySelectorAll('.no-print').forEach(el => {
        el.style.display = 'none';
    });
    
    // Afficher les éléments d'impression
    document.querySelectorAll('.print-only').forEach(el => {
        el.style.display = 'block';
    });
    
    // Optimiser les tableaux pour l'impression
    document.querySelectorAll('table').forEach(table => {
        table.style.fontSize = '10pt';
        table.style.pageBreakInside = 'auto';
    });
}

// Restaurer l'affichage après impression
function restaurerAffichage() {
    document.querySelectorAll('.no-print').forEach(el => {
        el.style.display = '';
    });
    
    document.querySelectorAll('.print-only').forEach(el => {
        el.style.display = 'none';
    });
}

// Event listeners pour l'impression
window.addEventListener('beforeprint', optimiserImpression);
window.addEventListener('afterprint', restaurerAffichage);

// Validation avancée des formulaires
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calcul en temps réel
    const inputs = document.querySelectorAll('input[type="number"]');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            // Empêcher les valeurs négatives
            if (this.value < 0) this.value = 0;
            
            // Arrondir à l'entier le plus proche
            if (this.value) {
                this.value = Math.round(parseFloat(this.value));
            }
        });
    });
    
    // Confirmation avant soumission
    const form = document.querySelector('form[method="POST"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const confirmation = confirm('Êtes-vous sûr de vouloir mettre à jour les montants de main d\'œuvre ?');
            if (!confirmation) {
                e.preventDefault();
                return false;
            }
        });
    }
});

// Fonction d'export avancé (à développer)
function exporterDonnees(format) {
    const data = {
        projet: {
            nom: '<?= htmlspecialchars($projet_info["nom"]) ?>',
            client: '<?= htmlspecialchars($projet_info["client"]) ?>',
            devis_numero: '<?= htmlspecialchars($projet_info["devis_numero"]) ?>'
        },
        totaux: {
            ht: <?= $total_ht_general ?>,
            tva: <?= $total_tva_general ?>,
            ttc: <?= $total_ttc_general ?>
        },
        date_export: new Date().toISOString(),
        categories: <?= json_encode($recapitulatifs) ?>
    };
    
    switch(format) {
        case 'json':
            const blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `devis_${data.projet.devis_numero}_${new Date().toISOString().split('T')[0]}.json`;
            a.click();
            URL.revokeObjectURL(url);
            break;
            
        case 'csv':
            let csv = 'Catégorie,Matériaux,Transport,Main d\'œuvre,M.O. Maçonnerie,Total HT,TVA,Total TTC\n';
            data.categories.forEach(cat => {
                const catInfo = <?= json_encode($categories) ?>[cat.categorie] || {titre: cat.categorie};
                csv += `"${catInfo.titre}",${cat.total_materiaux},${cat.total_transport},${cat.main_oeuvre},${cat.main_oeuvre_maconnerie},${cat.total_ht},${cat.montant_tva},${cat.total_ttc}\n`;
            });
            csv += `"TOTAUX",${data.totaux.ht - <?= $total_main_oeuvre_general + $total_main_oeuvre_maconnerie_general ?>},${<?= $total_transport_general ?>},${<?= $total_main_oeuvre_general ?>},${<?= $total_main_oeuvre_maconnerie_general ?>},${data.totaux.ht},${data.totaux.tva},${data.totaux.ttc}`;
            
            const csvBlob = new Blob([csv], {type: 'text/csv'});
            const csvUrl = URL.createObjectURL(csvBlob);
            const csvA = document.createElement('a');
            csvA.href = csvUrl;
            csvA.download = `devis_${data.projet.devis_numero}_${new Date().toISOString().split('T')[0]}.csv`;
            csvA.click();
            URL.revokeObjectURL(csvUrl);
            break;
            
        default:
            alert('Format non supporté');
    }
}

// Raccourcis clavier
document.addEventListener('keydown', function(e) {
    // Ctrl+P pour imprimer
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        window.print();
    }
    
    // Ctrl+S pour sauvegarder
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        saveReport();
    }
    
    // Escape pour fermer les modales (si utilisées plus tard)
    if (e.key === 'Escape') {
        // Fermer les éventuelles modales ouvertes
    }
});

// Notification de succès personnalisée
function afficherNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;
    
    // Styles de notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
        color: ${type === 'success' ? '#155724' : '#721c24'};
        padding: 1rem 1.5rem;
        border-radius: 8px;
        border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    // Supprimer après 5 secondes
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000);
}

// Ajouter les animations CSS pour les notifications
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .notification {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }
    
    .notification:hover {
        opacity: 0.8;
    }
`;
document.head.appendChild(styleSheet);

console.log('Module Récapitulatif chargé avec succès');
</script>

</body>
</html>

