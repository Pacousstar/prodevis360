<?php
// ===== CARRELAGE.PHP - PARTIE 1 : PHP LOGIC & CONFIG =====
// VERSION UNIFORMISÉE GSN ProDevis360°
require_once 'functions.php';

// Configuration du module actuel
$current_module = 'carrelage';

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

// Suggestions spécialisées pour le carrelage
$suggestions_carrelage = [
    // CARREAUX DE SOL INTÉRIEUR
    'Carrelage grès cérame 30x30cm antidérapant',
    'Carrelage grès cérame 45x45cm poli brillant',
    'Carrelage grès cérame 60x60cm mat moderne',
    'Carrelage grès cérame 80x80cm grand format',
    'Carrelage grès cérame 120x20cm aspect parquet',
    'Carrelage grès cérame imitation bois 20x120cm',
    'Carrelage grès cérame imitation pierre 60x90cm',
    'Carrelage grès cérame effet béton 75x75cm',
    'Carrelage grès cérame marbre blanc 60x120cm',
    'Carrelage grès cérame noir mat 30x60cm',
    
    // CARREAUX DE MUR INTÉRIEUR
    'Faïence murale blanche brillante 20x25cm',
    'Faïence murale métro blanc 10x20cm',
    'Faïence murale colorée 20x20cm',
    'Faïence murale aspect pierre 25x40cm',
    'Faïence murale mosaïque 30x30cm',
    'Carrelage mural grès cérame 30x60cm',
    'Carrelage mural grand format 40x120cm',
    'Listel décoratif carrelage 5x25cm',
    'Baguette d\'angle carrelage PVC blanc',
    'Profil de finition carrelage inox',
    
    // CARREAUX EXTÉRIEUR
    'Carrelage extérieur antidérapant R11 30x30cm',
    'Carrelage terrasse grès cérame 60x60cm R10',
    'Carrelage piscine émaillé bleu 25x25cm',
    'Dalle béton carrossable 50x50cm épaisseur 6cm',
    'Pavé autobloquant béton gris 20x10x6cm',
    'Carrelage aspect pierre naturelle 40x40cm',
    'Carrelage travertin beige 40x60cm',
    'Carrelage ardoise noire 30x30cm',
    'Margelle piscine droite 30x60cm',
    'Margelle piscine angle 30x30cm',
    
    // MOSAÏQUES
    'Mosaïque verre blanc 30x30cm sur trame',
    'Mosaïque pierre naturelle beige 30x30cm',
    'Mosaïque émaux brillants multicolore 30x30cm',
    'Mosaïque inox brossé 30x30cm',
    'Mosaïque pâte de verre bleu 30x30cm',
    'Frise mosaïque décorative 5x30cm',
    'Cabochon mosaïque isolé 2x2cm',
    'Galets mosaïque naturels 30x30cm',
    'Mosaïque hexagonale marbre 30x30cm',
    'Mosaïque micro-carreaux 1x1cm 30x30cm',
    
    // COLLES ET MORTIERS
    'Colle carrelage poudre C2TE flex 25kg',
    'Colle carrelage pâte prête à l\'emploi 25kg',
    'Colle carrelage grands formats C2TE-S1 25kg',
    'Colle carrelage piscine étanche 20kg',
    'Colle carrelage extérieur gel-dégel 25kg',
    'Double encollage colle rapide 25kg',
    'Primaire d\'accrochage sols difficiles 5L',
    'Ragréage autolissant fibré 25kg',
    'Mortier de scellement rapide 25kg',
    'Adjuvant plastifiant colle 1L',
    
    // JOINTS ET FINITIONS
    'Joint carrelage époxy blanc 5kg',
    'Joint carrelage époxy gris 5kg',
    'Joint carrelage époxy noir 5kg',
    'Joint carrelage ciment blanc 5kg',
    'Joint carrelage ciment gris 5kg',
    'Joint carrelage ciment beige 5kg',
    'Joint silicone sanitaire blanc 310ml',
    'Joint silicone sanitaire transparent 310ml',
    'Mastic acrylique blanc peinture 310ml',
    'Nettoyant laitance ciment 1L',
    
    // PROFILÉS ET ACCESSOIRES
    'Profilé angle sortant PVC blanc 10mm',
    'Profilé angle sortant alu naturel 10mm',
    'Profilé angle sortant inox brossé 10mm',
    'Profilé jonction sol-mur PVC souple',
    'Baguette quart-de-rond PVC blanc 20mm',
    'Seuil de porte alu anodisé 30mm',
    'Nez de marche antidérapant alu 40mm',
    'Équerre de pose carrelage plastique',
    'Croisillons auto-nivelants 3mm sachet 250',
    'Croisillons classiques 2mm sachet 500',
    
    // ÉTANCHÉITÉ ET SOUS-COUCHE
    'Membrane étanchéité liquide 5kg',
    'Membrane étanchéité sous carrelage 10m²',
    'Bande étanchéité périphérique 5m',
    'Bande étanchéité angles 2.5m',
    'Siphon de sol étanche Ø90mm',
    'Évacuation douche italienne linéaire 80cm',
    'Natte désolidarisation 10m²',
    'Sous-couche isolante phonique 10m²',
    'Film polyane 150µ protection 50m²',
    'Treillis fibre de verre anti-fissures 25m²',
    
    // OUTILLAGE SPÉCIALISÉ
    'Carrelette manuelle coupe droite 60cm',
    'Carrelette électrique eau diamètre 200mm',
    'Disque diamant carrelage Ø125mm',
    'Disque diamant grès cérame Ø230mm',
    'Pince perroquet carrelage',
    'Tenaille carrelage bec courbe',
    'Râpe à joint carrelage grain moyen',
    'Éponge nettoyage joint alvéolée',
    'Maillet caoutchouc carreleur 350g',
    'Niveau à bulle aluminium 60cm',
    
    // PRODUITS ENTRETIEN
    'Nettoyant carrelage quotidien 1L',
    'Détartrant carrelage salle de bain 750ml',
    'Anti-taches carrelage poreux 500ml',
    'Rénovateur joints carrelage 500ml',
    'Décapant laitance ciment 1L',
    'Protection carrelage hydrofuge 1L',
    'Cire carrelage brillance 750ml',
    'Produit anti-glisse carrelage 1L',
    'Nettoyant terrasse extérieure 2L',
    'Imperméabilisant joints 500ml',
    
    // CARRELAGES SPÉCIAUX
    'Carrelage technique laboratoire 30x30cm',
    'Carrelage industriel antiacide 20x20cm',
    'Carrelage conducteur électricité 60x60cm',
    'Carrelage photoluminescent 30x30cm',
    'Carrelage chauffant électrique 60x60cm',
    'Carrelage antibactérien hôpital 30x30cm',
    'Carrelage ultra-fin 3mm 120x60cm',
    'Carrelage XXL 160x320cm épaisseur 6mm',
    'Carrelage tactile malvoyants 30x30cm',
    'Carrelage drainant extérieur 40x40cm'
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
            $type_carrelage = trim($_POST['type_carrelage'] ?? '');
            $format_carreau = trim($_POST['format_carreau'] ?? '');
            $epaisseur = floatval($_POST['epaisseur'] ?? 0);
            $couleur = trim($_POST['couleur'] ?? '');
            $finition = trim($_POST['finition'] ?? '');
            $classe_usage = trim($_POST['classe_usage'] ?? '');
            $antiderapant = trim($_POST['antiderapant'] ?? '');
            $resistance_gel = trim($_POST['resistance_gel'] ?? '');
            $absorption_eau = floatval($_POST['absorption_eau'] ?? 0);
            $pose_type = trim($_POST['pose_type'] ?? '');
            $surface_prevue = floatval($_POST['surface_prevue'] ?? 0);
            
            // Validations spécifiques carrelage
            if (empty($designation)) {
                throw new Exception("La désignation est obligatoire.");
            }
            if ($quantite <= 0) {
                throw new Exception("La quantité doit être supérieure à 0.");
            }
            if ($prix_unitaire < 0) {
                throw new Exception("Le prix unitaire ne peut pas être négatif.");
            }
            
            // Validation format carreau (format : LxLcm ou LxHcm)
            if (!empty($format_carreau) && !preg_match('/^\d+x\d+(cm|mm)?$/i', $format_carreau)) {
                throw new Exception("Format de carreau invalide (ex: 30x30cm, 60x120cm).");
            }
            
            // Validation épaisseur
            if ($epaisseur < 0 || $epaisseur > 50) {
                throw new Exception("L'épaisseur doit être entre 0 et 50 mm.");
            }
            
            // Validation absorption eau
            if ($absorption_eau < 0 || $absorption_eau > 20) {
                throw new Exception("L'absorption d'eau doit être entre 0 et 20%.");
            }
            
            // Validation surface prévue
            if ($surface_prevue < 0 || $surface_prevue > 10000) {
                throw new Exception("La surface prévue doit être entre 0 et 10000 m².");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Insertion en base
            $stmt = $conn->prepare("
                INSERT INTO carrelage (
                    projet_id, devis_id, designation, quantite, unite, 
                    prix_unitaire, total, type_carrelage, format_carreau, 
                    epaisseur, couleur, finition, classe_usage, antiderapant,
                    resistance_gel, absorption_eau, pose_type, surface_prevue, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param(
                "iisdsdsssdsssssdssd", 
                $projet_id, $devis_id, $designation, $quantite, $unite,
                $prix_unitaire, $total, $type_carrelage, $format_carreau,
                $epaisseur, $couleur, $finition, $classe_usage, $antiderapant,
                $resistance_gel, $absorption_eau, $pose_type, $surface_prevue
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'carrelage');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'carrelage', 'Ajout', "Élément ajouté : {$designation}");
                
                $message = "Élément carrelage ajouté avec succès !";
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
            $type_carrelage = trim($_POST['type_carrelage'] ?? '');
            $format_carreau = trim($_POST['format_carreau'] ?? '');
            $epaisseur = floatval($_POST['epaisseur'] ?? 0);
            $couleur = trim($_POST['couleur'] ?? '');
            $finition = trim($_POST['finition'] ?? '');
            $classe_usage = trim($_POST['classe_usage'] ?? '');
            $antiderapant = trim($_POST['antiderapant'] ?? '');
            $resistance_gel = trim($_POST['resistance_gel'] ?? '');
            $absorption_eau = floatval($_POST['absorption_eau'] ?? 0);
            $pose_type = trim($_POST['pose_type'] ?? '');
            $surface_prevue = floatval($_POST['surface_prevue'] ?? 0);
            
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
            
            if (!empty($format_carreau) && !preg_match('/^\d+x\d+(cm|mm)?$/i', $format_carreau)) {
                throw new Exception("Format de carreau invalide (ex: 30x30cm, 60x120cm).");
            }
            
            if ($epaisseur < 0 || $epaisseur > 50) {
                throw new Exception("L'épaisseur doit être entre 0 et 50 mm.");
            }
            
            if ($absorption_eau < 0 || $absorption_eau > 20) {
                throw new Exception("L'absorption d'eau doit être entre 0 et 20%.");
            }
            
            if ($surface_prevue < 0 || $surface_prevue > 10000) {
                throw new Exception("La surface prévue doit être entre 0 et 10000 m².");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Mise à jour en base
            $stmt = $conn->prepare("
                UPDATE carrelage SET 
                    designation = ?, quantite = ?, unite = ?, prix_unitaire = ?, 
                    total = ?, type_carrelage = ?, format_carreau = ?, epaisseur = ?, 
                    couleur = ?, finition = ?, classe_usage = ?, antiderapant = ?,
                    resistance_gel = ?, absorption_eau = ?, pose_type = ?, surface_prevue = ?, 
                    date_modification = NOW()
                WHERE id = ? AND projet_id = ? AND devis_id = ?
            ");
            
            $stmt->bind_param(
                "sdsdsssdsssssdssdiii",
                $designation, $quantite, $unite, $prix_unitaire, $total,
                $type_carrelage, $format_carreau, $epaisseur, $couleur, $finition,
                $classe_usage, $antiderapant, $resistance_gel, $absorption_eau, 
                $pose_type, $surface_prevue, $element_id, $projet_id, $devis_id
            );
            
            if ($stmt->execute()) {
                // Mise à jour du récapitulatif
                updateRecapitulatif($projet_id, $devis_id, 'carrelage');
                
                // Sauvegarde dans l'historique
                sauvegarderHistorique($projet_id, $devis_id, 'carrelage', 'Modification', "Élément modifié : {$designation}");
                
                $message = "Élément carrelage modifié avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de la modification : " . $conn->error);
            }
            
        } elseif ($action == 'supprimer' && $element_id > 0) {
            // Récupération de la désignation avant suppression
            $stmt_get = $conn->prepare("SELECT designation FROM carrelage WHERE id = ? AND projet_id = ? AND devis_id = ?");
            $stmt_get->bind_param("iii", $element_id, $projet_id, $devis_id);
            $stmt_get->execute();
            $result_get = $stmt_get->get_result();
            $element_data = $result_get->fetch_assoc();
            
            if ($element_data) {
                // Suppression de l'élément
                $stmt = $conn->prepare("DELETE FROM carrelage WHERE id = ? AND projet_id = ? AND devis_id = ?");
                $stmt->bind_param("iii", $element_id, $projet_id, $devis_id);
                
                if ($stmt->execute()) {
                    // Mise à jour du récapitulatif
                    updateRecapitulatif($projet_id, $devis_id, 'carrelage');
                    
                    // Sauvegarde dans l'historique
                    sauvegarderHistorique($projet_id, $devis_id, 'carrelage', 'Suppression', "Élément supprimé : {$element_data['designation']}");
                    
                    $message = "Élément carrelage supprimé avec succès !";
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

// Récupération des éléments de carrelage pour affichage
$elements_carrelage = [];
$total_module = 0;

$stmt = $conn->prepare("
    SELECT id, designation, quantite, unite, prix_unitaire, total,
           type_carrelage, format_carreau, epaisseur, couleur, finition,
           classe_usage, antiderapant, resistance_gel, absorption_eau,
           pose_type, surface_prevue,
           DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation_fr,
           DATE_FORMAT(date_modification, '%d/%m/%Y %H:%i') as date_modification_fr
    FROM carrelage 
    WHERE projet_id = ? AND devis_id = ? 
    ORDER BY date_creation DESC
");

$stmt->bind_param("ii", $projet_id, $devis_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $elements_carrelage[] = $row;
    $total_module += $row['total'];
}

// Récupération de l'élément à modifier si nécessaire
$element_a_modifier = null;
if ($action == 'modifier' && $element_id > 0) {
    $stmt = $conn->prepare("
        SELECT * FROM carrelage 
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
    <title>Carrelage - <?= htmlspecialchars($projet_devis_info['nom_projet']) ?> | GSN ProDevis360°</title>
    
    <!-- Font Awesome 6.5.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ===== VARIABLES CSS GSN ProDevis360° CARRELAGE ===== */
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
            
            /* Variables spécifiques carrelage */
            --tile-primary: #16a085;
            --tile-light: #48c9b0;
            --tile-dark: #138d75;
            --ceramic-white: #fdfefe;
            --ceramic-beige: #f8f9f9;
            --ceramic-gray: #bdc3c7;
            --grout-light: #ecf0f1;
            --grout-dark: #95a5a6;
            --porcelain-blue: #5dade2;
            --stone-brown: #a0826d;
            --marble-cream: #f7f9fc;
            --mosaic-gold: #f1c40f;
            --glossy-shine: rgba(255,255,255,0.4);
            --matte-texture: rgba(0,0,0,0.05);
            --anti-slip: #e74c3c;
            --water-resistant: #3498db;
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
            background: var(--tile-primary);
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
            background: var(--glossy-shine);
            transition: left 0.6s ease;
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
            background: linear-gradient(90deg, transparent, var(--glossy-shine), transparent);
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
            width: 100px;
            height: 100px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="10" height="10" fill="%2316a085" opacity="0.1"/><rect x="10" y="10" width="10" height="10" fill="%2316a085" opacity="0.1"/><rect x="20" y="0" width="10" height="10" fill="%2316a085" opacity="0.1"/><rect x="30" y="10" width="10" height="10" fill="%2316a085" opacity="0.1"/></svg>') repeat;
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
            position: relative;
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

        /* ===== SUGGESTIONS CARRELAGE ===== */
        .suggestions-carrelage {
            background: linear-gradient(135deg, var(--tile-primary) 0%, var(--tile-dark) 100%);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .suggestions-carrelage::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="20" height="20" fill="white" opacity="0.1" stroke="white" stroke-width="0.5"/><rect x="20" y="20" width="20" height="20" fill="white" opacity="0.05" stroke="white" stroke-width="0.5"/><rect x="40" y="0" width="20" height="20" fill="white" opacity="0.1" stroke="white" stroke-width="0.5"/><rect x="60" y="20" width="20" height="20" fill="white" opacity="0.05" stroke="white" stroke-width="0.5"/><rect x="80" y="0" width="20" height="20" fill="white" opacity="0.1" stroke="white" stroke-width="0.5"/></svg>') repeat;
            pointer-events: none;
        }

        .suggestions-carrelage h4 {
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
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
            background: var(--glossy-shine);
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

        /* ===== CALCULATEUR CARRELAGE ===== */
        .calculator-section {
            background: linear-gradient(135deg, var(--stone-brown) 0%, var(--mosaic-gold) 100%);
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60"><circle cx="30" cy="30" r="2" fill="white" opacity="0.3"/><circle cx="10" cy="10" r="1" fill="white" opacity="0.2"/><circle cx="50" cy="15" r="1.5" fill="white" opacity="0.25"/><circle cx="15" cy="45" r="1" fill="white" opacity="0.2"/><circle cx="45" cy="50" r="1.5" fill="white" opacity="0.3"/></svg>') repeat;
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
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
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
            background: var(--glossy-shine);
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="10" height="10" fill="%2316a085" opacity="0.05"/><rect x="10" y="10" width="10" height="10" fill="%2316a085" opacity="0.03"/><rect x="20" y="0" width="10" height="10" fill="%2316a085" opacity="0.05"/></svg>') repeat;
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
            background: linear-gradient(90deg, var(--tile-primary), var(--primary-orange));
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
            background: rgba(22, 160, 133, 0.05);
        }

        .actions-cell {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        /* ===== BADGES SPÉCIALISÉS CARRELAGE ===== */
        .badge-type {
            background: var(--tile-primary);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }

        .badge-type::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--glossy-shine);
            transition: left 0.3s ease;
        }

        .badge-type:hover::before {
            left: 100%;
        }

        .badge-format {
            background: var(--porcelain-blue);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-finition {
            background: var(--stone-brown);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-antiderapant {
            background: var(--anti-slip);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-resistance {
            background: var(--water-resistant);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-classe {
            background: var(--mosaic-gold);
            color: var(--neutral-dark);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-pose {
            background: var(--grout-dark);
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="15" height="15" fill="white" opacity="0.05" stroke="white" stroke-width="0.5"/><rect x="15" y="15" width="15" height="15" fill="white" opacity="0.03" stroke="white" stroke-width="0.5"/></svg>') repeat;
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
            color: var(--tile-light);
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"><rect width="5" height="5" fill="%2316a085" opacity="0.03"/><rect x="5" y="5" width="5" height="5" fill="%2316a085" opacity="0.02"/></svg>') repeat;
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

        /* ===== EFFETS BRILLANCE CARRELAGE ===== */
        .glossy-effect {
            position: relative;
            overflow: hidden;
        }

        .glossy-effect::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: rotate 3s linear infinite;
            pointer-events: none;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .matte-effect {
            position: relative;
        }

        .matte-effect::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--matte-texture);
            pointer-events: none;
        }

        /* ===== INDICATEURS TECHNIQUES CARRELAGE ===== */
        .tech-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: rgba(22, 160, 133, 0.1);
            color: var(--tile-dark);
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            margin: 0.1rem;
        }

        .tech-indicator.water-resistant {
            background: rgba(52, 152, 219, 0.1);
            color: var(--water-resistant);
        }

        .tech-indicator.anti-slip {
            background: rgba(231, 76, 60, 0.1);
            color: var(--anti-slip);
        }

        .tech-indicator.frost-resistant {
            background: rgba(174, 182, 191, 0.2);
            color: var(--neutral-gray);
        }

        /* ===== PATTERN CARRELAGE BACKGROUND ===== */
        .tile-pattern-bg {
            background-image: 
                linear-gradient(45deg, var(--grout-light) 25%, transparent 25%), 
                linear-gradient(-45deg, var(--grout-light) 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, var(--grout-light) 75%), 
                linear-gradient(-45deg, transparent 75%, var(--grout-light) 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }

        .ceramic-shine {
            background: linear-gradient(135deg, 
                var(--ceramic-white) 0%, 
                var(--ceramic-beige) 50%, 
                var(--ceramic-white) 100%);
            position: relative;
        }

        .ceramic-shine::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255,255,255,0.6), 
                transparent);
            animation: shine 2s ease-in-out infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
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

            .suggestion-item {
                font-size: 0.8rem;
                padding: 0.4rem 0.6rem;
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

            .suggestions-grid {
                grid-template-columns: 1fr;
                gap: 0.3rem;
            }

            .calc-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
        }

        /* ===== ANIMATIONS AVANCÉES CARRELAGE ===== */
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

        @keyframes tileFlip {
            0% { 
                transform: rotateY(0deg); 
                background-color: var(--tile-primary);
            }
            50% { 
                transform: rotateY(90deg); 
                background-color: var(--tile-dark);
            }
            100% { 
                transform: rotateY(0deg); 
                background-color: var(--tile-primary);
            }
        }

        .tile-flip {
            animation: tileFlip 0.8s ease-in-out;
        }

        @keyframes groutFlow {
            0% { 
                transform: scaleX(0); 
                transform-origin: left;
            }
            100% { 
                transform: scaleX(1); 
                transform-origin: left;
            }
        }

        .grout-flow {
            animation: groutFlow 0.5s ease-out;
        }

        @keyframes ceramicGlow {
            0%, 100% { 
                box-shadow: 0 0 5px rgba(22, 160, 133, 0.3);
            }
            50% { 
                box-shadow: 0 0 20px rgba(22, 160, 133, 0.6);
            }
        }

        .ceramic-glow {
            animation: ceramicGlow 2s ease-in-out infinite;
        }

        /* ===== STYLES D'IMPRESSION ===== */
        @media print {
            .header-gsn,
            .navigation-modules,
            .form-section,
            .btn,
            .actions-cell,
            .suggestions-carrelage,
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

            .badge-type,
            .badge-format,
            .badge-finition,
            .badge-antiderapant,
            .badge-resistance,
            .badge-classe,
            .badge-pose {
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
        .text-tile { color: var(--tile-primary); }
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
            cursor: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><rect width="8" height="8" fill="%23ffffff" stroke="%2316a085" stroke-width="1"/></svg>') 10 10, pointer;
        }

        .calc-input:focus {
            cursor: text;
        }

        /* ===== SCROLLBAR PERSONNALISÉE ===== */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: var(--grout-light);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--tile-primary);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: var(--tile-dark);
        }

        /* ===== LOADER SPÉCIALISÉ CARRELAGE ===== */
        .loading-tiles {
            display: inline-block;
            width: 20px;
            height: 20px;
            background: var(--tile-primary);
            animation: tileLoader 1.2s ease-in-out infinite;
        }

        @keyframes tileLoader {
            0%, 100% {
                transform: scale(1) rotate(0deg);
                border-radius: 0;
            }
            25% {
                transform: scale(1.2) rotate(45deg);
                border-radius: 50%;
            }
            50% {
                transform: scale(1) rotate(90deg);
                border-radius: 0;
            }
            75% {
                transform: scale(0.8) rotate(135deg);
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
                        <i class="fas fa-th"></i>
                        Module Carrelage
                        <span class="module-badge glossy-effect">
                            <i class="fas fa-cube"></i>
                            Revêtements & Sols
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
                        <i class="fas fa-th-large"></i>
                        Carrelage & Faïence
                    </div>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn-header ceramic-shine">
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
                <a href="dupliquer_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn-header">
                    <i class="fas fa-copy"></i>
                    Dupliquer
                </a>
            </div>
        </div>
    </header>

    <!-- ===== NAVIGATION MODULES DYNAMIQUE ===== -->
    <nav class="navigation-modules tile-pattern-bg">
        <div class="nav-container">
            <div class="nav-modules">
                <?php foreach ($modules_config as $module_key => $module_info): ?>
                    <a href="<?= $module_key ?>.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                       class="nav-item <?= $module_key === $current_module ? 'active ceramic-glow' : '' ?>"
                       style="<?= $module_key === $current_module ? '' : '--hover-color: ' . $module_info['color'] ?>"
                       data-tooltip="<?= $module_info['name'] ?>">
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
            <div class="alert alert-<?= $message_type ?> fade-in-up tile-flip">
                <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- ===== FORMULAIRE CARRELAGE ===== -->
        <div class="form-section fade-in-up ceramic-shine">
            <h2>
                <i class="fas fa-<?= $element_a_modifier ? 'edit' : 'plus-circle' ?>"></i>
                <?= $element_a_modifier ? 'Modifier l\'élément carrelage' : 'Ajouter un élément carrelage' ?>
                <span class="tech-indicator">
                    <i class="fas fa-th"></i>
                    Revêtements & Carreaux
                </span>
            </h2>

            <!-- Suggestions Carrelage -->
            <div class="suggestions-carrelage">
                <h4>
                    <i class="fas fa-th-large"></i>
                    Catalogue Carrelage & Revêtements
                    <small>(Cliquez pour remplir automatiquement)</small>
                    <span class="tech-indicator water-resistant">
                        <i class="fas fa-tint"></i>
                        90+ suggestions
                    </span>
                </h4>
                <div class="suggestions-grid">
                    <?php foreach ($suggestions_carrelage as $suggestion): ?>
                        <div class="suggestion-item glossy-effect" onclick="remplirSuggestion('<?= htmlspecialchars($suggestion, ENT_QUOTES) ?>')">
                            <i class="fas fa-square"></i>
                            <?= htmlspecialchars($suggestion) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Calculateur Carrelage -->
            <div class="calculator-section">
                <h4>
                    <i class="fas fa-calculator"></i>
                    Calculateur Carrelage & Surfaces
                    <span class="tech-indicator">
                        <i class="fas fa-ruler-combined"></i>
                        Dimensionnement auto
                    </span>
                </h4>
                <div class="calc-grid">
                    <input type="number" id="calc_longueur" placeholder="Longueur (m)" class="calc-input" step="0.1" max="100">
                    <input type="number" id="calc_largeur" placeholder="Largeur (m)" class="calc-input" step="0.1" max="100">
                    <input type="number" id="calc_perte" placeholder="Perte (%)" class="calc-input" step="1" max="30" value="10">
                    <button type="button" class="btn btn-sm btn-info" onclick="calculerSurface()">
                        <i class="fas fa-square"></i> Surface m²
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="calculerQuantiteCarreaux()">
                        <i class="fas fa-th"></i> Nb carreaux
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="calculerColleJoint()">
                        <i class="fas fa-trowel"></i> Colle & joint
                    </button>
                </div>
            </div>

            <form method="POST" action="" id="formCarrelage">
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
                               placeholder="Ex: Carrelage grès cérame 60x60cm mat moderne"
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
                            <option value="unité" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'unité') ? 'selected' : '' ?>>Unité</option>
                            <option value="ml" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'ml') ? 'selected' : '' ?>>Mètre linéaire (ml)</option>
                            <option value="lot" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'lot') ? 'selected' : '' ?>>Lot</option>
                            <option value="boîte" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'boîte') ? 'selected' : '' ?>>Boîte</option>
                            <option value="palette" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'palette') ? 'selected' : '' ?>>Palette</option>
                            <option value="kg" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'kg') ? 'selected' : '' ?>>Kilogramme (kg)</option>
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
                               placeholder="Ex: 12500"
                               step="0.01"
                               min="0"
                               required>
                    </div>
                </div>

                <!-- Ligne 2 : Type et format -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="type_carrelage">
                            <i class="fas fa-th-large"></i>
                            Type carrelage
                        </label>
                        <select id="type_carrelage" name="type_carrelage">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Grès cérame" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Grès cérame') ? 'selected' : '' ?>>Grès cérame (sol intérieur/extérieur)</option>
                            <option value="Faïence" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Faïence') ? 'selected' : '' ?>>Faïence (mur intérieur)</option>
                            <option value="Mosaïque" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Mosaïque') ? 'selected' : '' ?>>Mosaïque (décoratif)</option>
                            <option value="Pierre naturelle" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Pierre naturelle') ? 'selected' : '' ?>>Pierre naturelle</option>
                            <option value="Terre cuite" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Terre cuite') ? 'selected' : '' ?>>Terre cuite (tomettes)</option>
                            <option value="Béton ciré" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Béton ciré') ? 'selected' : '' ?>>Béton ciré</option>
                            <option value="Carrelage technique" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Carrelage technique') ? 'selected' : '' ?>>Carrelage technique (industrie)</option>
                            <option value="Colle carrelage" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Colle carrelage') ? 'selected' : '' ?>>Colle carrelage</option>
                            <option value="Joint carrelage" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Joint carrelage') ? 'selected' : '' ?>>Joint carrelage</option>
                            <option value="Accessoire pose" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Accessoire pose') ? 'selected' : '' ?>>Accessoire de pose</option>
                            <option value="Produit entretien" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Produit entretien') ? 'selected' : '' ?>>Produit d'entretien</option>
                            <option value="Outillage" <?= ($element_a_modifier && $element_a_modifier['type_carrelage'] === 'Outillage') ? 'selected' : '' ?>>Outillage carreleur</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="format_carreau">
                            <i class="fas fa-expand-arrows-alt"></i>
                            Format carreau
                        </label>
                        <input type="text" 
                               id="format_carreau" 
                               name="format_carreau" 
                               value="<?= $element_a_modifier ? htmlspecialchars($element_a_modifier['format_carreau']) : '' ?>"
                               placeholder="Ex: 60x60cm, 30x120cm"
                               pattern="^[0-9]+x[0-9]+(cm|mm)?$"
                               title="Format: 60x60cm ou 300x600mm">
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
                               placeholder="Ex: 9.5"
                               step="0.1"
                               min="0"
                               max="50">
                    </div>

                    <div class="form-group">
                        <label for="couleur">
                            <i class="fas fa-palette"></i>
                            Couleur
                        </label>
                        <select id="couleur" name="couleur">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Blanc" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Blanc') ? 'selected' : '' ?>>Blanc</option>
                            <option value="Noir" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Noir') ? 'selected' : '' ?>>Noir</option>
                            <option value="Gris" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Gris') ? 'selected' : '' ?>>Gris</option>
                            <option value="Beige" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Beige') ? 'selected' : '' ?>>Beige</option>
                            <option value="Marron" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Marron') ? 'selected' : '' ?>>Marron</option>
                            <option value="Bleu" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Bleu') ? 'selected' : '' ?>>Bleu</option>
                            <option value="Vert" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Vert') ? 'selected' : '' ?>>Vert</option>
                            <option value="Rouge" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Rouge') ? 'selected' : '' ?>>Rouge</option>
                            <option value="Multicolore" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Multicolore') ? 'selected' : '' ?>>Multicolore</option>
                            <option value="Imitation bois" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Imitation bois') ? 'selected' : '' ?>>Imitation bois</option>
                            <option value="Imitation pierre" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Imitation pierre') ? 'selected' : '' ?>>Imitation pierre</option>
                            <option value="Effet béton" <?= ($element_a_modifier && $element_a_modifier['couleur'] === 'Effet béton') ? 'selected' : '' ?>>Effet béton</option>
                        </select>
                    </div>
                </div>

                <!-- Ligne 3 : Finition et propriétés -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="finition">
                            <i class="fas fa-gem"></i>
                            Finition
                        </label>
                        <select id="finition" name="finition">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Mat" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Mat') ? 'selected' : '' ?>>Mat</option>
                            <option value="Brillant" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Brillant') ? 'selected' : '' ?>>Brillant</option>
                            <option value="Satiné" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Satiné') ? 'selected' : '' ?>>Satiné</option>
                            <option value="Poli" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Poli') ? 'selected' : '' ?>>Poli</option>
                            <option value="Structuré" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Structuré') ? 'selected' : '' ?>>Structuré</option>
                            <option value="Antidérapant" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Antidérapant') ? 'selected' : '' ?>>Antidérapant</option>
                            <option value="Relief" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Relief') ? 'selected' : '' ?>>Relief</option>
                            <option value="Lappato" <?= ($element_a_modifier && $element_a_modifier['finition'] === 'Lappato') ? 'selected' : '' ?>>Lappato (semi-poli)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="classe_usage">
                            <i class="fas fa-shield-alt"></i>
                            Classe d'usage
                        </label>
                        <select id="classe_usage" name="classe_usage">
                            <option value="">-- Sélectionnez --</option>
                            <option value="PEI I" <?= ($element_a_modifier && $element_a_modifier['classe_usage'] === 'PEI I') ? 'selected' : '' ?>>PEI I (très faible passage)</option>
                            <option value="PEI II" <?= ($element_a_modifier && $element_a_modifier['classe_usage'] === 'PEI II') ? 'selected' : '' ?>>PEI II (faible passage)</option>
                            <option value="PEI III" <?= ($element_a_modifier && $element_a_modifier['classe_usage'] === 'PEI III') ? 'selected' : '' ?>>PEI III (passage moyen)</option>
                            <option value="PEI IV" <?= ($element_a_modifier && $element_a_modifier['classe_usage'] === 'PEI IV') ? 'selected' : '' ?>>PEI IV (passage intense)</option>
                            <option value="PEI V" <?= ($element_a_modifier && $element_a_modifier['classe_usage'] === 'PEI V') ? 'selected' : '' ?>>PEI V (très intense/public)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="antiderapant">
                            <i class="fas fa-shoe-prints"></i>
                            Antidérapant
                        </label>
                        <select id="antiderapant" name="antiderapant">
                            <option value="">-- Sélectionnez --</option>
                            <option value="R9" <?= ($element_a_modifier && $element_a_modifier['antiderapant'] === 'R9') ? 'selected' : '' ?>>R9 (pente < 10°)</option>
                            <option value="R10" <?= ($element_a_modifier && $element_a_modifier['antiderapant'] === 'R10') ? 'selected' : '' ?>>R10 (pente 10-19°)</option>
                            <option value="R11" <?= ($element_a_modifier && $element_a_modifier['antiderapant'] === 'R11') ? 'selected' : '' ?>>R11 (pente 19-27°)</option>
                            <option value="R12" <?= ($element_a_modifier && $element_a_modifier['antiderapant'] === 'R12') ? 'selected' : '' ?>>R12 (pente 27-35°)</option>
                            <option value="R13" <?= ($element_a_modifier && $element_a_modifier['antiderapant'] === 'R13') ? 'selected' : '' ?>>R13 (pente > 35°)</option>
                            <option value="A" <?= ($element_a_modifier && $element_a_modifier['antiderapant'] === 'A') ? 'selected' : '' ?>>A (pieds nus sec)</option>
                            <option value="B" <?= ($element_a_modifier && $element_a_modifier['antiderapant'] === 'B') ? 'selected' : '' ?>>B (pieds nus humide)</option>
                            <option value="C" <?= ($element_a_modifier && $element_a_modifier['antiderapant'] === 'C') ? 'selected' : '' ?>>C (pieds nus piscine)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="resistance_gel">
                            <i class="fas fa-snowflake"></i>
                            Résistance gel
                        </label>
                        <select id="resistance_gel" name="resistance_gel">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Non gélivable" <?= ($element_a_modifier && $element_a_modifier['resistance_gel'] === 'Non gélivable') ? 'selected' : '' ?>>Non gélivable (intérieur uniquement)</option>
                            <option value="Gélivable" <?= ($element_a_modifier && $element_a_modifier['resistance_gel'] === 'Gélivable') ? 'selected' : '' ?>>Gélivable (extérieur possible)</option>
                            <option value="Très résistant" <?= ($element_a_modifier && $element_a_modifier['resistance_gel'] === 'Très résistant') ? 'selected' : '' ?>>Très résistant (tout climat)</option>
                        </select>
                    </div>
                </div>

                <!-- Ligne 4 : Propriétés techniques et pose -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="absorption_eau">
                            <i class="fas fa-tint"></i>
                            Absorption eau (%)
                        </label>
                        <input type="number" 
                               id="absorption_eau" 
                               name="absorption_eau" 
                               value="<?= $element_a_modifier ? $element_a_modifier['absorption_eau'] : '' ?>"
                               placeholder="Ex: 0.5"
                               step="0.1"
                               min="0"
                               max="20">
                    </div>

                    <div class="form-group">
                        <label for="pose_type">
                            <i class="fas fa-tools"></i>
                            Type de pose
                        </label>
                        <select id="pose_type" name="pose_type">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Collée simple" <?= ($element_a_modifier && $element_a_modifier['pose_type'] === 'Collée simple') ? 'selected' : '' ?>>Collée simple</option>
                            <option value="Double encollage" <?= ($element_a_modifier && $element_a_modifier['pose_type'] === 'Double encollage') ? 'selected' : '' ?>>Double encollage</option>
                            <option value="Scellée mortier" <?= ($element_a_modifier && $element_a_modifier['pose_type'] === 'Scellée mortier') ? 'selected' : '' ?>>Scellée au mortier</option>
                            <option value="Sur plots" <?= ($element_a_modifier && $element_a_modifier['pose_type'] === 'Sur plots') ? 'selected' : '' ?>>Sur plots (terrasse)</option>
                            <option value="Pose droite" <?= ($element_a_modifier && $element_a_modifier['pose_type'] === 'Pose droite') ? 'selected' : '' ?>>Pose droite</option>
                            <option value="Pose diagonale" <?= ($element_a_modifier && $element_a_modifier['pose_type'] === 'Pose diagonale') ? 'selected' : '' ?>>Pose diagonale</option>
                            <option value="Pose à joints décalés" <?= ($element_a_modifier && $element_a_modifier['pose_type'] === 'Pose à joints décalés') ? 'selected' : '' ?>>Pose à joints décalés</option>
                            <option value="Pose en opus" <?= ($element_a_modifier && $element_a_modifier['pose_type'] === 'Pose en opus') ? 'selected' : '' ?>>Pose en opus</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="surface_prevue">
                            <i class="fas fa-square"></i>
                            Surface prévue (m²)
                        </label>
                        <input type="number" 
                               id="surface_prevue" 
                               name="surface_prevue" 
                               value="<?= $element_a_modifier ? $element_a_modifier['surface_prevue'] : '' ?>"
                               placeholder="Ex: 45.8"
                               step="0.1"
                               min="0"
                               max="10000">
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-info-circle"></i>
                            Actions rapides
                        </label>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <button type="button" 
                                    class="btn btn-sm btn-info" 
                                    onclick="calculerRendement()"
                                    title="Alt+R">
                                <i class="fas fa-chart-area"></i>
                                Rendement
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-warning" 
                                    onclick="estimerPrix()"
                                    title="Alt+P">
                                <i class="fas fa-euro-sign"></i>
                                Prix auto
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="form-row">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary ceramic-glow">
                            <i class="fas fa-<?= $element_a_modifier ? 'save' : 'plus' ?>"></i>
                            <?= $element_a_modifier ? 'Modifier l\'élément' : 'Ajouter l\'élément' ?>
                        </button>
                        
                        <?php if ($element_a_modifier): ?>
                            <a href="carrelage.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                        <?php endif; ?>
                        
                        <button type="reset" class="btn btn-secondary ml-2" onclick="resetFormulaire()">
                            <i class="fas fa-eraser"></i>
                            Effacer
                        </button>
                        
                        <button type="button" class="btn btn-info ml-2" onclick="previsualiserCarrelage()">
                            <i class="fas fa-eye"></i>
                            Prévisualiser
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ===== TABLEAU DES ÉLÉMENTS CARRELAGE ===== -->
        <div class="table-container fade-in-up">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list"></i>
                    Éléments carrelage
                    <span class="badge-type ml-2"><?= count($elements_carrelage) ?> élément(s)</span>
                </h3>
                <div class="table-actions">
                    <span class="total-amount">
                        <i class="fas fa-th"></i>
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
                            <th><i class="fas fa-th-large"></i> Type</th>
                            <th><i class="fas fa-expand-arrows-alt"></i> Format</th>
                            <th><i class="fas fa-arrows-alt-v"></i> Épaisseur</th>
                            <th><i class="fas fa-palette"></i> Couleur</th>
                            <th><i class="fas fa-gem"></i> Finition</th>
                            <th><i class="fas fa-shield-alt"></i> Classe</th>
                            <th><i class="fas fa-shoe-prints"></i> Antidérapant</th>
                            <th><i class="fas fa-snowflake"></i> Gel</th>
                            <th><i class="fas fa-tint"></i> Absorption</th>
                            <th><i class="fas fa-tools"></i> Pose</th>
                            <th><i class="fas fa-square"></i> Surface</th>
                            <th><i class="fas fa-euro-sign"></i> Total</th>
                            <th><i class="fas fa-calendar"></i> Créé le</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($elements_carrelage)): ?>
                            <tr>
                                <td colspan="19" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-th fa-3x mb-3 d-block"></i>
                                        <p>Aucun élément carrelage ajouté pour ce devis.</p>
                                        <small>Utilisez le formulaire ci-dessus pour ajouter des carreaux, colles, joints, etc.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($elements_carrelage as $element): ?>
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
                                    <td><span class="badge-format"><?= htmlspecialchars($element['unite']) ?></span></td>
                                    <td><strong><?= number_format($element['prix_unitaire'], 0, ',', ' ') ?></strong> FCFA</td>
                                    <td>
                                        <?php if (!empty($element['type_carrelage'])): ?>
                                            <span class="badge-type">
                                                <i class="fas fa-th-large"></i>
                                                <?= htmlspecialchars($element['type_carrelage']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['format_carreau'])): ?>
                                            <span class="badge-format">
                                                <i class="fas fa-expand-arrows-alt"></i>
                                                <?= htmlspecialchars($element['format_carreau']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['epaisseur']) && $element['epaisseur'] > 0): ?>
                                            <span class="badge-format">
                                                <i class="fas fa-arrows-alt-v"></i>
                                                <?= number_format($element['epaisseur'], 1) ?>mm
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['couleur'])): ?>
                                            <span class="badge-finition">
                                                <i class="fas fa-palette"></i>
                                                <?= htmlspecialchars($element['couleur']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['finition'])): ?>
                                            <span class="badge-finition">
                                                <i class="fas fa-gem"></i>
                                                <?= htmlspecialchars($element['finition']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['classe_usage'])): ?>
                                            <span class="badge-classe">
                                                <i class="fas fa-shield-alt"></i>
                                                <?= htmlspecialchars($element['classe_usage']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['antiderapant'])): ?>
                                            <span class="badge-antiderapant">
                                                <i class="fas fa-shoe-prints"></i>
                                                <?= htmlspecialchars($element['antiderapant']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['resistance_gel'])): ?>
                                            <span class="badge-resistance">
                                                <i class="fas fa-snowflake"></i>
                                                <?= substr(htmlspecialchars($element['resistance_gel']), 0, 10) ?><?= strlen($element['resistance_gel']) > 10 ? '...' : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['absorption_eau']) && $element['absorption_eau'] > 0): ?>
                                            <span class="badge-resistance">
                                                <i class="fas fa-tint"></i>
                                                <?= number_format($element['absorption_eau'], 1) ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['pose_type'])): ?>
                                            <span class="badge-pose">
                                                <i class="fas fa-tools"></i>
                                                <?= substr(htmlspecialchars($element['pose_type']), 0, 8) ?><?= strlen($element['pose_type']) > 8 ? '...' : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['surface_prevue']) && $element['surface_prevue'] > 0): ?>
                                            <span class="badge-format">
                                                <i class="fas fa-square"></i>
                                                <?= number_format($element['surface_prevue'], 1) ?>m²
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
                                            <a href="carrelage.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&action=modifier&element_id=<?= $element['id'] ?>" 
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
                                            <button type="button" 
                                                    class="btn btn-sm btn-info" 
                                                    onclick="previsualiserElement(<?= $element['id'] ?>)"
                                                    title="Prévisualiser ce carrelage">
                                                <i class="fas fa-eye"></i>
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
                        <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=carrelage" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-clock"></i> Voir tout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TOTAUX MODULE CARRELAGE ===== -->
        <div class="module-summary fade-in-up">
            <h3>
                <i class="fas fa-th"></i>
                Total Module Carrelage
            </h3>
            <div class="total-amount pulse-animation">
                <?= number_format($total_module, 0, ',', ' ') ?> FCFA
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Mis à jour automatiquement • <?= count($elements_carrelage) ?> élément(s)
                <?php if ($total_module > 0 && count($elements_carrelage) > 0): ?>
                    • Moyenne: <?= number_format($total_module / count($elements_carrelage), 0, ',', ' ') ?> FCFA/élément
                <?php endif; ?>
            </small>
        </div>

    </div>

    <!-- ===== JAVASCRIPT SPÉCIALISÉ CARRELAGE ===== -->
    <script>
        // ===== CONFIGURATION ET VARIABLES CARRELAGE =====
        const PRIX_CARRELAGE = {
            // Types de carreaux (prix par m²)
            'gres_cerame': { base: 12000, factor: 1.0 },
            'faience': { base: 8500, factor: 0.8 },
            'granito': { base: 15000, factor: 1.3 },
            'marbre': { base: 35000, factor: 2.8 },
            'pierre_naturelle': { base: 25000, factor: 2.0 },
            'mosaique': { base: 18000, factor: 1.5 },
            'carrelage_exterieur': { base: 14000, factor: 1.2 },
            
            // Formats courants (facteurs multiplicateurs)
            '10x10': { factor: 1.8 }, // Petits formats plus chers à la pose
            '15x15': { factor: 1.4 },
            '20x20': { factor: 1.2 },
            '30x30': { factor: 1.0 }, // Format standard
            '40x40': { factor: 0.9 },
            '45x45': { factor: 0.85 },
            '60x60': { factor: 0.8 },
            '80x80': { factor: 0.9 }, // Grands formats plus techniques
            
            // Finitions spéciales
            'antiderapant': { factor: 1.3 },
            'poli': { factor: 1.5 },
            'rectifie': { factor: 1.4 },
            'relief': { factor: 1.6 },
            'mat': { factor: 1.0 },
            
            // Accessoires (prix par ml ou unité)
            'plinthe': { base: 3500, factor: 1.0 },
            'baguette': { base: 2500, factor: 1.0 },
            'nez_marche': { base: 4500, factor: 1.0 },
            'angle_sortant': { base: 1800, factor: 1.0 },
            'angle_rentrant': { base: 1600, factor: 1.0 }
        };

        const FORMATS_NORMALISES = {
            'petits': ['10x10', '15x15', '20x20'],
            'moyens': ['25x33', '30x30', '33x33'],
            'grands': ['40x40', '45x45', '50x50'],
            'tres_grands': ['60x60', '75x75', '80x80'],
            'rectangulaires': ['30x60', '40x80', '20x80', '15x60']
        };

        const COLLE_JOINT = {
            // Consommation colle (kg/m²)
            'colle_c1': { conso: 3.5, prix: 450 }, // Colle standard
            'colle_c2': { conso: 4.0, prix: 580 }, // Colle améliorée
            'colle_flex': { conso: 4.5, prix: 720 }, // Colle flexible
            
            // Consommation joint (kg/10m²)
            'joint_standard': { conso: 0.8, prix: 320 },
            'joint_epoxy': { conso: 1.0, prix: 850 },
            'joint_hydrofuge': { conso: 0.9, prix: 480 }
        };

        // ===== FONCTIONS CALCULATRICES CARRELAGE =====
        
        /**
         * Remplir automatiquement le formulaire avec une suggestion
         */
        function remplirSuggestion(suggestion) {
            const designationField = document.getElementById('designation');
            const quantiteField = document.getElementById('quantite');
            const uniteField = document.getElementById('unite');
            const prixField = document.getElementById('prix_unitaire');
            const typeField = document.getElementById('type_carrelage');
            const formatField = document.getElementById('format');
            const finitionField = document.getElementById('finition');
            const usageField = document.getElementById('usage');
            const couleurField = document.getElementById('couleur');
            
            // Remplir la désignation
            designationField.value = suggestion;
            
            // Analyse intelligente de la suggestion
            const sug = suggestion.toLowerCase();
            
            // Déterminer le type de carrelage
            if (sug.includes('grès cérame') || sug.includes('gres cerame')) {
                typeField.value = 'Grès cérame';
                usageField.value = 'Sol intérieur';
            } else if (sug.includes('faïence') || sug.includes('faience')) {
                typeField.value = 'Faïence';
                usageField.value = 'Mur salle de bain';
            } else if (sug.includes('granito')) {
                typeField.value = 'Granito';
                usageField.value = 'Sol extérieur';
            } else if (sug.includes('marbre')) {
                typeField.value = 'Marbre';
                usageField.value = 'Sol intérieur';
            } else if (sug.includes('mosaïque') || sug.includes('mosaique')) {
                typeField.value = 'Mosaïque';
                usageField.value = 'Mur décoratif';
            }
            
            // Extraire le format (XXxYY)
            const formatMatch = sug.match(/(\d+)x(\d+)/);
            if (formatMatch) {
                formatField.value = formatMatch[1] + 'x' + formatMatch[2] + 'cm';
            }
            
            // Déterminer la finition
            if (sug.includes('poli')) {
                finitionField.value = 'Poli';
            } else if (sug.includes('mat')) {
                finitionField.value = 'Mat';
            } else if (sug.includes('antidérapant') || sug.includes('antiderapant')) {
                finitionField.value = 'Antidérapant';
            } else if (sug.includes('relief')) {
                finitionField.value = 'Relief';
            } else if (sug.includes('rectifié') || sug.includes('rectifie')) {
                finitionField.value = 'Rectifié';
            }
            
            // Déterminer la couleur
            if (sug.includes('blanc')) couleurField.value = 'Blanc';
            else if (sug.includes('noir')) couleurField.value = 'Noir';
            else if (sug.includes('gris')) couleurField.value = 'Gris';
            else if (sug.includes('beige')) couleurField.value = 'Beige';
            else if (sug.includes('marron')) couleurField.value = 'Marron';
            
            // Définir l'unité selon le type
            if (sug.includes('plinthe') || sug.includes('baguette') || sug.includes('profilé')) {
                uniteField.value = 'ml';
                quantiteField.value = '50';
            } else {
                uniteField.value = 'm²';
                quantiteField.value = '25';
            }
            
            // Estimer le prix
            const estimation = estimerPrixCarrelage(suggestion);
            if (estimation > 0) {
                prixField.value = estimation;
            }
            
            // Animation visuelle
            designationField.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #ffffff 100%)';
            setTimeout(() => {
                designationField.style.background = '';
            }, 1000);
            
            // Focus sur le champ quantité
            quantiteField.focus();
            quantiteField.select();
        }

        /**
         * Estimation automatique des prix carrelage
         */
        function estimerPrixCarrelage(designation) {
            const des = designation.toLowerCase();
            let prix = 0;
            
            // Déterminer le type de base
            let typeBase = '';
            if (des.includes('grès cérame') || des.includes('gres cerame')) typeBase = 'gres_cerame';
            else if (des.includes('faïence') || des.includes('faience')) typeBase = 'faience';
            else if (des.includes('granito')) typeBase = 'granito';
            else if (des.includes('marbre')) typeBase = 'marbre';
            else if (des.includes('pierre')) typeBase = 'pierre_naturelle';
            else if (des.includes('mosaïque') || des.includes('mosaique')) typeBase = 'mosaique';
            else if (des.includes('extérieur') || des.includes('exterieur')) typeBase = 'carrelage_exterieur';
            else typeBase = 'gres_cerame'; // Par défaut
            
            // Prix de base
            if (PRIX_CARRELAGE[typeBase]) {
                prix = PRIX_CARRELAGE[typeBase].base;
            }
            
            // Facteur format
            const formatMatch = des.match(/(\d+)x(\d+)/);
            if (formatMatch) {
                const format = formatMatch[1] + 'x' + formatMatch[2];
                if (PRIX_CARRELAGE[format]) {
                    prix *= PRIX_CARRELAGE[format].factor;
                }
            }
            
            // Facteurs finitions
            if (des.includes('poli')) prix *= PRIX_CARRELAGE.poli.factor;
            else if (des.includes('antidérapant')) prix *= PRIX_CARRELAGE.antiderapant.factor;
            else if (des.includes('rectifié')) prix *= PRIX_CARRELAGE.rectifie.factor;
            else if (des.includes('relief')) prix *= PRIX_CARRELAGE.relief.factor;
            
            // Facteurs spéciaux
            if (des.includes('première qualité') || des.includes('1er choix')) prix *= 1.3;
            if (des.includes('importé') || des.includes('import')) prix *= 1.5;
            if (des.includes('imitation') && des.includes('bois')) prix *= 1.4;
            if (des.includes('imitation') && des.includes('marbre')) prix *= 1.6;
            
            // Accessoires
            if (des.includes('plinthe')) {
                prix = PRIX_CARRELAGE.plinthe.base;
            } else if (des.includes('baguette')) {
                prix = PRIX_CARRELAGE.baguette.base;
            } else if (des.includes('nez de marche')) {
                prix = PRIX_CARRELAGE.nez_marche.base;
            }
            
            return Math.round(prix);
        }

        /**
         * Calculateur de surface avec pertes
         */
        function calculerSurfacePertes() {
            const longueur = parseFloat(document.getElementById('calc_longueur').value) || 0;
            const largeur = parseFloat(document.getElementById('calc_largeur').value) || 0;
            const format = document.getElementById('format').value;
            
            if (longueur > 0 && largeur > 0) {
                const surfaceNette = longueur * largeur;
                
                // Calcul des pertes selon le format et la pose
                let tauxPerte = 0.10; // 10% par défaut
                
                const formatMatch = format.match(/(\d+)x(\d+)/);
                if (formatMatch) {
                    const dim1 = parseInt(formatMatch[1]);
                    const dim2 = parseInt(formatMatch[2]);
                    
                    // Petits formats = plus de pertes
                    if (dim1 <= 20 && dim2 <= 20) tauxPerte = 0.15;
                    // Très grands formats = plus de pertes aussi
                    else if (dim1 >= 60 || dim2 >= 60) tauxPerte = 0.20;
                    // Formats rectangulaires = pertes modérées
                    else if (Math.abs(dim1 - dim2) > 20) tauxPerte = 0.12;
                }
                
                // Pose en diagonale = +5% de pertes
                const pose = document.getElementById('calc_pose')?.value;
                if (pose === 'diagonale') tauxPerte += 0.05;
                
                const surfaceAvecPertes = surfaceNette * (1 + tauxPerte);
                
                document.getElementById('quantite').value = surfaceAvecPertes.toFixed(2);
                document.getElementById('unite').value = 'm²';
                
                showToast(`📐 Surface calculée: ${surfaceAvecPertes.toFixed(2)} m²\n` +
                         `Surface nette: ${surfaceNette.toFixed(2)} m² + ${(tauxPerte*100).toFixed(0)}% pertes\n` +
                         `Format: ${format || 'Standard'}`, 'success');
            } else {
                showToast('⚠️ Veuillez saisir longueur et largeur.', 'warning');
            }
        }

        /**
         * Calculer la quantité de colle nécessaire
         */
        function calculerColle() {
            const surface = parseFloat(document.getElementById('quantite').value) || 0;
            const format = document.getElementById('format').value;
            
            if (surface > 0) {
                let typeColle = 'colle_c1'; // Standard par défaut
                let consommation = COLLE_JOINT[typeColle].conso;
                
                // Adapter selon le format
                const formatMatch = format.match(/(\d+)x(\d+)/);
                if (formatMatch) {
                    const dim1 = parseInt(formatMatch[1]);
                    const dim2 = parseInt(formatMatch[2]);
                    
                    // Grands formats = colle flexible
                    if (dim1 >= 40 || dim2 >= 40) {
                        typeColle = 'colle_flex';
                        consommation = COLLE_JOINT[typeColle].conso;
                    }
                    // Petits formats = colle améliorée
                    else if (dim1 <= 20 && dim2 <= 20) {
                        typeColle = 'colle_c2';
                        consommation = COLLE_JOINT[typeColle].conso;
                    }
                }
                
                const quantiteColle = surface * consommation;
                const prixColle = Math.ceil(quantiteColle) * COLLE_JOINT[typeColle].prix;
                
                showToast(`🧱 Colle nécessaire: ${quantiteColle.toFixed(1)} kg (${Math.ceil(quantiteColle)} sacs)\n` +
                         `Type recommandé: ${typeColle.replace('_', ' ').toUpperCase()}\n` +
                         `Prix estimé: ${prixColle.toLocaleString()} FCFA`, 'info');
            } else {
                showToast('⚠️ Veuillez d\'abord saisir la surface.', 'warning');
            }
        }

        /**
         * Calculer la quantité de joint nécessaire
         */
        function calculerJoint() {
            const surface = parseFloat(document.getElementById('quantite').value) || 0;
            const usage = document.getElementById('usage').value;
            
            if (surface > 0) {
                let typeJoint = 'joint_standard';
                let consommation = COLLE_JOINT[typeJoint].conso;
                
                // Adapter selon l'usage
                if (usage && (usage.includes('salle de bain') || usage.includes('piscine'))) {
                    typeJoint = 'joint_hydrofuge';
                    consommation = COLLE_JOINT[typeJoint].conso;
                } else if (usage && usage.includes('cuisine')) {
                    typeJoint = 'joint_epoxy';
                    consommation = COLLE_JOINT[typeJoint].conso;
                }
                
                const quantiteJoint = (surface / 10) * consommation;
                const prixJoint = Math.ceil(quantiteJoint) * COLLE_JOINT[typeJoint].prix;
                
                showToast(`🎨 Joint nécessaire: ${quantiteJoint.toFixed(2)} kg (${Math.ceil(quantiteJoint)} sacs)\n` +
                         `Type recommandé: ${typeJoint.replace('_', ' ').toUpperCase()}\n` +
                         `Prix estimé: ${prixJoint.toLocaleString()} FCFA`, 'info');
            } else {
                showToast('⚠️ Veuillez d\'abord saisir la surface.', 'warning');
            }
        }

        /**
         * Calculer nombre de carreaux nécessaires
         */
        function calculerNombreCarreaux() {
            const surface = parseFloat(document.getElementById('quantite').value) || 0;
            const format = document.getElementById('format').value;
            
            if (surface > 0 && format) {
                const formatMatch = format.match(/(\d+)x(\d+)/);
                if (formatMatch) {
                    const longueurCm = parseInt(formatMatch[1]);
                    const largeurCm = parseInt(formatMatch[2]);
                    const surfaceCarreau = (longueurCm * largeurCm) / 10000; // cm² vers m²
                    
                    const nombreCarreaux = Math.ceil(surface / surfaceCarreau);
                    const nombrePaquets = Math.ceil(nombreCarreaux / 10); // 10 carreaux par paquet généralement
                    
                    showToast(`🔢 Nombre de carreaux: ${nombreCarreaux.toLocaleString()}\n` +
                             `Format: ${format} (${surfaceCarreau.toFixed(4)} m²/carreau)\n` +
                             `Paquets nécessaires: ~${nombrePaquets}`, 'info');
                } else {
                    showToast('⚠️ Format invalide. Utilisez le format LxLcm', 'warning');
                }
            } else {
                showToast('⚠️ Veuillez saisir surface et format.', 'warning');
            }
        }

        /**
         * Estimation du temps de pose
         */
        function estimer TempsPos() {
            const surface = parseFloat(document.getElementById('quantite').value) || 0;
            const format = document.getElementById('format').value;
            const finition = document.getElementById('finition').value;
            
            if (surface > 0) {
                let tempsBase = 0.5; // heures par m² pour format standard
                
                // Adapter selon le format
                const formatMatch = format.match(/(\d+)x(\d+)/);
                if (formatMatch) {
                    const dim1 = parseInt(formatMatch[1]);
                    const dim2 = parseInt(formatMatch[2]);
                    
                    if (dim1 <= 15 && dim2 <= 15) tempsBase = 1.2; // Petits formats
                    else if (dim1 >= 60 || dim2 >= 60) tempsBase = 0.3; // Grands formats
                    else if (Math.abs(dim1 - dim2) > 20) tempsBase = 0.7; // Rectangulaires
                }
                
                // Facteurs finition
                if (finition === 'Mosaïque') tempsBase *= 2.5;
                else if (finition === 'Relief') tempsBase *= 1.4;
                else if (finition === 'Rectifié') tempsBase *= 0.9;
                
                const tempsTotal = surface * tempsBase;
                const joursOuvrier = Math.ceil(tempsTotal / 7); // 7h/jour
                
                showToast(`⏱️ Temps de pose estimé: ${tempsTotal.toFixed(1)} heures\n` +
                         `Soit environ ${joursOuvrier} jour(s) ouvrier\n` +
                         `Surface: ${surface} m² • Format: ${format}`, 'info');
            } else {
                showToast('⚠️ Veuillez d\'abord saisir la surface.', 'warning');
            }
        }

        /**
         * Réinitialiser le formulaire
         */
        function resetFormulaire() {
            if (confirm('🗑️ Êtes-vous sûr de vouloir effacer tous les champs du formulaire ?')) {
                document.getElementById('formCarrelage').reset();
                document.getElementById('unite').value = 'm²';
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
         * Afficher des notifications toast
         */
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'info' ? '#16a085' : type === 'warning' ? '#f39c12' : type === 'success' ? '#27ae60' : '#e74c3c'};
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
            
            const icon = type === 'info' ? '🎨' : type === 'warning' ? '⚠️' : type === 'success' ? '✅' : '❌';
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
         * Raccourcis clavier spécialisés carrelage
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
                    calculerSurfacePertes();
                }
                
                // Alt + C = Calculer colle
                if (e.altKey && e.key === 'c') {
                    e.preventDefault();
                    calculerColle();
                }
                
                // Alt + J = Calculer joint
                if (e.altKey && e.key === 'j') {
                    e.preventDefault();
                    calculerJoint();
                }
                
                // Alt + N = Calculer nombre de carreaux
                if (e.altKey && e.key === 'n') {
                    e.preventDefault();
                    calculerNombreCarreaux();
                }
                
                // Alt + T = Estimer temps de pose
                if (e.altKey && e.key === 't') {
                    e.preventDefault();
                    estimerTempsPos();
                }
                
                // Alt + F = Focus format
                if (e.altKey && e.key === 'f') {
                    e.preventDefault();
                    document.getElementById('format').focus();
                    showToast('📏 Focus sur Format', 'info');
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
                    document.getElementById('formCarrelage').submit();
                }
            });
        }

        /**
         * Validation en temps réel des champs carrelage
         */
        function initValidationTempsReel() {
            const formatField = document.getElementById('format');
            const quantiteField = document.getElementById('quantite');
            const prixField = document.getElementById('prix_unitaire');
            const typeField = document.getElementById('type_carrelage');
            
            // Validation format
            formatField.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && !value.match(/^\d+x\d+(cm)?$/i)) {
                    this.style.borderColor = '#e74c3c';
                    this.title = 'Format invalide. Exemples: 30x30cm, 60x60';
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
                            background: #16a085;
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
            
            // Auto-estimation prix selon type
            typeField.addEventListener('change', function() {
                const designation = document.getElementById('designation').value;
                if (designation && !prixField.value) {
                    const estimation = estimerPrixCarrelage(designation);
                    if (estimation > 0) {
                        prixField.value = estimation;
                        calculerTotal();
                        showToast(`💰 Prix auto-estimé: ${estimation.toLocaleString()} FCFA/m²`, 'info');
                    }
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
         * Suggestions automatiques selon usage
         */
        function suggerFormatUsage() {
            const usage = document.getElementById('usage').value;
            const formatField = document.getElementById('format');
            const finitionField = document.getElementById('finition');
            
            if (usage && !formatField.value) {
                let formatSuggere = '';
                let finitionSuggeree = '';
                
                switch(usage) {
                    case 'Sol intérieur':
                        formatSuggere = '45x45cm';
                        finitionSuggeree = 'Mat';
                        break;
                    case 'Sol extérieur':
                        formatSuggere = '30x30cm';
                        finitionSuggeree = 'Antidérapant';
                        break;
                    case 'Mur salle de bain':
                        formatSuggere = '25x40cm';
                        finitionSuggeree = 'Brillant';
                        break;
                    case 'Mur cuisine':
                        formatSuggere = '20x20cm';
                        finitionSuggeree = 'Mat';
                        break;
                    case 'Piscine':
                        formatSuggere = '15x15cm';
                        finitionSuggeree = 'Antidérapant';
                        break;
                    case 'Terrasse':
                        formatSuggere = '60x60cm';
                        finitionSuggeree = 'Antidérapant';
                        break;
                }
                
                if (formatSuggere) {
                    formatField.value = formatSuggere;
                    finitionField.value = finitionSuggeree;
                    showToast(`💡 Format suggéré: ${formatSuggere}\nFinition: ${finitionSuggeree}`, 'info');
                }
            }
        }

        // ===== INITIALISATION AU CHARGEMENT =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎨 Module Carrelage GSN ProDevis360° initialisé');
            
            // Initialiser toutes les fonctionnalités
            initRaccourcisClavier();
            initAnimationsScroll();
            initValidationTempsReel();
            
            // Ajouter les événements pour les suggestions automatiques
            document.getElementById('usage').addEventListener('change', suggerFormatUsage);
            
            // Afficher les raccourcis clavier
            showToast(`⌨️ Raccourcis Carrelage:\n` +
                     `Alt+D = Désignation\n` +
                     `Alt+S = Surface + pertes\n` +
                     `Alt+C = Quantité colle\n` +
                     `Alt+J = Quantité joint\n` +
                     `Alt+N = Nombre carreaux\n` +
                     `Alt+T = Temps de pose\n` +
                     `Alt+F = Format\n` +
                     `Alt+U = Usage\n` +
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
                    <h5><i class="fas fa-calculator"></i> Calculateur Surface Avancé</h5>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 0.5rem; margin: 1rem 0;">
                        <input type="number" id="calc_longueur" placeholder="Longueur (m)" step="0.1" class="form-control">
                        <input type="number" id="calc_largeur" placeholder="Largeur (m)" step="0.1" class="form-control">
                        <select id="calc_pose" class="form-control">
                            <option value="droite">Pose droite</option>
                            <option value="diagonale">Pose diagonale</option>
                        </select>
                        <button type="button" class="btn btn-info btn-sm" onclick="calculerSurfacePertes()">
                            <i class="fas fa-calculator"></i> Calculer
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
            
            // Vérification cohérence des données existantes
            const elements = <?= json_encode($elements_carrelage ?? []) ?>;
            let alertesFormat = 0;
            
            elements.forEach(element => {
                const format = element.format;
                const usage = element.usage;
                
                if (format && usage) {
                    // Vérifications de cohérence format/usage
                    if (usage.includes('extérieur') && format.includes('10x10')) {
                        alertesFormat++;
                    }
                    if (usage.includes('piscine') && !format.includes('15x15')) {
                        alertesFormat++;
                    }
                }
            });
            
            if (alertesFormat > 0) {
                setTimeout(() => {
                    showToast(`⚠️ ${alertesFormat} élément(s) avec format possiblement inadapté.\n` +
                             `Vérifiez la cohérence format/usage.`, 'warning');
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
                border: 2px solid #16a085;
                margin-top: 1rem;
            }
            
            #calculateur-avance h5 {
                color: #16a085;
                margin-bottom: 0.75rem;
            }
        `;
        document.head.appendChild(styleSheet);
    </script>

</body>
</html>