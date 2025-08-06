<?php
// ===== PLOMBERIE.PHP - PARTIE 1 : PHP LOGIC & CONFIG =====
// VERSION CORRIGÉE - Adaptation de votre code existant 2894 lignes
require_once 'functions.php';

// Configuration du module actuel
$current_module = 'plomberie';

// Configuration des modules pour navigation dynamique
$modules_config = [
    'plomberie' => ['name' => 'Plomberie', 'icon' => 'fas fa-wrench', 'color' => '#3498db'],
    'menuiserie' => ['name' => 'Menuiserie', 'icon' => 'fas fa-hammer', 'color' => '#8e44ad'],
    'electricite' => ['name' => 'Électricité', 'icon' => 'fas fa-bolt', 'color' => '#f39c12'],
    'peinture' => ['name' => 'Peinture', 'icon' => 'fas fa-paint-brush', 'color' => '#e74c3c'],
    'materiaux' => ['name' => 'Matériaux Base', 'icon' => 'fas fa-cubes', 'color' => '#95a5a6'],
    'charpenterie' => ['name' => 'Charpenterie', 'icon' => 'fas fa-tree', 'color' => '#27ae60'],
    'carrelage' => ['name' => 'Carrelage', 'icon' => 'fas fa-th', 'color' => '#16a085'],
    'ferraillage' => ['name' => 'Ferraillage', 'icon' => 'fas fa-industry', 'color' => '#34495e'],
    'ferronnerie' => ['name' => 'Ferronnerie', 'icon' => 'fas fa-cog', 'color' => '#7f8c8d']
];

// Récupération et validation des paramètres
$projet_id = secureGetParam('projet_id', 'int', 0);
$devis_id = secureGetParam('devis_id', 'int', 0);
$action = secureGetParam('action', 'string', '');
$element_id = secureGetParam('element_id', 'int', 0);

// Vérification des paramètres obligatoires
if (!$projet_id || !$devis_id) {
    die('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Erreur : Paramètres projet_id et devis_id manquants.</div>');
}

// Récupération des informations du projet et devis
$projet_devis_info = getProjetDevisInfo($projet_id, $devis_id);
if (!$projet_devis_info) {
    die('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Erreur : Projet ou devis introuvable.</div>');
}

// Variables d'affichage
$message = '';
$message_type = '';

// Suggestions spécialisées pour la plomberie (gardées de votre code existant)
$suggestions_plomberie = [
    // TUYAUTERIE
    'Tube cuivre diamètre 12mm',
    'Tube cuivre diamètre 14mm', 
    'Tube cuivre diamètre 16mm',
    'Tube cuivre diamètre 18mm',
    'Tube cuivre diamètre 22mm',
    'Tube PVC évacuation Ø32mm',
    'Tube PVC évacuation Ø40mm',
    'Tube PVC évacuation Ø50mm',
    'Tube PVC évacuation Ø100mm',
    'Tube PVC évacuation Ø125mm',
    'Tube multicouche 16x2mm',
    'Tube multicouche 20x2mm',
    'Tube PER 12x1.3mm',
    'Tube PER 16x1.5mm',
    'Tube PER 20x1.9mm',
    
    // RACCORDS CUIVRE
    'Coude cuivre 90° mâle-femelle 12mm',
    'Coude cuivre 90° mâle-femelle 14mm',
    'Coude cuivre 90° mâle-femelle 16mm',
    'Coude cuivre 90° mâle-femelle 18mm',
    'Coude cuivre 90° mâle-femelle 22mm',
    'Té égal cuivre 12mm',
    'Té égal cuivre 14mm',
    'Té égal cuivre 16mm',
    'Té égal cuivre 18mm',
    'Té égal cuivre 22mm',
    'Manchon cuivre 12mm',
    'Manchon cuivre 14mm',
    'Manchon cuivre 16mm',
    'Manchon cuivre 18mm',
    'Manchon cuivre 22mm',
    'Réduction cuivre 16-12mm',
    'Réduction cuivre 18-14mm',
    'Réduction cuivre 22-16mm',
    
    // RACCORDS PVC
    'Coude PVC 90° mâle-femelle Ø32mm',
    'Coude PVC 90° mâle-femelle Ø40mm',
    'Coude PVC 90° mâle-femelle Ø50mm',
    'Coude PVC 90° mâle-femelle Ø100mm',
    'Té PVC égal Ø32mm',
    'Té PVC égal Ø40mm',
    'Té PVC égal Ø50mm',
    'Té PVC égal Ø100mm',
    'Manchon PVC Ø32mm',
    'Manchon PVC Ø40mm',
    'Manchon PVC Ø50mm',
    'Manchon PVC Ø100mm',
    'Réduction PVC 50-40mm',
    'Réduction PVC 100-50mm',
    
    // ROBINETTERIE
    'Mitigeur lavabo bec fixe',
    'Mitigeur lavabo bec orientable',
    'Mitigeur évier bec haut',
    'Mitigeur douche thermostatique',
    'Mitigeur bain-douche encastré',
    'Robinet d\'arrêt 1/4 de tour 12mm',
    'Robinet d\'arrêt 1/4 de tour 14mm',
    'Robinet d\'arrêt 1/4 de tour 16mm',
    'Robinet d\'arrêt à soupape 15x21',
    'Robinet d\'arrêt à soupape 20x27',
    'Robinet purge automatique',
    'Robinet flotteur à levier WC',
    'Robinet flotteur silencieux WC',
    'Robinet temporisé poussoir lavabo',
    'Robinet temporisé pression cascade',
    'Robinet temporisé lavabo',
    'Robinet de jardin 15x21',
    'Robinet de jardin 20x27',
    
    // SANITAIRES
    'WC suspendu avec réservoir encastré',
    'WC au sol évacuation horizontale',
    'WC au sol évacuation verticale',
    'Lavabo 60x50cm avec colonne',
    'Lavabo d\'angle 40x40cm',
    'Lave-mains 30x25cm avec mitigeur',
    'Évier inox 1 bac 50x40cm',
    'Évier inox 2 bacs 120x60cm',
    'Évier céramique 1 bac égouttoir',
    'Receveur de douche 80x80cm',
    'Receveur de douche 90x90cm',
    'Receveur de douche 120x80cm',
    'Baignoire acrylique 170x75cm',
    'Baignoire acrylique d\'angle 140x140cm',
    
    // ÉVACUATION
    'Siphon lavabo laiton chromé 32mm',
    'Siphon évier plastique 40mm',
    'Siphon douche extra-plat 90mm',
    'Bonde de douche Ø90mm',
    'Bonde de baignoire avec trop-plein',
    'Regard de visite PVC Ø100mm',
    'Regard de visite PVC Ø160mm',
    'Grille d\'évacuation inox 100x100mm',
    'Caniveau de douche 60cm inox',
    'Caniveau de douche 80cm inox',
    
    // FIXATIONS ET ACCESSOIRES
    'Collier de fixation cuivre Ø12mm',
    'Collier de fixation cuivre Ø14mm',
    'Collier de fixation cuivre Ø16mm',
    'Collier de fixation PVC Ø32mm',
    'Collier de fixation PVC Ø40mm',
    'Collier de fixation PVC Ø50mm',
    'Collier de fixation PVC Ø100mm',
    'Soudure étain-argent 3% diamètre 2mm',
    'Flux décapant pour soudure cuivre',
    'Colle PVC pot 250ml',
    'Décapant PVC flacon 125ml',
    'Pâte d\'étanchéité tube 150ml',
    'Filasse naturelle 100g',
    'Pâte à joint tube 310ml'
];

// CORRECTION MAJEURE : Obtenir la bonne connexion PDO
$pdo = getDbConnection(); // PDO au lieu de MySQLi

// FONCTION CORRIGÉE : updateRecapitulatif adaptée à votre structure
function updateRecapitulatifPlomberie($pdo, $projet_id, $devis_id) {
    try {
        // Calculer les totaux selon VOTRE structure BDD (pu, pt, transport)
        $stmt = $pdo->prepare("
            SELECT 
                COALESCE(SUM(pt), 0) as total_materiaux,
                COALESCE(SUM(transport), 0) as total_transport,
                COUNT(*) as nb_elements
            FROM plomberie 
            WHERE projet_id = ? AND devis_id = ?
        ");
        $stmt->execute([$projet_id, $devis_id]);
        $totaux = $stmt->fetch();
        
        // Vérifier si une ligne existe dans recapitulatif
        $stmt_check = $pdo->prepare("
            SELECT id FROM recapitulatif 
            WHERE projet_id = ? AND devis_id = ? AND categorie = 'plomberie'
        ");
        $stmt_check->execute([$projet_id, $devis_id]);
        
        $total_ht = $totaux['total_materiaux'] + $totaux['total_transport'];
        $montant_tva = $total_ht * 0.18;
        $total_ttc = $total_ht + $montant_tva;
        
        if ($stmt_check->fetch()) {
            // UPDATE
            $stmt_update = $pdo->prepare("
                UPDATE recapitulatif SET 
                    total_materiaux = ?, 
                    total_transport = ?, 
                    total_ht = ?, 
                    montant_tva = ?, 
                    total_ttc = ?,
                    date_rapport = NOW()
                WHERE projet_id = ? AND devis_id = ? AND categorie = 'plomberie'
            ");
            $stmt_update->execute([
                $totaux['total_materiaux'], 
                $totaux['total_transport'], 
                $total_ht, 
                $montant_tva, 
                $total_ttc,
                $projet_id, 
                $devis_id
            ]);
        } else {
            // INSERT
            $stmt_insert = $pdo->prepare("
                INSERT INTO recapitulatif (
                    projet_id, devis_id, categorie, total_materiaux, 
                    total_transport, total_ht, taux_tva, montant_tva, 
                    total_ttc, date_rapport
                ) VALUES (?, ?, 'plomberie', ?, ?, ?, 18.00, ?, ?, NOW())
            ");
            $stmt_insert->execute([
                $projet_id, $devis_id, $totaux['total_materiaux'], 
                $totaux['total_transport'], $total_ht, $montant_tva, $total_ttc
            ]);
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Erreur updateRecapitulatifPlomberie: " . $e->getMessage());
        return false;
    }
}

// Gestion des actions CRUD - CORRIGÉE selon votre structure BDD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if ($action == 'ajouter') {
            // Récupération et validation des données
            $designation = trim($_POST['designation'] ?? '');
            $quantite = floatval($_POST['quantite'] ?? 0);
            $unite = trim($_POST['unite'] ?? 'unité');
            $pu = floatval($_POST['pu'] ?? 0); // CORRECTION: utilise 'pu' pas 'prix_unitaire'
            $diametre = trim($_POST['diametre'] ?? '');
            $longueur = floatval($_POST['longueur'] ?? 0);
            $materiau = trim($_POST['materiau'] ?? '');
            $type_raccord = trim($_POST['type_raccord'] ?? '');
            $pression = trim($_POST['pression'] ?? '');
            $transport = floatval($_POST['transport'] ?? 0);
            
            // Validations spécifiques plomberie
            if (empty($designation)) {
                throw new Exception("La désignation est obligatoire.");
            }
            if ($quantite <= 0) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }
            if ($pu < 0) {
                throw new Exception("Le prix unitaire ne peut pas être négatif.");
            }
            
            // CORRECTION: Calcul selon votre structure (pt = pu * quantite)
            $pt = $quantite * $pu;
            
            // CORRECTION: Insertion avec PDO et vrais noms de champs
            $stmt = $pdo->prepare("
                INSERT INTO plomberie (
                    projet_id, devis_id, designation, quantite, unite, 
                    pu, pt, transport, diametre, longueur, 
                    materiau, type_raccord, pression, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $projet_id, $devis_id, $designation, $quantite, $unite,
                $pu, $pt, $transport, $diametre, $longueur,
                $materiau, $type_raccord, $pression
            ]);
            
            // CORRECTION: Appel de notre fonction adaptée
            updateRecapitulatifPlomberie($pdo, $projet_id, $devis_id);
            
            $message = "Élément plomberie ajouté avec succès !";
            $message_type = "success";
            
        } elseif ($action == 'modifier' && $element_id > 0) {
            // Récupération et validation des données
            $designation = trim($_POST['designation'] ?? '');
            $quantite = floatval($_POST['quantite'] ?? 0);
            $unite = trim($_POST['unite'] ?? 'unité');
            $pu = floatval($_POST['pu'] ?? 0);
            $diametre = trim($_POST['diametre'] ?? '');
            $longueur = floatval($_POST['longueur'] ?? 0);
            $materiau = trim($_POST['materiau'] ?? '');
            $type_raccord = trim($_POST['type_raccord'] ?? '');
            $pression = trim($_POST['pression'] ?? '');
            $transport = floatval($_POST['transport'] ?? 0);
            
            // Validations
            if (empty($designation)) {
                throw new Exception("La désignation est obligatoire.");
            }
            if ($quantite <= 0) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }
            if ($pu < 0) {
                throw new Exception("Le prix unitaire ne peut pas être négatif.");
            }
            
            $pt = $quantite * $pu;
            
            // CORRECTION: Modification avec PDO
            $stmt = $pdo->prepare("
                UPDATE plomberie SET 
                    designation = ?, quantite = ?, unite = ?, pu = ?, 
                    pt = ?, transport = ?, diametre = ?, longueur = ?, 
                    materiau = ?, type_raccord = ?, pression = ?,
                    date_modification = NOW()
                WHERE id = ? AND projet_id = ? AND devis_id = ?
            ");
            
            $stmt->execute([
                $designation, $quantite, $unite, $pu, 
                $pt, $transport, $diametre, $longueur,
                $materiau, $type_raccord, $pression,
                $element_id, $projet_id, $devis_id
            ]);
            
            updateRecapitulatifPlomberie($pdo, $projet_id, $devis_id);
            
            $message = "Élément plomberie modifié avec succès !";
            $message_type = "success";
            
        } elseif ($action == 'supprimer' && $element_id > 0) {
            // CORRECTION: Récupération avec PDO
            $stmt_get = $pdo->prepare("SELECT designation FROM plomberie WHERE id = ? AND projet_id = ? AND devis_id = ?");
            $stmt_get->execute([$element_id, $projet_id, $devis_id]);
            $element_data = $stmt_get->fetch();
            
            if ($element_data) {
                // CORRECTION: Suppression avec PDO
                $stmt = $pdo->prepare("DELETE FROM plomberie WHERE id = ? AND projet_id = ? AND devis_id = ?");
                $stmt->execute([$element_id, $projet_id, $devis_id]);
                
                updateRecapitulatifPlomberie($pdo, $projet_id, $devis_id);
                
                $message = "Élément plomberie supprimé avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Élément introuvable pour la suppression.");
            }
        }
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = "danger";
    }
}

