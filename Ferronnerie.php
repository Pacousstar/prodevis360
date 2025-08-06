<?php
// ===== FERRONNERIE.PHP - PARTIE 1 : PHP LOGIC & CONFIG =====
// VERSION UNIFORMISÉE GSN ProDevis360°
require_once 'functions.php';

// Configuration du module actuel
$current_module = 'ferronnerie';

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

// Suggestions spécialisées pour la ferronnerie
$suggestions_ferronnerie = [
    // PORTAILS ET PORTES MÉTALLIQUES
    'Portail battant acier galvanisé 3m x 2m',
    'Portail coulissant acier galvanisé 4m x 2m',
    'Portail coulissant acier galvanisé 5m x 2m',
    'Portillon piéton acier galvanisé 1m x 2m',
    'Porte de service acier galvanisé 90cm x 2m',
    'Portail alu thermolaqué battant 3m x 1.8m',
    'Portail alu thermolaqué coulissant 4m x 1.8m',
    'Motorisation portail battant 24V',
    'Motorisation portail coulissant 220V',
    'Serrure électrique portail 12V',
    'Visiophone couleur filaire',
    'Interphone audio 2 postes',
    
    // GRILLES ET BARREAUDAGES
    'Grille de défense fenêtre 120x100cm',
    'Grille de défense fenêtre 140x120cm',
    'Grille de défense porte-fenêtre 200x240cm',
    'Barreaudage fixe galvanisé ø12mm entraxe 12cm',
    'Barreaudage amovible acier ø14mm',
    'Grille anti-effraction renforcée',
    'Protection de climatiseur acier',
    'Grille de ventilation métallique',
    'Cache-compteur électrique acier',
    'Grille avaloir route galvanisée 60x40cm',
    'Caillebotis acier galvanisé maille 30x30mm',
    'Grille caniveau fonte ductile classe D400',
    
    // ESCALIERS ET GARDE-CORPS
    'Escalier droit acier galvanisé 10 marches',
    'Escalier hélicoïdal acier galvanisé ø1.5m',
    'Garde-corps acier galvanisé hauteur 110cm',
    'Garde-corps inox brossé hauteur 110cm',
    'Main courante inox ø42mm',
    'Main courante acier galvanisé ø40mm',
    'Poteaux garde-corps acier ø48mm h=110cm',
    'Platine de fixation garde-corps 150x150mm',
    'Marche d\'escalier caillebotis 80cm',
    'Nez de marche acier galvanisé antidérapant',
    'Échelle à crinoline acier galvanisé',
    'Palier d\'escalier acier 120x80cm',
    
    // STRUCTURES MÉTALLIQUES
    'Poutre IPN 160 acier S235 long 6m',
    'Poutre IPN 200 acier S235 long 8m',
    'Poutre IPN 240 acier S235 long 10m',
    'Poutre HEA 160 acier S235 long 6m',
    'Poutre HEB 200 acier S235 long 8m',
    'Poteau HEB 160 acier S235 h=3m',
    'Poteau HEB 200 acier S235 h=4m',
    'Platine d\'assemblage acier S235 300x300x20mm',
    'Boulons HR M16x80 classe 8.8 galvanisés',
    'Boulons HR M20x100 classe 8.8 galvanisés',
    'Cornière acier L60x60x6 long 6m',
    'Cornière acier L80x80x8 long 6m',
    
    // MÉTALLERIE COURANTE
    'Tube carré acier 40x40x3mm galvanisé long 6m',
    'Tube carré acier 50x50x4mm galvanisé long 6m',
    'Tube carré acier 60x60x5mm galvanisé long 6m',
    'Tube rectangulaire 80x40x4mm galvanisé long 6m',
    'Tube rectangulaire 100x50x5mm galvanisé long 6m',
    'Tube rond acier ø42mm épaisseur 3mm long 6m',
    'Tube rond acier ø48mm épaisseur 4mm long 6m',
    'Plat acier 40x4mm galvanisé long 6m',
    'Plat acier 50x5mm galvanisé long 6m',
    'Fer rond lisse ø12mm acier S235 long 12m',
    'Fer rond lisse ø16mm acier S235 long 12m',
    'Tôle acier galvanisé ép.2mm 125x250cm',
    
    // QUINCAILLERIE ET FIXATIONS
    'Charnière à souder forte épaisseur 140mm',
    'Charnière à souder forte épaisseur 160mm',
    'Serrure 3 points portail',
    'Serrure applique verticale droite',
    'Serrure applique verticale gauche',
    'Verrou de sûreté à fouillot',
    'Cremone de sûreté 2 points',
    'Loquet automatique portillon',
    'Butée de portail au sol réglable',
    'Gâche électrique 12V rupture',
    'Ferme-porte hydraulique force 3',
    'Pivot de portail réglable',
    
    // AUVENTS ET MARQUISES
    'Auvent acier galvanisé 150x100cm',
    'Auvent acier galvanisé 200x120cm',
    'Marquise acier galvanisé 300x150cm',
    'Console de fixation auvent acier',
    'Couverture polycarbonate alvéolaire 16mm',
    'Gouttière acier galvanisé développé 25cm',
    'Descente EP acier galvanisé ø100mm',
    'Faîtage polycarbonate transparent',
    'Joint d\'étanchéité polycarbonate',
    'Fixation polycarbonate avec joint',
    
    // PROTECTION ET FINITION
    'Peinture antirouille glycérophtalique',
    'Peinture de finition acier glycéro',
    'Apprêt phosphatant métaux ferreux',
    'Peinture époxy bi-composant',
    'Thermolaquage poudre polyester',
    'Galvanisation à chaud',
    'Décapage sablage acier',
    'Traitement antirouille Fertan',
    'Peinture fer forgé martelée',
    'Cire de protection métaux'
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
            $type_ouvrage = trim($_POST['type_ouvrage'] ?? '');
            $materiaux = trim($_POST['materiaux'] ?? '');
            $dimensions = trim($_POST['dimensions'] ?? '');
            $finition = trim($_POST['finition'] ?? '');
            $fixation = trim($_POST['fixation'] ?? '');
            $accessoires = trim($_POST['accessoires'] ?? '');
            $norme_qualite = trim($_POST['norme_qualite'] ?? '');
            
            // Validations spécifiques ferronnerie
            if (empty($designation)) {
                throw new Exception("La désignation est obligatoire.");
            }
            if ($quantite <= 0) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }
            if ($prix_unitaire < 0) {
                throw new Exception("Le prix unitaire ne peut pas être négatif.");
            }
            
            // Validation dimensions si fournies (format : LxHxE ou LxH)
            if (!empty($dimensions) && !preg_match('/^\d+(\.\d+)?x\d+(\.\d+)?(x\d+(\.\d+)?)?(cm|mm|m)?$/i', $dimensions)) {
                throw new Exception("Format de dimensions invalide (ex: 300x200cm, 4x2m, 40x40x3mm).");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Insertion en base
            $stmt = $conn->prepare("
                INSERT INTO ferronnerie (
                    projet_id, devis_id, designation, quantite, unite, 
                    prix_unitaire, total, type_ouvrage, materiaux, 
                    dimensions, finition, fixation, accessoires, norme_qualite, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param(
                "iisdsdsssssss", 
                $projet_id, $devis_id, $designation, $quantite, $unite,
                $prix_unitaire, $total, $type_ouvrage, $materiaux,
                $dimensions, $finition, $fixation, $accessoires, $norme_qualite
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'ferronnerie');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'ferronnerie', 'Ajout', "Élément ajouté : {$designation}");
                
                $message = "Élément ferronnerie ajouté avec succès !";
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
            $type_ouvrage = trim($_POST['type_ouvrage'] ?? '');
            $materiaux = trim($_POST['materiaux'] ?? '');
            $dimensions = trim($_POST['dimensions'] ?? '');
            $finition = trim($_POST['finition'] ?? '');
            $fixation = trim($_POST['fixation'] ?? '');
            $accessoires = trim($_POST['accessoires'] ?? '');
            $norme_qualite = trim($_POST['norme_qualite'] ?? '');
            
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
            
            if (!empty($dimensions) && !preg_match('/^\d+(\.\d+)?x\d+(\.\d+)?(x\d+(\.\d+)?)?(cm|mm|m)?$/i', $dimensions)) {
                throw new Exception("Format de dimensions invalide (ex: 300x200cm, 4x2m, 40x40x3mm).");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Mise à jour en base
            $stmt = $conn->prepare("
                UPDATE ferronnerie SET 
                    designation = ?, quantite = ?, unite = ?, prix_unitaire = ?, 
                    total = ?, type_ouvrage = ?, materiaux = ?, dimensions = ?, 
                    finition = ?, fixation = ?, accessoires = ?, norme_qualite = ?, date_modification = NOW()
                WHERE id = ? AND projet_id = ? AND devis_id = ?
            ");
            
            $stmt->bind_param(
                "sdsdsssssssiii",
                $designation, $quantite, $unite, $prix_unitaire, $total,
                $type_ouvrage, $materiaux, $dimensions, $finition, 
                $fixation, $accessoires, $norme_qualite, $element_id, $projet_id, $devis_id
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'ferronnerie');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'ferronnerie', 'Modification', "Élément modifié : {$designation}");
                
                $message = "Élément ferronnerie modifié avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de la modification : " . $conn->error);
            }
            
        } elseif ($action == 'supprimer' && $element_id > 0) {
            // Récupération de la désignation avant suppression
            $stmt_get = $conn->prepare("SELECT designation FROM ferronnerie WHERE id = ? AND projet_id = ? AND devis_id = ?");
            $stmt_get->bind_param("iii", $element_id, $projet_id, $devis_id);
            $stmt_get->execute();
            $result_get = $stmt_get->get_result();
            $element_data = $result_get->fetch_assoc();
            
            if ($element_data) {
                // Suppression de l'élément
                $stmt = $conn->prepare("DELETE FROM ferronnerie WHERE id = ? AND projet_id = ? AND devis_id = ?");
                $stmt->bind_param("iii", $element_id, $projet_id, $devis_id);
                
                if ($stmt->execute()) {
                    // Mise à jour du récapitulatif
                    updateRecapitulatif($projet_id, $devis_id, 'ferronnerie');
                    
                    // Sauvegarde dans l'historique
                    sauvegarderHistorique($projet_id, $devis_id, 'ferronnerie', 'Suppression', "Élément supprimé : {$element_data['designation']}");
                    
                    $message = "Élément ferronnerie supprimé avec succès !";
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

// Récupération des éléments de ferronnerie pour affichage
$elements_ferronnerie = [];
$total_module = 0;

$stmt = $conn->prepare("
    SELECT id, designation, quantite, unite, prix_unitaire, total,
           type_ouvrage, materiaux, dimensions, finition, fixation, 
           accessoires, norme_qualite,
           DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation_fr,
           DATE_FORMAT(date_modification, '%d/%m/%Y %H:%i') as date_modification_fr
    FROM ferronnerie 
    WHERE projet_id = ? AND devis_id = ? 
    ORDER BY date_creation DESC
");

$stmt->bind_param("ii", $projet_id, $devis_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $elements_ferronnerie[] = $row;
    $total_module += $row['total'];
}

// Récupération de l'élément à modifier si nécessaire
$element_a_modifier = null;
if ($action == 'modifier' && $element_id > 0) {
    $stmt = $conn->prepare("
        SELECT * FROM ferronnerie 
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
    <title>Ferronnerie - <?= htmlspecialchars($projet_devis_info['nom_projet']) ?> | GSN ProDevis360°</title>
    
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
            
            /* Variables spécifiques ferronnerie */
            --steel-primary: #7f8c8d;
            --steel-light: #95a5a6;
            --steel-dark: #34495e;
            --iron-black: #2c3e50;
            --galva-silver: #bdc3c7;
            --rust-brown: #8b4513;
            --copper-orange: #b7410e;
            --bronze-gold: #cd7f32;
            --chrome-shine: #e8e8e8;
            --aluminium-gray: #848884;
            --forge-red: #8b0000;
            --weld-blue: #4682b4;
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
        .form-group select,
        .form-group textarea {
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition-fast);
            background: var(--secondary-white);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .form-group input[type="number"] {
            text-align: right;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* ===== SUGGESTIONS FERRONNERIE ===== */
        .suggestions-ferronnerie {
            background: linear-gradient(135deg, var(--steel-primary) 0%, var(--steel-dark) 100%);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .suggestions-ferronnerie h4 {
            color: var(--secondary-white);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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

        /* ===== CALCULATEUR FERRONNERIE ===== */
        .calculator-section {
            background: linear-gradient(135deg, var(--iron-black) 0%, var(--forge-red) 100%);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            color: var(--secondary-white);
        }

        .calculator-section h4 {
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
        }

        .calc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
            align-items: center;
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

        /* ===== BADGES SPÉCIALISÉS FERRONNERIE ===== */
        .badge-ouvrage {
            background: var(--steel-primary);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-materiaux {
            background: var(--iron-black);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-finition {
            background: var(--galva-silver);
            color: var(--neutral-dark);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-fixation {
            background: var(--bronze-gold);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-norme {
            background: var(--weld-blue);
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
            color: var(--steel-light);
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

        @keyframes metalShine {
            0% { transform: translateX(-100%) skewX(-15deg); opacity: 0; }
            50% { transform: translateX(0%) skewX(0deg); opacity: 1; }
            100% { transform: translateX(0%) skewX(0deg); opacity: 1; }
        }

        .metal-shine {
            animation: metalShine 0.8s ease-out;
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
                        <i class="fas fa-cog"></i>
                        Module Ferronnerie
                        <span class="module-badge">
                            <i class="fas fa-tools"></i>
                            Ouvrages Métalliques
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

        <!-- ===== FORMULAIRE FERRONNERIE ===== -->
        <div class="form-section fade-in-up">
            <h2>
                <i class="fas fa-<?= $element_a_modifier ? 'edit' : 'plus-circle' ?>"></i>
                <?= $element_a_modifier ? 'Modifier l\'élément ferronnerie' : 'Ajouter un élément ferronnerie' ?>
            </h2>

            <!-- Suggestions Ferronnerie -->
            <div class="suggestions-ferronnerie">
                <h4>
                    <i class="fas fa-cog"></i>
                    Suggestions Ferronnerie & Ouvrages Métalliques
                    <small>(Cliquez pour remplir automatiquement)</small>
                </h4>
                <div class="suggestions-grid">
                    <?php foreach ($suggestions_ferronnerie as $suggestion): ?>
                        <div class="suggestion-item" onclick="remplirSuggestion('<?= htmlspecialchars($suggestion, ENT_QUOTES) ?>')">
                            <?= htmlspecialchars($suggestion) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Calculateur Ferronnerie -->
            <div class="calculator-section">
                <h4>
                    <i class="fas fa-calculator"></i>
                    Calculateur Métallerie & Dimensionnement
                </h4>
                <div class="calc-grid">
                    <input type="number" id="calc_longueur" placeholder="Longueur (m)" class="calc-input" step="0.1" max="50">
                    <input type="number" id="calc_largeur" placeholder="Largeur (m)" class="calc-input" step="0.1" max="20">
                    <input type="number" id="calc_hauteur" placeholder="Hauteur (m)" class="calc-input" step="0.1" max="10">
                    <input type="number" id="calc_epaisseur" placeholder="Épaisseur (mm)" class="calc-input" step="0.5" max="50">
                    <button type="button" class="btn btn-sm btn-info" onclick="calculerSurface()">
                        <i class="fas fa-expand-arrows-alt"></i> Surface
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="calculerPoids()">
                        <i class="fas fa-weight"></i> Poids acier
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="calculerDebit()">
                        <i class="fas fa-cut"></i> Débit linéaire
                    </button>
                </div>
            </div>

            <form method="POST" action="" id="formFerronnerie">
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
                               placeholder="Ex: Portail battant acier galvanisé 3m x 2m"
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
                               placeholder="Ex: 1"
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
                            <option value="kg" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'kg') ? 'selected' : '' ?>>Kilogramme (kg)</option>
                            <option value="tonne" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'tonne') ? 'selected' : '' ?>>Tonne (t)</option>
                            <option value="forfait" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'forfait') ? 'selected' : '' ?>>Forfait</option>
                            <option value="lot" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'lot') ? 'selected' : '' ?>>Lot</option>
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
                               placeholder="Ex: 450000"
                               step="0.01"
                               min="0"
                               required>
                    </div>
                </div>

                <!-- Ligne 2 : Spécifications techniques -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="type_ouvrage">
                            <i class="fas fa-industry"></i>
                            Type d'ouvrage
                        </label>
                        <select id="type_ouvrage" name="type_ouvrage">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Portail" <?= ($element_a_modifier && $element_a_modifier['type_ouvrage'] === 'Portail') ? 'selected' : '' ?>>Portail</option>
                            <option value="Portillon" <?= ($element_a_modifier && $element_a_modifier['type_ouvrage'] === 'Portillon') ? 'selected' : '' ?>>Portillon</option>
                            <option value="Grille" <?= ($element_a_modifier && $element_a_modifier['type_ouvrage'] === 'Grille') ? 'selected' : '' ?>>Grille de défense</option>
                            <option value="Garde-corps" <?= ($element_a_modifier && $element_a_modifier['type_ouvrage'] === 'Garde-corps') ? 'selected' : '' ?>>Garde-corps</option>
                            <option value="Escalier" <?= ($element_a_modifier && $element_a_modifier['type_ouvrage'] === 'Escalier') ? 'selected' : '' ?>>Escalier métallique</option>
                            <option value="Structure" <?= ($element_a_modifier && $element_a_modifier['type_ouvrage'] === 'Structure') ? 'selected' : '' ?>>Structure porteuse</option>
                            <option value="Auvent" <?= ($element_a_modifier && $element_a_modifier['type_ouvrage'] === 'Auvent') ? 'selected' : '' ?>>Auvent/Marquise</option>
                            <option value="Serrurerie" <?= ($element_a_modifier && $element_a_modifier['type_ouvrage'] === 'Serrurerie') ? 'selected' : '' ?>>Serrurerie courante</option>
                            <option value="Métallerie" <?= ($element_a_modifier && $element_a_modifier['type_ouvrage'] === 'Métallerie') ? 'selected' : '' ?>>Métallerie diverses</option>
                            <option value="Protection" <?= ($element_a_modifier && $element_a_modifier['type_ouvrage'] === 'Protection') ? 'selected' : '' ?>>Protection/Sécurité</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="materiaux">
                            <i class="fas fa-cubes"></i>
                            Matériaux
                        </label>
                        <select id="materiaux" name="materiaux">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Acier S235" <?= ($element_a_modifier && $element_a_modifier['materiaux'] === 'Acier S235') ? 'selected' : '' ?>>Acier S235 (standard)</option>
                            <option value="Acier S355" <?= ($element_a_modifier && $element_a_modifier['materiaux'] === 'Acier S355') ? 'selected' : '' ?>>Acier S355 (haute résistance)</option>
                            <option value="Acier galvanisé" <?= ($element_a_modifier && $element_a_modifier['materiaux'] === 'Acier galvanisé') ? 'selected' : '' ?>>Acier galvanisé</option>
                            <option value="Acier inoxydable" <?= ($element_a_modifier && $element_a_modifier['materiaux'] === 'Acier inoxydable') ? 'selected' : '' ?>>Acier inoxydable</option>
                            <option value="Aluminium" <?= ($element_a_modifier && $element_a_modifier['materiaux'] === 'Aluminium') ? 'selected' : '' ?>>Aluminium</option>
                            <option value="Fer forgé" <?= ($element_a_modifier && $element_a_modifier['materiaux'] === 'Fer forgé') ? 'selected' : '' ?>>Fer forgé</option>
                            <option value="Fonte" <?= ($element_a_modifier && $element_a_modifier['materiaux'] === 'Fonte') ? 'selected' : '' ?>>Fonte</option>
                            <option value="Laiton" <?= ($element_a_modifier && $element_a_modifier['materiaux'] === 'Laiton') ? 'selected' : '' ?>>Laiton</option>
                            <option value="Bronze" <?= ($element_a_modifier && $element_a_modifier['materiaux'] === 'Bronze') ? 'selected' : '' ?>>Bronze</option>
                            <option value="Mixte" <?= ($element_a_modifier && $element_a_modifier['materiaux'] === 'Mixte') ? 'selected' : '' ?>>Matériaux mixtes</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dimensions">
                            <i class="fas fa-expand-arrows-alt"></i>
                            Dimensions
                        </label>
                        <input type="text" 
                               id="dimensions" 
                               name="dimensions" 
                               value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['dimensions']) : '' ?>"
                               placeholder="Ex: 300x200cm, 4x2m, 40x40x3mm"
                               pattern="^[0-9]+(\.[0-9]+)?x[0-9]+(\.[0-9]+)?(x[0-9]+(\.[0-9]+)?)?(cm|mm|m)?$"
                               title="Format: LxH ou LxHxE avec unité (ex: 300x200cm)">
                    </div>

                    <div class="form-group">
                        <label for="finition">
                            <i class="fas fa-spray-can"></i>
                            Finition
                        </label>
                        <select id="finition" name="finition">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Brut" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Brut') ? 'selected' : '' ?>>Brut (sans finition)</option>
                            <option value="Galvanisé à chaud" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Galvanisé à chaud') ? 'selected' : '' ?>>Galvanisé à chaud</option>
                            <option value="Thermolaqué" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Thermolaqué') ? 'selected' : '' ?>>Thermolaquage poudre</option>
                            <option value="Peinture antirouille" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Peinture antirouille') ? 'selected' : '' ?>>Peinture antirouille</option>
                            <option value="Peinture époxy" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Peinture époxy') ? 'selected' : '' ?>>Peinture époxy</option>
                            <option value="Anodisé" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Anodisé') ? 'selected' : '' ?>>Anodisé (aluminium)</option>
                            <option value="Poli miroir" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Poli miroir') ? 'selected' : '' ?>>Poli miroir</option>
                            <option value="Brossé" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Brossé') ? 'selected' : '' ?>>Brossé</option>
                            <option value="Patiné" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Patiné') ? 'selected' : '' ?>>Patiné/Vieilli</option>
                            <option value="Zingué" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Zingué') ? 'selected' : '' ?>>Zingué</option>
                        </select>
                    </div>
                </div>

                <!-- Ligne 3 : Accessoires et qualité -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="fixation">
                            <i class="fas fa-tools"></i>
                            Mode de fixation
                        </label>
                        <select id="fixation" name="fixation">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Scellement chimique" <?= ($element_a_modifier && $element_a_modifier['fixation'] === 'Scellement chimique') ? 'selected' : '' ?>>Scellement chimique</option>
                            <option value="Platines boulonnées" <?= ($element_a_modifier && $element_a_modifier['fixation'] === 'Platines boulonnées') ? 'selected' : '' ?>>Platines boulonnées</option>
                            <option value="Soudage" <?= ($element_a_modifier && $element_a_modifier['fixation'] === 'Soudage') ? 'selected' : '' ?>>Soudage</option>
                            <option value="Chevillage" <?= ($element_a_modifier && $element_a_modifier['fixation'] === 'Chevillage') ? 'selected' : '' ?>>Chevillage mécanique</option>
                            <option value="Scellement béton" <?= ($element_a_modifier && $element_a_modifier['fixation'] === 'Scellement béton') ? 'selected' : '' ?>>Scellement béton</option>
                            <option value="Fixation murale" <?= ($element_a_modifier && $element_a_modifier['fixation'] === 'Fixation murale') ? 'selected' : '' ?>>Fixation murale</option>
                            <option value="Posé libre" <?= ($element_a_modifier && $element_a_modifier['fixation'] === 'Posé libre') ? 'selected' : '' ?>>Posé libre</option>
                            <option value="Encastrement" <?= ($element_a_modifier && $element_a_modifier['fixation'] === 'Encastrement') ? 'selected' : '' ?>>Encastrement</option>
                            <option value="Suspension" <?= ($element_a_modifier && $element_a_modifier['fixation'] === 'Suspension') ? 'selected' : '' ?>>Suspension</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="accessoires">
                            <i class="fas fa-puzzle-piece"></i>
                            Accessoires inclus
                        </label>
                        <textarea id="accessoires" 
                                  name="accessoires" 
                                  placeholder="Ex: Serrure 3 points, charnières renforcées, motorisation..."
                                  rows="3"><?= $element_a_modifier ? htmlspecialchars($element_a_modifier['accessoires']) : '' ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="norme_qualite">
                            <i class="fas fa-certificate"></i>
                            Norme/Qualité
                        </label>
                        <select id="norme_qualite" name="norme_qualite">
                            <option value="">-- Sélectionnez --</option>
                            <option value="NF" <?= ($element_a_modifier && $element_a_modifier['norme_qualite'] === 'NF') ? 'selected' : '' ?>>Norme NF</option>
                            <option value="CE" <?= ($element_a_modifier && $element_a_modifier['norme_qualite'] === 'CE') ? 'selected' : '' ?>>Marquage CE</option>
                            <option value="A2P" <?= ($element_a_modifier && $element_a_modifier['norme_qualite'] === 'A2P') ? 'selected' : '' ?>>Certification A2P</option>
                            <option value="Qualimarine" <?= ($element_a_modifier && $element_a_modifier['norme_qualite'] === 'Qualimarine') ? 'selected' : '' ?>>Label Qualimarine</option>
                            <option value="Acotherm" <?= ($element_a_modifier && $element_a_modifier['norme_qualite'] === 'Acotherm') ? 'selected' : '' ?>>Label Acotherm</option>
                            <option value="Origine France" <?= ($element_a_modifier && $element_a_modifier['norme_qualite'] === 'Origine France') ? 'selected' : '' ?>>Origine France Garantie</option>
                            <option value="ISO 9001" <?= ($element_a_modifier && $element_a_modifier['norme_qualite'] === 'ISO 9001') ? 'selected' : '' ?>>ISO 9001</option>
                            <option value="Standard" <?= ($element_a_modifier && $element_a_modifier['norme_qualite'] === 'Standard') ? 'selected' : '' ?>>Qualité standard</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-calculator"></i>
                            Actions rapides
                        </label>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <button type="button" 
                                    class="btn btn-sm btn-info" 
                                    onclick="calculerPoidsPiece()"
                                    title="Alt+P">
                                <i class="fas fa-weight"></i>
                                Poids total
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-warning" 
                                    onclick="estimerPrix()"
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
                            <a href="ferronnerie.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-secondary ml-2">
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

        <!-- ===== TABLEAU DES ÉLÉMENTS FERRONNERIE ===== -->
        <div class="table-container fade-in-up">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list"></i>
                    Éléments ferronnerie
                    <span class="badge-ouvrage ml-2"><?= count($elements_ferronnerie) ?> élément(s)</span>
                </h3>
                <div class="table-actions">
                    <span class="total-amount">
                        <i class="fas fa-cog"></i>
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
                            <th><i class="fas fa-industry"></i> Type</th>
                            <th><i class="fas fa-cubes"></i> Matériaux</th>
                            <th><i class="fas fa-expand-arrows-alt"></i> Dimensions</th>
                            <th><i class="fas fa-spray-can"></i> Finition</th>
                            <th><i class="fas fa-tools"></i> Fixation</th>
                            <th><i class="fas fa-certificate"></i> Norme</th>
                            <th><i class="fas fa-euro-sign"></i> Total</th>
                            <th><i class="fas fa-calendar"></i> Créé le</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($elements_ferronnerie)): ?>
                            <tr>
                                <td colspan="14" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-cog fa-3x mb-3 d-block"></i>
                                        <p>Aucun élément ferronnerie ajouté pour ce devis.</p>
                                        <small>Utilisez le formulaire ci-dessus pour ajouter des ouvrages métalliques.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($elements_ferronnerie as $element): ?>
                                <tr>
                                    <td><strong><?= $counter++ ?></strong></td>
                                    <td>
                                        <strong><?= htmlspecialchars($element['designation']) ?></strong>
                                        <?php if (!empty($element['accessoires'])): ?>
                                            <br><small class="text-info">
                                                <i class="fas fa-puzzle-piece"></i> <?= htmlspecialchars($element['accessoires']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= number_format($element['quantite'], 2, ',', ' ') ?></strong></td>
                                    <td><span class="badge-ouvrage"><?= htmlspecialchars($element['unite']) ?></span></td>
                                    <td><strong><?= number_format($element['prix_unitaire'], 0, ',', ' ') ?></strong> FCFA</td>
                                    <td>
                                        <?php if (!empty($element['type_ouvrage'])): ?>
                                            <span class="badge-ouvrage">
                                                <i class="fas fa-industry"></i>
                                                <?= htmlspecialchars($element['type_ouvrage']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['materiaux'])): ?>
                                            <span class="badge-materiaux">
                                                <i class="fas fa-cubes"></i>
                                                <?= htmlspecialchars($element['materiaux']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['dimensions'])): ?>
                                            <span class="badge-ouvrage">
                                                <i class="fas fa-expand-arrows-alt"></i>
                                                <?= htmlspecialchars($element['dimensions']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['finition'])): ?>
                                            <span class="badge-finition">
                                                <i class="fas fa-spray-can"></i>
                                                <?= htmlspecialchars($element['finition']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['fixation'])): ?>
                                            <span class="badge-fixation">
                                                <i class="fas fa-tools"></i>
                                                <?= htmlspecialchars($element['fixation']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['norme_qualite'])): ?>
                                            <span class="badge-norme">
                                                <i class="fas fa-certificate"></i>
                                                <?= htmlspecialchars($element['norme_qualite']) ?>
                                            </span>
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
                                            <a href="ferronnerie.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&action=modifier&element_id=<?= $element['id'] ?>" 
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
                        <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=ferronnerie" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-clock"></i> Voir tout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TOTAUX MODULE FERRONNERIE ===== -->
        <div class="module-summary fade-in-up">
            <h3>
                <i class="fas fa-cog"></i>
                Total Module Ferronnerie
            </h3>
            <div class="total-amount pulse-animation">
                <?= number_format($total_module, 0, ',', ' ') ?> FCFA
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Mis à jour automatiquement • <?= count($elements_ferronnerie) ?> élément(s)
                <?php if ($total_module > 0 && count($elements_ferronnerie) > 0): ?>
                    • Moyenne: <?= number_format($total_module / count($elements_ferronnerie), 0, ',', ' ') ?> FCFA/élément
                <?php endif; ?>
            </small>
        </div>

    </div>

    <!-- ===== JAVASCRIPT SPÉCIALISÉ FERRONNERIE ===== -->
    <script>
        // ===== CONFIGURATION ET VARIABLES FERRONNERIE =====
        const PRIX_FERRONNERIE = {
            // Types d'ouvrages (prix forfaitaires ou au m²/ml/kg)
            'portail_battant': { base: 450000, factor: 1.0 }, // Prix forfait portail 3x2m
            'portail_coulissant': { base: 620000, factor: 1.3 },
            'portillon': { base: 180000, factor: 0.4 },
            'grille_defense': { base: 35000, factor: 1.0 }, // Prix au m²
            'garde_corps': { base: 25000, factor: 1.0 }, // Prix au ml
            'escalier_droit': { base: 80000, factor: 1.0 }, // Prix par marche
            'escalier_helicoidal': { base: 450000, factor: 2.5 },
            'structure_ipn': { base: 18000, factor: 1.0 }, // Prix au ml
            'auvent': { base: 65000, factor: 1.0 }, // Prix au m²
            
            // Matériaux (facteurs multiplicateurs)
            'acier_s235': { factor: 1.0 },
            'acier_s355': { factor: 1.3 },
            'acier_galvanise': { factor: 1.8 },
            'acier_inoxydable': { factor: 4.5 },
            'aluminium': { factor: 2.8 },
            'fer_forge': { factor: 3.2 },
            'fonte': { factor: 2.1 },
            
            // Finitions (facteurs multiplicateurs)
            'brut': { factor: 1.0 },
            'galvanise_chaud': { factor: 1.6 },
            'thermolaque': { factor: 1.4 },
            'peinture_antirouille': { factor: 1.2 },
            'peinture_epoxy': { factor: 1.5 },
            'anodise': { factor: 1.3 },
            'poli_miroir': { factor: 2.0 },
            
            // Acier au poids (prix par kg)
            'acier_kg': { base: 1200, factor: 1.0 }
        };

        const DENSITE_MATERIAUX = {
            // Densité en kg/dm³
            'acier': 7.85,
            'aluminium': 2.70,
            'fonte': 7.20,
            'laiton': 8.50,
            'bronze': 8.90,
            'inox': 7.90
        };

        const PROFILES_ACIER = {
            // Poids linéaire en kg/ml pour profiles courants
            'ipn 100': 8.34,
            'ipn 120': 11.10,
            'ipn 140': 14.30,
            'ipn 160': 17.90,
            'ipn 180': 21.90,
            'ipn 200': 26.20,
            'ipn 220': 31.10,
            'ipn 240': 36.20,
            'hea 100': 16.70,
            'hea 120': 19.90,
            'hea 140': 24.70,
            'hea 160': 30.40,
            'hea 180': 35.50,
            'hea 200': 42.30,
            'heb 100': 20.40,
            'heb 120': 26.70,
            'heb 140': 33.70,
            'heb 160': 42.60,
            'heb 180': 51.20,
            'heb 200': 61.30,
            'tube 40x40x3': 3.45,
            'tube 50x50x4': 5.82,
            'tube 60x60x5': 8.77,
            'tube 80x80x6': 14.20,
            'tube 100x100x8': 23.60
        };

        // ===== FONCTIONS CALCULATRICES FERRONNERIE =====
        
        /**
         * Remplir automatiquement le formulaire avec une suggestion
         */
        function remplirSuggestion(suggestion) {
            const designationField = document.getElementById('designation');
            const quantiteField = document.getElementById('quantite');
            const uniteField = document.getElementById('unite');
            const prixField = document.getElementById('prix_unitaire');
            const typeField = document.getElementById('type_ouvrage');
            const materiauxField = document.getElementById('materiaux');
            const dimensionsField = document.getElementById('dimensions');
            const finitionField = document.getElementById('finition');
            const fixationField = document.getElementById('fixation');
            const normeField = document.getElementById('norme_qualite');
            
            // Remplir la désignation
            designationField.value = suggestion;
            
            // Analyse intelligente de la suggestion
            const sug = suggestion.toLowerCase();
            
            // Déterminer le type d'ouvrage
            if (sug.includes('portail battant')) {
                typeField.value = 'Portail';
                quantiteField.value = '1';
                uniteField.value = 'unité';
                fixationField.value = 'Platines boulonnées';
            } else if (sug.includes('portail coulissant')) {
                typeField.value = 'Portail';
                quantiteField.value = '1';
                uniteField.value = 'unité';
                fixationField.value = 'Scellement chimique';
            } else if (sug.includes('portillon')) {
                typeField.value = 'Portillon';
                quantiteField.value = '1';
                uniteField.value = 'unité';
                fixationField.value = 'Platines boulonnées';
            } else if (sug.includes('grille')) {
                typeField.value = 'Grille';
                quantiteField.value = '3';
                uniteField.value = 'm²';
                fixationField.value = 'Chevillage';
            } else if (sug.includes('garde-corps')) {
                typeField.value = 'Garde-corps';
                quantiteField.value = '10';
                uniteField.value = 'ml';
                fixationField.value = 'Platines boulonnées';
            } else if (sug.includes('escalier')) {
                typeField.value = 'Escalier';
                quantiteField.value = '1';
                uniteField.value = 'unité';
                fixationField.value = 'Soudage';
            } else if (sug.includes('auvent') || sug.includes('marquise')) {
                typeField.value = 'Auvent';
                quantiteField.value = '6';
                uniteField.value = 'm²';
                fixationField.value = 'Fixation murale';
            } else if (sug.includes('ipn') || sug.includes('hea') || sug.includes('heb')) {
                typeField.value = 'Structure';
                quantiteField.value = '12';
                uniteField.value = 'ml';
                fixationField.value = 'Soudage';
            }
            
            // Déterminer les matériaux
            if (sug.includes('acier galvanisé') || sug.includes('galvanisé')) {
                materiauxField.value = 'Acier galvanisé';
                finitionField.value = 'Galvanisé à chaud';
            } else if (sug.includes('acier s355')) {
                materiauxField.value = 'Acier S355';
                finitionField.value = 'Peinture antirouille';
            } else if (sug.includes('aluminium') || sug.includes('alu')) {
                materiauxField.value = 'Aluminium';
                finitionField.value = 'Thermolaqué';
            } else if (sug.includes('inox') || sug.includes('inoxydable')) {
                materiauxField.value = 'Acier inoxydable';
                finitionField.value = 'Brossé';
            } else if (sug.includes('fer forgé')) {
                materiauxField.value = 'Fer forgé';
                finitionField.value = 'Peinture époxy';
            } else {
                materiauxField.value = 'Acier S235';
                finitionField.value = 'Peinture antirouille';
            }
            
            // Extraire les dimensions (format : NxNm ou NxNcm)
            const dimensionMatch = sug.match(/(\d+(?:\.\d+)?)x(\d+(?:\.\d+)?)\s*(m|cm)?/);
            if (dimensionMatch) {
                let dim = dimensionMatch[1] + 'x' + dimensionMatch[2];
                if (dimensionMatch[3] === 'cm') {
                    dim += 'cm';
                } else {
                    dim += 'm';
                }
                dimensionsField.value = dim;
            }
            
            // Déterminer les normes selon le type
            if (sug.includes('portail') || sug.includes('portillon')) {
                normeField.value = 'NF';
            } else if (sug.includes('garde-corps')) {
                normeField.value = 'NF';
            } else if (sug.includes('grille')) {
                normeField.value = 'A2P';
            }
            
            // Estimer le prix
            const estimation = estimerPrixFerronnerie(suggestion);
            if (estimation > 0) {
                prixField.value = estimation;
            }
            
            // Animation visuelle
            designationField.style.background = 'linear-gradient(135deg, #f0f0f0 0%, #ffffff 100%)';
            setTimeout(() => {
                designationField.style.background = '';
            }, 1000);
            
            // Focus sur le champ quantité
            quantiteField.focus();
            quantiteField.select();
        }

        /**
         * Estimation automatique des prix ferronnerie
         */
        function estimerPrixFerronnerie(designation) {
            const des = designation.toLowerCase();
            let prix = 0;
            
            // Déterminer le type d'ouvrage de base
            let typeBase = '';
            if (des.includes('portail battant')) typeBase = 'portail_battant';
            else if (des.includes('portail coulissant')) typeBase = 'portail_coulissant';
            else if (des.includes('portillon')) typeBase = 'portillon';
            else if (des.includes('grille')) typeBase = 'grille_defense';
            else if (des.includes('garde-corps')) typeBase = 'garde_corps';
            else if (des.includes('escalier droit')) typeBase = 'escalier_droit';
            else if (des.includes('escalier hélicoïdal') || des.includes('helicoidal')) typeBase = 'escalier_helicoidal';
            else if (des.includes('auvent') || des.includes('marquise')) typeBase = 'auvent';
            else if (des.includes('ipn') || des.includes('structure')) typeBase = 'structure_ipn';
            else typeBase = 'portail_battant'; // Par défaut
            
            // Prix de base
            if (PRIX_FERRONNERIE[typeBase]) {
                prix = PRIX_FERRONNERIE[typeBase].base;
            }
            
            // Facteur matériaux
            let facteurMat = 1.0;
            if (des.includes('acier galvanisé') || des.includes('galvanisé')) facteurMat = PRIX_FERRONNERIE.acier_galvanise.factor;
            else if (des.includes('acier s355')) facteurMat = PRIX_FERRONNERIE.acier_s355.factor;
            else if (des.includes('aluminium')) facteurMat = PRIX_FERRONNERIE.aluminium.factor;
            else if (des.includes('inox') || des.includes('inoxydable')) facteurMat = PRIX_FERRONNERIE.acier_inoxydable.factor;
            else if (des.includes('fer forgé')) facteurMat = PRIX_FERRONNERIE.fer_forge.factor;
            else if (des.includes('fonte')) facteurMat = PRIX_FERRONNERIE.fonte.factor;
            
            prix *= facteurMat;
            
            // Facteur finition
            let facteurFin = 1.0;
            if (des.includes('thermolaqué') || des.includes('thermolaque')) facteurFin = PRIX_FERRONNERIE.thermolaque.factor;
            else if (des.includes('galvanisé à chaud')) facteurFin = PRIX_FERRONNERIE.galvanise_chaud.factor;
            else if (des.includes('époxy')) facteurFin = PRIX_FERRONNERIE.peinture_epoxy.factor;
            else if (des.includes('poli miroir')) facteurFin = PRIX_FERRONNERIE.poli_miroir.factor;
            else if (des.includes('anodisé')) facteurFin = PRIX_FERRONNERIE.anodise.factor;
            
            prix *= facteurFin;
            
            // Facteurs spéciaux
            if (des.includes('motorisation') || des.includes('motorisé')) prix *= 1.8;
            if (des.includes('télécommande')) prix += 45000;
            if (des.includes('serrure 3 points')) prix += 85000;
            if (des.includes('visiophone')) prix += 120000;
            if (des.includes('anti-effraction') || des.includes('renforcé')) prix *= 1.4;
            
            // Ajustement selon les dimensions
            const dimensionMatch = des.match(/(\d+(?:\.\d+)?)x(\d+(?:\.\d+)?)/);
            if (dimensionMatch) {
                const dim1 = parseFloat(dimensionMatch[1]);
                const dim2 = parseFloat(dimensionMatch[2]);
                const surface = dim1 * dim2;
                
                if (surface > 8) prix *= 1.3; // Grandes dimensions
                else if (surface < 2) prix *= 0.7; // Petites dimensions
            }
            
            return Math.round(prix);
        }

        /**
         * Calculer la surface d'un ouvrage
         */
        function calculerSurface() {
            const longueur = parseFloat(document.getElementById('calc_longueur').value) || 0;
            const largeur = parseFloat(document.getElementById('calc_largeur').value) || 0;
            
            if (longueur > 0 && largeur > 0) {
                const surface = longueur * largeur;
                
                document.getElementById('quantite').value = surface.toFixed(2);
                document.getElementById('unite').value = 'm²';
                document.getElementById('dimensions').value = `${longueur}x${largeur}m`;
                
                showToast(`📐 Surface calculée: ${surface.toFixed(2)} m²\n` +
                         `Dimensions: ${longueur}m × ${largeur}m`, 'success');
            } else {
                showToast('⚠️ Veuillez saisir longueur et largeur.', 'warning');
            }
        }

        /**
         * Calculer le poids d'acier
         */
        function calculerPoids() {
            const longueur = parseFloat(document.getElementById('calc_longueur').value) || 0;
            const largeur = parseFloat(document.getElementById('calc_largeur').value) || 0;
            const hauteur = parseFloat(document.getElementById('calc_hauteur').value) || 0;
            const epaisseur = parseFloat(document.getElementById('calc_epaisseur').value) || 0;
            
            if (longueur > 0 && largeur > 0 && epaisseur > 0) {
                // Calcul pour tôle plane
                const volume = (longueur * largeur * epaisseur / 1000); // Volume en dm³
                const poids = volume * DENSITE_MATERIAUX.acier; // Poids en kg
                
                document.getElementById('quantite').value = poids.toFixed(1);
                document.getElementById('unite').value = 'kg';
                
                showToast(`⚖️ Poids calculé: ${poids.toFixed(1)} kg\n` +
                         `Tôle ${longueur}×${largeur}m ép.${epaisseur}mm\n` +
                         `Densité acier: ${DENSITE_MATERIAUX.acier} kg/dm³`, 'info');
                         
            } else if (longueur > 0) {
                // Calculer pour un profilé
                const designation = document.getElementById('designation').value.toLowerCase();
                let poidsLineaire = 0;
                
                // Recherche du profil dans les standards
                for (const [profile, poids] of Object.entries(PROFILES_ACIER)) {
                    if (designation.includes(profile)) {
                        poidsLineaire = poids;
                        break;
                    }
                }
                
                if (poidsLineaire > 0) {
                    const poidsTotal = longueur * poidsLineaire;
                    document.getElementById('quantite').value = poidsTotal.toFixed(1);
                    document.getElementById('unite').value = 'kg';
                    
                    showToast(`⚖️ Poids calculé: ${poidsTotal.toFixed(1)} kg\n` +
                             `Profil: ${poidsLineaire} kg/ml × ${longueur}m`, 'info');
                } else {
                    showToast('⚠️ Profil non reconnu. Utilisez les dimensions complètes.', 'warning');
                }
            } else {
                showToast('⚠️ Veuillez saisir au minimum la longueur.', 'warning');
            }
        }

        /**
         * Calculer le débit linéaire nécessaire
         */
        function calculerDebit() {
            const longueur = parseFloat(document.getElementById('calc_longueur').value) || 0;
            const largeur = parseFloat(document.getElementById('calc_largeur').value) || 0;
            const hauteur = parseFloat(document.getElementById('calc_hauteur').value) || 0;
            const quantite = parseFloat(document.getElementById('quantite').value) || 1;
            
            if (longueur > 0 && largeur > 0) {
                // Calcul périmètre pour un cadre rectangulaire
                const perimetre = 2 * (longueur + largeur);
                const debitTotal = perimetre * quantite;
                
                // Ajouter les traverses intérieures (estimation 20% du périmètre)
                const debitAvecTraverses = debitTotal * 1.2;
                
                showToast(`📏 Débit linéaire calculé:\n` +
                         `Périmètre: ${perimetre.toFixed(1)} ml/unité\n` +
                         `Total: ${debitAvecTraverses.toFixed(1)} ml (avec traverses)\n` +
                         `Quantité: ${quantite} unité(s)`, 'info');
                         
                // Suggestion unité
                document.getElementById('unite').value = 'ml';
                if (document.getElementById('quantite').value == quantite) {
                    document.getElementById('quantite').value = debitAvecTraverses.toFixed(1);
                }
            } else {
                showToast('⚠️ Veuillez saisir longueur et largeur.', 'warning');
            }
        }

        /**
         * Calculer le poids total d'une pièce
         */
        function calculerPoidsPiece() {
            const quantite = parseFloat(document.getElementById('quantite').value) || 0;
            const unite = document.getElementById('unite').value;
            const dimensions = document.getElementById('dimensions').value;
            const materiaux = document.getElementById('materiaux').value;
            
            if (quantite > 0 && dimensions) {
                const dimMatch = dimensions.match(/(\d+(?:\.\d+)?)x(\d+(?:\.\d+)?)(?:x(\d+(?:\.\d+)?))?(m|cm|mm)?/);
                if (dimMatch) {
                    let l = parseFloat(dimMatch[1]);
                    let w = parseFloat(dimMatch[2]);
                    let h = parseFloat(dimMatch[3]) || 5; // Épaisseur par défaut 5mm
                    const unit = dimMatch[4] || 'm';
                    
                    // Conversion en mètres
                    if (unit === 'cm') {
                        l /= 100; w /= 100; h /= 100;
                    } else if (unit === 'mm') {
                        l /= 1000; w /= 1000; h /= 1000;
                    }
                    
                    // Déterminer la densité du matériau
                    let densite = DENSITE_MATERIAUX.acier; // Par défaut
                    if (materiaux.toLowerCase().includes('aluminium')) densite = DENSITE_MATERIAUX.aluminium;
                    else if (materiaux.toLowerCase().includes('inox')) densite = DENSITE_MATERIAUX.inox;
                    else if (materiaux.toLowerCase().includes('fonte')) densite = DENSITE_MATERIAUX.fonte;
                    else if (materiaux.toLowerCase().includes('laiton')) densite = DENSITE_MATERIAUX.laiton;
                    else if (materiaux.toLowerCase().includes('bronze')) densite = DENSITE_MATERIAUX.bronze;
                    
                    let poidsUnitaire = 0;
                    
                    if (unite === 'm²') {
                        // Poids pour une surface (tôle)
                        poidsUnitaire = l * w * (h/1000) * densite * 1000; // kg/m²
                    } else if (unite === 'ml') {
                        // Poids linéaire (tube, profilé)
                        const section = w * (h/1000); // Section en m²
                        poidsUnitaire = section * densite * 1000; // kg/ml
                    } else {
                        // Poids total pièce
                        const volume = l * w * h; // Volume en m³
                        poidsUnitaire = volume * densite * 1000; // kg total
                    }
                    
                    const poidsTotal = poidsUnitaire * quantite;
                    
                    showToast(`⚖️ Poids calculé: ${poidsTotal.toFixed(1)} kg total\n` +
                             `Poids unitaire: ${poidsUnitaire.toFixed(1)} kg/${unite}\n` +
                             `Matériau: ${materiaux} (${densite} kg/dm³)\n` +
                             `Dimensions: ${dimensions}`, 'info');
                }
            } else {
                showToast('⚠️ Veuillez saisir quantité et dimensions.', 'warning');
            }
        }

        /**
         * Estimation automatique du prix selon les spécifications
         */
        function estimerPrix() {
            const designation = document.getElementById('designation').value;
            const typeOuvrage = document.getElementById('type_ouvrage').value;
            const materiaux = document.getElementById('materiaux').value;
            const finition = document.getElementById('finition').value;
            const dimensions = document.getElementById('dimensions').value;
            
            if (!designation.trim()) {
                alert('⚠️ Veuillez d\'abord saisir une désignation pour l\'estimation.');
                document.getElementById('designation').focus();
                return;
            }
            
            let prixEstime = estimerPrixFerronnerie(designation);
            
            // Ajustements selon les sélections
            if (typeOuvrage === 'Structure' && materiaux === 'Acier S355') {
                prixEstime *= 1.4;
            }
            if (finition === 'Thermolaqué') {
                prixEstime *= 1.3;
            }
            if (dimensions && dimensions.includes('x')) {
                const dimMatch = dimensions.match(/(\d+(?:\.\d+)?)x(\d+(?:\.\d+)?)/);
                if (dimMatch) {
                    const surface = parseFloat(dimMatch[1]) * parseFloat(dimMatch[2]);
                    if (surface > 10) prixEstime *= 1.2; // Grandes dimensions
                }
            }
            
            if (prixEstime > 0) {
                document.getElementById('prix_unitaire').value = prixEstime;
                
                const quantite = parseFloat(document.getElementById('quantite').value) || 1;
                const total = prixEstime * quantite;
                
                showToast(`💰 Prix estimé: ${prixEstime.toLocaleString()} FCFA/unité\n` +
                         `📊 Total: ${total.toLocaleString()} FCFA\n` +
                         `Type: ${typeOuvrage || 'Standard'}\n` +
                         `Matériau: ${materiaux || 'Acier S235'}`, 'info');
                
                // Animation
                const prixField = document.getElementById('prix_unitaire');
                prixField.style.background = 'linear-gradient(135deg, #e8f4f8 0%, #ffffff 100%)';
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
                document.getElementById('formFerronnerie').reset();
                document.getElementById('unite').value = 'unité';
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
         * Raccourcis clavier spécialisés ferronnerie
         */
        function initRaccourcisClavier() {
            document.addEventListener('keydown', function(e) {
                // Alt + D = Focus désignation
                if (e.altKey && e.key === 'd') {
                    e.preventDefault();
                    document.getElementById('designation').focus();
                    showToast('🎯 Focus sur Désignation', 'info');
                }
                
                // Alt + S = Calculer surface
                if (e.altKey && e.key === 's') {
                    e.preventDefault();
                    calculerSurface();
                }
                
                // Alt + P = Calculer poids
                if (e.altKey && e.key === 'p') {
                    e.preventDefault();
                    calculerPoids();
                }
                
                // Alt + L = Calculer débit linéaire
                if (e.altKey && e.key === 'l') {
                    e.preventDefault();
                    calculerDebit();
                }
                
                // Alt + W = Calculer poids pièce
                if (e.altKey && e.key === 'w') {
                    e.preventDefault();
                    calculerPoidsPiece();
                }
                
                // Alt + E = Estimation prix
                if (e.altKey && e.key === 'e') {
                    e.preventDefault();
                    estimerPrix();
                }
                
                // Alt + T = Focus type ouvrage
                if (e.altKey && e.key === 't') {
                    e.preventDefault();
                    document.getElementById('type_ouvrage').focus();
                    showToast('🏭 Focus sur Type ouvrage', 'info');
                }
                
                // Alt + M = Focus matériaux
                if (e.altKey && e.key === 'm') {
                    e.preventDefault();
                    document.getElementById('materiaux').focus();
                    showToast('🔩 Focus sur Matériaux', 'info');
                }
                
                // Alt + F = Focus finition
                if (e.altKey && e.key === 'f') {
                    e.preventDefault();
                    document.getElementById('finition').focus();
                    showToast('🎨 Focus sur Finition', 'info');
                }
                
                // Ctrl + Entrée = Soumettre formulaire
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('formFerronnerie').submit();
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
                background: ${type === 'info' ? '#7f8c8d' : type === 'warning' ? '#e67e22' : type === 'success' ? '#27ae60' : '#e74c3c'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                z-index: 9999;
                max-width: 400px;
                white-space: pre-line;
                animation: slideInRight 0.4s ease-out;
                font-family: 'Inter', sans-serif;
                font-size: 0.9rem;
                line-height: 1.4;
                cursor: pointer;
            `;
            
            const icon = type === 'info' ? '🔧' : type === 'warning' ? '⚠️' : type === 'success' ? '✅' : '❌';
            toast.innerHTML = `<strong>${icon}</strong> ${message}`;
            
            document.body.appendChild(toast);
            
            // Supprimer au clic
            toast.addEventListener('click', () => {
                toast.style.animation = 'slideOutRight 0.4s ease-out';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 400);
            });
            
            // Supprimer automatiquement après 6 secondes
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.animation = 'slideOutRight 0.4s ease-out';
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 400);
                }
            }, 6000);
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
         * Validation en temps réel des champs ferronnerie
         */
        function initValidationTempsReel() {
            const dimensionsField = document.getElementById('dimensions');
            const quantiteField = document.getElementById('quantite');
            const prixField = document.getElementById('prix_unitaire');
            const typeField = document.getElementById('type_ouvrage');
            const materiauxField = document.getElementById('materiaux');
            
            // Validation dimensions
            dimensionsField?.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && !value.match(/^\d+(\.\d+)?x\d+(\.\d+)?(x\d+(\.\d+)?)?(cm|mm|m)?$/i)) {
                    this.style.borderColor = '#e74c3c';
                    this.title = 'Format invalide. Exemples: 300x200cm, 4x2m, 40x40x3mm';
                } else {
                    this.style.borderColor = '';
                    this.title = '';
                }
            });
            
            // Calcul automatique du total
            function calculerTotal() {
                const quantite = parseFloat(quantiteField?.value) || 0;
                const prix = parseFloat(prixField?.value) || 0;
                const total = quantite * prix;
                
                if (total > 0) {
                    let totalElement = document.getElementById('total-preview');
                    if (!totalElement) {
                        const preview = document.createElement('div');
                        preview.id = 'total-preview';
                        preview.style.cssText = `
                            margin-top: 0.5rem;
                            padding: 0.5rem;
                            background: #7f8c8d;
                            color: white;
                            border-radius: 4px;
                            font-weight: 600;
                            text-align: center;
                        `;
                        prixField?.parentNode.appendChild(preview);
                        totalElement = preview;
                    }
                    totalElement.innerHTML = `
                        <i class="fas fa-calculator"></i> Total: ${total.toLocaleString()} FCFA
                    `;
                }
            }
            
            quantiteField?.addEventListener('input', calculerTotal);
            prixField?.addEventListener('input', calculerTotal);
            
            // Auto-estimation prix selon type et matériau
            function autoEstimation() {
                const designation = document.getElementById('designation').value;
                const type = typeField?.value;
                const materiau = materiauxField?.value;
                
                if (designation && type && materiau && !prixField?.value) {
                    const estimation = estimerPrixFerronnerie(designation);
                    if (estimation > 0) {
                        prixField.value = estimation;
                        calculerTotal();
                        showToast(`💰 Prix auto-estimé: ${estimation.toLocaleString()} FCFA`, 'info');
                    }
                }
            }
            
            typeField?.addEventListener('change', autoEstimation);
            materiauxField?.addEventListener('change', autoEstimation);
        }

        /**
         * Suggestions automatiques selon le type d'ouvrage
         */
        function suggerDimensionsType() {
            const type = document.getElementById('type_ouvrage').value;
            const dimensionsField = document.getElementById('dimensions');
            const quantiteField = document.getElementById('quantite');
            const uniteField = document.getElementById('unite');
            
            if (type && !dimensionsField.value) {
                let dimensionsSuggerees = '';
                let quantiteSuggeree = '';
                let uniteSuggeree = '';
                
                switch(type) {
                    case 'Portail':
                        dimensionsSuggerees = '300x200cm';
                        quantiteSuggeree = '1';
                        uniteSuggeree = 'unité';
                        break;
                    case 'Portillon':
                        dimensionsSuggerees = '100x200cm';
                        quantiteSuggeree = '1';
                        uniteSuggeree = 'unité';
                        break;
                    case 'Grille':
                        dimensionsSuggerees = '120x100cm';
                        quantiteSuggeree = '3';
                        uniteSuggeree = 'm²';
                        break;
                    case 'Garde-corps':
                        dimensionsSuggerees = '110cm hauteur';
                        quantiteSuggeree = '10';
                        uniteSuggeree = 'ml';
                        break;
                    case 'Escalier':
                        dimensionsSuggerees = '280cm longueur';
                        quantiteSuggeree = '10';
                        uniteSuggeree = 'marches';
                        break;
                    case 'Auvent':
                        dimensionsSuggerees = '200x120cm';
                        quantiteSuggeree = '2.4';
                        uniteSuggeree = 'm²';
                        break;
                }
                
                if (dimensionsSuggerees) {
                    dimensionsField.value = dimensionsSuggerees;
                    quantiteField.value = quantiteSuggeree;
                    uniteField.value = uniteSuggeree;
                    showToast(`💡 Suggestions pour ${type}:\n` +
                             `Dimensions: ${dimensionsSuggerees}\n` +
                             `Quantité: ${quantiteSuggeree} ${uniteSuggeree}`, 'info');
                }
            }
        }

        // ===== INITIALISATION AU CHARGEMENT =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔧 Module Ferronnerie GSN ProDevis360° initialisé');
            
            // Initialiser toutes les fonctionnalités
            initRaccourcisClavier();
            initAnimationsScroll();
            initValidationTempsReel();
            
            // Ajouter les événements pour les suggestions automatiques
            document.getElementById('type_ouvrage')?.addEventListener('change', suggerDimensionsType);
            
            // Afficher les raccourcis clavier
            showToast(`⌨️ Raccourcis Ferronnerie:\n` +
                     `Alt+D = Désignation\n` +
                     `Alt+S = Surface\n` +
                     `Alt+P = Poids acier\n` +
                     `Alt+L = Débit linéaire\n` +
                     `Alt+W = Poids pièce\n` +
                     `Alt+E = Estimation prix\n` +
                     `Alt+T = Type ouvrage\n` +
                     `Alt+M = Matériaux\n` +
                     `Alt+F = Finition\n` +
                     `Ctrl+Entrée = Envoyer`, 'info');
            
            // Focus automatique sur le premier champ
            const firstField = document.getElementById('designation');
            if (firstField && !firstField.value) {
                setTimeout(() => firstField.focus(), 500);
            }
            
            // Calculateur intégré dans la page
            const calcSection = document.createElement('div');
            calcSection.innerHTML = `
                <div style="display: none;" id="calculateur-avance">
                    <h5><i class="fas fa-calculator"></i> Calculateur Ferronnerie Avancé</h5>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.5rem; margin: 1rem 0;">
                        <select id="calc_type" class="form-control">
                            <option value="">Type calcul</option>
                            <option value="portail">Portail</option>
                            <option value="grille">Grille</option>
                            <option value="structure">Structure</option>
                            <option value="tole">Tôle</option>
                        </select>
                        <input type="text" id="calc_profil" placeholder="Profil (ex: IPN200)" class="form-control">
                        <button type="button" class="btn btn-info btn-sm" onclick="calculerAvance()">
                            <i class="fas fa-cogs"></i> Calculer
                        </button>
                    </div>
                </div>
            `;
            
            // Ajouter le calculateur après le formulaire
            const formSection = document.querySelector('.form-section');
            if (formSection) {
                formSection.appendChild(calcSection);
                
                // Bouton pour afficher/masquer le calculateur
                const toggleCalc = document.createElement('button');
                toggleCalc.type = 'button';
                toggleCalc.className = 'btn btn-outline-info btn-sm mb-3';
                toggleCalc.innerHTML = '<i class="fas fa-calculator"></i> Calculateur Avancé';
                toggleCalc.onclick = function() {
                    const calc = document.getElementById('calculateur-avance');
                    calc.style.display = calc.style.display === 'none' ? 'block' : 'none';
                };
                
                calcSection.insertBefore(toggleCalc, calcSection.firstChild);
            }
            
            // Fonction calculateur avancé
            window.calculerAvance = function() {
                const type = document.getElementById('calc_type')?.value;
                const profil = document.getElementById('calc_profil')?.value;
                
                if (type === 'portail') {
                    showToast('📋 Calcul portail:\n- Cadre périmétrique\n- Traverses diagonales\n- Remplissage barreaudage', 'info');
                } else if (type === 'structure' && profil) {
                    const profilKey = profil.toLowerCase();
                    const poids = PROFILES_ACIER[profilKey];
                    if (poids) {
                        showToast(`🏗️ Profil ${profil.toUpperCase()}:\nPoids: ${poids} kg/ml\nUtilisation: structure porteuse`, 'info');
                    } else {
                        showToast(`❓ Profil ${profil.toUpperCase()} non trouvé dans la base`, 'warning');
                    }
                }
            };
            
            // Vérification cohérence des données existantes
            const elements = <?= json_encode($elements_ferronnerie ?? []) ?>;
            let alertesPrix = 0;
            
            elements.forEach(element => {
                const prixUnitaire = parseFloat(element.prix_unitaire);
                const type = element.type_ouvrage;
                
                // Alertes de cohérence prix/type
                if (type === 'Portail' && prixUnitaire < 200000) alertesPrix++;
                if (type === 'Structure' && prixUnitaire < 10000) alertesPrix++;
                if (type === 'Grille' && prixUnitaire > 100000) alertesPrix++;
            });
            
            if (alertesPrix > 0) {
                setTimeout(() => {
                    showToast(`⚠️ ${alertesPrix} élément(s) avec prix possiblement incohérent.\n` +
                             `Vérifiez la cohérence prix/type d'ouvrage.`, 'warning');
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
            
            .toast-notification:hover {
                transform: scale(1.02);
                transition: transform 0.2s ease;
            }
            
            #calculateur-avance {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                padding: 1rem;
                border-radius: 8px;
                border: 2px solid #7f8c8d;
                margin-top: 1rem;
            }
            
            #calculateur-avance h5 {
                color: #7f8c8d;
                margin-bottom: 0.75rem;
            }
        `;
        document.head.appendChild(styleSheet);
    </script>

</body>
</html>