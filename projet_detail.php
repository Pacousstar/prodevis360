<?php
// ===== PROJET_DETAIL.PHP - VERSION CORRIGÉE =====
// Correction des erreurs de champs manquants

// Activation des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion des fonctions communes
require_once 'functions.php';

// Connexion BDD
$pdo = getDbConnection();

// Vérification des paramètres
$projet_id = secureGetParam('id', 'int', 0);

if ($projet_id === 0) {
    header("Location: liste_projets.php");
    exit();
}

// Récupération des infos du projet avec vérification des champs
try {
    $stmt = $pdo->prepare("SELECT * FROM projets WHERE id = ?");
    $stmt->execute([$projet_id]);
    $projet = $stmt->fetch();

    if (!$projet) {
        header("Location: liste_projets.php?error=projet_non_trouve");
        exit();
    }
    
    // Vérification et définition des champs manquants
    $projet['adresse'] = $projet['adresse'] ?? 'Non défini';
    $projet['date_creation'] = $projet['date_creation'] ?? date('Y-m-d H:i:s');
    
} catch (PDOException $e) {
    die("Erreur de requête : " . $e->getMessage());
}

// Récupération des devis du projet
try {
    $stmt = $pdo->prepare("SELECT * FROM devis WHERE projet_id = ? ORDER BY id DESC");
    $stmt->execute([$projet_id]);
    $devis_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $devis_list = [];
}

// Traitement de création de nouveau devis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_devis'])) {
    $description = trim($_POST['description'] ?? '');
    
    if (empty($description)) {
        $error = "La description du devis est requise";
    } else {
        try {
            // Comptage des devis existants pour ce projet
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM devis WHERE projet_id = ?");
            $stmt->execute([$projet_id]);
            $nb_devis = $stmt->fetchColumn();
            
            // Génération du numéro de devis
            $numero = 'DEV-' . str_pad($projet_id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($nb_devis + 1, 3, '0', STR_PAD_LEFT);
            
            // Insertion du nouveau devis
            $stmt = $pdo->prepare("INSERT INTO devis (projet_id, numero, description, statut) VALUES (?, ?, ?, 'brouillon')");
            $stmt->execute([$projet_id, $numero, $description]);
            $new_devis_id = $pdo->lastInsertId();
            
            // Initialisation des catégories pour ce devis
            initializeRecapitulatifCategories($pdo, $projet_id, $new_devis_id);
            
            // Redirection vers le nouveau devis
            header("Location: devis_detail.php?projet_id=$projet_id&devis_id=$new_devis_id");
            exit();
            
        } catch (PDOException $e) {
            $error = "Erreur lors de la création : " . $e->getMessage();
        }
    }
}