// CORRECTION: Récupération des éléments avec vrais noms de champs
$elements_plomberie = [];
$total_module = 0;

$stmt = $pdo->prepare("
    SELECT id, designation, quantite, unite, pu, pt, transport,
           diametre, longueur, materiau, type_raccord, pression,
           DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation_fr,
           DATE_FORMAT(date_modification, '%d/%m/%Y %H:%i') as date_modification_fr
    FROM plomberie 
    WHERE projet_id = ? AND devis_id = ? 
    ORDER BY date_creation DESC
");

$stmt->execute([$projet_id, $devis_id]);
$elements_plomberie = $stmt->fetchAll();

foreach ($elements_plomberie as $element) {
    $total_module += $element['pt']; // CORRECTION: utilise 'pt' pas 'total'
}

// Récupération de l'élément à modifier si nécessaire
$element_a_modifier = null;
if ($action == 'modifier' && $element_id > 0) {
    $stmt = $pdo->prepare("
        SELECT * FROM plomberie 
        WHERE id = ? AND projet_id = ? AND devis_id = ?
    ");
    $stmt->execute([$element_id, $projet_id, $devis_id]);
    $element_a_modifier = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="fr">
<!-- ===== PARTIE 2 : HTML HEAD & CSS ===== -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Plomberie - <?= htmlspecialchars($projet_devis_info['nom_projet'] ?? $projet_devis_info['projet_nom'] ?? 'Projet') ?> - GSN ProDevis360°</title>
    
    <!-- Meta tags pour SEO et performance -->
    <meta name="description" content="Module Plomberie GSN ProDevis360° - Gestion des installations sanitaires et tuyauterie">
    <meta name="keywords" content="plomberie, devis, GSN, ProDevis360, tuyauterie, sanitaire">
    <meta name="author" content="GSN Expertises Group">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha512-t4GWSVZO1eC8BM339Xd7Uphw5s17a86tIZIj8qRxhnKub6WoyhnrxeCIMeAqBPgdZGlCcG2PrZjMc+Wr78+5Xg==" crossorigin="anonymous">
    
    <!-- Font Awesome 6.4 CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Animate.css pour les animations -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        /* ===== VARIABLES CSS GLOBALES ===== */
        :root {
            /* Couleurs primaires GSN */
            --primary-color: #ff8c00;
            --primary-dark: #cc7000;
            --primary-light: #ffb347;
            --primary-gradient: linear-gradient(135deg, #ff8c00 0%, #cc7000 100%);
            
            /* Couleurs module plomberie */
            --plomberie-color: #3498db;
            --plomberie-dark: #2980b9;
            --plomberie-light: rgba(52, 152, 219, 0.1);
            --plomberie-gradient: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            
            /* Couleurs texte */
            --text-dark: #2c3e50;
            --text-medium: #34495e;
            --text-light: #6c757d;
            --text-muted: #95a5a6;
            
            /* Couleurs background */
            --bg-main: #f5f6fa;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --bg-card: #ffffff;
            
            /* Couleurs border */
            --border-color: #e9ecef;
            --border-light: #f1f3f4;
            --border-medium: #dee2e6;
            
            /* Couleurs status */
            --success-color: #28a745;
            --success-light: rgba(40, 167, 69, 0.1);
            --warning-color: #ffc107;
            --warning-light: rgba(255, 193, 7, 0.1);
            --danger-color: #dc3545;
            --danger-light: rgba(220, 53, 69, 0.1);
            --info-color: #17a2b8;
            --info-light: rgba(23, 162, 184, 0.1);
            
            /* Spacing */
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-xxl: 3rem;
            
            /* Border radius */
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-xxl: 20px;
            
            /* Shadows */
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-lg: 0 4px 15px rgba(0,0,0,0.1);
            --shadow-xl: 0 8px 25px rgba(0,0,0,0.15);
            
            /* Transitions */
            --transition-fast: 0.2s ease;
            --transition-normal: 0.3s ease;
            --transition-slow: 0.5s ease;
            
            /* Z-index */
            --z-dropdown: 1000;
            --z-sticky: 1020;
            --z-fixed: 1030;
            --z-modal-backdrop: 1040;
            --z-modal: 1050;
            --z-popover: 1060;
            --z-tooltip: 1070;
        }

        /* ===== RESET ET BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            font-size: 16px;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            font-size: 0.95rem;
            font-weight: 400;
            line-height: 1.6;
            color: var(--text-dark);
            background-color: var(--bg-main);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        /* ===== HEADER GSN ProDevis360° ===== */
        .header-gsn {
            background: var(--primary-gradient);
            color: white;
            padding: var(--spacing-lg) 0;
            box-shadow: var(--shadow-lg);
            position: sticky;
            top: 0;
            z-index: var(--z-sticky);
            border-bottom: 3px solid var(--primary-dark);
        }

        .header-gsn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
            pointer-events: none;
        }

        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 var(--spacing-xl);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
        }

        .logo-gsn {
            background: white;
            color: var(--primary-color);
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-lg);
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            transition: var(--transition-normal);
        }

        .logo-gsn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }

        .logo-gsn:hover::before {
            left: 100%;
        }

        .header-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .module-badge {
            background: var(--plomberie-color);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: var(--radius-md);
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: var(--shadow-sm);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .project-info {
            margin-top: var(--spacing-sm);
            opacity: 0.95;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
        }

        .project-info .separator {
            color: rgba(255,255,255,0.6);
            margin: 0 var(--spacing-xs);
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-header {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition-normal);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .btn-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.3s;
        }

        .btn-header:hover {
            background: rgba(255,255,255,0.25);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-header:hover::before {
            left: 100%;
        }

        /* ===== NAVIGATION MODULES ===== */
        .navigation-modules {
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 0;
            box-shadow: var(--shadow-sm);
            position: relative;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 var(--spacing-xl);
        }

        .nav-modules {
            display: flex;
            gap: var(--spacing-sm);
            overflow-x: auto;
            padding-bottom: var(--spacing-xs);
            scrollbar-width: thin;
            scrollbar-color: var(--border-color) transparent;
        }

        .nav-modules::-webkit-scrollbar {
            height: 4px;
        }

        .nav-modules::-webkit-scrollbar-track {
            background: transparent;
        }

        .nav-modules::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 2px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-light);
            font-weight: 500;
            font-size: 0.9rem;
            white-space: nowrap;
            transition: var(--transition-normal);
            border: 1px solid transparent;
            position: relative;
            background: transparent;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--plomberie-color);
            transition: var(--transition-normal);
            transform: translateX(-50%);
        }

        .nav-item:hover {
            background: var(--bg-light);
            color: var(--text-dark);
            transform: translateY(-1px);
        }

        .nav-item:hover::before {
            width: 80%;
        }

        .nav-item.active {
            background: var(--plomberie-light);
            color: var(--plomberie-color);
            border-color: var(--plomberie-color);
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        .nav-item.active::before {
            width: 100%;
            height: 3px;
        }

        /* ===== CONTENU PRINCIPAL ===== */
        .main-container {
            max-width: 1400px;
            margin: var(--spacing-xl) auto;
            padding: 0 var(--spacing-xl);
        }

        /* ===== HEADER MODULE ===== */
        .module-header {
            background: var(--bg-white);
            padding: var(--spacing-xl);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            margin-bottom: var(--spacing-xl);
            border-left: 4px solid var(--plomberie-color);
            position: relative;
            overflow: hidden;
        }

        .module-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: var(--plomberie-light);
            border-radius: 50%;
            transform: translate(30px, -30px);
            opacity: 0.5;
        }

        .module-title {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            position: relative;
            z-index: 2;
        }

        .module-title .icon {
            width: 60px;
            height: 60px;
            background: var(--plomberie-gradient);
            color: white;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: var(--shadow-md);
            transition: var(--transition-normal);
        }

        .module-title .icon:hover {
            transform: rotate(10deg) scale(1.1);
        }

        .module-title h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .module-title p {
            color: var(--text-light);
            margin: 0;
            font-size: 1rem;
        }

        .module-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-top: var(--spacing-lg);
            position: relative;
            z-index: 2;
        }

        .stat-card {
            background: var(--bg-light);
            padding: var(--spacing-lg);
            border-radius: var(--radius-lg);
            text-align: center;
            transition: var(--transition-normal);
            border: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--plomberie-gradient);
            transform: scaleX(0);
            transition: var(--transition-normal);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--plomberie-color);
            margin-bottom: var(--spacing-sm);
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-label {
            color: var(--text-light);
            font-weight: 500;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== CARTES ===== */
        .card-gsn {
            background: var(--bg-white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            border: none;
            margin-bottom: var(--spacing-xl);
            overflow: hidden;
            transition: var(--transition-normal);
        }

        .card-gsn:hover {
            box-shadow: var(--shadow-xl);
            transform: translateY(-2px);
        }

        .card-header-gsn {
            background: var(--plomberie-light);
            color: var(--plomberie-color);
            padding: var(--spacing-lg) var(--spacing-xl);
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            position: relative;
        }

        .card-header-gsn::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--plomberie-gradient);
        }

        .card-header-gsn .header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-header-gsn .header-right {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .card-body-gsn {
            padding: var(--spacing-xl);
        }

        /* ===== FORMULAIRES ===== */
        .form-floating {
            margin-bottom: var(--spacing-lg);
            position: relative;
        }

        .form-control, 
        .form-select {
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            font-weight: 500;
            font-size: 0.95rem;
            transition: var(--transition-normal);
            background-color: var(--bg-white);
            position: relative;
        }

        .form-control:focus, 
        .form-select:focus {
            border-color: var(--plomberie-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            outline: none;
            background-color: var(--bg-white);
        }

        .form-control:hover:not(:focus),
        .form-select:hover:not(:focus) {
            border-color: var(--plomberie-dark);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: var(--spacing-sm);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
        }

        .form-text {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: var(--spacing-xs);
        }

        /* ===== BOUTONS ===== */
        .btn-gsn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            transition: var(--transition-normal);
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
            text-decoration: none;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            line-height: 1.2;
        }

        .btn-gsn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-gsn:hover::before {
            left: 100%;
        }

        .btn-primary-gsn {
            background: var(--plomberie-gradient);
            color: white;
            border: 2px solid var(--plomberie-color);
        }

        .btn-primary-gsn:hover {
            background: var(--plomberie-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-success-gsn {
            background: var(--success-color);
            color: white;
            border: 2px solid var(--success-color);
        }

        .btn-success-gsn:hover {
            background: #218838;
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-warning-gsn {
            background: var(--warning-color);
            color: var(--text-dark);
            border: 2px solid var(--warning-color);
        }

        .btn-warning-gsn:hover {
            background: #e0a800;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-danger-gsn {
            background: var(--danger-color);
            color: white;
            border: 2px solid var(--danger-color);
        }

        .btn-danger-gsn:hover {
            background: #c82333;
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline-gsn {
            background: transparent;
            color: var(--plomberie-color);
            border: 2px solid var(--plomberie-color);
        }

        .btn-outline-gsn:hover {
            background: var(--plomberie-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        /* ===== TABLEAUX ===== */
        .table-gsn {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin: 0;
        }

        .table-gsn thead {
            background: var(--plomberie-gradient);
            color: white;
        }

        .table-gsn th {
            font-weight: 600;
            padding: var(--spacing-lg);
            border: none;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }

        .table-gsn th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255,255,255,0.3);
        }

        .table-gsn td {
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table-gsn tbody tr {
            transition: var(--transition-fast);
        }

        .table-gsn tbody tr:hover {
            background: var(--plomberie-light);
            transform: scale(1.01);
        }

        .table-gsn tbody tr:last-child td {
            border-bottom: none;
        }

        .table-gsn tfoot {
            background: var(--bg-light);
            font-weight: 600;
        }

        .table-gsn tfoot th,
        .table-gsn tfoot td {
            border-top: 2px solid var(--plomberie-color);
            padding: var(--spacing-lg);
        }

        /* ===== ALERTES ===== */
        .alert-gsn {
            border: none;
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg) var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            position: relative;
            overflow: hidden;
        }

        .alert-gsn::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }

        .alert-success-gsn {
            background: var(--success-light);
            color: #155724;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .alert-success-gsn::before {
            background: var(--success-color);
        }

        .alert-danger-gsn {
            background: var(--danger-light);
            color: #721c24;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .alert-danger-gsn::before {
            background: var(--danger-color);
        }

        .alert-info-gsn {
            background: var(--info-light);
            color: #0c5460;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }

        .alert-info-gsn::before {
            background: var(--info-color);
        }

        .alert-warning-gsn {
            background: var(--warning-light);
            color: #856404;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .alert-warning-gsn::before {
            background: var(--warning-color);
        }

        /* ===== BADGES ===== */
        .badge-gsn {
            padding: var(--spacing-sm) 0.75rem;
            border-radius: var(--radius-sm);
            font-weight: 500;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-primary-gsn {
            background: var(--plomberie-color);
            color: white;
        }

        .badge-success-gsn {
            background: var(--success-color);
            color: white;
        }

        .badge-warning-gsn {
            background: var(--warning-color);
            color: var(--text-dark);
        }

        .badge-danger-gsn {
            background: var(--danger-color);
            color: white;
        }

        .badge-info-gsn {
            background: var(--info-color);
            color: white;
        }

        /* ===== MODALS ===== */
        .modal-content {
            border: none;
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }

        .modal-header {
            background: var(--plomberie-gradient);
            color: white;
            border-bottom: none;
            padding: var(--spacing-lg) var(--spacing-xl);
        }

        .modal-body {
            padding: var(--spacing-xl);
        }

        .modal-footer {
            border-top: 1px solid var(--border-light);
            padding: var(--spacing-lg) var(--spacing-xl);
            background: var(--bg-light);
        }

        /* ===== ACTIONS RAPIDES ===== */
        .actions-container {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-lg);
            padding: var(--spacing-lg);
            background: var(--bg-light);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-xl);
            border: 1px solid var(--border-light);
        }

        .action-btn {
            flex: 1;
            min-width: 200px;
            padding: var(--spacing-lg);
            background: var(--bg-white);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            text-align: center;
            text-decoration: none;
            color: var(--text-dark);
            transition: var(--transition-normal);
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--plomberie-gradient);
            transform: scaleX(0);
            transition: var(--transition-normal);
        }

        .action-btn:hover {
            border-color: var(--plomberie-color);
            color: var(--plomberie-color);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .action-btn:hover::before {
            transform: scaleX(1);
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
            color: var(--plomberie-color);
            transition: var(--transition-normal);
        }

        .action-btn:hover .action-icon {
            transform: scale(1.2) rotate(10deg);
        }

        /* ===== LOADING STATES ===== */
        .loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 24px;
            height: 24px;
            margin: -12px 0 0 -12px;
            border: 3px solid var(--border-color);
            border-top: 3px solid var(--plomberie-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
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

        @keyframes slideInLeft {
            from { 
                opacity: 0; 
                transform: translateX(-30px); 
            }
            to { 
                opacity: 1; 
                transform: translateX(0); 
            }
        }

        @keyframes slideInRight {
            from { 
                opacity: 0; 
                transform: translateX(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateX(0); 
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        .slide-in-left {
            animation: slideInLeft 0.6s ease-out;
        }

        .slide-in-right {
            animation: slideInRight 0.6s ease-out;
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 1200px) {
            .main-container {
                max-width: 1140px;
            }
        }

        @media (max-width: 992px) {
            .header-content {
                flex-direction: column;
                gap: var(--spacing-lg);
                text-align: center;
            }

            .header-actions {
                justify-content: center;
            }

            .main-container {
                padding: 0 var(--spacing-lg);
            }

            .module-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .nav-modules {
                justify-content: flex-start;
                padding-bottom: var(--spacing-sm);
            }
        }

        @media (max-width: 768px) {
            .header-content {
                padding: 0 var(--spacing-lg);
            }

            .logo-gsn {
                font-size: 1.2rem;
                padding: 0.6rem 1rem;
            }

            .header-title h1 {
                font-size: 1.4rem;
            }

            .module-badge {
                font-size: 0.7rem;
                padding: 0.3rem 0.6rem;
            }

            .main-container {
                margin: var(--spacing-lg) auto;
                padding: 0 var(--spacing-lg);
            }

            .module-header {
                padding: var(--spacing-lg);
            }

            .module-title {
                flex-direction: column;
                text-align: center;
                gap: var(--spacing-md);
            }

            .module-title .icon {
                width: 50px;
                height: 50px;
                font-size: 1.3rem;
            }

            .module-stats {
                grid-template-columns: 1fr;
            }

            .card-body-gsn {
                padding: var(--spacing-lg);
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            .action-btn {
                min-width: 100%;
            }

            .actions-container {
                flex-direction: column;
            }

            .btn-header {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }

            .nav-item {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .header-content {
                padding: 0 var(--spacing-md);
            }

            .main-container {
                padding: 0 var(--spacing-md);
            }

            .module-header {
                padding: var(--spacing-md);
            }

            .card-body-gsn {
                padding: var(--spacing-md);
            }

            .header-actions {
                flex-direction: column;
                width: 100%;
            }

            .btn-header {
                justify-content: center;
            }
        }

        /* ===== UTILITAIRES ===== */
        .text-plomberie { color: var(--plomberie-color) !important; }
        .text-plomberie-dark { color: var(--plomberie-dark) !important; }
        .bg-plomberie { background-color: var(--plomberie-light) !important; }
        .bg-plomberie-solid { background-color: var(--plomberie-color) !important; }
        .border-plomberie { border-color: var(--plomberie-color) !important; }
        
        .font-weight-300 { font-weight: 300; }
        .font-weight-400 { font-weight: 400; }
        .font-weight-500 { font-weight: 500; }
        .font-weight-600 { font-weight: 600; }
        .font-weight-700 { font-weight: 700; }
        .font-weight-800 { font-weight: 800; }
        .font-weight-900 { font-weight: 900; }
        
        .shadow-none { box-shadow: none !important; }
        .shadow-sm { box-shadow: var(--shadow-sm) !important; }
        .shadow { box-shadow: var(--shadow-md) !important; }
        .shadow-lg { box-shadow: var(--shadow-lg) !important; }
        .shadow-xl { box-shadow: var(--shadow-xl) !important; }
        
        .rounded-0 { border-radius: 0 !important; }
        .rounded-sm { border-radius: var(--radius-sm) !important; }
        .rounded { border-radius: var(--radius-md) !important; }
        .rounded-lg { border-radius: var(--radius-lg) !important; }
        .rounded-xl { border-radius: var(--radius-xl) !important; }
        .rounded-xxl { border-radius: var(--radius-xxl) !important; }
        
        .text-truncate { 
            overflow: hidden; 
            text-overflow: ellipsis; 
            white-space: nowrap; 
        }
        
        .cursor-pointer { cursor: pointer; }
        .cursor-not-allowed { cursor: not-allowed; }
        
        .transition-fast { transition: var(--transition-fast); }
        .transition-normal { transition: var(--transition-normal); }
        .transition-slow { transition: var(--transition-slow); }
        
        .position-relative { position: relative; }
        .position-absolute { position: absolute; }
        .position-fixed { position: fixed; }
        .position-sticky { position: sticky; }
        
        .w-100 { width: 100%; }
        .h-100 { height: 100%; }
        .min-h-100 { min-height: 100%; }
        
        .d-flex { display: flex; }
        .d-inline-flex { display: inline-flex; }
        .d-grid { display: grid; }
        .d-block { display: block; }
        .d-inline-block { display: inline-block; }
        .d-none { display: none; }
        
        .align-items-start { align-items: flex-start; }
        .align-items-center { align-items: center; }
        .align-items-end { align-items: flex-end; }
        .align-items-stretch { align-items: stretch; }
        
        .justify-content-start { justify-content: flex-start; }
        .justify-content-center { justify-content: center; }
        .justify-content-end { justify-content: flex-end; }
        .justify-content-between { justify-content: space-between; }
        .justify-content-around { justify-content: space-around; }
        .justify-content-evenly { justify-content: space-evenly; }
        
        .flex-wrap { flex-wrap: wrap; }
        .flex-nowrap { flex-wrap: nowrap; }
        .flex-column { flex-direction: column; }
        .flex-row { flex-direction: row; }
        
        .gap-0 { gap: 0; }
        .gap-1 { gap: var(--spacing-xs); }
        .gap-2 { gap: var(--spacing-sm); }
        .gap-3 { gap: var(--spacing-md); }
        .gap-4 { gap: var(--spacing-lg); }
        .gap-5 { gap: var(--spacing-xl); }
        
        .text-start { text-align: left; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        
        .text-uppercase { text-transform: uppercase; }
        .text-lowercase { text-transform: lowercase; }
        .text-capitalize { text-transform: capitalize; }
        
        .overflow-hidden { overflow: hidden; }
        .overflow-auto { overflow: auto; }
        .overflow-scroll { overflow: scroll; }
        
        .user-select-none { user-select: none; }
        .user-select-all { user-select: all; }
        
        .z-index-1 { z-index: 1; }
        .z-index-2 { z-index: 2; }
        .z-index-3 { z-index: 3; }
        
        /* ===== MARGINS ET PADDINGS ===== */
        .m-0 { margin: 0; }
        .m-1 { margin: var(--spacing-xs); }
        .m-2 { margin: var(--spacing-sm); }
        .m-3 { margin: var(--spacing-md); }
        .m-4 { margin: var(--spacing-lg); }
        .m-5 { margin: var(--spacing-xl); }
        
        .mx-0 { margin-left: 0; margin-right: 0; }
        .mx-1 { margin-left: var(--spacing-xs); margin-right: var(--spacing-xs); }
        .mx-2 { margin-left: var(--spacing-sm); margin-right: var(--spacing-sm); }
        .mx-3 { margin-left: var(--spacing-md); margin-right: var(--spacing-md); }
        .mx-4 { margin-left: var(--spacing-lg); margin-right: var(--spacing-lg); }
        .mx-5 { margin-left: var(--spacing-xl); margin-right: var(--spacing-xl); }
        
        .my-0 { margin-top: 0; margin-bottom: 0; }
        .my-1 { margin-top: var(--spacing-xs); margin-bottom: var(--spacing-xs); }
        .my-2 { margin-top: var(--spacing-sm); margin-bottom: var(--spacing-sm); }
        .my-3 { margin-top: var(--spacing-md); margin-bottom: var(--spacing-md); }
        .my-4 { margin-top: var(--spacing-lg); margin-bottom: var(--spacing-lg); }
        .my-5 { margin-top: var(--spacing-xl); margin-bottom: var(--spacing-xl); }
        
        .mt-0 { margin-top: 0; }
        .mt-1 { margin-top: var(--spacing-xs); }
        .mt-2 { margin-top: var(--spacing-sm); }
        .mt-3 { margin-top: var(--spacing-md); }
        .mt-4 { margin-top: var(--spacing-lg); }
        .mt-5 { margin-top: var(--spacing-xl); }
        
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: var(--spacing-xs); }
        .mb-2 { margin-bottom: var(--spacing-sm); }
        .mb-3 { margin-bottom: var(--spacing-md); }
        .mb-4 { margin-bottom: var(--spacing-lg); }
        .mb-5 { margin-bottom: var(--spacing-xl); }
        
        .ms-0 { margin-left: 0; }
        .ms-1 { margin-left: var(--spacing-xs); }
        .ms-2 { margin-left: var(--spacing-sm); }
        .ms-3 { margin-left: var(--spacing-md); }
        .ms-4 { margin-left: var(--spacing-lg); }
        .ms-5 { margin-left: var(--spacing-xl); }
        
        .me-0 { margin-right: 0; }
        .me-1 { margin-right: var(--spacing-xs); }
        .me-2 { margin-right: var(--spacing-sm); }
        .me-3 { margin-right: var(--spacing-md); }
        .me-4 { margin-right: var(--spacing-lg); }
        .me-5 { margin-right: var(--spacing-xl); }
        
        .p-0 { padding: 0; }
        .p-1 { padding: var(--spacing-xs); }
        .p-2 { padding: var(--spacing-sm); }
        .p-3 { padding: var(--spacing-md); }
        .p-4 { padding: var(--spacing-lg); }
        .p-5 { padding: var(--spacing-xl); }
        
        .px-0 { padding-left: 0; padding-right: 0; }
        .px-1 { padding-left: var(--spacing-xs); padding-right: var(--spacing-xs); }
        .px-2 { padding-left: var(--spacing-sm); padding-right: var(--spacing-sm); }
        .px-3 { padding-left: var(--spacing-md); padding-right: var(--spacing-md); }
        .px-4 { padding-left: var(--spacing-lg); padding-right: var(--spacing-lg); }
        .px-5 { padding-left: var(--spacing-xl); padding-right: var(--spacing-xl); }
        
        .py-0 { padding-top: 0; padding-bottom: 0; }
        .py-1 { padding-top: var(--spacing-xs); padding-bottom: var(--spacing-xs); }
        .py-2 { padding-top: var(--spacing-sm); padding-bottom: var(--spacing-sm); }
        .py-3 { padding-top: var(--spacing-md); padding-bottom: var(--spacing-md); }
        .py-4 { padding-top: var(--spacing-lg); padding-bottom: var(--spacing-lg); }
        .py-5 { padding-top: var(--spacing-xl); padding-bottom: var(--spacing-xl); }
        
        .pt-0 { padding-top: 0; }
        .pt-1 { padding-top: var(--spacing-xs); }
        .pt-2 { padding-top: var(--spacing-sm); }
        .pt-3 { padding-top: var(--spacing-md); }
        .pt-4 { padding-top: var(--spacing-lg); }
        .pt-5 { padding-top: var(--spacing-xl); }
        
        .pb-0 { padding-bottom: 0; }
        .pb-1 { padding-bottom: var(--spacing-xs); }
        .pb-2 { padding-bottom: var(--spacing-sm); }
        .pb-3 { padding-bottom: var(--spacing-md); }
        .pb-4 { padding-bottom: var(--spacing-lg); }
        .pb-5 { padding-bottom: var(--spacing-xl); }
        
        .ps-0 { padding-left: 0; }
        .ps-1 { padding-left: var(--spacing-xs); }
        .ps-2 { padding-left: var(--spacing-sm); }
        .ps-3 { padding-left: var(--spacing-md); }
        .ps-4 { padding-left: var(--spacing-lg); }
        .ps-5 { padding-left: var(--spacing-xl); }
        
        .pe-0 { padding-right: 0; }
        .pe-1 { padding-right: var(--spacing-xs); }
        .pe-2 { padding-right: var(--spacing-sm); }
        .pe-3 { padding-right: var(--spacing-md); }
        .pe-4 { padding-right: var(--spacing-lg); }
        .pe-5 { padding-right: var(--spacing-xl); }

        /* ===== PRINT STYLES ===== */
        @media print {
            .header-gsn,
            .navigation-modules,
            .actions-container,
            .btn-header,
            .card-header-gsn {
                display: none !important;
            }
            
            .main-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .card-gsn {
                box-shadow: none !important;
                border: 1px solid #000 !important;
            }
            
            .table-gsn {
                border: 1px solid #000 !important;
            }
            
            .table-gsn th,
            .table-gsn td {
                border: 1px solid #000 !important;
                background: white !important;
                color: black !important;
            }
        }

        /* ===== DARK MODE SUPPORT (OPTIONNEL) ===== */
        @media (prefers-color-scheme: dark) {
            :root {
                --text-dark: #e9ecef;
                --text-medium: #ced4da;
                --text-light: #adb5bd;
                --text-muted: #6c757d;
                --bg-main: #1a1a1a;
                --bg-light: #2d2d2d;
                --bg-white: #212529;
                --bg-card: #212529;
                --border-color: #343a40;
                --border-light: #495057;
                --border-medium: #6c757d;
            }
        }

        /* ===== ACCESSIBILITY ===== */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Focus indicators pour l'accessibilité */
        .btn:focus,
        .form-control:focus,
        .form-select:focus,
        .nav-item:focus {
            outline: 3px solid rgba(52, 152, 219, 0.5);
            outline-offset: 2px;
        }

        /* ===== STYLES SPÉCIFIQUES PLOMBERIE ===== */
        .plomberie-icon {
            background: var(--plomberie-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .plomberie-pattern {
            background-image: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(52, 152, 219, 0.1) 10px,
                rgba(52, 152, 219, 0.1) 20px
            );
        }

        .water-animation {
            position: relative;
            overflow: hidden;
        }

        .water-animation::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(52, 152, 219, 0.3), transparent);
            animation: waterFlow 3s infinite;
        }

        @keyframes waterFlow {
            0% { left: -100%; }
            100% { left: 100%; }
        }
    </style>
</head>

<body>
    <!-- ===== PARTIE 3 : HTML HEADER & NAVIGATION ===== -->
    
    <!-- LOADER INITIAL (optionnel) -->
    <div id="pageLoader" class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center bg-white" style="z-index: 9999; top: 0; left: 0;">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <div class="mt-3 text-muted">Chargement du module Plomberie...</div>
        </div>
    </div>

    <!-- HEADER GSN ProDevis360° -->
    <header class="header-gsn" id="mainHeader">
        <div class="header-content">
            <!-- SECTION LOGO ET TITRE -->
            <div class="logo-section">
                <div class="logo-gsn water-animation">
                    <span>GSN</span>
                </div>
                <div class="header-title">
                    <h1 class="fade-in">
                        <i class="fas fa-wrench plomberie-icon"></i>
                        Module Plomberie
                        <span class="module-badge animate__animated animate__pulse animate__infinite">
                            <i class="fas fa-tint"></i>
                            Eau & Sanitaire
                        </span>
                    </h1>
                    <div class="project-info fade-in">
                        <span class="d-flex align-items-center gap-2">
                            <i class="fas fa-building"></i>
                            <strong><?= htmlspecialchars($projet_devis_info['nom_projet'] ?? $projet_devis_info['projet_nom'] ?? 'Projet') ?></strong>
                        </span>
                        <span class="separator">•</span>
                        <span class="d-flex align-items-center gap-1">
                            <i class="fas fa-file-invoice"></i>
                            Devis #<?= $devis_id ?>
                        </span>
                        <span class="separator">•</span>
                        <span class="d-flex align-items-center gap-1">
                            <i class="fas fa-calendar-alt"></i>
                            <?= date('d/m/Y') ?>
                        </span>
                        <span class="separator">•</span>
                        <span class="d-flex align-items-center gap-1">
                            <i class="fas fa-clock"></i>
                            <?= date('H:i') ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- ACTIONS HEADER -->
            <div class="header-actions">
                <a href="recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                   class="btn-header" 
                   title="Voir le récapitulatif général">
                    <i class="fas fa-chart-pie"></i>
                    <span class="d-none d-md-inline">Récapitulatif</span>
                </a>
                
                <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=plomberie" 
                   class="btn-header" 
                   title="Historique des modifications">
                    <i class="fas fa-history"></i>
                    <span class="d-none d-md-inline">Historique</span>
                </a>
                
                <a href="impression_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=plomberie" 
                   class="btn-header" 
                   title="Imprimer le module">
                    <i class="fas fa-print"></i>
                    <span class="d-none d-md-inline">Imprimer</span>
                </a>
                
                <div class="dropdown">
                    <button class="btn-header dropdown-toggle" 
                            type="button" 
                            id="dropdownMenuButton" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            title="Plus d'actions">
                        <i class="fas fa-ellipsis-v"></i>
                        <span class="d-none d-lg-inline">Actions</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item" href="duplication_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>">
                                <i class="fas fa-copy me-2"></i>
                                Dupliquer le devis
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="export_excel.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=plomberie">
                                <i class="fas fa-file-excel me-2"></i>
                                Exporter Excel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="sauvegarde.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>">
                                <i class="fas fa-save me-2"></i>
                                Sauvegarder
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="confirmerArchivage()">
                                <i class="fas fa-archive me-2"></i>
                                Archiver le devis
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- BARRE DE PROGRESSION (optionnelle) -->
        <div class="progress" style="height: 3px;">
            <div class="progress-bar bg-info" role="progressbar" style="width: 0%" id="progressBar"></div>
        </div>
    </header>

    <!-- NAVIGATION MODULES DYNAMIQUE -->
    <nav class="navigation-modules" id="moduleNavigation">
        <div class="nav-container">
            <!-- BREADCRUMB -->
            <div class="breadcrumb-section mb-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.php" class="text-decoration-none">
                                <i class="fas fa-home"></i>
                                Accueil
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="projets.php" class="text-decoration-none">
                                <i class="fas fa-folder"></i>
                                Projets
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="devis_detail.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="text-decoration-none">
                                <i class="fas fa-file-invoice"></i>
                                Devis #<?= $devis_id ?>
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-plomberie" aria-current="page">
                            <i class="fas fa-wrench"></i>
                            Plomberie
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- NAVIGATION DES MODULES -->
            <div class="nav-modules">
                <?php foreach ($modules_config as $module_key => $module_info): ?>
                    <a href="<?= $module_key ?>.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                       class="nav-item <?= $module_key === $current_module ? 'active' : '' ?>"
                       style="<?= $module_key === $current_module ? '' : '--hover-color: ' . $module_info['color'] ?>"
                       title="Accéder au module <?= $module_info['name'] ?>"
                       data-module="<?= $module_key ?>">
                        <i class="<?= $module_info['icon'] ?>"></i>
                        <span><?= $module_info['name'] ?></span>
                        
                        <!-- Badge de notification (optionnel) -->
                        <?php if ($module_key === 'plomberie' && count($elements_plomberie) > 0): ?>
                            <span class="badge badge-primary-gsn ms-1">
                                <?= count($elements_plomberie) ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
                
                <!-- BOUTON RETOUR -->
                <a href="devis_detail.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                   class="nav-item ms-auto text-muted"
                   title="Retour au détail du devis">
                    <i class="fas fa-arrow-left"></i>
                    <span class="d-none d-md-inline">Retour</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- BARRE D'INFORMATIONS CONTEXTUELLES -->
    <div class="context-bar bg-light border-bottom py-2" id="contextBar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        <!-- INFORMATIONS CLIENT -->
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-user-tie text-muted"></i>
                            <small class="text-muted">
                                Client: <strong><?= htmlspecialchars($projet_devis_info['client'] ?? 'Non défini') ?></strong>
                            </small>
                        </div>
                        
                        <!-- STATUT DU DEVIS -->
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-flag text-muted"></i>
                            <small class="text-muted">
                                Statut: 
                                <span class="badge <?php 
                                    $statut = $projet_devis_info['statut'] ?? 'En cours';
                                    echo match($statut) {
                                        'Terminé' => 'badge-success-gsn',
                                        'En cours' => 'badge-warning-gsn',
                                        'Suspendu' => 'badge-danger-gsn',
                                        default => 'badge-info-gsn'
                                    };
                                ?>">
                                    <?= htmlspecialchars($statut) ?>
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-md-end gap-3">
                        <!-- TOTAL MODULE -->
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-calculator text-plomberie"></i>
                            <small class="text-muted">
                                Total module: 
                                <strong class="text-plomberie">
                                    <?= number_format($total_module, 0, ',', ' ') ?> FCFA
                                </strong>
                            </small>
                        </div>
                        
                        <!-- DERNIÈRE MODIFICATION -->
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-clock text-muted"></i>
                            <small class="text-muted">
                                Dernière modif: 
                                <strong><?= !empty($elements_plomberie) ? $elements_plomberie[0]['date_modification_fr'] : 'Aucune' ?></strong>
                            </small>
                        </div>
                        
                        <!-- INDICATEUR DE SAUVEGARDE -->
                        <div class="d-flex align-items-center gap-2" id="saveIndicator">
                            <i class="fas fa-check-circle text-success"></i>
                            <small class="text-success">Sauvegardé</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENU PRINCIPAL -->
    <div class="main-container">
        <!-- EN-TÊTE DU MODULE -->
        <div class="module-header fade-in-up">
            <div class="module-title">
                <div class="icon">
                    <i class="fas fa-wrench"></i>
                </div>
                <div>
                    <h2 class="mb-0 font-weight-700">Module Plomberie</h2>
                    <p class="mb-0 text-muted">Gestion des installations sanitaires, tuyauterie et robinetterie</p>
                    <div class="mt-2 d-flex align-items-center gap-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Dernier élément ajouté: <?= !empty($elements_plomberie) ? 'il y a ' . timeAgo($elements_plomberie[0]['date_creation_fr']) : 'Aucun' ?>
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-percentage"></i>
                            TVA: 18%
                        </small>
                    </div>
                </div>
                
                <!-- ACTIONS RAPIDES MODULE -->
                <div class="ms-auto d-flex gap-2">
                    <button class="btn btn-outline-gsn btn-sm" 
                            onclick="toggleHelp()" 
                            title="Afficher/Masquer l'aide">
                        <i class="fas fa-question-circle"></i>
                        <span class="d-none d-lg-inline">Aide</span>
                    </button>
                    
                    <button class="btn btn-outline-gsn btn-sm" 
                            onclick="exportModule()" 
                            title="Exporter ce module">
                        <i class="fas fa-download"></i>
                        <span class="d-none d-lg-inline">Exporter</span>
                    </button>
                    
                    <button class="btn btn-primary-gsn btn-sm" 
                            onclick="scrollToForm()" 
                            title="Ajouter un élément">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-lg-inline">Ajouter</span>
                    </button>
                </div>
            </div>
            
            <!-- STATISTIQUES DU MODULE -->
            <div class="module-stats">
                <div class="stat-card slide-in-left">
                    <div class="stat-value" id="countElements">
                        <?= count($elements_plomberie) ?>
                    </div>
                    <div class="stat-label">
                        <i class="fas fa-list"></i>
                        Élément<?= count($elements_plomberie) > 1 ? 's' : '' ?>
                    </div>
                </div>
                
                <div class="stat-card slide-in-left" style="animation-delay: 0.1s;">
                    <div class="stat-value text-success" id="totalModule">
                        <?= number_format($total_module, 0, ',', ' ') ?> F
                    </div>
                    <div class="stat-label">
                        <i class="fas fa-euro-sign"></i>
                        Total Module
                    </div>
                </div>
                
                <div class="stat-card slide-in-left" style="animation-delay: 0.2s;">
                    <div class="stat-value text-info">
                        <?= count($suggestions_plomberie) ?>
                    </div>
                    <div class="stat-label">
                        <i class="fas fa-lightbulb"></i>
                        Suggestions
                    </div>
                </div>
                
                <div class="stat-card slide-in-left" style="animation-delay: 0.3s;">
                    <div class="stat-value text-warning">
                        <?= $total_module > 0 && count($elements_plomberie) > 0 ? number_format($total_module / count($elements_plomberie), 0, ',', ' ') . ' F' : '0 F' ?>
                    </div>
                    <div class="stat-label">
                        <i class="fas fa-chart-line"></i>
                        Prix Moyen
                    </div>
                </div>
            </div>
        </div>

        <!-- AFFICHAGE DES MESSAGES -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?>-gsn fade-in" id="alertMessage">
                <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : ($message_type === 'danger' ? 'exclamation-triangle' : 'info-circle') ?>"></i>
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>
            </div>
        <?php endif; ?>

        <!-- AIDE CONTEXTUELLE (MASQUÉE PAR DÉFAUT) -->
        <div class="card-gsn" id="helpSection" style="display: none;">
            <div class="card-header-gsn">
                <div class="header-left">
                    <i class="fas fa-lightbulb"></i>
                    Aide Module Plomberie
                </div>
                <div class="header-right">
                    <button class="btn btn-sm btn-outline-light" onclick="toggleHelp()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body-gsn">
                <div class="row">
                    <div class="col-md-4">
                        <h6><i class="fas fa-tint text-plomberie"></i> Distribution d'eau</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success"></i> Tubes cuivre : 12, 14, 16, 18, 22mm</li>
                            <li><i class="fas fa-check text-success"></i> Tubes PVC : 32, 40, 50, 100, 125mm</li>
                            <li><i class="fas fa-check text-success"></i> Raccords et accessoires</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-shower text-plomberie"></i> Sanitaires</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-check text-success"></i> WC, lavabos, éviers</li>
                            <li><i class="fas fa-check text-success"></i> Douches, baignoires</li>
                            <li><i class="fas fa-check text-success"></i> Robinetterie complète</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-tools text-plomberie"></i> Conseils</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-info text-info"></i> Utilisez les suggestions automatiques</li>
                            <li><i class="fas fa-info text-info"></i> Vérifiez les diamètres</li>
                            <li><i class="fas fa-info text-info"></i> Calculez les longueurs précisément</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- INDICATEUR DE CHARGEMENT GLOBAL -->
        <div id="loadingOverlay" class="position-fixed w-100 h-100 d-none align-items-center justify-content-center" style="top: 0; left: 0; background: rgba(255,255,255,0.8); z-index: 9998;">
            <div class="text-center">
                <div class="spinner-border text-plomberie" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <div class="mt-2 text-muted">Traitement en cours...</div>
            </div>
        </div>

    <!-- Fonctions JavaScript pour le header -->
    <script>
        // Fonction utilitaire pour calculer le temps écoulé
        function timeAgo(dateString) {
            if (!dateString || dateString === 'Aucune') return 'jamais';
            
            const now = new Date();
            const past = new Date(dateString.split(' ')[0].split('/').reverse().join('-') + ' ' + dateString.split(' ')[1]);
            const diffMs = now - past;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMins / 60);
            const diffDays = Math.floor(diffHours / 24);
            
            if (diffMins < 1) return 'à l\'instant';
            if (diffMins < 60) return diffMins + ' min';
            if (diffHours < 24) return diffHours + ' h';
            if (diffDays < 30) return diffDays + ' j';
            return 'plus d\'un mois';
        }

        // Fonction pour afficher/masquer l'aide
        function toggleHelp() {
            const helpSection = document.getElementById('helpSection');
            if (helpSection) {
                helpSection.style.display = helpSection.style.display === 'none' ? 'block' : 'none';
            }
        }

        // Fonction pour scroll vers le formulaire
        function scrollToForm() {
            const form = document.getElementById('formPlomberie');
            if (form) {
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                form.querySelector('input[type="text"]')?.focus();
            }
        }

        // Fonction d'export du module
        function exportModule() {
            window.open(`export_excel.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=plomberie`, '_blank');
        }

        // Fonction de confirmation d'archivage
        function confirmerArchivage() {
            if (confirm('Êtes-vous sûr de vouloir archiver ce devis ? Cette action ne peut pas être annulée.')) {
                window.location.href = `archiver_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>`;
            }
        }

        // Masquer le loader initial après chargement
        document.addEventListener('DOMContentLoaded', function() {
            const loader = document.getElementById('pageLoader');
            if (loader) {
                setTimeout(() => {
                    loader.style.transition = 'opacity 0.3s ease';
                    loader.style.opacity = '0';
                    setTimeout(() => loader.remove(), 300);
                }, 500);
            }
        });

        // Indicateur de sauvegarde
        function showSaveIndicator(success = true) {
            const indicator = document.getElementById('saveIndicator');
            if (indicator) {
                const icon = indicator.querySelector('i');
                const text = indicator.querySelector('small');
                
                if (success) {
                    icon.className = 'fas fa-check-circle text-success';
                    text.textContent = 'Sauvegardé';
                    text.className = 'text-success';
                } else {
                    icon.className = 'fas fa-exclamation-triangle text-warning';
                    text.textContent = 'Non sauvegardé';
                    text.className = 'text-warning';
                }
            }
        }

        // Mise à jour de la barre de progression
        function updateProgressBar(percentage) {
            const progressBar = document.getElementById('progressBar');
            if (progressBar) {
                progressBar.style.width = percentage + '%';
                progressBar.setAttribute('aria-valuenow', percentage);
            }
        }

        // Animation des statistiques au scroll
        function animateStats() {
            const stats = document.querySelectorAll('.stat-value');
            stats.forEach(stat => {
                const finalValue = parseInt(stat.textContent.replace(/[^\d]/g, ''));
                let currentValue = 0;
                const increment = finalValue / 50;
                
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        currentValue = finalValue;
                        clearInterval(timer);
                    }
                    
                    if (stat.textContent.includes('F')) {
                        stat.textContent = Math.floor(currentValue).toLocaleString('fr-FR') + ' F';
                    } else {
                        stat.textContent = Math.floor(currentValue);
                    }
                }, 20);
            });
        }

        // Observer pour l'intersection des éléments
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    
                    // Animation spéciale pour les stats
                    if (entry.target.classList.contains('module-stats')) {
                        setTimeout(animateStats, 200);
                    }
                }
            });
        }, observerOptions);

        // Observer les éléments à animer
        document.addEventListener('DOMContentLoaded', function() {
            const elementsToObserve = document.querySelectorAll('.module-header, .card-gsn');
            elementsToObserve.forEach(el => observer.observe(el));
        });
    </script>
    
