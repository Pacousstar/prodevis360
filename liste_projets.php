<?php
// ===== LISTE_PROJETS.PHP - LISTE DES DEVIS PAR PROJET =====
require_once 'functions.php';

// Récupération du projet_id
$projet_id = secureGetParam('projet_id', 'int', 0);

if (!$projet_id) {
    header('Location: projets.php');
    exit;
}

// Connexion à la base de données
$conn = getDbConnection();

// Récupération des informations du projet
$stmt = $conn->prepare("SELECT * FROM projets WHERE id = ?");
$stmt->bind_param("i", $projet_id);
$stmt->execute();
$projet = $stmt->get_result()->fetch_assoc();

if (!$projet) {
    die('<div class="alert alert-danger">Projet introuvable.</div>');
}

// Gestion des actions
$action = secureGetParam('action', 'string', '');
$devis_id = secureGetParam('devis_id', 'int', 0);
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if ($action == 'nouveau_devis') {
            $nom_devis = trim($_POST['nom_devis'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $statut = $_POST['statut'] ?? 'Brouillon';
            
            if (empty($nom_devis)) {
                throw new Exception("Le nom du devis est obligatoire.");
            }
            
            $stmt = $conn->prepare("
                INSERT INTO devis (projet_id, nom_devis, description, statut, date_creation) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("isss", $projet_id, $nom_devis, $description, $statut);
            
            if ($stmt->execute()) {
                $nouveau_devis_id = $conn->insert_id;
                
                // Initialiser le récapitulatif
                initializeRecapitulatifCategories($projet_id, $nouveau_devis_id);
                
                $message = "Nouveau devis créé avec succès !";
                $message_type = "success";
            }
            
        } elseif ($action == 'supprimer_devis' && $devis_id > 0) {
            // Supprimer le devis et toutes ses données associées
            $modules = ['plomberie', 'menuiserie', 'electricite', 'peinture', 'materiaux', 
                       'charpenterie', 'carrelage', 'ferraillage', 'ferronnerie'];
            
            foreach ($modules as $module) {
                $stmt = $conn->prepare("DELETE FROM $module WHERE projet_id = ? AND devis_id = ?");
                $stmt->bind_param("ii", $projet_id, $devis_id);
                $stmt->execute();
            }
            
            // Supprimer le récapitulatif et l'historique
            $stmt = $conn->prepare("DELETE FROM recapitulatif WHERE projet_id = ? AND devis_id = ?");
            $stmt->bind_param("ii", $projet_id, $devis_id);
            $stmt->execute();
            
            $stmt = $conn->prepare("DELETE FROM historique_devis WHERE projet_id = ? AND devis_id = ?");
            $stmt->bind_param("ii", $projet_id, $devis_id);
            $stmt->execute();
            
            // Supprimer le devis
            $stmt = $conn->prepare("DELETE FROM devis WHERE id = ? AND projet_id = ?");
            $stmt->bind_param("ii", $devis_id, $projet_id);
            
            if ($stmt->execute()) {
                $message = "Devis supprimé avec succès !";
                $message_type = "success";
            }
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = "danger";
    }
}

// Récupération des devis du projet avec leurs totaux
$stmt = $conn->prepare("
    SELECT d.*, 
           r.total_general,
           COUNT(h.id) as nb_modifications,
           DATE_FORMAT(d.date_creation, '%d/%m/%Y') as date_creation_fr,
           DATE_FORMAT(d.date_modification, '%d/%m/%Y à %H:%i') as date_modification_fr
    FROM devis d
    LEFT JOIN recapitulatif r ON d.id = r.devis_id
    LEFT JOIN historique_devis h ON d.id = h.devis_id
    WHERE d.projet_id = ?
    GROUP BY d.id
    ORDER BY d.date_creation DESC
");

$stmt->bind_param("i", $projet_id);
$stmt->execute();
$result = $stmt->get_result();

$devis_list = [];
$total_projet = 0;

while ($row = $result->fetch_assoc()) {
    $devis_list[] = $row;
    $total_projet += $row['total_general'] ?? 0;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis - <?= htmlspecialchars($projet['nom_projet']) ?> | GSN ProDevis360°</title>
    
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
        }

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
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
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

        .header-title h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .project-meta {
            font-size: 0.9rem;
            opacity: 0.9;
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
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-header:hover {
            background: rgba(255,255,255,0.3);
            color: var(--secondary-white);
            transform: translateY(-1px);
        }

        .project-summary {
            background: rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .summary-item {
            text-align: center;
        }

        .summary-number {
            font-size: 2rem;
            font-weight: 700;
            display: block;
            margin-bottom: 0.25rem;
        }

        .summary-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            box-shadow: var(--shadow-soft);
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

        .devis-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .add-devis-card {
            background: var(--secondary-white);
            border: 2px dashed var(--primary-orange);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 200px;
        }

        .add-devis-card:hover {
            background: rgba(255, 107, 53, 0.05);
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .add-devis-card i {
            font-size: 3rem;
            color: var(--primary-orange);
            margin-bottom: 1rem;
        }

        .devis-card {
            background: var(--secondary-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            transition: var(--transition-smooth);
            border-top: 4px solid var(--primary-orange);
        }

        .devis-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .devis-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--neutral-light);
        }

        .devis-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-orange);
            margin-bottom: 0.5rem;
        }

        .devis-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .devis-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-brouillon { background: #f8d7da; color: #721c24; }
        .status-en-cours { background: #fff3cd; color: #856404; }
        .status-valide { background: #d4edda; color: #155724; }
        .status-envoye { background: #cce5ff; color: #004085; }
        .status-accepte { background: #d1ecf1; color: #0c5460; }

        .devis-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            padding: 1rem 1.5rem;
            background: var(--neutral-light);
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-orange);
            display: block;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--neutral-gray);
            margin-top: 0.25rem;
        }

        .devis-actions {
            padding: 1.5rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

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
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
            color: var(--secondary-white);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--accent-green) 0%, #218838 100%);
            color: var(--secondary-white);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--accent-blue) 0%, #0056b3 100%);
            color: var(--secondary-white);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--accent-red) 0%, #c82333 100%);
            color: var(--secondary-white);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }

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
            background: var(--secondary-white);
            margin: 10% auto;
            padding: 2rem;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow-medium);
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
        }

        .close:hover {
            color: var(--accent-red);
        }

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

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--neutral-gray);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .devis-grid {
                grid-template-columns: 1fr;
            }

            .project-summary {
                grid-template-columns: repeat(2, 1fr);
            }

            .devis-stats {
                grid-template-columns: 1fr;
            }

            .devis-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header-gsn">
        <div class="header-content">
            <div class="header-top">
                <div class="logo-section">
                    <div class="logo-gsn">GSN</div>
                    <div class="header-title">
                        <h1><i class="fas fa-file-invoice"></i> Devis du Projet</h1>
                        <div class="project-meta">
                            <strong><?= htmlspecialchars($projet['nom_projet']) ?></strong>
                            <?php if ($projet['client_nom']): ?>
                                • Client: <?= htmlspecialchars($projet['client_nom']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 0.75rem;">
                    <a href="projet_detail.php?id=<?= $projet_id ?>" class="btn-header">
                        <i class="fas fa-info-circle"></i> Détails Projet
                    </a>
                    <a href="projets.php" class="btn-header">
                        <i class="fas fa-arrow-left"></i> Tous les Projets
                    </a>
                </div>
            </div>

            <div class="project-summary">
                <div class="summary-item">
                    <span class="summary-number"><?= count($devis_list) ?></span>
                    <span class="summary-label">Devis créés</span>
                </div>
                <div class="summary-item">
                    <span class="summary-number"><?= number_format($total_projet, 0, ',', ' ') ?></span>
                    <span class="summary-label">FCFA Total</span>
                </div>
                <div class="summary-item">
                    <span class="summary-number"><?= $projet['statut'] ?></span>
                    <span class="summary-label">Statut Projet</span>
                </div>
                <div class="summary-item">
                    <span class="summary-number"><?= number_format($projet['budget_previsionnel'], 0, ',', ' ') ?></span>
                    <span class="summary-label">Budget Prévisionnel</span>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?>">
                <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Grille des Devis -->
        <div class="devis-grid">
            <!-- Bouton Nouveau Devis -->
            <div class="add-devis-card" onclick="openModal()">
                <i class="fas fa-plus-circle"></i>
                <h3>Nouveau Devis</h3>
                <p>Créer un nouveau devis pour ce projet</p>
            </div>

            <!-- Liste des Devis -->
            <?php if (empty($devis_list)): ?>
                <div style="grid-column: 1 / -1;">
                    <div class="empty-state">
                        <i class="fas fa-file-invoice"></i>
                        <h3>Aucun devis créé</h3>
                        <p>Commencez par créer votre premier devis pour ce projet.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($devis_list as $devis): ?>
                    <div class="devis-card">
                        <div class="devis-header">
                            <h3 class="devis-title"><?= htmlspecialchars($devis['nom_devis']) ?></h3>
                            <div class="devis-meta">
                                <span class="devis-status status-<?= strtolower(str_replace(' ', '-', $devis['statut'])) ?>">
                                    <?= htmlspecialchars($devis['statut']) ?>
                                </span>
                                <small class="text-muted">
                                    Créé le <?= $devis['date_creation_fr'] ?>
                                </small>
                            </div>
                            <?php if ($devis['description']): ?>
                                <p style="font-size: 0.9rem; color: var(--neutral-gray); margin-top: 0.5rem;">
                                    <?= htmlspecialchars($devis['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="devis-stats">
                            <div class="stat-item">
                                <span class="stat-number"><?= number_format($devis['total_general'] ?? 0, 0, ',', ' ') ?></span>
                                <span class="stat-label">Total FCFA</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?= $devis['nb_modifications'] ?></span>
                                <span class="stat-label">Modifications</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">
                                    <?php if ($devis['date_modification_fr']): ?>
                                        <i class="fas fa-check" style="color: var(--accent-green);"></i>
                                    <?php else: ?>
                                        <i class="fas fa-minus" style="color: var(--neutral-gray);"></i>
                                    <?php endif; ?>
                                </span>
                                <span class="stat-label">Modifié</span>
                            </div>
                        </div>

                        <div class="devis-actions">
                            <a href="recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis['id'] ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="impression_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis['id'] ?>" 
                               class="btn btn-info" target="_blank">
                                <i class="fas fa-print"></i> PDF
                            </a>
                            <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis['id'] ?>" 
                               class="btn btn-success">
                                <i class="fas fa-history"></i> Historique
                            </a>
                            <button class="btn btn-danger" 
                                    onclick="confirmerSuppression(<?= $devis['id'] ?>, '<?= htmlspecialchars($devis['nom_devis'], ENT_QUOTES) ?>')">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Nouveau Devis -->
    <div id="modalDevis" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus-circle"></i> Nouveau Devis</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="nouveau_devis">
                
                <div class="form-group">
                    <label for="nom_devis">Nom du devis *</label>
                    <input type="text" id="nom_devis" name="nom_devis" required 
                           placeholder="Ex: Devis rénovation complète">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" 
                              placeholder="Description détaillée du devis..."></textarea>
                </div>

                <div class="form-group">
                    <label for="statut">Statut initial</label>
                    <select id="statut" name="statut">
                        <option value="Brouillon">Brouillon</option>
                        <option value="En cours">En cours</option>
                        <option value="Validé">Validé</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le devis
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalDevis').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modalDevis').style.display = 'none';
        }

        function confirmerSuppression(devisId, nomDevis) {
            if (confirm(`⚠️ ATTENTION - Suppression définitive\n\nÊtes-vous sûr de vouloir supprimer le devis "${nomDevis}" ?\n\nCette action supprimera :\n• Tous les éléments de tous les modules\n• L'historique complet\n• Le récapitulatif\n\nCette action est IRRÉVERSIBLE !`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'supprimer_devis';
                
                const devisIdInput = document.createElement('input');
                devisIdInput.type = 'hidden';
                devisIdInput.name = 'devis_id';
                devisIdInput.value = devisId;
                
                form.appendChild(actionInput);
                form.appendChild(devisIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Fermer modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('modalDevis');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Animation d'apparition des cartes
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.devis-card, .add-devis-card');
            
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease-out';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 150);
            });

            // Animation des statistiques de résumé
            const summaryNumbers = document.querySelectorAll('.summary-number');
            summaryNumbers.forEach(stat => {
                const finalValue = stat.textContent.replace(/[^\d]/g, '');
                if (!isNaN(finalValue) && finalValue !== '0') {
                    let currentValue = 0;
                    const increment = Math.ceil(finalValue / 20);
                    
                    const timer = setInterval(() => {
                        currentValue += increment;
                        if (currentValue >= finalValue) {
                            currentValue = finalValue;
                            clearInterval(timer);
                        }
                        
                        if (stat.textContent.includes('FCFA')) {
                            stat.textContent = new Intl.NumberFormat('fr-FR').format(currentValue);
                        } else {
                            stat.textContent = currentValue;
                        }
                    }, 50);
                }
            });
        });
    </script>
</body>
</html>