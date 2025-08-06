<?php
// ===== CHARPENTERIE.PHP - PARTIE 1 : PHP LOGIC & CONFIG =====
// VERSION UNIFORMISÉE GSN ProDevis360°
require_once 'functions.php';

// Configuration du module actuel
$current_module = 'charpenterie';

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

// Suggestions spécialisées pour la charpenterie
$suggestions_charpenterie = [
    // CHARPENTE TRADITIONNELLE
    'Poutre chêne massif 20x30cm long 4m',
    'Poutre chêne massif 25x35cm long 5m',
    'Poutre chêne massif 30x40cm long 6m',
    'Sablière chêne 15x20cm long 4m',
    'Panne faîtière chêne 20x25cm long 8m',
    'Panne intermédiaire chêne 15x20cm long 6m',
    'Chevron chêne 8x10cm long 4m',
    'Arbalétrier chêne 20x25cm long 5m',
    'Entrait chêne 25x30cm long 8m',
    'Poinçon chêne 20x20cm long 3m',
    'Contrefiche chêne 15x15cm long 2.5m',
    'Jambe de force chêne 15x20cm long 3m',
    
    // CHARPENTE INDUSTRIELLE SAPIN
    'Chevron sapin C24 63x175mm long 4m',
    'Chevron sapin C24 63x200mm long 4.5m',
    'Chevron sapin C24 75x200mm long 5m',
    'Chevron sapin C24 75x225mm long 6m',
    'Madrier sapin C24 63x225mm long 4m',
    'Madrier sapin C24 75x225mm long 5m',
    'Madrier sapin C24 100x225mm long 6m',
    'Panne sapin lamellé-collé 120x240mm long 8m',
    'Panne sapin lamellé-collé 140x280mm long 10m',
    'Poutre sapin lamellé-collé 160x320mm long 12m',
    'Poteau sapin lamellé-collé 120x120mm long 3m',
    'Poteau sapin lamellé-collé 140x140mm long 4m',
    
    // FERMETTES INDUSTRIELLES
    'Fermette industrielle W traditionnelle portée 8m',
    'Fermette industrielle W traditionnelle portée 10m',
    'Fermette industrielle W traditionnelle portée 12m',
    'Fermette industrielle W combles aménageables 8m',
    'Fermette industrielle W combles aménageables 10m',
    'Fermette monopente industrielle portée 6m',
    'Fermette monopente industrielle portée 8m',
    'Anti-flambage fermettes 38x63mm long 2.4m',
    'Contreventement fermettes 38x100mm',
    'Entrait retroussé fermette 38x150mm',
    
    // PANNES ET CHEVRONS
    'Panne sablière sapin 75x150mm long 4m',
    'Panne intermédiaire sapin 63x175mm long 6m',
    'Panne faîtière sapin 75x200mm long 8m',
    'Chevron sapin brut 63x75mm long 3m',
    'Chevron sapin raboté 63x100mm long 4m',
    'Chevron sapin traité CL2 63x125mm long 5m',
    'Volige sapin 18x100mm long 4m',
    'Volige sapin 22x150mm long 4m',
    'Liteaux sapin 25x40mm long 4m',
    'Contre-liteaux sapin 32x50mm long 4m',
    
    // OSSATURE BOIS
    'Montant ossature bois 45x120mm long 2.7m',
    'Montant ossature bois 45x145mm long 2.7m',
    'Montant ossature bois 45x170mm long 2.7m',
    'Lisse basse ossature 45x120mm long 4m',
    'Lisse haute ossature 45x120mm long 4m',
    'Sablière ossature 45x145mm long 6m',
    'Solive plancher 50x200mm long 4m',
    'Solive plancher 63x220mm long 5m',
    'Poutrelle I-Joist h=240mm long 6m',
    'Poutrelle I-Joist h=300mm long 8m',
    
    // PANNEAUX STRUCTURELS
    'Panneau OSB3 structural 12mm 250x125cm',
    'Panneau OSB3 structural 15mm 250x125cm',
    'Panneau OSB3 structural 18mm 250x125cm',
    'Panneau OSB3 structural 22mm 250x125cm',
    'Panneau contreplaqué CTB-X 15mm',
    'Panneau contreplaqué CTB-X 18mm',
    'Panneau contreplaqué CTB-X 22mm',
    'Aggloméré P5 hydrofuge 19mm 280x207cm',
    'Aggloméré P5 hydrofuge 22mm 280x207cm',
    
    // ASSEMBLAGES ET CONNECTEURS
    'Connecteur métallique poutre-poteau Simpson',
    'Sabot de chevron galvanisé 63x175mm',
    'Sabot de chevron galvanisé 75x200mm',
    'Étrier de solive galvanisé 50x200mm',
    'Étrier de solive galvanisé 63x220mm',
    'Équerre de charpente 90x90x65mm',
    'Équerre de charpente 105x105x90mm',
    'Platine d\'ancrage 120x120mm épaisseur 8mm',
    'Goujon d\'ancrage chimique M12x160mm',
    'Goujon d\'ancrage chimique M16x200mm',
    'Tige filetée galvanisée M12 longueur 1m',
    'Tige filetée galvanisée M16 longueur 1m',
    
    // ISOLATION SOUS TOITURE
    'Pare-vapeur 200µ rouleau 50m²',
    'Écran sous-toiture HPV 135g/m² rouleau 75m²',
    'Membrane EPDM étanchéité toiture terrasse',
    'Laine de verre sous chevrons 100mm R=2.5',
    'Laine de verre sous chevrons 120mm R=3.0',
    'Laine de verre sous chevrons 140mm R=3.5',
    'Laine de roche sous chevrons 120mm R=3.0',
    'Ouate de cellulose en vrac m³',
    'Fibre de bois rigide sous chevrons 120mm',
    
    // COUVERTURE SUPPORTS
    'Plaque fibres-ciment ondulée 920x2000mm',
    'Plaque fibres-ciment plane 1200x2500mm',
    'Bac acier nervuré galvanisé 1000mm',
    'Bac acier nervuré prélaqué 1000mm',
    'Plaque polycarbonate alvéolaire 1000x2000mm',
    'Profile finition faîtage zinc naturel',
    'Profile finition égout zinc naturel',
    'Solin zinc naturel développé 250mm',
    'Noue zinc naturel développé 400mm',
    
    // TRAITEMENT ET FINITION
    'Traitement préventif bois CL2 pulvérisation',
    'Traitement curatif bois contre insectes',
    'Produit de traitement anti-bleuissement',
    'Lasure de protection bois extérieur incolore',
    'Lasure de protection bois extérieur teintée',
    'Vernis parquet trafic intense brillant',
    'Huile de protection bois naturelle',
    'Cire d\'abeille protection bois intérieur'
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
            $unite = trim($_POST['unite'] ?? 'ml');
            $prix_unitaire = floatval($_POST['prix_unitaire'] ?? 0);
            $essence_bois = trim($_POST['essence_bois'] ?? '');
            $section = trim($_POST['section'] ?? '');
            $longueur = floatval($_POST['longueur'] ?? 0);
            $classe_resistance = trim($_POST['classe_resistance'] ?? '');
            $traitement = trim($_POST['traitement'] ?? '');
            $usage = trim($_POST['usage'] ?? '');
            
            // Validations spécifiques charpenterie
            if (empty($designation)) {
                throw new Exception("La désignation est obligatoire.");
            }
            if ($quantite <= 0) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }
            if ($prix_unitaire < 0) {
                throw new Exception("Le prix unitaire ne peut pas être négatif.");
            }
            
            // Validation section si fournie (format : LxHmm ou LxHcm)
            if (!empty($section) && !preg_match('/^\d+x\d+(mm|cm)?$/i', $section)) {
                throw new Exception("Format de section invalide (ex: 63x175mm, 20x30cm).");
            }
            
            // Validation longueur
            if ($longueur < 0 || $longueur > 20) {
                throw new Exception("La longueur doit être entre 0 et 20 mètres.");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Insertion en base
            $stmt = $conn->prepare("
                INSERT INTO charpenterie (
                    projet_id, devis_id, designation, quantite, unite, 
                    prix_unitaire, total, essence_bois, section, 
                    longueur, classe_resistance, traitement, usage, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param(
                "iisdsdsssdsss", 
                $projet_id, $devis_id, $designation, $quantite, $unite,
                $prix_unitaire, $total, $essence_bois, $section,
                $longueur, $classe_resistance, $traitement, $usage
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'charpenterie');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'charpenterie', 'Ajout', "Élément ajouté : {$designation}");
                
                $message = "Élément charpenterie ajouté avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de l'ajout : " . $conn->error);
            }
            
        } elseif ($action == 'modifier' && $element_id > 0) {
            // Récupération et validation des données
            $designation = trim($_POST['designation'] ?? '');
            $quantite = floatval($_POST['quantite'] ?? 0);
            $unite = trim($_POST['unite'] ?? 'ml');
            $prix_unitaire = floatval($_POST['prix_unitaire'] ?? 0);
            $essence_bois = trim($_POST['essence_bois'] ?? '');
            $section = trim($_POST['section'] ?? '');
            $longueur = floatval($_POST['longueur'] ?? 0);
            $classe_resistance = trim($_POST['classe_resistance'] ?? '');
            $traitement = trim($_POST['traitement'] ?? '');
            $usage = trim($_POST['usage'] ?? '');
            
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
            
            if (!empty($section) && !preg_match('/^\d+x\d+(mm|cm)?$/i', $section)) {
                throw new Exception("Format de section invalide (ex: 63x175mm, 20x30cm).");
            }
            
            if ($longueur < 0 || $longueur > 20) {
                throw new Exception("La longueur doit être entre 0 et 20 mètres.");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Mise à jour en base
            $stmt = $conn->prepare("
                UPDATE charpenterie SET 
                    designation = ?, quantite = ?, unite = ?, prix_unitaire = ?, 
                    total = ?, essence_bois = ?, section = ?, longueur = ?, 
                    classe_resistance = ?, traitement = ?, usage = ?, date_modification = NOW()
                WHERE id = ? AND projet_id = ? AND devis_id = ?
            ");
            
            $stmt->bind_param(
                "sdsdsssdsssiii",
                $designation, $quantite, $unite, $prix_unitaire, $total,
                $essence_bois, $section, $longueur, $classe_resistance, 
                $traitement, $usage, $element_id, $projet_id, $devis_id
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'charpenterie');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'charpenterie', 'Modification', "Élément modifié : {$designation}");
                
                $message = "Élément charpenterie modifié avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de la modification : " . $conn->error);
            }
            
        } elseif ($action == 'supprimer' && $element_id > 0) {
            // Récupération de la désignation avant suppression
            $stmt_get = $conn->prepare("SELECT designation FROM charpenterie WHERE id = ? AND projet_id = ? AND devis_id = ?");
            $stmt_get->bind_param("iii", $element_id, $projet_id, $devis_id);
            $stmt_get->execute();
            $result_get = $stmt_get->get_result();
            $element_data = $result_get->fetch_assoc();
            
            if ($element_data) {
                // Suppression de l'élément
                $stmt = $conn->prepare("DELETE FROM charpenterie WHERE id = ? AND projet_id = ? AND devis_id = ?");
                $stmt->bind_param("iii", $element_id, $projet_id, $devis_id);
                
                if ($stmt->execute()) {
                    // Mise à jour du récapitulatif
                    updateRecapitulatif($projet_id, $devis_id, 'charpenterie');
                    
                    // Sauvegarde dans l'historique
                    sauvegarderHistorique($projet_id, $devis_id, 'charpenterie', 'Suppression', "Élément supprimé : {$element_data['designation']}");
                    
                    $message = "Élément charpenterie supprimé avec succès !";
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

// Récupération des éléments de charpenterie pour affichage
$elements_charpenterie = [];
$total_module = 0;

$stmt = $conn->prepare("
    SELECT id, designation, quantite, unite, prix_unitaire, total,
           essence_bois, section, longueur, classe_resistance, traitement, usage,
           DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation_fr,
           DATE_FORMAT(date_modification, '%d/%m/%Y %H:%i') as date_modification_fr
    FROM charpenterie 
    WHERE projet_id = ? AND devis_id = ? 
    ORDER BY date_creation DESC
");

$stmt->bind_param("ii", $projet_id, $devis_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $elements_charpenterie[] = $row;
    $total_module += $row['total'];
}

// Récupération de l'élément à modifier si nécessaire
$element_a_modifier = null;
if ($action == 'modifier' && $element_id > 0) {
    $stmt = $conn->prepare("
        SELECT * FROM charpenterie 
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
    <title>Charpenterie - <?= htmlspecialchars($projet_devis_info['nom_projet']) ?> | GSN ProDevis360°</title>
    
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
            
            /* Variables spécifiques charpenterie */
            --wood-primary: #27ae60;
            --wood-light: #58d68d;
            --wood-dark: #1e8449;
            --oak-brown: #8b4513;
            --pine-beige: #daa520;
            --cedar-red: #d2691e;
            --maple-gold: #ffd700;
            --mahogany-deep: #c04000;
            --birch-light: #f5deb3;
            --walnut-dark: #654321;
            --forest-green: #228b22;
            --bark-gray: #696969;
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
            background: var(--wood-primary);
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

        /* ===== SUGGESTIONS CHARPENTERIE ===== */
        .suggestions-charpenterie {
            background: linear-gradient(135deg, var(--wood-primary) 0%, var(--wood-dark) 100%);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .suggestions-charpenterie h4 {
            color: var(--secondary-white);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
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

        /* ===== CALCULATEUR CHARPENTE ===== */
        .calculator-section {
            background: linear-gradient(135deg, var(--oak-brown) 0%, var(--cedar-red) 100%);
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
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
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

        /* ===== BADGES SPÉCIALISÉS CHARPENTERIE ===== */
        .badge-essence {
            background: var(--oak-brown);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-section {
            background: var(--pine-beige);
            color: var(--neutral-dark);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-resistance {
            background: var(--forest-green);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-traitement {
            background: var(--cedar-red);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-usage {
            background: var(--bark-gray);
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
            color: var(--wood-light);
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

        @keyframes woodGrain {
            0% { transform: translateX(-100%) skewX(-15deg); opacity: 0; }
            50% { transform: translateX(0%) skewX(0deg); opacity: 1; }
            100% { transform: translateX(0%) skewX(0deg); opacity: 1; }
        }

        .wood-grain {
            animation: woodGrain 0.8s ease-out;
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
                        <i class="fas fa-tree"></i>
                        Module Charpenterie
                        <span class="module-badge">
                            <i class="fas fa-hammer"></i>
                            Structure Bois
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

        <!-- ===== FORMULAIRE CHARPENTERIE ===== -->
        <div class="form-section fade-in-up">
            <h2>
                <i class="fas fa-<?= $element_a_modifier ? 'edit' : 'plus-circle' ?>"></i>
                <?= $element_a_modifier ? 'Modifier l\'élément charpenterie' : 'Ajouter un élément charpenterie' ?>
            </h2>

            <!-- Suggestions Charpenterie -->
            <div class="suggestions-charpenterie">
                <h4>
                    <i class="fas fa-tree"></i>
                    Suggestions Charpenterie & Structure Bois
                    <small>(Cliquez pour remplir automatiquement)</small>
                </h4>
                <div class="suggestions-grid">
                    <?php foreach ($suggestions_charpenterie as $suggestion): ?>
                        <div class="suggestion-item" onclick="remplirSuggestion('<?= htmlspecialchars($suggestion, ENT_QUOTES) ?>')">
                            <?= htmlspecialchars($suggestion) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Calculateur Charpente -->
            <div class="calculator-section">
                <h4>
                    <i class="fas fa-ruler-combined"></i>
                    Calculateur Charpente & Dimensionnement
                </h4>
                <div class="calc-grid">
                    <input type="number" id="calc_portee" placeholder="Portée (m)" class="calc-input" step="0.1" max="20">
                    <input type="number" id="calc_entraxe" placeholder="Entraxe (cm)" class="calc-input" step="10" max="100">
                    <input type="number" id="calc_charge" placeholder="Charge (kg/m²)" class="calc-input" step="50" max="1000">
                    <button type="button" class="btn btn-sm btn-info" onclick="calculerSection()">
                        <i class="fas fa-calculator"></i> Section mini
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="calculerVolumeBois()">
                        <i class="fas fa-cube"></i> Volume bois
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="calculerNombrePieces()">
                        <i class="fas fa-sort-numeric-up"></i> Nb pièces
                    </button>
                </div>
            </div>

            <form method="POST" action="" id="formCharpenterie">
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
                               placeholder="Ex: Chevron sapin C24 63x175mm long 4m"
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
                               placeholder="Ex: 12"
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
                            <option value="ml" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'ml') ? 'selected' : '' ?>>Mètre linéaire (ml)</option>
                            <option value="unité" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'unité') ? 'selected' : '' ?>>Unité</option>
                            <option value="m²" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm²') ? 'selected' : '' ?>>Mètre carré (m²)</option>
                            <option value="m³" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm³') ? 'selected' : '' ?>>Mètre cube (m³)</option>
                            <option value="kg" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'kg') ? 'selected' : '' ?>>Kilogramme (kg)</option>
                            <option value="palette" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'palette') ? 'selected' : '' ?>>Palette</option>
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
                               placeholder="Ex: 8500"
                               step="0.01"
                               min="0"
                               required>
                    </div>
                </div>

                <!-- Ligne 2 : Spécifications bois -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="essence_bois">
                            <i class="fas fa-seedling"></i>
                            Essence de bois
                        </label>
                        <select id="essence_bois" name="essence_bois">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Chêne" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Chêne') ? 'selected' : '' ?>>Chêne (traditionnel)</option>
                            <option value="Sapin" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Sapin') ? 'selected' : '' ?>>Sapin (résineux)</option>
                            <option value="Épicéa" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Épicéa') ? 'selected' : '' ?>>Épicéa (résineux)</option>
                            <option value="Pin" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Pin') ? 'selected' : '' ?>>Pin (résineux)</option>
                            <option value="Douglas" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Douglas') ? 'selected' : '' ?>>Douglas (résineux)</option>
                            <option value="Mélèze" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Mélèze') ? 'selected' : '' ?>>Mélèze (résineux)</option>
                            <option value="Hêtre" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Hêtre') ? 'selected' : '' ?>>Hêtre (feuillu)</option>
                            <option value="Frêne" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Frêne') ? 'selected' : '' ?>>Frêne (feuillu)</option>
                            <option value="Châtaignier" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Châtaignier') ? 'selected' : '' ?>>Châtaignier (feuillu)</option>
                            <option value="Lamellé-collé" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Lamellé-collé') ? 'selected' : '' ?>>Lamellé-collé (industriel)</option>
                            <option value="OSB" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'OSB') ? 'selected' : '' ?>>OSB (panneau)</option>
                            <option value="Contreplaqué" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Contreplaqué') ? 'selected' : '' ?>>Contreplaqué (panneau)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="section">
                            <i class="fas fa-expand-arrows-alt"></i>
                            Section
                        </label>
                        <input type="text" 
                               id="section" 
                               name="section" 
                               value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['section']) : '' ?>"
                               placeholder="Ex: 63x175mm, 20x30cm"
                               pattern="^[0-9]+x[0-9]+(mm|cm)?$"
                               title="Format: 63x175mm ou 20x30cm">
                    </div>

                    <div class="form-group">
                        <label for="longueur">
                            <i class="fas fa-arrows-alt-h"></i>
                            Longueur (m)
                        </label>
                        <input type="number" 
                               id="longueur" 
                               name="longueur" 
                               value="<?= $element_a_modifier ? $element_a_modifier['longueur'] : '' ?>"
                               placeholder="Ex: 4.0"
                               step="0.1"
                               min="0"
                               max="20">
                    </div>

                    <div class="form-group">
                        <label for="classe_resistance">
                            <i class="fas fa-shield-alt"></i>
                            Classe résistance
                        </label>
                        <select id="classe_resistance" name="classe_resistance">
                            <option value="">-- Sélectionnez --</option>
                            <option value="C14" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'C14') ? 'selected' : '' ?>>C14 (14 MPa)</option>
                            <option value="C16" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'C16') ? 'selected' : '' ?>>C16 (16 MPa)</option>
                            <option value="C18" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'C18') ? 'selected' : '' ?>>C18 (18 MPa)</option>
                            <option value="C20" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'C20') ? 'selected' : '' ?>>C20 (20 MPa)</option>
                            <option value="C22" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'C22') ? 'selected' : '' ?>>C22 (22 MPa)</option>
                            <option value="C24" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'C24') ? 'selected' : '' ?>>C24 (24 MPa)</option>
                            <option value="C27" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'C27') ? 'selected' : '' ?>>C27 (27 MPa)</option>
                            <option value="C30" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'C30') ? 'selected' : '' ?>>C30 (30 MPa)</option>
                            <option value="GL24h" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'GL24h') ? 'selected' : '' ?>>GL24h (lamellé-collé)</option>
                            <option value="GL28h" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'GL28h') ? 'selected' : '' ?>>GL28h (lamellé-collé)</option>
                            <option value="GL32h" <?= ($element_a_modifier && $element_a_modifier['classe_resistance'] === 'GL32h') ? 'selected' : '' ?>>GL32h (lamellé-collé)</option>
                        </select>
                    </div>
                </div>

                <!-- Ligne 3 : Traitement et usage -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="traitement">
                            <i class="fas fa-spray-can"></i>
                            Traitement
                        </label>
                        <select id="traitement" name="traitement">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Brut" <?= ($element_a_modifier && $element_a_modifier['traitement'] === 'Brut') ? 'selected' : '' ?>>Brut (sans traitement)</option>
                            <option value="Raboté" <?= ($element_a_modifier && $element_a_modifier['traitement'] === 'Raboté') ? 'selected' : '' ?>>Raboté</option>
                            <option value="CL1" <?= ($element_a_modifier && $element_a_modifier['traitement'] === 'CL1') ? 'selected' : '' ?>>CL1 (anti-bleuissement)</option>
                            <option value="CL2" <?= ($element_a_modifier && $element_a_modifier['traitement'] === 'CL2') ? 'selected' : '' ?>>CL2 (anti-insectes/champignons)</option>
                            <option value="CL3" <?= ($element_a_modifier && $element_a_modifier['traitement'] === 'CL3') ? 'selected' : '' ?>>CL3 (humidité fréquente)</option>
                            <option value="CL4" <?= ($element_a_modifier && $element_a_modifier['traitement'] === 'CL4') ? 'selected' : '' ?>>CL4 (contact permanent eau)</option>
                            <option value="Autoclave" <?= ($element_a_modifier && $element_a_modifier['traitement'] === 'Autoclave') ? 'selected' : '' ?>>Autoclave (imprégnation)</option>
                            <option value="Thermique" <?= ($element_a_modifier && $element_a_modifier['traitement'] === 'Thermique') ? 'selected' : '' ?>>Traitement thermique</option>
                            <option value="Ignifugé" <?= ($element_a_modifier && $element_a_modifier['traitement'] === 'Ignifugé') ? 'selected' : '' ?>>Ignifugé (anti-feu)</option>
                            <option value="Hydrofuge" <?= ($element_a_modifier && $element_a_modifier['traitement'] === 'Hydrofuge') ? 'selected' : '' ?>>Hydrofuge</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="usage">
                            <i class="fas fa-tools"></i>
                            Usage
                        </label>
                        <select id="usage" name="usage">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Charpente" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Charpente') ? 'selected' : '' ?>>Charpente traditionnelle</option>
                            <option value="Fermette" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Fermette') ? 'selected' : '' ?>>Fermette industrielle</option>
                            <option value="Ossature" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Ossature') ? 'selected' : '' ?>>Ossature bois</option>
                            <option value="Plancher" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Plancher') ? 'selected' : '' ?>>Plancher/Solives</option>
                            <option value="Couverture" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Couverture') ? 'selected' : '' ?>>Support couverture</option>
                            <option value="Cloison" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Cloison') ? 'selected' : '' ?>>Cloison/Doublage</option>
                            <option value="Coffrage" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Coffrage') ? 'selected' : '' ?>>Coffrage béton</option>
                            <option value="Finition" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Finition') ? 'selected' : '' ?>>Finition/Habillage</option>
                            <option value="Structure" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Structure') ? 'selected' : '' ?>>Structure porteuse</option>
                            <option value="Assemblage" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Assemblage') ? 'selected' : '' ?>>Connecteurs/Assemblage</option>
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
                                    onclick="calculerVolumePiece()"
                                    title="Alt+V">
                                <i class="fas fa-cube"></i>
                                Volume pièce
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

                    <div class="form-group">
                        <label>
                            <i class="fas fa-info-circle"></i>
                            Informations essence
                        </label>
                        <div id="info-essence" style="font-size: 0.85rem; color: var(--neutral-gray); line-height: 1.4;">
                            <i class="fas fa-seedling"></i> Sélectionnez une essence pour voir les caractéristiques techniques
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
                            <a href="charpenterie.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-secondary ml-2">
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

        <!-- ===== TABLEAU DES ÉLÉMENTS CHARPENTERIE ===== -->
        <div class="table-container fade-in-up">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list"></i>
                    Éléments charpenterie
                    <span class="badge-essence ml-2"><?= count($elements_charpenterie) ?> élément(s)</span>
                </h3>
                <div class="table-actions">
                    <span class="total-amount">
                        <i class="fas fa-tree"></i>
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
                            <th><i class="fas fa-seedling"></i> Essence</th>
                            <th><i class="fas fa-expand-arrows-alt"></i> Section</th>
                            <th><i class="fas fa-arrows-alt-h"></i> Longueur</th>
                            <th><i class="fas fa-shield-alt"></i> Résistance</th>
                            <th><i class="fas fa-spray-can"></i> Traitement</th>
                            <th><i class="fas fa-tools"></i> Usage</th>
                            <th><i class="fas fa-euro-sign"></i> Total</th>
                            <th><i class="fas fa-calendar"></i> Créé le</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($elements_charpenterie)): ?>
                            <tr>
                                <td colspan="14" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-tree fa-3x mb-3 d-block"></i>
                                        <p>Aucun élément charpenterie ajouté pour ce devis.</p>
                                        <small>Utilisez le formulaire ci-dessus pour ajouter des éléments bois.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($elements_charpenterie as $element): ?>
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
                                        <?php if (!empty($element['essence_bois'])): ?>
                                            <span class="badge-essence">
                                                <i class="fas fa-seedling"></i>
                                                <?= htmlspecialchars($element['essence_bois']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['section'])): ?>
                                            <span class="badge-section">
                                                <i class="fas fa-expand-arrows-alt"></i>
                                                <?= htmlspecialchars($element['section']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['longueur']) && $element['longueur'] > 0): ?>
                                            <span class="badge-section">
                                                <i class="fas fa-arrows-alt-h"></i>
                                                <?= number_format($element['longueur'], 1) ?>m
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['classe_resistance'])): ?>
                                            <span class="badge-resistance">
                                                <i class="fas fa-shield-alt"></i>
                                                <?= htmlspecialchars($element['classe_resistance']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['traitement'])): ?>
                                            <span class="badge-traitement">
                                                <i class="fas fa-spray-can"></i>
                                                <?= htmlspecialchars($element['traitement']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['usage'])): ?>
                                            <span class="badge-usage">
                                                <i class="fas fa-tools"></i>
                                                <?= htmlspecialchars($element['usage']) ?>
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
                                            <a href="charpenterie.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&action=modifier&element_id=<?= $element['id'] ?>" 
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
                        <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=charpenterie" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-clock"></i> Voir tout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TOTAUX MODULE CHARPENTERIE ===== -->
        <div class="module-summary fade-in-up">
            <h3>
                <i class="fas fa-tree"></i>
                Total Module Charpenterie
            </h3>
            <div class="total-amount pulse-animation">
                <?= number_format($total_module, 0, ',', ' ') ?> FCFA
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Mis à jour automatiquement • <?= count($elements_charpenterie) ?> élément(s)
                <?php if ($total_module > 0 && count($elements_charpenterie) > 0): ?>
                    • Moyenne: <?= number_format($total_module / count($elements_charpenterie), 0, ',', ' ') ?> FCFA/élément
                <?php endif; ?>
            </small>
        </div>

    </div>

    <!-- ===== JAVASCRIPT SPÉCIALISÉ CHARPENTERIE ===== -->
    <script>
        // ===== CONFIGURATION ET VARIABLES =====
        const PRIX_CHARPENTERIE = {
            // Essences de bois (prix par m³ ou ml)
            'chene': { base: 1200000, factor: 2.5 }, // Chêne premium
            'sapin': { base: 450000, factor: 1.0 },   // Sapin standard
            'epicea': { base: 480000, factor: 1.1 },  // Épicéa
            'pin': { base: 420000, factor: 0.9 },     // Pin
            'douglas': { base: 550000, factor: 1.2 }, // Douglas
            'meleze': { base: 580000, factor: 1.3 },  // Mélèze
            'lamelle_colle': { base: 750000, factor: 1.7 }, // Lamellé-collé
            
            // Types d'éléments (prix par ml)
            'chevron': { base: 8500, factor: 1.0 },
            'poutre': { base: 25000, factor: 2.0 },
            'madrier': { base: 15000, factor: 1.5 },
            'panne': { base: 18000, factor: 1.6 },
            'fermette': { base: 35000, factor: 2.5 },
            'solive': { base: 12000, factor: 1.2 },
            
            // Panneaux (prix par m²)
            'osb': { base: 18000, factor: 1.0 },
            'contreplaque': { base: 25000, factor: 1.4 }
        };

        const SECTIONS_NORMALISEES = {
            // Sections courantes en mm
            'chevrons': ['63x75', '63x100', '63x125', '63x150', '63x175', '75x100', '75x125', '75x150', '75x175', '75x200', '75x225'],
            'madriers': ['63x175', '63x200', '63x225', '75x200', '75x225', '100x200', '100x225', '100x250'],
            'poutres': ['100x200', '120x240', '140x280', '160x320', '180x360', '200x400'],
            'pannes': ['63x175', '75x200', '100x225', '120x240', '140x280'],
            'solives': ['50x150', '50x175', '50x200', '63x175', '63x200', '75x200', '75x225']
        };

        const INFO_ESSENCES = {
            'Chêne': {
                description: "Bois dur noble, très résistant et durable",
                densite: "0.65-0.75 kg/dm³",
                resistance: "Très élevée (classe 1-2)",
                usage: "Charpente traditionnelle, structure visible",
                duree_vie: "100+ ans en intérieur, 25+ ans extérieur"
            },
            'Sapin': {
                description: "Résineux tendre, facile à travailler",
                densite: "0.35-0.45 kg/dm³", 
                resistance: "Moyenne (classe C18-C24)",
                usage: "Charpente courante, ossature",
                duree_vie: "50+ ans en intérieur avec traitement"
            },
            'Épicéa': {
                description: "Résineux très utilisé en charpenterie",
                densite: "0.40-0.50 kg/dm³",
                resistance: "Bonne (classe C20-C24)",
                usage: "Charpente, fermettes, ossature",
                duree_vie: "60+ ans avec traitement approprié"
            },
            'Douglas': {
                description: "Résineux naturellement durable",
                densite: "0.45-0.55 kg/dm³",
                resistance: "Très bonne (classe C22-C27)",
                usage: "Charpente extérieure, structure",
                duree_vie: "20-25 ans extérieur sans traitement"
            },
            'Lamellé-collé': {
                description: "Bois reconstitué haute performance",
                densite: "0.35-0.50 kg/dm³",
                resistance: "Très élevée (GL24h-GL32h)",
                usage: "Grandes portées, structures complexes",
                duree_vie: "50+ ans avec entretien"
            }
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
            const essenceField = document.getElementById('essence_bois');
            const sectionField = document.getElementById('section');
            const longueurField = document.getElementById('longueur');
            const resistanceField = document.getElementById('classe_resistance');
            const traitementField = document.getElementById('traitement');
            const usageField = document.getElementById('usage');
            
            // Remplir la désignation
            designationField.value = suggestion;
            
            // Analyse intelligente de la suggestion
            const sug = suggestion.toLowerCase();
            
            // Déterminer l'essence
            if (sug.includes('chêne') || sug.includes('chene')) {
                essenceField.value = 'Chêne';
                traitementField.value = 'Brut';
                usageField.value = 'Charpente';
            } else if (sug.includes('sapin')) {
                essenceField.value = 'Sapin';
                traitementField.value = 'CL2';
            } else if (sug.includes('épicéa') || sug.includes('epicea')) {
                essenceField.value = 'Épicéa';
                traitementField.value = 'CL2';
            } else if (sug.includes('douglas')) {
                essenceField.value = 'Douglas';
                traitementField.value = 'CL3';
            } else if (sug.includes('lamellé') || sug.includes('lamelle')) {
                essenceField.value = 'Lamellé-collé';
                traitementField.value = 'CL2';
            } else if (sug.includes('osb')) {
                essenceField.value = 'OSB';
                traitementField.value = 'Hydrofuge';
            } else if (sug.includes('contreplaqué') || sug.includes('contreplaque')) {
                essenceField.value = 'Contreplaqué';
                traitementField.value = 'CTB-X';
            }
            
            // Extraire les sections (format XXxYYmm ou XXxYYcm)
            const sectionMatch = sug.match(/(\d+)x(\d+)(mm|cm)?/);
            if (sectionMatch) {
                let section = sectionMatch[1] + 'x' + sectionMatch[2];
                if (sectionMatch[3] === 'cm') {
                    section += 'cm';
                } else {
                    section += 'mm';
                }
                sectionField.value = section;
            }
            
            // Extraire la longueur
            const longueurMatch = sug.match(/long(?:ueur)?\s*(\d+(?:[.,]\d+)?)\s*m/);
            if (longueurMatch) {
                longueurField.value = longueurMatch[1].replace(',', '.');
            }
            
            // Déterminer la classe de résistance
            if (sug.includes('c24')) {
                resistanceField.value = 'C24';
            } else if (sug.includes('c22')) {
                resistanceField.value = 'C22';
            } else if (sug.includes('c18')) {
                resistanceField.value = 'C18';
            } else if (sug.includes('gl24')) {
                resistanceField.value = 'GL24h';
            } else if (sug.includes('gl28')) {
                resistanceField.value = 'GL28h';
            }
            
            // Déterminer l'usage selon le type d'élément
            if (sug.includes('chevron')) {
                usageField.value = 'Couverture';
                quantiteField.value = '15';
                uniteField.value = 'ml';
            } else if (sug.includes('poutre')) {
                usageField.value = 'Structure';
                quantiteField.value = '8';
                uniteField.value = 'ml';
            } else if (sug.includes('fermette')) {
                usageField.value = 'Fermette';
                quantiteField.value = '12';
                uniteField.value = 'unité';
            } else if (sug.includes('solive')) {
                usageField.value = 'Plancher';
                quantiteField.value = '20';
                uniteField.value = 'ml';
            } else if (sug.includes('panne')) {
                usageField.value = 'Couverture';
                quantiteField.value = '6';
                uniteField.value = 'ml';
            } else if (sug.includes('ossature')) {
                usageField.value = 'Ossature';
                quantiteField.value = '25';
                uniteField.value = 'ml';
            } else if (sug.includes('panneau')) {
                usageField.value = 'Coffrage';
                quantiteField.value = '10';
                uniteField.value = 'm²';
            } else {
                quantiteField.value = '10';
                uniteField.value = 'ml';
            }
            
            // Estimer le prix
            const estimation = estimerPrixCharpenterie(suggestion);
            if (estimation > 0) {
                prixField.value = estimation;
            }
            
            // Mettre à jour les informations essence
            updateInfoEssence();
            
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
         * Estimation automatique des prix charpenterie
         */
        function estimerPrixCharpenterie(designation) {
            const des = designation.toLowerCase();
            let prix = 0;
            
            // Déterminer le type d'élément et l'essence
            let typeElement = '';
            let essence = '';
            
            // Identifier le type
            if (des.includes('chevron')) typeElement = 'chevron';
            else if (des.includes('poutre')) typeElement = 'poutre';
            else if (des.includes('madrier')) typeElement = 'madrier';
            else if (des.includes('panne')) typeElement = 'panne';
            else if (des.includes('fermette')) typeElement = 'fermette';
            else if (des.includes('solive')) typeElement = 'solive';
            else if (des.includes('osb')) typeElement = 'osb';
            else if (des.includes('contreplaqué')) typeElement = 'contreplaque';
            
            // Identifier l'essence
            if (des.includes('chêne')) essence = 'chene';
            else if (des.includes('douglas')) essence = 'douglas';
            else if (des.includes('mélèze')) essence = 'meleze';
            else if (des.includes('lamellé')) essence = 'lamelle_colle';
            else if (des.includes('épicéa')) essence = 'epicea';
            else if (des.includes('pin')) essence = 'pin';
            else essence = 'sapin'; // Par défaut
            
            // Calculer le prix de base
            if (typeElement && PRIX_CHARPENTERIE[typeElement]) {
                prix = PRIX_CHARPENTERIE[typeElement].base;
                
                // Appliquer le facteur essence si disponible
                if (essence && PRIX_CHARPENTERIE[essence]) {
                    prix *= PRIX_CHARPENTERIE[essence].factor;
                }
            } else if (essence && PRIX_CHARPENTERIE[essence]) {
                // Si pas de type spécifique, utiliser le prix essence pour du chevron standard
                prix = PRIX_CHARPENTERIE.chevron.base * PRIX_CHARPENTERIE[essence].factor;
            }
            
            // Facteurs multiplicateurs selon spécifications
            if (des.includes('lamellé-collé') || des.includes('lamelle colle')) prix *= 1.8;
            if (des.includes('c24') || des.includes('c27') || des.includes('c30')) prix *= 1.2;
            if (des.includes('gl24h') || des.includes('gl28h') || des.includes('gl32h')) prix *= 1.5;
            if (des.includes('autoclave') || des.includes('cl3') || des.includes('cl4')) prix *= 1.3;
            if (des.includes('raboté')) prix *= 1.1;
            if (des.includes('abouté')) prix *= 1.15;
            
            // Ajustement selon les dimensions
            const sectionMatch = des.match(/(\d+)x(\d+)/);
            if (sectionMatch) {
                const largeur = parseInt(sectionMatch[1]);
                const hauteur = parseInt(sectionMatch[2]);
                const surface = (largeur * hauteur) / 10000; // cm² vers m²
                
                if (surface > 0.025) prix *= 1.5; // Grosses sections
                else if (surface > 0.015) prix *= 1.3;
                else if (surface > 0.010) prix *= 1.1;
            }
            
            return Math.round(prix);
        }

        /**
         * Calculer la section minimale requise
         */
        function calculerSection() {
            const portee = parseFloat(document.getElementById('calc_portee').value) || 0;
            const entraxe = parseFloat(document.getElementById('calc_entraxe').value) || 60;
            const charge = parseFloat(document.getElementById('calc_charge').value) || 150;
            
            if (portee > 0) {
                // Formule simplifiée pour section minimale (Eurocode 5)
                // I = (5 * q * L^4) / (384 * E * flèche_admissible)
                const E = 11000; // Module d'élasticité moyen (N/mm²)
                const fleche_adm = portee * 1000 / 200; // L/200 en mm
                const q = (charge * entraxe / 100) / 1000; // Charge linéaire en N/mm
                
                const I_requis = (5 * q * Math.pow(portee * 1000, 4)) / (384 * E * fleche_adm);
                
                // Estimer hauteur nécessaire (h³/12 pour section rectangulaire)
                const h_theorique = Math.pow(12 * I_requis / 63, 1/3); // Largeur 63mm standard
                
                // Trouver la section normalisée la plus proche
                let sectionRecommandee = '';
                let hauteurMin = 1000;
                
                SECTIONS_NORMALISEES.chevrons.forEach(section => {
                    const [l, h] = section.split('x').map(Number);
                    if (h >= h_theorique && h < hauteurMin) {
                        hauteurMin = h;
                        sectionRecommandee = section + 'mm';
                    }
                });
                
                if (!sectionRecommandee) {
                    // Si aucune section chevron ne convient, essayer les madriers
                    SECTIONS_NORMALISEES.madriers.forEach(section => {
                        const [l, h] = section.split('x').map(Number);
                        if (h >= h_theorique && h < hauteurMin) {
                            hauteurMin = h;
                            sectionRecommandee = section + 'mm';
                        }
                    });
                }
                
                if (sectionRecommandee) {
                    document.getElementById('section').value = sectionRecommandee;
                    showToast(`📐 Section calculée: ${sectionRecommandee}\n` +
                             `Portée: ${portee}m, Entraxe: ${entraxe}cm\n` +
                             `Charge: ${charge}kg/m²`, 'success');
                } else {
                    showToast(`⚠️ Portée trop importante (${portee}m)\nConsultez un bureau d'études`, 'warning');
                }
            } else {
                showToast('⚠️ Veuillez saisir la portée.', 'warning');
            }
        }

        /**
         * Calculer le volume de bois nécessaire
         */
        function calculerVolumeBois() {
            const quantite = parseFloat(document.getElementById('quantite').value) || 0;
            const section = document.getElementById('section').value;
            const longueur = parseFloat(document.getElementById('longueur').value) || 0;
            
            if (quantite > 0 && section && longueur > 0) {
                const sectionMatch = section.match(/(\d+)x(\d+)/);
                if (sectionMatch) {
                    const largeur = parseInt(sectionMatch[1]) / 1000; // mm vers m
                    const hauteur = parseInt(sectionMatch[2]) / 1000; // mm vers m
                    const volume = quantite * largeur * hauteur * longueur;
                    
                    showToast(`📦 Volume bois calculé: ${volume.toFixed(3)} m³\n` +
                             `${quantite} pièces de ${section} × ${longueur}m\n` +
                             `Poids estimé: ${(volume * 450).toFixed(0)} kg`, 'info');
                    
                    // Optionnel : mettre à jour l'unité vers m³ si souhaité
                    // document.getElementById('unite').value = 'm³';
                    // document.getElementById('quantite').value = volume.toFixed(3);
                }
            } else {
                showToast('⚠️ Veuillez saisir quantité, section et longueur.', 'warning');
            }
        }

        /**
         * Calculer le nombre de pièces nécessaires
         */
        function calculerNombrePieces() {
            const portee = parseFloat(document.getElementById('calc_portee').value) || 0;
            const entraxe = parseFloat(document.getElementById('calc_entraxe').value) || 60;
            const longueur = parseFloat(document.getElementById('longueur').value) || 4;
            
            if (portee > 0) {
                // Largeur de bâtiment à couvrir (estimation)
                const largeurBatiment = prompt("Largeur du bâtiment à couvrir (m) :", "8");
                if (largeurBatiment && !isNaN(largeurBatiment)) {
                    const largeur = parseFloat(largeurBatiment);
                    const nombreChevrons = Math.ceil((largeur * 100) / entraxe) + 1;
                    const nombreParLongueur = Math.ceil(portee / longueur);
                    const nombreTotal = nombreChevrons * nombreParLongueur;
                    
                    document.getElementById('quantite').value = nombreTotal;
                    document.getElementById('unite').value = 'unité';
                    
                    showToast(`🔢 Nombre calculé: ${nombreTotal} pièces\n` +
                             `${nombreChevrons} chevrons × ${nombreParLongueur} longueurs\n` +
                             `Entraxe: ${entraxe}cm, Portée: ${portee}m`, 'success');
                }
            } else {
                showToast('⚠️ Veuillez saisir la portée.', 'warning');
            }
        }

        /**
         * Calculer le volume d'une pièce
         */
        function calculerVolumePiece() {
            const section = document.getElementById('section').value;
            const longueur = parseFloat(document.getElementById('longueur').value) || 0;
            
            if (section && longueur > 0) {
                const sectionMatch = section.match(/(\d+)x(\d+)/);
                if (sectionMatch) {
                    const largeur = parseInt(sectionMatch[1]) / 1000; // mm vers m
                    const hauteur = parseInt(sectionMatch[2]) / 1000; // mm vers m
                    const volume = largeur * hauteur * longueur;
                    
                    showToast(`📏 Volume d'une pièce: ${volume.toFixed(4)} m³\n` +
                             `Section: ${section}, Longueur: ${longueur}m\n` +
                             `Poids estimé: ${(volume * 450).toFixed(1)} kg/pièce`, 'info');
                }
            } else {
                showToast('⚠️ Veuillez saisir section et longueur.', 'warning');
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
            
            const prixUnitaire = estimerPrixCharpenterie(designation);
            
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
         * Mettre à jour les informations sur l'essence
         */
        function updateInfoEssence() {
            const essence = document.getElementById('essence_bois').value;
            const infoDiv = document.getElementById('info-essence');
            
            if (essence && INFO_ESSENCES[essence]) {
                const info = INFO_ESSENCES[essence];
                infoDiv.innerHTML = `
                    <strong>${essence}</strong><br>
                    <small><i class="fas fa-info-circle"></i> ${info.description}</small><br>
                    <small><i class="fas fa-weight"></i> Densité: ${info.densite}</small><br>
                    <small><i class="fas fa-shield-alt"></i> Résistance: ${info.resistance}</small><br>
                    <small><i class="fas fa-tools"></i> Usage: ${info.usage}</small><br>
                    <small><i class="fas fa-clock"></i> Durée de vie: ${info.duree_vie}</small>
                `;
            } else {
                infoDiv.innerHTML = '<i class="fas fa-seedling"></i> Sélectionnez une essence pour voir les caractéristiques techniques';
            }
        }

        /**
         * Réinitialiser le formulaire
         */
        function resetFormulaire() {
            if (confirm('🗑️ Êtes-vous sûr de vouloir effacer tous les champs du formulaire ?')) {
                document.getElementById('formCharpenterie').reset();
                document.getElementById('unite').value = 'ml';
                document.getElementById('designation').focus();
                updateInfoEssence();
                showToast('✨ Formulaire réinitialisé !', 'info');
            }
        }

        /**
         * Confirmation de suppression
         */
        function confirmerSuppression(elementId, designation) {
            if (confirm(`⚠️ ATTENTION - Suppression définitive\n\n` +
                       `Êtes-vous sûr de vouloir supprimer cet élément ?\n\n` +
                       `🌲 Élément: ${designation}\n` +
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
         * Raccourcis clavier spécialisés charpenterie
         */
        function initRaccourcisClavier() {
            document.addEventListener('keydown', function(e) {
                // Alt + D = Focus désignation
                if (e.altKey && e.key === 'd') {
                    e.preventDefault();
                    document.getElementById('designation').focus();
                    showToast('🎯 Focus sur Désignation', 'info');
                }
                
                // Alt + S = Calculer section
                if (e.altKey && e.key === 's') {
                    e.preventDefault();
                    calculerSection();
                }
                
                // Alt + V = Calculer volume
                if (e.altKey && e.key === 'v') {
                    e.preventDefault();
                    calculerVolumePiece();
                }
                
                // Alt + N = Calculer nombre de pièces
                if (e.altKey && e.key === 'n') {
                    e.preventDefault();
                    calculerNombrePieces();
                }
                
                // Alt + E = Estimation prix
                if (e.altKey && e.key === 'e') {
                    e.preventDefault();
                    calculerEstimation();
                }
                
                // Alt + B = Focus essence bois
                if (e.altKey && e.key === 'b') {
                    e.preventDefault();
                    document.getElementById('essence_bois').focus();
                    showToast('🌲 Focus sur Essence de bois', 'info');
                }
                
                // Alt + T = Focus traitement
                if (e.altKey && e.key === 't') {
                    e.preventDefault();
                    document.getElementById('traitement').focus();
                    showToast('🎨 Focus sur Traitement', 'info');
                }
                
                // Alt + U = Focus usage
                if (e.altKey && e.key === 'u') {
                    e.preventDefault();
                    document.getElementById('usage').focus();
                    showToast('🔨 Focus sur Usage', 'info');
                }
                
                // Ctrl + Entrée = Soumettre formulaire
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('formCharpenterie').submit();
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
                background: ${type === 'info' ? 'var(--accent-blue)' : type === 'warning' ? 'var(--oak-brown)' : type === 'success' ? 'var(--wood-primary)' : 'var(--accent-red)'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-medium);
                z-index: 9999;
                max-width: 400px;
                white-space: pre-line;
                animation: slideInRight 0.4s ease-out;
            `;
            
            const icon = type === 'info' ? '🌲' : type === 'warning' ? '⚠️' : type === 'success' ? '✅' : '❌';
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
         * Validation en temps réel des champs charpenterie
         */
        function initValidationTempsReel() {
            const sectionField = document.getElementById('section');
            const longueurField = document.getElementById('longueur');
            const quantiteField = document.getElementById('quantite');
            const prixField = document.getElementById('prix_unitaire');
            const essenceField = document.getElementById('essence_bois');
            
            // Validation section
            sectionField.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && !value.match(/^\d+x\d+(mm|cm)?$/i)) {
                    this.style.borderColor = 'var(--accent-red)';
                    this.title = 'Format invalide. Exemples: 63x175mm, 20x30cm';
                } else {
                    this.style.borderColor = '';
                    this.title = '';
                }
            });
            
            // Validation longueur
            longueurField.addEventListener('input', function() {
                const value = parseFloat(this.value);
                if (value && (value < 0 || value > 20)) {
                    this.style.borderColor = 'var(--accent-red)';
                    this.title = 'Longueur doit être entre 0 et 20 mètres';
                } else {
                    this.style.borderColor = '';
                    this.title = '';
                }
            });
            
            // Mise à jour info essence
            essenceField.addEventListener('change', updateInfoEssence);
            
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
                            background: var(--wood-primary);
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

        /**
         * Suggérer des sections selon l'usage
         */
        function suggerSection() {
            const usage = document.getElementById('usage').value;
            const sectionField = document.getElementById('section');
            
            if (usage && !sectionField.value) {
                let sections = [];
                
                switch(usage) {
                    case 'Couverture':
                        sections = SECTIONS_NORMALISEES.chevrons;
                        break;
                    case 'Structure':
                        sections = SECTIONS_NORMALISEES.poutres;
                        break;
                    case 'Plancher':
                        sections = SECTIONS_NORMALISEES.solives;
                        break;
                    case 'Charpente':
                        sections = SECTIONS_NORMALISEES.madriers;
                        break;
                    default:
                        sections = SECTIONS_NORMALISEES.chevrons;
                }
                
                if (sections.length > 0) {
                    // Proposer une section moyenne
                    const sectionMoyenne = sections[Math.floor(sections.length / 2)];
                    sectionField.value = sectionMoyenne + 'mm';
                    
                    showToast(`💡 Section suggérée pour ${usage}: ${sectionMoyenne}mm\n` +
                             `Autres options disponibles dans les normes`, 'info');
                }
            }
        }

        /**
         * Vérification de cohérence section/usage
         */
        function verifierCoherence() {
            const section = document.getElementById('section').value;
            const usage = document.getElementById('usage').value;
            const longueur = parseFloat(document.getElementById('longueur').value) || 0;
            
            if (section && usage && longueur > 0) {
                const sectionMatch = section.match(/(\d+)x(\d+)/);
                if (sectionMatch) {
                    const hauteur = parseInt(sectionMatch[2]);
                    
                    // Règles empiriques de vérification
                    let alerte = '';
                    
                    if (usage === 'Couverture' && longueur > 4 && hauteur < 175) {
                        alerte = '⚠️ Section possiblement insuffisante pour cette portée en couverture';
                    } else if (usage === 'Structure' && longueur > 6 && hauteur < 240) {
                        alerte = '⚠️ Section structurelle probablement sous-dimensionnée';
                    } else if (usage === 'Plancher' && longueur > 4 && hauteur < 200) {
                        alerte = '⚠️ Hauteur de solive possiblement insuffisante';
                    }
                    
                    if (alerte) {
                        showToast(alerte + '\nConsultez les règles de calcul ou un bureau d\'études', 'warning');
                    }
                }
            }
        }

        // ===== INITIALISATION AU CHARGEMENT =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🌲 Module Charpenterie GSN ProDevis360° initialisé');
            
            // Initialiser toutes les fonctionnalités
            initRaccourcisClavier();
            initAnimationsScroll();
            initValidationTempsReel();
            updateInfoEssence();
            
            // Ajouter les événements pour les suggestions automatiques
            document.getElementById('usage').addEventListener('change', suggerSection);
            document.getElementById('section').addEventListener('blur', verifierCoherence);
            document.getElementById('longueur').addEventListener('blur', verifierCoherence);
            
            // Afficher les raccourcis clavier
            showToast(`⌨️ Raccourcis disponibles:\n` +
                     `Alt+D = Désignation\n` +
                     `Alt+S = Calculer section\n` +
                     `Alt+V = Volume pièce\n` +
                     `Alt+N = Nombre pièces\n` +
                     `Alt+E = Estimation prix\n` +
                     `Alt+B = Essence bois\n` +
                     `Alt+T = Traitement\n` +
                     `Alt+U = Usage\n` +
                     `Ctrl+Entrée = Envoyer`, 'info');
            
            // Focus automatique sur le premier champ
            const firstField = document.getElementById('designation');
            if (firstField && !firstField.value) {
                setTimeout(() => firstField.focus(), 500);
            }
            
            // Vérifier la cohérence des données existantes
            const elements = <?= json_encode($elements_charpenterie) ?>;
            let alertesCoherence = 0;
            
            elements.forEach(element => {
                const longueur = parseFloat(element.longueur);
                const section = element.section;
                
                if (longueur > 0 && section) {
                    const sectionMatch = section.match(/(\d+)x(\d+)/);
                    if (sectionMatch) {
                        const hauteur = parseInt(sectionMatch[2]);
                        const rapport = longueur * 1000 / hauteur; // Élancement
                        
                        if (rapport > 25) { // Élancement critique
                            alertesCoherence++;
                        }
                    }
                }
            });
            
            if (alertesCoherence > 0) {
                setTimeout(() => {
                    showToast(`⚠️ ${alertesCoherence} élément(s) avec élancement élevé détecté(s).\n` +
                             `Vérifiez les calculs de résistance et flambement.`, 'warning');
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