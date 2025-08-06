<?php
// ===== DEVIS_DETAIL.PHP - CORRECTION DÉFINITIVE =====
// Résolution du problème "Erreur système - Code: 5c982657"

// Activation des erreurs pour développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion des fonctions communes
require_once 'functions.php';

// Fonction de diagnostic améliorée
function logDiagnostic($message, $data = null) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] DEVIS_DETAIL: $message";
    if ($data !== null) {
        $logMessage .= " | Data: " . json_encode($data);
    }
    error_log($logMessage);
    
    // Affichage en développement
    if (isset($_GET['debug'])) {
        echo "<div style='background: #f0f0f0; padding: 10px; margin: 5px; border-left: 4px solid #3498db; font-family: monospace;'>";
        echo "<strong>DEBUG:</strong> $message";
        if ($data) echo "<br><pre>" . print_r($data, true) . "</pre>";
        echo "</div>";
    }
}

// Connexion BDD avec diagnostic
try {
    $pdo = getDbConnection();
    logDiagnostic("Connexion BDD réussie");
} catch (Exception $e) {
    logDiagnostic("ERREUR: Connexion BDD échouée", $e->getMessage());
    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
}

// Récupération et validation des paramètres
$projet_id = isset($_GET['projet_id']) ? (int)$_GET['projet_id'] : 0;
$devis_id = isset($_GET['devis_id']) ? (int)$_GET['devis_id'] : 0;

logDiagnostic("Paramètres reçus", [
    'projet_id' => $projet_id,
    'devis_id' => $devis_id,
    'GET' => $_GET
]);

// Validation des paramètres
if ($projet_id <= 0 || $devis_id <= 0) {
    logDiagnostic("ERREUR: Paramètres invalides", [
        'projet_id' => $projet_id,
        'devis_id' => $devis_id
    ]);
    
    header("Location: liste_projets.php?error=parametres_manquants");
    exit();
}

// Vérifications étape par étape avec diagnostic détaillé
$projet = null;
$devis = null;

