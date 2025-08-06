<?php
// ===== MATERIAUXBASE.PHP - PJESA 1: PHP LOGIC & CONFIG =====
// VERSION UNIFORMISÉE GSN ProDevis360°
require_once 'functions.php';

// Konfigurimi i modulit aktual
$current_module = 'materiaux';

// Konfigurimi i moduleve për navigim dinamik
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

// Marrja dhe validimi i parametrave
$projet_id = secureGetParam('projet_id', 'int', 0);
$devis_id = secureGetParam('devis_id', 'int', 0);
$action = secureGetParam('action', 'string', '');
$element_id = secureGetParam('element_id', 'int', 0);

// Verifikimi i parametrave të detyrueshëm
if (!$projet_id || !$devis_id) {
    die('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Erreur : Parametrat projet_id dhe devis_id mungojnë.</div>');
}

// Navigimi dinamik
$navigation = getNavigationModules($modules_config, $current_module);

// Marrja e informacioneve të projektit dhe devisit
$projet_devis_info = getProjetDevisInfo($projet_id, $devis_id);
if (!$projet_devis_info) {
    die('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Erreur : Projekti ose devisi nuk gjendet.</div>');
}

// Variablat e shfaqjes
$message = '';
$message_type = '';

// Sugjerimet e specializuara për materialet bazë
$suggestions_materiaux = [
    // BETONI DHE AGREGATËT
    'Beton prêt à l\'emploi C25/30 dosé 350kg',
    'Beton prêt à l\'emploi C30/37 dosé 400kg',
    'Beton prêt à l\'emploi C35/45 dosé 450kg',
    'Beton fibré C25/30 avec fibres polypropylène',
    'Beton autoplaçant C30/37 haute fluidité',
    'Beton désactivé décoratif granulat apparent',
    'Beton imprimé coloré effet pavé ou pierre',
    'Mortier prêt gâchage 25kg tous usages',
    'Mortier de scellement rapide 25kg',
    'Mortier réfractaire haute température 25kg',
    
    // ÇIMENTO DHE LIDHËS
    'Ciment Portland CEM I 52.5 sac 35kg',
    'Ciment Portland CEM II/A-L 42.5 sac 35kg',
    'Ciment Portland CEM II/B-M 32.5 sac 35kg',
    'Ciment blanc CEM I 52.5 sac 25kg',
    'Ciment prompt naturel prise rapide 35kg',
    'Ciment alumineux fondu CA-25 sac 25kg',
    'Chaux hydraulique NHL 3.5 sac 35kg',
    'Chaux aérienne éteinte CL 90 sac 25kg',
    'Chaux vive en mottes sac 25kg',
    'Plâtre traditionnel gros sac 25kg',
    'Plâtre fin haute dureté sac 25kg',
    'Plâtre de Paris moulage sac 25kg',
    
    // AGREGATET DHE RËRA
    'Sable jaune 0/4 lavé criblé m³',
    'Sable blanc 0/2 extra-fin m³',
    'Sable rouge 0/4 naturel m³',
    'Sable de rivière 0/4 lavé m³',
    'Sable concassé 0/4 calcaire m³',
    'Gravillon 4/8 roulé lavé m³',
    'Gravillon 8/16 concassé calcaire m³',
    'Gravillon 16/25 concassé granite m³',
    'Gravier décoratif 8/16 blanc m³',
    'Gravier décoratif 4/8 rouge m³',
    'Gravier décoratif 8/16 noir basalte m³',
    'Ballast 20/40 pour drainage m³',
    'Tout-venant 0/31.5 compactable m³',
    'Grave 0/20 naturelle criblée m³',
    'Concassé 0/20 calcaire stabilisé m³',
    
    // BLLOQET DHE TULLAT
    'Bloc béton creux 15x20x50 NF',
    'Bloc béton creux 20x20x50 NF',
    'Bloc béton creux 25x20x50 NF',
    'Bloc béton plein 15x20x50 NF',
    'Bloc béton plein 20x20x50 NF',
    'Bloc à bancher 15x20x50 NF',
    'Bloc à bancher 20x20x50 NF',
    'Bloc cellulaire AAC 15x25x62.5',
    'Bloc cellulaire AAC 20x25x62.5',
    'Bloc cellulaire AAC 25x25x62.5',
    'Brique terre cuite 15x20x50',
    'Brique terre cuite 20x20x50',
    'Brique pleine rouge 5.5x10.5x22',
    'Brique creuse 15x20x50',
    'Brique réfractaire four 22x11x6',
    
    // PANELET DHE IZOLIMI
    'Panneau OSB3 12mm 250x125cm',
    'Panneau OSB3 18mm 250x125cm',
    'Panneau OSB3 22mm 250x125cm',
    'Panneau contreplaqué marine 15mm',
    'Panneau contreplaqué marine 18mm',
    'Panneau MDF médium 16mm 280x207',
    'Panneau MDF médium 19mm 280x207',
    'Panneau aggloméré P5 16mm 280x207',
    'Panneau aggloméré P5 19mm 280x207',
    'Panneau fibres-ciment 8mm extérieur',
    'Laine de verre 200mm R=5.0 m²',
    'Laine de verre 240mm R=6.0 m²',
    'Laine de roche 200mm R=5.5 m²',
    'Polystyrène expansé 100mm R=2.6 m²',
    'Polystyrène extrudé 120mm R=3.7 m²',
    
    // PRODUKTET KIMIKE
    'Adjuvant plastifiant béton 5L',
    'Adjuvant retardateur prise 5L',
    'Adjuvant accélérateur prise 5L',
    'Hydrofuge de masse 5L',
    'Résine époxy bi-composant 5kg',
    'Résine polyuréthane étanchéité 5kg',
    'Primaire d\'accrochage universel 5L',
    'Durcisseur de surface béton 5L',
    'Anti-gel béton -10°C bidon 5L',
    'Décoffrant béton biodégradable 5L',
    
    // GEOTEKSTILI DHE MEMBRANAT
    'Géotextile de séparation 200g/m²',
    'Géotextile de drainage 300g/m²',
    'Géomembrane EPDM 1.2mm étanchéité',
    'Géomembrane PVC 1.5mm bassin',
    'Film polyéthylène 200µ pare-vapeur',
    'Feutre bitumé 36S toiture froide',
    'Membrane EPDM toiture 1.2mm',
    'Membrane bitume SBS 4mm',
    'Pare-pluie respirant HPV 135g',
    'Écran sous-toiture 115g/m²',
    
    // ASHËSORË DHE VEGLA
    'Fer à béton HA 8mm barre 12m',
    'Fer à béton HA 10mm barre 12m',
    'Fer à béton HA 12mm barre 12m',
    'Treillis soudé ST25C maille 15x15',
    'Treillis soudé ST50C maille 15x15',
    'Chevron sapin 63x175mm long 4m',
    'Madrier sapin 63x225mm long 4m',
    'Poutrelle béton précontrainte 12cm',
    'Hourdis béton 16+4 entraxe 60cm',
    'Plancher collaborant bac acier'
];

// Lidhja me bazën e të dhënave
$conn = getDbConnection();

