<?php
// ===== FERRAILLAGE.PHP - PARTIE 1 : PHP LOGIC & CONFIG =====
// VERSION UNIFORMISÉE GSN ProDevis360°
require_once 'functions.php';

// Configuration du module actuel
$current_module = 'ferraillage';

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

// Navigation dynamique
$navigation = getNavigationModules($modules_config, $current_module);

// Récupération des informations du projet et devis
$projet_devis_info = getProjetDevisInfo($projet_id, $devis_id);
if (!$projet_devis_info) {
    die('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Erreur : Projet ou devis introuvable.</div>');
}

// Variables d'affichage
$message = '';
$message_type = '';

// Suggestions spécialisées pour le ferraillage
$suggestions_ferraillage = [
    // FERS À BÉTON LISSES
    'Fer rond lisse Ø6mm longueur 6m',
    'Fer rond lisse Ø8mm longueur 6m',
    'Fer rond lisse Ø10mm longueur 6m',
    'Fer rond lisse Ø12mm longueur 6m',
    'Fer rond lisse Ø14mm longueur 6m',
    'Fer rond lisse Ø16mm longueur 6m',
    'Fer rond lisse Ø20mm longueur 6m',
    'Fer rond lisse Ø25mm longueur 6m',
    
    // FERS À BÉTON HAUTE ADHÉRENCE (HA)
    'Fer HA Ø6mm (FeE500) longueur 6m',
    'Fer HA Ø8mm (FeE500) longueur 6m',
    'Fer HA Ø10mm (FeE500) longueur 6m',
    'Fer HA Ø12mm (FeE500) longueur 6m',
    'Fer HA Ø14mm (FeE500) longueur 6m',
    'Fer HA Ø16mm (FeE500) longueur 6m',
    'Fer HA Ø20mm (FeE500) longueur 6m',
    'Fer HA Ø25mm (FeE500) longueur 6m',
    'Fer HA Ø32mm (FeE500) longueur 12m',
    'Fer HA Ø40mm (FeE500) longueur 12m',
    
    // TREILLIS SOUDÉS
    'Treillis soudé ST25C maille 150x150mm',
    'Treillis soudé ST25C maille 200x200mm',
    'Treillis soudé ST35C maille 150x150mm',
    'Treillis soudé ST50C maille 150x150mm',
    'Treillis soudé PAF6 maille 100x100mm',
    'Treillis soudé PAF8 maille 150x150mm',
    'Treillis soudé ADETS maille 200x200mm',
    'Treillis électrosoudé 6x6mm/150x150',
    'Treillis électrosoudé 8x8mm/100x100',
    'Treillis de répartition Ø4mm/200x200',
    
    // ARMATURES PRÉFABRIQUÉES
    'Cadre préfabriqué poteau 20x20cm',
    'Cadre préfabriqué poteau 25x25cm',
    'Cadre préfabriqué poteau 30x30cm',
    'Étrier Ø6mm carré 15x15cm',
    'Étrier Ø6mm carré 20x20cm',
    'Étrier Ø6mm carré 25x25cm',
    'Étrier Ø8mm rectangulaire 20x30cm',
    'Étrier Ø8mm rectangulaire 25x40cm',
    'Épingle Ø6mm longueur 20cm',
    'Épingle Ø8mm longueur 25cm',
    'Épingle Ø10mm longueur 30cm',
    
    // ARMATURES SPÉCIALES
    'Armature en attente Ø12mm L=40cm',
    'Armature en attente Ø16mm L=50cm',
    'Crochet de levage Ø16mm charge 500kg',
    'Crochet de levage Ø20mm charge 1000kg',
    'Armature de continuité Ø14mm L=80cm',
    'Armature de chaînage Ø10mm L=6m',
    'Armature longitudinale poutre Ø16mm',
    'Armature transversale poutre Ø8mm',
    'Fer d\'angle Ø12mm coudé 90°',
    'Fer de répartition Ø8mm L=1m',
    
    // ACIERS SPÉCIAUX
    'Acier inoxydable Ø12mm grade 316L',
    'Acier galvanisé Ø10mm protection marine',
    'Acier fibré haute résistance Ø16mm',
    'Acier précontraint T15 Ø12.5mm',
    'Acier précontraint T15 Ø15.2mm',
    'Barre d\'armature epoxy Ø16mm',
    'Barre composite GFRP Ø12mm',
    'Barre composite CFRP Ø10mm',
    
    // CONNECTEURS ET FIXATIONS
    'Connecteur plaque base Ø16mm L=30cm',
    'Connecteur goujonnage Ø19mm L=15cm',
    'Ancrage chimique Ø12mm L=20cm',
    'Ancrage mécanique Ø16mm expansion',
    'Douille filetée M12 scellement chimique',
    'Tige filetée Ø12mm classe 8.8 L=1m',
    'Tige filetée Ø16mm classe 8.8 L=1m',
    'Coupleur mécanique Ø16mm',
    'Coupleur mécanique Ø20mm',
    'Manchon de raccordement Ø25mm',
    
    // ACCESSOIRES FERRAILLAGE
    'Fil de ligature galvanisé Ø1.2mm rouleau 100m',
    'Fil de ligature plastifié Ø1.5mm rouleau 50m',
    'Cale béton plastique épaisseur 20mm',
    'Cale béton plastique épaisseur 25mm',
    'Cale béton plastique épaisseur 30mm',
    'Cale béton plastique épaisseur 40mm',
    'Distancier béton rond Ø20mm ep.25mm',
    'Distancier béton carré 25x25mm ep.30mm',
    'Support d\'armature réglable H=15cm',
    'Support d\'armature réglable H=20cm',
    
    // OUTILLAGE FERRAILLAGE
    'Cintreuse manuelle fer Ø6-16mm',
    'Cintreuse électrique fer Ø8-25mm',
    'Cisaille manuelle fer Ø6-12mm',
    'Cisaille électrique fer Ø8-20mm',
    'Pince à ligaturer automatique',
    'Pince à ligaturer manuelle',
    'Équerre de traçage ferrailleur 50cm',
    'Règle de ferrailleur graduée 2m',
    'Compas d\'épaisseur ferraillage',
    'Calibre de contrôle diamètre fers',
    
    // PROTECTION ET TRAITEMENT
    'Primaire antirouille fers à béton 5L',
    'Peinture de protection acier 5L',
    'Galvanisation à froid spray 400ml',
    'Inhibiteur de corrosion béton 25kg',
    'Résine époxy armatures 5kg',
    'Produit passivation acier 1L',
    'Revêtement zinc-aluminium 5L',
    'Protection cathodique anode zinc',
    
    // CONTRÔLE QUALITÉ
    'Essai traction fers à béton (laboratoire)',
    'Contrôle soudabilité treillis (expertise)',
    'Vérification géométrie armatures (métrologie)',
    'Test adhérence béton-acier (labo)',
    'Certificat conformité NF A35-080-1',
    'Attestation traçabilité sidérurgique',
    'Rapport contrôle dimensionnel armatures',
    'Analyse chimique acier (laboratoire)'
];

// Connexion à la base de données
$conn = getDbConnection();

// Gestion des actions CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if ($action == 'ajouter') {
            // Récupération et validation des données
            $designation = trim($_POST['designation'] ?? '');
            $quantite = floatval($_POST['quantite'] ?? 0);
            $unite = trim($_POST['unite'] ?? 'kg');
            $prix_unitaire = floatval($_POST['prix_unitaire'] ?? 0);
            $type_acier = trim($_POST['type_acier'] ?? '');
            $diametre = floatval($_POST['diametre'] ?? 0);
            $longueur = floatval($_POST['longueur'] ?? 0);
            $nuance_acier = trim($_POST['nuance_acier'] ?? '');
            $forme = trim($_POST['forme'] ?? '');
            $traitement_surface = trim($_POST['traitement_surface'] ?? '');
            $usage_structural = trim($_POST['usage_structural'] ?? '');
            $classe_exposition = trim($_POST['classe_exposition'] ?? '');
            $poids_lineique = floatval($_POST['poids_lineique'] ?? 0);
            $resistance_traction = floatval($_POST['resistance_traction'] ?? 0);
            $limite_elastique = floatval($_POST['limite_elastique'] ?? 0);
            $allongement = floatval($_POST['allongement'] ?? 0);
            
            // Validations spécifiques ferraillage
            if (empty($designation)) {
                throw new Exception("La désignation est obligatoire.");
            }
            if ($quantite <= 0) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }
            if ($prix_unitaire < 0) {
                throw new Exception("Le prix unitaire ne peut pas être négatif.");
            }
            
            // Validation diamètre
            if ($diametre < 0 || $diametre > 50) {
                throw new Exception("Le diamètre doit être entre 0 et 50 mm.");
            }
            
            // Validation longueur
            if ($longueur < 0 || $longueur > 20) {
                throw new Exception("La longueur doit être entre 0 et 20 mètres.");
            }
            
            // Validation résistance traction
            if ($resistance_traction < 0 || $resistance_traction > 1000) {
                throw new Exception("La résistance traction doit être entre 0 et 1000 MPa.");
            }
            
            // Validation limite élastique
            if ($limite_elastique < 0 || $limite_elastique > 800) {
                throw new Exception("La limite élastique doit être entre 0 et 800 MPa.");
            }
            
            // Validation allongement
            if ($allongement < 0 || $allongement > 50) {
                throw new Exception("L'allongement doit être entre 0 et 50%.");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Insertion en base
            $stmt = $conn->prepare("
                INSERT INTO ferraillage (
                    projet_id, devis_id, designation, quantite, unite, 
                    prix_unitaire, total, type_acier, diametre, longueur,
                    nuance_acier, forme, traitement_surface, usage_structural,
                    classe_exposition, poids_lineique, resistance_traction,
                    limite_elastique, allongement, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param(
                "iisdsdssddsssssdddd", 
                $projet_id, $devis_id, $designation, $quantite, $unite,
                $prix_unitaire, $total, $type_acier, $diametre, $longueur,
                $nuance_acier, $forme, $traitement_surface, $usage_structural,
                $classe_exposition, $poids_lineique, $resistance_traction,
                $limite_elastique, $allongement
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'ferraillage');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'ferraillage', 'Ajout', "Élément ajouté : {$designation}");
                
                $message = "Élément ferraillage ajouté avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de l'ajout : " . $conn->error);
            }
            
        } elseif ($action == 'modifier' && $element_id > 0) {
            // [Code de modification similaire avec toutes les validations]
            // ... (logique similaire à l'ajout pour la modification)
            
        } elseif ($action == 'supprimer' && $element_id > 0) {
            // Récupération de la désignation avant suppression
            $stmt_get = $conn->prepare("SELECT designation FROM ferraillage WHERE id = ? AND projet_id = ? AND devis_id = ?");
            $stmt_get->bind_param("iii", $element_id, $projet_id, $devis_id);
            $stmt_get->execute();
            $result_get = $stmt_get->get_result();
            $element_data = $result_get->fetch_assoc();
            
            if ($element_data) {
                // Suppression de l'élément
                $stmt = $conn->prepare("DELETE FROM ferraillage WHERE id = ? AND projet_id = ? AND devis_id = ?");
                $stmt->bind_param("iii", $element_id, $projet_id, $devis_id);
                
                if ($stmt->execute()) {
                    // Mise à jour du récapitulatif
                    updateRecapitulatif($projet_id, $devis_id, 'ferraillage');
                    
                    // Sauvegarde dans l'historique
                    sauvegarderHistorique($projet_id, $devis_id, 'ferraillage', 'Suppression', "Élément supprimé : {$element_data['designation']}");
                    
                    $message = "Élément ferraillage supprimé avec succès !";
                    $message_type = "success";
                } else {
                    throw new Exception("Erreur lors de la suppression : " . $conn->error);
                }
            } else {
                throw new Exception("Élément introuvable pour la suppression.");
            }
        }
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = "danger";
    }
}

// Récupération des éléments de ferraillage pour affichage
$elements_ferraillage = [];
$total_module = 0;

