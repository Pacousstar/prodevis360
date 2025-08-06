<?php
// ===== FUNCTIONS.PHP - VERSION COMPATIBLE AVEC ANCIENS MODULES =====
// Version qui marche avec les appels existants sans les modifier

// Configuration de la base de données
function getDbConnection() {
    try {
        $host = 'localhost';
        $dbname = 'u370633571_ProDevis360';
        $username = 'u370633571_Pacous07';
        $password = 'PDev360@';
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erreur de connexion BDD: " . $e->getMessage());
        throw new Exception("Erreur de connexion à la base de données");
    }
}

// Fonction sécurisée pour récupérer les paramètres
function secureGetParam($param, $type = 'string', $default = null) {
    $value = $_GET[$param] ?? $_POST[$param] ?? $default;
    
    switch ($type) {
        case 'int':
            return (int)$value;
        case 'float':
            return (float)str_replace(',', '.', $value);
        case 'string':
        default:
            return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }
}

// ===== FONCTION COMPATIBLE AVEC 2 OU 3 PARAMÈTRES =====
function getProjetDevisInfo($param1, $param2 = null, $param3 = null) {
    try {
        // Si 3 paramètres : getProjetDevisInfo($pdo, $projet_id, $devis_id)
        if ($param3 !== null) {
            $pdo = $param1;
            $projet_id = $param2;
            $devis_id = $param3;
        }
        // Si 2 paramètres : getProjetDevisInfo($projet_id, $devis_id) - ancien format
        else {
            $pdo = getDbConnection();
            $projet_id = $param1;
            $devis_id = $param2;
        }
        
        $stmt = $pdo->prepare("
            SELECT p.id as projet_id, p.nom as projet_nom, p.client, p.adresse, 
                   p.description as projet_description, p.date_creation as projet_date_creation,
                   d.id as devis_id, d.numero as devis_numero, d.description as devis_description, 
                   d.statut, d.date_creation as devis_date_creation
            FROM projets p 
            JOIN devis d ON p.id = d.projet_id 
            WHERE p.id = ? AND d.id = ?
        ");
        $stmt->execute([$projet_id, $devis_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur getProjetDevisInfo: " . $e->getMessage());
        return false;
    }
}

// ===== FONCTION CORRIGÉE - INITIALISATION DES CATÉGORIES =====
function initializeRecapitulatifCategories($pdo, $projet_id, $devis_id) {
    $categories = [
        'materiaux_base' => 'Matériaux de Base',
        'plomberie' => 'Plomberie',
        'menuiserie' => 'Menuiserie',
        'electricite' => 'Électricité',
        'peinture' => 'Peinture',
        'charpenterie' => 'Charpenterie',
        'carrelage' => 'Carrelage',
        'ferraillage' => 'Ferraillage',
        'ferronnerie' => 'Ferronnerie'
    ];
    
    try {
        // Vérifier d'abord si la table recapitulatif existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'recapitulatif'");
        if (!$stmt->fetch()) {
            // Créer la table si elle n'existe pas
            $createTable = "
                CREATE TABLE recapitulatif (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    projet_id INT NOT NULL,
                    devis_id INT NOT NULL,
                    categorie VARCHAR(50) NOT NULL,
                    nom_categorie VARCHAR(100) NOT NULL,
                    total_materiaux DECIMAL(15,2) DEFAULT 0,
                    total_transport DECIMAL(15,2) DEFAULT 0,
                    main_oeuvre DECIMAL(15,2) DEFAULT 0,
                    main_oeuvre_maconnerie DECIMAL(15,2) DEFAULT 0,
                    total_ht DECIMAL(15,2) DEFAULT 0,
                    taux_tva DECIMAL(5,2) DEFAULT 18.0,
                    montant_tva DECIMAL(15,2) DEFAULT 0,
                    total_ttc DECIMAL(15,2) DEFAULT 0,
                    total_general DECIMAL(15,2) DEFAULT 0,
                    nb_elements INT DEFAULT 0,
                    date_rapport TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_recap (projet_id, devis_id, categorie),
                    INDEX idx_projet_devis (projet_id, devis_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ";
            $pdo->exec($createTable);
        }
        
        foreach ($categories as $key => $nom) {
            // Utiliser INSERT IGNORE pour éviter les doublons
            $stmt = $pdo->prepare("
                INSERT IGNORE INTO recapitulatif (
                    projet_id, devis_id, categorie, nom_categorie, 
                    total_materiaux, total_transport, main_oeuvre, main_oeuvre_maconnerie,
                    total_ht, taux_tva, montant_tva, total_ttc, total_general, nb_elements
                ) VALUES (?, ?, ?, ?, 0, 0, 0, 0, 0, 18.0, 0, 0, 0, 0)
            ");
            $stmt->execute([$projet_id, $devis_id, $key, $nom]);
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Erreur initializeRecapitulatifCategories: " . $e->getMessage());
        // Ne pas lancer d'exception, juste logger l'erreur
        return false;
    }
}

// Mise à jour du récapitulatif pour un module
function updateRecapitulatif($pdo, $projet_id, $devis_id, $module = null) {
    try {
        // Si aucun module spécifié, mettre à jour tous les modules
        if ($module === null) {
            $modules = ['materiaux_base', 'plomberie', 'menuiserie', 'electricite', 'peinture', 'charpenterie', 'carrelage', 'ferraillage', 'ferronnerie'];
            foreach ($modules as $mod) {
                updateRecapitulatif($pdo, $projet_id, $devis_id, $mod);
            }
            
            // Calculer le total général
            calculerTotalGeneral($pdo, $projet_id, $devis_id);
            return true;
        }
        
        // Vérifier si la table du module existe
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$module]);
        if (!$stmt->fetch()) {
            // Table n'existe pas, initialiser avec des zéros
            updateCategorieRecap($pdo, $projet_id, $devis_id, $module, 0, 0, 0);
            return false;
        }
        
        // Calculer les totaux du module
        $stmt = $pdo->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN pt IS NOT NULL AND pt > 0 THEN pt ELSE pu * quantite END), 0) as total_materiaux,
                COALESCE(SUM(transport), 0) as total_transport,
                COUNT(*) as nb_elements
            FROM `$module` 
            WHERE projet_id = ? AND devis_id = ?
        ");
        $stmt->execute([$projet_id, $devis_id]);
        $totaux = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Mettre à jour la catégorie
        updateCategorieRecap($pdo, $projet_id, $devis_id, $module, $totaux['total_materiaux'], $totaux['total_transport'], $totaux['nb_elements']);
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Erreur updateRecapitulatif pour $module: " . $e->getMessage());
        return false;
    }
}

// Fonction auxiliaire pour mettre à jour une catégorie
function updateCategorieRecap($pdo, $projet_id, $devis_id, $categorie, $total_materiaux, $total_transport, $nb_elements) {
    try {
        // Calculer les totaux
        $total_ht = $total_materiaux + $total_transport;
        $taux_tva = 18.0;
        $montant_tva = $total_ht * ($taux_tva / 100);
        $total_ttc = $total_ht + $montant_tva;
        
        $stmt = $pdo->prepare("
            UPDATE recapitulatif SET 
                total_materiaux = ?, 
                total_transport = ?, 
                total_ht = ?, 
                montant_tva = ?, 
                total_ttc = ?, 
                nb_elements = ?,
                date_rapport = NOW()
            WHERE projet_id = ? AND devis_id = ? AND categorie = ?
        ");
        
        return $stmt->execute([
            $total_materiaux, $total_transport, $total_ht, 
            $montant_tva, $total_ttc, $nb_elements,
            $projet_id, $devis_id, $categorie
        ]);
        
    } catch (PDOException $e) {
        error_log("Erreur updateCategorieRecap: " . $e->getMessage());
        return false;
    }
}

// Calculer le total général du devis
function calculerTotalGeneral($pdo, $projet_id, $devis_id) {
    try {
        // Calculer la somme de toutes les catégories
        $stmt = $pdo->prepare("
            SELECT SUM(total_ttc + COALESCE(main_oeuvre, 0) + COALESCE(main_oeuvre_maconnerie, 0)) as total_general
            FROM recapitulatif 
            WHERE projet_id = ? AND devis_id = ?
        ");
        $stmt->execute([$projet_id, $devis_id]);
        $result = $stmt->fetch();
        $total_general = $result['total_general'] ?? 0;
        
        // Mettre à jour toutes les lignes du récapitulatif avec le total général
        $stmt = $pdo->prepare("
            UPDATE recapitulatif 
            SET total_general = ? 
            WHERE projet_id = ? AND devis_id = ?
        ");
        $stmt->execute([$total_general, $projet_id, $devis_id]);
        
        return $total_general;
        
    } catch (PDOException $e) {
        error_log("Erreur calculerTotalGeneral: " . $e->getMessage());
        return 0;
    }
}

// Navigation dynamique entre modules
function getNavigationModules($modules_config, $current_module) {
    $navigation = [];
    foreach ($modules_config as $key => $config) {
        $navigation[] = [
            'key' => $key,
            'name' => $config['name'],
            'icon' => $config['icon'],
            'color' => $config['color'],
            'active' => ($key === $current_module)
        ];
    }
    return $navigation;
}

// Sauvegarde de l'historique des actions
function sauvegarderHistorique($pdo, $projet_id, $devis_id, $module, $action, $details) {
    try {
        // Vérifier si la table historique existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'historique'");
        if (!$stmt->fetch()) {
            // Créer la table historique si elle n'existe pas
            $createTable = "
                CREATE TABLE historique (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    projet_id INT NOT NULL,
                    devis_id INT NOT NULL,
                    module VARCHAR(50) NOT NULL,
                    action VARCHAR(50) NOT NULL,
                    details TEXT,
                    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_projet_devis (projet_id, devis_id),
                    INDEX idx_module (module),
                    INDEX idx_date (date_action)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ";
            $pdo->exec($createTable);
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO historique (projet_id, devis_id, module, action, details) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$projet_id, $devis_id, $module, $action, $details]);
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Erreur sauvegarderHistorique: " . $e->getMessage());
        return false;
    }
}

// Duplication de devis
function duppliquerDevis($pdo, $projet_id, $devis_id_source, $nouvelle_description) {
    try {
        $pdo->beginTransaction();
        
        // Créer le nouveau devis
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM devis WHERE projet_id = ?");
        $stmt->execute([$projet_id]);
        $nb_devis = $stmt->fetchColumn();
        
        $nouveau_numero = 'DEV-' . str_pad($projet_id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($nb_devis + 1, 3, '0', STR_PAD_LEFT);
        
        $stmt = $pdo->prepare("
            INSERT INTO devis (projet_id, numero, description, statut) 
            VALUES (?, ?, ?, 'brouillon')
        ");
        $stmt->execute([$projet_id, $nouveau_numero, $nouvelle_description]);
        $nouveau_devis_id = $pdo->lastInsertId();
        
        // Copier les données de tous les modules
        $modules = ['materiaux_base', 'plomberie', 'menuiserie', 'electricite', 'peinture', 'charpenterie', 'carrelage', 'ferraillage', 'ferronnerie'];
        
        foreach ($modules as $module) {
            $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$module]);
            if ($stmt->fetch()) {
                $stmt = $pdo->prepare("
                    INSERT INTO `$module` (projet_id, devis_id, designation, unite, quantite, pu, pt, transport, observation)
                    SELECT ?, ?, designation, unite, quantite, pu, pt, transport, observation 
                    FROM `$module` 
                    WHERE projet_id = ? AND devis_id = ?
                ");
                $stmt->execute([$projet_id, $nouveau_devis_id, $projet_id, $devis_id_source]);
            }
        }
        
        // Initialiser le récapitulatif pour le nouveau devis
        initializeRecapitulatifCategories($pdo, $projet_id, $nouveau_devis_id);
        
        // Mettre à jour le récapitulatif
        updateRecapitulatif($pdo, $projet_id, $nouveau_devis_id);
        
        // Sauvegarder l'historique
        sauvegarderHistorique($pdo, $projet_id, $nouveau_devis_id, 'general', 'duplication', "Devis dupliqué depuis le devis #$devis_id_source");
        
        $pdo->commit();
        return $nouveau_devis_id;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Erreur duppliquerDevis: " . $e->getMessage());
        throw new Exception("Erreur lors de la duplication: " . $e->getMessage());
    }
}

// Diagnostic du système
function diagnosticSysteme($pdo) {
    $diagnostic = [
        'bdd_connexion' => false,
        'tables_existantes' => [],
        'donnees_coherentes' => true,
        'erreurs' => []
    ];
    
    try {
        // Test connexion
        $pdo->query("SELECT 1");
        $diagnostic['bdd_connexion'] = true;
        
        // Vérifier les tables principales
        $tables_requises = ['projets', 'devis', 'recapitulatif', 'plomberie', 'menuiserie', 'electricite', 'peinture', 'charpenterie', 'carrelage', 'ferraillage', 'ferronnerie', 'materiaux_base'];
        
        foreach ($tables_requises as $table) {
            $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            if ($stmt->fetch()) {
                $diagnostic['tables_existantes'][] = $table;
            } else {
                $diagnostic['erreurs'][] = "Table manquante: $table";
                $diagnostic['donnees_coherentes'] = false;
            }
        }
        
    } catch (Exception $e) {
        $diagnostic['erreurs'][] = "Erreur diagnostic: " . $e->getMessage();
        $diagnostic['donnees_coherentes'] = false;
    }
    
    return $diagnostic;
}

?>