// Suppression d'un devis
if (isset($_GET['delete_devis']) && is_numeric($_GET['delete_devis'])) {
    $devis_id = (int)$_GET['delete_devis'];
    
    try {
        $pdo->beginTransaction();
        
        // Supprimer les données de tous les modules
        $tables = ['plomberie', 'menuiserie', 'electricite', 'peinture', 'charpenterie', 'carrelage', 'ferraillage', 'ferronnerie', 'materiaux_base'];
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE projet_id = ? AND devis_id = ?");
            $stmt->execute([$projet_id, $devis_id]);
        }
        
        // Supprimer le récapitulatif
        $stmt = $pdo->prepare("DELETE FROM recapitulatif WHERE projet_id = ? AND devis_id = ?");
        $stmt->execute([$projet_id, $devis_id]);
        
        // Supprimer le devis
        $stmt = $pdo->prepare("DELETE FROM devis WHERE id = ? AND projet_id = ?");
        $stmt->execute([$devis_id, $projet_id]);
        
        $pdo->commit();
        
        header("Location: projet_detail.php?id=$projet_id&success=devis_supprime");
        exit();
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Calcul des statistiques du projet
$stats = ['total_devis' => count($devis_list), 'total_montant' => 0];
foreach ($devis_list as $devis) {
    try {
        $stmt = $pdo->prepare("SELECT SUM(total_ttc) as total FROM recapitulatif WHERE projet_id = ? AND devis_id = ?");
        $stmt->execute([$projet_id, $devis['id']]);
        $result = $stmt->fetch();
        $stats['total_montant'] += $result['total'] ?? 0;
    } catch (PDOException $e) {
        // Ignore les erreurs de calcul
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet <?= htmlspecialchars($projet['nom'] ?? 'Sans nom') ?> - GSN ProDevis360°</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a1a2e;
            --secondary: #16213e;
            --accent: #0f3460;
            --success: #00d4aa;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--gradient);
            min-height: 100vh;
            color: var(--dark);
        }

        .header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-container img {
            height: 50px;
            width: auto;
        }

        .page-title {
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 700;
        }

        .breadcrumb {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--accent);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .nav-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .nav-btn {
            background: var(--gradient);
            color: white;
            padding: 0.7rem 1.2rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .nav-btn.success {
            background: linear-gradient(135deg, var(--success), #27ae60);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .card-title {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .projet-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-group h4 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .info-group p {
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .stat-card {
            background: var(--gradient);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .devis-grid {
            display: grid;
            gap: 1rem;
            margin-top: 2rem;
        }

        .devis-card {
            background: white;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .devis-card:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .devis-info h4 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .devis-info p {
            color: var(--dark);
            font-size: 0.9rem;
        }

        .devis-status {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin: 0.5rem 0;
        }

        .devis-status.brouillon {
            background: rgba(243,156,18,0.1);
            color: var(--warning);
        }

        .devis-status.finalise {
            background: rgba(0,212,170,0.1);
            color: var(--success);
        }

        .devis-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15,52,96,0.1);
        }

        .btn {
            background: var(--gradient);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert.success {
            background: rgba(0,212,170,0.1);
            border-left: 4px solid var(--success);
            color: var(--success);
        }

        .alert.error {
            background: rgba(231,76,60,0.1);
            border-left: 4px solid var(--danger);
            color: var(--danger);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--dark);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .nav-actions {
                justify-content: center;
            }
            
            .projet-info {
                grid-template-columns: 1fr;
            }
            
            .devis-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo-container">
                <img src="logo.png" alt="Logo GSN" onerror="this.style.display='none'">
                <div>
                    <h1 class="page-title">Projet : <?= htmlspecialchars($projet['nom'] ?? 'Sans nom') ?></h1>
                    <div class="breadcrumb">
                        <a href="index.php">Accueil</a> › 
                        <a href="liste_projets.php">Projets</a> › 
                        <?= htmlspecialchars($projet['nom'] ?? 'Sans nom') ?>
                    </div>
                </div>
            </div>
            
            <nav class="nav-actions">
                <a href="liste_projets.php" class="nav-btn" title="Retour aux projets">
                    <i class="fas fa-arrow-left"></i> Projets
                </a>
                <button type="button" class="nav-btn success" onclick="document.getElementById('nouveauDevisForm').style.display='block'" title="Créer un nouveau devis">
                    <i class="fas fa-plus"></i> Nouveau Devis
                </button>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_GET['success']) && $_GET['success'] === 'devis_supprime'): ?>
        <div class="alert success fade-in">
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>Devis supprimé avec succès !</strong>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert error fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Erreur :</strong>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="card fade-in">
            <h2 class="card-title">
                <i class="fas fa-info-circle"></i> 
                Informations du Projet
            </h2>
            
            <div class="projet-info">
                <div class="info-group">
                    <h4>Détails du Projet</h4>
                    <p><strong>Nom :</strong> <?= htmlspecialchars($projet['nom'] ?? 'Non défini') ?></p>
                    <p><strong>Client :</strong> <?= htmlspecialchars($projet['client'] ?? 'Non défini') ?></p>
                    <p><strong>Adresse :</strong> <?= htmlspecialchars($projet['adresse'] ?? 'Non défini') ?></p>
                </div>
                <div class="info-group">
                    <h4>Description</h4>
                    <p><?= htmlspecialchars($projet['description'] ?? 'Aucune description') ?></p>
                </div>
                <div class="info-group">
                    <h4>Informations Techniques</h4>
                    <p><strong>Créé le :</strong> <?= isset($projet['date_creation']) && $projet['date_creation'] ? date('d/m/Y', strtotime($projet['date_creation'])) : 'Date inconnue' ?></p>
                    <p><strong>ID Projet :</strong> #<?= $projet['id'] ?></p>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['total_devis'] ?></div>
                    <div class="stat-label">Devis Créés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= number_format($stats['total_montant'], 0, ',', ' ') ?> FCFA</div>
                    <div class="stat-label">Montant Total</div>
                </div>
            </div>
        </div>

        <!-- Formulaire de création de devis (caché par défaut) -->
        <div class="card" id="nouveauDevisForm" style="display: none;">
            <h2 class="card-title">
                <i class="fas fa-plus-circle"></i> 
                Créer un Nouveau Devis
            </h2>
            
            <form method="POST">
                <input type="hidden" name="creer_devis" value="1">
                
                <div class="form-group">
                    <label for="description">Description du devis</label>
                    <input type="text" class="form-control" name="description" id="description" 
                           placeholder="Ex: Devis initial, Variante 1, Version finale..." required>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn">
                        <i class="fas fa-plus"></i> Créer le Devis
                    </button>
                    <button type="button" class="btn" style="background: #6c757d;" onclick="document.getElementById('nouveauDevisForm').style.display='none'">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                </div>
            </form>
        </div>

        <div class="card fade-in">
            <h2 class="card-title">
                <i class="fas fa-file-alt"></i> 
                Devis du Projet (<?= count($devis_list) ?>)
            </h2>
            
            <?php if (empty($devis_list)): ?>
            <div class="empty-state">
                <i class="fas fa-file-plus"></i>
                <h3>Aucun devis créé</h3>
                <p>Commencez par créer votre premier devis pour ce projet.</p>
                <button type="button" class="btn" onclick="document.getElementById('nouveauDevisForm').style.display='block'">
                    <i class="fas fa-plus"></i> Créer un Devis
                </button>
            </div>
            <?php else: ?>
            <div class="devis-grid">
                <?php foreach ($devis_list as $devis): ?>
                <div class="devis-card">
                    <div class="devis-info">
                        <h4><?= htmlspecialchars($devis['numero'] ?? 'N/A') ?></h4>
                        <p><strong>Description :</strong> <?= htmlspecialchars($devis['description'] ?? 'Sans description') ?></p>
                        <p><strong>Créé le :</strong> <?= isset($devis['date_creation']) && $devis['date_creation'] ? date('d/m/Y à H:i', strtotime($devis['date_creation'])) : 'Date inconnue' ?></p>
                        <div class="devis-status <?= $devis['statut'] ?? 'brouillon' ?>">
                            <?= ucfirst($devis['statut'] ?? 'brouillon') ?>
                        </div>
                    </div>
                    
                    <div class="devis-actions">
                        <a href="devis_detail.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis['id'] ?>" 
                           class="btn-sm btn-primary" title="Ouvrir le devis">
                            <i class="fas fa-folder-open"></i> Ouvrir
                        </a>
                        <a href="?id=<?= $projet_id ?>&delete_devis=<?= $devis['id'] ?>" 
                           class="btn-sm btn-danger" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce devis ?')"
                           title="Supprimer le devis">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        console.log('Projet Detail chargé avec succès !');
    </script>
</body>
</html>

<!-- ===== FIN PROJET_DETAIL.PHP CORRIGÉ ===== -->