$stmt = $conn->prepare("
    SELECT id, designation, quantite, unite, prix_unitaire, total,
           type_acier, diametre, longueur, nuance_acier, forme,
           traitement_surface, usage_structural, classe_exposition,
           poids_lineique, resistance_traction, limite_elastique, allongement,
           DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation_fr,
           DATE_FORMAT(date_modification, '%d/%m/%Y %H:%i') as date_modification_fr
    FROM ferraillage 
    WHERE projet_id = ? AND devis_id = ? 
    ORDER BY date_creation DESC
");

$stmt->bind_param("ii", $projet_id, $devis_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $elements_ferraillage[] = $row;
    $total_module += $row['total'];
}

// Récupération de l'élément à modifier si nécessaire
$element_a_modifier = null;
if ($action == 'modifier' && $element_id > 0) {
    $stmt = $conn->prepare("
        SELECT * FROM ferraillage 
        WHERE id = ? AND projet_id = ? AND devis_id = ?
    ");
    $stmt->bind_param("iii", $element_id, $projet_id, $devis_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $element_a_modifier = $result->fetch_assoc();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferraillage - <?= htmlspecialchars($projet_devis_info['nom_projet']) ?> | GSN ProDevis360°</title>
    
    <!-- Font Awesome 6.5.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ===== VARIABLES CSS GSN ProDevis360° FERRAILLAGE ===== */
        :root {
            --primary-orange: #ff6b35;
            --primary-orange-light: #ff8c69;
            --primary-orange-dark: #e55a2b;
            --secondary-white: #ffffff;
            --accent-green: #28a745;
            --accent-red: #dc3545;
            --accent-blue: #007bff;
            --neutral-gray: #6c757d;
            --neutral-light: #f8f9fa;
            --neutral-dark: #343a40;
            --shadow-soft: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-medium: 0 4px 20px rgba(0,0,0,0.15);
            --border-radius: 8px;
            --transition-fast: 0.2s ease;
            --transition-smooth: 0.3s ease;
            
            /* Variables spécifiques ferraillage */
            --steel-primary: #34495e;
            --steel-light: #5d6d7e;
            --steel-dark: #2c3e50;
            --iron-rust: #b7472a;
            --iron-dark: #8b3626;
            --galvanized: #95a5a6;
            --inox-shine: #bdc3c7;
            --rebar-gray: #566573;
            --concrete-light: #d5dbdb;
            --concrete-dark: #85929e;
            --weld-blue: #3498db;
            --carbon-black: #212529;
            --zinc-silver: #aeb6bf;
            --copper-brown: #a0522d;
            --alloy-gold: #f39c12;
            --spark-orange: #ff7f50;
            --forge-red: #cd5c5c;
            --metal-shine: rgba(255,255,255,0.6);
        }

        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--neutral-light) 0%, #e9ecef 100%);
            color: var(--neutral-dark);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ===== HEADER GSN ===== */
        .header-gsn {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
            color: var(--secondary-white);
            padding: 1rem 0;
            box-shadow: var(--shadow-medium);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-gsn {
            width: 60px;
            height: 60px;
            background: var(--secondary-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary-orange);
            box-shadow: var(--shadow-soft);
        }

        .header-title {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .header-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .module-badge {
            background: var(--steel-primary);
            color: var(--secondary-white);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            position: relative;
            overflow: hidden;
        }

        .module-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, var(--metal-shine), transparent);
            transition: left 0.8s ease;
        }

        .module-badge:hover::before {
            left: 100%;
        }

        .project-info {
            font-size: 0.9rem;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .btn-header {
            background: rgba(255,255,255,0.2);
            color: var(--secondary-white);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition-fast);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-header:hover {
            background: rgba(255,255,255,0.3);
            color: var(--secondary-white);
            transform: translateY(-1px);
        }

        /* ===== NAVIGATION MODULES ===== */
        .navigation-modules {
            background: var(--secondary-white);
            border-bottom: 3px solid var(--primary-orange);
            box-shadow: var(--shadow-soft);
            position: sticky;
            top: 80px;
            z-index: 999;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .nav-modules {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 0.75rem 0;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-orange) var(--neutral-light);
        }

        .nav-modules::-webkit-scrollbar {
            height: 4px;
        }

        .nav-modules::-webkit-scrollbar-track {
            background: var(--neutral-light);
        }

        .nav-modules::-webkit-scrollbar-thumb {
            background: var(--primary-orange);
            border-radius: 2px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: var(--neutral-light);
            color: var(--neutral-gray);
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition-smooth);
            white-space: nowrap;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(52, 73, 94, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .nav-item:hover::before {
            left: 100%;
        }

        .nav-item:hover {
            background: rgba(255, 107, 53, 0.1);
            color: var(--primary-orange);
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }

        .nav-item.active {
            background: var(--primary-orange);
            color: var(--secondary-white);
            box-shadow: var(--shadow-medium);
            border-color: var(--primary-orange-dark);
        }

        .nav-item i {
            font-size: 1.1rem;
        }

        /* ===== CONTAINER PRINCIPAL ===== */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* ===== MESSAGES & ALERTES ===== */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            box-shadow: var(--shadow-soft);
            animation: slideInDown 0.4s ease-out;
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: currentColor;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 4px solid var(--accent-green);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 4px solid var(--accent-red);
        }

        .alert i {
            font-size: 1.25rem;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== FORMULAIRES ===== */
        .form-section {
            background: var(--secondary-white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
            margin-bottom: 2rem;
            border-top: 4px solid var(--primary-orange);
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect x="10" y="10" width="80" height="5" fill="%2334495e" opacity="0.1"/><rect x="10" y="20" width="80" height="5" fill="%2334495e" opacity="0.08"/><rect x="10" y="30" width="80" height="5" fill="%2334495e" opacity="0.06"/><circle cx="50" cy="50" r="3" fill="%2334495e" opacity="0.15"/></svg>') repeat;
            pointer-events: none;
        }

        .form-section h2 {
            color: var(--primary-orange);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            position: relative;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 500;
            color: var(--neutral-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .required {
            color: var(--accent-red);
        }

        .form-group input,
        .form-group select {
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition-fast);
            background: var(--secondary-white);
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .form-group input[type="number"] {
            text-align: right;
        }

        /* ===== SUGGESTIONS FERRAILLAGE ===== */
        .suggestions-ferraillage {
            background: linear-gradient(135deg, var(--steel-primary) 0%, var(--steel-dark) 100%);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .suggestions-ferraillage::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect x="20" y="20" width="60" height="3" fill="white" opacity="0.1"/><rect x="20" y="30" width="60" height="3" fill="white" opacity="0.08"/><rect x="20" y="40" width="60" height="3" fill="white" opacity="0.06"/><rect x="20" y="50" width="60" height="3" fill="white" opacity="0.1"/><rect x="20" y="60" width="60" height="3" fill="white" opacity="0.08"/></svg>') repeat;
            pointer-events: none;
        }

        .suggestions-ferraillage h4 {
            color: var(--secondary-white);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .suggestion-item {
            background: rgba(255,255,255,0.15);
            color: var(--secondary-white);
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition-fast);
            font-size: 0.85rem;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.2);
            position: relative;
            overflow: hidden;
        }

        .suggestion-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, var(--metal-shine), transparent);
            transition: left 0.4s ease;
        }

        .suggestion-item:hover::before {
            left: 100%;
        }

        .suggestion-item:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* ===== CALCULATEUR FERRAILLAGE ===== */
        .calculator-section {
            background: linear-gradient(135deg, var(--iron-rust) 0%, var(--forge-red) 100%);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            color: var(--secondary-white);
            position: relative;
            overflow: hidden;
        }

        .calculator-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60"><circle cx="15" cy="15" r="2" fill="white" opacity="0.3"/><circle cx="45" cy="15" r="1.5" fill="white" opacity="0.2"/><circle cx="30" cy="30" r="2.5" fill="white" opacity="0.25"/><circle cx="15" cy="45" r="1" fill="white" opacity="0.2"/><circle cx="45" cy="45" r="2" fill="white" opacity="0.3"/></svg>') repeat;
            pointer-events: none;
        }

        .calculator-section h4 {
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .calc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .calc-input {
            padding: 0.5rem;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 4px;
            background: rgba(255,255,255,0.9);
            font-size: 0.9rem;
            color: var(--neutral-dark);
        }

        /* ===== BOUTONS ===== */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            text-align: center;
            box-shadow: var(--shadow-soft);
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
            background: linear-gradient(90deg, transparent, var(--metal-shine), transparent);
            transition: left 0.4s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
            color: var(--secondary-white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--accent-green) 0%, #218838 100%);
            color: var(--secondary-white);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--accent-red) 0%, #c82333 100%);
            color: var(--secondary-white);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: var(--neutral-dark);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--neutral-gray) 0%, #5a6268 100%);
            color: var(--secondary-white);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--accent-blue) 0%, #0056b3 100%);
            color: var(--secondary-white);
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }

        /* ===== TABLEAUX ===== */
        .table-container {
            background: var(--secondary-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
            overflow: hidden;
            margin-bottom: 2rem;
            position: relative;
        }

        .table-container::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 100px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect x="10" y="10" width="80" height="3" fill="%2334495e" opacity="0.05"/><rect x="10" y="20" width="80" height="3" fill="%2334495e" opacity="0.03"/><rect x="10" y="30" width="80" height="3" fill="%2334495e" opacity="0.05"/></svg>') repeat;
            pointer-events: none;
            z-index: 0;
        }

        .table-header {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
            color: var(--secondary-white);
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .table-header h3 {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: var(--secondary-white);
            position: relative;
            z-index: 1;
        }

        .table th {
            background: var(--neutral-light);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--neutral-dark);
            border-bottom: 2px solid var(--primary-orange);
            white-space: nowrap;
            position: relative;
        }

        .table th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--steel-primary), var(--primary-orange));
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: var(--transition-fast);
        }

        .table tbody tr:hover {
            background: rgba(52, 73, 94, 0.05);
        }

        .actions-cell {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        /* ===== BADGES SPÉCIALISÉS FERRAILLAGE ===== */
        .badge-acier {
            background: var(--steel-primary);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }

        .badge-acier::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, var(--metal-shine), transparent);
            transition: left 0.3s ease;
        }

        .badge-acier:hover::before {
            left: 100%;
        }

        .badge-diametre {
            background: var(--rebar-gray);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-nuance {
            background: var(--alloy-gold);
            color: var(--neutral-dark);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-traitement {
            background: var(--galvanized);
            color: var(--neutral-dark);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-forme {
            background: var(--weld-blue);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-usage {
            background: var(--iron-rust);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-exposition {
            background: var(--concrete-dark);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* ===== TOTAUX MODULE ===== */
        .module-summary {
            background: linear-gradient(135deg, var(--neutral-dark) 0%, #495057 100%);
            color: var(--secondary-white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .module-summary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect x="20" y="20" width="60" height="2" fill="white" opacity="0.05"/><rect x="20" y="30" width="60" height="2" fill="white" opacity="0.03"/><rect x="20" y="40" width="60" height="2" fill="white" opacity="0.05"/></svg>') repeat;
            pointer-events: none;
        }

        .module-summary h3 {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .total-amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--steel-light);
            position: relative;
            z-index: 1;
        }

        /* ===== CARTES D'ACTIONS ===== */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -0.75rem;
        }

        .col-md-3 {
            flex: 0 0 25%;
            max-width: 25%;
            padding: 0 0.75rem;
        }

        .card {
            background: var(--secondary-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            transition: var(--transition-smooth);
            height: 100%;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"><rect x="5" y="5" width="40" height="2" fill="%2334495e" opacity="0.03"/><rect x="5" y="10" width="40" height="2" fill="%2334495e" opacity="0.02"/></svg>') repeat;
            pointer-events: none;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .card:hover::before {
            opacity: 0.7;
        }

        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--neutral-dark);
        }

        .card-text {
            flex-grow: 1;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        /* ===== EFFETS MÉTALLIQUES AVANCÉS ===== */
        .steel-effect {
            background: linear-gradient(135deg, 
                var(--steel-primary) 0%, 
                var(--steel-light) 50%, 
                var(--steel-primary) 100%);
            position: relative;
        }

        .steel-effect::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                var(--metal-shine), 
                transparent);
            animation: steelShine 3s ease-in-out infinite;
        }

        @keyframes steelShine {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }

        .rebar-texture {
            background: linear-gradient(135deg, 
                var(--rebar-gray) 0%, 
                var(--steel-primary) 50%, 
                var(--rebar-gray) 100%);
            position: relative;
        }

        .rebar-texture::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255,255,255,0.5), 
                transparent);
            animation: rebarShine 4s ease-in-out infinite;
        }

        @keyframes rebarShine {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }

        .weld-spark {
            position: relative;
            animation: weldSparks 2s ease-in-out infinite;
        }

        @keyframes weldSparks {
            0%, 100% { 
                box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
            }
            25% { 
                box-shadow: 0 0 15px rgba(52, 152, 219, 0.6), 0 0 25px var(--spark-orange);
            }
            50% { 
                box-shadow: 0 0 20px rgba(52, 152, 219, 0.8), 0 0 35px var(--spark-orange), 0 0 45px rgba(255, 255, 255, 0.4);
            }
            75% { 
                box-shadow: 0 0 15px rgba(52, 152, 219, 0.6), 0 0 25px var(--spark-orange);
            }
        }

        .iron-texture {
            background-image: 
                radial-gradient(circle at 25% 25%, var(--iron-rust) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, var(--iron-dark) 1px, transparent 1px);
            background-size: 20px 20px, 15px 15px;
        }

        .galvanized-coating {
            background: linear-gradient(135deg, 
                var(--galvanized) 0%, 
                var(--zinc-silver) 50%, 
                var(--galvanized) 100%);
            position: relative;
        }

        .galvanized-coating::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                transparent 25%, 
                rgba(255, 255, 255, 0.1) 25%, 
                rgba(255, 255, 255, 0.1) 50%, 
                transparent 50%, 
                transparent 75%, 
                rgba(255, 255, 255, 0.1) 75%);
            background-size: 4px 4px;
            animation: galvanizedPattern 3s linear infinite;
        }

        @keyframes galvanizedPattern {
            0% { transform: translateX(0); }
            100% { transform: translateX(4px); }
        }

        /* ===== PATTERN ACIER BACKGROUND ===== */
        .steel-pattern-bg {
            background-image: 
                repeating-linear-gradient(0deg, 
                    var(--steel-primary) 0px, 
                    var(--steel-primary) 2px, 
                    var(--steel-light) 2px, 
                    var(--steel-light) 4px),
                repeating-linear-gradient(90deg, 
                    var(--steel-primary) 0px, 
                    var(--steel-primary) 2px, 
                    var(--steel-light) 2px, 
                    var(--steel-light) 4px);
            background-size: 20px 20px;
        }

        /* ===== INDICATEURS TECHNIQUES FERRAILLAGE ===== */
        .tech-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: rgba(52, 73, 94, 0.1);
            color: var(--steel-dark);
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            margin: 0.1rem;
        }

        .tech-indicator.high-strength {
            background: rgba(183, 71, 42, 0.1);
            color: var(--iron-rust);
        }

        .tech-indicator.galvanized {
            background: rgba(149, 165, 166, 0.2);
            color: var(--galvanized);
        }

        .tech-indicator.stainless {
            background: rgba(189, 195, 199, 0.2);
            color: var(--inox-shine);
        }

        /* ===== DESIGN RESPONSIVE ===== */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .suggestions-grid {
                grid-template-columns: 1fr;
            }

            .calc-grid {
                grid-template-columns: 1fr;
            }

            .nav-modules {
                gap: 0.25rem;
            }

            .nav-item {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }

            .container {
                padding: 1rem 0.5rem;
            }

            .table th,
            .table td {
                padding: 0.5rem;
                font-size: 0.85rem;
            }

            .actions-cell {
                flex-direction: column;
            }

            .col-md-3 {
                flex: 0 0 50%;
                max-width: 50%;
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 480px) {
            .logo-gsn {
                width: 50px;
                height: 50px;
                font-size: 1rem;
            }

            .header-title h1 {
                font-size: 1.25rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .form-section {
                padding: 1rem;
            }

            .col-md-3 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        /* ===== ANIMATIONS AVANCÉES FERRAILLAGE ===== */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
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

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes steelForge {
            0% { 
                transform: rotateX(0deg) rotateY(0deg); 
                background-color: var(--steel-primary);
            }
            50% { 
                transform: rotateX(180deg) rotateY(0deg); 
                background-color: var(--iron-rust);
            }
            100% { 
                transform: rotateX(0deg) rotateY(0deg); 
                background-color: var(--steel-primary);
            }
        }

        .steel-forge {
            animation: steelForge 1.2s ease-in-out;
        }

        @keyframes rebarBend {
            0% { transform: rotate(0deg) scaleX(1); }
            25% { transform: rotate(5deg) scaleX(0.95); }
            75% { transform: rotate(-5deg) scaleX(1.05); }
            100% { transform: rotate(0deg) scaleX(1); }
        }

        .rebar-bend {
            animation: rebarBend 0.8s ease-out;
        }

        @keyframes hammerStrike {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-3px) scale(1.05); }
        }

        .hammer-strike {
            animation: hammerStrike 0.6s ease-in-out;
        }

        @keyframes sparksFly {
            0% { opacity: 1; transform: scale(1) rotate(0deg); }
            25% { opacity: 0.8; transform: scale(1.2) rotate(90deg); }
            50% { opacity: 0.6; transform: scale(1.4) rotate(180deg); }
            75% { opacity: 0.4; transform: scale(1.6) rotate(270deg); }
            100% { opacity: 0; transform: scale(2) rotate(360deg); }
        }

        .sparks-fly {
            position: relative;
        }

        .sparks-fly::after {
            content: '✨';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: sparksFly 1s ease-out;
        }

        /* ===== STYLES D'IMPRESSION ===== */
        @media print {
            .header-gsn,
            .navigation-modules,
            .form-section,
            .btn,
            .actions-cell,
            .suggestions-ferraillage,
            .calculator-section {
                display: none !important;
            }

            body {
                background: white;
                color: black;
            }

            .table-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .table th {
                background: #f5f5f5 !important;
                color: black !important;
            }

            .badge-acier,
            .badge-diametre,
            .badge-nuance,
            .badge-traitement,
            .badge-forme,
            .badge-usage,
            .badge-exposition {
                background: #ddd !important;
                color: black !important;
                border: 1px solid #999;
            }
        }

        /* ===== STYLES UTILITAIRES ===== */
        .text-center { text-align: center; }
        .text-muted { color: var(--neutral-gray); }
        .text-success { color: var(--accent-green); }
        .text-primary { color: var(--primary-orange); }
        .text-info { color: var(--accent-blue); }
        .text-steel { color: var(--steel-primary); }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .ml-1 { margin-left: 0.25rem; }
        .ml-2 { margin-left: 0.5rem; }
        .mx-2 { margin-left: 0.5rem; margin-right: 0.5rem; }
        .py-4 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
        .d-block { display: block; }
        .d-inline-block { display: inline-block; }
        .fa-3x { font-size: 3rem; }

        /* ===== CURSEURS SPÉCIALISÉS ===== */
        .suggestion-item {
            cursor: pointer;
        }

        .suggestion-item:hover {
            cursor: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><rect width="15" height="3" fill="%23ffffff" stroke="%2334495e" stroke-width="1"/></svg>') 10 10, pointer;
        }

        .calc-input:focus {
            cursor: text;
        }

        /* ===== SCROLLBAR PERSONNALISÉE ===== */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: var(--concrete-light);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--steel-primary);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: var(--steel-dark);
        }

        /* ===== LOADER SPÉCIALISÉ FERRAILLAGE ===== */
        .loading-steel {
            display: inline-block;
            width: 20px;
            height: 20px;
            background: var(--steel-primary);
            animation: steelLoader 1.5s ease-in-out infinite;
        }

        @keyframes steelLoader {
            0%, 100% {
                transform: scaleY(1) rotate(0deg);
                border-radius: 0;
            }
            25% {
                transform: scaleY(1.5) rotate(45deg);
                border-radius: 50%;
            }
            50% {
                transform: scaleY(1) rotate(90deg);
                border-radius: 0;
            }
            75% {
                transform: scaleY(0.5) rotate(135deg);
                border-radius: 50%;
            }
        }

        /* ===== TOOLTIPS TECHNIQUES ===== */
        .tooltip-tech {
            position: relative;
            cursor: help;
        }

        .tooltip-tech::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--neutral-dark);
            color: white;
            padding: 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .tooltip-tech::after {
            content: '';
            position: absolute;
            bottom: 115%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: var(--neutral-dark);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .tooltip-tech:hover::before,
        .tooltip-tech:hover::after {
            opacity: 1;
            visibility: visible;
        }

        /* ===== EFFETS SPÉCIAUX MÉTALLURGIE ===== */
        .molten-steel {
            background: linear-gradient(45deg, 
                var(--iron-rust), 
                var(--spark-orange), 
                var(--forge-red), 
                var(--iron-rust));
            background-size: 400% 400%;
            animation: moltenFlow 4s ease-in-out infinite;
        }

        @keyframes moltenFlow {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .crystalline-structure {
            background-image: 
                polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            background-size: 20px 20px;
            background-repeat: repeat;
        }

        /* ===== THÈME SOMBRE OPTIONNEL ===== */
        @media (prefers-color-scheme: dark) {
            :root {
                --neutral-light: #2c3e50;
                --neutral-dark: #ecf0f1;
                --secondary-white: #34495e;
            }
            
            body {
                background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
                color: var(--neutral-dark);
            }
        }
    </style>
</head>

<body>
    <!-- ===== HEADER GSN ProDevis360° ===== -->
    <header class="header-gsn">
        <div class="header-content">
            <div class="logo-section">
                <div class="logo-gsn">
                    <span>GSN</span>
                </div>
                <div class="header-title">
                    <h1>
                        <i class="fas fa-industry"></i>
                        Module Ferraillage
                        <span class="module-badge steel-effect">
                            <i class="fas fa-bars"></i>
                            Aciers & Armatures
                        </span>
                    </h1>
                    <div class="project-info">
                        <i class="fas fa-building"></i>
                        <strong><?= htmlspecialchars($projet_devis_info['nom_projet']) ?></strong>
                        <span class="mx-2">•</span>
                        <i class="fas fa-file-invoice"></i>
                        Devis #<?= $devis_id ?>
                        <span class="mx-2">•</span>
                        <i class="fas fa-calendar-alt"></i>
                        <?= date('d/m/Y') ?>
                        <span class="mx-2">•</span>
                        <i class="fas fa-hammer"></i>
                        Fers & Treillis
                        <span class="mx-2">•</span>
                        <i class="fas fa-fire"></i>
                        Métallurgie
                    </div>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn-header steel-effect">
                    <i class="fas fa-chart-pie"></i>
                    Récapitulatif
                </a>
                <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn-header">
                    <i class="fas fa-history"></i>
                    Historique
                </a>
                <a href="impression_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn-header">
                    <i class="fas fa-print"></i>
                    Imprimer PDF
                </a>
                <a href="dupliquer_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn-header galvanized-coating">
                    <i class="fas fa-copy"></i>
                    Dupliquer
                </a>
                <button type="button" class="btn-header" onclick="calculerTotalFerraillage()">
                    <i class="fas fa-calculator"></i>
                    Calculs fers
                </button>
            </div>
        </div>
    </header>

    <!-- ===== NAVIGATION MODULES DYNAMIQUE ===== -->
    <nav class="navigation-modules steel-pattern-bg">
        <div class="nav-container">
            <div class="nav-modules">
                <?php foreach ($modules_config as $module_key => $module_info): ?>
                    <a href="<?= $module_key ?>.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                       class="nav-item <?= $module_key === $current_module ? 'active weld-spark' : '' ?>"
                       style="<?= $module_key === $current_module ? '' : '--hover-color: ' . $module_info['color'] ?>"
                       data-tooltip="<?= $module_info['name'] ?> - Cliquez pour accéder"
                       onmouseenter="previewModule('<?= $module_key ?>')">
                        <i class="<?= $module_info['icon'] ?>"></i>
                        <span><?= $module_info['name'] ?></span>
                        <?php if ($module_key === $current_module): ?>
                            <i class="fas fa-check-circle ml-1"></i>
                            <span class="tech-indicator high-strength">
                                <i class="fas fa-cog"></i>
                                Actif
                            </span>
                        <?php endif; ?>
                        
                        <!-- Indicateur de progression pour chaque module -->
                        <?php 
                        $progression = getModuleProgression($module_key, $projet_id, $devis_id);
                        if ($progression > 0): ?>
                            <div class="module-progress" style="
                                position: absolute;
                                bottom: 0;
                                left: 0;
                                height: 3px;
                                width: <?= $progression ?>%;
                                background: linear-gradient(90deg, var(--accent-green), var(--alloy-gold));
                                border-radius: 0 0 4px 4px;
                            "></div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
                
                <!-- Bouton d'aide contextuelle -->
                <button type="button" class="nav-item" onclick="afficherAideFerraillage()" style="margin-left: auto;">
                    <i class="fas fa-question-circle"></i>
                    <span>Aide Ferraillage</span>
                    <span class="tech-indicator stainless">
                        <i class="fas fa-info"></i>
                        Guide
                    </span>
                </button>
            </div>
            
            <!-- Barre de progression globale -->
            <div class="global-progress" style="
                height: 4px;
                background: var(--neutral-light);
                border-radius: 2px;
                margin-top: 0.5rem;
                overflow: hidden;
            ">
                <?php 
                $progression_globale = getProgressionGlobale($projet_id, $devis_id);
                ?>
                <div style="
                    height: 100%;
                    width: <?= $progression_globale ?>%;
                    background: linear-gradient(90deg, var(--steel-primary), var(--primary-orange), var(--alloy-gold));
                    transition: width 0.5s ease;
                    position: relative;
                ">
                    <div style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
                        animation: progressShine 2s ease-in-out infinite;
                    "></div>
                </div>
            </div>
            
            <!-- Informations contextuelles -->
            <div class="context-info" style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 0;
                font-size: 0.85rem;
                color: var(--neutral-gray);
            ">
                <div class="project-stats">
                    <span class="tech-indicator">
                        <i class="fas fa-percentage"></i>
                        Progression: <?= $progression_globale ?>%
                    </span>
                    <span class="tech-indicator galvanized">
                        <i class="fas fa-tasks"></i>
                        <?= getTotalElements($projet_id, $devis_id) ?> éléments
                    </span>
                    <span class="tech-indicator high-strength">
                        <i class="fas fa-euro-sign"></i>
                        <?= number_format(getTotalGeneral($projet_id, $devis_id), 0, ',', ' ') ?> FCFA
                    </span>
                </div>
                
                <div class="module-stats">
                    <span class="tech-indicator stainless">
                        <i class="fas fa-industry"></i>
                        Module Ferraillage
                    </span>
                    <span class="tech-indicator">
                        <i class="fas fa-weight"></i>
                        <?= getTotalPoidsFerraillage($projet_id, $devis_id) ?> kg
                    </span>
                    <span class="tech-indicator galvanized">
                        <i class="fas fa-ruler"></i>
                        <?= getTotalLongueursFerraillage($projet_id, $devis_id) ?> ml
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== ALERTE DE SÉCURITÉ FERRAILLAGE ===== -->
    <div class="safety-alert" style="
        background: linear-gradient(135deg, var(--forge-red) 0%, var(--iron-rust) 100%);
        color: var(--secondary-white);
        padding: 0.75rem 1rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.9rem;
        border-bottom: 2px solid var(--iron-dark);
        animation: safetyPulse 3s ease-in-out infinite;
    ">
        <i class="fas fa-hard-hat" style="font-size: 1.5rem;"></i>
        <div class="safety-content">
            <strong>⚠️ SÉCURITÉ FERRAILLAGE :</strong>
            Port obligatoire des EPI • Gants anti-coupure • Lunettes de protection • Casque de sécurité
            <span class="ml-2">•</span>
            <strong>Attention :</strong> Manipulation d'aciers lourds et coupants
        </div>
        <div class="safety-actions">
            <button type="button" class="btn-header" onclick="afficherConsignesSecurite()" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">
                <i class="fas fa-shield-alt"></i>
                Consignes
            </button>
        </div>
    </div>

    <!-- ===== BARRE D'OUTILS FERRAILLAGE ===== -->
    <div class="ferraillage-toolbar" style="
        background: var(--secondary-white);
        border-bottom: 1px solid #e9ecef;
        padding: 0.75rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow-soft);
    ">
        <div class="toolbar-left">
            <button type="button" class="btn btn-sm btn-info steel-effect" onclick="ouvrirCalculatriceFerraillage()">
                <i class="fas fa-calculator"></i>
                Calculatrice fers
            </button>
            <button type="button" class="btn btn-sm btn-warning rebar-texture" onclick="genererPlanFerraillage()">
                <i class="fas fa-drafting-compass"></i>
                Plan armatures
            </button>
            <button type="button" class="btn btn-sm btn-success galvanized-coating" onclick="exporterListeFerraillage()">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </button>
        </div>
        
        <div class="toolbar-center">
            <div class="quick-stats" style="display: flex; gap: 1rem; align-items: center;">
                <div class="stat-item" style="text-align: center;">
                    <div style="font-size: 1.2rem; font-weight: 600; color: var(--steel-primary);">
                        <?= count($elements_ferraillage) ?>
                    </div>
                    <div style="font-size: 0.7rem; color: var(--neutral-gray);">Éléments</div>
                </div>
                <div class="stat-item" style="text-align: center;">
                    <div style="font-size: 1.2rem; font-weight: 600; color: var(--iron-rust);">
                        <?= getTotalPoidsFerraillage($projet_id, $devis_id) ?>
                    </div>
                    <div style="font-size: 0.7rem; color: var(--neutral-gray);">kg Total</div>
                </div>
                <div class="stat-item" style="text-align: center;">
                    <div style="font-size: 1.2rem; font-weight: 600; color: var(--alloy-gold);">
                        <?= number_format($total_module / 1000, 0) ?>k
                    </div>
                    <div style="font-size: 0.7rem; color: var(--neutral-gray);">FCFA</div>
                </div>
            </div>
        </div>
        
        <div class="toolbar-right">
            <div class="view-options" style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-sm btn-secondary" onclick="changerVueTableau('compact')" title="Vue compacte">
                    <i class="fas fa-compress-alt"></i>
                </button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="changerVueTableau('detaillee')" title="Vue détaillée">
                    <i class="fas fa-expand-alt"></i>
                </button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="changerVueTableau('technique')" title="Vue technique">
                    <i class="fas fa-cogs"></i>
                </button>
            </div>
            
            <button type="button" class="btn btn-sm btn-info weld-spark" onclick="lancerDiagnosticFerraillage()">
                <i class="fas fa-stethoscope"></i>
                Diagnostic
            </button>
        </div>
    </div>

    <!-- ===== NOTIFICATIONS TEMPS RÉEL ===== -->
    <div id="notifications-ferraillage" style="
        position: fixed;
        top: 200px;
        right: 20px;
        z-index: 9999;
        max-width: 350px;
    ">
        <!-- Les notifications apparaîtront ici dynamiquement -->
    </div>

    <!-- ===== MODAL AIDE FERRAILLAGE ===== -->
    <div id="modal-aide-ferraillage" style="
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        z-index: 10000;
        justify-content: center;
        align-items: center;
    ">
        <div style="
            background: var(--secondary-white);
            border-radius: var(--border-radius);
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: var(--shadow-medium);
            position: relative;
        ">
            <div style="
                background: linear-gradient(135deg, var(--steel-primary), var(--steel-dark));
                color: var(--secondary-white);
                padding: 1rem;
                border-radius: var(--border-radius) var(--border-radius) 0 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            ">
                <h3><i class="fas fa-question-circle"></i> Guide Ferraillage GSN</h3>
                <button onclick="fermerAideFerraillage()" style="
                    background: none;
                    border: none;
                    color: var(--secondary-white);
                    font-size: 1.5rem;
                    cursor: pointer;
                ">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div style="padding: 1.5rem;">
                <div class="aide-content">
                    <div class="aide-section">
                        <h4><i class="fas fa-industry"></i> Types d'aciers</h4>
                        <ul>
                            <li><strong>Fer lisse :</strong> Armatures de répartition, étriers</li>
                            <li><strong>Fer HA :</strong> Armatures principales, haute adhérence</li>
                            <li><strong>Treillis soudé :</strong> Dalles, voiles, rapidité de pose</li>
                            <li><strong>Acier inox :</strong> Environnements agressifs, marine</li>
                        </ul>
                    </div>
                    
                    <div class="aide-section">
                        <h4><i class="fas fa-ruler"></i> Calculs rapides</h4>
                        <ul>
                            <li><strong>Poids :</strong> Ø²(mm) × L(m) × 0.00617 = kg</li>
                            <li><strong>Section :</strong> π × (Ø/2)² en cm²</li>
                            <li><strong>Recouvrement :</strong> 40 × Ø minimum</li>
                            <li><strong>Ancrage :</strong> selon classe exposition</li>
                        </ul>
                    </div>
                    
                    <div class="aide-section">
                        <h4><i class="fas fa-shield-alt"></i> Classes d'exposition</h4>
                        <ul>
                            <li><strong>XC1-XC4 :</strong> Corrosion carbonatation</li>
                            <li><strong>XD1-XD3 :</strong> Corrosion chlorures (hors mer)</li>
                            <li><strong>XS1-XS3 :</strong> Corrosion chlorures marins</li>
                            <li><strong>XF1-XF4 :</strong> Attaque gel/dégel</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ===== FONCTIONS NAVIGATION FERRAILLAGE =====
        
        function previewModule(moduleKey) {
            // Prévisualisation au survol des modules
            const descriptions = {
                'plomberie': 'Tuyaux, raccords, sanitaires - Installations eau',
                'menuiserie': 'Portes, fenêtres, placards - Bois & PVC',
                'electricite': 'Câbles, prises, tableaux - Installations électriques',
                'peinture': 'Peintures, enduits, finitions - Revêtements',
                'materiaux': 'Ciment, sable, graviers - Matériaux de base',
                'charpenterie': 'Bois, ossatures, couverture - Structure bois',
                'carrelage': 'Carreaux, joints, colles - Revêtements sols/murs',
                'ferraillage': 'Fers, armatures, treillis - Béton armé',
                'ferronnerie': 'Portails, grilles, structures - Métallerie'
            };
            
            if (descriptions[moduleKey]) {
                showNotification(descriptions[moduleKey], 'info', 2000);
            }
        }
        
        function afficherAideFerraillage() {
            document.getElementById('modal-aide-ferraillage').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function fermerAideFerraillage() {
            document.getElementById('modal-aide-ferraillage').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function afficherConsignesSecurite() {
            const consignes = `
🛡️ CONSIGNES DE SÉCURITÉ FERRAILLAGE :

👷 EPI OBLIGATOIRES :
• Casque de sécurité
• Gants anti-coupure
• Lunettes de protection
• Chaussures de sécurité

⚠️ RISQUES PRINCIPAUX :
• Coupures par aciers
• Chutes d'objets lourds
• Écrasement des pieds
• Projections oculaires

🔧 BONNES PRATIQUES :
• Vérifier l'état des outils
• Manipuler à plusieurs si lourd
• Ranger les fers correctement
• Nettoyer l'espace de travail
            `;
            
            showNotification(consignes, 'warning', 8000);
        }
        
        function calculerTotalFerraillage() {
            // Calculer totaux en temps réel
            const elements = <?= json_encode($elements_ferraillage) ?>;
            let totalPoids = 0;
            let totalLongueur = 0;
            let totalCout = 0;
            
            elements.forEach(element => {
                if (element.poids_lineique && element.longueur) {
                    totalPoids += element.poids_lineique * element.longueur * element.quantite;
                }
                if (element.longueur) {
                    totalLongueur += element.longueur * element.quantite;
                }
                totalCout += parseFloat(element.total);
            });
            
            const resultats = `
📊 TOTAUX FERRAILLAGE :

⚖️ Poids total : ${totalPoids.toFixed(1)} kg
📏 Longueur totale : ${totalLongueur.toFixed(1)} ml
💰 Coût total : ${totalCout.toLocaleString()} FCFA
📦 Éléments : ${elements.length}

💡 Estimation camion : ${Math.ceil(totalPoids/1000)} tonne(s)
            `;
            
            showNotification(resultats, 'success', 6000);
        }
        
        function showNotification(message, type = 'info', duration = 3000) {
            const container = document.getElementById('notifications-ferraillage');
            const notification = document.createElement('div');
            
            const colors = {
                'info': 'var(--weld-blue)',
                'success': 'var(--accent-green)',
                'warning': 'var(--spark-orange)',
                'error': 'var(--iron-rust)'
            };
            
            const icons = {
                'info': 'fas fa-info-circle',
                'success': 'fas fa-check-circle',
                'warning': 'fas fa-exclamation-triangle',
                'error': 'fas fa-times-circle'
            };
            
            notification.style.cssText = `
                background: ${colors[type]};
                color: var(--secondary-white);
                padding: 1rem;
                border-radius: var(--border-radius);
                margin-bottom: 0.5rem;
                box-shadow: var(--shadow-medium);
                animation: slideInRight 0.4s ease-out;
                white-space: pre-line;
                font-size: 0.9rem;
                max-width: 100%;
                word-wrap: break-word;
            `;
            
            notification.innerHTML = `
                <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                    <i class="${icons[type]}"></i>
                    <div style="flex: 1;">${message}</div>
                    <button onclick="this.parentElement.parentElement.remove()" style="
                        background: none;
                        border: none;
                        color: var(--secondary-white);
                        cursor: pointer;
                        font-size: 1.2rem;
                    ">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'slideOutRight 0.4s ease-out';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 400);
                }
            }, duration);
        }
        
        // Fonctions additionnelles pour la barre d'outils
        function ouvrirCalculatriceFerraillage() {
            showNotification('🧮 Calculatrice ferraillage : Utilisez les champs calculateurs du formulaire ci-dessous !', 'info');
        }
        
        function genererPlanFerraillage() {
            showNotification('📐 Génération du plan de ferraillage en cours...', 'info');
            // Logique pour générer un plan
        }
        
        function exporterListeFerraillage() {
            showNotification('📊 Export Excel des éléments ferraillage en cours...', 'success');
            // Logique d'export Excel
        }
        
        function changerVueTableau(type) {
            showNotification(`👁️ Vue tableau changée en mode : ${type}`, 'info');
            // Logique pour changer la vue
        }
        
        function lancerDiagnosticFerraillage() {
            const elements = <?= json_encode($elements_ferraillage) ?>;
            let alertes = [];
            
            elements.forEach(element => {
                // Vérifications techniques
                if (element.diametre > 25 && !element.nuance_acier.includes('500')) {
                    alertes.push(`⚠️ Gros diamètre (${element.diametre}mm) sans nuance FeE500`);
                }
                if (element.classe_exposition && element.classe_exposition.startsWith('XS') && !element.traitement_surface) {
                    alertes.push(`🌊 Classe marine ${element.classe_exposition} sans traitement`);
                }
            });
            
            const diagnostic = alertes.length > 0 
                ? `🔍 DIAGNOSTIC FERRAILLAGE :\n\n${alertes.join('\n')}\n\n✅ ${elements.length - alertes.length} éléments conformes`
                : `✅ DIAGNOSTIC FERRAILLAGE :\n\nTous les éléments sont conformes !\n${elements.length} éléments vérifiés.`;
                
            showNotification(diagnostic, alertes.length > 0 ? 'warning' : 'success', 5000);
        }
        
        // Animation CSS pour les notifications
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { opacity: 0; transform: translateX(100%); }
                to { opacity: 1; transform: translateX(0); }
            }
            @keyframes slideOutRight {
                from { opacity: 1; transform: translateX(0); }
                to { opacity: 0; transform: translateX(100%); }
            }
            @keyframes safetyPulse {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }
            @keyframes progressShine {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(200%); }
            }
        `;
        document.head.appendChild(style);
        
        // Initialisation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            // Fermer la modal d'aide en cliquant à l'extérieur
            document.getElementById('modal-aide-ferraillage').addEventListener('click', function(e) {
                if (e.target === this) {
                    fermerAideFerraillage();
                }
            });
            
            // Notification de bienvenue
            setTimeout(() => {
                showNotification('⚡ Module Ferraillage GSN ProDevis360° chargé !\n🔧 Toutes les fonctionnalités métallurgie sont disponibles.', 'success', 3000);
            }, 1000);
        });
    </script>
    
<!-- ===== CONTAINER PRINCIPAL ===== -->
    <div class="container">
        
        <!-- ===== MESSAGES D'ALERTE ===== -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> fade-in-up steel-forge">
                <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- ===== FORMULAIRE FERRAILLAGE ===== -->
        <div class="form-section fade-in-up steel-effect">
            <h2>
                <i class="fas fa-<?= $element_a_modifier ? 'edit' : 'plus-circle' ?>"></i>
                <?= $element_a_modifier ? 'Modifier l\'élément ferraillage' : 'Ajouter un élément ferraillage' ?>
                <span class="tech-indicator high-strength">
                    <i class="fas fa-industry"></i>
                    Aciers & Armatures
                </span>
                <span class="tech-indicator stainless">
                    <i class="fas fa-certificate"></i>
                    Normes EN/NF
                </span>
            </h2>

            <!-- Suggestions Ferraillage -->
            <div class="suggestions-ferraillage">
                <h4>
                    <i class="fas fa-bars"></i>
                    Catalogue Ferraillage & Aciers pour Béton Armé
                    <small>(Cliquez pour remplir automatiquement)</small>
                    <span class="tech-indicator galvanized">
                        <i class="fas fa-hammer"></i>
                        85+ suggestions techniques
                    </span>
                    <span class="tech-indicator high-strength">
                        <i class="fas fa-shield-alt"></i>
                        Conformité Eurocode 2
                    </span>
                </h4>
                <div class="suggestions-grid">
                    <?php foreach ($suggestions_ferraillage as $suggestion): ?>
                        <div class="suggestion-item steel-effect" onclick="remplirSuggestion('<?= htmlspecialchars($suggestion, ENT_QUOTES) ?>')">
                            <i class="fas fa-minus"></i>
                            <?= htmlspecialchars($suggestion) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Calculateur Ferraillage -->
            <div class="calculator-section molten-steel">
                <h4>
                    <i class="fas fa-calculator"></i>
                    Calculateur Ferraillage & Dimensionnement Eurocode 2
                    <span class="tech-indicator stainless">
                        <i class="fas fa-ruler-combined"></i>
                        Calculs normatifs automatiques
                    </span>
                </h4>
                <div class="calc-grid">
                    <input type="number" id="calc_diametre" placeholder="Diamètre (mm)" class="calc-input" step="1" max="50" onchange="calculerPoidsLineique()">
                    <input type="number" id="calc_longueur_barre" placeholder="Longueur (m)" class="calc-input" step="0.1" max="20">
                    <input type="number" id="calc_nombre_barres" placeholder="Nombre" class="calc-input" step="1" max="1000">
                    <select id="calc_nuance" class="calc-input">
                        <option value="">Nuance</option>
                        <option value="FeE500">FeE500</option>
                        <option value="B500A">B500A</option>
                        <option value="B500B">B500B</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-info weld-spark" onclick="calculerPoidsFer()">
                        <i class="fas fa-weight"></i> Poids total
                    </button>
                    <button type="button" class="btn btn-sm btn-warning rebar-texture" onclick="calculerSection()">
                        <i class="fas fa-circle"></i> Section cm²
                    </button>
                    <button type="button" class="btn btn-sm btn-success galvanized-coating" onclick="calculerRecouvrement()">
                        <i class="fas fa-link"></i> Recouvrement
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary steel-effect" onclick="calculerAncrage()">
                        <i class="fas fa-anchor"></i> Ancrage
                    </button>
                </div>
                
                <!-- Résultats calculateurs en temps réel -->
                <div id="resultats-calculs" style="
                    margin-top: 1rem;
                    padding: 0.75rem;
                    background: rgba(255,255,255,0.9);
                    border-radius: 4px;
                    color: var(--neutral-dark);
                    font-size: 0.85rem;
                    display: none;
                ">
                    <div id="resultats-content"></div>
                </div>
            </div>

            <form method="POST" action="" id="formFerraillage">
                <input type="hidden" name="action" value="<?= $element_a_modifier ? 'modifier' : 'ajouter' ?>">
                <?php if ($element_a_modifier): ?>
                    <input type="hidden" name="element_id" value="<?= $element_a_modifier['id'] ?>">
                <?php endif; ?>

                <!-- Ligne 1 : Informations principales -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="designation">
                            <i class="fas fa-tag"></i>
                            Désignation <span class="required">*</span>
                            <span class="tooltip-tech" data-tooltip="Description complète de l'élément ferraillage">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <input type="text" 
                               id="designation" 
                               name="designation" 
                               value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['designation']) : '' ?>"
                               placeholder="Ex: Fer HA Ø12mm (FeE500) longueur 6m"
                               required
                               maxlength="255"
                               onchange="analyserDesignation()">
                    </div>

                    <div class="form-group">
                        <label for="quantite">
                            <i class="fas fa-calculator"></i>
                            Quantité <span class="required">*</span>
                            <span class="tooltip-tech" data-tooltip="Quantité selon l'unité choisie">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <input type="number" 
                               id="quantite" 
                               name="quantite" 
                               value="<?= $element_a_modifier ? $element_a_modifier['quantite'] : '' ?>"
                               placeholder="Ex: 125.5"
                               step="0.01"
                               min="0.01"
                               required
                               onchange="calculerTotalElement()">
                    </div>

                    <div class="form-group">
                        <label for="unite">
                            <i class="fas fa-ruler"></i>
                            Unité
                            <span class="tooltip-tech" data-tooltip="Unité de mesure pour la quantité">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <select id="unite" name="unite" onchange="adapterChampsSelon UniteUnite()">
                            <option value="kg" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'kg') ? 'selected' : '' ?>>Kilogramme (kg)</option>
                            <option value="t" <?= ($element_a_modifier && $element_a_modifier['unite'] === 't') ? 'selected' : '' ?>>Tonne (t)</option>
                            <option value="ml" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'ml') ? 'selected' : '' ?>>Mètre linéaire (ml)</option>
                            <option value="unité" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'unité') ? 'selected' : '' ?>>Unité</option>
                            <option value="m²" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm²') ? 'selected' : '' ?>>Mètre carré (m²)</option>
                            <option value="lot" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'lot') ? 'selected' : '' ?>>Lot</option>
                            <option value="palette" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'palette') ? 'selected' : '' ?>>Palette</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="prix_unitaire">
                            <i class="fas fa-euro-sign"></i>
                            Prix unitaire (FCFA) <span class="required">*</span>
                            <span class="tooltip-tech" data-tooltip="Prix par unité en Francs CFA">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <input type="number" 
                               id="prix_unitaire" 
                               name="prix_unitaire" 
                               value="<?= $element_a_modifier ? $element_a_modifier['prix_unitaire'] : '' ?>"
                               placeholder="Ex: 850"
                               step="0.01"
                               min="0"
                               required
                               onchange="calculerTotalElement()">
                    </div>
                </div>

                <!-- Ligne 2 : Type et caractéristiques géométriques -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="type_acier">
                            <i class="fas fa-industry"></i>
                            Type d'acier
                            <span class="tooltip-tech" data-tooltip="Catégorie d'acier selon usage et fabrication">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <select id="type_acier" name="type_acier" onchange="adapterChampsSelonType()">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Fer rond lisse" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Fer rond lisse') ? 'selected' : '' ?>>Fer rond lisse (RL)</option>
                            <option value="Fer haute adhérence" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Fer haute adhérence') ? 'selected' : '' ?>>Fer haute adhérence (HA)</option>
                            <option value="Treillis soudé" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Treillis soudé') ? 'selected' : '' ?>>Treillis soudé (TS)</option>
                            <option value="Armature préfabriquée" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Armature préfabriquée') ? 'selected' : '' ?>>Armature préfabriquée</option>
                            <option value="Acier inoxydable" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Acier inoxydable') ? 'selected' : '' ?>>Acier inoxydable</option>
                            <option value="Acier galvanisé" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Acier galvanisé') ? 'selected' : '' ?>>Acier galvanisé</option>
                            <option value="Acier précontraint" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Acier précontraint') ? 'selected' : '' ?>>Acier précontraint</option>
                            <option value="Barre composite" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Barre composite') ? 'selected' : '' ?>>Barre composite (GFRP/CFRP)</option>
                            <option value="Connecteur" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Connecteur') ? 'selected' : '' ?>>Connecteur/Ancrage</option>
                            <option value="Accessoire" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Accessoire') ? 'selected' : '' ?>>Accessoire ferraillage</option>
                            <option value="Outillage" <?= ($element_a_modifier && $element_a_modifier['type_acier'] === 'Outillage') ? 'selected' : '' ?>>Outillage spécialisé</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="diametre">
                            <i class="fas fa-circle"></i>
                            Diamètre (mm)
                            <span class="tooltip-tech" data-tooltip="Diamètre nominal de la barre en millimètres">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <input type="number" 
                               id="diametre" 
                               name="diametre" 
                               value="<?= $element_a_modifier ? $element_a_modifier['diametre'] : '' ?>"
                               placeholder="Ex: 12"
                               step="0.1"
                               min="0"
                               max="50"
                               onchange="calculerCaracteristiques()">
                    </div>

                    <div class="form-group">
                        <label for="longueur">
                            <i class="fas fa-arrows-alt-h"></i>
                            Longueur (m)
                            <span class="tooltip-tech" data-tooltip="Longueur de la barre en mètres">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <input type="number" 
                               id="longueur" 
                               name="longueur" 
                               value="<?= $element_a_modifier ? $element_a_modifier['longueur'] : '' ?>"
                               placeholder="Ex: 6.0"
                               step="0.1"
                               min="0"
                               max="20"
                               onchange="calculerCaracteristiques()">
                    </div>

                    <div class="form-group">
                        <label for="nuance_acier">
                            <i class="fas fa-certificate"></i>
                            Nuance acier
                            <span class="tooltip-tech" data-tooltip="Classe de résistance selon norme EN 10080">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <select id="nuance_acier" name="nuance_acier" onchange="remplirCaracteristiquesMecaniques()">
                            <option value="">-- Sélectionnez --</option>
                            <option value="FeE235" <?= ($element_a_modifier && $element_a_modifier['nuance_acier'] === 'FeE235') ? 'selected' : '' ?>>FeE235 (235 MPa)</option>
                            <option value="FeE400" <?= ($element_a_modifier && $element_a_modifier['nuance_acier'] === 'FeE400') ? 'selected' : '' ?>>FeE400 (400 MPa)</option>
                            <option value="FeE500" <?= ($element_a_modifier && $element_a_modifier['nuance_acier'] === 'FeE500') ? 'selected' : '' ?>>FeE500 (500 MPa)</option>
                            <option value="FeE500B" <?= ($element_a_modifier && $element_a_modifier['nuance_acier'] === 'FeE500B') ? 'selected' : '' ?>>FeE500B (soudable)</option>
                            <option value="B500A" <?= ($element_a_modifier && $element_a_modifier['nuance_acier'] === 'B500A') ? 'selected' : '' ?>>B500A (ductilité normale)</option>
                            <option value="B500B" <?= ($element_a_modifier && $element_a_modifier['nuance_acier'] === 'B500B') ? 'selected' : '' ?>>B500B (haute ductilité)</option>
                            <option value="316L" <?= ($element_a_modifier && $element_a_modifier['nuance_acier'] === '316L') ? 'selected' : '' ?>>316L (inox marine)</option>
                            <option value="304L" <?= ($element_a_modifier && $element_a_modifier['nuance_acier'] === '304L') ? 'selected' : '' ?>>304L (inox standard)</option>
                            <option value="T15" <?= ($element_a_modifier && $element_a_modifier['nuance_acier'] === 'T15') ? 'selected' : '' ?>>T15 (précontraint)</option>
                            <option value="Galvanisé Z275" <?= ($element_a_modifier && $element_a_modifier['nuance_acier'] === 'Galvanisé Z275') ? 'selected' : '' ?>>Galvanisé Z275</option>
                        </select>
                    </div>
                </div>

                <!-- Ligne 3 : Forme et traitement -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="forme">
                            <i class="fas fa-shapes"></i>
                            Forme géométrique
                            <span class="tooltip-tech" data-tooltip="Configuration géométrique de l'armature">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <select id="forme" name="forme">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Barre droite" <?= ($element_a_modifier && $element_a_modifier['forme'] === 'Barre droite') ? 'selected' : '' ?>>Barre droite</option>
                            <option value="Barre coudée" <?= ($element_a_modifier && $element_a_modifier['forme'] === 'Barre coudée') ? 'selected' : '' ?>>Barre coudée</option>
                            <option value="Cadre rectangulaire" <?= ($element_a_modifier && $element_a_modifier['forme'] === 'Cadre rectangulaire') ? 'selected' : '' ?>>Cadre rectangulaire</option>
                            <option value="Cadre carré" <?= ($element_a_modifier && $element_a_modifier['forme'] === 'Cadre carré') ? 'selected' : '' ?>>Cadre carré</option>
                            <option value="Étrier" <?= ($element_a_modifier && $element_a_modifier['forme'] === 'Étrier') ? 'selected' : '' ?>>Étrier</option>
                            <option value="Épingle" <?= ($element_a_modifier && $element_a_modifier['forme'] === 'Épingle') ? 'selected' : '' ?>>Épingle</option>
                            <option value="Crochet" <?= ($element_a_modifier && $element_a_modifier['forme'] === 'Crochet') ? 'selected' : '' ?>>Crochet</option>
                            <option value="Panneau treillis" <?= ($element_a_modifier && $element_a_modifier['forme'] === 'Panneau treillis') ? 'selected' : '' ?>>Panneau treillis</option>
                            <option value="Rouleau treillis" <?= ($element_a_modifier && $element_a_modifier['forme'] === 'Rouleau treillis') ? 'selected' : '' ?>>Rouleau treillis</option>
                            <option value="Armature façonnée" <?= ($element_a_modifier && $element_a_modifier['forme'] === 'Armature façonnée') ? 'selected' : '' ?>>Armature façonnée</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="traitement_surface">
                            <i class="fas fa-spray-can"></i>
                            Traitement surface
                            <span class="tooltip-tech" data-tooltip="Protection contre la corrosion">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <select id="traitement_surface" name="traitement_surface">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Brut de laminage" <?= ($element_a_modifier && $element_a_modifier['traitement_surface'] === 'Brut de laminage') ? 'selected' : '' ?>>Brut de laminage</option>
                            <option value="Galvanisé à chaud" <?= ($element_a_modifier && $element_a_modifier['traitement_surface'] === 'Galvanisé à chaud') ? 'selected' : '' ?>>Galvanisé à chaud</option>
                            <option value="Galvanisé électrolytique" <?= ($element_a_modifier && $element_a_modifier['traitement_surface'] === 'Galvanisé électrolytique') ? 'selected' : '' ?>>Galvanisé électrolytique</option>
                            <option value="Revêtement époxy" <?= ($element_a_modifier && $element_a_modifier['traitement_surface'] === 'Revêtement époxy') ? 'selected' : '' ?>>Revêtement époxy</option>
                            <option value="Passivé" <?= ($element_a_modifier && $element_a_modifier['traitement_surface'] === 'Passivé') ? 'selected' : '' ?>>Passivé (inox)</option>
                            <option value="Phosphaté" <?= ($element_a_modifier && $element_a_modifier['traitement_surface'] === 'Phosphaté') ? 'selected' : '' ?>>Phosphaté</option>
                            <option value="Zingué" <?= ($element_a_modifier && $element_a_modifier['traitement_surface'] === 'Zingué') ? 'selected' : '' ?>>Zingué</option>
                            <option value="Dacromet" <?= ($element_a_modifier && $element_a_modifier['traitement_surface'] === 'Dacromet') ? 'selected' : '' ?>>Dacromet</option>
                            <option value="Métallisation zinc" <?= ($element_a_modifier && $element_a_modifier['traitement_surface'] === 'Métallisation zinc') ? 'selected' : '' ?>>Métallisation zinc</option>
                            <option value="Inhibiteur corrosion" <?= ($element_a_modifier && $element_a_modifier['traitement_surface'] === 'Inhibiteur corrosion') ? 'selected' : '' ?>>Inhibiteur corrosion</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="usage_structural">
                            <i class="fas fa-building"></i>
                            Usage structural
                            <span class="tooltip-tech" data-tooltip="Fonction dans la structure béton armé">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <select id="usage_structural" name="usage_structural">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Fondations" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Fondations') ? 'selected' : '' ?>>Fondations</option>
                            <option value="Dalles" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Dalles') ? 'selected' : '' ?>>Dalles</option>
                            <option value="Poutres" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Poutres') ? 'selected' : '' ?>>Poutres</option>
                            <option value="Poteaux" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Poteaux') ? 'selected' : '' ?>>Poteaux</option>
                            <option value="Voiles" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Voiles') ? 'selected' : '' ?>>Voiles</option>
                            <option value="Escaliers" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Escaliers') ? 'selected' : '' ?>>Escaliers</option>
                            <option value="Chaînages" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Chaînages') ? 'selected' : '' ?>>Chaînages</option>
                            <option value="Longrines" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Longrines') ? 'selected' : '' ?>>Longrines</option>
                            <option value="Radier" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Radier') ? 'selected' : '' ?>>Radier</option>
                            <option value="Précontrainte" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Précontrainte') ? 'selected' : '' ?>>Précontrainte</option>
                            <option value="Armatures de peau" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Armatures de peau') ? 'selected' : '' ?>>Armatures de peau</option>
                            <option value="Armatures de répartition" <?= ($element_a_modifier && $element_a_modifier['usage_structural'] === 'Armatures de répartition') ? 'selected' : '' ?>>Armatures de répartition</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="classe_exposition">
                            <i class="fas fa-shield-alt"></i>
                            Classe exposition
                            <span class="tooltip-tech" data-tooltip="Classe d'environnement selon Eurocode 2">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <select id="classe_exposition" name="classe_exposition" onchange="verifierCompatibiliteExposition()">
                            <option value="">-- Sélectionnez --</option>
                            <optgroup label="🏠 Carbonatation (XC)">
                                <option value="XC1" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XC1') ? 'selected' : '' ?>>XC1 (sec permanent)</option>
                                <option value="XC2" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XC2') ? 'selected' : '' ?>>XC2 (humide/sec cyclique)</option>
                                <option value="XC3" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XC3') ? 'selected' : '' ?>>XC3 (humidité modérée)</option>
                                <option value="XC4" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XC4') ? 'selected' : '' ?>>XC4 (humide/sec cyclique)</option>
                            </optgroup>
                            <optgroup label="🧂 Chlorures hors mer (XD)">
                                <option value="XD1" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XD1') ? 'selected' : '' ?>>XD1 (chlorures sauf eau mer)</option>
                                <option value="XD2" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XD2') ? 'selected' : '' ?>>XD2 (piscines chlorées)</option>
                                <option value="XD3" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XD3') ? 'selected' : '' ?>>XD3 (sels de déverglaçage)</option>
                            </optgroup>
                            <optgroup label="🌊 Chlorures marins (XS)">
                                <option value="XS1" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XS1') ? 'selected' : '' ?>>XS1 (air marin)</option>
                                <option value="XS2" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XS2') ? 'selected' : '' ?>>XS2 (immersion eau mer)</option>
                                <option value="XS3" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XS3') ? 'selected' : '' ?>>XS3 (zones marnage)</option>
                            </optgroup>
                            <optgroup label="❄️ Gel/Dégel (XF)">
                                <option value="XF1" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XF1') ? 'selected' : '' ?>>XF1 (gel/dégel modéré)</option>
                                <option value="XF2" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XF2') ? 'selected' : '' ?>>XF2 (gel/dégel + sels)</option>
                                <option value="XF3" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XF3') ? 'selected' : '' ?>>XF3 (gel/dégel sévère)</option>
                                <option value="XF4" <?= ($element_a_modifier && $element_a_modifier['classe_exposition'] === 'XF4') ? 'selected' : '' ?>>XF4 (gel/dégel + sels sévère)</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <!-- Ligne 4 : Propriétés mécaniques et physiques -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="poids_lineique">
                            <i class="fas fa-weight"></i>
                            Poids linéique (kg/m)
                            <span class="tooltip-tech" data-tooltip="Masse par mètre linéaire (calculé automatiquement)">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <input type="number" 
                               id="poids_lineique" 
                               name="poids_lineique" 
                               value="<?= $element_a_modifier ? $element_a_modifier['poids_lineique'] : '' ?>"
                               placeholder="Ex: 0.888"
                               step="0.001"
                               min="0"
                               max="50"
                               readonly
                               style="background-color: #f8f9fa;">
                    </div>

                    <div class="form-group">
                        <label for="resistance_traction">
                            <i class="fas fa-expand-arrows-alt"></i>
                            Résistance traction (MPa)
                            <span class="tooltip-tech" data-tooltip="Résistance ultime en traction (Rm)">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <input type="number" 
                               id="resistance_traction" 
                               name="resistance_traction" 
                               value="<?= $element_a_modifier ? $element_a_modifier['resistance_traction'] : '' ?>"
                               placeholder="Ex: 550"
                               step="1"
                               min="0"
                               max="1000">
                    </div>

                    <div class="form-group">
                        <label for="limite_elastique">
                            <i class="fas fa-chart-line"></i>
                            Limite élastique (MPa)
                            <span class="tooltip-tech" data-tooltip="Limite d'élasticité caractéristique (Re)">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <input type="number" 
                               id="limite_elastique" 
                               name="limite_elastique" 
                               value="<?= $element_a_modifier ? $element_a_modifier['limite_elastique'] : '' ?>"
                               placeholder="Ex: 500"
                               step="1"
                               min="0"
                               max="800">
                    </div>

                    <div class="form-group">
                        <label for="allongement">
                            <i class="fas fa-arrows-alt-h"></i>
                            Allongement (%)
                            <span class="tooltip-tech" data-tooltip="Allongement sous charge maximale (A%)">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </label>
                        <input type="number" 
                               id="allongement" 
                               name="allongement" 
                               value="<?= $element_a_modifier ? $element_a_modifier['allongement'] : '' ?>"
                               placeholder="Ex: 12"
                               step="0.1"
                               min="0"
                               max="50">
                    </div>
                </div>

                <!-- Ligne 5 : Actions rapides et informations calculées -->
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-bolt"></i>
                            Actions rapides ferraillage
                        </label>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <button type="button" 
                                    class="btn btn-sm btn-info weld-spark" 
                                    onclick="calculerPoidsTotal()"
                                    title="Alt+P">
                                <i class="fas fa-balance-scale"></i>
                                Poids total
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-warning rebar-texture" 
                                    onclick="estimerPrixFer()"
                                    title="Alt+E">
                                <i class="fas fa-euro-sign"></i>
                                Prix auto
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-success galvanized-coating" 
                                    onclick="verifierNorme()"
                                    title="Alt+N">
                                <i class="fas fa-certificate"></i>
                                Vérif. norme
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-secondary steel-effect" 
                                    onclick="genererFicheTechnique()"
                                    title="Alt+F">
                                <i class="fas fa-file-alt"></i>
                                Fiche tech.
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-info-circle"></i>
                            Informations calculées temps réel
                        </label>
                        <div id="info-calculs-temps-reel" style="
                            font-size: 0.85rem; 
                            color: var(--neutral-gray); 
                            line-height: 1.4;
                            padding: 0.5rem;
                            background: var(--neutral-light);
                            border-radius: 4px;
                            min-height: 60px;
                        ">
                            <i class="fas fa-calculator"></i> Les calculs apparaîtront automatiquement selon les données saisies
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-exclamation-triangle"></i>
                            Alertes conformité
                        </label>
                        <div id="alertes-conformite" style="
                            font-size: 0.85rem;
                            line-height: 1.4;
                            padding: 0.5rem;
                            background: #fff3cd;
                            border-radius: 4px;
                            min-height: 60px;
                            border-left: 4px solid #ffc107;
                        ">
                            <i class="fas fa-shield-alt"></i> Les vérifications de conformité s'afficheront ici
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-chart-bar"></i>
                            Total élément
                        </label>
                        <div id="total-element-preview" style="
                            font-size: 1.2rem;
                            font-weight: 600;
                            color: var(--steel-primary);
                            padding: 0.75rem;
                            background: linear-gradient(135deg, var(--neutral-light), var(--secondary-white));
                            border-radius: 4px;
                            text-align: center;
                            border: 2px solid var(--steel-primary);
                        ">
                            <i class="fas fa-calculator"></i> 0 FCFA
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="form-row">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary weld-spark hammer-strike">
                            <i class="fas fa-<?= $element_a_modifier ? 'save' : 'plus' ?>"></i>
                            <?= $element_a_modifier ? 'Modifier l\'élément' : 'Ajouter l\'élément' ?>
                        </button>
                        
                        <?php if ($element_a_modifier): ?>
                            <a href="ferraillage.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                        <?php endif; ?>
                        
                        <button type="reset" class="btn btn-secondary ml-2" onclick="resetFormulaire()">
                            <i class="fas fa-eraser"></i>
                            Effacer
                        </button>
                        
                        <button type="button" class="btn btn-info ml-2 steel-effect" onclick="previsualiserFerraillage()">
                            <i class="fas fa-eye"></i>
                            Prévisualiser
                        </button>
                        
                        <button type="button" class="btn btn-warning ml-2 rebar-texture" onclick="genererPlanFerraillage()">
                            <i class="fas fa-drafting-compass"></i>
                            Plan ferraillage
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ===== TABLEAU DES ÉLÉMENTS FERRAILLAGE ===== -->
        <div class="table-container fade-in-up">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list"></i>
                    Éléments ferraillage & armatures
                    <span class="badge-acier ml-2"><?= count($elements_ferraillage) ?> élément(s)</span>
                    <span class="tech-indicator galvanized ml-2">
                        <i class="fas fa-weight"></i>
                        <?= getTotalPoidsFerraillage($projet_id, $devis_id) ?> kg
                    </span>
                </h3>
                <div class="table-actions">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <button type="button" class="btn btn-sm btn-info" onclick="exporterTableauExcel()">
                            <i class="fas fa-file-excel"></i>
                            Export Excel
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" onclick="genererBonCommande()">
                            <i class="fas fa-shopping-cart"></i>
                            Bon commande
                        </button>
                        <span class="total-amount weld-spark">
                            <i class="fas fa-industry"></i>
                            <?= number_format($total_module, 0, ',', ' ') ?> FCFA
                        </span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" id="tableau-ferraillage">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> #</th>
                            <th><i class="fas fa-tag"></i> Désignation</th>
                            <th><i class="fas fa-calculator"></i> Qté</th>
                            <th><i class="fas fa-ruler"></i> Unité</th>
                            <th><i class="fas fa-euro-sign"></i> Prix Unit.</th>
                            <th><i class="fas fa-industry"></i> Type</th>
                            <th><i class="fas fa-circle"></i> Ø (mm)</th>
                            <th><i class="fas fa-arrows-alt-h"></i> Long. (m)</th>
                            <th><i class="fas fa-certificate"></i> Nuance</th>
                            <th><i class="fas fa-shapes"></i> Forme</th>
                            <th><i class="fas fa-spray-can"></i> Traitement</th>
                            <th><i class="fas fa-building"></i> Usage</th>
                            <th><i class="fas fa-shield-alt"></i> Exposition</th>
                            <th><i class="fas fa-weight"></i> P.lin (kg/m)</th>
                            <th><i class="fas fa-expand-arrows-alt"></i> Rm (MPa)</th>
                            <th><i class="fas fa-chart-line"></i> Re (MPa)</th>
                            <th><i class="fas fa-arrows-alt-h"></i> A (%)</th>
                            <th><i class="fas fa-euro-sign"></i> Total</th>
                            <th><i class="fas fa-calendar"></i> Créé</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($elements_ferraillage)): ?>
                            <tr>
                                <td colspan="20" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-industry fa-3x mb-3 d-block steel-effect"></i>
                                        <p><strong>Aucun élément ferraillage ajouté pour ce devis.</strong></p>
                                        <small>Utilisez le formulaire ci-dessus pour ajouter des fers à béton, treillis soudés, armatures préfabriquées, etc.</small>
                                        <div style="margin-top: 1rem;">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('designation').focus()">
                                                <i class="fas fa-plus"></i>
                                                Ajouter le premier élément
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($elements_ferraillage as $element): ?>
                                <tr class="steel-effect" onmouseover="highlightElement(this)" onmouseout="unhighlightElement(this)">
                                    <td><strong class="badge-diametre"><?= $counter++ ?></strong></td>
                                    <td>
                                        <strong><?= htmlspecialchars($element['designation']) ?></strong>
                                        <?php if ($element['date_modification_fr']): ?>
                                            <br><small class="text-muted">
                                                <i class="fas fa-edit"></i> Modifié le <?= $element['date_modification_fr'] ?>
                                            </small>
                                        <?php endif; ?>
                                        
                                        <!-- Indicateurs visuels -->
                                        <div style="margin-top: 0.25rem;">
                                            <?php if (!empty($element['nuance_acier']) && strpos($element['nuance_acier'], '500') !== false): ?>
                                                <span class="tech-indicator high-strength">
                                                    <i class="fas fa-star"></i> HA
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($element['traitement_surface']) && strpos($element['traitement_surface'], 'Galvanisé') !== false): ?>
                                                <span class="tech-indicator galvanized">
                                                    <i class="fas fa-shield-alt"></i> Galva
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($element['classe_exposition']) && strpos($element['classe_exposition'], 'XS') !== false): ?>
                                                <span class="tech-indicator stainless">
                                                    <i class="fas fa-water"></i> Marine
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><strong><?= number_format($element['quantite'], 2, ',', ' ') ?></strong></td>
                                    <td><span class="badge-diametre"><?= htmlspecialchars($element['unite']) ?></span></td>
                                    <td><strong><?= number_format($element['prix_unitaire'], 0, ',', ' ') ?></strong> FCFA</td>
                                    <td>
                                        <?php if (!empty($element['type_acier'])): ?>
                                            <span class="badge-acier">
                                                <i class="fas fa-industry"></i>
                                                <?= substr(htmlspecialchars($element['type_acier']), 0, 12) ?><?= strlen($element['type_acier']) > 12 ? '...' : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['diametre']) && $element['diametre'] > 0): ?>
                                            <span class="badge-diametre">
                                                <i class="fas fa-circle"></i>
                                                Ø<?= number_format($element['diametre'], 0) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['longueur']) && $element['longueur'] > 0): ?>
                                            <span class="badge-diametre">
                                                <i class="fas fa-arrows-alt-h"></i>
                                                <?= number_format($element['longueur'], 1) ?>m
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['nuance_acier'])): ?>
                                            <span class="badge-nuance">
                                                <i class="fas fa-certificate"></i>
                                                <?= htmlspecialchars($element['nuance_acier']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['forme'])): ?>
                                            <span class="badge-forme">
                                                <i class="fas fa-shapes"></i>
                                                <?= substr(htmlspecialchars($element['forme']), 0, 8) ?><?= strlen($element['forme']) > 8 ? '...' : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['traitement_surface'])): ?>
                                            <span class="badge-traitement">
                                                <i class="fas fa-spray-can"></i>
                                                <?= substr(htmlspecialchars($element['traitement_surface']), 0, 8) ?><?= strlen($element['traitement_surface']) > 8 ? '...' : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['usage_structural'])): ?>
                                            <span class="badge-usage">
                                                <i class="fas fa-building"></i>
                                                <?= substr(htmlspecialchars($element['usage_structural']), 0, 8) ?><?= strlen($element['usage_structural']) > 8 ? '...' : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['classe_exposition'])): ?>
                                            <span class="badge-exposition">
                                                <i class="fas fa-shield-alt"></i>
                                                <?= htmlspecialchars($element['classe_exposition']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['poids_lineique']) && $element['poids_lineique'] > 0): ?>
                                            <span class="badge-diametre">
                                                <i class="fas fa-weight"></i>
                                                <?= number_format($element['poids_lineique'], 3) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['resistance_traction']) && $element['resistance_traction'] > 0): ?>
                                            <span class="badge-nuance">
                                                <?= number_format($element['resistance_traction'], 0) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['limite_elastique']) && $element['limite_elastique'] > 0): ?>
                                            <span class="badge-nuance">
                                                <?= number_format($element['limite_elastique'], 0) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['allongement']) && $element['allongement'] > 0): ?>
                                            <span class="badge-forme">
                                                <?= number_format($element['allongement'], 1) ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            <?= number_format($element['total'], 0, ',', ' ') ?> FCFA
                                        </strong>
                                        
                                        <!-- Calcul poids si possible -->
                                        <?php if ($element['poids_lineique'] > 0 && $element['longueur'] > 0): ?>
                                            <br><small class="text-muted">
                                                <i class="fas fa-weight"></i>
                                                <?= number_format($element['poids_lineique'] * $element['longueur'] * $element['quantite'], 1) ?> kg
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-plus"></i>
                                            <?= $element['date_creation_fr'] ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="ferraillage.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&action=modifier&element_id=<?= $element['id'] ?>" 
                                               class="btn btn-sm btn-warning rebar-bend" 
                                               title="Modifier cet élément">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmerSuppression(<?= $element['id'] ?>, '<?= htmlspecialchars($element['designation'], ENT_QUOTES) ?>')"
                                                    title="Supprimer cet élément">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-info weld-spark" 
                                                    onclick="calculerElementDetaille(<?= $element['id'] ?>)"
                                                    title="Calculer cet élément">
                                                <i class="fas fa-calculator"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-success galvanized-coating" 
                                                    onclick="genererFicheTechnique(<?= $element['id'] ?>)"
                                                    title="Fiche technique">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== FONCTIONS JAVASCRIPT TABLEAU ===== -->
        <script>
            function highlightElement(row) {
                row.style.background = 'rgba(52, 73, 94, 0.1)';
                row.style.transform = 'scale(1.01)';
                row.style.transition = 'all 0.2s ease';
            }
            
            function unhighlightElement(row) {
                row.style.background = '';
                row.style.transform = 'scale(1)';
            }
            
            function exporterTableauExcel() {
                showNotification('📊 Export Excel du tableau ferraillage en cours...', 'info');
                // Logique d'export Excel
            }
            
            function genererBonCommande() {
                showNotification('🛒 Génération du bon de commande ferraillage...', 'success');
                // Logique bon de commande
            }
            
            function calculerElementDetaille(elementId) {
                const elements = <?= json_encode($elements_ferraillage) ?>;
                const element = elements.find(el => el.id == elementId);
                
                if (element && element.diametre && element.longueur) {
                    const section = Math.PI * Math.pow(element.diametre/2, 2) / 100; // cm²
                    const poids = element.poids_lineique * element.longueur * element.quantite;
                    const volume = section * element.longueur * element.quantite / 10000; // m³
                    
                    const calculs = `
🔧 CALCULS DÉTAILLÉS :

📏 Section : ${section.toFixed(2)} cm²
⚖️ Poids total : ${poids.toFixed(1)} kg
📦 Volume acier : ${volume.toFixed(4)} m³
💰 Prix au kg : ${(element.total/poids).toFixed(0)} FCFA/kg

📊 Recouvrement recommandé : ${(element.diametre * 40).toFixed(0)}mm
📐 Ancrage minimal : ${(element.diametre * 25).toFixed(0)}mm
                    `;
                    
                    showNotification(calculs, 'info', 8000);
                }
            }
        </script>
        
