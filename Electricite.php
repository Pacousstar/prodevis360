<?php
// ===== ELECTRICITE.PHP - PARTIE 1 : PHP LOGIC & CONFIG =====
// VERSION UNIFORMISÉE GSN ProDevis360°
require_once 'functions.php';

// Configuration du module actuel
$current_module = 'electricite';

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

// Suggestions spécialisées pour l'électricité
$suggestions_electricite = [
    // PROTECTION ÉLECTRIQUE
    'Tableau électrique 3 rangées 36 modules',
    'Tableau électrique 4 rangées 48 modules',
    'Disjoncteur divisionnaire 10A courbe C',
    'Disjoncteur divisionnaire 16A courbe C',
    'Disjoncteur divisionnaire 20A courbe C',
    'Disjoncteur divisionnaire 25A courbe C',
    'Disjoncteur divisionnaire 32A courbe C',
    'Disjoncteur divisionnaire 40A courbe C',
    'Interrupteur différentiel 30mA 40A type AC',
    'Interrupteur différentiel 30mA 63A type AC',
    'Interrupteur différentiel 30mA 40A type A',
    'Parafoudre type 2 monophasé',
    'Parafoudre type 2 triphasé',
    
    // CÂBLAGE ET FILS
    'Câble H07V-U 1,5mm² bleu (neutre)',
    'Câble H07V-U 1,5mm² rouge (phase)',
    'Câble H07V-U 1,5mm² vert/jaune (terre)',
    'Câble H07V-U 2,5mm² bleu (neutre)',
    'Câble H07V-U 2,5mm² rouge (phase)',
    'Câble H07V-U 2,5mm² vert/jaune (terre)',
    'Câble H07V-U 6mm² bleu (neutre)',
    'Câble H07V-U 6mm² rouge (phase)',
    'Câble H07V-U 6mm² vert/jaune (terre)',
    'Câble H07V-R 10mm² phase',
    'Câble H07V-R 16mm² phase',
    'Câble RO2V 3G1,5mm²',
    'Câble RO2V 3G2,5mm²',
    'Câble RO2V 3G6mm²',
    
    // GAINES ET PROTECTION
    'Gaine ICTA Ø16mm avec tire-fil',
    'Gaine ICTA Ø20mm avec tire-fil',
    'Gaine ICTA Ø25mm avec tire-fil',
    'Gaine ICTA Ø32mm avec tire-fil',
    'Gaine IRL Ø16mm rigide',
    'Gaine IRL Ø20mm rigide',
    'Gaine IRL Ø25mm rigide',
    'Moulure électrique 20x12mm',
    'Moulure électrique 32x16mm',
    'Plinthes à câbles 80x20mm',
    'Goulottes 60x40mm',
    'Goulottes 80x60mm',
    
    // APPAREILLAGE ÉCLAIRAGE
    'Interrupteur simple 10A blanc',
    'Interrupteur simple 10A ivoire',
    'Interrupteur va-et-vient 10A blanc',
    'Interrupteur va-et-vient 10A ivoire',
    'Bouton poussoir 10A blanc',
    'Télérupteur 16A silencieux',
    'Minuterie escalier 2 minutes',
    'Détecteur de mouvement 360°',
    'Variateur LED 400W',
    'Variateur halogène 600W',
    
    // PRISES ET SOCLES
    'Prise de courant 16A 2P+T blanc',
    'Prise de courant 16A 2P+T ivoire',
    'Prise de courant 20A 2P+T blanc',
    'Prise de courant 32A 2P+T blanc',
    'Prise RJ45 catégorie 6',
    'Prise TV/SAT simple',
    'Prise USB double 2,4A',
    'Multiprise étanche IP44',
    'Socle DCL plafonnier',
    'Douille E27 avec cache',
    
    // ÉCLAIRAGE
    'Spot LED encastrable 7W blanc chaud',
    'Spot LED encastrable 12W blanc froid',
    'Plafonnier LED 24W dimmable',
    'Applique murale LED 10W',
    'Réglette LED 18W 60cm',
    'Réglette LED 36W 120cm',
    'Tube LED T8 18W 120cm',
    'Ampoule LED E27 9W blanc chaud',
    'Ampoule LED E14 6W blanc chaud',
    'Projecteur LED extérieur 50W',
    'Hublot LED étanche 15W',
    
    // ÉQUIPEMENTS SPÉCIALISÉS
    'Chauffe-eau électrique 200L',
    'Chauffe-eau électrique 300L',
    'Convecteur électrique 1000W',
    'Convecteur électrique 1500W',
    'Convecteur électrique 2000W',
    'Radiateur sèche-serviettes 750W',
    'VMC simple flux autoréglable',
    'VMC double flux thermodynamique',
    'Interphone audio 2 fils',
    'Visiophone couleur 4 fils',
    'Sonnette sans fil',
    'Carillon 2 tons',
    
    // MISE À LA TERRE
    'Piquet de terre 1,5m acier galvanisé',
    'Barrette de coupure terre',
    'Conducteur de terre nu 25mm²',
    'Collier de prise de terre',
    'Borne de raccordement terre',
    'Répartiteur de terre 4 départs'
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
            $unite = trim($_POST['unite'] ?? 'unité');
            $prix_unitaire = floatval($_POST['prix_unitaire'] ?? 0);
            $amperage = trim($_POST['amperage'] ?? '');
            $section_cable = trim($_POST['section_cable'] ?? '');
            $type_protection = trim($_POST['type_protection'] ?? '');
            $norme_electrique = trim($_POST['norme_electrique'] ?? '');
            
            // Validations spécifiques électricité
            if (empty($designation)) {
                throw new Exception("La désignation est obligatoire.");
            }
            if ($quantite <= 0) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }
            if ($prix_unitaire < 0) {
                throw new Exception("Le prix unitaire ne peut pas être négatif.");
            }
            
            // Validation ampérage si fourni
            if (!empty($amperage) && !preg_match('/^\d+A?$/i', $amperage)) {
                throw new Exception("Format d'ampérage invalide (ex: 16A, 20A).");
            }
            
            // Validation section câble si fournie
            if (!empty($section_cable) && !preg_match('/^\d+([.,]\d+)?mm²?$/i', $section_cable)) {
                throw new Exception("Format de section invalide (ex: 1.5mm², 2.5mm²).");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Insertion en base
            $stmt = $conn->prepare("
                INSERT INTO electricite (
                    projet_id, devis_id, designation, quantite, unite, 
                    prix_unitaire, total, amperage, section_cable, 
                    type_protection, norme_electrique, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param(
                "iisdssdssss", 
                $projet_id, $devis_id, $designation, $quantite, $unite,
                $prix_unitaire, $total, $amperage, $section_cable,
                $type_protection, $norme_electrique
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'electricite');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'electricite', 'Ajout', "Élément ajouté : {$designation}");
                
                $message = "Élément électrique ajouté avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de l'ajout : " . $conn->error);
            }
            
        } elseif ($action == 'modifier' && $element_id > 0) {
            // Récupération et validation des données
            $designation = trim($_POST['designation'] ?? '');
            $quantite = floatval($_POST['quantite'] ?? 0);
            $unite = trim($_POST['unite'] ?? 'unité');
            $prix_unitaire = floatval($_POST['prix_unitaire'] ?? 0);
            $amperage = trim($_POST['amperage'] ?? '');
            $section_cable = trim($_POST['section_cable'] ?? '');
            $type_protection = trim($_POST['type_protection'] ?? '');
            $norme_electrique = trim($_POST['norme_electrique'] ?? '');
            
            // Mêmes validations que pour l'ajout
            if (empty($designation)) {
                throw new Exception("La désignation est obligatoire.");
            }
            if ($quantite <= 0) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }
            if ($prix_unitaire < 0) {
                throw new Exception("Le prix unitaire ne peut pas être négatif.");
            }
            
            if (!empty($amperage) && !preg_match('/^\d+A?$/i', $amperage)) {
                throw new Exception("Format d'ampérage invalide (ex: 16A, 20A).");
            }
            
            if (!empty($section_cable) && !preg_match('/^\d+([.,]\d+)?mm²?$/i', $section_cable)) {
                throw new Exception("Format de section invalide (ex: 1.5mm², 2.5mm²).");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Mise à jour en base
            $stmt = $conn->prepare("
                UPDATE electricite SET 
                    designation = ?, quantite = ?, unite = ?, prix_unitaire = ?, 
                    total = ?, amperage = ?, section_cable = ?, type_protection = ?, 
                    norme_electrique = ?, date_modification = NOW()
                WHERE id = ? AND projet_id = ? AND devis_id = ?
            ");
            
            $stmt->bind_param(
                "sdsdssssiiii",
                $designation, $quantite, $unite, $prix_unitaire, $total,
                $amperage, $section_cable, $type_protection, $norme_electrique,
                $element_id, $projet_id, $devis_id
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'electricite');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'electricite', 'Modification', "Élément modifié : {$designation}");
                
                $message = "Élément électrique modifié avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de la modification : " . $conn->error);
            }
            
        } elseif ($action == 'supprimer' && $element_id > 0) {
            // Récupération de la désignation avant suppression
            $stmt_get = $conn->prepare("SELECT designation FROM electricite WHERE id = ? AND projet_id = ? AND devis_id = ?");
            $stmt_get->bind_param("iii", $element_id, $projet_id, $devis_id);
            $stmt_get->execute();
            $result_get = $stmt_get->get_result();
            $element_data = $result_get->fetch_assoc();
            
            if ($element_data) {
                // Suppression de l'élément
                $stmt = $conn->prepare("DELETE FROM electricite WHERE id = ? AND projet_id = ? AND devis_id = ?");
                $stmt->bind_param("iii", $element_id, $projet_id, $devis_id);
                
                if ($stmt->execute()) {
                    // Mise à jour du récapitulatif
                    updateRecapitulatif($projet_id, $devis_id, 'electricite');
                    
                    // Sauvegarde dans l'historique
                    sauvegarderHistorique($projet_id, $devis_id, 'electricite', 'Suppression', "Élément supprimé : {$element_data['designation']}");
                    
                    $message = "Élément électrique supprimé avec succès !";
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

// Récupération des éléments d'électricité pour affichage
$elements_electricite = [];
$total_module = 0;

$stmt = $conn->prepare("
    SELECT id, designation, quantite, unite, prix_unitaire, total,
           amperage, section_cable, type_protection, norme_electrique,
           DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation_fr,
           DATE_FORMAT(date_modification, '%d/%m/%Y %H:%i') as date_modification_fr
    FROM electricite 
    WHERE projet_id = ? AND devis_id = ? 
    ORDER BY date_creation DESC
");

$stmt->bind_param("ii", $projet_id, $devis_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $elements_electricite[] = $row;
    $total_module += $row['total'];
}

// Récupération de l'élément à modifier si nécessaire
$element_a_modifier = null;
if ($action == 'modifier' && $element_id > 0) {
    $stmt = $conn->prepare("
        SELECT * FROM electricite 
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
    <title>Électricité - <?= htmlspecialchars($projet_devis_info['nom_projet']) ?> | GSN ProDevis360°</title>
    
    <!-- Font Awesome 6.5.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ===== VARIABLES CSS GSN ProDevis360° ===== */
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
            
            /* Variables spécifiques électricité */
            --electric-yellow: #ffd700;
            --electric-blue: #4169e1;
            --safety-orange: #ff4500;
            --warning-amber: #ffc107;
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
            background: var(--electric-yellow);
            color: var(--neutral-dark);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
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

        /* ===== ALERTES SÉCURITÉ ÉLECTRIQUE ===== */
        .security-alerts {
            max-width: 1400px;
            margin: 1rem auto;
            padding: 0 1rem;
        }

        .alert-electric {
            background: linear-gradient(135deg, var(--warning-amber) 0%, #ffcd39 100%);
            color: var(--neutral-dark);
            padding: 1rem;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--safety-orange);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: var(--shadow-soft);
        }

        .alert-electric i {
            font-size: 1.5rem;
            color: var(--safety-orange);
        }

        .alert-electric-content h4 {
            margin-bottom: 0.5rem;
            color: var(--neutral-dark);
            font-weight: 600;
        }

        .alert-electric-content p {
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.4;
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
        }

        .form-section h2 {
            color: var(--primary-orange);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
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

        /* ===== SUGGESTIONS ÉLECTRICITÉ ===== */
        .suggestions-electricite {
            background: linear-gradient(135deg, var(--electric-blue) 0%, #5a7fff 100%);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .suggestions-electricite h4 {
            color: var(--secondary-white);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.5rem;
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
        }

        .suggestion-item:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-1px);
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
        }

        .table-header {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
            color: var(--secondary-white);
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }

        .table th {
            background: var(--neutral-light);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--neutral-dark);
            border-bottom: 2px solid var(--primary-orange);
            white-space: nowrap;
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
            background: rgba(255, 107, 53, 0.05);
        }

        .actions-cell {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        /* ===== BADGES SPÉCIALISÉS ===== */
        .badge-amperage {
            background: var(--electric-yellow);
            color: var(--neutral-dark);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-section {
            background: var(--electric-blue);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-protection {
            background: var(--safety-orange);
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
        }

        .module-summary h3 {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .total-amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--electric-yellow);
        }

        /* ===== RESPONSIVE DESIGN ===== */
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
        }

        /* ===== ANIMATIONS AVANCÉES ===== */
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

        /* ===== STYLES D'IMPRESSION ===== */
        @media print {
            .header-gsn,
            .navigation-modules,
            .security-alerts,
            .form-section,
            .btn,
            .actions-cell {
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
        }
    </style>
    
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
                        <i class="fas fa-bolt"></i>
                        Module Électricité
                        <span class="module-badge">
                            <i class="fas fa-lightning-bolt"></i>
                            Spécialisé
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
                    </div>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn-header">
                    <i class="fas fa-chart-pie"></i>
                    Récapitulatif
                </a>
                <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn-header">
                    <i class="fas fa-history"></i>
                    Historique
                </a>
                <a href="impression_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn-header">
                    <i class="fas fa-print"></i>
                    Imprimer
                </a>
            </div>
        </div>
    </header>

    <!-- ===== NAVIGATION MODULES DYNAMIQUE ===== -->
    <nav class="navigation-modules">
        <div class="nav-container">
            <div class="nav-modules">
                <?php foreach ($modules_config as $module_key => $module_info): ?>
                    <a href="<?= $module_key ?>.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                       class="nav-item <?= $module_key === $current_module ? 'active' : '' ?>"
                       style="<?= $module_key === $current_module ? '' : '--hover-color: ' . $module_info['color'] ?>">
                        <i class="<?= $module_info['icon'] ?>"></i>
                        <span><?= $module_info['name'] ?></span>
                        <?php if ($module_key === $current_module): ?>
                            <i class="fas fa-check-circle ml-1"></i>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <!-- ===== ALERTES SÉCURITÉ ÉLECTRIQUE ===== -->
    <div class="security-alerts">
        <div class="alert-electric">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="alert-electric-content">
                <h4><i class="fas fa-hard-hat"></i> Sécurité Électrique - Normes NF C 15-100</h4>
                <p>
                    <strong>Rappel important :</strong> Tous les travaux électriques doivent respecter la norme NF C 15-100. 
                    Vérifiez les sections de câbles, ampérages des protections et mise à la terre. 
                    <strong>Coupez l'alimentation</strong> avant toute intervention !
                </p>
            </div>
        </div>
    </div>
    
<!-- ===== CONTAINER PRINCIPAL ===== -->
    <div class="container">
        
        <!-- ===== MESSAGES D'ALERTE ===== -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> fade-in-up">
                <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- ===== FORMULAIRE ÉLECTRICITÉ ===== -->
        <div class="form-section fade-in-up">
            <h2>
                <i class="fas fa-<?= $element_a_modifier ? 'edit' : 'plus-circle' ?>"></i>
                <?= $element_a_modifier ? 'Modifier l\'élément électrique' : 'Ajouter un élément électrique' ?>
            </h2>

            <!-- Suggestions Électricité -->
            <div class="suggestions-electricite">
                <h4>
                    <i class="fas fa-lightbulb"></i>
                    Suggestions Électriques Spécialisées
                    <small>(Cliquez pour remplir automatiquement)</small>
                </h4>
                <div class="suggestions-grid">
                    <?php foreach ($suggestions_electricite as $suggestion): ?>
                        <div class="suggestion-item" onclick="remplirSuggestion('<?= htmlspecialchars($suggestion, ENT_QUOTES) ?>')">
                            <?= htmlspecialchars($suggestion) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <form method="POST" action="" id="formElectricite">
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
                        </label>
                        <input type="text" 
                               id="designation" 
                               name="designation" 
                               value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['designation']) : '' ?>"
                               placeholder="Ex: Disjoncteur 16A courbe C"
                               required
                               maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="quantite">
                            <i class="fas fa-calculator"></i>
                            Quantité <span class="required">*</span>
                        </label>
                        <input type="number" 
                               id="quantite" 
                               name="quantite" 
                               value="<?= $element_a_modifier ? $element_a_modifier['quantite'] : '' ?>"
                               placeholder="Ex: 5"
                               step="0.01"
                               min="0.01"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="unite">
                            <i class="fas fa-ruler"></i>
                            Unité
                        </label>
                        <select id="unite" name="unite">
                            <option value="unité" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'unité') ? 'selected' : '' ?>>Unité</option>
                            <option value="ml" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'ml') ? 'selected' : '' ?>>Mètre linéaire (ml)</option>
                            <option value="m²" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm²') ? 'selected' : '' ?>>Mètre carré (m²)</option>
                            <option value="point" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'point') ? 'selected' : '' ?>>Point</option>
                            <option value="circuit" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'circuit') ? 'selected' : '' ?>>Circuit</option>
                            <option value="tableau" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'tableau') ? 'selected' : '' ?>>Tableau</option>
                            <option value="coffret" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'coffret') ? 'selected' : '' ?>>Coffret</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="prix_unitaire">
                            <i class="fas fa-euro-sign"></i>
                            Prix unitaire (FCFA) <span class="required">*</span>
                        </label>
                        <input type="number" 
                               id="prix_unitaire" 
                               name="prix_unitaire" 
                               value="<?= $element_a_modifier ? $element_a_modifier['prix_unitaire'] : '' ?>"
                               placeholder="Ex: 15000"
                               step="0.01"
                               min="0"
                               required>
                    </div>
                </div>

                <!-- Ligne 2 : Spécifications électriques -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="amperage">
                            <i class="fas fa-lightning-bolt"></i>
                            Ampérage
                        </label>
                        <input type="text" 
                               id="amperage" 
                               name="amperage" 
                               value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['amperage']) : '' ?>"
                               placeholder="Ex: 16A, 20A, 32A"
                               pattern="^[0-9]+A?$"
                               title="Format: 16A ou 16">
                    </div>

                    <div class="form-group">
                        <label for="section_cable">
                            <i class="fas fa-wire"></i>
                            Section câble
                        </label>
                        <input type="text" 
                               id="section_cable" 
                               name="section_cable" 
                               value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['section_cable']) : '' ?>"
                               placeholder="Ex: 1.5mm², 2.5mm², 6mm²"
                               pattern="^[0-9]+([.,][0-9]+)?mm²?$"
                               title="Format: 1.5mm² ou 2.5mm²">
                    </div>

                    <div class="form-group">
                        <label for="type_protection">
                            <i class="fas fa-shield-alt"></i>
                            Type de protection
                        </label>
                        <select id="type_protection" name="type_protection">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Disjoncteur" <?= ($element_a_modifier && $element_a_modifier['type_protection'] === 'Disjoncteur') ? 'selected' : '' ?>>Disjoncteur</option>
                            <option value="Différentiel" <?= ($element_a_modifier && $element_a_modifier['type_protection'] === 'Différentiel') ? 'selected' : '' ?>>Interrupteur différentiel</option>
                            <option value="Parafoudre" <?= ($element_a_modifier && $element_a_modifier['type_protection'] === 'Parafoudre') ? 'selected' : '' ?>>Parafoudre</option>
                            <option value="Télérupteur" <?= ($element_a_modifier && $element_a_modifier['type_protection'] === 'Télérupteur') ? 'selected' : '' ?>>Télérupteur</option>
                            <option value="Minuterie" <?= ($element_a_modifier && $element_a_modifier['type_protection'] === 'Minuterie') ? 'selected' : '' ?>>Minuterie</option>
                            <option value="Contacteur" <?= ($element_a_modifier && $element_a_modifier['type_protection'] === 'Contacteur') ? 'selected' : '' ?>>Contacteur</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="norme_electrique">
                            <i class="fas fa-certificate"></i>
                            Norme électrique
                        </label>
                        <select id="norme_electrique" name="norme_electrique">
                            <option value="">-- Sélectionnez --</option>
                            <option value="NF C 15-100" <?= ($element_a_modifier && $element_a_modifier['norme_electrique'] === 'NF C 15-100') ? 'selected' : '' ?>>NF C 15-100 (Résidentiel)</option>
                            <option value="NF C 13-200" <?= ($element_a_modifier && $element_a_modifier['norme_electrique'] === 'NF C 13-200') ? 'selected' : '' ?>>NF C 13-200 (Tertiaire)</option>
                            <option value="NF C 14-100" <?= ($element_a_modifier && $element_a_modifier['norme_electrique'] === 'NF C 14-100') ? 'selected' : '' ?>>NF C 14-100 (Industriel)</option>
                            <option value="IP44" <?= ($element_a_modifier && $element_a_modifier['norme_electrique'] === 'IP44') ? 'selected' : '' ?>>IP44 (Protection)</option>
                            <option value="IP65" <?= ($element_a_modifier && $element_a_modifier['norme_electrique'] === 'IP65') ? 'selected' : '' ?>>IP65 (Étanche)</option>
                            <option value="CE" <?= ($element_a_modifier && $element_a_modifier['norme_electrique'] === 'CE') ? 'selected' : '' ?>>Marquage CE</option>
                        </select>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="form-row">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-<?= $element_a_modifier ? 'save' : 'plus' ?>"></i>
                            <?= $element_a_modifier ? 'Modifier l\'élément' : 'Ajouter l\'élément' ?>
                        </button>
                        
                        <?php if ($element_a_modifier): ?>
                            <a href="electricite.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-info ml-2" onclick="calculerEstimation()" title="Alt+E">
                            <i class="fas fa-calculator"></i>
                            Estimation auto
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ===== TABLEAU DES ÉLÉMENTS ÉLECTRICITÉ ===== -->
        <div class="table-container fade-in-up">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list"></i>
                    Éléments électriques
                    <span class="badge-amperage ml-2"><?= count($elements_electricite) ?> élément(s)</span>
                </h3>
                <div class="table-actions">
                    <span class="total-amount">
                        <i class="fas fa-bolt"></i>
                        <?= number_format($total_module, 0, ',', ' ') ?> FCFA
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> #</th>
                            <th><i class="fas fa-tag"></i> Désignation</th>
                            <th><i class="fas fa-calculator"></i> Qté</th>
                            <th><i class="fas fa-ruler"></i> Unité</th>
                            <th><i class="fas fa-euro-sign"></i> Prix Unit.</th>
                            <th><i class="fas fa-lightning-bolt"></i> Ampérage</th>
                            <th><i class="fas fa-wire"></i> Section</th>
                            <th><i class="fas fa-shield-alt"></i> Protection</th>
                            <th><i class="fas fa-certificate"></i> Norme</th>
                            <th><i class="fas fa-euro-sign"></i> Total</th>
                            <th><i class="fas fa-calendar"></i> Créé le</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($elements_electricite)): ?>
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <p>Aucun élément électrique ajouté pour ce devis.</p>
                                        <small>Utilisez le formulaire ci-dessus pour ajouter des éléments.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($elements_electricite as $element): ?>
                                <tr>
                                    <td><strong><?= $counter++ ?></strong></td>
                                    <td>
                                        <strong><?= htmlspecialchars($element['designation']) ?></strong>
                                        <?php if ($element['date_modification_fr']): ?>
                                            <br><small class="text-muted">
                                                <i class="fas fa-edit"></i> Modifié le <?= $element['date_modification_fr'] ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= number_format($element['quantite'], 2, ',', ' ') ?></strong></td>
                                    <td><span class="badge-section"><?= htmlspecialchars($element['unite']) ?></span></td>
                                    <td><strong><?= number_format($element['prix_unitaire'], 0, ',', ' ') ?></strong> FCFA</td>
                                    <td>
                                        <?php if (!empty($element['amperage'])): ?>
                                            <span class="badge-amperage">
                                                <i class="fas fa-lightning-bolt"></i>
                                                <?= htmlspecialchars($element['amperage']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['section_cable'])): ?>
                                            <span class="badge-section">
                                                <i class="fas fa-wire"></i>
                                                <?= htmlspecialchars($element['section_cable']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['type_protection'])): ?>
                                            <span class="badge-protection">
                                                <i class="fas fa-shield-alt"></i>
                                                <?= htmlspecialchars($element['type_protection']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['norme_electrique'])): ?>
                                            <small class="badge badge-info">
                                                <?= htmlspecialchars($element['norme_electrique']) ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            <?= number_format($element['total'], 0, ',', ' ') ?> FCFA
                                        </strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-plus"></i>
                                            <?= $element['date_creation_fr'] ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="electricite.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&action=modifier&element_id=<?= $element['id'] ?>" 
                                               class="btn btn-sm btn-warning" 
                                               title="Modifier cet élément">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmerSuppression(<?= $element['id'] ?>, '<?= htmlspecialchars($element['designation'], ENT_QUOTES) ?>')"
                                                    title="Supprimer cet élément">
                                                <i class="fas fa-trash"></i>
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
                        <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=electricite" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-clock"></i> Voir tout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TOTAUX MODULE ÉLECTRICITÉ ===== -->
        <div class="module-summary fade-in-up">
            <h3>
                <i class="fas fa-bolt"></i>
                Total Module Électricité
            </h3>
            <div class="total-amount pulse-animation">
                <?= number_format($total_module, 0, ',', ' ') ?> FCFA
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Mis à jour automatiquement • <?= count($elements_electricite) ?> élément(s)
                <?php if ($total_module > 0): ?>
                    • Moyenne: <?= number_format($total_module / max(1, count($elements_electricite)), 0, ',', ' ') ?> FCFA/élément
                <?php endif; ?>
            </small>
        </div>

    </div>

    <!-- ===== JAVASCRIPT SPÉCIALISÉ ÉLECTRICITÉ ===== -->
    <script>
        // ===== CONFIGURATION ET VARIABLES =====
        const PRIX_ELECTRICITE = {
            // Protection électrique
            'disjoncteur': { base: 8000, factor: 1.2 },
            'différentiel': { base: 25000, factor: 1.5 },
            'parafoudre': { base: 45000, factor: 1.0 },
            'tableau': { base: 150000, factor: 2.0 },
            
            // Câblage
            'cable': { base: 1500, factor: 0.8 }, // par mètre
            'gaine': { base: 800, factor: 0.5 },
            
            // Appareillage
            'interrupteur': { base: 3500, factor: 1.0 },
            'prise': { base: 4000, factor: 1.1 },
            'télérupteur': { base: 18000, factor: 1.3 },
            
            // Éclairage
            'spot': { base: 12000, factor: 1.4 },
            'plafonnier': { base: 25000, factor: 1.8 },
            'applique': { base: 15000, factor: 1.2 },
            'led': { base: 8000, factor: 1.0 }
        };

        // ===== FONCTIONS UTILITAIRES =====
        
        /**
         * Remplir automatiquement le formulaire avec une suggestion
         */
        function remplirSuggestion(suggestion) {
            const designationField = document.getElementById('designation');
            const quantiteField = document.getElementById('quantite');
            const uniteField = document.getElementById('unite');
            const prixField = document.getElementById('prix_unitaire');
            const amperageField = document.getElementById('amperage');
            const sectionField = document.getElementById('section_cable');
            const protectionField = document.getElementById('type_protection');
            const normeField = document.getElementById('norme_electrique');
            
            // Remplir la désignation
            designationField.value = suggestion;
            
            // Estimation automatique basée sur le type d'élément
            let estimation = estimerPrixElectrique(suggestion);
            
            // Remplir les champs selon le type
            if (suggestion.toLowerCase().includes('disjoncteur')) {
                const amperage = suggestion.match(/(\d+)A/);
                if (amperage) {
                    amperageField.value = amperage[1] + 'A';
                }
                protectionField.value = 'Disjoncteur';
                normeField.value = 'NF C 15-100';
                quantiteField.value = '1';
                uniteField.value = 'unité';
                
            } else if (suggestion.toLowerCase().includes('différentiel')) {
                const amperage = suggestion.match(/(\d+)A/);
                if (amperage) {
                    amperageField.value = amperage[1] + 'A';
                }
                protectionField.value = 'Différentiel';
                normeField.value = 'NF C 15-100';
                quantiteField.value = '1';
                uniteField.value = 'unité';
                
            } else if (suggestion.toLowerCase().includes('câble')) {
                const section = suggestion.match(/(\d+[.,]?\d*)mm²/);
                if (section) {
                    sectionField.value = section[1] + 'mm²';
                }
                quantiteField.value = '10';
                uniteField.value = 'ml';
                normeField.value = 'NF C 15-100';
                
            } else if (suggestion.toLowerCase().includes('gaine')) {
                quantiteField.value = '10';
                uniteField.value = 'ml';
                
            } else if (suggestion.toLowerCase().includes('prise')) {
                const amperage = suggestion.match(/(\d+)A/);
                if (amperage) {
                    amperageField.value = amperage[1] + 'A';
                }
                quantiteField.value = '1';
                uniteField.value = 'point';
                normeField.value = 'NF C 15-100';
                
            } else if (suggestion.toLowerCase().includes('interrupteur')) {
                amperageField.value = '10A';
                quantiteField.value = '1';
                uniteField.value = 'point';
                normeField.value = 'NF C 15-100';
                
            } else if (suggestion.toLowerCase().includes('tableau')) {
                const modules = suggestion.match(/(\d+)\s*modules?/);
                if (modules) {
                    amperageField.value = modules[1] + 'A';
                }
                protectionField.value = 'Disjoncteur';
                normeField.value = 'NF C 15-100';
                quantiteField.value = '1';
                uniteField.value = 'tableau';
                
            } else if (suggestion.toLowerCase().includes('spot') || suggestion.toLowerCase().includes('led')) {
                quantiteField.value = '4';
                uniteField.value = 'point';
                normeField.value = 'CE';
                
            } else {
                quantiteField.value = '1';
                uniteField.value = 'unité';
            }
            
            // Appliquer l'estimation de prix
            if (estimation > 0) {
                prixField.value = estimation;
            }
            
            // Animation visuelle
            designationField.style.background = 'linear-gradient(135deg, #fff3cd 0%, #ffffff 100%)';
            setTimeout(() => {
                designationField.style.background = '';
            }, 1000);
            
            // Focus sur le champ quantité pour faciliter la modification
            quantiteField.focus();
            quantiteField.select();
        }

        /**
         * Estimation automatique des prix électriques
         */
        function estimerPrixElectrique(designation) {
            const des = designation.toLowerCase();
            let prix = 0;
            
            // Analyse par mots-clés
            for (const [type, config] of Object.entries(PRIX_ELECTRICITE)) {
                if (des.includes(type)) {
                    prix = config.base;
                    
                    // Facteurs multiplicateurs selon caractéristiques
                    if (des.includes('triphasé')) prix *= 1.8;
                    if (des.includes('étanche') || des.includes('ip65')) prix *= 1.4;
                    if (des.includes('dimmable') || des.includes('variateur')) prix *= 1.3;
                    if (des.includes('wifi') || des.includes('connecté')) prix *= 2.0;
                    
                    // Facteurs selon ampérage
                    const amperage = des.match(/(\d+)a/);
                    if (amperage) {
                        const amp = parseInt(amperage[1]);
                        if (amp >= 63) prix *= 2.5;
                        else if (amp >= 40) prix *= 2.0;
                        else if (amp >= 32) prix *= 1.6;
                        else if (amp >= 25) prix *= 1.4;
                        else if (amp >= 20) prix *= 1.2;
                    }
                    
                    // Facteurs selon puissance
                    const wattage = des.match(/(\d+)w/);
                    if (wattage) {
                        const watts = parseInt(wattage[1]);
                        if (watts >= 100) prix *= 1.8;
                        else if (watts >= 50) prix *= 1.4;
                        else if (watts >= 20) prix *= 1.2;
                    }
                    
                    break;
                }
            }
            
            return Math.round(prix);
        }

        /**
         * Calculer estimation automatique pour le formulaire actuel
         */
        function calculerEstimation() {
            const designation = document.getElementById('designation').value;
            const quantite = parseFloat(document.getElementById('quantite').value) || 1;
            
            if (!designation.trim()) {
                alert('⚠️ Veuillez d\'abord saisir une désignation pour l\'estimation.');
                document.getElementById('designation').focus();
                return;
            }
            
            const prixUnitaire = estimerPrixElectrique(designation);
            
            if (prixUnitaire > 0) {
                document.getElementById('prix_unitaire').value = prixUnitaire;
                
                // Animation de confirmation
                const prixField = document.getElementById('prix_unitaire');
                prixField.style.background = 'linear-gradient(135deg, #d1ecf1 0%, #ffffff 100%)';
                setTimeout(() => {
                    prixField.style.background = '';
                }, 1500);
                
                // Afficher le total estimé
                const total = prixUnitaire * quantite;
                if (total > 0) {
                    showToast(`💡 Prix estimé: ${prixUnitaire.toLocaleString()} FCFA/unité\n📊 Total: ${total.toLocaleString()} FCFA`, 'info');
                }
            } else {
                showToast('❓ Impossible d\'estimer le prix pour cet élément.\nVeuillez saisir manuellement.', 'warning');
                document.getElementById('prix_unitaire').focus();
            }
        }

        /**
         * Confirmation de suppression avec détails
         */
        function confirmerSuppression(elementId, designation) {
            if (confirm(`⚠️ ATTENTION - Suppression définitive\n\n` +
                       `Êtes-vous sûr de vouloir supprimer cet élément ?\n\n` +
                       `📋 Élément: ${designation}\n` +
                       `🔢 ID: ${elementId}\n\n` +
                       `Cette action est irréversible !`)) {
                
                // Créer un formulaire de suppression dynamique
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
                
                // Soumettre le formulaire
                form.submit();
            }
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
                background: ${type === 'info' ? 'var(--accent-blue)' : type === 'warning' ? 'var(--warning-amber)' : 'var(--accent-green)'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-medium);
                z-index: 9999;
                max-width: 400px;
                white-space: pre-line;
                animation: slideInRight 0.4s ease-out;
            `;
            
            const icon = type === 'info' ? '💡' : type === 'warning' ? '⚠️' : '✅';
            toast.innerHTML = `<strong>${icon}</strong> ${message}`;
            
            document.body.appendChild(toast);
            
            // Supprimer automatiquement après 5 secondes
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
         * Validation en temps réel des champs électriques
         */
        function validerChampsElectriques() {
            const amperageField = document.getElementById('amperage');
            const sectionField = document.getElementById('section_cable');
            const quantiteField = document.getElementById('quantite');
            const prixField = document.getElementById('prix_unitaire');
            
            // Validation ampérage
            amperageField.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && !value.match(/^\d+A?$/i)) {
                    this.style.borderColor = 'var(--accent-red)';
                    this.title = 'Format invalide. Exemples: 16A, 20A, 32A';
                } else {
                    this.style.borderColor = '';
                    this.title = '';
                }
            });
            
            // Validation section câble
            sectionField.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && !value.match(/^\d+([.,]\d+)?mm²?$/i)) {
                    this.style.borderColor = 'var(--accent-red)';
                    this.title = 'Format invalide. Exemples: 1.5mm², 2.5mm², 6mm²';
                } else {
                    this.style.borderColor = '';
                    this.title = '';
                }
            });
            
            // Alertes cohérence ampérage/section
            function verifierCoherence() {
                const amperage = amperageField.value.replace(/[^\d]/g, '');
                const section = sectionField.value.replace(/[^\d.,]/g, '').replace(',', '.');
                
                if (amperage && section) {
                    const amp = parseInt(amperage);
                    const sec = parseFloat(section);
                    
                    let alerte = '';
                    if (amp <= 16 && sec > 2.5) alerte = '⚠️ Section surdimensionnée pour cet ampérage';
                    else if (amp >= 20 && sec < 2.5) alerte = '⚠️ Section sous-dimensionnée pour cet ampérage';
                    else if (amp >= 32 && sec < 6) alerte = '⚠️ Section probablement insuffisante';
                    
                    if (alerte) {
                        showToast(alerte, 'warning');
                    }
                }
            }
            
            amperageField.addEventListener('blur', verifierCoherence);
            sectionField.addEventListener('blur', verifierCoherence);
            
            // Calcul automatique du total
            function calculerTotal() {
                const quantite = parseFloat(quantiteField.value) || 0;
                const prix = parseFloat(prixField.value) || 0;
                const total = quantite * prix;
                
                if (total > 0) {
                    const totalElement = document.getElementById('total-preview');
                    if (!totalElement) {
                        const preview = document.createElement('div');
                        preview.id = 'total-preview';
                        preview.style.cssText = `
                            margin-top: 0.5rem;
                            padding: 0.5rem;
                            background: var(--accent-green);
                            color: white;
                            border-radius: 4px;
                            font-weight: 600;
                            text-align: center;
                        `;
                        prixField.parentNode.appendChild(preview);
                    }
                    document.getElementById('total-preview').innerHTML = `
                        <i class="fas fa-calculator"></i> Total: ${total.toLocaleString()} FCFA
                    `;
                }
            }
            
            quantiteField.addEventListener('input', calculerTotal);
            prixField.addEventListener('input', calculerTotal);
        }

        /**
         * Raccourcis clavier spécialisés
         */
        function initRaccourcisClavier() {
            document.addEventListener('keydown', function(e) {
                // Alt + D = Focus désignation
                if (e.altKey && e.key === 'd') {
                    e.preventDefault();
                    document.getElementById('designation').focus();
                    showToast('🎯 Focus sur Désignation', 'info');
                }
                
                // Alt + Q = Focus quantité
                if (e.altKey && e.key === 'q') {
                    e.preventDefault();
                    document.getElementById('quantite').focus();
                    showToast('🔢 Focus sur Quantité', 'info');
                }
                
                // Alt + P = Focus prix
                if (e.altKey && e.key === 'p') {
                    e.preventDefault();
                    document.getElementById('prix_unitaire').focus();
                    showToast('💰 Focus sur Prix', 'info');
                }
                
                // Alt + E = Estimation automatique
                if (e.altKey && e.key === 'e') {
                    e.preventDefault();
                    calculerEstimation();
                }
                
                // Alt + A = Focus ampérage
                if (e.altKey && e.key === 'a') {
                    e.preventDefault();
                    document.getElementById('amperage').focus();
                    showToast('⚡ Focus sur Ampérage', 'info');
                }
                
                // Alt + S = Focus section
                if (e.altKey && e.key === 's') {
                    e.preventDefault();
                    document.getElementById('section_cable').focus();
                    showToast('🔌 Focus sur Section', 'info');
                }
                
                // Ctrl + Entrée = Soumettre formulaire
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('formElectricite').submit();
                }
            });
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
         * Filtrage et recherche dans le tableau
         */
        function initRechercheTableau() {
            // Créer un champ de recherche si il n'existe pas
            const tableHeader = document.querySelector('.table-header');
            if (tableHeader && !document.getElementById('recherche-electricite')) {
                const rechercheContainer = document.createElement('div');
                rechercheContainer.style.cssText = 'margin-left: auto; display: flex; align-items: center; gap: 1rem;';
                
                const rechercheInput = document.createElement('input');
                rechercheInput.type = 'text';
                rechercheInput.id = 'recherche-electricite';
                rechercheInput.placeholder = 'Rechercher...';
                rechercheInput.style.cssText = `
                    padding: 0.5rem 1rem;
                    border: 1px solid rgba(255,255,255,0.3);
                    background: rgba(255,255,255,0.2);
                    color: white;
                    border-radius: 20px;
                    width: 200px;
                `;
                
                rechercheInput.addEventListener('input', function() {
                    const terme = this.value.toLowerCase();
                    const lignes = document.querySelectorAll('.table tbody tr');
                    
                    lignes.forEach(ligne => {
                        const texte = ligne.textContent.toLowerCase();
                        ligne.style.display = texte.includes(terme) ? '' : 'none';
                    });
                });
                
                rechercheContainer.appendChild(rechercheInput);
                tableHeader.insertBefore(rechercheContainer, tableHeader.lastElementChild);
            }
        }

        // ===== INITIALISATION AU CHARGEMENT =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔌 Module Électricité GSN ProDevis360° initialisé');
            
            // Initialiser toutes les fonctionnalités
            validerChampsElectriques();
            initRaccourcisClavier();
            initAnimationsScroll();
            initRechercheTableau();
            
            // Afficher les raccourcis clavier
            showToast(`⌨️ Raccourcis disponibles:\n` +
                     `Alt+D = Désignation\n` +
                     `Alt+Q = Quantité\n` +
                     `Alt+P = Prix\n` +
                     `Alt+E = Estimation\n` +
                     `Alt+A = Ampérage\n` +
                     `Alt+S = Section\n` +
                     `Ctrl+Entrée = Envoyer`, 'info');
            
            // Focus automatique sur le premier champ
            const firstField = document.getElementById('designation');
            if (firstField && !firstField.value) {
                setTimeout(() => firstField.focus(), 500);
            }
            
            // Vérifier la cohérence des données existantes
            const elements = <?= json_encode($elements_electricite) ?>;
            let alertesCoherence = 0;
            
            elements.forEach(element => {
                if (element.amperage && element.section_cable) {
                    const amp = parseInt(element.amperage.replace(/[^\d]/g, ''));
                    const section = parseFloat(element.section_cable.replace(/[^\d.,]/g, '').replace(',', '.'));
                    
                    if ((amp <= 16 && section > 2.5) || (amp >= 20 && section < 2.5) || (amp >= 32 && section < 6)) {
                        alertesCoherence++;
                    }
                }
            });
            
            if (alertesCoherence > 0) {
                setTimeout(() => {
                    showToast(`⚠️ ${alertesCoherence} élément(s) avec incohérence ampérage/section détecté(s).\nVérifiez la conformité NF C 15-100.`, 'warning');
                }, 2000);
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