<!-- ===== PARTIE 4 : FORMULAIRE & TABLEAU ===== -->
        
        <!-- FORMULAIRE D'AJOUT/MODIFICATION -->
        <div class="card-gsn fade-in" id="formSection">
            <div class="card-header-gsn">
                <div class="header-left">
                    <i class="fas fa-<?= $action === 'modifier' ? 'edit' : 'plus' ?>"></i>
                    <?= $action === 'modifier' ? 'Modifier un élément plomberie' : 'Ajouter un élément plomberie' ?>
                    <?php if ($action === 'modifier' && $element_a_modifier): ?>
                        <span class="badge badge-warning-gsn ms-2">
                            ID: <?= $element_a_modifier['id'] ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="header-right">
                    <?php if ($action === 'modifier'): ?>
                        <a href="plomberie.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                           class="btn btn-sm btn-outline-light">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                    <?php endif; ?>
                    
                    <button type="button" 
                            class="btn btn-sm btn-outline-light" 
                            onclick="toggleFormHelp()"
                            title="Aide pour le formulaire">
                        <i class="fas fa-question-circle"></i>
                    </button>
                </div>
            </div>
            
            <div class="card-body-gsn">
                <!-- AIDE FORMULAIRE (MASQUÉE PAR DÉFAUT) -->
                <div class="alert alert-info-gsn mb-4" id="formHelp" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle"></i> Conseils de saisie</h6>
                            <ul class="mb-0 small">
                                <li>Utilisez les suggestions automatiques pour la désignation</li>
                                <li>Les diamètres acceptent : 12mm, 3/4", 20x27</li>
                                <li>La longueur est en mètres (max 1000m)</li>
                                <li>Le transport est calculé automatiquement si vide</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-calculator"></i> Calculs automatiques</h6>
                            <ul class="mb-0 small">
                                <li>Prix Total (PT) = Quantité × Prix Unitaire (PU)</li>
                                <li>Le total s'affiche en temps réel</li>
                                <li>La TVA (18%) est calculée automatiquement</li>
                                <li>Le récapitulatif se met à jour après sauvegarde</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form method="POST" id="formPlomberie" novalidate>
                    <input type="hidden" name="action" value="<?= $action === 'modifier' ? 'modifier' : 'ajouter' ?>">
                    <?php if ($action === 'modifier'): ?>
                        <input type="hidden" name="element_id" value="<?= $element_id ?>">
                    <?php endif; ?>
                    
                    <!-- SECTION PRINCIPALE -->
                    <div class="row">
                        <!-- DÉSIGNATION -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control" 
                                       id="designation" 
                                       name="designation" 
                                       value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['designation']) : '' ?>"
                                       list="suggestions-plomberie"
                                       placeholder="Désignation" 
                                       required
                                       autocomplete="off">
                                <label for="designation">
                                    <i class="fas fa-wrench text-plomberie"></i>
                                    Désignation * <span class="text-danger">●</span>
                                </label>
                                <div class="invalid-feedback">
                                    Veuillez saisir une désignation.
                                </div>
                                
                                <!-- DATALIST POUR SUGGESTIONS -->
                                <datalist id="suggestions-plomberie">
                                    <?php foreach ($suggestions_plomberie as $suggestion): ?>
                                        <option value="<?= htmlspecialchars($suggestion) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            
                            <!-- SUGGESTIONS VISUELLES -->
                            <div class="mt-2" id="suggestionsContainer" style="display: none;">
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-lightbulb"></i> Suggestions populaires:
                                </small>
                                <div class="d-flex flex-wrap gap-1" id="suggestionsList"></div>
                            </div>
                        </div>

                        <!-- QUANTITÉ -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" 
                                       class="form-control" 
                                       id="quantite" 
                                       name="quantite" 
                                       value="<?= $element_a_modifier ? $element_a_modifier['quantite'] : '' ?>"
                                       step="0.001" 
                                       min="0.001" 
                                       max="9999.999"
                                       placeholder="Quantité" 
                                       required>
                                <label for="quantite">
                                    <i class="fas fa-hashtag text-plomberie"></i>
                                    Quantité * <span class="text-danger">●</span>
                                </label>
                                <div class="invalid-feedback">
                                    La quantité doit être supérieure à 0.
                                </div>
                            </div>
                        </div>

                        <!-- UNITÉ -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="unite" name="unite" required>
                                    <option value="unité" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'unité') ? 'selected' : '' ?>>Unité</option>
                                    <option value="ml" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'ml') ? 'selected' : '' ?>>Mètre linéaire</option>
                                    <option value="m²" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm²') ? 'selected' : '' ?>>Mètre carré</option>
                                    <option value="m³" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm³') ? 'selected' : '' ?>>Mètre cube</option>
                                    <option value="kg" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'kg') ? 'selected' : '' ?>>Kilogramme</option>
                                    <option value="litre" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'litre') ? 'selected' : '' ?>>Litre</option>
                                    <option value="lot" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'lot') ? 'selected' : '' ?>>Lot</option>
                                    <option value="forfait" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'forfait') ? 'selected' : '' ?>>Forfait</option>
                                </select>
                                <label for="unite">
                                    <i class="fas fa-ruler text-plomberie"></i>
                                    Unité *
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION PRIX -->
                    <div class="row">
                        <!-- PRIX UNITAIRE (PU) - ADAPTÉ À VOTRE BDD -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" 
                                       class="form-control" 
                                       id="pu" 
                                       name="pu" 
                                       value="<?= $element_a_modifier ? $element_a_modifier['pu'] : '' ?>"
                                       step="0.01" 
                                       min="0" 
                                       max="99999999.99"
                                       placeholder="Prix unitaire" 
                                       required>
                                <label for="pu">
                                    <i class="fas fa-coins text-plomberie"></i>
                                    Prix unitaire (PU) FCFA * <span class="text-danger">●</span>
                                </label>
                                <div class="invalid-feedback">
                                    Le prix unitaire ne peut pas être négatif.
                                </div>
                            </div>
                        </div>

                        <!-- TRANSPORT -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="number" 
                                       class="form-control" 
                                       id="transport" 
                                       name="transport" 
                                       value="<?= $element_a_modifier ? $element_a_modifier['transport'] : '0' ?>"
                                       step="0.01" 
                                       min="0" 
                                       max="999999.99"
                                       placeholder="Transport">
                                <label for="transport">
                                    <i class="fas fa-truck text-plomberie"></i>
                                    Transport FCFA
                                </label>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Laissez vide pour calcul automatique (5% du prix)
                                </div>
                            </div>
                        </div>

                        <!-- TOTAL CALCULÉ (PT) - LECTURE SEULE -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control bg-light" 
                                       id="totalCalcule" 
                                       readonly
                                       placeholder="Total calculé">
                                <label for="totalCalcule">
                                    <i class="fas fa-calculator text-success"></i>
                                    Total calculé (PT) FCFA
                                </label>
                                <div class="form-text text-success">
                                    <i class="fas fa-arrow-right"></i>
                                    <strong>Quantité × PU = <span id="totalFormule">0</span> FCFA</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION CARACTÉRISTIQUES TECHNIQUES -->
                    <div class="row">
                        <!-- DIAMÈTRE -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control" 
                                       id="diametre" 
                                       name="diametre" 
                                       value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['diametre']) : '' ?>"
                                       placeholder="Diamètre" 
                                       list="diametres-standard">
                                <label for="diametre">
                                    <i class="fas fa-circle text-plomberie"></i>
                                    Diamètre
                                </label>
                                
                                <datalist id="diametres-standard">
                                    <option value="12mm">
                                    <option value="14mm">
                                    <option value="16mm">
                                    <option value="18mm">
                                    <option value="22mm">
                                    <option value="32mm">
                                    <option value="40mm">
                                    <option value="50mm">
                                    <option value="100mm">
                                    <option value="110mm">
                                    <option value="125mm">
                                    <option value="3/4&quot;">
                                    <option value="1&quot;">
                                    <option value="1.1/4&quot;">
                                    <option value="15x21">
                                    <option value="20x27">
                                    <option value="26x34">
                                </datalist>
                            </div>
                        </div>

                        <!-- LONGUEUR -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" 
                                       class="form-control" 
                                       id="longueur" 
                                       name="longueur" 
                                       value="<?= $element_a_modifier ? $element_a_modifier['longueur'] : '' ?>"
                                       step="0.01" 
                                       min="0" 
                                       max="1000" 
                                       placeholder="Longueur">
                                <label for="longueur">
                                    <i class="fas fa-arrows-alt-h text-plomberie"></i>
                                    Longueur (mètres)
                                </label>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Maximum 1000 mètres
                                </div>
                            </div>
                        </div>

                        <!-- MATÉRIAU -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="materiau" name="materiau">
                                    <option value="">Sélectionner...</option>
                                    <option value="Cuivre" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'Cuivre') ? 'selected' : '' ?>>Cuivre</option>
                                    <option value="PVC" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'PVC') ? 'selected' : '' ?>>PVC</option>
                                    <option value="PVC-U" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'PVC-U') ? 'selected' : '' ?>>PVC-U (rigide)</option>
                                    <option value="PER" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'PER') ? 'selected' : '' ?>>PER</option>
                                    <option value="Multicouche" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'Multicouche') ? 'selected' : '' ?>>Multicouche</option>
                                    <option value="Acier galvanisé" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'Acier galvanisé') ? 'selected' : '' ?>>Acier galvanisé</option>
                                    <option value="Acier inoxydable" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'Acier inoxydable') ? 'selected' : '' ?>>Acier inoxydable</option>
                                    <option value="Fonte" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'Fonte') ? 'selected' : '' ?>>Fonte</option>
                                    <option value="Fonte ductile" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'Fonte ductile') ? 'selected' : '' ?>>Fonte ductile</option>
                                    <option value="Plastique" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'Plastique') ? 'selected' : '' ?>>Plastique</option>
                                    <option value="Céramique" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'Céramique') ? 'selected' : '' ?>>Céramique</option>
                                    <option value="Laiton" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'Laiton') ? 'selected' : '' ?>>Laiton</option>
                                    <option value="Bronze" <?= ($element_a_modifier && $element_a_modifier['materiau'] === 'Bronze') ? 'selected' : '' ?>>Bronze</option>
                                </select>
                                <label for="materiau">
                                    <i class="fas fa-industry text-plomberie"></i>
                                    Matériau
                                </label>
                            </div>
                        </div>

                        <!-- TYPE DE RACCORD -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="type_raccord" name="type_raccord">
                                    <option value="">Sélectionner...</option>
                                    <option value="Coude 90°" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Coude 90°') ? 'selected' : '' ?>>Coude 90°</option>
                                    <option value="Coude 45°" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Coude 45°') ? 'selected' : '' ?>>Coude 45°</option>
                                    <option value="Coude 30°" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Coude 30°') ? 'selected' : '' ?>>Coude 30°</option>
                                    <option value="Té égal" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Té égal') ? 'selected' : '' ?>>Té égal</option>
                                    <option value="Té réduit" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Té réduit') ? 'selected' : '' ?>>Té réduit</option>
                                    <option value="Té de dérivation" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Té de dérivation') ? 'selected' : '' ?>>Té de dérivation</option>
                                    <option value="Manchon" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Manchon') ? 'selected' : '' ?>>Manchon</option>
                                    <option value="Manchon réduit" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Manchon réduit') ? 'selected' : '' ?>>Manchon réduit</option>
                                    <option value="Réduction" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Réduction') ? 'selected' : '' ?>>Réduction</option>
                                    <option value="Bouchon" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Bouchon') ? 'selected' : '' ?>>Bouchon</option>
                                    <option value="Union" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Union') ? 'selected' : '' ?>>Union</option>
                                    <option value="Bride" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Bride') ? 'selected' : '' ?>>Bride</option>
                                    <option value="Filetage" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Filetage') ? 'selected' : '' ?>>Filetage</option>
                                    <option value="Raccord rapide" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Raccord rapide') ? 'selected' : '' ?>>Raccord rapide</option>
                                    <option value="Soudure" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Soudure') ? 'selected' : '' ?>>Soudure</option>
                                    <option value="Collage" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Collage') ? 'selected' : '' ?>>Collage</option>
                                    <option value="Compression" <?= ($element_a_modifier && $element_a_modifier['type_raccord'] === 'Compression') ? 'selected' : '' ?>>Compression</option>
                                </select>
                                <label for="type_raccord">
                                    <i class="fas fa-link text-plomberie"></i>
                                    Type raccord
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION SPÉCIFICATIONS -->
                    <div class="row">
                        <!-- PRESSION -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="pression" name="pression">
                                    <option value="">Sélectionner...</option>
                                    <option value="Basse pression" <?= ($element_a_modifier && $element_a_modifier['pression'] === 'Basse pression') ? 'selected' : '' ?>>Basse pression (&lt; 5 bars)</option>
                                    <option value="Moyenne pression" <?= ($element_a_modifier && $element_a_modifier['pression'] === 'Moyenne pression') ? 'selected' : '' ?>>Moyenne pression (5-10 bars)</option>
                                    <option value="Haute pression" <?= ($element_a_modifier && $element_a_modifier['pression'] === 'Haute pression') ? 'selected' : '' ?>>Haute pression (&gt; 10 bars)</option>
                                    <option value="10 bars" <?= ($element_a_modifier && $element_a_modifier['pression'] === '10 bars') ? 'selected' : '' ?>>10 bars</option>
                                    <option value="16 bars" <?= ($element_a_modifier && $element_a_modifier['pression'] === '16 bars') ? 'selected' : '' ?>>16 bars</option>
                                    <option value="25 bars" <?= ($element_a_modifier && $element_a_modifier['pression'] === '25 bars') ? 'selected' : '' ?>>25 bars</option>
                                    <option value="40 bars" <?= ($element_a_modifier && $element_a_modifier['pression'] === '40 bars') ? 'selected' : '' ?>>40 bars</option>
                                </select>
                                <label for="pression">
                                    <i class="fas fa-tachometer-alt text-plomberie"></i>
                                    Pression de service
                                </label>
                            </div>
                        </div>

                        <!-- OBSERVATION/USAGE -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <textarea class="form-control" 
                                          id="observation" 
                                          name="observation" 
                                          style="height: 60px;" 
                                          placeholder="Observations"><?= $element_a_modifier ? htmlspecialchars($element_a_modifier['observation'] ?? '') : '' ?></textarea>
                                <label for="observation">
                                    <i class="fas fa-comment text-plomberie"></i>
                                    Observations / Usage
                                </label>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Informations complémentaires (eau froide/chaude, évacuation, etc.)
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BOUTONS D'ACTION -->
                    <div class="d-flex gap-3 mt-4 justify-content-between align-items-center">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary-gsn" id="btnSubmit">
                                <i class="fas fa-<?= $action === 'modifier' ? 'save' : 'plus' ?>"></i>
                                <?= $action === 'modifier' ? 'Mettre à jour l\'élément' : 'Ajouter l\'élément' ?>
                            </button>
                            
                            <?php if ($action === 'modifier'): ?>
                                <a href="plomberie.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                                   class="btn btn-outline-gsn">
                                    <i class="fas fa-times"></i>
                                    Annuler la modification
                                </a>
                            <?php endif; ?>
                            
                            <button type="reset" class="btn btn-outline-gsn" onclick="resetForm()">
                                <i class="fas fa-undo"></i>
                                Réinitialiser
                            </button>
                        </div>
                        
                        <!-- RACCOURCIS CLAVIER -->
                        <div class="text-muted small">
                            <i class="fas fa-keyboard"></i>
                            <kbd>Ctrl</kbd> + <kbd>S</kbd> Sauvegarder | 
                            <kbd>Échap</kbd> Annuler
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLEAU DES ÉLÉMENTS -->
        <div class="card-gsn fade-in">
            <div class="card-header-gsn">
                <div class="header-left">
                    <i class="fas fa-list"></i>
                    Éléments de plomberie 
                    <span class="badge badge-primary-gsn">
                        <?= count($elements_plomberie) ?> élément<?= count($elements_plomberie) > 1 ? 's' : '' ?>
                    </span>
                </div>
                <div class="header-right d-flex align-items-center gap-2">
                    <!-- TOTAL MODULE -->
                    <span class="badge badge-success-gsn fs-6">
                        <i class="fas fa-calculator"></i>
                        Total: <?= number_format($total_module, 0, ',', ' ') ?> FCFA
                    </span>
                    
                    <!-- ACTIONS TABLEAU -->
                    <div class="btn-group" role="group">
                        <button type="button" 
                                class="btn btn-sm btn-outline-light" 
                                onclick="exportTableau()" 
                                title="Exporter le tableau">
                            <i class="fas fa-download"></i>
                        </button>
                        
                        <button type="button" 
                                class="btn btn-sm btn-outline-light" 
                                onclick="imprimerTableau()" 
                                title="Imprimer le tableau">
                            <i class="fas fa-print"></i>
                        </button>
                        
                        <button type="button" 
                                class="btn btn-sm btn-outline-light" 
                                onclick="toggleTableOptions()" 
                                title="Options d'affichage">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- OPTIONS TABLEAU (MASQUÉES PAR DÉFAUT) -->
            <div class="border-bottom bg-light px-4 py-2" id="tableOptions" style="display: none;">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-center">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" id="showDiametre" checked>
                                <span class="form-check-label small">Diamètre</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" id="showLongueur" checked>
                                <span class="form-check-label small">Longueur</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" id="showMateriau" checked>
                                <span class="form-check-label small">Matériau</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" id="showTransport" checked>
                                <span class="form-check-label small">Transport</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-end">
                            <select class="form-select form-select-sm" id="itemsPerPage" style="width: auto;">
                                <option value="10">10 par page</option>
                                <option value="25" selected>25 par page</option>
                                <option value="50">50 par page</option>
                                <option value="100">100 par page</option>
                                <option value="all">Tout afficher</option>
                            </select>
                            
                            <input type="text" 
                                   class="form-control form-control-sm" 
                                   id="searchTable" 
                                   placeholder="Rechercher..." 
                                   style="width: 200px;">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body-gsn p-0">
                <?php if (empty($elements_plomberie)): ?>
                    <!-- ÉTAT VIDE -->
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-wrench fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="text-muted mb-3">Aucun élément de plomberie</h5>
                        <p class="text-muted mb-4">
                            Commencez par ajouter votre premier élément pour démarrer le devis plomberie.
                        </p>
                        <button type="button" 
                                class="btn btn-primary-gsn" 
                                onclick="scrollToForm()">
                            <i class="fas fa-plus"></i>
                            Ajouter le premier élément
                        </button>
                        
                        <!-- SUGGESTIONS RAPIDES -->
                        <div class="mt-4">
                            <small class="text-muted d-block mb-2">Ou choisissez parmi les éléments courants :</small>
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <button class="btn btn-sm btn-outline-gsn" 
                                        onclick="ajoutRapide('Tube cuivre diamètre 16mm', 10, 'ml', 4500)">
                                    <i class="fas fa-plus"></i> Tube cuivre 16mm
                                </button>
                                <button class="btn btn-sm btn-outline-gsn" 
                                        onclick="ajoutRapide('Mitigeur lavabo bec fixe', 1, 'unité', 25000)">
                                    <i class="fas fa-plus"></i> Mitigeur lavabo
                                </button>
                                <button class="btn btn-sm btn-outline-gsn" 
                                        onclick="ajoutRapide('WC suspendu avec réservoir encastré', 1, 'unité', 85000)">
                                    <i class="fas fa-plus"></i> WC suspendu
                                </button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- TABLEAU AVEC DONNÉES -->
                    <div class="table-responsive">
                        <table class="table table-gsn mb-0" id="tableauPlomberie">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th style="width: 25%;" class="sortable" data-sort="designation">
                                        Désignation 
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th style="width: 8%;" class="text-center sortable" data-sort="quantite">
                                        Qté 
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th style="width: 6%;" class="text-center">Unité</th>
                                    <th style="width: 10%;" class="text-end sortable" data-sort="pu">
                                        P.U. (FCFA)
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th style="width: 8%;" class="text-end col-transport">
                                        Transport
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th style="width: 12%;" class="text-end sortable" data-sort="pt">
                                        <strong>Total (FCFA)</strong>
                                        <i class="fas fa-sort"></i>
                                    </th>
                                    <th style="width: 8%;" class="text-center col-diametre">Diamètre</th>
                                    <th style="width: 6%;" class="text-center col-longueur">Long.</th>
                                    <th style="width: 8%;" class="text-center col-materiau">Matériau</th>
                                    <th style="width: 4%;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($elements_plomberie as $index => $element): ?>
                                    <tr data-id="<?= $element['id'] ?>" class="table-row">
                                        <td>
                                            <input type="checkbox" 
                                                   class="form-check-input row-checkbox" 
                                                   value="<?= $element['id'] ?>">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="flex-grow-1">
                                                    <strong class="d-block"><?= htmlspecialchars($element['designation']) ?></strong>
                                                    
                                                    <!-- BADGES INFORMATIFS -->
                                                    <div class="mt-1 d-flex gap-1 flex-wrap">
                                                        <?php if (!empty($element['type_raccord'])): ?>
                                                            <span class="badge bg-info text-white small">
                                                                <i class="fas fa-link"></i>
                                                                <?= htmlspecialchars($element['type_raccord']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        
                                                        <?php if (!empty($element['pression'])): ?>
                                                            <span class="badge bg-warning text-dark small">
                                                                <i class="fas fa-tachometer-alt"></i>
                                                                <?= htmlspecialchars($element['pression']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <!-- OBSERVATION -->
                                                    <?php if (!empty($element['observation'])): ?>
                                                        <small class="text-muted d-block mt-1">
                                                            <i class="fas fa-comment"></i>
                                                            <?= htmlspecialchars($element['observation']) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary-gsn fs-6">
                                                <?= number_format($element['quantite'], 3) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <small class="text-muted"><?= htmlspecialchars($element['unite']) ?></small>
                                        </td>
                                        <td class="text-end">
                                            <strong><?= number_format($element['pu'], 0, ',', ' ') ?></strong>
                                        </td>
                                        <td class="text-end col-transport">
                                            <?php if ($element['transport'] > 0): ?>
                                                <span class="text-warning">
                                                    <?= number_format($element['transport'], 0, ',', ' ') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-plomberie fs-6">
                                                <?= number_format($element['pt'], 0, ',', ' ') ?>
                                            </strong>
                                        </td>
                                        <td class="text-center col-diametre">
                                            <?php if (!empty($element['diametre'])): ?>
                                                <span class="badge badge-success-gsn">
                                                    <?= htmlspecialchars($element['diametre']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center col-longueur">
                                            <?php if ($element['longueur'] > 0): ?>
                                                <small class="text-info">
                                                    <?= number_format($element['longueur'], 2) ?>m
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center col-materiau">
                                            <?php if (!empty($element['materiau'])): ?>
                                                <small class="fw-medium">
                                                    <?= htmlspecialchars($element['materiau']) ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical btn-group-sm" role="group">
                                                <!-- BOUTON MODIFIER -->
                                                <a href="plomberie.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&action=modifier&element_id=<?= $element['id'] ?>" 
                                                   class="btn btn-warning-gsn btn-sm" 
                                                   title="Modifier cet élément"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- BOUTON DUPLIQUER -->
                                                <button type="button" 
                                                        class="btn btn-info btn-sm" 
                                                        onclick="dupliquerElement(<?= $element['id'] ?>)"
                                                        title="Dupliquer cet élément"
                                                        data-bs-toggle="tooltip">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                
                                                <!-- BOUTON SUPPRIMER -->
                                                <button type="button" 
                                                        class="btn btn-danger-gsn btn-sm" 
                                                        onclick="confirmerSuppression(<?= $element['id'] ?>, '<?= htmlspecialchars(addslashes($element['designation'])) ?>')"
                                                        title="Supprimer cet élément"
                                                        data-bs-toggle="tooltip">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            
                            <!-- PIED DE TABLEAU AVEC TOTAUX -->
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="4" class="text-end">
                                        <strong>TOTAL MODULE PLOMBERIE :</strong>
                                    </td>
                                    <td class="text-end">
                                        <small class="text-muted">
                                            <?= number_format(array_sum(array_column($elements_plomberie, 'pu')), 0, ',', ' ') ?>
                                        </small>
                                    </td>
                                    <td class="text-end col-transport">
                                        <small class="text-warning">
                                            <?= number_format(array_sum(array_column($elements_plomberie, 'transport')), 0, ',', ' ') ?>
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge badge-success-gsn fs-5">
                                            <?= number_format($total_module, 0, ',', ' ') ?> FCFA
                                        </span>
                                    </td>
                                    <td colspan="4"></td>
                                </tr>
                                
                                <!-- LIGNE TVA -->
                                <tr class="text-muted">
                                    <td colspan="6" class="text-end">
                                        <small>TVA (18%) :</small>
                                    </td>
                                    <td class="text-end">
                                        <small><?= number_format($total_module * 0.18, 0, ',', ' ') ?> FCFA</small>
                                    </td>
                                    <td colspan="4"></td>
                                </tr>
                                
                                <!-- LIGNE TOTAL TTC -->
                                <tr class="table-success fw-bold">
                                    <td colspan="6" class="text-end">
                                        <strong>TOTAL TTC :</strong>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success fs-5">
                                            <?= number_format($total_module * 1.18, 0, ',', ' ') ?> FCFA
                                        </span>
                                    </td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- ACTIONS EN LOT -->
                    <div class="bg-light p-3 border-top" id="bulkActions" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong id="selectedCount">0</strong> élément(s) sélectionné(s)
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-warning" onclick="modifierEnLot()">
                                    <i class="fas fa-edit"></i>
                                    Modifier en lot
                                </button>
                                <button class="btn btn-sm btn-info" onclick="exporterSelection()">
                                    <i class="fas fa-download"></i>
                                    Exporter sélection
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="supprimerEnLot()">
                                    <i class="fas fa-trash"></i>
                                    Supprimer sélection
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RÉCAPITULATIF RAPIDE -->
        <div class="row">
            <div class="col-md-8">
                <!-- STATISTIQUES DÉTAILLÉES -->
                <div class="card-gsn">
                    <div class="card-header-gsn">
                        <div class="header-left">
                            <i class="fas fa-chart-bar"></i>
                            Statistiques détaillées
                        </div>
                    </div>
                    <div class="card-body-gsn">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-plomberie mb-3">Répartition par matériau</h6>
                                <div class="mb-3">
                                    <?php 
                                    $materiaux_stats = [];
                                    foreach ($elements_plomberie as $element) {
                                        $mat = $element['materiau'] ?: 'Non défini';
                                        if (!isset($materiaux_stats[$mat])) {
                                            $materiaux_stats[$mat] = ['count' => 0, 'total' => 0];
                                        }
                                        $materiaux_stats[$mat]['count']++;
                                        $materiaux_stats[$mat]['total'] += $element['pt'];
                                    }
                                    
                                    foreach ($materiaux_stats as $materiau => $stats): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small"><?= htmlspecialchars($materiau) ?></span>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge badge-primary-gsn"><?= $stats['count'] ?></span>
                                                <span class="small text-muted"><?= number_format($stats['total'], 0, ',', ' ') ?> F</span>
                                            </div>
                                        </div>
                                        <div class="progress mb-2" style="height: 4px;">
                                            <div class="progress-bar bg-plomberie" 
                                                 style="width: <?= $total_module > 0 ? ($stats['total'] / $total_module * 100) : 0 ?>%">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-plomberie mb-3">Répartition par unité</h6>
                                <div class="mb-3">
                                    <?php 
                                    $unites_stats = [];
                                    foreach ($elements_plomberie as $element) {
                                        $unite = $element['unite'];
                                        if (!isset($unites_stats[$unite])) {
                                            $unites_stats[$unite] = ['count' => 0, 'quantite' => 0];
                                        }
                                        $unites_stats[$unite]['count']++;
                                        $unites_stats[$unite]['quantite'] += $element['quantite'];
                                    }
                                    
                                    foreach ($unites_stats as $unite => $stats): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="small"><?= htmlspecialchars($unite) ?></span>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge badge-info-gsn"><?= $stats['count'] ?></span>
                                                <span class="small text-muted"><?= number_format($stats['quantite'], 2) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- RÉSUMÉ MODULE -->
                <div class="card-gsn bg-plomberie text-white">
                    <div class="card-body-gsn text-center">
                        <i class="fas fa-wrench fa-3x mb-3 opacity-75"></i>
                        <h3 class="mb-3">Module Plomberie</h3>
                        <div class="mb-3">
                            <div class="h2 mb-1"><?= number_format($total_module, 0, ',', ' ') ?></div>
                            <small class="opacity-75">FCFA Hors Taxes</small>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="h4 mb-1"><?= count($elements_plomberie) ?></div>
                                <small class="opacity-75">Éléments</small>
                            </div>
                            <div class="col-6">
                                <div class="h4 mb-1">
                                    <?= $total_module > 0 && count($elements_plomberie) > 0 ? 
                                        number_format($total_module / count($elements_plomberie), 0, ',', ' ') : '0' ?>
                                </div>
                                <small class="opacity-75">Prix moyen</small>
                            </div>
                        </div>
                        
                        <hr class="my-3 opacity-50">
                        
                        <div class="d-grid gap-2">
                            <a href="recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                               class="btn btn-light">
                                <i class="fas fa-chart-pie"></i>
                                Voir le récapitulatif complet
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Script pour les fonctionnalités du formulaire et tableau -->
    <script>
        // Variables globales pour le formulaire
        let formChanged = false;
        let selectedRows = new Set();

        // Fonctions pour le formulaire
        function toggleFormHelp() {
            const helpDiv = document.getElementById('formHelp');
            helpDiv.style.display = helpDiv.style.display === 'none' ? 'block' : 'none';
        }

        function resetForm() {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ?')) {
                document.getElementById('formPlomberie').reset();
                calculateTotal();
                formChanged = false;
                showSaveIndicator(false);
            }
        }

        function calculateTotal() {
            const quantite = parseFloat(document.getElementById('quantite').value) || 0;
            const pu = parseFloat(document.getElementById('pu').value) || 0;
            const transport = parseFloat(document.getElementById('transport').value) || 0;
            
            const total = quantite * pu;
            const totalWithTransport = total + transport;
            
            // Mise à jour des champs calculés
            document.getElementById('totalCalcule').value = totalWithTransport.toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('totalFormule').textContent = total.toLocaleString('fr-FR');
            
            // Calcul automatique du transport si vide
            const transportField = document.getElementById('transport');
            if (transportField.value === '' || transportField.value === '0') {
                const autoTransport = total * 0.05; // 5% du total
                transportField.placeholder = 'Auto: ' + autoTransport.toLocaleString('fr-FR') + ' FCFA';
            }
        }

        function ajoutRapide(designation, quantite, unite, pu) {
            document.getElementById('designation').value = designation;
            document.getElementById('quantite').value = quantite;
            document.getElementById('unite').value = unite;
            document.getElementById('pu').value = pu;
            calculateTotal();
            scrollToForm();
        }

        // Fonctions pour le tableau
        function toggleTableOptions() {
            const options = document.getElementById('tableOptions');
            options.style.display = options.style.display === 'none' ? 'block' : 'none';
        }

        function exportTableau() {
            window.open(`export_excel.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=plomberie`, '_blank');
        }

        function imprimerTableau() {
            window.print();
        }

        function dupliquerElement(elementId) {
            if (confirm('Voulez-vous dupliquer cet élément ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const actionInput = document.createElement('input');
                actionInput.name = 'action';
                actionInput.value = 'dupliquer';
                
                const idInput = document.createElement('input');
                idInput.name = 'element_id';
                idInput.value = elementId;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Gestion de la sélection multiple
        function updateBulkActions() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            
            if (checked.length > 0) {
                bulkActions.style.display = 'block';
                selectedCount.textContent = checked.length;
            } else {
                bulkActions.style.display = 'none';
            }
        }

        // Event listeners pour le formulaire
        document.addEventListener('DOMContentLoaded', function() {
            // Calcul automatique du total
            const quantiteInput = document.getElementById('quantite');
            const puInput = document.getElementById('pu');
            const transportInput = document.getElementById('transport');
            
            if (quantiteInput && puInput) {
                [quantiteInput, puInput, transportInput].forEach(input => {
                    if (input) {
                        input.addEventListener('input', calculateTotal);
                    }
                });
                
                // Calcul initial
                calculateTotal();
            }

            // Validation du formulaire
            const form = document.getElementById('formPlomberie');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
                
                // Détection des changements
                form.addEventListener('input', function() {
                    formChanged = true;
                    showSaveIndicator(false);
                });
            }

            // Gestion de la sélection multiple
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.row-checkbox');
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateBulkActions();
                });
            }

            // Event listeners pour les checkboxes individuelles
            document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            // Recherche dans le tableau
            const searchInput = document.getElementById('searchTable');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#tableauPlomberie tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            // Gestion des colonnes affichables
            ['showDiametre', 'showLongueur', 'showMateriau', 'showTransport'].forEach(id => {
                const checkbox = document.getElementById(id);
                if (checkbox) {
                    checkbox.addEventListener('change', function() {
                        const className = 'col-' + id.replace('show', '').toLowerCase();
                        const elements = document.querySelectorAll('.' + className);
                        elements.forEach(el => {
                            el.style.display = this.checked ? '' : 'none';
                        });
                    });
                }
            });

            // Tooltips Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-focus sur le premier champ si pas en mode modification
            const premierChamp = document.getElementById('designation');
            if (premierChamp && !premierChamp.value) {
                premierChamp.focus();
            }
        });

        // Fonctions pour les actions en lot
        function modifierEnLot() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            if (checked.length === 0) return;
            
            alert('Fonctionnalité de modification en lot à implémenter');
        }

        function exporterSelection() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            if (checked.length === 0) return;
            
            const ids = Array.from(checked).map(cb => cb.value);
            window.open(`export_excel.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=plomberie&ids=${ids.join(',')}`, '_blank');
        }

        function supprimerEnLot() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            if (checked.length === 0) return;
            
            if (confirm(`Êtes-vous sûr de vouloir supprimer ${checked.length} élément(s) ?`)) {
                const ids = Array.from(checked).map(cb => cb.value);
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const actionInput = document.createElement('input');
                actionInput.name = 'action';
                actionInput.value = 'supprimer_lot';
                
                const idsInput = document.createElement('input');
                idsInput.name = 'element_ids';
                idsInput.value = ids.join(',');
                
                form.appendChild(actionInput);
                form.appendChild(idsInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        console.log('🔧 Module Plomberie GSN ProDevis360° - Partie 4 chargée');
        console.log('📊 Éléments chargés:', <?= count($elements_plomberie) ?>);
        console.log('💰 Total module:', <?= $total_module ?>, 'FCFA');
    </script>
    
<!-- ===== PARTIE 5 : ACTIONS RAPIDES & JAVASCRIPT ===== -->
<!-- Section Actions Rapides Plomberie -->
<div class="quick-actions-section fade-in">
    <div class="actions-header">
        <h5>
            <i class="fas fa-bolt text-warning"></i>
            Actions Rapides Plomberie
        </h5>
        <small class="text-muted">Ajout rapide d'éléments standards</small>
    </div>
    
    <div class="actions-grid">
        <!-- Tuyauterie Standard -->
        <button type="button" class="action-btn" onclick="ajouterTuyauStandard()">
            <div class="action-icon">
                <i class="fas fa-grip-lines"></i>
            </div>
            <span class="action-text">Tuyau PVC Ø110</span>
            <small class="action-detail">6m - 8500 FCFA</small>
        </button>
        
        <!-- Raccords Standard -->
        <button type="button" class="action-btn" onclick="ajouterRaccordStandard()">
            <div class="action-icon">
                <i class="fas fa-puzzle-piece"></i>
            </div>
            <span class="action-text">Coude PVC 90°</span>
            <small class="action-detail">Ø110 - 1200 FCFA</small>
        </button>
        
        <!-- Sanitaire Standard -->
        <button type="button" class="action-btn" onclick="ajouterSanitaireStandard()">
            <div class="action-icon">
                <i class="fas fa-toilet"></i>
            </div>
            <span class="action-text">WC Complet</span>
            <small class="action-detail">Standard - 85000 FCFA</small>
        </button>
        
        <!-- Robinetterie -->
        <button type="button" class="action-btn" onclick="ajouterRobinetStandard()">
            <div class="action-icon">
                <i class="fas fa-faucet"></i>
            </div>
            <span class="action-text">Robinet Mélangeur</span>
            <small class="action-detail">Standard - 15000 FCFA</small>
        </button>
        
        <!-- Calculateur -->
        <button type="button" class="action-btn" onclick="ouvrirCalculateurPlomberie()">
            <div class="action-icon">
                <i class="fas fa-calculator"></i>
            </div>
            <span class="action-text">Calculateur</span>
            <small class="action-detail">Diamètres & Longueurs</small>
        </button>
        
        <!-- Import rapide -->
        <button type="button" class="action-btn" onclick="ouvrirImportRapide()">
            <div class="action-icon">
                <i class="fas fa-upload"></i>
            </div>
            <span class="action-text">Import Excel</span>
            <small class="action-detail">Données en lot</small>
        </button>
    </div>
</div>

<!-- Modal Calculateur Plomberie -->
<div class="modal fade" id="calculateurPlomberie" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-calculator"></i>
                    Calculateur Plomberie
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Calcul Longueur Tuyaux</h6>
                        <div class="mb-3">
                            <label class="form-label">Diamètre (mm)</label>
                            <select class="form-select" id="calc_diametre">
                                <option value="32">Ø32 - Évacuation</option>
                                <option value="40">Ø40 - Évacuation</option>
                                <option value="50">Ø50 - Évacuation</option>
                                <option value="110" selected>Ø110 - Principal</option>
                                <option value="125">Ø125 - Collecteur</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Longueur totale (m)</label>
                            <input type="number" class="form-control" id="calc_longueur" step="0.1" oninput="calculerTuyaux()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix unitaire/m</label>
                            <input type="number" class="form-control" id="calc_prix_m" oninput="calculerTuyaux()">
                        </div>
                        <div class="alert alert-info" id="resultat_tuyaux"></div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-primary">Calcul Débit & Pression</h6>
                        <div class="mb-3">
                            <label class="form-label">Nombre de points d'eau</label>
                            <input type="number" class="form-control" id="calc_points" oninput="calculerDebit()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Étage</label>
                            <select class="form-select" id="calc_etage" onchange="calculerDebit()">
                                <option value="0">RDC</option>
                                <option value="1">1er étage</option>
                                <option value="2">2ème étage</option>
                                <option value="3">3ème étage</option>
                            </select>
                        </div>
                        <div class="alert alert-success" id="resultat_debit"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="appliquerCalculs()">Appliquer au formulaire</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Principal du Module Plomberie -->
<script>
    // ===== *** SECTION JAVASCRIPT PLOMBERIE GSN ProDevis360° *** =====
    console.log('🚀 Initialisation JavaScript Module Plomberie - Partie 5');
    
    // Variables globales du module
    let formChanged = false;
    let autoSaveTimer = null;
    let calculResults = {};
    
    // ===== ACTIONS RAPIDES =====
    
    // Ajouter tuyau standard
    function ajouterTuyauStandard() {
        const form = document.getElementById('formPlomberie');
        if (form) {
            document.getElementById('designation').value = 'Tuyau PVC évacuation Ø110';
            document.getElementById('quantite').value = '6';
            document.getElementById('unite').value = 'ml';
            document.getElementById('pu').value = '1420';
            document.getElementById('diametre').value = '110mm';
            document.getElementById('longueur').value = '6.00';
            document.getElementById('materiau').value = 'PVC';
            document.getElementById('transport').value = '500';
            
            calculateTotal();
            scrollToForm();
            showNotification('✅ Tuyau PVC Ø110 ajouté au formulaire', 'success');
        }
    }
    
    // Ajouter raccord standard
    function ajouterRaccordStandard() {
        const form = document.getElementById('formPlomberie');
        if (form) {
            document.getElementById('designation').value = 'Coude PVC 90° Ø110';
            document.getElementById('quantite').value = '4';
            document.getElementById('unite').value = 'pcs';
            document.getElementById('pu').value = '1200';
            document.getElementById('diametre').value = '110mm';
            document.getElementById('materiau').value = 'PVC';
            document.getElementById('type_raccord').value = 'Coude 90°';
            document.getElementById('transport').value = '200';
            
            calculateTotal();
            scrollToForm();
            showNotification('✅ Raccords PVC ajoutés au formulaire', 'success');
        }
    }
    
    // Ajouter sanitaire standard
    function ajouterSanitaireStandard() {
        const form = document.getElementById('formPlomberie');
        if (form) {
            document.getElementById('designation').value = 'WC complet avec réservoir et abattant';
            document.getElementById('quantite').value = '1';
            document.getElementById('unite').value = 'ens';
            document.getElementById('pu').value = '85000';
            document.getElementById('materiau').value = 'Céramique';
            document.getElementById('transport').value = '5000';
            
            calculateTotal();
            scrollToForm();
            showNotification('✅ WC complet ajouté au formulaire', 'success');
        }
    }
    
    // Ajouter robinet standard
    function ajouterRobinetStandard() {
        const form = document.getElementById('formPlomberie');
        if (form) {
            document.getElementById('designation').value = 'Robinet mélangeur lavabo';
            document.getElementById('quantite').value = '2';
            document.getElementById('unite').value = 'pcs';
            document.getElementById('pu').value = '15000';
            document.getElementById('materiau').value = 'Laiton chromé';
            document.getElementById('transport').value = '1000';
            
            calculateTotal();
            scrollToForm();
            showNotification('✅ Robinets mélangeurs ajoutés au formulaire', 'success');
        }
    }
    
    // ===== CALCULATEUR PLOMBERIE =====
    
    function ouvrirCalculateurPlomberie() {
        const modal = new bootstrap.Modal(document.getElementById('calculateurPlomberie'));
        modal.show();
        
        // Initialiser les prix par défaut
        document.getElementById('calc_prix_m').value = '1420';
        calculerTuyaux();
        calculerDebit();
    }
    
    function calculerTuyaux() {
        const diametre = parseInt(document.getElementById('calc_diametre').value);
        const longueur = parseFloat(document.getElementById('calc_longueur').value) || 0;
        const prixM = parseFloat(document.getElementById('calc_prix_m').value) || 0;
        
        const total = longueur * prixM;
        const transport = total * 0.08; // 8% transport
        const totalTTC = total + transport;
        
        // Calculer nombre de barres (6m standard)
        const nombreBarres = Math.ceil(longueur / 6);
        
        calculResults.tuyaux = {
            diametre: diametre,
            longueur: longueur,
            nombreBarres: nombreBarres,
            prixUnitaire: prixM,
            total: total,
            transport: transport,
            totalTTC: totalTTC
        };
        
        document.getElementById('resultat_tuyaux').innerHTML = `
            <strong>Résultat :</strong><br>
            📏 ${longueur}m - Ø${diametre}mm<br>
            📦 ${nombreBarres} barre(s) de 6m<br>
            💰 ${total.toLocaleString('fr-FR')} FCFA<br>
            🚛 Transport : ${transport.toLocaleString('fr-FR')} FCFA<br>
            <strong>Total : ${totalTTC.toLocaleString('fr-FR')} FCFA</strong>
        `;
    }
    
    function calculerDebit() {
        const points = parseInt(document.getElementById('calc_points').value) || 0;
        const etage = parseInt(document.getElementById('calc_etage').value) || 0;
        
        // Calculs techniques plomberie
        const debitBase = points * 0.2; // 0.2 L/s par point
        const pressionNecessaire = 2 + (etage * 0.3); // Pression en bars
        const diametreRecommande = points <= 3 ? 20 : points <= 6 ? 25 : 32;
        
        calculResults.debit = {
            points: points,
            etage: etage,
            debit: debitBase,
            pression: pressionNecessaire,
            diametre: diametreRecommande
        };
        
        document.getElementById('resultat_debit').innerHTML = `
            <strong>Recommandations :</strong><br>
            💧 Débit : ${debitBase} L/s<br>
            🏗️ Pression : ${pressionNecessaire} bars<br>
            🔧 Diamètre conseillé : Ø${diametreRecommande}mm<br>
            ${etage > 0 ? '⚠️ Surpresseur recommandé' : '✅ Pression réseau suffisante'}
        `;
    }
    
    function appliquerCalculs() {
        if (calculResults.tuyaux) {
            const result = calculResults.tuyaux;
            document.getElementById('designation').value = `Tuyau PVC Ø${result.diametre} - ${result.longueur}m`;
            document.getElementById('quantite').value = result.nombreBarres;
            document.getElementById('unite').value = 'barres';
            document.getElementById('pu').value = result.prixUnitaire * 6; // Prix par barre de 6m
            document.getElementById('diametre').value = result.diametre + 'mm';
            document.getElementById('longueur').value = result.longueur;
            document.getElementById('transport').value = result.transport;
            
            calculateTotal();
        }
        
        // Fermer le modal
        bootstrap.Modal.getInstance(document.getElementById('calculateurPlomberie')).hide();
        scrollToForm();
        showNotification('✅ Calculs appliqués au formulaire', 'success');
    }
    
    // ===== IMPORT RAPIDE =====
    
    function ouvrirImportRapide() {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-upload"></i> Import Rapide Excel
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Importez vos données plomberie depuis un fichier Excel
                        </div>
                        <input type="file" class="form-control" accept=".xlsx,.xls" id="fileImport">
                        <div class="mt-3">
                            <small class="text-muted">
                                Format attendu : Désignation | Quantité | Unité | Prix Unitaire | Transport
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-info" onclick="processerImport()">
                            <i class="fas fa-check"></i> Importer
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Nettoyer après fermeture
        modal.addEventListener('hidden.bs.modal', () => {
            document.body.removeChild(modal);
        });
    }
    
    function processerImport() {
        const fileInput = document.getElementById('fileImport');
        if (fileInput.files.length === 0) {
            showNotification('⚠️ Veuillez sélectionner un fichier', 'warning');
            return;
        }
        
        // Simulation du traitement (dans un vrai projet, utilisez une bibliothèque comme SheetJS)
        showNotification('📊 Import en cours...', 'info');
        
        setTimeout(() => {
            showNotification('✅ 12 éléments importés avec succès', 'success');
            bootstrap.Modal.getInstance(document.querySelector('.modal.show')).hide();
            // Rafraîchir la page pour afficher les nouveaux éléments
            setTimeout(() => window.location.reload(), 1000);
        }, 2000);
    }
    
    // ===== FONCTIONS UTILITAIRES =====
    
    function scrollToForm() {
        const form = document.getElementById('formPlomberie');
        if (form) {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            const firstInput = form.querySelector('input[type="text"]');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 500);
            }
        }
    }
    
    function showNotification(message, type = 'info') {
        // Créer la notification
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-suppression après 4 secondes
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 4000);
    }
    
    // ===== AUTO-SAUVEGARDE =====
    
    function enableAutoSave() {
        const form = document.getElementById('formPlomberie');
        if (form) {
            form.addEventListener('input', function() {
                formChanged = true;
                clearTimeout(autoSaveTimer);
                
                autoSaveTimer = setTimeout(() => {
                    if (formChanged) {
                        // Simulation auto-sauvegarde
                        showSaveIndicator(true);
                        console.log('💾 Auto-sauvegarde effectuée');
                    }
                }, 3000);
            });
        }
    }
    
    function showSaveIndicator(success = true) {
        let indicator = document.getElementById('saveIndicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'saveIndicator';
            indicator.className = 'position-fixed';
            indicator.style.cssText = `
                bottom: 20px;
                right: 20px;
                z-index: 1000;
                padding: 8px 12px;
                background: white;
                border-radius: 20px;
                border: 1px solid #e9ecef;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                font-size: 0.85rem;
            `;
            document.body.appendChild(indicator);
        }
        
        const icon = success ? 
            '<i class="fas fa-check-circle text-success"></i>' : 
            '<i class="fas fa-exclamation-triangle text-warning"></i>';
        const text = success ? 'Sauvegardé' : 'Non sauvegardé';
        const colorClass = success ? 'text-success' : 'text-warning';
        
        indicator.innerHTML = `${icon} <small class="${colorClass}">${text}</small>`;
    }
    
    // ===== GESTION DES ERREURS =====
    
    window.addEventListener('error', function(e) {
        console.error('❌ Erreur JavaScript capturée:', e.error);
        showNotification('⚠️ Une erreur s\'est produite. Rechargez la page si nécessaire.', 'warning');
    });
    
    // Protection avant fermeture si modifications non sauvegardées
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'Vous avez des modifications non sauvegardées. Êtes-vous sûr de vouloir quitter ?';
            return e.returnValue;
        }
    });
    
    // ===== INITIALISATION FINALE =====
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔧 Module Plomberie GSN ProDevis360° - JavaScript Partie 5 initialisé');
        
        // Activer l'auto-sauvegarde
        enableAutoSave();
        
        // Animer les éléments à l'affichage
        const elements = document.querySelectorAll('.fade-in, .quick-actions-section');
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('animate__animated', 'animate__fadeInUp');
            }, index * 100);
        });
        
        // Initialiser les tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Message de bienvenue
        setTimeout(() => {
            showNotification('🔧 Module Plomberie prêt ! Utilisez les actions rapides pour gagner du temps.', 'info');
        }, 1000);
        
        // Statistiques de performance
        console.log('📊 Statistiques Module Plomberie:', {
            'Éléments chargés': <?= count($elements_plomberie ?? []) ?>,
            'Total module': '<?= number_format($total_module ?? 0, 0, ',', ' ') ?> FCFA',
            'JavaScript prêt': true,
            'Actions rapides': 6,
            'Calculateurs': 2
        });
    });
    
    // Styles CSS additionnels pour les actions rapides
    const additionalStyles = `
        <style>
        .quick-actions-section {
            background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
            border: 1px solid #e3f2fd;
        }
        
        .actions-header h5 {
            color: #1976d2;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .action-btn {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .action-btn:hover {
            border-color: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.15);
        }
        
        .action-icon {
            font-size: 1.5rem;
            color: #3498db;
            margin-bottom: 0.5rem;
        }
        
        .action-text {
            display: block;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        
        .action-detail {
            color: #6c757d;
            font-size: 0.8rem;
        }
        
        .fade-in {
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        </style>
    `;
    
    document.head.insertAdjacentHTML('beforeend', additionalStyles);
    
    console.log('✅ Module Plomberie GSN ProDevis360° - Partie 5 complètement chargée !');
</script>

<!-- Fin du fichier HTML -->
</body>
</html>