try {
    // Étape 1: Vérifier l'existence du projet
    logDiagnostic("Étape 1: Vérification du projet", $projet_id);
    
    $stmt = $pdo->prepare("SELECT id, nom, client, adresse, description, date_creation FROM projets WHERE id = ?");
    $stmt->execute([$projet_id]);
    $projet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$projet) {
        logDiagnostic("ERREUR: Projet non trouvé", $projet_id);
        header("Location: liste_projets.php?error=projet_inexistant&id=$projet_id");
        exit();
    }
    
    logDiagnostic("Projet trouvé", $projet);

    // Étape 2: Vérifier l'existence du devis
    logDiagnostic("Étape 2: Vérification du devis", $devis_id);
    
    $stmt = $pdo->prepare("SELECT id, projet_id, numero, description, date_creation, date_modification, statut FROM devis WHERE id = ?");
    $stmt->execute([$devis_id]);
    $devis = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$devis) {
        logDiagnostic("ERREUR: Devis non trouvé", $devis_id);
        header("Location: projet_detail.php?id=$projet_id&error=devis_inexistant&devis_id=$devis_id");
        exit();
    }
    
    logDiagnostic("Devis trouvé", $devis);

    // Étape 3: Vérifier que le devis appartient au projet
    if ((int)$devis['projet_id'] !== $projet_id) {
        logDiagnostic("ERREUR: Devis n'appartient pas au projet", [
            'devis_projet_id' => $devis['projet_id'],
            'demande_projet_id' => $projet_id
        ]);
        
        // Rediriger vers le bon projet
        header("Location: devis_detail.php?projet_id=" . $devis['projet_id'] . "&devis_id=$devis_id");
        exit();
    }
    
    logDiagnostic("Vérification projet-devis OK");

} catch (PDOException $e) {
    logDiagnostic("ERREUR PDO", [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    // Affichage d'erreur plus informatif
    die("
    <div style='font-family: Arial; padding: 20px; background: #f8f9fa;'>
        <h2 style='color: #e74c3c;'>Erreur de Base de Données</h2>
        <p><strong>Détails:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p><strong>Projet ID:</strong> $projet_id</p>
        <p><strong>Devis ID:</strong> $devis_id</p>
        <p><a href='liste_projets.php' style='color: #3498db;'>← Retour aux projets</a></p>
    </div>");
}

// Initialisation des catégories (avec gestion d'erreur)
try {
    if (function_exists('initializeRecapitulatifCategories')) {
        initializeRecapitulatifCategories($pdo, $projet_id, $devis_id);
        logDiagnostic("Initialisation catégories OK");
    } else {
        logDiagnostic("WARNING: Fonction initializeRecapitulatifCategories non trouvée");
    }
} catch (Exception $e) {
    logDiagnostic("WARNING: Erreur initialisation catégories", $e->getMessage());
    // Continuer sans bloquer
}

// Configuration des modules avec vérification table
$modules = [
    'plomberie' => [
        'icon' => 'fa-faucet', 
        'title' => 'Plomberie', 
        'file' => 'Plomberie.php',
        'color' => '#3498db',
        'description' => 'Tuyauterie, robinetterie, sanitaires'
    ],
    'menuiserie' => [
        'icon' => 'fa-door-open', 
        'title' => 'Menuiserie', 
        'file' => 'Menuiserie.php',
        'color' => '#8e44ad',
        'description' => 'Portes, fenêtres, placards'
    ],
    'electricite' => [
        'icon' => 'fa-bolt', 
        'title' => 'Électricité', 
        'file' => 'Electricite.php',
        'color' => '#f1c40f',
        'description' => 'Installation électrique, éclairage'
    ],
    'peinture' => [
        'icon' => 'fa-paint-roller', 
        'title' => 'Peinture', 
        'file' => 'Peinture.php',
        'color' => '#e74c3c',
        'description' => 'Peintures, enduits, finitions'
    ],
    'charpenterie' => [
        'icon' => 'fa-hammer', 
        'title' => 'Charpenterie', 
        'file' => 'Charpenterie.php',
        'color' => '#d35400',
        'description' => 'Charpente, ossature bois'
    ],
    'carrelage' => [
        'icon' => 'fa-border-style', 
        'title' => 'Carrelage', 
        'file' => 'Carrelage.php',
        'color' => '#16a085',
        'description' => 'Revêtements sols et murs'
    ],
    'ferraillage' => [
        'icon' => 'fa-grip-lines', 
        'title' => 'Ferraillage', 
        'file' => 'Ferraillage.php',
        'color' => '#7f8c8d',
        'description' => 'Armatures béton, fers à béton'
    ],
    'ferronnerie' => [
        'icon' => 'fa-shield-alt', 
        'title' => 'Ferronnerie', 
        'file' => 'Ferronnerie.php',
        'color' => '#c0392b',
        'description' => 'Ouvrages métalliques'
    ],
    'materiaux_base' => [
        'icon' => 'fa-cubes', 
        'title' => 'Matériaux de Base', 
        'file' => 'MatériauxBase.php',
        'color' => '#2c3e50',
        'description' => 'Ciment, sable, gravier'
    ]
];

// Vérification du statut de remplissage des modules
$modules_status = [];
$completion_percentage = 0;

try {
    foreach ($modules as $module_key => $module_info) {
        // Vérifier si la table existe
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$module_key]);
        $table_exists = $stmt->fetch();
        
        if ($table_exists) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM `$module_key` WHERE projet_id = ? AND devis_id = ?");
                $stmt->execute([$projet_id, $devis_id]);
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                $modules_status[$module_key] = $count > 0;
                
                logDiagnostic("Module $module_key", ['count' => $count, 'has_data' => $modules_status[$module_key]]);
            } catch (PDOException $e) {
                logDiagnostic("WARNING: Erreur module $module_key", $e->getMessage());
                $modules_status[$module_key] = false;
            }
        } else {
            logDiagnostic("WARNING: Table $module_key n'existe pas");
            $modules_status[$module_key] = false;
        }
    }

    // Calcul du pourcentage de completion
    $modules_completed = array_sum($modules_status);
    $completion_percentage = count($modules) > 0 ? round(($modules_completed / count($modules)) * 100) : 0;
    
    logDiagnostic("Completion calculée", [
        'modules_completed' => $modules_completed,
        'total_modules' => count($modules),
        'percentage' => $completion_percentage
    ]);

} catch (Exception $e) {
    logDiagnostic("ERREUR: Calcul statut modules", $e->getMessage());
    // Valeurs par défaut en cas d'erreur
    foreach ($modules as $key => $module) {
        $modules_status[$key] = false;
    }
    $completion_percentage = 0;
}