<!-- ===== SECTION ACTIONS RAPIDES ===== -->
        <div class="row mb-4 fade-in-up">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--primary-orange) !important;">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-chart-pie fa-2x text-primary"></i>
                        </div>
                        <h5 class="card-title">Récapitulatif Global</h5>
                        <p class="card-text text-muted">Voir tous les totaux par module</p>
                        <a href="recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Consulter
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--accent-green) !important;">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-copy fa-2x text-success"></i>
                        </div>
                        <h5 class="card-title">Dupliquer Devis</h5>
                        <p class="card-text text-muted">Créer une copie de ce devis</p>
                        <a href="dupliquer_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-clone"></i> Dupliquer
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--accent-blue) !important;">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-print fa-2x text-info"></i>
                        </div>
                        <h5 class="card-title">Impression PDF</h5>
                        <p class="card-text text-muted">Générer le devis en PDF</p>
                        <a href="impression_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                           class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-file-pdf"></i> Générer
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--neutral-gray) !important;">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-history fa-2x text-secondary"></i>
                        </div>
                        <h5 class="card-title">Historique Complet</h5>
                        <p class="card-text text-muted">Toutes les modifications</p>
                        <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=ferraillage" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-clock"></i> Voir tout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TOTAUX MODULE FERRAILLAGE ===== -->
        <div class="module-summary fade-in-up">
            <h3>
                <i class="fas fa-industry"></i>
                Total Module Ferraillage
            </h3>
            <div class="total-amount pulse-animation">
                <?= number_format($total_module, 0, ',', ' ') ?> FCFA
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Mis à jour automatiquement • <?= count($elements_ferraillage) ?> élément(s)
                <?php if ($total_module > 0 && count($elements_ferraillage) > 0): ?>
                    • Moyenne: <?= number_format($total_module / count($elements_ferraillage), 0, ',', ' ') ?> FCFA/élément
                    • Tonnage total estimé: <?= number_format(array_sum(array_column($elements_ferraillage, 'poids_total')) / 1000, 2) ?> tonnes
                <?php endif; ?>
            </small>
        </div>

    </div>

    <!-- ===== JAVASCRIPT SPÉCIALISÉ FERRAILLAGE ===== -->
    <script>
        // ===== CONFIGURATION ET VARIABLES FERRAILLAGE =====
        const PRIX_FERRAILLAGE = {
            // Barres d'armature (prix par tonne)
            'fer_rond_lisse': { base: 850000, factor: 1.0 },     // FeE215
            'fer_haute_adherence': { base: 950000, factor: 1.2 }, // FeE400
            'acier_inox': { base: 2800000, factor: 3.5 },        // Inoxydable
            'acier_galvanise': { base: 1200000, factor: 1.5 },   // Galvanisé
            
            // Treillis soudés (prix par m²)
            'treillis_st25': { base: 8500, factor: 1.0 },
            'treillis_st35': { base: 12000, factor: 1.4 },
            'treillis_st50': { base: 16000, factor: 1.9 },
            
            // Accessoires (prix unitaire)
            'fil_ligature': { base: 2500, factor: 1.0 },
            'cale_beton': { base: 450, factor: 1.0 },
            'espaceur': { base: 380, factor: 1.0 },
            'cadre_attente': { base: 1200, factor: 1.0 }
        };

        const POIDS_LINEAIRES = {
            // Poids au mètre linéaire (kg/ml)
            '6': 0.222,   '8': 0.395,   '10': 0.617,  '12': 0.888,
            '14': 1.208,  '16': 1.578,  '20': 2.466,  '25': 3.853,
            '32': 6.313,  '40': 9.865
        };

        const LONGUEURS_COMMERCIALES = [6, 8, 10, 12]; // mètres

        const RESISTANCES_ACIER = {
            'FeE215': { limite: 215, usage: 'Rond lisse, étriers' },
            'FeE400': { limite: 400, usage: 'Haute adhérence standard' },
            'FeE500': { limite: 500, usage: 'Haute adhérence renforcée' }
        };

        // ===== FONCTIONS SPÉCIALISÉES FERRAILLAGE =====

        /**
         * Calculer la masse d'armatures nécessaire
         */
        function calculerMasseArmatures() {
            const diametre = document.getElementById('calc_diametre').value;
            const longueur = parseFloat(document.getElementById('calc_longueur').value) || 0;
            const nombre = parseFloat(document.getElementById('calc_nombre').value) || 0;
            
            if (diametre && longueur > 0 && nombre > 0) {
                const poidsLineaire = POIDS_LINEAIRES[diametre] || 0;
                const masseTotale = poidsLineaire * longueur * nombre;
                const prixEstime = masseTotale * (PRIX_FERRAILLAGE.fer_haute_adherence.base / 1000);
                
                document.getElementById('quantite').value = masseTotale.toFixed(0);
                document.getElementById('unite').value = 'kg';
                
                showToast(`⚖️ Masse calculée: ${masseTotale.toFixed(1)} kg\n` +
                         `${nombre} barres Ø${diametre}mm × ${longueur}m\n` +
                         `Prix estimé: ${prixEstime.toLocaleString()} FCFA`, 'success');
            } else {
                quantiteField.value = '50';
                uniteField.value = 'kg';
                if (usageField) usageField.value = 'Divers';
            }
            
            // Estimation prix
            calculerEstimationFerraillage();
            
            // Animation
            designationField.style.background = 'linear-gradient(135deg, #fff3cd 0%, #ffffff 100%)';
            setTimeout(() => designationField.style.background = '', 1000);
            
            quantiteField.focus();
            quantiteField.select();
        }

        /**
         * Réinitialiser le formulaire
         */
        function resetFormulaire() {
            if (confirm('🗑️ Êtes-vous sûr de vouloir effacer tous les champs du formulaire ?')) {
                document.getElementById('formFerraillage').reset();
                document.getElementById('unite').value = 'kg';
                document.getElementById('designation').focus();
                showToast('✨ Formulaire réinitialisé !', 'info');
            }
        }

        /**
         * Confirmation de suppression
         */
        function confirmerSuppression(elementId, designation) {
            if (confirm(`⚠️ ATTENTION - Suppression définitive\n\n` +
                       `Êtes-vous sûr de vouloir supprimer cet élément ?\n\n` +
                       `🔧 Élément: ${designation}\n` +
                       `🔢 ID: ${elementId}\n\n` +
                       `Cette action est irréversible !`)) {
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'supprimer';
                
                const elementIdInput = document.createElement('input');
                elementIdInput.type = 'hidden';
                elementIdInput.name = 'element_id';
                elementIdInput.value = elementId;
                
                form.appendChild(actionInput);
                form.appendChild(elementIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        /**
         * Raccourcis clavier ferraillage
         */
        function initRaccourcisClavier() {
            document.addEventListener('keydown', function(e) {
                // Alt + D = Focus désignation
                if (e.altKey && e.key === 'd') {
                    e.preventDefault();
                    document.getElementById('designation').focus();
                    showToast('🎯 Focus sur Désignation', 'info');
                }
                
                // Alt + M = Calculer masse
                if (e.altKey && e.key === 'm') {
                    e.preventDefault();
                    calculerMasseArmatures();
                }
                
                // Alt + O = Optimiser longueurs
                if (e.altKey && e.key === 'o') {
                    e.preventDefault();
                    optimiserLongueurs();
                }
                
                // Alt + E = Calculer espacement
                if (e.altKey && e.key === 'e') {
                    e.preventDefault();
                    calculerEspacement();
                }
                
                // Alt + S = Calculer section acier
                if (e.altKey && e.key === 's') {
                    e.preventDefault();
                    calculerSectionAcier();
                }
                
                // Alt + A = Calculer ancrage
                if (e.altKey && e.key === 'a') {
                    e.preventDefault();
                    calculerAncrage();
                }
                
                // Alt + P = Estimation prix
                if (e.altKey && e.key === 'p') {
                    e.preventDefault();
                    calculerEstimationFerraillage();
                }
                
                // Alt + N = Vérifier normes
                if (e.altKey && e.key === 'n') {
                    e.preventDefault();
                    verifierNormes();
                }
                
                // Alt + T = Calculer poids total
                if (e.altKey && e.key === 't') {
                    e.preventDefault();
                    calculerPoidsTotal();
                }
                
                // Alt + F = Générer plan ferraillage
                if (e.altKey && e.key === 'f') {
                    e.preventDefault();
                    genererPlanFerraillage();
                }
                
                // Ctrl + Entrée = Soumettre formulaire
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('formFerraillage').submit();
                }
            });
        }

        /**
         * Afficher des notifications toast
         */
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'info' ? 'var(--accent-blue)' : type === 'warning' ? 'var(--steel-orange)' : type === 'success' ? 'var(--steel-primary)' : 'var(--accent-red)'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-medium);
                z-index: 9999;
                max-width: 400px;
                white-space: pre-line;
                animation: slideInRight 0.4s ease-out;
            `;
            
            const icon = type === 'info' ? '🔧' : type === 'warning' ? '⚠️' : type === 'success' ? '✅' : '❌';
            toast.innerHTML = `<strong>${icon}</strong> ${message}`;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.4s ease-out';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 400);
            }, 5000);
        }

        /**
         * Animation des éléments au scroll
         */
        function initAnimationsScroll() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });
            
            document.querySelectorAll('.fade-in-up').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'all 0.6s ease-out';
                observer.observe(el);
            });
        }

        /**
         * Validation temps réel
         */
        function initValidationTempsReel() {
            const diametreField = document.getElementById('diametre');
            const longueurField = document.getElementById('longueur');
            const quantiteField = document.getElementById('quantite');
            const prixField = document.getElementById('prix_unitaire');
            
            // Validation diamètre (6-40mm standard)
            if (diametreField) {
                diametreField.addEventListener('input', function() {
                    const value = parseInt(this.value);
                    if (value && (value < 6 || value > 40)) {
                        this.style.borderColor = 'var(--accent-red)';
                        this.title = 'Diamètres courants: 6, 8, 10, 12, 14, 16, 20, 25, 32, 40mm';
                    } else {
                        this.style.borderColor = '';
                        this.title = '';
                    }
                });
            }
            
            // Validation longueur
            if (longueurField) {
                longueurField.addEventListener('input', function() {
                    const value = parseFloat(this.value);
                    if (value && (value < 0 || value > 12)) {
                        this.style.borderColor = 'var(--accent-red)';
                        this.title = 'Longueurs commerciales: 6, 8, 10, 12 mètres';
                    } else {
                        this.style.borderColor = '';
                        this.title = '';
                    }
                });
            }
            
            // Calcul automatique du total
            function calculerTotal() {
                const quantite = parseFloat(quantiteField.value) || 0;
                const prix = parseFloat(prixField.value) || 0;
                const total = quantite * prix;
                
                if (total > 0) {
                    let totalElement = document.getElementById('total-preview');
                    if (!totalElement) {
                        const preview = document.createElement('div');
                        preview.id = 'total-preview';
                        preview.style.cssText = `
                            margin-top: 0.5rem;
                            padding: 0.5rem;
                            background: var(--steel-primary);
                            color: white;
                            border-radius: 4px;
                            font-weight: 600;
                            text-align: center;
                        `;
                        prixField.parentNode.appendChild(preview);
                        totalElement = preview;
                    }
                    totalElement.innerHTML = `
                        <i class="fas fa-calculator"></i> Total: ${total.toLocaleString()} FCFA
                    `;
                }
            }
            
            quantiteField.addEventListener('input', calculerTotal);
            prixField.addEventListener('input', calculerTotal);
        }

        // ===== INITIALISATION AU CHARGEMENT =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔧 Module Ferraillage GSN ProDevis360° initialisé');
            
            // Initialiser toutes les fonctionnalités
            initRaccourcisClavier();
            initAnimationsScroll();
            initValidationTempsReel();
            
            // Afficher les raccourcis clavier
            showToast(`⌨️ Raccourcis disponibles:\n` +
                     `Alt+D = Désignation\n` +
                     `Alt+M = Calculer masse\n` +
                     `Alt+O = Optimiser longueurs\n` +
                     `Alt+E = Espacement\n` +
                     `Alt+S = Section acier\n` +
                     `Alt+A = Ancrage\n` +
                     `Alt+P = Prix estimation\n` +
                     `Alt+N = Vérifier normes\n` +
                     `Alt+T = Poids total\n` +
                     `Alt+F = Plan ferraillage\n` +
                     `Ctrl+Entrée = Envoyer`, 'info');
            
            // Focus automatique
            const firstField = document.getElementById('designation');
            if (firstField && !firstField.value) {
                setTimeout(() => firstField.focus(), 500);
            }
            
            // Analyse des éléments existants
            const elements = <?= json_encode($elements_ferraillage) ?>;
            let alertesPoids = 0;
            let poidsTotal = 0;
            
            elements.forEach(element => {
                const quantite = parseFloat(element.quantite) || 0;
                const unite = element.unite || '';
                
                if (unite.includes('kg')) {
                    poidsTotal += quantite;
                } else if (unite.includes('tonne')) {
                    poidsTotal += quantite * 1000;
                }
                
                // Vérifier cohérence diamètre/usage
                const diametre = parseInt(element.diametre);
                const usage = element.usage;
                
                if (usage === 'Poteau' && diametre < 12) alertesPoids++;
                if (usage === 'Etrier' && diametre > 12) alertesPoids++;
            });
            
            if (alertesPoids > 0) {
                setTimeout(() => {
                    showToast(`⚠️ ${alertesPoids} élément(s) avec diamètre non standard détecté(s).\n` +
                             `Vérifiez la conformité aux normes BAEL/Eurocode`, 'warning');
                }, 2000);
            }
            
            if (poidsTotal > 0) {
                setTimeout(() => {
                    showToast(`📊 Poids total ferraillage: ${poidsTotal.toFixed(0)} kg\n` +
                             `(${(poidsTotal/1000).toFixed(2)} tonnes)`, 'info');
                }, 3000);
            }
        });

        // Styles CSS pour les animations toast
        const styleSheet = document.createElement('style');
        styleSheet.textContent = `
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes slideOutRight {
                from {
                    opacity: 1;
                    transform: translateX(0);
                }
                to {
                    opacity: 0;
                    transform: translateX(100%);
                }
            }
            
            .toast-notification {
                font-family: 'Inter', sans-serif;
                font-size: 0.9rem;
                line-height: 1.4;
                cursor: pointer;
            }
            
            .toast-notification:hover {
                transform: scale(1.02);
                transition: transform 0.2s ease;
            }
        `;
        document.head.appendChild(styleSheet);
    </script>

</body>
</html>