<?php
// ===== PROJETS.PHP - GESTION DES PROJETS GSN ProDevis360° =====
require_once 'functions.php';

// Connexion à la base de données
$conn = getDbConnection();

// Variables d'affichage
$message = '';
$message_type = '';

// Gestion des actions
$action = secureGetParam('action', 'string', '');
$projet_id = secureGetParam('projet_id', 'int', 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if ($action == 'ajouter') {
            $nom_projet = trim($_POST['nom_projet'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $client_nom = trim($_POST['client_nom'] ?? '');
            $client_email = trim($_POST['client_email'] ?? '');
            $client_telephone = trim($_POST['client_telephone'] ?? '');
            $adresse_projet = trim($_POST['adresse_projet'] ?? '');
            $date_debut = $_POST['date_debut'] ?? '';
            $date_fin_prevue = $_POST['date_fin_prevue'] ?? '';
            $budget_previsionnel = floatval($_POST['budget_previsionnel'] ?? 0);
            $statut = $_POST['statut'] ?? 'En planification';
            
            if (empty($nom_projet)) {
                throw new Exception("Le nom du projet est obligatoire.");
            }
            
            $stmt = $conn->prepare("
                INSERT INTO projets (
                    nom_projet, description, client_nom, client_email, client_telephone,
                    adresse_projet, date_debut, date_fin_prevue, budget_previsionnel, 
                    statut, date_creation
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param(
                "ssssssssds",
                $nom_projet, $description, $client_nom, $client_email, $client_telephone,
                $adresse_projet, $date_debut, $date_fin_prevue, $budget_previsionnel, $statut
            );
            
            if ($stmt->execute()) {
                $message = "Projet créé avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de la création : " . $conn->error);
            }
            
        } elseif ($action == 'supprimer' && $projet_id > 0) {
            $stmt = $conn->prepare("DELETE FROM projets WHERE id = ?");
            $stmt->bind_param("i", $projet_id);
            
            if ($stmt->execute()) {
                $message = "Projet supprimé avec succès !";
                $message_type = "success";
            } else {
                throw new Exception("Erreur lors de la suppression : " . $conn->error);
            }
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = "danger";
    }
}

// Récupération des projets
$projets = [];
$stmt = $conn->query("
    SELECT p.*, 
           COUNT(d.id) as nb_devis,
           SUM(r.total_general) as total_projets,
           DATE_FORMAT(p.date_creation, '%d/%m/%Y') as date_creation_fr,
           DATE_FORMAT(p.date_debut, '%d/%m/%Y') as date_debut_fr,
           DATE_FORMAT(p.date_fin_prevue, '%d/%m/%Y') as date_fin_prevue_fr
    FROM projets p
    LEFT JOIN devis d ON p.id = d.projet_id
    LEFT JOIN recapitulatif r ON d.id = r.devis_id
    GROUP BY p.id
    ORDER BY p.date_creation DESC
");

while ($row = $stmt->fetch_assoc()) {
    $projets[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Projets | GSN ProDevis360°</title>
    
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
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
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
        }

        .status-planification { background: #fff3cd; color: #856404; }
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

        .btn-danger {
            background: linear-gradient(135deg, var(--accent-red) 0%, #c82333 100%);
            color: var(--secondary-white);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--accent-blue) 0%, #0056b3 100%);
            color: var(--secondary-white);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }

        .add-project {
            background: var(--secondary-white);
            border: 2px dashed var(--primary-orange);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .add-project:hover {
            background: rgba(255, 107, 53, 0.05);
            transform: translateY(-2px);
        }

        .add-project i {
            font-size: 3rem;
            color: var(--primary-orange);
            margin-bottom: 1rem;
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
            background-color: var(--secondary-white);
            margin: 5% auto;
            padding: 2rem;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 600px;
            box-shadow: var(--shadow-medium);
            max-height: 80vh;
            overflow-y: auto;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

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
            <div class="alert alert-<?= $message_type ?>">
                <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Projets Grid -->
        <div class="projects-grid">
            <!-- Bouton Ajouter Projet -->
            <div class="add-project" onclick="openModal()">
                <i class="fas fa-plus-circle"></i>
                <h3>Nouveau Projet</h3>
                <p>Cliquez pour créer un nouveau projet</p>
            </div>

            <!-- Liste des Projets -->
            <?php foreach ($projets as $projet): ?>
                <div class="project-card">
                    <div class="project-header">
                        <div>
                            <h3 class="project-title"><?= htmlspecialchars($projet['nom_projet']) ?></h3>
                            <p class="text-muted"><?= htmlspecialchars($projet['client_nom']) ?></p>
                        </div>
                        <span class="project-status status-<?= strtolower(str_replace(' ', '-', $projet['statut'])) ?>">
                            <?= htmlspecialchars($projet['statut']) ?>
                        </span>
                    </div>

                    <div class="project-info">
                        <div class="info-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($projet['adresse_projet'] ?: 'Adresse non renseignée') ?></span>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Créé le <?= $projet['date_creation_fr'] ?></span>
                        </div>
                        <?php if ($projet['date_debut']): ?>
                        <div class="info-row">
                            <i class="fas fa-play-circle"></i>
                            <span>Début: <?= $projet['date_debut_fr'] ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="project-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?= $projet['nb_devis'] ?></span>
                            <span class="stat-label">Devis</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?= number_format($projet['total_projets'] ?: 0, 0, ',', ' ') ?></span>
                            <span class="stat-label">FCFA</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?= number_format($projet['budget_previsionnel'], 0, ',', ' ') ?></span>
                            <span class="stat-label">Budget</span>
                        </div>
                    </div>

                    <div class="project-actions">
                        <a href="projet_detail.php?id=<?= $projet['id'] ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Détails
                        </a>
                        <a href="liste_projets.php?projet_id=<?= $projet['id'] ?>" class="btn btn-success">
                            <i class="fas fa-file-invoice"></i> Devis
                        </a>
                        <button class="btn btn-danger" onclick="confirmerSuppression(<?= $projet['id'] ?>, '<?= htmlspecialchars($projet['nom_projet'], ENT_QUOTES) ?>')">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal Nouveau Projet -->
    <div id="modalProjet" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus-circle"></i> Nouveau Projet</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="ajouter">
                
                <div class="form-group">
                    <label for="nom_projet">Nom du projet *</label>
                    <input type="text" id="nom_projet" name="nom_projet" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="client_nom">Nom du client</label>
                        <input type="text" id="client_nom" name="client_nom">
                    </div>
                    <div class="form-group">
                        <label for="client_email">Email client</label>
                        <input type="email" id="client_email" name="client_email">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="client_telephone">Téléphone client</label>
                        <input type="tel" id="client_telephone" name="client_telephone">
                    </div>
                    <div class="form-group">
                        <label for="statut">Statut</label>
                        <select id="statut" name="statut">
                            <option value="En planification">En planification</option>
                            <option value="En cours">En cours</option>
                            <option value="Terminé">Terminé</option>
                            <option value="Suspendu">Suspendu</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="adresse_projet">Adresse du projet</label>
                    <input type="text" id="adresse_projet" name="adresse_projet">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_debut">Date de début</label>
                        <input type="date" id="date_debut" name="date_debut">
                    </div>
                    <div class="form-group">
                        <label for="date_fin_prevue">Date de fin prévue</label>
                        <input type="date" id="date_fin_prevue" name="date_fin_prevue">
                    </div>
                </div>

                <div class="form-group">
                    <label for="budget_previsionnel">Budget prévisionnel (FCFA)</label>
                    <input type="number" id="budget_previsionnel" name="budget_previsionnel" min="0" step="1000">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le projet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalProjet').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modalProjet').style.display = 'none';
        }

        function confirmerSuppression(projetId, nomProjet) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer le projet "${nomProjet}" ?\n\nCette action est irréversible et supprimera également tous les devis associés.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'supprimer';
                
                const projetIdInput = document.createElement('input');
                projetIdInput.type = 'hidden';
                projetIdInput.name = 'projet_id';
                projetIdInput.value = projetId;
                
                form.appendChild(actionInput);
                form.appendChild(projetIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Fermer modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('modalProjet');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>