// Menaxhimi i veprimeve CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if ($action == 'ajouter') {
            // Marrja dhe validimi i të dhënave
            $designation = trim($_POST['designation'] ?? '');
            $quantite = floatval($_POST['quantite'] ?? 0);
            $unite = trim($_POST['unite'] ?? 'm³');
            $prix_unitaire = floatval($_POST['prix_unitaire'] ?? 0);
            $type_materiau = trim($_POST['type_materiau'] ?? '');
            $densite = floatval($_POST['densite'] ?? 0);
            $resistance = trim($_POST['resistance'] ?? '');
            $norme = trim($_POST['norme'] ?? '');
            $conditionnement = trim($_POST['conditionnement'] ?? '');
            $origine = trim($_POST['origine'] ?? '');
            
            // Validimet specifike për materialet
            if (empty($designation)) {
                throw new Exception("Emërtimi është i detyrueshëm.");
            }
            if ($quantite <= 0) {
                throw new Exception("Sasia duhet të jetë më e madhe se 0.");
            }
            if ($prix_unitaire < 0) {
                throw new Exception("Çmimi për njësi nuk mund të jetë negativ.");
            }
            
            // Validimi i densitetit nëse është dhënë
            if (!empty($densite) && ($densite < 0.1 || $densite > 10)) {
                throw new Exception("Densiteti duhet të jetë midis 0.1 dhe 10 t/m³.");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Futja në bazën e të dhënave
            $stmt = $conn->prepare("
                INSERT INTO materiaux_base (
                    projet_id, devis_id, designation, quantite, unite, 
                    prix_unitaire, total, type_materiau, densite, 
                    resistance, norme, conditionnement, origine, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param(
                "iisdsdssdssss", 
                $projet_id, $devis_id, $designation, $quantite, $unite,
                $prix_unitaire, $total, $type_materiau, $densite,
                $resistance, $norme, $conditionnement, $origine
            );
            
            if ($stmt->execute()) {
                // Përditësimi i përmbledhjes
                updateRecapitulatif($projet_id, $devis_id, 'materiaux');
                
                // Ruajtja në historik
                sauvegarderHistorique($projet_id, $devis_id, 'materiaux', 'Ajout', "Elementi u shtua: {$designation}");
                
                $message = "Elementi i materialeve u shtua me sukses!";
                $message_type = "success";
            } else {
                throw new Exception("Gabim gjatë shtimit: " . $conn->error);
            }
            
        } elseif ($action == 'modifier' && $element_id > 0) {
            // Marrja dhe validimi i të dhënave
            $designation = trim($_POST['designation'] ?? '');
            $quantite = floatval($_POST['quantite'] ?? 0);
            $unite = trim($_POST['unite'] ?? 'm³');
            $prix_unitaire = floatval($_POST['prix_unitaire'] ?? 0);
            $type_materiau = trim($_POST['type_materiau'] ?? '');
            $densite = floatval($_POST['densite'] ?? 0);
            $resistance = trim($_POST['resistance'] ?? '');
            $norme = trim($_POST['norme'] ?? '');
            $conditionnement = trim($_POST['conditionnement'] ?? '');
            $origine = trim($_POST['origine'] ?? '');
            
            // Të njëjtat validime si për shtimin
            if (empty($designation)) {
                throw new Exception("Emërtimi është i detyrueshëm.");
            }
            if ($quantite <= 0) {
                throw new Exception("Sasia duhet të jetë më e madhe se 0.");
            }
            if ($prix_unitaire < 0) {
                throw new Exception("Çmimi për njësi nuk mund të jetë negativ.");
            }
            
            if (!empty($densite) && ($densite < 0.1 || $densite > 10)) {
                throw new Exception("Densiteti duhet të jetë midis 0.1 dhe 10 t/m³.");
            }
            
            $total = $quantite * $prix_unitaire;
            
            // Përditësimi në bazën e të dhënave
            $stmt = $conn->prepare("
                UPDATE materiaux_base SET 
                    designation = ?, quantite = ?, unite = ?, prix_unitaire = ?, 
                    total = ?, type_materiau = ?, densite = ?, resistance = ?, 
                    norme = ?, conditionnement = ?, origine = ?, date_modification = NOW()
                WHERE id = ? AND projet_id = ? AND devis_id = ?
            ");
            
            $stmt->bind_param(
                "sdsdssdssssiii",
                $designation, $quantite, $unite, $prix_unitaire, $total,
                $type_materiau, $densite, $resistance, $norme, 
                $conditionnement, $origine, $element_id, $projet_id, $devis_id
            );
            
            if ($stmt->execute()) {
                // Përditësimi i përmbledhjes
                updateRecapitulatif($projet_id, $devis_id, 'materiaux');
                
                // Ruajtja në historik
                sauvegarderHistorique($projet_id, $devis_id, 'materiaux', 'Modification', "Elementi u modifikua: {$designation}");
                
                $message = "Elementi i materialeve u modifikua me sukses!";
                $message_type = "success";
            } else {
                throw new Exception("Gabim gjatë modifikimit: " . $conn->error);
            }
            
        } elseif ($action == 'supprimer' && $element_id > 0) {
            // Marrja e emërtimit para fshirjes
            $stmt_get = $conn->prepare("SELECT designation FROM materiaux_base WHERE id = ? AND projet_id = ? AND devis_id = ?");
            $stmt_get->bind_param("iii", $element_id, $projet_id, $devis_id);
            $stmt_get->execute();
            $result_get = $stmt_get->get_result();
            $element_data = $result_get->fetch_assoc();
            
            if ($element_data) {
                // Fshirja e elementit
                $stmt = $conn->prepare("DELETE FROM materiaux_base WHERE id = ? AND projet_id = ? AND devis_id = ?");
                $stmt->bind_param("iii", $element_id, $projet_id, $devis_id);
                
                if ($stmt->execute()) {
                    // Përditësimi i përmbledhjes
                    updateRecapitulatif($projet_id, $devis_id, 'materiaux');
                    
                    // Ruajtja në historik
                    sauvegarderHistorique($projet_id, $devis_id, 'materiaux', 'Suppression', "Elementi u fshi: {$element_data['designation']}");
                    
                    $message = "Elementi i materialeve u fshi me sukses!";
                    $message_type = "success";
                } else {
                    throw new Exception("Gabim gjatë fshirjes: " . $conn->error);
                }
            } else {
                throw new Exception("Elementi nuk gjendet për fshirje.");
            }
        }
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = "danger";
    }
}

// Marrja e elementeve të materialeve për shfaqje
$elements_materiaux = [];
$total_module = 0;

$stmt = $conn->prepare("
    SELECT id, designation, quantite, unite, prix_unitaire, total,
           type_materiau, densite, resistance, norme, conditionnement, origine,
           DATE_FORMAT(date_creation, '%d/%m/%Y %H:%i') as date_creation_fr,
           DATE_FORMAT(date_modification, '%d/%m/%Y %H:%i') as date_modification_fr
    FROM materiaux_base 
    WHERE projet_id = ? AND devis_id = ? 
    ORDER BY date_creation DESC
");

$stmt->bind_param("ii", $projet_id, $devis_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $elements_materiaux[] = $row;
    $total_module += $row['total'];
}

// Marrja e elementit për modifikim nëse nevojitet
$element_a_modifier = null;
if ($action == 'modifier' && $element_id > 0) {
    $stmt = $conn->prepare("
        SELECT * FROM materiaux_base 
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
    <title>Matériaux Base - <?= htmlspecialchars($projet_devis_info['nom_projet']) ?> | GSN ProDevis360°</title>
    
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
            
            /* Variables spécifiques matériaux */
            --concrete-gray: #95a5a6;
            --steel-blue: #34495e;
            --sand-yellow: #f1c40f;
            --stone-brown: #8b4513;
            --cement-light: #bdc3c7;
            --aggregate-dark: #2c3e50;
            --brick-red: #c0392b;
            --lime-green: #2ecc71;
            --iron-black: #1a1a1a;
            --wood-brown: #d2691e;
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
            background: var(--concrete-gray);
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

        /* ===== SUGGESTIONS MATÉRIAUX ===== */
        .suggestions-materiaux {
            background: linear-gradient(135deg, var(--concrete-gray) 0%, var(--steel-blue) 100%);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .suggestions-materiaux h4 {
            color: var(--secondary-white);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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

        /* ===== CALCULATEUR MATÉRIAUX ===== */
        .calculator-section {
            background: linear-gradient(135deg, var(--sand-yellow) 0%, #f39c12 100%);
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
        }

        .calc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.75rem;
            align-items: center;
        }

        .calc-input {
            padding: 0.5rem;
            border: 1px solid rgba(0,0,0,0.2);
            border-radius: 4px;
            background: rgba(255,255,255,0.9);
            font-size: 0.9rem;
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

        /* ===== BADGES SPÉCIALISÉS MATÉRIAUX ===== */
        .badge-type {
            background: var(--concrete-gray);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-densite {
            background: var(--steel-blue);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-resistance {
            background: var(--brick-red);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-norme {
            background: var(--lime-green);
            color: var(--secondary-white);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-origine {
            background: var(--wood-brown);
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
            color: var(--sand-yellow);
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

        @keyframes materialFlow {
            0% { transform: translateX(-100%) rotate(0deg); opacity: 0; }
            50% { transform: translateX(0%) rotate(180deg); opacity: 1; }
            100% { transform: translateX(0%) rotate(360deg); opacity: 1; }
        }

        .material-flow {
            animation: materialFlow 0.8s ease-out;
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
                        <i class="fas fa-cubes"></i>
                        Module Matériaux Base
                        <span class="module-badge">
                            <i class="fas fa-industry"></i>
                            BTP Fondamental
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

        <!-- ===== FORMULAIRE MATÉRIAUX BASE ===== -->
        <div class="form-section fade-in-up">
            <h2>
                <i class="fas fa-<?= $element_a_modifier ? 'edit' : 'plus-circle' ?>"></i>
                <?= $element_a_modifier ? 'Modifier l\'élément matériau' : 'Ajouter un élément matériau' ?>
            </h2>

            <!-- Suggestions Matériaux -->
            <div class="suggestions-materiaux">
                <h4>
                    <i class="fas fa-cubes"></i>
                    Suggestions Matériaux BTP Fondamentaux
                    <small>(Cliquez pour remplir automatiquement)</small>
                </h4>
                <div class="suggestions-grid">
                    <?php foreach ($suggestions_materiaux as $suggestion): ?>
                        <div class="suggestion-item" onclick="remplirSuggestion('<?= htmlspecialchars($suggestion, ENT_QUOTES) ?>')">
                            <?= htmlspecialchars($suggestion) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Calculateur Matériaux -->
            <div class="calculator-section">
                <h4>
                    <i class="fas fa-calculator"></i>
                    Calculateur Quantités Matériaux
                </h4>
                <div class="calc-grid">
                    <input type="number" id="calc_longueur" placeholder="Longueur (m)" class="calc-input" step="0.01">
                    <input type="number" id="calc_largeur" placeholder="Largeur (m)" class="calc-input" step="0.01">
                    <input type="number" id="calc_hauteur" placeholder="Hauteur (m)" class="calc-input" step="0.01">
                    <button type="button" class="btn btn-sm btn-info" onclick="calculerVolume()">
                        <i class="fas fa-cube"></i> Volume m³
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="calculerSurface()">
                        <i class="fas fa-square"></i> Surface m²
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="calculerBetonNecessaire()">
                        <i class="fas fa-truck-loading"></i> Béton nécessaire
                    </button>
                </div>
            </div>

            <form method="POST" action="" id="formMateriaux">
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
                               placeholder="Ex: Béton prêt à l'emploi C25/30 dosé 350kg"
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
                               placeholder="Ex: 15.5"
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
                            <option value="m³" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm³') ? 'selected' : '' ?>>Mètre cube (m³)</option>
                            <option value="m²" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'm²') ? 'selected' : '' ?>>Mètre carré (m²)</option>
                            <option value="ml" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'ml') ? 'selected' : '' ?>>Mètre linéaire (ml)</option>
                            <option value="t" <?= ($element_a_modifier && $element_a_modifier['unite'] === 't') ? 'selected' : '' ?>>Tonne (t)</option>
                            <option value="kg" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'kg') ? 'selected' : '' ?>>Kilogramme (kg)</option>
                            <option value="sac" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'sac') ? 'selected' : '' ?>>Sac</option>
                            <option value="palette" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'palette') ? 'selected' : '' ?>>Palette</option>
                            <option value="unité" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'unité') ? 'selected' : '' ?>>Unité</option>
                            <option value="L" <?= ($element_a_modifier && $element_a_modifier['unite'] === 'L') ? 'selected' : '' ?>>Litre (L)</option>
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

                <!-- Ligne 2 : Spécifications matériaux -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="type_materiau">
                            <i class="fas fa-industry"></i>
                            Type de matériau
                        </label>
                        <select id="type_materiau" name="type_materiau">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Béton" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Béton') ? 'selected' : '' ?>>Béton</option>
                            <option value="Ciment" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Ciment') ? 'selected' : '' ?>>Ciment</option>
                            <option value="Sable" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Sable') ? 'selected' : '' ?>>Sable</option>
                            <option value="Gravier" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Gravier') ? 'selected' : '' ?>>Gravier</option>
                            <option value="Bloc" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Bloc') ? 'selected' : '' ?>>Bloc béton</option>
                            <option value="Brique" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Brique') ? 'selected' : '' ?>>Brique</option>
                            <option value="Panneau" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Panneau') ? 'selected' : '' ?>>Panneau</option>
                            <option value="Isolant" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Isolant') ? 'selected' : '' ?>>Isolant</option>
                            <option value="Chaux" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Chaux') ? 'selected' : '' ?>>Chaux</option>
                            <option value="Plâtre" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Plâtre') ? 'selected' : '' ?>>Plâtre</option>
                            <option value="Mortier" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Mortier') ? 'selected' : '' ?>>Mortier</option>
                            <option value="Adjuvant" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Adjuvant') ? 'selected' : '' ?>>Adjuvant</option>
                            <option value="Géotextile" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Géotextile') ? 'selected' : '' ?>>Géotextile</option>
                            <option value="Membrane" <?= ($element_a_modifier && $element_a_modifier['type_materiau'] === 'Membrane') ? 'selected' : '' ?>>Membrane</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="densite">
                            <i class="fas fa-weight-hanging"></i>
                            Densité (t/m³)
                        </label>
                        <input type="number" 
                               id="densite" 
                               name="densite" 
                               value="<?= $element_a_modifier ? $element_a_modifier['densite'] : '' ?>"
                               placeholder="Ex: 2.4"
                               step="0.1"
                               min="0.1"
                               max="10">
                    </div>

                    <div class="form-group">
                        <label for="resistance">
                            <i class="fas fa-shield-alt"></i>
                            Résistance
                        </label>
                        <select id="resistance" name="resistance">
                            <option value="">-- Sélectionnez --</option>
                            <option value="C20/25" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'C20/25') ? 'selected' : '' ?>>C20/25 (20 MPa)</option>
                            <option value="C25/30" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'C25/30') ? 'selected' : '' ?>>C25/30 (25 MPa)</option>
                            <option value="C30/37" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'C30/37') ? 'selected' : '' ?>>C30/37 (30 MPa)</option>
                            <option value="C35/45" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'C35/45') ? 'selected' : '' ?>>C35/45 (35 MPa)</option>
                            <option value="C40/50" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'C40/50') ? 'selected' : '' ?>>C40/50 (40 MPa)</option>
                            <option value="CEM I 32.5" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'CEM I 32.5') ? 'selected' : '' ?>>CEM I 32.5</option>
                            <option value="CEM I 42.5" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'CEM I 42.5') ? 'selected' : '' ?>>CEM I 42.5</option>
                            <option value="CEM I 52.5" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'CEM I 52.5') ? 'selected' : '' ?>>CEM I 52.5</option>
                            <option value="B40" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'B40') ? 'selected' : '' ?>>B40 (bloc)</option>
                            <option value="B60" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'B60') ? 'selected' : '' ?>>B60 (bloc)</option>
                            <option value="B80" <?= ($element_a_modifier && $element_a_modifier['resistance'] === 'B80') ? 'selected' : '' ?>>B80 (bloc)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="norme">
                            <i class="fas fa-certificate"></i>
                            Norme
                        </label>
                        <select id="norme" name="norme">
                            <option value="">-- Sélectionnez --</option>
                            <option value="NF EN 206" <?= ($element_a_modifier && $element_a_modifier['norme'] === 'NF EN 206') ? 'selected' : '' ?>>NF EN 206 (Béton)</option>
                            <option value="NF EN 197-1" <?= ($element_a_modifier && $element_a_modifier['norme'] === 'NF EN 197-1') ? 'selected' : '' ?>>NF EN 197-1 (Ciment)</option>
                            <option value="NF EN 12620" <?= ($element_a_modifier && $element_a_modifier['norme'] === 'NF EN 12620') ? 'selected' : '' ?>>NF EN 12620 (Granulats)</option>
                            <option value="NF EN 771-1" <?= ($element_a_modifier && $element_a_modifier['norme'] === 'NF EN 771-1') ? 'selected' : '' ?>>NF EN 771-1 (Maçonnerie)</option>
                            <option value="NF P18-545" <?= ($element_a_modifier && $element_a_modifier['norme'] === 'NF P18-545') ? 'selected' : '' ?>>NF P18-545 (Granulats)</option>
                            <option value="CE" <?= ($element_a_modifier && $element_a_modifier['norme'] === 'CE') ? 'selected' : '' ?>>Marquage CE</option>
                            <option value="NF" <?= ($element_a_modifier && $element_a_modifier['norme'] === 'NF') ? 'selected' : '' ?>>Norme NF</option>
                            <option value="AFNOR" <?= ($element_a_modifier && $element_a_modifier['norme'] === 'AFNOR') ? 'selected' : '' ?>>AFNOR</option>
                        </select>
                    </div>
                </div>

                <!-- Ligne 3 : Conditionnement et origine -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="conditionnement">
                            <i class="fas fa-box"></i>
                            Conditionnement
                        </label>
                        <select id="conditionnement" name="conditionnement">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Vrac" <?= ($element_a_modifier && $element_a_modifier['conditionnement'] === 'Vrac') ? 'selected' : '' ?>>En vrac</option>
                            <option value="Sac 25kg" <?= ($element_a_modifier && $element_a_modifier['conditionnement'] === 'Sac 25kg') ? 'selected' : '' ?>>Sac 25kg</option>
                            <option value="Sac 35kg" <?= ($element_a_modifier && $element_a_modifier['conditionnement'] === 'Sac 35kg') ? 'selected' : '' ?>>Sac 35kg</option>
                            <option value="Big-bag 1t" <?= ($element_a_modifier && $element_a_modifier['conditionnement'] === 'Big-bag 1t') ? 'selected' : '' ?>>Big-bag 1t</option>
                            <option value="Palette" <?= ($element_a_modifier && $element_a_modifier['conditionnement'] === 'Palette') ? 'selected' : '' ?>>Palette</option>
                            <option value="Camion" <?= ($element_a_modifier && $element_a_modifier['conditionnement'] === 'Camion') ? 'selected' : '' ?>>Camion complet</option>
                            <option value="Toupie" <?= ($element_a_modifier && $element_a_modifier['conditionnement'] === 'Toupie') ? 'selected' : '' ?>>Toupie béton</option>
                            <option value="Bidon 5L" <?= ($element_a_modifier && $element_a_modifier['conditionnement'] === 'Bidon 5L') ? 'selected' : '' ?>>Bidon 5L</option>
                            <option value="Rouleau" <?= ($element_a_modifier && $element_a_modifier['conditionnement'] === 'Rouleau') ? 'selected' : '' ?>>Rouleau</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="origine">
                            <i class="fas fa-map-marker-alt"></i>
                            Origine
                        </label>
                        <select id="origine" name="origine">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Locale" <?= ($element_a_modifier && $element_a_modifier['origine'] === 'Locale') ? 'selected' : '' ?>>Production locale</option>
                            <option value="Régionale" <?= ($element_a_modifier && $element_a_modifier['origine'] === 'Régionale') ? 'selected' : '' ?>>Production régionale</option>
                            <option value="Nationale" <?= ($element_a_modifier && $element_a_modifier['origine'] === 'Nationale') ? 'selected' : '' ?>>Production nationale</option>
                            <option value="Importée" <?= ($element_a_modifier && $element_a_modifier['origine'] === 'Importée') ? 'selected' : '' ?>>Importée</option>
                            <option value="Recyclée" <?= ($element_a_modifier && $element_a_modifier['origine'] === 'Recyclée') ? 'selected' : '' ?>>Matériau recyclé</option>
                            <option value="Naturelle" <?= ($element_a_modifier && $element_a_modifier['origine'] === 'Naturelle') ? 'selected' : '' ?>>Carrière naturelle</option>
                            <option value="Artificielle" <?= ($element_a_modifier && $element_a_modifier['origine'] === 'Artificielle') ? 'selected' : '' ?>>Matériau artificiel</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-calculator"></i>
                            Calculs rapides
                        </label>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <button type="button" 
                                    class="btn btn-sm btn-info" 
                                    onclick="calculerPoidsMateriau()"
                                    title="Alt+P">
                                <i class="fas fa-weight"></i>
                                Poids auto
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
                            Informations
                        </label>
                        <div id="info-materiau" style="font-size: 0.85rem; color: var(--neutral-gray); line-height: 1.4;">
                            <i class="fas fa-lightbulb"></i> Sélectionnez un type de matériau pour voir les informations techniques
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
                            <a href="materiaux.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn btn-secondary ml-2">
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

        <!-- ===== TABLEAU DES ÉLÉMENTS MATÉRIAUX ===== -->
        <div class="table-container fade-in-up">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list"></i>
                    Éléments matériaux base
                    <span class="badge-type ml-2"><?= count($elements_materiaux) ?> élément(s)</span>
                </h3>
                <div class="table-actions">
                    <span class="total-amount">
                        <i class="fas fa-cubes"></i>
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
                            <th><i class="fas fa-weight-hanging"></i> Densité</th>
                            <th><i class="fas fa-shield-alt"></i> Résistance</th>
                            <th><i class="fas fa-certificate"></i> Norme</th>
                            <th><i class="fas fa-map-marker-alt"></i> Origine</th>
                            <th><i class="fas fa-euro-sign"></i> Total</th>
                            <th><i class="fas fa-calendar"></i> Créé le</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($elements_materiaux)): ?>
                            <tr>
                                <td colspan="13" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-cubes fa-3x mb-3 d-block"></i>
                                        <p>Aucun élément matériau ajouté pour ce devis.</p>
                                        <small>Utilisez le formulaire ci-dessus pour ajouter des matériaux.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($elements_materiaux as $element): ?>
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
                                    <td><span class="badge-type"><?= htmlspecialchars($element['unite']) ?></span></td>
                                    <td><strong><?= number_format($element['prix_unitaire'], 0, ',', ' ') ?></strong> FCFA</td>
                                    <td>
                                        <?php if (!empty($element['type_materiau'])): ?>
                                            <span class="badge-type">
                                                <i class="fas fa-industry"></i>
                                                <?= htmlspecialchars($element['type_materiau']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['densite']) && $element['densite'] > 0): ?>
                                            <span class="badge-densite">
                                                <i class="fas fa-weight-hanging"></i>
                                                <?= number_format($element['densite'], 1) ?> t/m³
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['resistance'])): ?>
                                            <span class="badge-resistance">
                                                <i class="fas fa-shield-alt"></i>
                                                <?= htmlspecialchars($element['resistance']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['norme'])): ?>
                                            <span class="badge-norme">
                                                <i class="fas fa-certificate"></i>
                                                <?= htmlspecialchars($element['norme']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($element['origine'])): ?>
                                            <span class="badge-origine">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?= htmlspecialchars($element['origine']) ?>
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
                                            <a href="materiaux.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&action=modifier&element_id=<?= $element['id'] ?>" 
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
                        <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=materiaux" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-clock"></i> Voir tout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TOTAUX MODULE MATÉRIAUX BASE ===== -->
        <div class="module-summary fade-in-up">
            <h3>
                <i class="fas fa-cubes"></i>
                Total Module Matériaux Base
            </h3>
            <div class="total-amount pulse-animation">
                <?= number_format($total_module, 0, ',', ' ') ?> FCFA
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Mis à jour automatiquement • <?= count($elements_materiaux) ?> élément(s)
                <?php if ($total_module > 0 && count($elements_materiaux) > 0): ?>
                    • Moyenne: <?= number_format($total_module / count($elements_materiaux), 0, ',', ' ') ?> FCFA/élément
                    • Volume total estimé: <?= number_format(array_sum(array_map(function($e) { 
                        return ($e['unite'] === 'm³' || $e['unite'] === 'm3') ? floatval($e['quantite']) : 0; 
                    }, $elements_materiaux)), 2) ?> m³
                <?php endif; ?>
            </small>
        </div>

    </div>

    <!-- ===== JAVASCRIPT SPÉCIALISÉ MATÉRIAUX BASE ===== -->
    <script>
        // ===== CONFIGURATION ET VARIABLES MATÉRIAUX BASE =====
        const PRIX_MATERIAUX = {
            // Granulats (prix par m³)
            'sable': { base: 35000, factor: 1.0, densite: 1.6 },
            'gravier': { base: 45000, factor: 1.2, densite: 1.7 },
            'tout_venant': { base: 25000, factor: 0.8, densite: 1.8 },
            'grave': { base: 40000, factor: 1.1, densite: 1.75 },
            'laterite': { base: 18000, factor: 0.6, densite: 1.9 },
            'galet': { base: 55000, factor: 1.5, densite: 1.6 },
            'pouzzolane': { base: 75000, factor: 2.0, densite: 0.9 },
            
            // Liants (prix par tonne)
            'ciment_portland': { base: 95000, factor: 1.0, densite: 1.5 },
            'ciment_cpj': { base: 88000, factor: 0.9, densite: 1.5 },
            'chaux_hydraulique': { base: 120000, factor: 1.3, densite: 1.0 },
            'platre': { base: 65000, factor: 0.7, densite: 0.8 },
            
            // Métaux (prix par tonne)
            'acier': { base: 950000, factor: 1.0, densite: 7.85 },
            'aluminum': { base: 2800000, factor: 3.0, densite: 2.7 },
            'cuivre': { base: 8500000, factor: 9.0, densite: 8.96 },
            'zinc': { base: 3200000, factor: 3.4, densite: 7.14 },
            
            // Bois (prix par m³)
            'bois_dur': { base: 850000, factor: 1.5, densite: 0.8 },
            'bois_tendre': { base: 450000, factor: 1.0, densite: 0.5 },
            'bois_exotique': { base: 1800000, factor: 3.5, densite: 0.9 },
            
            // Isolants (prix par m³)
            'laine_verre': { base: 120000, factor: 1.0, densite: 0.03 },
            'laine_roche': { base: 150000, factor: 1.3, densite: 0.04 },
            'polystyrene': { base: 180000, factor: 1.5, densite: 0.02 },
            'polyurethane': { base: 350000, factor: 3.0, densite: 0.04 }
        };

        const CONVERSIONS_UNITES = {
            // Facteurs de conversion vers unité de base
            'kg_vers_tonne': 0.001,
            'g_vers_kg': 0.001,
            'l_vers_m3': 0.001,
            'dm3_vers_m3': 0.001,
            'cm3_vers_m3': 0.000001,
            'm2_vers_m3': function(epaisseur_mm) { return epaisseur_mm / 1000; }
        };

        const DOSAGES_TYPES = {
            // Dosages béton (kg ciment / m³ béton)
            'beton_proprete': { ciment: 150, resistance: '5 MPa', usage: 'Propreté, arase' },
            'beton_arme': { ciment: 350, resistance: '25 MPa', usage: 'Structure courante' },
            'beton_haute_perf': { ciment: 450, resistance: '35 MPa', usage: 'Ouvrages spéciaux' },
            
            // Dosages mortier (kg ciment / m³ sable)
            'mortier_montage': { ciment: 300, resistance: '15 MPa', usage: 'Maçonnerie' },
            'mortier_enduit': { ciment: 250, resistance: '10 MPa', usage: 'Enduits' },
            'mortier_chape': { ciment: 400, resistance: '20 MPa', usage: 'Chapes' }
        };

        const NORMES_GRANULATS = {
            '0_4': { designation: 'Sable fin 0/4', usage: 'Mortier, enduit', prix_coeff: 1.1 },
            '0_6': { designation: 'Sable moyen 0/6', usage: 'Béton, chape', prix_coeff: 1.0 },
            '4_8': { designation: 'Gravillon 4/8', usage: 'Béton standard', prix_coeff: 1.2 },
            '8_16': { designation: 'Gravier 8/16', usage: 'Béton armé', prix_coeff: 1.3 },
            '16_25': { designation: 'Gravier 16/25', usage: 'Gros béton', prix_coeff: 1.4 },
            '0_31_5': { designation: 'Tout-venant 0/31.5', usage: 'Remblai, forme', prix_coeff: 0.8 }
        };

        // ===== FONCTIONS SPÉCIALISÉES MATÉRIAUX BASE =====

        /**
         * Calculer les quantités pour béton
         */
        function calculerQuantitesBeton() {
            const volume = parseFloat(document.getElementById('calc_volume').value) || 0;
            const dosage = document.getElementById('calc_dosage').value;
            const typeGranulat = document.getElementById('calc_granulat').value;
            
            if (volume > 0 && dosage) {
                const dosageInfo = DOSAGES_TYPES[dosage];
                
                if (dosageInfo) {
                    // Calculs selon formule de Dreux-Gorisse simplifiée
                    const ciment = volume * dosageInfo.ciment; // kg
                    const sable = volume * 700; // kg (densité 1400 kg/m³ × 0.5)
                    const gravier = volume * 1050; // kg (densité 1500 kg/m³ × 0.7)
                    const eau = volume * 175; // litres
                    
                    const resultat = `🧱 Quantités pour ${volume}m³ de béton (${dosage}):\n\n` +
                                   `• Ciment: ${ciment.toFixed(0)} kg (${(ciment/50).toFixed(1)} sacs 50kg)\n` +
                                   `• Sable 0/4: ${sable.toFixed(0)} kg (${(sable/1400).toFixed(2)} m³)\n` +
                                   `• Gravier 8/16: ${gravier.toFixed(0)} kg (${(gravier/1500).toFixed(2)} m³)\n` +
                                   `• Eau: ${eau.toFixed(0)} litres\n\n` +
                                   `📊 Résistance: ${dosageInfo.resistance}\n` +
                                   `🎯 Usage: ${dosageInfo.usage}`;
                    
                    // Remplir automatiquement si on est sur ciment
                    const designation = document.getElementById('designation').value.toLowerCase();
                    if (designation.includes('ciment')) {
                        document.getElementById('quantite').value = ciment;
                        document.getElementById('unite').value = 'kg';
                    } else if (designation.includes('sable')) {
                        document.getElementById('quantite').value = (sable/1400).toFixed(2);
                        document.getElementById('unite').value = 'm³';
                    } else if (designation.includes('gravier')) {
                        document.getElementById('quantite').value = (gravier/1500).toFixed(2);
                        document.getElementById('unite').value = 'm³';
                    }
                    
                    showToast(resultat, 'success');
                }
            } else {
                showToast('⚠️ Veuillez saisir le volume et sélectionner un dosage.', 'warning');
            }
        }

        /**
         * Convertir entre unités
         */
        function convertirUnites() {
            const quantite = parseFloat(document.getElementById('calc_quantite').value) || 0;
            const uniteSource = document.getElementById('calc_unite_source').value;
            const uniteCible = document.getElementById('calc_unite_cible').value;
            const densite = parseFloat(document.getElementById('calc_densite').value) || 1.5;
            
            if (quantite > 0 && uniteSource && uniteCible) {
                let resultat = 0;
                let explication = '';
                
                // Conversions de base
                if (uniteSource === 'kg' && uniteCible === 'tonne') {
                    resultat = quantite * 0.001;
                    explication = `${quantite} kg = ${resultat} tonne`;
                } else if (uniteSource === 'tonne' && uniteCible === 'kg') {
                    resultat = quantite * 1000;
                    explication = `${quantite} tonne = ${resultat} kg`;
                } else if (uniteSource === 'l' && uniteCible === 'm³') {
                    resultat = quantite * 0.001;
                    explication = `${quantite} L = ${resultat} m³`;
                } else if (uniteSource === 'm³' && uniteCible === 'l') {
                    resultat = quantite * 1000;
                    explication = `${quantite} m³ = ${resultat} L`;
                    
                // Conversions avec densité
                } else if (uniteSource === 'kg' && uniteCible === 'm³') {
                    resultat = quantite / (densite * 1000);
                    explication = `${quantite} kg ÷ ${densite * 1000} kg/m³ = ${resultat.toFixed(3)} m³`;
                } else if (uniteSource === 'm³' && uniteCible === 'kg') {
                    resultat = quantite * densite * 1000;
                    explication = `${quantite} m³ × ${densite * 1000} kg/m³ = ${resultat.toFixed(0)} kg`;
                } else if (uniteSource === 'tonne' && uniteCible === 'm³') {
                    resultat = quantite / densite;
                    explication = `${quantite} tonne ÷ ${densite} t/m³ = ${resultat.toFixed(3)} m³`;
                } else if (uniteSource === 'm³' && uniteCible === 'tonne') {
                    resultat = quantite * densite;
                    explication = `${quantite} m³ × ${densite} t/m³ = ${resultat.toFixed(3)} tonne`;
                } else {
                    explication = 'Conversion non supportée';
                }
                
                if (resultat > 0) {
                    document.getElementById('quantite').value = resultat.toFixed(3);
                    document.getElementById('unite').value = uniteCible;
                    
                    showToast(`🔄 Conversion effectuée:\n${explication}\n\n` +
                             `Densité utilisée: ${densite} t/m³`, 'success');
                } else {
                    showToast('❌ ' + explication, 'warning');
                }
            } else {
                showToast('⚠️ Veuillez remplir tous les champs de conversion.', 'warning');
            }
        }

        /**
         * Calculer le coût de transport
         */
        function calculerTransport() {
            const quantite = parseFloat(document.getElementById('calc_transport_qty').value) || 0;
            const unite = document.getElementById('calc_transport_unite').value;
            const distance = parseFloat(document.getElementById('calc_distance').value) || 0;
            const typeCamion = document.getElementById('calc_camion').value;
            
            if (quantite > 0 && distance > 0 && typeCamion) {
                const capacites = {
                    'petit': { volume: 8, poids: 5, prix_km: 1200 },    // m³, tonnes, FCFA/km
                    'moyen': { volume: 15, poids: 10, prix_km: 1800 },
                    'grand': { volume: 25, poids: 20, prix_km: 2500 },
                    'semi': { volume: 40, poids: 35, prix_km: 3500 }
                };
                
                const camion = capacites[typeCamion];
                let voyages = 0;
                
                // Calculer nombre de voyages selon contrainte limitante
                if (unite === 'm³') {
                    voyages = Math.ceil(quantite / camion.volume);
                } else if (unite === 'tonne' || unite === 'kg') {
                    const poids = unite === 'kg' ? quantite / 1000 : quantite;
                    voyages = Math.ceil(poids / camion.poids);
                } else {
                    voyages = Math.ceil(quantite / camion.volume); // Par défaut volume
                }
                
                const coutTransport = voyages * distance * 2 * camion.prix_km; // Aller-retour
                const coutParUnite = coutTransport / quantite;
                
                showToast(`🚛 Coût transport calculé:\n\n` +
                         `${voyages} voyage(s) camion ${typeCamion}\n` +
                         `Distance: ${distance} km (aller-retour: ${distance * 2} km)\n` +
                         `Coût total: ${coutTransport.toLocaleString()} FCFA\n` +
                         `Coût par ${unite}: ${coutParUnite.toLocaleString()} FCFA\n\n` +
                         `Capacité camion: ${camion.volume}m³ / ${camion.poids}t`, 'info');
            } else {
                showToast('⚠️ Veuillez remplir quantité, distance et type de camion.', 'warning');
            }
        }

        /**
         * Optimiser le mélange granulaire
         */
        function optimiserMelange() {
            const volume = parseFloat(document.getElementById('calc_volume_melange').value) || 0;
            const usage = document.getElementById('calc_usage_melange').value;
            
            if (volume > 0 && usage) {
                let melange = {};
                
                switch(usage) {
                    case 'beton_arme':
                        melange = {
                            'Sable 0/4': { proportion: 35, prix_m3: 35000 },
                            'Gravier 8/16': { proportion: 40, prix_m3: 45000 },
                            'Gravier 16/25': { proportion: 25, prix_m3: 50000 }
                        };
                        break;
                        
                    case 'beton_proprete':
                        melange = {
                            'Sable 0/6': { proportion: 45, prix_m3: 32000 },
                            'Gravier 4/8': { proportion: 35, prix_m3: 40000 },
                            'Gravier 8/16': { proportion: 20, prix_m3: 45000 }
                        };
                        break;
                        
                    case 'remblai':
                        melange = {
                            'Tout-venant 0/31.5': { proportion: 60, prix_m3: 25000 },
                            'Sable 0/4': { proportion: 25, prix_m3: 35000 },
                            'Gravier 8/16': { proportion: 15, prix_m3: 45000 }
                        };
                        break;
                        
                    case 'chape':
                        melange = {
                            'Sable 0/4': { proportion: 75, prix_m3: 35000 },
                            'Sable 0/2': { proportion: 25, prix_m3: 38000 }
                        };
                        break;
                }
                
                let resultat = `📊 Mélange optimisé pour ${volume}m³ - ${usage}:\n\n`;
                let coutTotal = 0;
                
                Object.keys(melange).forEach(materiau => {
                    const info = melange[materiau];
                    const quantiteM3 = volume * info.proportion / 100;
                    const cout = quantiteM3 * info.prix_m3;
                    coutTotal += cout;
                    
                    resultat += `• ${materiau}: ${quantiteM3.toFixed(2)}m³ (${info.proportion}%) - ${cout.toLocaleString()} FCFA\n`;
                });
                
                resultat += `\n💰 Coût total: ${coutTotal.toLocaleString()} FCFA`;
                resultat += `\n📈 Prix moyen: ${(coutTotal/volume).toLocaleString()} FCFA/m³`;
                
                showToast(resultat, 'success');
            } else {
                showToast('⚠️ Veuillez saisir le volume et l\'usage.', 'warning');
            }
        }

        /**
         * Estimation prix matériaux
         */
        function calculerEstimationMateriaux() {
            const designation = document.getElementById('designation').value.toLowerCase();
            const quantite = parseFloat(document.getElementById('quantite').value) || 1;
            const unite = document.getElementById('unite').value;
            
            let prixUnitaire = 0;
            let materiauTrouve = false;
            
            // Rechercher dans la base de prix
            Object.keys(PRIX_MATERIAUX).forEach(materiau => {
                if (designation.includes(materiau.replace('_', ' ')) || 
                    designation.includes(materiau)) {
                    
                    const info = PRIX_MATERIAUX[materiau];
                    prixUnitaire = info.base * info.factor;
                    materiauTrouve = true;
                    
                    // Ajustements selon l'unité
                    if (unite === 'kg' && materiau.includes('ciment')) {
                        prixUnitaire = prixUnitaire / 1000; // Prix tonne vers kg
                    } else if (unite === 'sac' && materiau.includes('ciment')) {
                        prixUnitaire = (prixUnitaire / 1000) * 50; // Prix sac 50kg
                    }
                }
            });
            
            // Rechercher dans les granulats normalisés
            if (!materiauTrouve) {
                Object.keys(NORMES_GRANULATS).forEach(norme => {
                    const info = NORMES_GRANULATS[norme];
                    if (designation.includes(norme.replace('_', '/')) || 
                        designation.includes(info.designation.toLowerCase())) {
                        
                        prixUnitaire = 40000 * info.prix_coeff; // Prix base granulat
                        materiauTrouve = true;
                    }
                });
            }
            
            // Prix par défaut selon type de matériau deviné
            if (!materiauTrouve) {
                if (designation.includes('ciment')) {
                    prixUnitaire = unite === 'kg' ? 95 : unite === 'sac' ? 4750 : 95000;
                } else if (designation.includes('sable')) {
                    prixUnitaire = 35000;
                } else if (designation.includes('gravier')) {
                    prixUnitaire = 45000;
                } else if (designation.includes('béton')) {
                    prixUnitaire = 85000;
                } else if (designation.includes('acier')) {
                    prixUnitaire = unite === 'kg' ? 950 : 950000;
                } else if (designation.includes('bois')) {
                    prixUnitaire = 450000;
                } else {
                    prixUnitaire = 25000; // Prix générique
                }
            }
            
            if (prixUnitaire > 0) {
                document.getElementById('prix_unitaire').value = Math.round(prixUnitaire);
                
                const total = prixUnitaire * quantite;
                showToast(`💰 Prix estimé: ${prixUnitaire.toLocaleString()} FCFA/${unite}\n` +
                         `📊 Total: ${total.toLocaleString()} FCFA\n` +
                         `${materiauTrouve ? '✅ Matériau reconnu' : '⚠️ Estimation générique'}`, 'info');
                
                // Animation
                const prixField = document.getElementById('prix_unitaire');
                prixField.style.background = 'linear-gradient(135deg, #e8f5e8 0%, #ffffff 100%)';
                setTimeout(() => prixField.style.background = '', 1500);
            } else {
                showToast('❓ Matériau non reconnu.\nVeuillez saisir le prix manuellement.', 'warning');
            }
        }

        /**
         * Calculer les pertes et marges
         */
        function calculerPertes() {
            const quantiteNette = parseFloat(document.getElementById('quantite').value) || 0;
            const typeMateriau = document.getElementById('type_materiau').value;
            const typeChantier = document.getElementById('type_chantier').value;
            
            if (quantiteNette > 0) {
                // Pourcentages de pertes selon matériau et type de chantier
                const pertes = {
                    'granulats': { 'neuf': 5, 'renovation': 8, 'difficile': 12 },
                    'ciment': { 'neuf': 2, 'renovation': 5, 'difficile': 8 },
                    'bois': { 'neuf': 10, 'renovation': 15, 'difficile': 20 },
                    'acier': { 'neuf': 3, 'renovation': 6, 'difficile': 10 },
                    'isolant': { 'neuf': 8, 'renovation': 12, 'difficile': 18 },
                    'autre': { 'neuf': 7, 'renovation': 10, 'difficile': 15 }
                };
                
                const pourcentagePerte = pertes[typeMateriau || 'autre'][typeChantier || 'neuf'];
                const quantitePerdue = quantiteNette * pourcentagePerte / 100;
                const quantiteTotale = quantiteNette + quantitePerdue;
                
                document.getElementById('quantite').value = quantiteTotale.toFixed(2);
                
                showToast(`📈 Calcul avec pertes appliqué:\n\n` +
                         `Quantité nette: ${quantiteNette.toFixed(2)}\n` +
                         `Pertes ${pourcentagePerte}%: ${quantitePerdue.toFixed(2)}\n` +
                         `Quantité totale: ${quantiteTotale.toFixed(2)}\n\n` +
                         `Type: ${typeMateriau} - Chantier: ${typeChantier}`, 'success');
            } else {
                showToast('⚠️ Veuillez d\'abord saisir une quantité nette.', 'warning');
            }
        }

        /**
         * Analyser la composition d'un mélange
         */
        function analyserComposition() {
            const elements = <?= json_encode($elements_materiaux) ?>;
            
            if (elements.length === 0) {
                showToast('📋 Aucun matériau pour analyser la composition', 'warning');
                return;
            }
            
            // Grouper par catégories
            const categories = {
                'Granulats': ['sable', 'gravier', 'galet', 'laterite', 'tout', 'grave'],
                'Liants': ['ciment', 'chaux', 'plâtre'],
                'Métaux': ['acier', 'fer', 'aluminum', 'cuivre', 'zinc'],
                'Bois': ['bois', 'planche', 'madrier', 'chevron'],
                'Isolants': ['laine', 'polystyrène', 'polyuréthane'],
                'Autres': []
            };
            
            let composition = {};
            let valeurTotale = 0;
            let volumeTotal = 0;
            
            elements.forEach(element => {
                const designation = element.designation.toLowerCase();
                const total = parseFloat(element.total);
                const quantite = parseFloat(element.quantite);
                const unite = element.unite;
                
                let categorieFound = false;
                
                Object.keys(categories).forEach(categorie => {
                    if (!categorieFound && categorie !== 'Autres') {
                        categories[categorie].forEach(mot => {
                            if (designation.includes(mot)) {
                                if (!composition[categorie]) composition[categorie] = { valeur: 0, elements: 0, volume: 0 };
                                composition[categorie].valeur += total;
                                composition[categorie].elements += 1;
                                
                                if (unite === 'm³' || unite === 'm3') {
                                    composition[categorie].volume += quantite;
                                }
                                
                                categorieFound = true;
                            }
                        });
                    }
                });
                
                if (!categorieFound) {
                    if (!composition['Autres']) composition['Autres'] = { valeur: 0, elements: 0, volume: 0 };
                    composition['Autres'].valeur += total;
                    composition['Autres'].elements += 1;
                }
                
                valeurTotale += total;
                if (unite === 'm³' || unite === 'm3') {
                    volumeTotal += quantite;
                }
            });
            
            let resultat = `📊 ANALYSE COMPOSITION MATÉRIAUX\n\n`;
            
            Object.keys(composition).forEach(categorie => {
                const info = composition[categorie];
                const pourcentage = (info.valeur / valeurTotale * 100).toFixed(1);
                
                resultat += `${categorie}: ${pourcentage}% (${info.elements} élément(s))\n`;
                resultat += `  Valeur: ${info.valeur.toLocaleString()} FCFA\n`;
                if (info.volume > 0) {
                    resultat += `  Volume: ${info.volume.toFixed(2)} m³\n`;
                }
                resultat += '\n';
            });
            
            resultat += `💰 Valeur totale: ${valeurTotale.toLocaleString()} FCFA\n`;
            if (volumeTotal > 0) {
                resultat += `📦 Volume total: ${volumeTotal.toFixed(2)} m³\n`;
                resultat += `📈 Prix moyen: ${(valeurTotale/volumeTotal).toLocaleString()} FCFA/m³`;
            }
            
            console.log(resultat);
            showToast('📊 Analyse générée - voir console pour détails complets', 'info');
        }

        /**
         * Remplir suggestion matériaux
         */
        function remplirSuggestion(suggestion) {
            const designationField = document.getElementById('designation');
            const quantiteField = document.getElementById('quantite');
            const uniteField = document.getElementById('unite');
            const prixField = document.getElementById('prix_unitaire');
            const typeField = document.getElementById('type_materiau');
            const categorieField = document.getElementById('categorie');
            const densiteField = document.getElementById('densite');
            
            designationField.value = suggestion;
            
            const sug = suggestion.toLowerCase();
            
            // Déterminer le type et la catégorie
            if (sug.includes('sable') || sug.includes('gravier') || sug.includes('galet')) {
                if (typeField) typeField.value = 'granulats';
                if (categorieField) categorieField.value = 'Granulats';
                quantiteField.value = '15';
                uniteField.value = 'm³';
                if (densiteField) densiteField.value = '1.6';
                
            } else if (sug.includes('ciment') || sug.includes('chaux') || sug.includes('plâtre')) {
                if (typeField) typeField.value = 'ciment';
                if (categorieField) categorieField.value = 'Liants';
                if (sug.includes('sac')) {
                    quantiteField.value = '20';
                    uniteField.value = 'sac';
                } else {
                    quantiteField.value = '1';
                    uniteField.value = 'tonne';
                }
                if (densiteField) densiteField.value = '1.5';
                
            } else if (sug.includes('acier') || sug.includes('fer') || sug.includes('métal')) {
                if (typeField) typeField.value = 'acier';
                if (categorieField) categorieField.value = 'Métaux';
                quantiteField.value = '500';
                uniteField.value = 'kg';
                if (densiteField) densiteField.value = '7.85';
                
            } else if (sug.includes('bois') || sug.includes('madrier') || sug.includes('planche')) {
                if (typeField) typeField.value = 'bois';
                if (categorieField) categorieField.value = 'Bois';
                quantiteField.value = '2';
                uniteField.value = 'm³';
                if (densiteField) densiteField.value = '0.6';
                
            } else if (sug.includes('laine') || sug.includes('isolant') || sug.includes('polystyrène')) {
                if (typeField) typeField.value = 'isolant';
                if (categorieField) categorieField.value = 'Isolants';
                quantiteField.value = '50';
                uniteField.value = 'm²';
                if (densiteField) densiteField.value = '0.03';
                
            } else {
                if (typeField) typeField.value = 'autre';
                if (categorieField) categorieField.value = 'Autres';
                quantiteField.value = '10';
                uniteField.value = 'unité';
                if (densiteField) densiteField.value = '1.0';
            }
            
            // Estimation prix
            calculerEstimationMateriaux();
            
            // Animation
            designationField.style.background = 'linear-gradient(135deg, #f0f8ff 0%, #ffffff 100%)';
            setTimeout(() => designationField.style.background = '', 1000);
            
            quantiteField.focus();
            quantiteField.select();
        }

        /**
         * Réinitialiser le formulaire
         */
        function resetFormulaire() {
            if (confirm('🗑️ Êtes-vous sûr de vouloir effacer tous les champs du formulaire ?')) {
                document.getElementById('formMateriaux').reset();
                document.getElementById('unite').value = 'm³';
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
                       `📦 Élément: ${designation}\n` +
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
         * Raccourcis clavier matériaux
         */
        function initRaccourcisClavier() {
            document.addEventListener('keydown', function(e) {
                // Alt + D = Focus désignation
                if (e.altKey && e.key === 'd') {
                    e.preventDefault();
                    document.getElementById('designation').focus();
                    showToast('🎯 Focus sur Désignation', 'info');
                }
                
                // Alt + B = Calculer béton
                if (e.altKey && e.key === 'b') {
                    e.preventDefault();
                    calculerQuantitesBeton();
                }
                
                // Alt + C = Convertir unités
                if (e.altKey && e.key === 'c') {
                    e.preventDefault();
                    convertirUnites();
                }
                
                // Alt + T = Calculer transport
                if (e.altKey && e.key === 't') {
                    e.preventDefault();
                    calculerTransport();
                }
                
                // Alt + M = Optimiser mélange
                if (e.altKey && e.key === 'm') {
                    e.preventDefault();
                    optimiserMelange();
                }
                
                // Alt + P = Estimation prix
                if (e.altKey && e.key === 'p') {
                    e.preventDefault();
                    calculerEstimationMateriaux();
                }
                
                // Alt + L = Calculer pertes
                if (e.altKey && e.key === 'l') {
                    e.preventDefault();
                    calculerPertes();
                }
                
                // Alt + A = Analyser composition
                if (e.altKey && e.key === 'a') {
                    e.preventDefault();
                    analyserComposition();
                }
                
                // Ctrl + Entrée = Soumettre formulaire
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('formMateriaux').submit();
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
                background: ${type === 'info' ? 'var(--accent-blue)' : type === 'warning' ? 'var(--concrete-gray)' : type === 'success' ? 'var(--materials-primary)' : 'var(--accent-red)'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-medium);
                z-index: 9999;
                max-width: 400px;
                white-space: pre-line;
                animation: slideInRight 0.4s ease-out;
            `;
            
            const icon = type === 'info' ? '📦' : type === 'warning' ? '⚠️' : type === 'success' ? '✅' : '❌';
            toast.innerHTML = `<strong>${icon}</strong> ${message}`;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.4s ease-out';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 400);
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
         * Validation temps réel
         */
        function initValidationTempsReel() {
            const densiteField = document.getElementById('densite');
            const quantiteField = document.getElementById('quantite');
            const prixField = document.getElementById('prix_unitaire');
            
            // Validation densité
            if (densiteField) {
                densiteField.addEventListener('input', function() {
                    const value = parseFloat(this.value);
                    if (value && (value < 0.01 || value > 20)) {
                        this.style.borderColor = 'var(--accent-red)';
                        this.title = 'Densité doit être entre 0.01 et 20 t/m³';
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
                            background: var(--materials-primary);
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
            console.log('📦 Module Matériaux Base GSN ProDevis360° initialisé');
            
            // Initialiser toutes les fonctionnalités
            initRaccourcisClavier();
            initAnimationsScroll();
            initValidationTempsReel();
            
            // Afficher les raccourcis clavier
            showToast(`⌨️ Raccourcis disponibles:\n` +
                     `Alt+D = Désignation\n` +
                     `Alt+B = Calculer béton\n` +
                     `Alt+C = Convertir unités\n` +
                     `Alt+T = Transport\n` +
                     `Alt+M = Mélange\n` +
                     `Alt+P = Prix estimation\n` +
                     `Alt+L = Calculer pertes\n` +
                     `Alt+A = Analyser composition\n` +
                     `Ctrl+Entrée = Envoyer`, 'info');
            
            // Focus automatique
            const firstField = document.getElementById('designation');
            if (firstField && !firstField.value) {
                setTimeout(() => firstField.focus(), 500);
            }
            
            // Analyse des éléments existants
            const elements = <?= json_encode($elements_materiaux) ?>;
            let volumeTotal = 0;
            let poidTotal = 0;
            
            elements.forEach(element => {
                const quantite = parseFloat(element.quantite) || 0;
                const unite = element.unite || '';
                const densite = parseFloat(element.densite) || 1.5;
                
                if (unite === 'm³' || unite === 'm3') {
                    volumeTotal += quantite;
                    poidTotal += quantite * densite * 1000; // kg
                } else if (unite === 'tonne') {
                    poidTotal += quantite * 1000; // kg
                    volumeTotal += quantite / densite;
                } else if (unite === 'kg') {
                    poidTotal += quantite;
                    volumeTotal += quantite / (densite * 1000);
                }
            });
            
            if (volumeTotal > 0 || poidTotal > 0) {
                setTimeout(() => {
                    showToast(`📊 Récapitulatif matériaux:\n` +
                             `Volume total: ${volumeTotal.toFixed(2)} m³\n` +
                             `Poids total: ${poidTotal.toFixed(0)} kg (${(poidTotal/1000).toFixed(2)} tonnes)\n` +
                             `${elements.length} matériau(x) référencé(s)`, 'info');
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