// Traitement de la duplication de devis
if (isset($_GET['dupliquer']) && $_GET['dupliquer'] === '1') {
    try {
        logDiagnostic("Début duplication devis", $devis_id);
        
        $pdo->beginTransaction();
        
        // Création du nouveau numéro de devis
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM devis WHERE projet_id = ?");
        $stmt->execute([$projet_id]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        $new_numero = 'DEV-' . str_pad($projet_id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        
        // Duplication du devis
        $stmt = $pdo->prepare("INSERT INTO devis (projet_id, numero, description, statut) VALUES (?, ?, ?, 'brouillon')");
        $stmt->execute([$projet_id, $new_numero, $devis['description'] . ' (Copie)']);
        $new_devis_id = $pdo->lastInsertId();
        
        logDiagnostic("Nouveau devis créé", ['new_devis_id' => $new_devis_id, 'numero' => $new_numero]);
        
        // Duplication des données de chaque module
        foreach (array_keys($modules) as $module) {
            try {
                // Vérifier si la table existe
                $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$module]);
                if ($stmt->fetch()) {
                    $stmt = $pdo->prepare("INSERT INTO `$module` (projet_id, devis_id, designation, quantite, pu, pt, transport)
                                          SELECT ?, ?, designation, quantite, pu, pt, transport 
                                          FROM `$module` 
                                          WHERE projet_id = ? AND devis_id = ?");
                    $stmt->execute([$projet_id, $new_devis_id, $projet_id, $devis_id]);
                    
                    $rows_copied = $stmt->rowCount();
                    if ($rows_copied > 0) {
                        logDiagnostic("Module $module dupliqué", ['rows' => $rows_copied]);
                    }
                }
            } catch (PDOException $e) {
                logDiagnostic("WARNING: Erreur duplication module $module", $e->getMessage());
                // Continuer avec les autres modules
            }
        }
        
        // Duplication du récapitulatif si existe
        try {
            $stmt = $pdo->prepare("SHOW TABLES LIKE 'recapitulatif'");
            $stmt->execute();
            if ($stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO recapitulatif (projet_id, devis_id, categorie, total_materiaux, total_transport, main_oeuvre, total_ht, taux_tva, montant_tva, total_ttc)
                                      SELECT ?, ?, categorie, total_materiaux, total_transport, main_oeuvre, total_ht, taux_tva, montant_tva, total_ttc 
                                      FROM recapitulatif 
                                      WHERE projet_id = ? AND devis_id = ?");
                $stmt->execute([$projet_id, $new_devis_id, $projet_id, $devis_id]);
                
                logDiagnostic("Récapitulatif dupliqué", ['rows' => $stmt->rowCount()]);
            }
        } catch (PDOException $e) {
            logDiagnostic("WARNING: Erreur duplication récapitulatif", $e->getMessage());
        }
        
        $pdo->commit();
        logDiagnostic("Duplication terminée avec succès");
        
        header("Location: devis_detail.php?projet_id=$projet_id&devis_id=$new_devis_id&success=duplique");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        logDiagnostic("ERREUR: Duplication échouée", $e->getMessage());
        $error = "Erreur lors de la duplication : " . $e->getMessage();
    }
}

logDiagnostic("Page prête à être affichée", [
    'projet' => $projet['nom'],
    'devis' => $devis['numero'],
    'completion' => $completion_percentage . '%'
]);

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis <?= htmlspecialchars($devis['numero']) ?> | ProDevis360°</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== STYLES OPTIMISÉS DEVIS_DETAIL ===== */
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
            --orange: #fd7e14;
            --purple: #6f42c1;
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Messages d'état */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.5s ease;
        }

        .alert.success {
            background: rgba(39, 174, 96, 0.1);
            border-color: var(--success);
            color: var(--success);
        }

        .alert.error {
            background: rgba(231, 76, 60, 0.1);
            border-color: var(--danger);
            color: var(--danger);
        }

        .alert.info {
            background: rgba(52, 152, 219, 0.1);
            border-color: var(--secondary);
            color: var(--secondary);
        }

        /* Header principal */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-info h1 {
            color: var(--primary);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .breadcrumb {
            color: #666;
            font-size: 1rem;
        }

        .breadcrumb a {
            color: var(--secondary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb a:hover {
            color: var(--primary);
        }

        .header-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--gradient);
            color: white;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f39c12, #f1c40f);
            color: white;
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
        }

        /* Section d'information */
        .info-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .info-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .info-item.completed {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success);
        }

        /* Progress bar */
        .progress-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .progress-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary);
        }

        .progress-percentage {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--success);
        }

        .progress-bar {
            width: 100%;
            height: 12px;
            background: #ecf0f1;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--success), var(--info));
            border-radius: 6px;
            transition: width 1s ease-in-out;
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200px 0; }
            100% { background-position: 200px 0; }
        }

        .progress-fill {
            background-image: linear-gradient(90deg, var(--success) 0%, var(--info) 50%, var(--success) 100%);
            background-size: 200px 100%;
            animation: shimmer 2s infinite linear;
        }

        /* Grid des modules */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .module-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            border: 3px solid transparent;
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .module-card:hover::before {
            opacity: 1;
        }

        .module-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            border-color: var(--secondary);
        }

        .module-card.has-data {
            border-color: var(--success);
            background: linear-gradient(145deg, white, rgba(39, 174, 96, 0.02));
        }

        .module-card.has-data::after {
            content: '✓';
            position: absolute;
            top: 15px;
            right: 15px;
            width: 25px;
            height: 25px;
            background: var(--success);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .module-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .module-card:hover .module-icon {
            transform: scale(1.1) rotateY(360deg);
        }

        .module-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .module-description {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .module-link {
            display: inline-block;
            padding: 12px 24px;
            background: var(--gradient);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .module-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            transition: left 0.5s ease;
        }

        .module-link:hover::before {
            left: 100%;
        }

        .module-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
        }

        /* Actions section */
        .actions-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .actions-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .action-card {
            padding: 20px;
            background: linear-gradient(145deg, #f8f9fa, white);
            border-radius: 12px;
            text-align: center;
            border: 2px solid #ecf0f1;
            transition: all 0.3s ease;
        }

        .action-card:hover {
            border-color: var(--secondary);
            transform: translateY(-3px);
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .action-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .action-description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-info h1 {
                font-size: 2rem;
            }

            .modules-grid {
                grid-template-columns: 1fr;
            }

            .module-card {
                padding: 20px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Debug info */
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
        }

        .debug-info h4 {
            color: #495057;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Messages de statut et debug -->
        <?php if (isset($_GET['debug'])): ?>
        <div class="debug-info">
            <h4>🔧 Mode Debug Activé</h4>
            <p><strong>URL:</strong> <?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></p>
            <p><strong>Projet ID:</strong> <?= $projet_id ?> | <strong>Devis ID:</strong> <?= $devis_id ?></p>
            <p><strong>Projet:</strong> <?= htmlspecialchars($projet['nom'] ?? 'N/A') ?></p>
            <p><strong>Devis:</strong> <?= htmlspecialchars($devis['numero'] ?? 'N/A') ?></p>
            <p><strong>Modules avec données:</strong> <?= implode(', ', array_keys(array_filter($modules_status))) ?></p>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
        <div class="alert success fade-in">
            <i class="fas fa-check-circle"></i>
            <div>
                <?php if ($_GET['success'] === 'duplique'): ?>
                    <strong>Devis dupliqué avec succès !</strong>
                    <p>Vous pouvez maintenant modifier cette nouvelle version.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
        <div class="alert error fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Information :</strong>
                <?php if ($_GET['error'] === 'parametres_manquants'): ?>
                    <p>Paramètres manquants dans l'URL. Redirection depuis la liste des projets.</p>
                <?php elseif ($_GET['error'] === 'projet_inexistant'): ?>
                    <p>Le projet demandé (ID: <?= htmlspecialchars($_GET['id'] ?? 'N/A') ?>) n'existe pas.</p>
                <?php elseif ($_GET['error'] === 'devis_inexistant'): ?>
                    <p>Le devis demandé (ID: <?= htmlspecialchars($_GET['devis_id'] ?? 'N/A') ?>) n'appartient pas à ce projet.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert error fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Erreur système :</strong>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Header principal -->
        <div class="header fade-in">
            <div class="header-content">
                <div class="header-info">
                    <h1>
                        <i class="fas fa-file-invoice"></i>
                        Devis <?= htmlspecialchars($devis['numero']) ?>
                    </h1>
                    <div class="breadcrumb">
                        <a href="index.php">Accueil</a> › 
                        <a href="liste_projets.php">Projets</a> › 
                        <a href="projet_detail.php?id=<?= $projet_id ?>"><?= htmlspecialchars($projet['nom']) ?></a> › 
                        <?= htmlspecialchars($devis['numero']) ?>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="projet_detail.php?id=<?= $projet_id ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Retour au Projet
                    </a>
                    <a href="Recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-success">
                        <i class="fas fa-calculator"></i> Récapitulatif
                    </a>
                    <a href="?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&dupliquer=1" 
                       class="btn btn-warning" 
                       onclick="return confirm('Dupliquer ce devis ? Une copie sera créée.')">
                        <i class="fas fa-copy"></i> Dupliquer
                    </a>
                </div>
            </div>
        </div>

        <!-- Section de progression -->
        <div class="progress-section fade-in">
            <div class="progress-header">
                <h2 class="progress-title">
                    <i class="fas fa-tasks"></i> Progression du Devis
                </h2>
                <div class="progress-percentage"><?= $completion_percentage ?>%</div>
            </div>
            
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $completion_percentage ?>%" 
                     data-percentage="<?= $completion_percentage ?>"></div>
            </div>
            
            <div class="info-grid">
                <?php foreach ($modules as $key => $module): ?>
                <div class="info-item <?= $modules_status[$key] ? 'completed' : '' ?>">
                    <i class="fas <?= $modules_status[$key] ? 'fa-check-circle' : 'fa-circle' ?>"></i>
                    <span><?= $module['title'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Informations du devis -->
        <div class="info-section fade-in">
            <h2 class="info-title">
                <i class="fas fa-info-circle"></i> Informations du Devis
            </h2>
            <div class="info-grid">
                <div class="info-item completed">
                    <i class="fas fa-building"></i>
                    <span><strong>Projet:</strong> <?= htmlspecialchars($projet['nom'] ?? '') ?></span>
                </div>
                <div class="info-item completed">
                    <i class="fas fa-user"></i>
                    <span><strong>Client:</strong> <?= htmlspecialchars($projet['client'] ?? '') ?></span>
                </div>
                <div class="info-item completed">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><strong>Adresse:</strong> <?= htmlspecialchars($projet['adresse'] ?? '') ?></span>
                </div>
                <div class="info-item completed">
                    <i class="fas fa-calendar"></i>
                    <span><strong>Créé le:</strong> <?= date('d/m/Y à H:i', strtotime($devis['date_creation'])) ?></span>
                </div>
            </div>
            <?php if (!empty($devis['description'])): ?>
            <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <strong>Description:</strong> <?= htmlspecialchars($devis['description'] ?? '') ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Grid des modules -->
        <div class="modules-grid">
            <?php foreach ($modules as $key => $module): ?>
            <div class="module-card <?= $modules_status[$key] ? 'has-data' : '' ?> fade-in" 
                 style="animation-delay: <?= array_search($key, array_keys($modules)) * 0.1 ?>s">
                <div class="module-icon" style="color: <?= $module['color'] ?>">
                    <i class="fas <?= $module['icon'] ?>"></i>
                </div>
                <h3 class="module-title"><?= $module['title'] ?></h3>
                <p class="module-description"><?= $module['description'] ?></p>
                <a href="<?= $module['file'] ?>?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                   class="module-link">
                    <i class="fas fa-arrow-right"></i>
                    <?= $modules_status[$key] ? 'Modifier' : 'Renseigner' ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Actions rapides -->
        <div class="actions-section fade-in">
            <h2 class="actions-title">
                <i class="fas fa-tools"></i> Actions Rapides
            </h2>
            <div class="actions-grid">
                <div class="action-card">
                    <div class="action-icon" style="color: var(--success);">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h4 class="action-title">Récapitulatif & Totaux</h4>
                    <p class="action-description">Consultez le récapitulatif complet avec tous les totaux</p>
                    <a href="Recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-success">
                        <i class="fas fa-eye"></i> Voir le Récapitulatif
                    </a>
                </div>
                
                <div class="action-card">
                    <div class="action-icon" style="color: var(--danger);">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h4 class="action-title">Impression PDF</h4>
                    <p class="action-description">Générez et imprimez votre devis au format PDF professionnel</p>
                    <a href="impression_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                       class="btn btn-primary" target="_blank">
                        <i class="fas fa-print"></i> Imprimer PDF
                    </a>
                </div>
                
                <div class="action-card">
                    <div class="action-icon" style="color: var(--warning);">
                        <i class="fas fa-copy"></i>
                    </div>
                    <h4 class="action-title">Dupliquer le Devis</h4>
                    <p class="action-description">Créez une copie de ce devis pour en faire une nouvelle version</p>
                    <a href="?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&dupliquer=1" 
                       class="btn btn-warning"
                       onclick="return confirm('Êtes-vous sûr de vouloir dupliquer ce devis ?')">
                        <i class="fas fa-clone"></i> Dupliquer
                    </a>
                </div>
                
                <div class="action-card">
                    <div class="action-icon" style="color: var(--info);">
                        <i class="fas fa-history"></i>
                    </div>
                    <h4 class="action-title">Historique & Versions</h4>
                    <p class="action-description">Consultez l'historique des modifications et les versions</p>
                    <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-clock"></i> Voir l'Historique
                    </a>
                </div>
            </div>
        </div>

        <!-- Section de diagnostic (uniquement en mode debug) -->
        <?php if (isset($_GET['debug'])): ?>
        <div class="debug-info" style="margin-top: 30px;">
            <h4>🔍 Diagnostic Technique</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                <div>
                    <strong>Base de Données:</strong><br>
                    ✅ Connexion active<br>
                    ✅ Projet vérifié<br>
                    ✅ Devis vérifié<br>
                    ✅ Cohérence projet-devis
                </div>
                <div>
                    <strong>Modules Status:</strong><br>
                    <?php foreach ($modules_status as $module => $status): ?>
                    <?= $status ? '✅' : '❌' ?> <?= ucfirst($module) ?><br>
                    <?php endforeach; ?>
                </div>
                <div>
                    <strong>Performance:</strong><br>
                    📊 Completion: <?= $completion_percentage ?>%<br>
                    🔧 Modules actifs: <?= array_sum($modules_status) ?>/<?= count($modules) ?><br>
                    ⚡ Page générée en <?= number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3) ?>s
                </div>
            </div>
            <div style="margin-top: 15px; padding: 10px; background: #e9ecef; border-radius: 5px;">
                <strong>💡 Astuce:</strong> Retirez <code>&debug=1</code> de l'URL pour masquer ces informations de diagnostic.
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // ===== SCRIPTS AMÉLIORÉS AVEC DIAGNOSTIC =====
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== DIAGNOSTIC DEVIS_DETAIL ===');
            console.log('Page chargée avec succès');
            console.log('Projet ID:', <?= $projet_id ?>);
            console.log('Devis ID:', <?= $devis_id ?>);
            console.log('Completion:', '<?= $completion_percentage ?>%');
            console.log('Modules Status:', <?= json_encode($modules_status) ?>);
            
            // Animation de la barre de progression
            const progressFill = document.querySelector('.progress-fill');
            const percentage = <?= $completion_percentage ?>;
            
            // Animation différée pour l'effet visuel
            setTimeout(() => {
                if (progressFill) {
                    progressFill.style.width = percentage + '%';
                    console.log('Barre de progression animée:', percentage + '%');
                }
            }, 500);

            // Animation des cartes modules avec délai
            const moduleCards = document.querySelectorAll('.module-card');
            moduleCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Vérification périodique de la validité des liens
            function checkLinksValidity() {
                const links = document.querySelectorAll('a[href*="projet_id"]');
                let invalidLinks = 0;
                
                links.forEach(link => {
                    if (!link.href.includes('projet_id=<?= $projet_id ?>') || 
                        (link.href.includes('devis_id') && !link.href.includes('devis_id=<?= $devis_id ?>'))) {
                        console.warn('Lien potentiellement invalide:', link.href);
                        invalidLinks++;
                    }
                });
                
                if (invalidLinks === 0) {
                    console.log('✅ Tous les liens sont valides');
                } else {
                    console.warn('⚠️ Liens invalides détectés:', invalidLinks);
                }
            }

            // Vérification immédiate puis périodique
            checkLinksValidity();
            setInterval(checkLinksValidity, 30000);

            // Gestion des erreurs JavaScript
            window.addEventListener('error', function(e) {
                console.error('Erreur JavaScript détectée:', {
                    message: e.message,
                    filename: e.filename,
                    lineno: e.lineno,
                    colno: e.colno
                });
            });

            // Test de connectivité avec le serveur
            function testConnectivity() {
                fetch(window.location.href, { method: 'HEAD' })
                    .then(response => {
                        if (response.ok) {
                            console.log('✅ Connectivité serveur OK');
                        } else {
                            console.warn('⚠️ Problème de connectivité:', response.status);
                        }
                    })
                    .catch(error => {
                        console.error('❌ Erreur de connectivité:', error);
                    });
            }

            // Test initial puis périodique
            testConnectivity();
            setInterval(testConnectivity, 60000); // Toutes les minutes

            // Tracking des interactions utilisateur
            document.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' && e.target.href.includes('projet_id')) {
                    console.log('Navigation vers:', e.target.href);
                    
                    // Vérification avant navigation
                    if (!e.target.href.includes('projet_id=<?= $projet_id ?>')) {
                        console.warn('⚠️ Navigation vers un autre projet détectée');
                    }
                }
                
                // Tracking des actions sur les modules
                if (e.target.closest('.module-card')) {
                    const moduleCard = e.target.closest('.module-card');
                    const moduleTitle = moduleCard.querySelector('.module-title')?.textContent;
                    console.log('Interaction module:', moduleTitle);
                }
            });

            // Sauvegarde automatique de l'état de la page
            const pageState = {
                projet_id: <?= $projet_id ?>,
                devis_id: <?= $devis_id ?>,
                completion: <?= $completion_percentage ?>,
                timestamp: new Date().toISOString(),
                modules_status: <?= json_encode($modules_status) ?>
            };
            
            sessionStorage.setItem('devis_detail_state', JSON.stringify(pageState));
            console.log('État de la page sauvegardé:', pageState);

            // Notification de succès de chargement
            if (<?= $completion_percentage ?> > 0) {
                setTimeout(() => {
                    console.log(`🎉 Devis <?= htmlspecialchars($devis['numero']) ?> chargé avec succès (${<?= $completion_percentage ?>}% complété)`);
                }, 1000);
            }

            console.log('=== FIN DIAGNOSTIC ===');
        });

        // Fonction utilitaire pour débugger en cas de problème
        function debugInfo() {
            return {
                url: window.location.href,
                projet_id: <?= $projet_id ?>,
                devis_id: <?= $devis_id ?>,
                completion: <?= $completion_percentage ?>,
                modules_status: <?= json_encode($modules_status) ?>,
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent,
                viewport: {
                    width: window.innerWidth,
                    height: window.innerHeight
                }
            };
        }

        // Fonction pour signaler un problème
        function reportIssue(description) {
            const issueData = {
                description: description,
                page: 'devis_detail.php',
                debug_info: debugInfo()
            };
            
            console.log('🐛 Problème signalé:', issueData);
            
            // Ici on pourrait envoyer les données au serveur
            // fetch('/report_issue.php', { method: 'POST', body: JSON.stringify(issueData) });
            
            alert('Problème signalé. Informations sauvegardées dans la console du navigateur.');
        }

        // Raccourci pour le mode debug
        document.addEventListener('keydown', function(e) {
            // Ctrl + Shift + D pour activer le debug
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('debug', '1');
                window.location.href = currentUrl.toString();
            }
        });
    </script>
