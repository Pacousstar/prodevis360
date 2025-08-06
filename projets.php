<?php
// ===== PROJETS.PHP - VERSION ULTRA SÉCURISÉE SANS WARNINGS =====
// Correction complète de tous les warnings et erreurs

// Activation des erreurs pour diagnostic (désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';

// Fonction utilitaire pour éviter les warnings
function safeGet($array, $key, $default = '') {
    return isset($array[$key]) && $array[$key] !== null ? $array[$key] : $default;
}

// Fonction pour formater les dates en sécurité
function safeDateFormat($date, $format = 'd/m/Y') {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return null;
    }
    try {
        return date($format, strtotime($date));
    } catch (Exception $e) {
        return null;
    }
}

// Connexion à la base de données avec gestion d'erreur
try {
    $pdo = getDbConnection();
} catch (Exception $e) {
    die('<div style="padding: 20px; background: #f8d7da; color: #721c24; border-radius: 5px; margin: 20px;">
        <h2>❌ Erreur de connexion</h2>
        <p>Impossible de se connecter à la base de données.</p>
        <p><strong>Détails:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
        <p><a href="index.php">← Retour à l\'accueil</a></p>
    </div>');
}

// Variables d'affichage
$message = '';
$message_type = '';

// Gestion des actions
$action = secureGetParam('action', 'string', '');
$projet_id = secureGetParam('projet_id', 'int', 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if ($action == 'ajouter') {
            // Récupération sécurisée des données POST
            $nom = trim(safeGet($_POST, 'nom'));
            $description = trim(safeGet($_POST, 'description'));
            $client = trim(safeGet($_POST, 'client'));
            $adresse = trim(safeGet($_POST, 'adresse'));
            $date_debut = safeGet($_POST, 'date_debut') ?: null;
            $date_fin_prevue = safeGet($_POST, 'date_fin_prevue') ?: null;
            $budget_previsionnel = floatval(safeGet($_POST, 'budget_previsionnel', 0));
            $statut = safeGet($_POST, 'statut', 'En planification');
            
            if (empty($nom)) {
                throw new Exception("Le nom du projet est obligatoire.");
            }
            
            // Validation des dates
            if ($date_debut && !strtotime($date_debut)) {
                $date_debut = null;
            }
            if ($date_fin_prevue && !strtotime($date_fin_prevue)) {
                $date_fin_prevue = null;
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO projets (
                    nom, description, client, adresse, date_debut, 
                    date_fin_prevue, budget_previsionnel, statut, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([
                $nom, $description, $client, $adresse, 
                $date_debut, $date_fin_prevue, $budget_previsionnel, $statut
            ])) {
                $message = "Projet '$nom' créé avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de la création du projet.");
            }
            
        } elseif ($action == 'supprimer' && $projet_id > 0) {
            // Vérifier d'abord si le projet existe
            $stmt = $pdo->prepare("SELECT nom FROM projets WHERE id = ?");
            $stmt->execute([$projet_id]);
            $projet_nom = $stmt->fetchColumn();
            
            if ($projet_nom) {
                $stmt = $pdo->prepare("DELETE FROM projets WHERE id = ?");
                if ($stmt->execute([$projet_id])) {
                    $message = "Projet '$projet_nom' supprimé avec succès !";
                    $message_type = "success";
                } else {
                    throw new Exception("Erreur lors de la suppression du projet.");
                }
            } else {
                throw new Exception("Projet introuvable.");
            }
        }
        
        // Redirection pour éviter la resoumission du formulaire
        if (!empty($message)) {
            header("Location: projets.php?msg=" . urlencode($message) . "&type=" . $message_type);
            exit();
        }
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = "danger";
    }
}

// Récupération des messages depuis l'URL (après redirection)
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $message_type = safeGet($_GET, 'type', 'info');
}

