<?php
// ===== PEINTURE.PHP - PARTIE 1 : PHP LOGIC & CONFIG =====
// VERSION UNIFORMISÉE GSN ProDevis360°
require_once 'functions.php';

// Configuration du module actuel
$current_module = 'peinture';

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

// Suggestions spécialisées pour la peinture
$suggestions_peinture = [
    // PEINTURES INTÉRIEURES
    'Peinture acrylique murs intérieurs blanc mat',
    'Peinture acrylique murs intérieurs blanc satiné',
    'Peinture acrylique murs intérieurs couleur mat',
    'Peinture acrylique murs intérieurs couleur satiné',
    'Peinture acrylique plafonds blanc mat',
    'Peinture lessivable cuisine blanc satiné',
    'Peinture lessivable salle de bain blanc satiné',
    'Peinture anti-humidité caves et sous-sols',
    'Peinture magnétique tableau noir',
    'Peinture tableau blanc effaçable',
    
    // PEINTURES EXTÉRIEURES
    'Peinture façade acrylique blanc',
    'Peinture façade acrylique couleur',
    'Peinture façade siloxane blanc premium',
    'Peinture façade siloxane couleur premium',
    'Peinture anti-mousse façades',
    'Peinture étanche toiture fibres',
    'Peinture sol extérieur antidérapante',
    'Peinture parking époxy gris',
    'Peinture balcon étanche',
    'Crépi décoratif extérieur',
    
    // PEINTURES BOISERIES
    'Peinture boiseries glycéro blanc brillant',
    'Peinture boiseries glycéro couleur brillant',
    'Peinture boiseries acrylique blanc satiné',
    'Peinture boiseries acrylique couleur satiné',
    'Lasure bois extérieur incolore',
    'Lasure bois extérieur teinté chêne',
    'Lasure bois extérieur teinté pin',
    'Lasure bois extérieur teinté rouge',
    'Vernis parquet brillant',
    'Vernis parquet mat',
    'Vernis parquet satiné',
    'Huile parquet naturelle',
    
    // PEINTURES MÉTALLIQUES
    'Peinture métal antirouille blanc',
    'Peinture métal antirouille couleur',
    'Peinture métal forge noir brillant',
    'Peinture métal forge martelé',
    'Peinture radiateur blanc brillant',
    'Peinture haute température noir',
    'Primer métal galvanisé',
    'Convertisseur de rouille',
    
    // ENDUITS ET PRÉPARATION
    'Enduit de lissage intérieur poudre 25kg',
    'Enduit de lissage intérieur pâte 15kg',
    'Enduit de rebouchage trous et fissures',
    'Enduit de rénovation murs abîmés',
    'Enduit décoratif effet béton ciré',
    'Enduit décoratif effet stuc',
    'Enduit extérieur de façade 25kg',
    'Calicot bande à fissures',
    'Mastic acrylique blanc étanchéité',
    'Mastic silicone sanitaire blanc',
    
    // SOUS-COUCHES ET PRIMAIRES
    'Sous-couche universelle tous supports',
    'Sous-couche bois tanniques',
    'Sous-couche métal antirouille',
    'Sous-couche placo hydrofuge',
    'Primer d\'accrochage supports lisses',
    'Fixateur de fond poudrant',
    'Impression façade avant peinture',
    'Isolation taches nicotine et suie',
    
    // MATÉRIEL ET ACCESSOIRES
    'Rouleau peinture anti-gouttes 180mm',
    'Rouleau peinture laque 110mm',
    'Rouleau façade poils longs 250mm',
    'Pinceau plat boiseries 30mm',
    'Pinceau plat boiseries 50mm',
    'Pinceau plat boiseries 70mm',
    'Pinceau rond rechampir n°8',
    'Pinceau rond rechampir n°12',
    'Brosse métallique décapage',
    'Ponceuse excentrique 150mm',
    'Papier abrasif grain 120',
    'Papier abrasif grain 240',
    'Bâche protection plastique 4x5m',
    'Adhésif masquage 19mm x 50m',
    'Adhésif masquage 38mm x 50m',
    'Grille essorage rouleau',
    'Seau peinture gradué 12L',
    'Mélangeur peinture électrique'
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
            $unite = trim($_POST['unite'] ?? 'm²');
            $prix_unitaire = floatval($_POST['prix_unitaire'] ?? 0);
            $type_peinture = trim($_POST['type_peinture'] ?? '');
            $finition = trim($_POST['finition'] ?? '');
            $couleur = trim($_POST['couleur'] ?? '');
            $marque = trim($_POST['marque'] ?? '');
            $support = trim($_POST['support'] ?? '');
            $nbr_couches = intval($_POST['nbr_couches'] ?? 1);
            
            // Validations spécifiques peinture
            if (empty($designation)) {
                throw new Exception("La désignation est obligatoire.");
            }
            if ($quantite <= 0) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }
            if ($prix_unitaire < 0) {
                throw new Exception("Le prix unitaire ne peut pas être négatif.");
            }
            if ($nbr_couches < 1 || $nbr_couches > 5) {
                throw new Exception("Le nombre de couches doit être entre 1 et 5.");
            }
            
            // Validation couleur si fournie (format hexadécimal ou nom)
            if (!empty($couleur) && !preg_match('/^(#[0-9A-Fa-f]{6}|[a-zA-ZÀ-ÿ\s]+)$/', $couleur)) {
                throw new Exception("Format de couleur invalide (ex: #FF0000 ou Rouge).");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Insertion en base
            $stmt = $conn->prepare("
                INSERT INTO peinture (
                    projet_id, devis_id, designation, quantite, unite, 
                    prix_unitaire, total, type_peinture, finition, 
                    couleur, marque, support, nbr_couches, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param(
                "iisdsdssssssi", 
                $projet_id, $devis_id, $designation, $quantite, $unite,
                $prix_unitaire, $total, $type_peinture, $finition,
                $couleur, $marque, $support, $nbr_couches
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'peinture');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'peinture', 'Ajout', "Élément ajouté : {$designation}");
                
                $message = "Élément peinture ajouté avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de l'ajout : " . $conn->error);
            }
            
        } elseif ($action == 'modifier' && $element_id > 0) {
            // Récupération et validation des données
            $designation = trim($_POST['designation'] ?? '');
            $quantite = floatval($_POST['quantite'] ?? 0);
            $unite = trim($_POST['unite'] ?? 'm²');
            $prix_unitaire = floatval($_POST['prix_unitaire'] ?? 0);
            $type_peinture = trim($_POST['type_peinture'] ?? '');
            $finition = trim($_POST['finition'] ?? '');
            $couleur = trim($_POST['couleur'] ?? '');
            $marque = trim($_POST['marque'] ?? '');
            $support = trim($_POST['support'] ?? '');
            $nbr_couches = intval($_POST['nbr_couches'] ?? 1);
            
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
            if ($nbr_couches < 1 || $nbr_couches > 5) {
                throw new Exception("Le nombre de couches doit être entre 1 et 5.");
            }
            
            if (!empty($couleur) && !preg_match('/^(#[0-9A-Fa-f]{6}|[a-zA-ZÀ-ÿ\s]+)$/', $couleur)) {
                throw new Exception("Format de couleur invalide (ex: #FF0000 ou Rouge).");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Mise à jour en base
            $stmt = $conn->prepare("
                UPDATE peinture SET 
                    designation = ?, quantite = ?, unite = ?, prix_unitaire = ?, 
                    total = ?, type_peinture = ?, finition = ?, couleur = ?, 
                    marque = ?, support = ?, nbr_couches = ?, date_modification = NOW()
                WHERE id = ? AND projet_id = ? AND devis_id = ?
            ");
            
            $stmt->bind_param(
                "sdsdssssssiiii",
                $designation, $quantite, $unite, $prix_unitaire, $total,
                $type_peinture, $finition, $couleur, $marque, $support, 
                $nbr_couches, $element_id, $projet_id, $devis_id
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'peinture');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'peinture', 'Modification', "Élément modifié : {$designation}");
                
                $message = "Élément peinture modifié avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de la modification : " . $conn->error);
            }
            
        } elseif ($action == 'supprimer' && $element_id > 0) {
            // Récupération de la désignation avant suppression
            $stmt_get = $conn->prepare("SELECT designation FROM peinture WHERE id = ? AND projet_id = ? AND devis_id = ?");
            $stmt_get->bind_param("iii", $element_id, $projet_id, $devis_id);
            $stmt_get->execute();
            $result_get = $stmt_get->get_result();
            $element_data = $result_get->fetch_assoc();
            
            if ($element_data) {
                // Suppression de l'élément
                $stmt = $conn->prepare("DELETE FROM peinture WHERE id = ? AND projet_id = ? AND devis_id = ?");
                $stmt->bind_param("iii", $element_id, $projet_id, $devis_id);
                
                if ($stmt->execute()) {
                    // Mise à jour du récapitulatif
                    updateRecapitulatif($projet_id, $devis_id, 'peinture');
                    
                    // Sauvegarde dans l'historique
                    sauvegarderHistorique($projet_id, $devis_id, 'peinture', 'Suppression', "Élément supprimé : {$element_data['designation']}");
                    
                    $message = "Élément peinture supprimé avec succès !";
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

// Récupération des éléments de peinture pour affichage
$elements_peinture = [];
$total_module = 0;

$stmt = $conn->prepare("
    SELECT id, designation, quantite, unite, prix_unitaire, total,
           type_peinture, finition, couleur, marque, support, nbr_couches,
           DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation_fr,
           DATE_FORMAT(date_modification, '%d/%m/%Y %H:%i') as date_modification_fr
    FROM peinture 
    WHERE projet_id = ? AND devis_id = ? 
    ORDER BY date_creation DESC
");

$stmt->bind_param("ii", $projet_id, $devis_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $elements_peinture[] = $row;
    $total_module += $row['total'];
}

// Récupération de l'élément à modifier si nécessaire
$element_a_modifier = null;
if ($action == 'modifier' && $element_id > 0) {
    $stmt = $conn->prepare("
        SELECT * FROM peinture 
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
    <title>Peinture - <?= htmlspecialchars($projet_devis_info['nom_projet']) ?> | GSN ProDevis360°</title>
    
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
            
            /* Variables spécifiques peinture */
            --paint-red: #e74c3c;
            --paint-blue: #3498db;
            --paint-green: #27ae60;
            --paint-yellow: #f1c40f;
            --paint-purple: #9b59b6;
            --paint-orange: #e67e22;
            --paint-pink: #e91e63;
            --paint-teal: #1abc9c;
            --paint-indigo: #6c5ce7;
            --brush-brown: #8b4513;
            --palette-gray: #95a5a6;
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
            background: var(--paint-red);
            color: var(--secondary-white);
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

        .form-group input[type="color"] {
            width: 60px;
            height: 40px;
            padding: 0;
            border: 2px solid #e9ecef;
            cursor: pointer;
        }

        /* ===== SUGGESTIONS PEINTURE ===== */
        .suggestions-peinture {
            background: linear-gradient(135deg, var(--paint-red) 0%, #c0392b 100%);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .suggestions-peinture h4 {
            color: var(--secondary-white);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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

        /* ===== SÉLECTEUR DE COULEURS ===== */
        .color-palette {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .color-swatch {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: var(--transition-fast);
        }

        .color-swatch:hover {
            transform: scale(1.1);
            border-color: var(--neutral-dark);
        }

        .color-swatch.selected {
            border-color: var(--neutral-dark);
            box-shadow: var(--shadow-soft);
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

        /* ===== BADGES SPÉCIALISÉS PEINTURE ===== */
        .badge-type {
            background: var(--paint-blue);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-finition {
            background: var(--paint-green);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-couleur {
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(0,0,0,0.2);
        }

        .badge-support {
            background: var(--brush-brown);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-couches {
            background: var(--palette-gray);
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
            color: var(--paint-red);
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
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
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

            .color-palette {
                grid-template-columns: repeat(6, 1fr);
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

            .color-palette {
                grid-template-columns: repeat(4, 1fr);
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

        @keyframes paintSplash {
            0% { transform: scale(0) rotate(0deg); opacity: 0; }
            50% { transform: scale(1.2) rotate(180deg); opacity: 1; }
            100% { transform: scale(1) rotate(360deg); opacity: 1; }
        }

        .paint-splash {
            animation: paintSplash 0.8s ease-out;
        }

        /* ===== STYLES D'IMPRESSION ===== */
        @media print {
            .header-gsn,
            .navigation-modules,
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

            .badge-couleur {
                border: 1px solid #000 !important;
            }
        }

        /* ===== STYLES UTILITAIRES ===== */
        .text-center { text-align: center; }
        .text-muted { color: var(--neutral-gray); }
        .text-success { color: var(--accent-green); }
        .text-primary { color: var(--primary-orange); }
        .text-info { color: var(--accent-blue); }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .ml-1 { margin-left: 0.25rem; }
        .ml-2 { margin-left: 0.5rem; }
        .mx-2 { margin-left: 0.5rem; margin-right: 0.5rem; }
        .py-4 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
        .d-block { display: block; }
        .fa-3x { font-size: 3rem; }
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
                        <i class="fas fa-paint-brush"></i>
                        Module Peinture
                        <span class="module-badge">
                            <i class="fas fa-palette"></i>
                            Couleurs & Finitions
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
    
<!-- ===== CONTAINER PRINCIPAL ===== -->
    <div class="container">
        
        <!-- ===== MESSAGES D'ALERTE ===== -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> fade-in-up">
                <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- ===== FORMULAIRE PEINTURE ===== -->
        <div class="form-section fade-in-up">
            <h2>
                <i class="fas fa-<?= $element_a_modifier ? 'edit' : 'plus-circle' ?>"></i>
                <?= $element_a_modifier ? 'Modifier l\'élément peinture' : 'Ajouter un élément peinture' ?>
            </h2>

            <!-- Suggestions Peinture -->
            <div class="suggestions-peinture">
                <h4>
                    <i class="fas fa-palette"></i>
                    Suggestions Peintures & Finitions
                    <small>(Cliquez pour remplir automatiquement)</small>
                </h4>
                <div class="suggestions-grid">
                    <?php foreach ($suggestions_peinture as $suggestion): ?>
                        <div class="suggestion-item" onclick="remplirSuggestion('<?= htmlspecialchars($suggestion, ENT_QUOTES) ?>')">
                            <?= htmlspecialchars($suggestion) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <form method="POST" action="" id="formPeinture">
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
                               placeholder="Ex: Peinture acrylique murs intérieurs blanc mat"
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
                               placeholder="Ex: 25.5"
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
                            <option value="m²" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm²') ? 'selected' : '' ?>>Mètre carré (m²)</option>
                            <option value="ml" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'ml') ? 'selected' : '' ?>>Mètre linéaire (ml)</option>
                            <option value="L" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'L') ? 'selected' : '' ?>>Litre (L)</option>
                            <option value="kg" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'kg') ? 'selected' : '' ?>>Kilogramme (kg)</option>
                            <option value="unité" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'unité') ? 'selected' : '' ?>>Unité</option>
                            <option value="pot" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'pot') ? 'selected' : '' ?>>Pot</option>
                            <option value="seau" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'seau') ? 'selected' : '' ?>>Seau</option>
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
                               placeholder="Ex: 2500"
                               step="0.01"
                               min="0"
                               required>
                    </div>
                </div>

                <!-- Ligne 2 : Spécifications peinture -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="type_peinture">
                            <i class="fas fa-paint-brush"></i>
                            Type de peinture
                        </label>
                        <select id="type_peinture" name="type_peinture">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Acrylique" <?= ($element_a_modifier && $element_a_modifier['type_peinture'] === 'Acrylique') ? 'selected' : '' ?>>Acrylique (à l'eau)</option>
                            <option value="Glycéro" <?= ($element_a_modifier && $element_a_modifier['type_peinture'] === 'Glycéro') ? 'selected' : '' ?>>Glycéro (à l'huile)</option>
                            <option value="Siloxane" <?= ($element_a_modifier && $element_a_modifier['type_peinture'] === 'Siloxane') ? 'selected' : '' ?>>Siloxane (façade)</option>
                            <option value="Époxy" <?= ($element_a_modifier && $element_a_modifier['type_peinture'] === 'Époxy') ? 'selected' : '' ?>>Époxy (sol)</option>
                            <option value="Alkyde" <?= ($element_a_modifier && $element_a_modifier['type_peinture'] === 'Alkyde') ? 'selected' : '' ?>>Alkyde (bois)</option>
                            <option value="Lasure" <?= ($element_a_modifier && $element_a_modifier['type_peinture'] === 'Lasure') ? 'selected' : '' ?>>Lasure (bois)</option>
                            <option value="Vernis" <?= ($element_a_modifier && $element_a_modifier['type_peinture'] === 'Vernis') ? 'selected' : '' ?>>Vernis (protection)</option>
                            <option value="Enduit" <?= ($element_a_modifier && $element_a_modifier['type_peinture'] === 'Enduit') ? 'selected' : '' ?>>Enduit décoratif</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="finition">
                            <i class="fas fa-star"></i>
                            Finition
                        </label>
                        <select id="finition" name="finition">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Mat" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Mat') ? 'selected' : '' ?>>Mat</option>
                            <option value="Satiné" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Satiné') ? 'selected' : '' ?>>Satiné</option>
                            <option value="Brillant" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Brillant') ? 'selected' : '' ?>>Brillant</option>
                            <option value="Velours" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Velours') ? 'selected' : '' ?>>Velours</option>
                            <option value="Texturé" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Texturé') ? 'selected' : '' ?>>Texturé</option>
                            <option value="Lisse" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Lisse') ? 'selected' : '' ?>>Lisse</option>
                            <option value="Grené" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Grené') ? 'selected' : '' ?>>Grené</option>
                            <option value="Martelé" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Martelé') ? 'selected' : '' ?>>Martelé</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="couleur">
                            <i class="fas fa-palette"></i>
                            Couleur
                        </label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="text" 
                                   id="couleur" 
                                   name="couleur" 
                                   value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['couleur']) : '' ?>"
                                   placeholder="Ex: Blanc, Rouge, #FF0000"
                                   style="flex: 1;">
                            <input type="color" 
                                   id="couleur_picker" 
                                   title="Sélecteur de couleur">
                        </div>
                        <!-- Palette de couleurs rapides -->
                        <div class="color-palette">
                            <div class="color-swatch" style="background: #ffffff; border: 1px solid #ddd;" data-color="Blanc" title="Blanc"></div>
                            <div class="color-swatch" style="background: #000000;" data-color="Noir" title="Noir"></div>
                            <div class="color-swatch" style="background: #ff0000;" data-color="Rouge" title="Rouge"></div>
                            <div class="color-swatch" style="background: #00ff00;" data-color="Vert" title="Vert"></div>
                            <div class="color-swatch" style="background: #0000ff;" data-color="Bleu" title="Bleu"></div>
                            <div class="color-swatch" style="background: #ffff00;" data-color="Jaune" title="Jaune"></div>
                            <div class="color-swatch" style="background: #ff8000;" data-color="Orange" title="Orange"></div>
                            <div class="color-swatch" style="background: #800080;" data-color="Violet" title="Violet"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="support">
                            <i class="fas fa-layer-group"></i>
                            Support
                        </label>
                        <select id="support" name="support">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Mur intérieur" <?= ($element_a_modifier && $element_a_modifier['support'] === 'Mur intérieur') ? 'selected' : '' ?>>Mur intérieur</option>
                            <option value="Mur extérieur" <?= ($element_a_modifier && $element_a_modifier['support'] === 'Mur extérieur') ? 'selected' : '' ?>>Mur extérieur</option>
                            <option value="Plafond" <?= ($element_a_modifier && $element_a_modifier['support'] === 'Plafond') ? 'selected' : '' ?>>Plafond</option>
                            <option value="Boiserie" <?= ($element_a_modifier && $element_a_modifier['support'] === 'Boiserie') ? 'selected' : '' ?>>Boiserie</option>
                            <option value="Métal" <?= ($element_a_modifier && $element_a_modifier['support'] === 'Métal') ? 'selected' : '' ?>>Métal</option>
                            <option value="Façade" <?= ($element_a_modifier && $element_a_modifier['support'] === 'Façade') ? 'selected' : '' ?>>Façade</option>
                            <option value="Sol" <?= ($element_a_modifier && $element_a_modifier['support'] === 'Sol') ? 'selected' : '' ?>>Sol</option>
                            <option value="Carrelage" <?= ($element_a_modifier && $element_a_modifier['support'] === 'Carrelage') ? 'selected' : '' ?>>Carrelage</option>
                            <option value="Béton" <?= ($element_a_modifier && $element_a_modifier['support'] === 'Béton') ? 'selected' : '' ?>>Béton</option>
                            <option value="Placo" <?= ($element_a_modifier && $element_a_modifier['support'] === 'Placo') ? 'selected' : '' ?>>Placo</option>
                        </select>
                    </div>
                </div>

                <!-- Ligne 3 : Détails techniques -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="marque">
                            <i class="fas fa-tags"></i>
                            Marque
                        </label>
                        <select id="marque" name="marque">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Dulux Valentine" <?= ($element_a_modifier && $element_a_modifier['marque'] === 'Dulux Valentine') ? 'selected' : '' ?>>Dulux Valentine</option>
                            <option value="Levis" <?= ($element_a_modifier && $element_a_modifier['marque'] === 'Levis') ? 'selected' : '' ?>>Levis</option>
                            <option value="Tollens" <?= ($element_a_modifier && $element_a_modifier['marque'] === 'Tollens') ? 'selected' : '' ?>>Tollens</option>
                            <option value="Ripolin" <?= ($element_a_modifier && $element_a_modifier['marque'] === 'Ripolin') ? 'selected' : '' ?>>Ripolin</option>
                            <option value="V33" <?= ($element_a_modifier && $element_a_modifier['marque'] === 'V33') ? 'selected' : '' ?>>V33</option>
                            <option value="Zolpan" <?= ($element_a_modifier && $element_a_modifier['marque'] === 'Zolpan') ? 'selected' : '' ?>>Zolpan</option>
                            <option value="Seigneurie" <?= ($element_a_modifier && $element_a_modifier['marque'] === 'Seigneurie') ? 'selected' : '' ?>>Seigneurie</option>
                            <option value="Guittet" <?= ($element_a_modifier && $element_a_modifier['marque'] === 'Guittet') ? 'selected' : '' ?>>Guittet</option>
                            <option value="Ressource" <?= ($element_a_modifier && $element_a_modifier['marque'] === 'Ressource') ? 'selected' : '' ?>>Ressource</option>
                            <option value="Farrow & Ball" <?= ($element_a_modifier && $element_a_modifier['marque'] === 'Farrow & Ball') ? 'selected' : '' ?>>Farrow & Ball</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nbr_couches">
                            <i class="fas fa-layers"></i>
                            Nombre de couches
                        </label>
                        <select id="nbr_couches" name="nbr_couches">
                            <option value="1" <?= ($element_a_modifier && $element_a_modifier['nbr_couches'] == 1) ? 'selected' : '' ?>>1 couche</option>
                            <option value="2" <?= ($element_a_modifier && $element_a_modifier['nbr_couches'] == 2) ? 'selected' : 'selected' ?>>2 couches</option>
                            <option value="3" <?= ($element_a_modifier && $element_a_modifier['nbr_couches'] == 3) ? 'selected' : '' ?>>3 couches</option>
                            <option value="4" <?= ($element_a_modifier && $element_a_modifier['nbr_couches'] == 4) ? 'selected' : '' ?>>4 couches</option>
                            <option value="5" <?= ($element_a_modifier && $element_a_modifier['nbr_couches'] == 5) ? 'selected' : '' ?>>5 couches</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-calculator"></i>
                            Calculateur surface
                        </label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="number" 
                                   id="longueur" 
                                   placeholder="Longueur (m)"
                                   step="0.01"
                                   style="width: 80px;">
                            <span>×</span>
                            <input type="number" 
                                   id="largeur" 
                                   placeholder="Largeur (m)"
                                   step="0.01"
                                   style="width: 80px;">
                            <button type="button" 
                                    class="btn btn-sm btn-info" 
                                    onclick="calculerSurface()"
                                    title="Calculer la surface">
                                <i class="fas fa-equals"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-tint"></i>
                            Actions rapides
                        </label>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <button type="button" 
                                    class="btn btn-sm btn-info" 
                                    onclick="calculerQuantitePeinture()"
                                    title="Alt+Q">
                                <i class="fas fa-calculator"></i>
                                Quantité auto
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-warning" 
                                    onclick="calculerEstimation()"
                                    title="Alt+E">
                                <i class="fas fa-euro-sign"></i>
                                Prix auto
                            </button>
                        </div>
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
                            <a href="peinture.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                        <?php endif; ?>
                        
                        <button type="reset" class="btn btn-secondary ml-2" onclick="resetFormulaire()">
                            <i class="fas fa-eraser"></i>
                            Effacer
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ===== TABLEAU DES ÉLÉMENTS PEINTURE ===== -->
        <div class="table-container fade-in-up">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list"></i>
                    Éléments peinture
                    <span class="badge-type ml-2"><?= count($elements_peinture) ?> élément(s)</span>
                </h3>
                <div class="table-actions">
                    <span class="total-amount">
                        <i class="fas fa-paint-brush"></i>
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
                            <th><i class="fas fa-paint-brush"></i> Type</th>
                            <th><i class="fas fa-star"></i> Finition</th>
                            <th><i class="fas fa-palette"></i> Couleur</th>
                            <th><i class="fas fa-layer-group"></i> Support</th>
                            <th><i class="fas fa-layers"></i> Couches</th>
                            <th><i class="fas fa-euro-sign"></i> Total</th>
                            <th><i class="fas fa-calendar"></i> Créé le</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($elements_peinture)): ?>
                            <tr>
                                <td colspan="13" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-paint-brush fa-3x mb-3 d-block"></i>
                                        <p>Aucun élément peinture ajouté pour ce devis.</p>
                                        <small>Utilisez le formulaire ci-dessus pour ajouter des éléments.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($elements_peinture as $element): ?>
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
                                    <td><span class="badge-support"><?= htmlspecialchars($element['unite']) ?></span></td>
                                    <td><strong><?= number_format($element['prix_unitaire'], 0, ',', ' ') ?></strong> FCFA</td>
                                    <td>
                                        <?php if (!empty($element['type_peinture'])): ?>
                                            <span class="badge-type">
                                                <i class="fas fa-paint-brush"></i>
                                                <?= htmlspecialchars($element['type_peinture']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['finition'])): ?>
                                            <span class="badge-finition">
                                                <i class="fas fa-star"></i>
                                                <?= htmlspecialchars($element['finition']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['couleur'])): ?>
                                            <?php
                                            // Déterminer la couleur de fond du badge
                                            $couleur_bg = '#6c757d'; // Gris par défaut
                                            if (preg_match('/^#[0-9A-Fa-f]{6}$/', $element['couleur'])) {
                                                $couleur_bg = $element['couleur'];
                                            } elseif (strtolower($element['couleur']) === 'blanc') {
                                                $couleur_bg = '#ffffff';
                                            } elseif (strtolower($element['couleur']) === 'noir') {
                                                $couleur_bg = '#000000';
                                            } elseif (strtolower($element['couleur']) === 'rouge') {
                                                $couleur_bg = '#dc3545';
                                            } elseif (strtolower($element['couleur']) === 'bleu') {
                                                $couleur_bg = '#007bff';
                                            } elseif (strtolower($element['couleur']) === 'vert') {
                                                $couleur_bg = '#28a745';
                                            } elseif (strtolower($element['couleur']) === 'jaune') {
                                                $couleur_bg = '#ffc107';
                                            }
                                            
                                            // Déterminer la couleur du texte (noir ou blanc selon la luminosité)
                                            $text_color = (strtolower($element['couleur']) === 'blanc' || $couleur_bg === '#ffffff' || $couleur_bg === '#ffc107') ? '#000000' : '#ffffff';
                                            ?>
                                            <span class="badge-couleur" style="background-color: <?= $couleur_bg ?>; color: <?= $text_color ?>;">
                                                <i class="fas fa-circle"></i>
                                                <?= htmlspecialchars($element['couleur']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['support'])): ?>
                                            <span class="badge-support">
                                                <i class="fas fa-layer-group"></i>
                                                <?= htmlspecialchars($element['support']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge-couches">
                                            <i class="fas fa-layers"></i>
                                            <?= $element['nbr_couches'] ?> couche<?= $element['nbr_couches'] > 1 ? 's' : '' ?>
                                        </span>
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
                                            <a href="peinture.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&action=modifier&element_id=<?= $element['id'] ?>" 
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
                        <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=peinture" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-clock"></i> Voir tout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TOTAUX MODULE PEINTURE ===== -->
        <div class="module-summary fade-in-up">
            <h3>
                <i class="fas fa-paint-brush"></i>
                Total Module Peinture
            </h3>
            <div class="total-amount pulse-animation">
                <?= number_format($total_module, 0, ',', ' ') ?> FCFA
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Mis à jour automatiquement • <?= count($elements_peinture) ?> élément(s)
                <?php if ($total_module > 0 && count($elements_peinture) > 0): ?>
                    • Moyenne: <?= number_format($total_module / count($elements_peinture), 0, ',', ' ') ?> FCFA/élément
                <?php endif; ?>
            </small>
        </div>

    </div>

    <!-- ===== JAVASCRIPT SPÉCIALISÉ PEINTURE ===== -->
    <script>
        // ===== CONFIGURATION ET VARIABLES =====
        const PRIX_PEINTURE = {
            // Peintures intérieures (prix par m²)
            'acrylique_interieur': { base: 2500, factor: 1.0 },
            'glycero_interieur': { base: 3500, factor: 1.2 },
            'lessivable': { base: 4000, factor: 1.3 },
            'anti_humidite': { base: 5000, factor: 1.5 },
            
            // Peintures extérieures (prix par m²)
            'facade_acrylique': { base: 3000, factor: 1.4 },
            'facade_siloxane': { base: 4500, factor: 1.8 },
            'crepi_decoratif': { base: 6000, factor: 2.0 },
            'etanche_toiture': { base: 7000, factor: 2.2 },
            
            // Peintures boiseries (prix par m²)
            'glycero_boiserie': { base: 4000, factor: 1.5 },
            'acrylique_boiserie': { base: 3000, factor: 1.2 },
            'lasure_bois': { base: 3500, factor: 1.3 },
            'vernis_parquet': { base: 4500, factor: 1.6 },
            
            // Enduits et préparation (prix par m² ou kg)
            'enduit_lissage': { base: 1500, factor: 0.8 },
            'enduit_rebouchage': { base: 2000, factor: 1.0 },
            'sous_couche': { base: 2000, factor: 0.9 },
            
            // Matériel (prix unitaire)
            'rouleau': { base: 8000, factor: 1.0 },
            'pinceau': { base: 5000, factor: 0.8 },
            'bache': { base: 3000, factor: 0.6 }
        };

        const RENDEMENT_PEINTURE = {
            // Rendement moyen en m² par litre selon le type
            'Acrylique': 10,
            'Glycéro': 12,
            'Siloxane': 8,
            'Époxy': 6,
            'Lasure': 15,
            'Vernis': 14,
            'Enduit': 5
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
            const typeField = document.getElementById('type_peinture');
            const finitionField = document.getElementById('finition');
            const couleurField = document.getElementById('couleur');
            const supportField = document.getElementById('support');
            const couchesField = document.getElementById('nbr_couches');
            const marqueField = document.getElementById('marque');
            
            // Remplir la désignation
            designationField.value = suggestion;
            
            // Analyse intelligente de la suggestion
            const sug = suggestion.toLowerCase();
            
            // Déterminer le type de peinture
            if (sug.includes('acrylique')) {
                typeField.value = 'Acrylique';
            } else if (sug.includes('vert')) {
                couleurField.value = 'Vert';
            } else if (sug.includes('jaune')) {
                couleurField.value = 'Jaune';
            } else if (sug.includes('orange')) {
                couleurField.value = 'Orange';
            } else if (sug.includes('chêne') || sug.includes('chene')) {
                couleurField.value = 'Chêne';
            } else if (sug.includes('pin')) {
                couleurField.value = 'Pin';
            } else if (sug.includes('incolore')) {
                couleurField.value = 'Incolore';
            }
            
            // Déterminer le support
            if (sug.includes('murs') || sug.includes('mur')) {
                if (sug.includes('extérieur') || sug.includes('exterieur') || sug.includes('façade') || sug.includes('facade')) {
                    supportField.value = 'Mur extérieur';
                } else {
                    supportField.value = 'Mur intérieur';
                }
            } else if (sug.includes('plafond')) {
                supportField.value = 'Plafond';
            } else if (sug.includes('boiserie') || sug.includes('bois')) {
                supportField.value = 'Boiserie';
            } else if (sug.includes('métal') || sug.includes('metal') || sug.includes('radiateur')) {
                supportField.value = 'Métal';
            } else if (sug.includes('façade') || sug.includes('facade')) {
                supportField.value = 'Façade';
            } else if (sug.includes('sol') || sug.includes('parking')) {
                supportField.value = 'Sol';
            } else if (sug.includes('parquet')) {
                supportField.value = 'Boiserie';
            } else if (sug.includes('toiture')) {
                supportField.value = 'Façade';
            }
            
            // Déterminer l'unité selon le type d'élément
            if (sug.includes('rouleau') || sug.includes('pinceau') || sug.includes('brosse') || 
                sug.includes('grille') || sug.includes('seau') || sug.includes('mélangeur')) {
                uniteField.value = 'unité';
                quantiteField.value = '1';
            } else if (sug.includes('bâche') || sug.includes('bache') || sug.includes('adhésif') || sug.includes('adhesif')) {
                uniteField.value = 'unité';
                quantiteField.value = '1';
            } else if (sug.includes('papier abrasif') || sug.includes('calicot')) {
                uniteField.value = 'unité';
                quantiteField.value = '5';
            } else if (sug.includes('25kg') || sug.includes('15kg')) {
                uniteField.value = 'seau';
                quantiteField.value = '1';
            } else {
                uniteField.value = 'm²';
                quantiteField.value = '20';
            }
            
            // Déterminer le nombre de couches
            if (sug.includes('sous-couche') || sug.includes('primer') || sug.includes('impression')) {
                couchesField.value = '1';
            } else if (sug.includes('enduit') || sug.includes('rebouchage')) {
                couchesField.value = '1';
            } else {
                couchesField.value = '2'; // Par défaut 2 couches pour les peintures
            }
            
            // Estimer le prix
            const estimation = estimerPrixPeinture(suggestion);
            if (estimation > 0) {
                prixField.value = estimation;
            }
            
            // Animation visuelle
            designationField.style.background = 'linear-gradient(135deg, #fff3cd 0%, #ffffff 100%)';
            setTimeout(() => {
                designationField.style.background = '';
            }, 1000);
            
            // Focus sur le champ quantité
            quantiteField.focus();
            quantiteField.select();
        }

        /**
         * Estimation automatique des prix peinture
         */
        function estimerPrixPeinture(designation) {
            const des = designation.toLowerCase();
            let prix = 0;
            
            // Analyse par mots-clés et types
            if (des.includes('acrylique') && des.includes('intérieur')) {
                prix = PRIX_PEINTURE.acrylique_interieur.base;
            } else if (des.includes('glycéro') && des.includes('boiserie')) {
                prix = PRIX_PEINTURE.glycero_boiserie.base;
            } else if (des.includes('façade') && des.includes('siloxane')) {
                prix = PRIX_PEINTURE.facade_siloxane.base;
            } else if (des.includes('façade') && des.includes('acrylique')) {
                prix = PRIX_PEINTURE.facade_acrylique.base;
            } else if (des.includes('lessivable')) {
                prix = PRIX_PEINTURE.lessivable.base;
            } else if (des.includes('anti-humidité') || des.includes('caves')) {
                prix = PRIX_PEINTURE.anti_humidite.base;
            } else if (des.includes('lasure')) {
                prix = PRIX_PEINTURE.lasure_bois.base;
            } else if (des.includes('vernis')) {
                prix = PRIX_PEINTURE.vernis_parquet.base;
            } else if (des.includes('enduit')) {
                prix = PRIX_PEINTURE.enduit_lissage.base;
            } else if (des.includes('sous-couche')) {
                prix = PRIX_PEINTURE.sous_couche.base;
            } else if (des.includes('rouleau')) {
                prix = PRIX_PEINTURE.rouleau.base;
            } else if (des.includes('pinceau')) {
                prix = PRIX_PEINTURE.pinceau.base;
            } else if (des.includes('bâche')) {
                prix = PRIX_PEINTURE.bache.base;
            } else if (des.includes('acrylique')) {
                prix = PRIX_PEINTURE.acrylique_interieur.base;
            } else if (des.includes('glycéro')) {
                prix = PRIX_PEINTURE.glycero_boiserie.base;
            }
            
            // Facteurs multiplicateurs
            if (des.includes('premium') || des.includes('haut de gamme')) prix *= 1.8;
            if (des.includes('anti-mousse') || des.includes('étanche')) prix *= 1.4;
            if (des.includes('brillant')) prix *= 1.2;
            if (des.includes('effet') || des.includes('décoratif')) prix *= 1.6;
            if (des.includes('haute température')) prix *= 1.5;
            
            return Math.round(prix);
        }

        /**
         * Calculer la surface automatiquement
         */
        function calculerSurface() {
            const longueur = parseFloat(document.getElementById('longueur').value) || 0;
            const largeur = parseFloat(document.getElementById('largeur').value) || 0;
            
            if (longueur > 0 && largeur > 0) {
                const surface = longueur * largeur;
                document.getElementById('quantite').value = surface.toFixed(2);
                
                showToast(`📐 Surface calculée: ${surface.toFixed(2)} m²\n(${longueur}m × ${largeur}m)`, 'success');
                
                // Animation
                const quantiteField = document.getElementById('quantite');
                quantiteField.style.background = 'linear-gradient(135deg, #d1ecf1 0%, #ffffff 100%)';
                setTimeout(() => {
                    quantiteField.style.background = '';
                }, 1500);
            } else {
                showToast('⚠️ Veuillez saisir la longueur ET la largeur.', 'warning');
            }
        }

        /**
         * Calculer la quantité de peinture nécessaire
         */
        function calculerQuantitePeinture() {
            const surface = parseFloat(document.getElementById('quantite').value) || 0;
            const typePeinture = document.getElementById('type_peinture').value;
            const nbrCouches = parseInt(document.getElementById('nbr_couches').value) || 2;
            
            if (surface > 0 && typePeinture) {
                const rendement = RENDEMENT_PEINTURE[typePeinture] || 10;
                const quantiteNecessaire = Math.ceil((surface * nbrCouches) / rendement);
                
                // Changer l'unité en litres
                document.getElementById('unite').value = 'L';
                document.getElementById('quantite').value = quantiteNecessaire;
                
                showToast(`🎨 Quantité calculée: ${quantiteNecessaire}L\n` +
                         `Surface: ${surface}m² × ${nbrCouches} couche(s)\n` +
                         `Rendement: ${rendement}m²/L`, 'info');
                
                // Recalculer l'estimation de prix pour les litres
                const prixLitre = estimerPrixPeinture(document.getElementById('designation').value);
                if (prixLitre > 0) {
                    document.getElementById('prix_unitaire').value = Math.round(prixLitre * 0.8); // Prix au litre généralement moins cher
                }
            } else {
                showToast('⚠️ Veuillez saisir une surface et sélectionner un type de peinture.', 'warning');
            }
        }

        /**
         * Estimation automatique des prix
         */
        function calculerEstimation() {
            const designation = document.getElementById('designation').value;
            const quantite = parseFloat(document.getElementById('quantite').value) || 1;
            
            if (!designation.trim()) {
                alert('⚠️ Veuillez d\'abord saisir une désignation pour l\'estimation.');
                document.getElementById('designation').focus();
                return;
            }
            
            const prixUnitaire = estimerPrixPeinture(designation);
            
            if (prixUnitaire > 0) {
                document.getElementById('prix_unitaire').value = prixUnitaire;
                
                const total = prixUnitaire * quantite;
                showToast(`💰 Prix estimé: ${prixUnitaire.toLocaleString()} FCFA/unité\n📊 Total: ${total.toLocaleString()} FCFA`, 'info');
                
                // Animation
                const prixField = document.getElementById('prix_unitaire');
                prixField.style.background = 'linear-gradient(135deg, #d1ecf1 0%, #ffffff 100%)';
                setTimeout(() => {
                    prixField.style.background = '';
                }, 1500);
            } else {
                showToast('❓ Impossible d\'estimer le prix pour cet élément.\nVeuillez saisir manuellement.', 'warning');
                document.getElementById('prix_unitaire').focus();
            }
        }

        /**
         * Réinitialiser le formulaire
         */
        function resetFormulaire() {
            if (confirm('🗑️ Êtes-vous sûr de vouloir effacer tous les champs du formulaire ?')) {
                document.getElementById('formPeinture').reset();
                document.getElementById('unite').value = 'm²';
                document.getElementById('nbr_couches').value = '2';
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
                       `🎨 Élément: ${designation}\n` +
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
         * Gestion de la palette de couleurs
         */
        function initPaletteCouleurs() {
            const colorSwatches = document.querySelectorAll('.color-swatch');
            const couleurField = document.getElementById('couleur');
            const colorPicker = document.getElementById('couleur_picker');
            
            // Gestion des échantillons de couleur
            colorSwatches.forEach(swatch => {
                swatch.addEventListener('click', function() {
                    const couleur = this.getAttribute('data-color');
                    couleurField.value = couleur;
                    
                    // Retirer la sélection précédente
                    colorSwatches.forEach(s => s.classList.remove('selected'));
                    // Ajouter la sélection actuelle
                    this.classList.add('selected');
                    
                    // Animation
                    this.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 200);
                });
            });
            
            // Synchronisation avec le sélecteur de couleur
            colorPicker.addEventListener('change', function() {
                couleurField.value = this.value;
                colorSwatches.forEach(s => s.classList.remove('selected'));
            });
            
            // Synchronisation inverse
            couleurField.addEventListener('input', function() {
                if (this.value.startsWith('#')) {
                    colorPicker.value = this.value;
                }
                colorSwatches.forEach(s => s.classList.remove('selected'));
            });
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
                
                // Alt + Q = Calculer quantité peinture
                if (e.altKey && e.key === 'q') {
                    e.preventDefault();
                    calculerQuantitePeinture();
                }
                
                // Alt + S = Calculer surface
                if (e.altKey && e.key === 's') {
                    e.preventDefault();
                    document.getElementById('longueur').focus();
                    showToast('📐 Calculateur de surface - Saisissez longueur et largeur', 'info');
                }
                
                // Alt + E = Estimation prix
                if (e.altKey && e.key === 'e') {
                    e.preventDefault();
                    calculerEstimation();
                }
                
                // Alt + C = Focus couleur
                if (e.altKey && e.key === 'c') {
                    e.preventDefault();
                    document.getElementById('couleur').focus();
                    showToast('🎨 Focus sur Couleur', 'info');
                }
                
                // Alt + T = Focus type peinture
                if (e.altKey && e.key === 't') {
                    e.preventDefault();
                    document.getElementById('type_peinture').focus();
                    showToast('🖌️ Focus sur Type de peinture', 'info');
                }
                
                // Ctrl + Entrée = Soumettre formulaire
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('formPeinture').submit();
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
                background: ${type === 'info' ? 'var(--accent-blue)' : type === 'warning' ? 'var(--paint-orange)' : type === 'success' ? 'var(--accent-green)' : 'var(--paint-red)'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-medium);
                z-index: 9999;
                max-width: 400px;
                white-space: pre-line;
                animation: slideInRight 0.4s ease-out;
            `;
            
            const icon = type === 'info' ? '🎨' : type === 'warning' ? '⚠️' : type === 'success' ? '✅' : '❌';
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
         * Validation en temps réel
         */
        function initValidationTempsReel() {
            const couleurField = document.getElementById('couleur');
            const quantiteField = document.getElementById('quantite');
            const prixField = document.getElementById('prix_unitaire');
            
            // Validation couleur
            couleurField.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && !value.match(/^(#[0-9A-Fa-f]{6}|[a-zA-ZÀ-ÿ\s]+)$/)) {
                    this.style.borderColor = 'var(--accent-red)';
                    this.title = 'Format invalide. Exemples: Rouge, Bleu, #FF0000';
                } else {
                    this.style.borderColor = '';
                    this.title = '';
                }
            });
            
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
                            background: var(--paint-red);
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
            console.log('🎨 Module Peinture GSN ProDevis360° initialisé');
            
            // Initialiser toutes les fonctionnalités
            initPaletteCouleurs();
            initRaccourcisClavier();
            initAnimationsScroll();
            initValidationTempsReel();
            
            // Afficher les raccourcis clavier
            showToast(`⌨️ Raccourcis disponibles:\n` +
                     `Alt+D = Désignation\n` +
                     `Alt+Q = Quantité peinture\n` +
                     `Alt+S = Calculer surface\n` +
                     `Alt+E = Estimation prix\n` +
                     `Alt+C = Couleur\n` +
                     `Alt+T = Type peinture\n` +
                     `Ctrl+Entrée = Envoyer`, 'info');
            
            // Focus automatique sur le premier champ
            const firstField = document.getElementById('designation');
            if (firstField && !firstField.value) {
                setTimeout(() => firstField.focus(), 500);
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