</body>
</html>

<?php
// Log final de succès
logDiagnostic("Page affichée avec succès", [
    'projet' => $projet['nom'],
    'devis' => $devis['numero'],
    'completion' => $completion_percentage . '%',
    'execution_time' => number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3) . 's'
]);
?>

<!-- 
===== GUIDE DE DÉPANNAGE =====

Si vous voyez encore l'erreur "Erreur système - Code: xxxxxxxx", voici les étapes de diagnostic :

1. ACTIVER LE MODE DEBUG :
   Ajoutez &debug=1 à votre URL :
   https://prodevis360.gsnexpertises.com/devis_detail.php?projet_id=24&devis_id=25&debug=1

2. VÉRIFIER LES LOGS :
   Les messages de diagnostic sont enregistrés dans les logs PHP du serveur

3. VÉRIFIER LA BASE DE DONNÉES :
   - Table 'projets' : Le projet ID 24 existe-t-il ?
   - Table 'devis' : Le devis ID 25 existe-t-il avec projet_id = 24 ?

4. REQUÊTES SQL DE VÉRIFICATION :
   SELECT * FROM projets WHERE id = 24;
   SELECT * FROM devis WHERE id = 25;
   SELECT * FROM devis WHERE id = 25 AND projet_id = 24;

5. PERMISSIONS ET STRUCTURE :
   - Vérifier que le fichier functions.php est accessible
   - Vérifier que la fonction getDbConnection() fonctionne
   - Vérifier les permissions sur les tables

Ce nouveau code donne des informations précises sur l'origine du problème !
-->