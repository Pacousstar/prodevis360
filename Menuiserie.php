<?php
// ===== MENUISERIE.PHP - PARTIE 1 : PHP LOGIC & CONFIG =====
// VERSION UNIFORMISÉE GSN ProDevis360°
require_once 'functions.php';

// Configuration du module actuel
$current_module = 'menuiserie';

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

// Suggestions spécialisées pour la menuiserie
$suggestions_menuiserie = [
    // PORTES INTÉRIEURES
    'Porte isoplane 73x204cm épaisseur 40mm',
    'Porte plane laquée blanc 83x204cm épaisseur 40mm',
    'Porte postformée chêne 73x204cm épaisseur 40mm',
    'Porte vitrée cuisine 63x204cm épaisseur 40mm',
    'Porte coulissante applique 73x204cm rail 150cm',
    'Porte pliante accordéon PVC blanc 83x204cm',
    'Porte de placard coulissante miroir 240x250cm',
    'Porte persienne pin 63x204cm épaisseur 35mm',
    'Porte moulurée tradition 83x204cm épaisseur 40mm',
    'Bloc-porte complet huisserie + serrure',
    
    // PORTES EXTÉRIEURES
    'Porte d\'entrée bois exotique 90x215cm épaisseur 68mm',
    'Porte d\'entrée PVC blanc 90x215cm double vitrage',
    'Porte d\'entrée aluminium gris anthracite 80x215cm',
    'Porte de service PVC blanc 80x200cm simple vitrage',
    'Porte de garage basculante 240x200cm isolée',
    'Porte de garage sectionnelle 240x200cm motorisée',
    'Portail battant aluminium 300x180cm avec piliers',
    'Portillon simple aluminium 100x180cm avec serrure',
    'Porte-fenêtre PVC blanc 240x215cm double vitrage',
    'Porte-fenêtre aluminium coulissante 300x215cm',
    
    // FENÊTRES
    'Fenêtre PVC blanc 2 vantaux 120x100cm double vitrage',
    'Fenêtre PVC blanc 1 vantail 60x80cm double vitrage',
    'Fenêtre aluminium gris 2 vantaux 140x120cm',
    'Fenêtre bois exotique 2 vantaux 100x100cm',
    'Fenêtre de toit Velux 78x98cm double vitrage',
    'Fenêtre de toit Velux 114x118cm motorisée',
    'Châssis fixe PVC blanc 100x60cm double vitrage',
    'Fenêtre coulissante aluminium 180x120cm',
    'Fenêtre oscillo-battante PVC 80x100cm',
    'Baie vitrée coulissante 3 vantaux 300x215cm',
    
    // VOLETS ET STORES
    'Volet battant bois exotique 60x120cm épaisseur 27mm',
    'Volet roulant PVC blanc coffre tunnel 120x100cm',
    'Volet roulant aluminium motorisé 140x120cm',
    'Store banne coffre intégral largeur 400cm',
    'Store vénitien aluminium 120x160cm',
    'Store enrouleur screen 100x180cm',
    'Pergola bioclimatique aluminium 400x300cm',
    'Brise-soleil orientable aluminium largeur 200cm',
    'Contrevent bois pin 2 vantaux 120x150cm',
    'Jalousie aluminium lames orientables 100x120cm',
    
    // CLOISONS ET DOUBLAGES
    'Cloison placo BA13 sur rail 48mm hauteur 250cm',
    'Cloison placo hydrofuge BA13 sur rail 70mm',
    'Doublage placo+isolant 13+40mm sur rail',
    'Cloison béton cellulaire 7cm épaisseur',
    'Cloison carreau de plâtre 7cm standard',
    'Cloison verrière atelier acier 200x250cm',
    'Cloison mobile accordéon bureau hauteur 250cm',
    'Cloison japonaise coulissante bois 180x250cm',
    'Claustra bois ajouré séparation 120x200cm',
    'Verrière cuisine acier noir 150x100cm',
    
    // ESCALIERS
    'Escalier droit bois hêtre 14 marches avec rampe',
    'Escalier quart tournant chêne limon central',
    'Escalier hélicoïdal métal diamètre 140cm',
    'Escalier escamotable combles 120x70cm',
    'Rampe escalier inox brossé main courante bois',
    'Garde-corps escalier bois balustres tournés',
    'Marche rénovation hêtre 100x33cm épaisseur 40mm',
    'Contremarche rénovation hêtre 100x18cm',
    'Main courante chêne massif section 45x70mm',
    'Limon escalier bois massif sapin 45x300mm',
    
    // PARQUETS ET SOLS
    'Parquet massif chêne français 14x70mm cloué',
    'Parquet contrecollé chêne huilé 14x125mm',
    'Parquet stratifié décor chêne naturel 8mm',
    'Parquet bambou vertical naturel 15x96mm',
    'Lame composite terrasse 25x140mm coloris teck',
    'Parquet point de Hongrie chêne 14x70mm',
    'Sol vinyle LVT imitation parquet 4mm',
    'Plinthe assortie parquet chêne 16x80mm',
    'Baguette de finition quart de rond 16x16mm',
    'Sous-couche parquet isolante phonique 3mm',
    
    // AMÉNAGEMENTS INTÉRIEURS
    'Bibliothèque sur mesure mélaminé blanc 200x40x250cm',
    'Dressing sur mesure mélaminé chêne clair',
    'Placard sous pente portes coulissantes',
    'Meuble TV suspendu laqué blanc 180x40cm',
    'Étagères murales invisibles chêne massif',
    'Tête de lit capitonnée sur mesure largeur 160cm',
    'Banquette coffre entrée mélaminé 120x40x45cm',
    'Meuble vasque salle de bain chêne 80x50cm',
    'Plan de travail cuisine stratifié 240x65cm',
    'Crédence cuisine verre trempé 300x60cm',
    
    // TERRASSES ET EXTÉRIEURS
    'Terrasse bois exotique IPE lames 21x145mm',
    'Terrasse composite rainurée gris anthracite',
    'Pergola bois pin autoclave 400x300x250cm',
    'Carport bois lamellé-collé 1 voiture 300x500cm',
    'Abri de jardin bois 12m² toit double pente',
    'Clôture bois exotique lames verticales 180cm',
    'Portail coulissant bois remplissage lames',
    'Bac à fleurs bois autoclave 100x40x40cm',
    'Treillage bois décoratif 180x180cm',
    'Lame de bardage bois claire-voie 21x95mm',
    
    // QUINCAILLERIE ET ACCESSOIRES
    'Serrure 3 points porte d\'entrée A2P**',
    'Serrure à larder bec de cane axe 40mm',
    'Cylindre européen 30x30mm 5 clés',
    'Poignée de porte design inox brossé',
    'Paumelle forte porte lourde 140mm',
    'Ferme-porte hydraulique force 3',
    'Crémone 2 points fenêtre espagnolette',
    'Compas de fenêtre réglable inox',
    'Gond penture volet charge 60kg',
    'Loquet de portail automatique réglable',
    
    // ISOLATION ET ÉTANCHÉITÉ
    'Mousse polyuréthane expansive 750ml',
    'Mastic acrylique blanc menuiserie 310ml',
    'Joint d\'étanchéité EPDM noir largeur 10mm',
    'Pare-vapeur menuiserie rouleau 50m',
    'Compribande largeur 15mm expansion 4-9mm',
    'Film de protection chantier 100 microns',
    'Adhésif étanche membrane largeur 60mm',
    'Silicone neutre transparent 310ml',
    'Colle polyuréthane bois extérieur 750ml',
    'Visserie inox A4 torx menuiserie',
    
    // FINITIONS ET PROTECTION
    'Lasure bois extérieur chêne moyen 2.5L',
    'Peinture bois extérieur microporeuse blanc 2.5L',
    'Huile parquet incolore finition satinée 1L',
    'Vitrificateur parquet trafic intense 2.5L',
    'Saturateur terrasse bois teck 5L',
    'Traitement préventif bois extérieur 5L',
    'Décapant peinture bois écologique 1L',
    'Cire d\'abeille protection parquet 500ml',
    'Nettoyant dégraissant bois avant finition 1L',
    'Primaire d\'accrochage bois exotique 1L'
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
            $dimensions = trim($_POST['dimensions'] ?? '');
            $essence_bois = trim($_POST['essence_bois'] ?? '');
            $finition = trim($_POST['finition'] ?? '');
            $type_pose = trim($_POST['type_pose'] ?? '');
            $epaisseur = floatval($_POST['epaisseur'] ?? 0);
            $usage = trim($_POST['usage'] ?? '');
            
            // Validations spécifiques menuiserie
            if (empty($designation)) {
                throw new Exception("La désignation est obligatoire.");
            }
            if ($quantite <= 0) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }
            if ($prix_unitaire < 0) {
                throw new Exception("Le prix unitaire ne peut pas être négatif.");
            }
            
            // Validation dimensions si fournie (format : LxHxE ou LxH)
            if (!empty($dimensions) && !preg_match('/^\d+x\d+(?:x\d+)?(?:cm|mm)?$/i', $dimensions)) {
                throw new Exception("Format de dimensions invalide (ex: 120x80cm, 240x215x68mm).");
            }
            
            // Validation épaisseur
            if ($epaisseur < 0 || $epaisseur > 200) {
                throw new Exception("L'épaisseur doit être entre 0 et 200mm.");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Insertion en base
            $stmt = $conn->prepare("
                INSERT INTO menuiserie (
                    projet_id, devis_id, designation, quantite, unite, 
                    prix_unitaire, total, dimensions, essence_bois, 
                    finition, type_pose, epaisseur, usage, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param(
                "iisdsdsssssds", 
                $projet_id, $devis_id, $designation, $quantite, $unite,
                $prix_unitaire, $total, $dimensions, $essence_bois,
                $finition, $type_pose, $epaisseur, $usage
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'menuiserie');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'menuiserie', 'Ajout', "Élément ajouté : {$designation}");
                
                $message = "Élément menuiserie ajouté avec succès !";
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
            $dimensions = trim($_POST['dimensions'] ?? '');
            $essence_bois = trim($_POST['essence_bois'] ?? '');
            $finition = trim($_POST['finition'] ?? '');
            $type_pose = trim($_POST['type_pose'] ?? '');
            $epaisseur = floatval($_POST['epaisseur'] ?? 0);
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
            
            if (!empty($dimensions) && !preg_match('/^\d+x\d+(?:x\d+)?(?:cm|mm)?$/i', $dimensions)) {
                throw new Exception("Format de dimensions invalide (ex: 120x80cm, 240x215x68mm).");
            }
            
            if ($epaisseur < 0 || $epaisseur > 200) {
                throw new Exception("L'épaisseur doit être entre 0 et 200mm.");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Mise à jour en base
            $stmt = $conn->prepare("
                UPDATE menuiserie SET 
                    designation = ?, quantite = ?, unite = ?, prix_unitaire = ?, 
                    total = ?, dimensions = ?, essence_bois = ?, finition = ?, 
                    type_pose = ?, epaisseur = ?, usage = ?, date_modification = NOW()
                WHERE id = ? AND projet_id = ? AND devis_id = ?
            ");
            
            $stmt->bind_param(
                "sdsdssssdsiii",
                $designation, $quantite, $unite, $prix_unitaire, $total,
                $dimensions, $essence_bois, $finition, $type_pose, 
                $epaisseur, $usage, $element_id, $projet_id, $devis_id
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'menuiserie');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'menuiserie', 'Modification', "Élément modifié : {$designation}");
                
                $message = "Élément menuiserie modifié avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de la modification : " . $conn->error);
            }
            
        } elseif ($action == 'supprimer' && $element_id > 0) {
            // Récupération de la désignation avant suppression
            $stmt_get = $conn->prepare("SELECT designation FROM menuiserie WHERE id = ? AND projet_id = ? AND devis_id = ?");
            $stmt_get->bind_param("iii", $element_id, $projet_id, $devis_id);
            $stmt_get->execute();
            $result_get = $stmt_get->get_result();
            $element_data = $result_get->fetch_assoc();
            
            if ($element_data) {
                // Suppression de l'élément
                $stmt = $conn->prepare("DELETE FROM menuiserie WHERE id = ? AND projet_id = ? AND devis_id = ?");
                $stmt->bind_param("iii", $element_id, $projet_id, $devis_id);
                
                if ($stmt->execute()) {
                    // Mise à jour du récapitulatif
                    updateRecapitulatif($projet_id, $devis_id, 'menuiserie');
                    
                    // Sauvegarde dans l'historique
                    sauvegarderHistorique($projet_id, $devis_id, 'menuiserie', 'Suppression', "Élément supprimé : {$element_data['designation']}");
                    
                    $message = "Élément menuiserie supprimé avec succès !";
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

// Récupération des éléments de menuiserie pour affichage
$elements_menuiserie = [];
$total_module = 0;

$stmt = $conn->prepare("
    SELECT id, designation, quantite, unite, prix_unitaire, total,
           dimensions, essence_bois, finition, type_pose, epaisseur, usage,
           DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation_fr,
           DATE_FORMAT(date_modification, '%d/%m/%Y %H:%i') as date_modification_fr
    FROM menuiserie 
    WHERE projet_id = ? AND devis_id = ? 
    ORDER BY date_creation DESC
");

$stmt->bind_param("ii", $projet_id, $devis_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $elements_menuiserie[] = $row;
    $total_module += $row['total'];
}

// Récupération de l'élément à modifier si nécessaire
$element_a_modifier = null;
if ($action == 'modifier' && $element_id > 0) {
    $stmt = $conn->prepare("
        SELECT * FROM menuiserie 
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
    <title>Menuiserie - <?= htmlspecialchars($projet_devis_info['nom_projet']) ?> | GSN ProDevis360°</title>
    
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
            
            /* Variables spécifiques menuiserie */
            --woodwork-primary: #8e44ad;
            --woodwork-light: #bb6bd9;
            --woodwork-dark: #7d3c98;
            --wood-brown: #8b4513;
            --wood-beige: #daa520;
            --varnish-gold: #ffd700;
            --paint-white: #f8f8ff;
            --hardware-silver: #c0c0c0;
            --oak-natural: #d2b48c;
            --pine-light: #f5deb3;
            --mahogany-dark: #c04000;
            --glass-blue: #87ceeb;
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
            background: var(--woodwork-primary);
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

        /* ===== SUGGESTIONS MENUISERIE ===== */
        .suggestions-menuiserie {
            background: linear-gradient(135deg, var(--woodwork-primary) 0%, var(--woodwork-dark) 100%);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .suggestions-menuiserie h4 {
            color: var(--secondary-white);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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

        /* ===== CALCULATEUR MENUISERIE ===== */
        .calculator-section {
            background: linear-gradient(135deg, var(--wood-brown) 0%, var(--varnish-gold) 100%);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            color: var(--neutral-dark);
        }

        .calculator-section h4 {
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: var(--neutral-dark);
        }

        .calc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 0.75rem;
            align-items: center;
        }

        .calc-input {
            padding: 0.5rem;
            border: 1px solid rgba(0,0,0,0.2);
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

        /* ===== BADGES SPÉCIALISÉS MENUISERIE ===== */
        .badge-essence {
            background: var(--wood-brown);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-dimensions {
            background: var(--woodwork-primary);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-finition {
            background: var(--varnish-gold);
            color: var(--neutral-dark);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-pose {
            background: var(--hardware-silver);
            color: var(--neutral-dark);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-usage {
            background: var(--oak-natural);
            color: var(--neutral-dark);
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
            color: var(--woodwork-light);
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

        @keyframes woodWorking {
            0% { transform: translateX(-100%) skewX(-15deg); opacity: 0; }
            50% { transform: translateX(0%) skewX(0deg); opacity: 1; }
            100% { transform: translateX(0%) skewX(0deg); opacity: 1; }
        }

        .wood-working {
            animation: woodWorking 0.8s ease-out;
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
                        <i class="fas fa-hammer"></i>
                        Module Menuiserie
                        <span class="module-badge">
                            <i class="fas fa-door-open"></i>
                            Bois & Aménagements
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

        <!-- ===== BANNIÈRE INFORMATIVE MENUISERIE ===== -->
        <div class="info-banner menuiserie-banner fade-in-up">
            <div class="banner-content">
                <div class="banner-icon">
                    <i class="fas fa-hammer"></i>
                </div>
                <div class="banner-text">
                    <h4>Module Menuiserie & Aménagements</h4>
                    <p>Portes, fenêtres, parquets, placards, escaliers et tous travaux de menuiserie intérieure/extérieure</p>
                </div>
                <div class="banner-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?= count($elements_menuiserie) ?></span>
                        <span class="stat-label">Éléments</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?= number_format($total_module, 0, ',', ' ') ?></span>
                        <span class="stat-label">FCFA</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== INDICATEURS RAPIDES MENUISERIE ===== -->
        <div class="quick-indicators fade-in-up">
            <div class="indicator-grid">
                <div class="indicator-card wood-primary">
                    <div class="indicator-icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="indicator-content">
                        <span class="indicator-title">Portes & Fenêtres</span>
                        <span class="indicator-value">
                            <?php 
                            $portes_fenetres = 0;
                            foreach ($elements_menuiserie as $element) {
                                if (stripos($element['designation'], 'porte') !== false || 
                                    stripos($element['designation'], 'fenêtre') !== false ||
                                    stripos($element['designation'], 'fenetre') !== false) {
                                    $portes_fenetres++;
                                }
                            }
                            echo $portes_fenetres;
                            ?> éléments
                        </span>
                    </div>
                </div>

                <div class="indicator-card wood-secondary">
                    <div class="indicator-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div class="indicator-content">
                        <span class="indicator-title">Parquets & Sols</span>
                        <span class="indicator-value">
                            <?php 
                            $parquets = 0;
                            foreach ($elements_menuiserie as $element) {
                                if (stripos($element['designation'], 'parquet') !== false || 
                                    stripos($element['designation'], 'lame') !== false ||
                                    stripos($element['designation'], 'plancher') !== false) {
                                    $parquets++;
                                }
                            }
                            echo $parquets;
                            ?> éléments
                        </span>
                    </div>
                </div>

                <div class="indicator-card wood-accent">
                    <div class="indicator-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="indicator-content">
                        <span class="indicator-title">Placards & Rangements</span>
                        <span class="indicator-value">
                            <?php 
                            $placards = 0;
                            foreach ($elements_menuiserie as $element) {
                                if (stripos($element['designation'], 'placard') !== false || 
                                    stripos($element['designation'], 'dressing') !== false ||
                                    stripos($element['designation'], 'rangement') !== false ||
                                    stripos($element['designation'], 'bibliothèque') !== false) {
                                    $placards++;
                                }
                            }
                            echo $placards;
                            ?> éléments
                        </span>
                    </div>
                </div>

                <div class="indicator-card wood-dark">
                    <div class="indicator-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="indicator-content">
                        <span class="indicator-title">Quincaillerie</span>
                        <span class="indicator-value">
                            <?php 
                            $quincaillerie = 0;
                            foreach ($elements_menuiserie as $element) {
                                if (stripos($element['designation'], 'serrure') !== false || 
                                    stripos($element['designation'], 'poignée') !== false ||
                                    stripos($element['designation'], 'charnière') !== false ||
                                    stripos($element['designation'], 'verrou') !== false ||
                                    stripos($element['designation'], 'cylindre') !== false) {
                                    $quincaillerie++;
                                }
                            }
                            echo $quincaillerie;
                            ?> éléments
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== CONSEILS TECHNIQUES MENUISERIE ===== -->
        <div class="technical-tips fade-in-up">
            <div class="tips-header">
                <h5>
                    <i class="fas fa-lightbulb"></i>
                    Conseils Techniques Menuiserie
                </h5>
                <button type="button" class="btn-toggle-tips" onclick="toggleTips()">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="tips-content" id="tipsContent" style="display: none;">
                <div class="tips-grid">
                    <div class="tip-card">
                        <div class="tip-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="tip-content">
                            <h6>Portes Intérieures</h6>
                            <p>Hauteur standard : 204cm<br>
                               Largeurs courantes : 63, 73, 83, 93cm<br>
                               Épaisseur : 40mm (standard)</p>
                        </div>
                    </div>

                    <div class="tip-card">
                        <div class="tip-icon">
                            <i class="fas fa-window-maximize"></i>
                        </div>
                        <div class="tip-content">
                            <h6>Fenêtres</h6>
                            <p>Coefficient Uw ≤ 1,3 W/m²K (RT2012)<br>
                               Double vitrage 4/16/4 minimum<br>
                               Étanchéité AEV classe 4</p>
                        </div>
                    </div>

                    <div class="tip-card">
                        <div class="tip-icon">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <div class="tip-content">
                            <h6>Parquets</h6>
                            <p>Épaisseur massif : 14-20mm<br>
                               Contrecollé : 10-15mm<br>
                               Stratifié : 7-12mm<br>
                               Joint de dilatation : 8-10mm</p>
                        </div>
                    </div>

                    <div class="tip-card">
                        <div class="tip-icon">
                            <i class="fas fa-hammer"></i>
                        </div>
                        <div class="tip-content">
                            <h6>Essences Bois</h6>
                            <p>Chêne : dur, durable (classe 2)<br>
                               Hêtre : dur, nerveux<br>
                               Pin : tendre, économique<br>
                               Exotic : IPE, Teck (ext.)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== QUICK ACTIONS MENUISERIE ===== -->
        <div class="quick-actions fade-in-up">
            <div class="actions-header">
                <h5>
                    <i class="fas fa-bolt"></i>
                    Actions Rapides
                </h5>
            </div>
            <div class="actions-grid">
                <button type="button" class="action-btn" onclick="ajouterPorteStandard()">
                    <i class="fas fa-door-closed"></i>
                    <span>Porte Standard</span>
                </button>
                <button type="button" class="action-btn" onclick="ajouterFenetreStandard()">
                    <i class="fas fa-window-maximize"></i>
                    <span>Fenêtre Standard</span>
                </button>
                <button type="button" class="action-btn" onclick="ajouterParquetStandard()">
                    <i class="fas fa-th-large"></i>
                    <span>Parquet Standard</span>
                </button>
                <button type="button" class="action-btn" onclick="ouvrirCalculateurSurface()">
                    <i class="fas fa-calculator"></i>
                    <span>Calculateur</span>
                </button>
            </div>
        </div>

        <style>
        /* Styles additionnels pour la partie 3 Menuiserie */
        .info-banner {
            background: linear-gradient(135deg, var(--wood-primary) 0%, var(--wood-dark) 100%);
            color: var(--secondary-white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-medium);
        }

        .menuiserie-banner {
            border-left: 5px solid var(--wood-light);
        }

        .banner-content {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .banner-icon {
            font-size: 3rem;
            opacity: 0.9;
        }

        .banner-text h4 {
            margin: 0 0 0.5rem 0;
            font-weight: 600;
        }

        .banner-text p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .banner-stats {
            display: flex;
            gap: 2rem;
            margin-left: auto;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            display: block;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--wood-light);
        }

        .stat-label {
            font-size: 0.85rem;
            opacity: 0.8;
        }

        .quick-indicators {
            margin-bottom: 2rem;
        }

        .indicator-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .indicator-card {
            background: var(--secondary-white);
            padding: 1.25rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition-smooth);
        }

        .indicator-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
        }

        .indicator-card.wood-primary {
            border-left: 4px solid var(--wood-primary);
        }

        .indicator-card.wood-secondary {
            border-left: 4px solid var(--wood-secondary);
        }

        .indicator-card.wood-accent {
            border-left: 4px solid var(--wood-accent);
        }

        .indicator-card.wood-dark {
            border-left: 4px solid var(--wood-dark);
        }

        .indicator-icon {
            font-size: 2rem;
            color: var(--wood-primary);
            opacity: 0.8;
        }

        .indicator-content {
            display: flex;
            flex-direction: column;
        }

        .indicator-title {
            font-weight: 600;
            color: var(--neutral-dark);
            font-size: 0.9rem;
        }

        .indicator-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--wood-primary);
            margin-top: 0.25rem;
        }

        .technical-tips {
            background: var(--secondary-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .tips-header {
            background: linear-gradient(135deg, var(--wood-accent) 0%, var(--wood-secondary) 100%);
            color: var(--secondary-white);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tips-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .btn-toggle-tips {
            background: rgba(255,255,255,0.2);
            border: none;
            color: var(--secondary-white);
            padding: 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .btn-toggle-tips:hover {
            background: rgba(255,255,255,0.3);
        }

        .tips-content {
            padding: 1.5rem;
        }

        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .tip-card {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: var(--neutral-light);
            border-radius: 6px;
            border-left: 3px solid var(--wood-primary);
        }

        .tip-icon {
            font-size: 1.5rem;
            color: var(--wood-primary);
            margin-top: 0.25rem;
        }

        .tip-content h6 {
            margin: 0 0 0.5rem 0;
            color: var(--neutral-dark);
            font-weight: 600;
        }

        .tip-content p {
            margin: 0;
            font-size: 0.85rem;
            line-height: 1.4;
            color: var(--neutral-gray);
        }

        .quick-actions {
            background: var(--secondary-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
            padding: 1.5rem;
        }

        .actions-header h5 {
            margin: 0 0 1rem 0;
            color: var(--neutral-dark);
            font-weight: 600;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            background: linear-gradient(135deg, var(--wood-primary) 0%, var(--wood-dark) 100%);
            color: var(--secondary-white);
            border: none;
            padding: 1rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition-smooth);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .action-btn i {
            font-size: 1.5rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .banner-content {
                flex-direction: column;
                text-align: center;
            }

            .banner-stats {
                margin-left: 0;
                justify-content: center;
            }

            .indicator-grid {
                grid-template-columns: 1fr;
            }

            .tips-grid {
                grid-template-columns: 1fr;
            }

            .actions-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .actions-grid {
                grid-template-columns: 1fr;
            }

            .stat-item {
                text-align: center;
            }

            .banner-stats {
                gap: 1rem;
            }
        }
        </style>

        <script>
        // Scripts pour la partie 3 Menuiserie
        function toggleTips() {
            const content = document.getElementById('tipsContent');
            const btn = document.querySelector('.btn-toggle-tips i');
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                btn.className = 'fas fa-chevron-up';
            } else {
                content.style.display = 'none';
                btn.className = 'fas fa-chevron-down';
            }
        }

        function ajouterPorteStandard() {
            document.getElementById('designation').value = 'Porte intérieure isoplarie 83x204cm';
            document.getElementById('quantite').value = '1';
            document.getElementById('unite').value = 'unité';
            document.getElementById('prix_unitaire').value = '85000';
            if (document.getElementById('type_element')) {
                document.getElementById('type_element').value = 'Porte';
            }
            if (document.getElementById('essence_bois')) {
                document.getElementById('essence_bois').value = 'Pin';
            }
            if (document.getElementById('finition')) {
                document.getElementById('finition').value = 'Prêt à peindre';
            }
            
            // Focus sur le bouton d'ajout
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                submitBtn.style.background = 'linear-gradient(135deg, #27ae60 0%, #2ecc71 100%)';
                setTimeout(() => {
                    submitBtn.style.background = '';
                }, 2000);
            }
        }

        function ajouterFenetreStandard() {
            document.getElementById('designation').value = 'Fenêtre PVC blanc 2 vantaux 125x120cm';
            document.getElementById('quantite').value = '1';
            document.getElementById('unite').value = 'unité';
            document.getElementById('prix_unitaire').value = '320000';
            if (document.getElementById('type_element')) {
                document.getElementById('type_element').value = 'Fenêtre';
            }
            if (document.getElementById('materiaux')) {
                document.getElementById('materiaux').value = 'PVC';
            }
            if (document.getElementById('vitrage')) {
                document.getElementById('vitrage').value = 'Double vitrage 4/16/4';
            }
            
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                submitBtn.style.background = 'linear-gradient(135deg, #3498db 0%, #2980b9 100%)';
                setTimeout(() => {
                    submitBtn.style.background = '';
                }, 2000);
            }
        }

        function ajouterParquetStandard() {
            document.getElementById('designation').value = 'Parquet contrecollé chêne 14mm pose flottante';
            document.getElementById('quantite').value = '25';
            document.getElementById('unite').value = 'm²';
            document.getElementById('prix_unitaire').value = '45000';
            if (document.getElementById('type_element')) {
                document.getElementById('type_element').value = 'Parquet';
            }
            if (document.getElementById('essence_bois')) {
                document.getElementById('essence_bois').value = 'Chêne';
            }
            if (document.getElementById('finition')) {
                document.getElementById('finition').value = 'Vernis mat';
            }
            
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                submitBtn.style.background = 'linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%)';
                setTimeout(() => {
                    submitBtn.style.background = '';
                }, 2000);
            }
        }

        function ouvrirCalculateurSurface() {
            const calculateur = prompt('Calculateur Surface Parquet\n\nSaisissez les dimensions (format: LxL en mètres):', '5x4');
            if (calculateur) {
                const match = calculateur.match(/(\d+(?:\.\d+)?)x(\d+(?:\.\d+)?)/);
                if (match) {
                    const longueur = parseFloat(match[1]);
                    const largeur = parseFloat(match[2]);
                    const surface = longueur * largeur;
                    const surfaceAvecPertes = surface * 1.1; // +10% pertes
                    
                    alert(`Surface calculée:\n` +
                          `Surface nette: ${surface} m²\n` +
                          `Avec pertes (10%): ${surfaceAvecPertes.toFixed(2)} m²\n\n` +
                          `Cette valeur sera ajoutée au champ quantité.`);
                    
                    document.getElementById('quantite').value = surfaceAvecPertes.toFixed(2);
                    document.getElementById('unite').value = 'm²';
                } else {
                    alert('Format invalide. Utilisez le format: 5x4');
                }
            }
        }

        // Animation d'entrée pour les indicateurs
        document.addEventListener('DOMContentLoaded', function() {
            const indicators = document.querySelectorAll('.indicator-card');
            indicators.forEach((indicator, index) => {
                setTimeout(() => {
                    indicator.style.opacity = '0';
                    indicator.style.transform = 'translateY(20px)';
                    indicator.style.transition = 'all 0.6s ease-out';
                    
                    setTimeout(() => {
                        indicator.style.opacity = '1';
                        indicator.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
        });
        </script>
        
<!-- ===== FORMULAIRE MENUISERIE ===== -->
        <div class="form-section fade-in-up">
            <h2>
                <i class="fas fa-<?= $element_a_modifier ? 'edit' : 'plus-circle' ?>"></i>
                <?= $element_a_modifier ? 'Modifier l\'élément menuiserie' : 'Ajouter un élément menuiserie' ?>
            </h2>

            <!-- Suggestions Menuiserie -->
            <div class="suggestions-menuiserie">
                <h4>
                    <i class="fas fa-hammer"></i>
                    Suggestions Menuiserie & Bois
                    <small>(Cliquez pour remplir automatiquement)</small>
                </h4>
                <div class="suggestions-grid">
                    <?php foreach ($suggestions_menuiserie as $suggestion): ?>
                        <div class="suggestion-item" onclick="remplirSuggestion('<?= htmlspecialchars($suggestion, ENT_QUOTES) ?>')">
                            <?= htmlspecialchars($suggestion) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Calculateur Menuiserie -->
            <div class="calculator-section">
                <h4>
                    <i class="fas fa-calculator"></i>
                    Calculateur Menuiserie & Métrage
                </h4>
                <div class="calc-grid">
                    <input type="number" id="calc_largeur" placeholder="Largeur (cm)" class="calc-input" step="0.1" max="500">
                    <input type="number" id="calc_hauteur" placeholder="Hauteur (cm)" class="calc-input" step="0.1" max="500">
                    <input type="number" id="calc_epaisseur" placeholder="Épaisseur (mm)" class="calc-input" step="1" max="200">
                    <button type="button" class="btn btn-sm btn-info" onclick="calculerSurface()">
                        <i class="fas fa-expand-arrows-alt"></i> Surface
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="calculerVolume()">
                        <i class="fas fa-cube"></i> Volume
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="calculerMetrage()">
                        <i class="fas fa-ruler"></i> Métrage
                    </button>
                </div>
            </div>

            <form method="POST" action="" id="formMenuiserie">
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
                               placeholder="Ex: Porte isoplane 73x204cm épaisseur 40mm"
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
                               placeholder="Ex: 3"
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
                            <option value="m²" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm²') ? 'selected' : '' ?>>Mètre carré (m²)</option>
                            <option value="ml" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'ml') ? 'selected' : '' ?>>Mètre linéaire (ml)</option>
                            <option value="m³" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm³') ? 'selected' : '' ?>>Mètre cube (m³)</option>
                            <option value="ensemble" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'ensemble') ? 'selected' : '' ?>>Ensemble</option>
                            <option value="kit" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'kit') ? 'selected' : '' ?>>Kit</option>
                            <option value="forfait" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'forfait') ? 'selected' : '' ?>>Forfait</option>
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
                               placeholder="Ex: 85000"
                               step="0.01"
                               min="0"
                               required>
                    </div>
                </div>

                <!-- Ligne 2 : Spécifications techniques -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="dimensions">
                            <i class="fas fa-expand-arrows-alt"></i>
                            Dimensions
                        </label>
                        <input type="text" 
                               id="dimensions" 
                               name="dimensions" 
                               value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['dimensions']) : '' ?>"
                               placeholder="Ex: 73x204cm, 120x80x40mm"
                               pattern="^\d+x\d+(?:x\d+)?(?:cm|mm)?$"
                               title="Format: 73x204cm, 120x80x40mm">
                    </div>

                    <div class="form-group">
                        <label for="epaisseur">
                            <i class="fas fa-arrows-alt-v"></i>
                            Épaisseur (mm)
                        </label>
                        <input type="number" 
                               id="epaisseur" 
                               name="epaisseur" 
                               value="<?= $element_a_modifier ? $element_a_modifier['epaisseur'] : '' ?>"
                               placeholder="Ex: 40"
                               step="1"
                               min="0"
                               max="200">
                    </div>

                    <div class="form-group">
                        <label for="essence_bois">
                            <i class="fas fa-tree"></i>
                            Essence de bois
                        </label>
                        <select id="essence_bois" name="essence_bois">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Chêne" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Chêne') ? 'selected' : '' ?>>Chêne</option>
                            <option value="Hêtre" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Hêtre') ? 'selected' : '' ?>>Hêtre</option>
                            <option value="Frêne" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Frêne') ? 'selected' : '' ?>>Frêne</option>
                            <option value="Érable" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Érable') ? 'selected' : '' ?>>Érable</option>
                            <option value="Noyer" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Noyer') ? 'selected' : '' ?>>Noyer</option>
                            <option value="Merisier" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Merisier') ? 'selected' : '' ?>>Merisier</option>
                            <option value="Pin" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Pin') ? 'selected' : '' ?>>Pin</option>
                            <option value="Sapin" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Sapin') ? 'selected' : '' ?>>Sapin</option>
                            <option value="Épicéa" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Épicéa') ? 'selected' : '' ?>>Épicéa</option>
                            <option value="Bois exotique" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Bois exotique') ? 'selected' : '' ?>>Bois exotique</option>
                            <option value="Teck" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Teck') ? 'selected' : '' ?>>Teck</option>
                            <option value="IPE" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'IPE') ? 'selected' : '' ?>>IPE</option>
                            <option value="Bambou" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Bambou') ? 'selected' : '' ?>>Bambou</option>
                            <option value="Contreplaqué" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Contreplaqué') ? 'selected' : '' ?>>Contreplaqué</option>
                            <option value="MDF" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'MDF') ? 'selected' : '' ?>>MDF</option>
                            <option value="Aggloméré" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Aggloméré') ? 'selected' : '' ?>>Aggloméré</option>
                            <option value="Mélaminé" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Mélaminé') ? 'selected' : '' ?>>Mélaminé</option>
                            <option value="Stratifié" <?= ($element_a_modifier && $element_a_modifier['essence_bois'] === 'Stratifié') ? 'selected' : '' ?>>Stratifié</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="finition">
                            <i class="fas fa-paint-brush"></i>
                            Finition
                        </label>
                        <select id="finition" name="finition">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Brut" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Brut') ? 'selected' : '' ?>>Brut</option>
                            <option value="Poncé" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Poncé') ? 'selected' : '' ?>>Poncé</option>
                            <option value="Lasuré" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Lasuré') ? 'selected' : '' ?>>Lasuré</option>
                            <option value="Verni" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Verni') ? 'selected' : '' ?>>Verni</option>
                            <option value="Vitrifié" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Vitrifié') ? 'selected' : '' ?>>Vitrifié</option>
                            <option value="Huilé" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Huilé') ? 'selected' : '' ?>>Huilé</option>
                            <option value="Ciré" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Ciré') ? 'selected' : '' ?>>Ciré</option>
                            <option value="Peint" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Peint') ? 'selected' : '' ?>>Peint</option>
                            <option value="Laqué" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Laqué') ? 'selected' : '' ?>>Laqué</option>
                            <option value="Teinté" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Teinté') ? 'selected' : '' ?>>Teinté</option>
                            <option value="Thermolaqué" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Thermolaqué') ? 'selected' : '' ?>>Thermolaqué</option>
                            <option value="Anodisé" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Anodisé') ? 'selected' : '' ?>>Anodisé (alu)</option>
                        </select>
                    </div>
                </div>

                <!-- Ligne 3 : Pose et usage -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="type_pose">
                            <i class="fas fa-tools"></i>
                            Type de pose
                        </label>
                        <select id="type_pose" name="type_pose">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Neuf" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Neuf') ? 'selected' : '' ?>>Neuf</option>
                            <option value="Rénovation" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Rénovation') ? 'selected' : '' ?>>Rénovation</option>
                            <option value="Applique" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Applique') ? 'selected' : '' ?>>Applique</option>
                            <option value="Encastré" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Encastré') ? 'selected' : '' ?>>Encastré</option>
                            <option value="Suspendu" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Suspendu') ? 'selected' : '' ?>>Suspendu</option>
                            <option value="Scellé" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Scellé') ? 'selected' : '' ?>>Scellé</option>
                            <option value="Vissé" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Vissé') ? 'selected' : '' ?>>Vissé</option>
                            <option value="Collé" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Collé') ? 'selected' : '' ?>>Collé</option>
                            <option value="Cloué" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Cloué') ? 'selected' : '' ?>>Cloué</option>
                            <option value="Flottant" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Flottant') ? 'selected' : '' ?>>Flottant</option>
                            <option value="Sur rails" <?= ($element_a_modifier && $element_a_modifier['type_pose'] === 'Sur rails') ? 'selected' : '' ?>>Sur rails</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="usage">
                            <i class="fas fa-home"></i>
                            Usage
                        </label>
                        <select id="usage" name="usage">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Porte intérieure" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Porte intérieure') ? 'selected' : '' ?>>Porte intérieure</option>
                            <option value="Porte d'entrée" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Porte d\'entrée') ? 'selected' : '' ?>>Porte d'entrée</option>
                            <option value="Fenêtre" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Fenêtre') ? 'selected' : '' ?>>Fenêtre</option>
                            <option value="Volet" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Volet') ? 'selected' : '' ?>>Volet</option>
                            <option value="Cloison" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Cloison') ? 'selected' : '' ?>>Cloison</option>
                            <option value="Escalier" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Escalier') ? 'selected' : '' ?>>Escalier</option>
                            <option value="Parquet" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Parquet') ? 'selected' : '' ?>>Parquet</option>
                            <option value="Terrasse" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Terrasse') ? 'selected' : '' ?>>Terrasse</option>
                            <option value="Aménagement" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Aménagement') ? 'selected' : '' ?>>Aménagement</option>
                            <option value="Placard" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Placard') ? 'selected' : '' ?>>Placard</option>
                            <option value="Cuisine" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Cuisine') ? 'selected' : '' ?>>Cuisine</option>
                            <option value="Salle de bain" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Salle de bain') ? 'selected' : '' ?>>Salle de bain</option>
                            <option value="Extérieur" <?= ($element_a_modifier && $element_a_modifier['usage'] === 'Extérieur') ? 'selected' : '' ?>>Extérieur</option>
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
                                    onclick="calculerSurfaceElement()"
                                    title="Alt+S">
                                <i class="fas fa-expand-arrows-alt"></i>
                                Surface
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-warning" 
                                    onclick="estimerPrixMenuiserie()"
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
                            <i class="fas fa-tree"></i> Sélectionnez une essence pour voir les caractéristiques
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
                            <a href="menuiserie.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-secondary ml-2">
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

        <!-- ===== TABLEAU DES ÉLÉMENTS MENUISERIE ===== -->
        <div class="table-container fade-in-up">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list"></i>
                    Éléments menuiserie
                    <span class="badge-essence ml-2"><?= count($elements_menuiserie) ?> élément(s)</span>
                </h3>
                <div class="table-actions">
                    <span class="total-amount">
                        <i class="fas fa-hammer"></i>
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
                            <th><i class="fas fa-expand-arrows-alt"></i> Dimensions</th>
                            <th><i class="fas fa-arrows-alt-v"></i> Épaisseur</th>
                            <th><i class="fas fa-tree"></i> Essence</th>
                            <th><i class="fas fa-paint-brush"></i> Finition</th>
                            <th><i class="fas fa-tools"></i> Pose</th>
                            <th><i class="fas fa-home"></i> Usage</th>
                            <th><i class="fas fa-euro-sign"></i> Total</th>
                            <th><i class="fas fa-calendar"></i> Créé le</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($elements_menuiserie)): ?>
                            <tr>
                                <td colspan="14" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-hammer fa-3x mb-3 d-block"></i>
                                        <p>Aucun élément menuiserie ajouté pour ce devis.</p>
                                        <small>Utilisez le formulaire ci-dessus pour ajouter des éléments menuiserie.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($elements_menuiserie as $element): ?>
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
                                    <td><span class="badge-dimensions"><?= htmlspecialchars($element['unite']) ?></span></td>
                                    <td><strong><?= number_format($element['prix_unitaire'], 0, ',', ' ') ?></strong> FCFA</td>
                                    <td>
                                        <?php if (!empty($element['dimensions'])): ?>
                                            <span class="badge-dimensions">
                                                <i class="fas fa-expand-arrows-alt"></i>
                                                <?= htmlspecialchars($element['dimensions']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['epaisseur']) && $element['epaisseur'] > 0): ?>
                                            <span class="badge-dimensions">
                                                <i class="fas fa-arrows-alt-v"></i>
                                                <?= number_format($element['epaisseur'], 0) ?>mm
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['essence_bois'])): ?>
                                            <span class="badge-essence">
                                                <i class="fas fa-tree"></i>
                                                <?= htmlspecialchars($element['essence_bois']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['finition'])): ?>
                                            <span class="badge-finition">
                                                <i class="fas fa-paint-brush"></i>
                                                <?= htmlspecialchars($element['finition']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['type_pose'])): ?>
                                            <span class="badge-pose">
                                                <i class="fas fa-tools"></i>
                                                <?= htmlspecialchars($element['type_pose']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['usage'])): ?>
                                            <span class="badge-usage">
                                                <i class="fas fa-home"></i>
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
                                            <a href="menuiserie.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&action=modifier&element_id=<?= $element['id'] ?>" 
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
                        <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=menuiserie" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-clock"></i> Voir tout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TOTAUX MODULE MENUISERIE ===== -->
        <div class="module-summary fade-in-up">
            <h3>
                <i class="fas fa-hammer"></i>
                Total Module Menuiserie
            </h3>
            <div class="total-amount pulse-animation">
                <?= number_format($total_module, 0, ',', ' ') ?> FCFA
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Mis à jour automatiquement • <?= count($elements_menuiserie) ?> élément(s)
                <?php if ($total_module > 0 && count($elements_menuiserie) > 0): ?>
                    • Moyenne: <?= number_format($total_module / count($elements_menuiserie), 0, ',', ' ') ?> FCFA/élément
                <?php endif; ?>
            </small>
        </div>

    </div>

    <!-- ===== JAVASCRIPT SPÉCIALISÉ MENUISERIE ===== -->
    <script>
        // ===== CONFIGURATION ET VARIABLES MENUISERIE =====
        const PRIX_MENUISERIE = {
            // Portes (prix unitaire)
            'porte_isoplane': { base: 85000, factor: 1.0 },
            'porte_postformee': { base: 125000, factor: 1.5 },
            'porte_massive': { base: 185000, factor: 2.2 },
            'porte_entree_bois': { base: 350000, factor: 4.1 },
            'porte_entree_pvc': { base: 285000, factor: 3.4 },
            'porte_entree_alu': { base: 450000, factor: 5.3 },
            'porte_coulissante': { base: 195000, factor: 2.3 },
            
            // Fenêtres (prix par m²)
            'fenetre_pvc': { base: 65000, factor: 1.0 },
            'fenetre_bois': { base: 95000, factor: 1.5 },
            'fenetre_alu': { base: 85000, factor: 1.3 },
            'fenetre_mixte': { base: 125000, factor: 1.9 },
            'velux': { base: 145000, factor: 2.2 },
            
            // Volets (prix par m²)
            'volet_bois': { base: 85000, factor: 1.0 },
            'volet_pvc': { base: 65000, factor: 0.8 },
            'volet_alu': { base: 95000, factor: 1.1 },
            'volet_roulant': { base: 125000, factor: 1.5 },
            
            // Parquets (prix par m²)
            'parquet_massif': { base: 65000, factor: 1.0 },
            'parquet_contrecolle': { base: 45000, factor: 0.7 },
            'parquet_stratifie': { base: 25000, factor: 0.4 },
            'parquet_bambou': { base: 55000, factor: 0.8 },
            
            // Escaliers (prix unitaire)
            'escalier_droit': { base: 850000, factor: 1.0 },
            'escalier_quart_tournant': { base: 1250000, factor: 1.5 },
            'escalier_helicoidal': { base: 1850000, factor: 2.2 },
            
            // Aménagements (prix par ml ou m²)
            'placard_melamine': { base: 35000, factor: 1.0 },
            'placard_bois': { base: 65000, factor: 1.9 },
            'bibliotheque': { base: 45000, factor: 1.3 },
            'dressing': { base: 55000, factor: 1.6 }
        };

        const ESSENCES_INFO = {
            'Chêne': {
                description: "Bois dur noble, très résistant et durable",
                densite: "0.65-0.75 kg/dm³",
                durete: "Très dur",
                usage: "Parquet, menuiserie haut de gamme, escaliers",
                prix_coeff: 2.5,
                durabilite: "100+ ans"
            },
            'Hêtre': {
                description: "Bois dur clair, facile à travailler",
                densite: "0.65-0.70 kg/dm³",
                durete: "Dur",
                usage: "Escaliers, plans de travail, jouets",
                prix_coeff: 1.8,
                durabilite: "50+ ans"
            },
            'Pin': {
                description: "Résineux tendre, économique",
                densite: "0.45-0.55 kg/dm³",
                durete: "Tendre",
                usage: "Menuiserie courante, lambris",
                prix_coeff: 1.0,
                durabilite: "30+ ans avec traitement"
            },
            'Sapin': {
                description: "Résineux blanc, léger",
                densite: "0.35-0.45 kg/dm³",
                durete: "Tendre",
                usage: "Charpente, menuiserie peinte",
                prix_coeff: 0.8,
                durabilite: "25+ ans avec traitement"
            },
            'Teck': {
                description: "Bois exotique imputrescible",
                densite: "0.60-0.70 kg/dm³",
                durete: "Dur",
                usage: "Extérieur, salle de bain, terrasse",
                prix_coeff: 4.5,
                durabilite: "50+ ans sans traitement"
            },
            'MDF': {
                description: "Panneau de fibres dense et homogène",
                densite: "0.65-0.85 kg/dm³",
                durete: "Moyen",
                usage: "Mobilier, moulures, peinture",
                prix_coeff: 0.6,
                durabilite: "20+ ans en intérieur"
            }
        };

        const DIMENSIONS_STANDARD = {
            // Portes standard (en cm)
            'portes': {
                'interieur': ['63x204', '73x204', '83x204', '93x204'],
                'entree': ['80x215', '90x215', '100x215'],
                'service': ['80x200', '90x200']
            },
            // Fenêtres standard (en cm)
            'fenetres': {
                'simple': ['60x80', '80x100', '100x120'],
                'double': ['120x100', '140x120', '160x120', '180x120'],
                'baie': ['240x215', '300x215', '360x215']
            },
            // Parquets (largeurs en mm)
            'parquets': {
                'massif': [70, 90, 120, 140],
                'contrecolle': [125, 139, 180, 220],
                'stratifie': [193, 214, 240]
            }
        };

        // ===== FONCTIONS SPÉCIALISÉES MENUISERIE =====

        /**
         * Calculer la surface d'un élément
         */
        function calculerSurface() {
            const largeur = parseFloat(document.getElementById('calc_largeur').value) || 0;
            const hauteur = parseFloat(document.getElementById('calc_hauteur').value) || 0;
            
            if (largeur > 0 && hauteur > 0) {
                const surface = (largeur * hauteur) / 10000; // cm² vers m²
                
                document.getElementById('quantite').value = surface.toFixed(2);
                document.getElementById('unite').value = 'm²';
                document.getElementById('dimensions').value = `${largeur}x${hauteur}cm`;
                
                showToast(`📐 Surface calculée: ${surface.toFixed(2)} m²\n` +
                         `Dimensions: ${largeur}cm × ${hauteur}cm\n` +
                         `Périmètre: ${((largeur + hauteur) * 2).toFixed(0)}cm`, 'success');
            } else {
                showToast('⚠️ Veuillez saisir largeur et hauteur.', 'warning');
            }
        }

        /**
         * Calculer le volume de bois
         */
        function calculerVolume() {
            const largeur = parseFloat(document.getElementById('calc_largeur').value) || 0;
            const hauteur = parseFloat(document.getElementById('calc_hauteur').value) || 0;
            const epaisseur = parseFloat(document.getElementById('calc_epaisseur').value) || 0;
            
            if (largeur > 0 && hauteur > 0 && epaisseur > 0) {
                const volume = (largeur * hauteur * epaisseur) / 1000000; // cm³ vers m³
                const surface = (largeur * hauteur) / 10000; // cm² vers m²
                
                // Estimation poids selon essence
                const essence = document.getElementById('essence_bois').value;
                const densite = essence && ESSENCES_INFO[essence] ? 
                    parseFloat(ESSENCES_INFO[essence].densite.split('-')[0]) : 0.6;
                const poids = volume * densite * 1000; // kg
                
                showToast(`📦 Volume calculé: ${volume.toFixed(4)} m³\n` +
                         `Surface: ${surface.toFixed(2)} m²\n` +
                         `Épaisseur: ${epaisseur}mm\n` +
                         `Poids estimé: ${poids.toFixed(1)} kg\n` +
                         `Densité utilisée: ${densite} t/m³`, 'success');
                
                document.getElementById('quantite').value = volume.toFixed(4);
                document.getElementById('unite').value = 'm³';
            } else {
                showToast('⚠️ Veuillez saisir largeur, hauteur et épaisseur.', 'warning');
            }
        }

        /**
         * Calculer le métrage linéaire
         */
        function calculerMetrage() {
            const largeur = parseFloat(document.getElementById('calc_largeur').value) || 0;
            const hauteur = parseFloat(document.getElementById('calc_hauteur').value) || 0;
            
            if (largeur > 0 || hauteur > 0) {
                // Pour éléments linéaires (plinthes, baguettes, etc.)
                const metrage = Math.max(largeur, hauteur) / 100; // cm vers m
                
                document.getElementById('quantite').value = metrage.toFixed(2);
                document.getElementById('unite').value = 'ml';
                
                showToast(`📏 Métrage calculé: ${metrage.toFixed(2)} ml\n` +
                         `Dimension principale: ${Math.max(largeur, hauteur)}cm\n` +
                         `Usage: plinthes, baguettes, profilés`, 'success');
            } else {
                showToast('⚠️ Veuillez saisir au moins une dimension.', 'warning');
            }
        }

        /**
         * Calculer surface d'un élément spécifique
         */
        function calculerSurfaceElement() {
            const dimensions = document.getElementById('dimensions').value;
            
            if (dimensions) {
                const dimensionsMatch = dimensions.match(/(\d+)x(\d+)/);
                if (dimensionsMatch) {
                    const largeur = parseInt(dimensionsMatch[1]);
                    const hauteur = parseInt(dimensionsMatch[2]);
                    const surface = (largeur * hauteur) / 10000; // cm² vers m²
                    
                    if (document.getElementById('unite').value === 'unité') {
                        const quantiteActuelle = parseFloat(document.getElementById('quantite').value) || 1;
                        const surfaceTotale = surface * quantiteActuelle;
                        
                        showToast(`📐 Surface par unité: ${surface.toFixed(2)} m²\n` +
                                 `Quantité: ${quantiteActuelle}\n` +
                                 `Surface totale: ${surfaceTotale.toFixed(2)} m²\n` +
                                 `Dimensions: ${largeur}cm × ${hauteur}cm`, 'info');
                    } else {
                        document.getElementById('quantite').value = surface.toFixed(2);
                        document.getElementById('unite').value = 'm²';
                        showToast(`📐 Surface calculée: ${surface.toFixed(2)} m²`, 'success');
                    }
                }
            } else {
                showToast('⚠️ Veuillez d\'abord saisir les dimensions.', 'warning');
            }
        }

        /**
         * Estimation prix menuiserie
         */
        function estimerPrixMenuiserie() {
            const designation = document.getElementById('designation').value.toLowerCase();
            const quantite = parseFloat(document.getElementById('quantite').value) || 1;
            const unite = document.getElementById('unite').value;
            const essence = document.getElementById('essence_bois').value;
            const dimensions = document.getElementById('dimensions').value;
            
            let prixUnitaire = 0;
            let typeDetecte = '';
            
            // Recherche par type d'élément
            Object.keys(PRIX_MENUISERIE).forEach(key => {
                const keyWords = key.split('_');
                if (keyWords.some(word => designation.includes(word))) {
                    prixUnitaire = PRIX_MENUISERIE[key].base * PRIX_MENUISERIE[key].factor;
                    typeDetecte = key.replace(/_/g, ' ');
                }
            });
            
            // Ajustement selon essence
            if (essence && ESSENCES_INFO[essence] && prixUnitaire > 0) {
                prixUnitaire *= ESSENCES_INFO[essence].prix_coeff;
                typeDetecte += ` (${essence})`;
            }
            
            // Ajustement selon dimensions pour portes/fenêtres
            if (dimensions && (designation.includes('porte') || designation.includes('fenêtre'))) {
                const dimensionsMatch = dimensions.match(/(\d+)x(\d+)/);
                if (dimensionsMatch) {
                    const largeur = parseInt(dimensionsMatch[1]);
                    const hauteur = parseInt(dimensionsMatch[2]);
                    const surface = (largeur * hauteur) / 10000;
                    
                    // Facteur taille (grande taille = plus cher au m²)
                    if (surface > 2.5) prixUnitaire *= 1.3;
                    else if (surface > 2.0) prixUnitaire *= 1.2;
                    else if (surface > 1.5) prixUnitaire *= 1.1;
                }
            }
            
            // Estimation par défaut selon type
            if (prixUnitaire === 0) {
                if (designation.includes('porte')) {
                    prixUnitaire = 125000;
                    typeDetecte = 'Porte standard';
                } else if (designation.includes('fenêtre')) {
                    prixUnitaire = unite === 'm²' ? 75000 : 185000;
                    typeDetecte = 'Fenêtre standard';
                } else if (designation.includes('parquet')) {
                    prixUnitaire = 45000;
                    typeDetecte = 'Parquet standard';
                } else if (designation.includes('escalier')) {
                    prixUnitaire = 950000;
                    typeDetecte = 'Escalier standard';
                } else if (designation.includes('placard') || designation.includes('dressing')) {
                    prixUnitaire = 45000;
                    typeDetecte = 'Aménagement standard';
                } else if (designation.includes('terrasse')) {
                    prixUnitaire = 85000;
                    typeDetecte = 'Terrasse bois';
                } else {
                    prixUnitaire = 35000;
                    typeDetecte = 'Menuiserie standard';
                }
            }
            
            if (prixUnitaire > 0) {
                document.getElementById('prix_unitaire').value = Math.round(prixUnitaire);
                
                const total = prixUnitaire * quantite;
                showToast(`💰 Prix estimé: ${prixUnitaire.toLocaleString()} FCFA/${unite}\n` +
                         `🎯 Type détecté: ${typeDetecte}\n` +
                         `📊 Total: ${total.toLocaleString()} FCFA`, 'info');
                
                // Animation
                const prixField = document.getElementById('prix_unitaire');
                prixField.style.background = 'linear-gradient(135deg, #f3e5f5 0%, #ffffff 100%)';
                setTimeout(() => prixField.style.background = '', 1500);
            } else {
                showToast('❓ Type de menuiserie non reconnu.\nVeuillez saisir le prix manuellement.', 'warning');
            }
        }

        /**
         * Mettre à jour les informations essence
         */
        function updateInfoEssence() {
            const essence = document.getElementById('essence_bois').value;
            const infoDiv = document.getElementById('info-essence');
            
            if (essence && ESSENCES_INFO[essence]) {
                const info = ESSENCES_INFO[essence];
                infoDiv.innerHTML = `
                    <strong>${essence}</strong><br>
                    <small><i class="fas fa-info-circle"></i> ${info.description}</small><br>
                    <small><i class="fas fa-weight"></i> Densité: ${info.densite}</small><br>
                    <small><i class="fas fa-hammer"></i> Dureté: ${info.durete}</small><br>
                    <small><i class="fas fa-tools"></i> Usage: ${info.usage}</small><br>
                    <small><i class="fas fa-euro-sign"></i> Coeff. prix: ×${info.prix_coeff}</small><br>
                    <small><i class="fas fa-clock"></i> Durabilité: ${info.durabilite}</small>
                `;
            } else {
                infoDiv.innerHTML = '<i class="fas fa-tree"></i> Sélectionnez une essence pour voir les caractéristiques';
            }
        }

        /**
         * Suggérer dimensions standards
         */
        function suggererDimensions() {
            const designation = document.getElementById('designation').value.toLowerCase();
            const dimensionsField = document.getElementById('dimensions');
            
            if (!dimensionsField.value) {
                let suggestions = [];
                
                if (designation.includes('porte') && designation.includes('intérieur')) {
                    suggestions = DIMENSIONS_STANDARD.portes.interieur;
                } else if (designation.includes('porte') && (designation.includes('entrée') || designation.includes('entree'))) {
                    suggestions = DIMENSIONS_STANDARD.portes.entree;
                } else if (designation.includes('fenêtre') || designation.includes('fenetre')) {
                    if (designation.includes('baie')) {
                        suggestions = DIMENSIONS_STANDARD.fenetres.baie;
                    } else if (designation.includes('2 vantaux') || designation.includes('double')) {
                        suggestions = DIMENSIONS_STANDARD.fenetres.double;
                    } else {
                        suggestions = DIMENSIONS_STANDARD.fenetres.simple;
                    }
                }
                
                if (suggestions.length > 0) {
                    // Prendre une dimension moyenne
                    const dimensionSuggérée = suggestions[Math.floor(suggestions.length / 2)];
                    dimensionsField.value = dimensionSuggérée + 'cm';
                    
                    showToast(`💡 Dimension suggérée: ${dimensionSuggérée}cm\n` +
                             `Autres options: ${suggestions.join('cm, ')}cm`, 'info');
                }
            }
        }

        /**
         * Calculer besoins accessoires
         */
        function calculerAccessoires() {
            const elements = <?= json_encode($elements_menuiserie) ?>;
            
            if (elements.length === 0) {
                showToast('📋 Aucun élément pour calculer les accessoires', 'warning');
                return;
            }
            
            let besoins = {
                serrures: 0,
                poignees: 0,
                paumelles: 0,
                visserie: 0,
                lasure: 0,
                vernis: 0
            };
            
            let details = [];
            
            elements.forEach(element => {
                const designation = element.designation.toLowerCase();
                const quantite = parseFloat(element.quantite) || 0;
                
                if (designation.includes('porte')) {
                    if (designation.includes('entrée') || designation.includes('entree')) {
                        besoins.serrures += quantite; // Serrure 3 points
                        besoins.poignees += quantite;
                        besoins.paumelles += quantite * 3; // 3 paumelles par porte
                        details.push(`${quantite} porte(s) d'entrée → ${quantite} serrure(s) 3 points`);
                    } else {
                        besoins.serrures += quantite; // Serrure bec de cane
                        besoins.poignees += quantite * 2; // 2 poignées par porte
                        besoins.paumelles += quantite * 2; // 2 paumelles par porte
                        details.push(`${quantite} porte(s) intérieure(s) → ${quantite} serrure(s) simple`);
                    }
                }
                
                if (designation.includes('fenêtre') || designation.includes('fenetre')) {
                    besoins.poignees += quantite * 2; // Poignées fenêtre
                    details.push(`${quantite} fenêtre(s) → ${quantite * 2} poignée(s)`);
                }
                
                if (designation.includes('parquet')) {
                    const surface = element.unite === 'm²' ? quantite : quantite * 2; // Estimation
                    besoins.vernis += surface * 0.1; // 0.1L par m²
                    details.push(`${surface.toFixed(1)}m² parquet → ${(surface * 0.1).toFixed(1)}L vernis`);
                }
                
                if (designation.includes('terrasse') || designation.includes('extérieur')) {
                    const surface = element.unite === 'm²' ? quantite : quantite * 2;
                    besoins.lasure += surface * 0.12; // 0.12L par m²
                    details.push(`${surface.toFixed(1)}m² extérieur → ${(surface * 0.12).toFixed(1)}L lasure`);
                }
            });
            
            // Estimation visserie (10% du nombre d'éléments en kg)
            besoins.visserie = Math.ceil(elements.length * 0.5);
            
            let resultat = `🔧 BESOINS ACCESSOIRES ESTIMÉS:\n\n`;
            
            if (besoins.serrures > 0) {
                resultat += `🔐 Serrures: ${besoins.serrures}\n`;
            }
            if (besoins.poignees > 0) {
                resultat += `🚪 Poignées: ${besoins.poignees}\n`;
            }
            if (besoins.paumelles > 0) {
                resultat += `🔗 Paumelles: ${besoins.paumelles}\n`;
            }
            if (besoins.visserie > 0) {
                resultat += `🔩 Visserie: ${besoins.visserie} kg\n`;
            }
            if (besoins.vernis > 0) {
                resultat += `🎨 Vernis: ${besoins.vernis.toFixed(1)} L\n`;
            }
            if (besoins.lasure > 0) {
                resultat += `🏠 Lasure: ${besoins.lasure.toFixed(1)} L\n`;
            }
            
            resultat += `\n📋 Détail calculs:\n${details.join('\n')}`;
            
            console.log(resultat);
            showToast('🔧 Besoins accessoires calculés - voir console', 'info');
        }

        /**
         * Remplir suggestion menuiserie
         */
        function remplirSuggestion(suggestion) {
            const designationField = document.getElementById('designation');
            const quantiteField = document.getElementById('quantite');
            const uniteField = document.getElementById('unite');
            const prixField = document.getElementById('prix_unitaire');
            const dimensionsField = document.getElementById('dimensions');
            const epaisseurField = document.getElementById('epaisseur');
            const essenceField = document.getElementById('essence_bois');
            const finitionField = document.getElementById('finition');
            const poseField = document.getElementById('type_pose');
            const usageField = document.getElementById('usage');
            
            designationField.value = suggestion;
            
            const sug = suggestion.toLowerCase();
            
            // Extraire dimensions
            const dimMatch = sug.match(/(\d+)x(\d+)(?:x(\d+))?cm/);
            if (dimMatch) {
                if (dimMatch[3]) {
                    dimensionsField.value = `${dimMatch[1]}x${dimMatch[2]}x${dimMatch[3]}cm`;
                } else {
                    dimensionsField.value = `${dimMatch[1]}x${dimMatch[2]}cm`;
                }
            }
            
            // Extraire épaisseur
            const epaisMatch = sug.match(/épaisseur\s*(\d+)mm/);
            if (epaisMatch) {
                epaisseurField.value = epaisMatch[1];
            }
            
            // Déterminer essence
            if (sug.includes('chêne')) {
                essenceField.value = 'Chêne';
            } else if (sug.includes('hêtre')) {
                essenceField.value = 'Hêtre';
            } else if (sug.includes('pin')) {
                essenceField.value = 'Pin';
            } else if (sug.includes('sapin')) {
                essenceField.value = 'Sapin';
            } else if (sug.includes('exotique')) {
                essenceField.value = 'Bois exotique';
            } else if (sug.includes('teck')) {
                essenceField.value = 'Teck';
            } else if (sug.includes('ipe')) {
                essenceField.value = 'IPE';
            } else if (sug.includes('mélaminé')) {
                essenceField.value = 'Mélaminé';
            } else if (sug.includes('stratifié')) {
                essenceField.value = 'Stratifié';
            } else if (sug.includes('mdf')) {
                essenceField.value = 'MDF';
            }
            
            // Déterminer finition
            if (sug.includes('laqué') || sug.includes('laque')) {
                finitionField.value = 'Laqué';
            } else if (sug.includes('verni')) {
                finitionField.value = 'Verni';
            } else if (sug.includes('lasuré')) {
                finitionField.value = 'Lasuré';
            } else if (sug.includes('huilé')) {
                finitionField.value = 'Huilé';
            } else if (sug.includes('brut')) {
                finitionField.value = 'Brut';
            }
            
            // Déterminer usage et unité
            if (sug.includes('porte')) {
                quantiteField.value = '1';
                uniteField.value = 'unité';
                if (sug.includes('entrée') || sug.includes('entree')) {
                    usageField.value = 'Porte d\'entrée';
                } else {
                    usageField.value = 'Porte intérieure';
                }
                poseField.value = 'Neuf';
            } else if (sug.includes('fenêtre') || sug.includes('fenetre')) {
                quantiteField.value = '1';
                uniteField.value = 'unité';
                usageField.value = 'Fenêtre';
                poseField.value = 'Neuf';
            } else if (sug.includes('volet')) {
                quantiteField.value = '1';
                uniteField.value = 'unité';
                usageField.value = 'Volet';
                poseField.value = 'Applique';
            } else if (sug.includes('parquet')) {
                quantiteField.value = '25';
                uniteField.value = 'm²';
                usageField.value = 'Parquet';
                poseField.value = 'Flottant';
            } else if (sug.includes('terrasse')) {
                quantiteField.value = '20';
                uniteField.value = 'm²';
                usageField.value = 'Terrasse';
                poseField.value = 'Vissé';
            } else if (sug.includes('escalier')) {
                quantiteField.value = '1';
                uniteField.value = 'ensemble';
                usageField.value = 'Escalier';
                poseField.value = 'Neuf';
            } else if (sug.includes('placard') || sug.includes('dressing')) {
                quantiteField.value = '3';
                uniteField.value = 'ml';
                usageField.value = 'Placard';
                poseField.value = 'Neuf';
            } else if (sug.includes('cloison')) {
                quantiteField.value = '10';
                uniteField.value = 'm²';
                usageField.value = 'Cloison';
                poseField.value = 'Neuf';
            } else {
                quantiteField.value = '1';
                uniteField.value = 'unité';
                usageField.value = 'Aménagement';
                poseField.value = 'Neuf';
            }
            
            // Suggérer dimensions standards si pas déjà rempli
            suggererDimensions();
            
            // Mettre à jour les infos essence
            updateInfoEssence();
            
            // Estimation prix
            estimerPrixMenuiserie();
            
            // Animation
            designationField.style.background = 'linear-gradient(135deg, #f3e5f5 0%, #ffffff 100%)';
            setTimeout(() => designationField.style.background = '', 1000);
            
            quantiteField.focus();
            quantiteField.select();
        }

        /**
         * Réinitialiser le formulaire
         */
        function resetFormulaire() {
            if (confirm('🗑️ Êtes-vous sûr de vouloir effacer tous les champs du formulaire ?')) {
                document.getElementById('formMenuiserie').reset();
                document.getElementById('unite').value = 'unité';
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
                       `🔨 Élément: ${designation}\n` +
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
         * Raccourcis clavier menuiserie
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
                    calculerSurfaceElement();
                }
                
                // Alt + V = Calculer volume
                if (e.altKey && e.key === 'v') {
                    e.preventDefault();
                    calculerVolume();
                }
                
                // Alt + M = Calculer métrage
                if (e.altKey && e.key === 'm') {
                    e.preventDefault();
                    calculerMetrage();
                }
                
                // Alt + E = Estimation prix
                if (e.altKey && e.key === 'e') {
                    e.preventDefault();
                    estimerPrixMenuiserie();
                }
                
                // Alt + A = Calculer accessoires
                if (e.altKey && e.key === 'a') {
                    e.preventDefault();
                    calculerAccessoires();
                }
                
                // Alt + B = Focus essence bois
                if (e.altKey && e.key === 'b') {
                    e.preventDefault();
                    document.getElementById('essence_bois').focus();
                    showToast('🌳 Focus sur Essence', 'info');
                }
                
                // Alt + F = Focus finition
                if (e.altKey && e.key === 'f') {
                    e.preventDefault();
                    document.getElementById('finition').focus();
                    showToast('🎨 Focus sur Finition', 'info');
                }
                
                // Alt + U = Focus usage
                if (e.altKey && e.key === 'u') {
                    e.preventDefault();
                    document.getElementById('usage').focus();
                    showToast('🏠 Focus sur Usage', 'info');
                }
                
                // Ctrl + Entrée = Soumettre formulaire
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('formMenuiserie').submit();
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
                background: ${type === 'info' ? 'var(--accent-blue)' : type === 'warning' ? 'var(--wood-brown)' : type === 'success' ? 'var(--woodwork-primary)' : 'var(--accent-red)'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-medium);
                z-index: 9999;
                max-width: 400px;
                white-space: pre-line;
                animation: slideInRight 0.4s ease-out;
            `;
            
            const icon = type === 'info' ? '🔨' : type === 'warning' ? '⚠️' : type === 'success' ? '✅' : '❌';
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
            const dimensionsField = document.getElementById('dimensions');
            const epaisseurField = document.getElementById('epaisseur');
            const quantiteField = document.getElementById('quantite');
            const prixField = document.getElementById('prix_unitaire');
            const essenceField = document.getElementById('essence_bois');
            
            // Validation dimensions
            dimensionsField.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && !value.match(/^\d+x\d+(?:x\d+)?(?:cm|mm)?$/i)) {
                    this.style.borderColor = 'var(--accent-red)';
                    this.title = 'Format invalide. Exemples: 73x204cm, 120x80x40mm';
                } else {
                    this.style.borderColor = '';
                    this.title = '';
                }
            });
            
            // Validation épaisseur
            epaisseurField.addEventListener('input', function() {
                const value = parseFloat(this.value);
                if (value && (value < 0 || value > 200)) {
                    this.style.borderColor = 'var(--accent-red)';
                    this.title = 'Épaisseur doit être entre 0 et 200mm';
                } else {
                    this.style.borderColor = '';
                    this.title = '';
                }
            });
            
            // Mise à jour info essence
            essenceField.addEventListener('change', updateInfoEssence);
            
            // Suggestion dimensions automatique
            document.getElementById('designation').addEventListener('blur', suggererDimensions);
            
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
                            background: var(--woodwork-primary);
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
            console.log('🔨 Module Menuiserie GSN ProDevis360° initialisé');
            
            // Initialiser toutes les fonctionnalités
            initRaccourcisClavier();
            initAnimationsScroll();
            initValidationTempsReel();
            updateInfoEssence();
            
            // Afficher les raccourcis clavier
            showToast(`⌨️ Raccourcis disponibles:\n` +
                     `Alt+D = Désignation\n` +
                     `Alt+S = Calculer surface\n` +
                     `Alt+V = Volume\n` +
                     `Alt+M = Métrage\n` +
                     `Alt+E = Estimation prix\n` +
                     `Alt+A = Accessoires\n` +
                     `Alt+B = Essence bois\n` +
                     `Alt+F = Finition\n` +
                     `Alt+U = Usage\n` +
                     `Ctrl+Entrée = Envoyer`, 'info');
            
            // Focus automatique
            const firstField = document.getElementById('designation');
            if (firstField && !firstField.value) {
                setTimeout(() => firstField.focus(), 500);
            }
            
            // Analyse des éléments existants
            const elements = <?= json_encode($elements_menuiserie) ?>;
            let surfaceTotale = 0;
            let volumeTotal = 0;
            let alertesDimensions = 0;
            
            elements.forEach(element => {
                const quantite = parseFloat(element.quantite) || 0;
                const unite = element.unite || '';
                const dimensions = element.dimensions || '';
                
                if (unite === 'm²') {
                    surfaceTotale += quantite;
                } else if (unite === 'm³') {
                    volumeTotal += quantite;
                }
                
                // Vérifier cohérence dimensions/usage
                if (dimensions && element.usage) {
                    const dimMatch = dimensions.match(/(\d+)x(\d+)/);
                    if (dimMatch) {
                        const largeur = parseInt(dimMatch[1]);
                        const hauteur = parseInt(dimMatch[2]);
                        
                        // Alertes selon usage
                        if (element.usage.includes('Porte') && (largeur > 120 || hauteur > 250)) {
                            alertesDimensions++;
                        }
                        if (element.usage.includes('Fenêtre') && (largeur > 300 || hauteur > 300)) {
                            alertesDimensions++;
                        }
                    }
                }
            });
            
            if (alertesDimensions > 0) {
                setTimeout(() => {
                    showToast(`⚠️ ${alertesDimensions} élément(s) avec dimensions importantes détecté(s).\n` +
                             `Vérifiez la faisabilité et les coûts de transport`, 'warning');
                }, 2000);
            }
            
            if (surfaceTotale > 0 || volumeTotal > 0) {
                setTimeout(() => {
                    let recap = `📊 Récapitulatif menuiserie:\n`;
                    if (surfaceTotale > 0) recap += `Surface: ${surfaceTotale.toFixed(1)} m²\n`;
                    if (volumeTotal > 0) recap += `Volume: ${volumeTotal.toFixed(2)} m³\n`;
                    recap += `${elements.length} élément(s) menuiserie`;
                    showToast(recap, 'info');
                }, 3000);
            }
            
            // Ajouter bouton accessoires si beaucoup d'éléments
            if (elements.length > 3) {
                const accessoiresBtn = document.createElement('button');
                accessoiresBtn.className = 'btn btn-sm btn-warning ml-2';
                accessoiresBtn.innerHTML = '<i class="fas fa-wrench"></i> Accessoires';
                accessoiresBtn.onclick = calculerAccessoires;
                
                const tableActions = document.querySelector('.table-header .table-actions');
                if (tableActions) {
                    tableActions.appendChild(accessoiresBtn);
                }
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