// Récupération sécurisée des projets
$projets = [];
try {
    // Tentative avec requête complète
    $stmt = $pdo->query("
        SELECT 
            p.id,
            p.nom,
            p.description,
            p.client,
            p.adresse,
            p.date_creation,
            p.date_debut,
            p.date_fin_prevue,
            p.budget_previsionnel,
            p.statut,
            COUNT(DISTINCT d.id) as nb_devis,
            COALESCE(SUM(r.total_general), 0) as total_projets
        FROM projets p
        LEFT JOIN devis d ON p.id = d.projet_id
        LEFT JOIN recapitulatif r ON d.id = r.devis_id AND d.projet_id = r.projet_id
        GROUP BY p.id, p.nom, p.description, p.client, p.adresse, p.date_creation, p.date_debut, p.date_fin_prevue, p.budget_previsionnel, p.statut
        ORDER BY p.date_creation DESC
    ");
    
    $projets_bruts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Traitement sécurisé de chaque projet
    foreach ($projets_bruts as $projet_brut) {
        $projets[] = [
            'id' => intval(safeGet($projet_brut, 'id', 0)),
            'nom' => safeGet($projet_brut, 'nom', 'Projet sans nom'),
            'description' => safeGet($projet_brut, 'description', ''),
            'client' => safeGet($projet_brut, 'client', 'Client non défini'),
            'adresse' => safeGet($projet_brut, 'adresse', 'Adresse non renseignée'),
            'date_creation' => safeGet($projet_brut, 'date_creation'),
            'date_debut' => safeGet($projet_brut, 'date_debut'),
            'date_fin_prevue' => safeGet($projet_brut, 'date_fin_prevue'),
            'budget_previsionnel' => floatval(safeGet($projet_brut, 'budget_previsionnel', 0)),
            'statut' => safeGet($projet_brut, 'statut', 'En planification'),
            'nb_devis' => intval(safeGet($projet_brut, 'nb_devis', 0)),
            'total_projets' => floatval(safeGet($projet_brut, 'total_projets', 0)),
            // Formatage sécurisé des dates
            'date_creation_fr' => safeDateFormat(safeGet($projet_brut, 'date_creation')) ?: date('d/m/Y'),
            'date_debut_fr' => safeDateFormat(safeGet($projet_brut, 'date_debut')),
            'date_fin_prevue_fr' => safeDateFormat(safeGet($projet_brut, 'date_fin_prevue'))
        ];
    }
    
} catch (PDOException $e) {
    // Fallback avec requête simplifiée
    try {
        $stmt = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC");
        $projets_simples = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($projets_simples as $projet_simple) {
            $projets[] = [
                'id' => intval(safeGet($projet_simple, 'id', 0)),
                'nom' => safeGet($projet_simple, 'nom', 'Projet sans nom'),
                'description' => safeGet($projet_simple, 'description', ''),
                'client' => safeGet($projet_simple, 'client', 'Client non défini'),
                'adresse' => safeGet($projet_simple, 'adresse', 'Adresse non renseignée'),
                'statut' => safeGet($projet_simple, 'statut', 'En planification'),
                'budget_previsionnel' => floatval(safeGet($projet_simple, 'budget_previsionnel', 0)),
                'date_creation_fr' => safeDateFormat(safeGet($projet_simple, 'date_creation')) ?: date('d/m/Y'),
                'date_debut_fr' => safeDateFormat(safeGet($projet_simple, 'date_debut')),
                'date_fin_prevue_fr' => safeDateFormat(safeGet($projet_simple, 'date_fin_prevue')),
                'nb_devis' => 0,
                'total_projets' => 0
            ];
        }
    } catch (PDOException $e2) {
        $message = "Erreur lors de la récupération des projets : " . $e2->getMessage();
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Projets - GSN ProDevis360°</title>
    <meta name="description" content="Gestion complète de vos projets de construction avec GSN ProDevis360°">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* ===== VARIABLES CSS GSN ===== */
        :root {
            --primary-orange: #FF6B35;
            --primary-orange-dark: #E55A2B;
            --secondary-white: #FFFFFF;
            --neutral-light: #F8F9FA;
            --neutral-gray: #6C757D;
            --neutral-dark: #343A40;
            --accent-blue: #007BFF;
            --accent-green: #28A745;
            --accent-red: #DC3545;
            --border-radius: 8px;
            --shadow-soft: 0 2px 8px rgba(0,0,0,0.1);
            --shadow-medium: 0 4px 16px rgba(0,0,0,0.15);
            --transition-fast: all 0.2s ease;
            --transition-smooth: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--neutral-light) 0%, #E9ECEF 100%);
            color: var(--neutral-dark);
            line-height: 1.6;
        }

        /* ===== HEADER GSN ===== */
        .header-gsn {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
            color: var(--secondary-white);
            padding: 2rem 0;
            box-shadow: var(--shadow-medium);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
            text-align: center;
        }

        .logo-gsn {
            width: 80px;
            height: 80px;
            background: var(--secondary-white);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-orange);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1rem;
        }

        .header-title h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* ===== ALERTS ===== */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            box-shadow: var(--shadow-soft);
            animation: slideInDown 0.5s ease-out;
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

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border-left: 4px solid var(--accent-blue);
        }

        /* ===== GRILLE PROJETS ===== */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .project-card {
            background: var(--secondary-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            padding: 1.5rem;
            transition: var(--transition-smooth);
            border-top: 4px solid var(--primary-orange);
            position: relative;
            overflow: hidden;
        }

        .project-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary-orange), transparent);
            transition: left 0.5s ease;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .project-card:hover::before {
            left: 100%;
        }

        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .project-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-orange);
            margin-bottom: 0.5rem;
        }

        .project-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-en-planification { background: #fff3cd; color: #856404; }
        .status-en-cours { background: #cce5ff; color: #004085; }
        .status-termine { background: #d4edda; color: #155724; }
        .status-suspendu { background: #f8d7da; color: #721c24; }

        .project-info {
            margin-bottom: 1rem;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .info-row i {
            width: 16px;
            color: var(--primary-orange);
        }

        .project-stats {
            display: flex;
            justify-content: space-between;
            background: var(--neutral-light);
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-number {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-orange);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--neutral-gray);
        }

        .project-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* ===== BOUTONS ===== */
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            text-align: center;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
            color: var(--secondary-white);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--accent-green) 0%, #218838 100%);
            color: var(--secondary-white);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--accent-red) 0%, #c82333 100%);
            color: var(--secondary-white);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--accent-blue) 0%, #0056b3 100%);
            color: var(--secondary-white);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--neutral-gray) 0%, #5a6268 100%);
            color: var(--secondary-white);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }

        /* ===== NOUVEAU PROJET ===== */
        .add-project {
            background: var(--secondary-white);
            border: 2px dashed var(--primary-orange);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition-smooth);
            position: relative;
        }

        .add-project:hover {
            background: rgba(255, 107, 53, 0.05);
            transform: translateY(-2px);
            border-style: solid;
        }

        .add-project i {
            font-size: 3rem;
            color: var(--primary-orange);
            margin-bottom: 1rem;
        }

        .add-project h3 {
            color: var(--primary-orange);
            margin-bottom: 0.5rem;
        }

        /* ===== MODAL ===== */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: var(--secondary-white);
            margin: 5% auto;
            padding: 2rem;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 600px;
            box-shadow: var(--shadow-medium);
            max-height: 80vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--neutral-light);
        }

        .modal-header h3 {
            color: var(--primary-orange);
            font-weight: 600;
        }

        .close {
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            color: var(--neutral-gray);
            transition: var(--transition-fast);
        }

        .close:hover {
            color: var(--accent-red);
            transform: scale(1.1);
        }

        /* ===== FORMULAIRE ===== */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--neutral-dark);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition-fast);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* ===== ÉTAT VIDE ===== */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--secondary-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-orange);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: var(--neutral-dark);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--neutral-gray);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .projects-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .project-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .project-actions {
                justify-content: center;
            }
            
            .header-title h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .project-card {
                padding: 1rem;
            }
            
            .modal-content {
                margin: 10% auto;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header-gsn">
        <div class="header-content">
            <div class="logo-gsn">GSN</div>
            <div class="header-title">
                <h1><i class="fas fa-project-diagram"></i> Gestion des Projets</h1>
                <p class="header-subtitle">GSN ProDevis360° - Pilotage de vos projets de construction</p>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= htmlspecialchars($message_type) ?>">
                <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : ($message_type === 'danger' ? 'exclamation-triangle' : 'info-circle') ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Grille des Projets -->
        <div class="projects-grid">
            <!-- Bouton Ajouter Projet -->
            <div class="add-project" onclick="openModal()">
                <i class="fas fa-plus-circle"></i>
                <h3>Nouveau Projet</h3>
                <p>Cliquez pour créer un nouveau projet</p>
            </div>

            <!-- Liste des Projets -->
            <?php if (empty($projets)): ?>
                <div style="grid-column: 1 / -1;">
                    <div class="empty-state">
                        <i class="fas fa-project-diagram"></i>
                        <h3>Aucun projet créé</h3>
                        <p>Commencez par créer votre premier projet pour démarrer vos devis.</p>
                        <button class="btn btn-primary" onclick="openModal()" style="margin-top: 1rem;">
                            <i class="fas fa-plus"></i> Créer mon premier projet
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($projets as $projet): ?>
                    <div class="project-card">
                        <div class="project-header">
                            <div>
                                <h3 class="project-title"><?= htmlspecialchars($projet['nom']) ?></h3>
                                <p class="text-muted" style="color: var(--neutral-gray); font-size: 0.9rem;">
                                    <?= htmlspecialchars($projet['client']) ?>
                                </p>
                            </div>
                            <span class="project-status status-<?= strtolower(str_replace(' ', '-', $projet['statut'])) ?>">
                                <?= htmlspecialchars($projet['statut']) ?>
                            </span>
                        </div>

                        <div class="project-info">
                            <div class="info-row">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($projet['adresse']) ?></span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Créé le <?= htmlspecialchars($projet['date_creation_fr']) ?></span>
                            </div>
                            <?php if (!empty($projet['date_debut_fr'])): ?>
                            <div class="info-row">
                                <i class="fas fa-play-circle"></i>
                                <span>Début: <?= htmlspecialchars($projet['date_debut_fr']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($projet['date_fin_prevue_fr'])): ?>
                            <div class="info-row">
                                <i class="fas fa-flag-checkered"></i>
                                <span>Fin prévue: <?= htmlspecialchars($projet['date_fin_prevue_fr']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="project-stats">
                            <div class="stat-item">
                                <span class="stat-number"><?= number_format($projet['nb_devis'], 0, ',', ' ') ?></span>
                                <span class="stat-label">Devis</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?= number_format($projet['total_projets'], 0, ',', ' ') ?></span>
                                <span class="stat-label">FCFA</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?= number_format($projet['budget_previsionnel'], 0, ',', ' ') ?></span>
                                <span class="stat-label">Budget</span>
                            </div>
                        </div>

                        <div class="project-actions">
                            <a href="projet_detail.php?id=<?= $projet['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            <a href="liste_projets.php?id=<?= $projet['id'] ?>" class="btn btn-success">
                                <i class="fas fa-file-invoice"></i> Devis
                            </a>
                            <button class="btn btn-info" onclick="editProject(<?= $projet['id'] ?>)">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                            <button class="btn btn-danger" onclick="deleteProject(<?= $projet['id'] ?>, '<?= htmlspecialchars($projet['nom'], ENT_QUOTES) ?>')">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Statistiques globales -->
        <?php if (!empty($projets)): ?>
        <div style="background: var(--secondary-white); padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow-soft); margin-top: 2rem;">
            <h3 style="color: var(--primary-orange); margin-bottom: 1rem;">
                <i class="fas fa-chart-bar"></i> Statistiques globales
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
                <div style="text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: var(--primary-orange);">
                        <?= count($projets) ?>
                    </div>
                    <div style="color: var(--neutral-gray);">Projets total</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: var(--accent-green);">
                        <?= array_sum(array_column($projets, 'nb_devis')) ?>
                    </div>
                    <div style="color: var(--neutral-gray);">Devis créés</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: var(--accent-blue);">
                        <?= number_format(array_sum(array_column($projets, 'total_projets')), 0, ',', ' ') ?>
                    </div>
                    <div style="color: var(--neutral-gray);">FCFA total</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: var(--neutral-dark);">
                        <?= number_format(array_sum(array_column($projets, 'budget_previsionnel')), 0, ',', ' ') ?>
                    </div>
                    <div style="color: var(--neutral-gray);">Budget prévu</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal Nouveau Projet -->
    <div id="projectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus-circle"></i> Nouveau Projet</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            
            <form method="post" action="" onsubmit="return validateForm()">
                <input type="hidden" name="action" value="ajouter">
                
                <div class="form-group">
                    <label for="nom">
                        <i class="fas fa-project-diagram"></i> Nom du projet *
                    </label>
                    <input type="text" id="nom" name="nom" required maxlength="255" placeholder="Ex: Villa Moderne Cocody">
                </div>
                
                <div class="form-group">
                    <label for="client">
                        <i class="fas fa-user"></i> Client
                    </label>
                    <input type="text" id="client" name="client" maxlength="255" placeholder="Nom du client">
                </div>
                
                <div class="form-group">
                    <label for="description">
                        <i class="fas fa-align-left"></i> Description
                    </label>
                    <textarea id="description" name="description" rows="3" placeholder="Description détaillée du projet..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="adresse">
                        <i class="fas fa-map-marker-alt"></i> Adresse du projet
                    </label>
                    <input type="text" id="adresse" name="adresse" placeholder="Adresse complète du chantier">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_debut">
                            <i class="fas fa-calendar-plus"></i> Date de début
                        </label>
                        <input type="date" id="date_debut" name="date_debut">
                    </div>
                    <div class="form-group">
                        <label for="date_fin_prevue">
                            <i class="fas fa-calendar-check"></i> Date de fin prévue
                        </label>
                        <input type="date" id="date_fin_prevue" name="date_fin_prevue">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="budget_previsionnel">
                            <i class="fas fa-coins"></i> Budget prévisionnel (FCFA)
                        </label>
                        <input type="number" id="budget_previsionnel" name="budget_previsionnel" min="0" step="1000" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label for="statut">
                            <i class="fas fa-flag"></i> Statut
                        </label>
                        <select id="statut" name="statut">
                            <option value="En planification" selected>En planification</option>
                            <option value="En cours">En cours</option>
                            <option value="Terminé">Terminé</option>
                            <option value="Suspendu">Suspendu</option>
                        </select>
                    </div>
                </div>
                
                <div style="text-align: right; margin-top: 2rem; border-top: 1px solid var(--neutral-light); padding-top: 1rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-success" style="margin-left: 0.5rem;">
                        <i class="fas fa-save"></i> Créer le projet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- FOOTER NAVIGATION -->
    <div style="background: var(--secondary-white); padding: 1rem; margin-top: 2rem; text-align: center; box-shadow: var(--shadow-soft);">
        <div style="max-width: 1400px; margin: 0 auto;">
            <a href="index.php" class="btn btn-info" style="margin: 0.25rem;">
                <i class="fas fa-home"></i> Accueil
            </a>
            <a href="liste_projets.php" class="btn btn-primary" style="margin: 0.25rem;">
                <i class="fas fa-list"></i> Liste des Projets
            </a>
            <a href="corrections_systeme.php" class="btn btn-secondary" style="margin: 0.25rem;">
                <i class="fas fa-tools"></i> Diagnostic Système
            </a>
        </div>
        <p style="margin-top: 1rem; color: var(--neutral-gray); font-size: 0.9rem;">
            © 2025 GSN Expertises - ProDevis360° | Version sécurisée sans warnings
        </p>
    </div>

    <script>
        // ===== GESTION MODAL =====
        function openModal() {
            document.getElementById('projectModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Empêcher le scroll
            // Focus sur le premier champ
            setTimeout(() => {
                document.getElementById('nom').focus();
            }, 100);
        }

        function closeModal() {
            document.getElementById('projectModal').style.display = 'none';
            document.body.style.overflow = 'auto'; // Restaurer le scroll
            // Réinitialiser le formulaire
            document.querySelector('#projectModal form').reset();
        }

        // Fermer modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('projectModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // ===== VALIDATION FORMULAIRE =====
        function validateForm() {
            const nom = document.getElementById('nom').value.trim();
            const dateDebut = document.getElementById('date_debut').value;
            const dateFin = document.getElementById('date_fin_prevue').value;
            
            if (!nom) {
                alert('⚠️ Le nom du projet est obligatoire !');
                document.getElementById('nom').focus();
                return false;
            }
            
            if (nom.length < 3) {
                alert('⚠️ Le nom du projet doit contenir au moins 3 caractères !');
                document.getElementById('nom').focus();
                return false;
            }
            
            // Validation des dates
            if (dateDebut && dateFin && new Date(dateDebut) > new Date(dateFin)) {
                alert('⚠️ La date de début ne peut pas être postérieure à la date de fin !');
                document.getElementById('date_debut').focus();
                return false;
            }
            
            return true;
        }

        // ===== ACTIONS PROJETS =====
        function editProject(id) {
            // TODO: Implémenter la modification en modal
            showNotification('🔧 Fonction de modification en cours de développement', 'info');
        }

        function deleteProject(id, nom) {
            if (confirm(`🗑️ Êtes-vous sûr de vouloir supprimer le projet "${nom}" ?\n\n⚠️ Cette action est irréversible et supprimera tous les devis associés !`)) {
                // Créer un formulaire de suppression
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = `
                    <input type="hidden" name="action" value="supprimer">
                    <input type="hidden" name="projet_id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // ===== NOTIFICATIONS =====
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'}"></i>
                ${message}
            `;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.style.maxWidth = '400px';
            
            document.body.appendChild(notification);
            
            // Animation d'entrée
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            
            setTimeout(() => {
                notification.style.transition = 'all 0.3s ease';
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
            }, 10);
            
            // Suppression automatique après 5 secondes
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }

        // ===== RACCOURCIS CLAVIER =====
        document.addEventListener('keydown', function(e) {
            // Ctrl+N pour nouveau projet
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                openModal();
            }
            
            // Échap pour fermer la modal
            if (e.key === 'Escape') {
                closeModal();
            }
            
            // Entrée dans la modal pour soumettre
            if (e.key === 'Enter' && document.getElementById('projectModal').style.display === 'block') {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'TEXTAREA' && activeElement.type !== 'submit') {
                    e.preventDefault();
                    document.querySelector('#projectModal form').dispatchEvent(new Event('submit'));
                }
            }
        });

        // ===== AMÉLIORATIONS UX =====
        // Auto-formatage du budget
        document.getElementById('budget_previsionnel').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            if (value && !isNaN(value)) {
                // Formater avec des espaces tous les 3 chiffres
                e.target.value = parseInt(value).toLocaleString('fr-FR').replace(/,/g, ' ');
            }
        });

        // Validation temps réel du nom
        document.getElementById('nom').addEventListener('input', function(e) {
            const value = e.target.value.trim();
            if (value.length > 0 && value.length < 3) {
                e.target.style.borderColor = '#ffc107';
            } else if (value.length >= 3) {
                e.target.style.borderColor = '#28a745';
            } else {
                e.target.style.borderColor = '#e9ecef';
            }
        });

        // Auto-complétion intelligente
        const commonClients = ['Particulier', 'Entreprise', 'Administration', 'Promotion immobilière'];
        const clientInput = document.getElementById('client');
        
        clientInput.addEventListener('focus', function() {
            if (!this.value) {
                showNotification('💡 Astuce: Utilisez Tab pour naviguer rapidement entre les champs', 'info');
            }
        });

        // ===== INITIALISATION =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎯 GSN ProDevis360° - Projets Ultra Sécurisé initialisé');
            
            // Vérification de la connectivité
            fetch('functions.php')
                .then(response => response.ok ? 
                    console.log('✅ Connexion functions.php OK') : 
                    console.warn('⚠️ Problème functions.php'))
                .catch(error => console.error('❌ Erreur réseau:', error));
            
            // Message de bienvenue si pas de projets
            <?php if (empty($projets)): ?>
            setTimeout(() => {
                showNotification('👋 Bienvenue ! Créez votre premier projet pour commencer.', 'info');
            }, 1000);
            <?php endif; ?>
            
            // Affichage des statistiques si projets existents
            <?php if (!empty($projets)): ?>
            console.log('📊 Statistiques:', {
                projets: <?= count($projets) ?>,
                devis: <?= array_sum(array_column($projets, 'nb_devis')) ?>,
                total: '<?= number_format(array_sum(array_column($projets, 'total_projets')), 0, ',', ' ') ?> FCFA'
            });
            <?php endif; ?>
        });

        // Protection contre les erreurs JavaScript
        window.addEventListener('error', function(e) {
            console.error('Erreur JS capturée:', e.error);
        });
    </script>
</body>
</html>