<?php
// ===== HISTORIQUE_DEVIS.PHP - HISTORIQUE DES MODIFICATIONS =====
require_once 'functions.php';

// Récupération et validation des paramètres
$projet_id = secureGetParam('projet_id', 'int', 0);
$devis_id = secureGetParam('devis_id', 'int', 0);
$module = secureGetParam('module', 'string', '');

// Vérification des paramètres obligatoires
if (!$projet_id || !$devis_id) {
    die('<div class="alert alert-danger">Erreur : Paramètres projet_id et devis_id manquants.</div>');
}

// Récupération des informations du projet et devis
$projet_devis_info = getProjetDevisInfo($projet_id, $devis_id);
if (!$projet_devis_info) {
    die('<div class="alert alert-danger">Erreur : Projet ou devis introuvable.</div>');
}

// Connexion à la base de données
$conn = getDbConnection();

// Construction de la requête avec filtres
$whereClause = "WHERE projet_id = ? AND devis_id = ?";
$params = [$projet_id, $devis_id];
$types = "ii";

if (!empty($module)) {
    $whereClause .= " AND module = ?";
    $params[] = $module;
    $types .= "s";
}

// Récupération de l'historique
$stmt = $conn->prepare("
    SELECT 
        id, module, action, description, utilisateur,
        DATE_FORMAT(date_action, '%d/%m/%Y à %H:%i:%s') as date_action_fr,
        date_action
    FROM historique_devis 
    $whereClause
    ORDER BY date_action DESC
    LIMIT 500
");

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$historique = [];
while ($row = $result->fetch_assoc()) {
    $historique[] = $row;
}

// Statistiques de l'historique
$stats = [
    'total_actions' => count($historique),
    'modules_actifs' => 0,
    'derniere_modification' => null,
    'actions_par_module' => []
];

if (!empty($historique)) {
    $stats['derniere_modification'] = $historique[0]['date_action_fr'];
    
    // Compter les actions par module
    foreach ($historique as $entry) {
        if (!isset($stats['actions_par_module'][$entry['module']])) {
            $stats['actions_par_module'][$entry['module']] = 0;
        }
        $stats['actions_par_module'][$entry['module']]++;
    }
    
    $stats['modules_actifs'] = count($stats['actions_par_module']);
}

// Modules disponibles pour le filtre
$modules_disponibles = [
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

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique - <?= htmlspecialchars($projet_devis_info['nom_projet']) ?> | GSN ProDevis360°</title>
    
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
            padding: 1.5rem 0;
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

        .header-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .project-info {
            font-size: 0.9rem;
            opacity: 0.9;
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

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--secondary-white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            text-align: center;
            border-top: 4px solid var(--primary-orange);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-orange);
            display: block;
        }

        .stat-label {
            color: var(--neutral-gray);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .filters-section {
            background: var(--secondary-white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.5rem;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border: 2px solid var(--neutral-light);
            background: var(--neutral-light);
            color: var(--neutral-gray);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition-fast);
            text-decoration: none;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .filter-btn:hover {
            border-color: var(--primary-orange);
            color: var(--primary-orange);
        }

        .filter-btn.active {
            background: var(--primary-orange);
            border-color: var(--primary-orange);
            color: var(--secondary-white);
        }

        .timeline-container {
            background: var(--secondary-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .timeline-header {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
            color: var(--secondary-white);
            padding: 1.5rem;
            text-align: center;
        }

        .timeline {
            padding: 2rem;
            position: relative;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--primary-orange);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            padding-left: 4rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0.5rem;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--primary-orange);
            border: 3px solid var(--secondary-white);
            box-shadow: var(--shadow-soft);
        }

        .timeline-content {
            background: var(--neutral-light);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-orange);
        }

        .timeline-header-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .timeline-module {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            background: var(--primary-orange);
            color: var(--secondary-white);
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .timeline-action {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .action-ajout { background: #d4edda; color: #155724; }
        .action-modification { background: #fff3cd; color: #856404; }
        .action-suppression { background: #f8d7da; color: #721c24; }

        .timeline-description {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .timeline-meta {
            font-size: 0.85rem;
            color: var(--neutral-gray);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
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
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .filters-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .timeline {
                padding: 1rem;
            }
            
            .timeline-item {
                padding-left: 3rem;
            }
            
            .timeline::before {
                left: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header-gsn">
        <div class="header-content">
            <div class="logo-section">
                <div class="logo-gsn">GSN</div>
                <div class="header-title">
                    <h1><i class="fas fa-history"></i> Historique des Modifications</h1>
                    <div class="project-info">
                        <strong><?= htmlspecialchars($projet_devis_info['nom_projet']) ?></strong> • Devis #<?= $devis_id ?>
                    </div>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" class="btn-header">
                    <i class="fas fa-chart-pie"></i> Récapitulatif
                </a>
                <a href="projets.php" class="btn-header">
                    <i class="fas fa-arrow-left"></i> Retour Projets
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Statistiques -->
        <div class="stats-row">
            <div class="stat-card">
                <span class="stat-number"><?= $stats['total_actions'] ?></span>
                <div class="stat-label">Actions totales</div>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= $stats['modules_actifs'] ?></span>
                <div class="stat-label">Modules modifiés</div>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= $stats['derniere_modification'] ? '✓' : '—' ?></span>
                <div class="stat-label">Dernière modification</div>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= date('d/m/Y') ?></span>
                <div class="stat-label">Consultation</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <div class="filters-header">
                <h3><i class="fas fa-filter"></i> Filtrer par module</h3>
                <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
                   class="filter-btn <?= empty($module) ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> Tous
                </a>
            </div>
            
            <div class="filters-grid">
                <?php foreach ($modules_disponibles as $module_key => $module_info): ?>
                    <a href="historique_devis.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>&module=<?= $module_key ?>" 
                       class="filter-btn <?= $module === $module_key ? 'active' : '' ?>">
                        <i class="<?= $module_info['icon'] ?>"></i>
                        <?= $module_info['name'] ?>
                        <?php if (isset($stats['actions_par_module'][$module_key])): ?>
                            (<?= $stats['actions_par_module'][$module_key] ?>)
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Timeline -->
        <div class="timeline-container">
            <div class="timeline-header">
                <h3><i class="fas fa-clock"></i> Chronologie des modifications</h3>
                <?php if (!empty($module)): ?>
                    <p>Filtré sur le module : <strong><?= $modules_disponibles[$module]['name'] ?? $module ?></strong></p>
                <?php endif; ?>
            </div>

            <div class="timeline">
                <?php if (empty($historique)): ?>
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <h3>Aucune modification trouvée</h3>
                        <p>
                            <?php if (!empty($module)): ?>
                                Aucune action n'a été effectuée sur le module <strong><?= $modules_disponibles[$module]['name'] ?? $module ?></strong>.
                            <?php else: ?>
                                Aucune modification n'a encore été effectuée sur ce devis.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($historique as $entry): ?>
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <div class="timeline-header-item">
                                    <div>
                                        <span class="timeline-module">
                                            <i class="<?= $modules_disponibles[$entry['module']]['icon'] ?? 'fas fa-cog' ?>"></i>
                                            <?= $modules_disponibles[$entry['module']]['name'] ?? ucfirst($entry['module']) ?>
                                        </span>
                                        <span class="timeline-action action-<?= strtolower($entry['action']) ?>">
                                            <?= htmlspecialchars($entry['action']) ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="timeline-description">
                                    <?= htmlspecialchars($entry['description']) ?>
                                </div>
                                
                                <div class="timeline-meta">
                                    <span><i class="fas fa-calendar"></i> <?= $entry['date_action_fr'] ?></span>
                                    <?php if (!empty($entry['utilisateur'])): ?>
                                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($entry['utilisateur']) ?></span>
                                    <?php endif; ?>
                                    <span><i class="fas fa-hashtag"></i> ID: <?= $entry['id'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions -->
        <div style="text-align: center; margin-top: 2rem;">
            <a href="recapitulatif.php?projet_id=<?= $projet_id ?>&devis_id=<?= $devis_id ?>" 
               class="btn-header" style="display: inline-flex;">
                <i class="fas fa-chart-line"></i> Voir le récapitulatif
            </a>
        </div>
    </div>

    <script>
        // Animation d'apparition des éléments de timeline
        document.addEventListener('DOMContentLoaded', function() {
            const timelineItems = document.querySelectorAll('.timeline-item');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateX(0)';
                        }, index * 100);
                    }
                });
            }, {
                threshold: 0.1
            });

            timelineItems.forEach(item => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-30px)';
                item.style.transition = 'all 0.6s ease-out';
                observer.observe(item);
            });

            // Animation des statistiques
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const finalValue = stat.textContent;
                if (!isNaN(finalValue) && finalValue !== '0') {
                    let currentValue = 0;
                    const increment = Math.ceil(finalValue / 20);
                    
                    const timer = setInterval(() => {
                        currentValue += increment;
                        if (currentValue >= finalValue) {
                            currentValue = finalValue;
                            clearInterval(timer);
                        }
                        stat.textContent = currentValue;
                    }, 50);
                }
            });
        });
    </script>
</body>
</html>