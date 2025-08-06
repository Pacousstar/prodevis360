<?php
// ===== FUNCTIONS.PHP - FONCTIONS COMMUNES PRODEVIS360° =====
// Version finale - À installer EN PREMIER

/**
 * Connexion à la base de données
 * @return PDO Instance de connexion PDO
 */
function getDbConnection() {
    // ⚠️ VÉRIFIEZ CES PARAMÈTRES AVEC VOS VRAIS PARAMÈTRES ⚠️
    $host = 'localhost';
    $dbname = 'u370633571_ProDevis360';
    $username = 'u370633571_Pacous07';
    $password = 'PDev360@';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

/**
 * Récupération sécurisée des paramètres GET/POST
 * @param string $param Nom du paramètre
 * @param string $type Type attendu (int, string, float)
 * @param mixed $default Valeur par défaut
 * @return mixed Valeur sécurisée
 */
function secureGetParam($param, $type = 'string', $default = null) {
    $value = $_GET[$param] ?? $_POST[$param] ?? $default;
    
    switch ($type) {
        case 'int':
            return is_numeric($value) ? (int)$value : (int)$default;
        case 'float':
            return is_numeric($value) ? (float)$value : (float)$default;
        case 'string':
        default:
            return is_string($value) ? trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8')) : (string)$default;
    }
}

/**
 * Récupération des informations projet et devis
 * @param PDO $pdo Connexion à la base
 * @param int $projet_id ID du projet
 * @param int $devis_id ID du devis
 * @return array|false Informations ou false si non trouvé
 */
function getProjetDevisInfo($pdo, $projet_id, $devis_id) {
    try {
        // Requête projet
        $stmt = $pdo->prepare("SELECT * FROM projets WHERE id = ?");
        $stmt->execute([$projet_id]);
        $projet = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$projet) {
            error_log("Projet non trouvé (ID: $projet_id)");
            return false;
        }

        // Requête devis avec vérification d'appartenance
        $stmt = $pdo->prepare("SELECT * FROM devis WHERE id = ? AND projet_id = ?");
        $stmt->execute([$devis_id, $projet_id]);
        $devis = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$devis) {
            error_log("Devis non trouvé ou n'appartient pas au projet (Devis ID: $devis_id, Projet ID: $projet_id)");
            return false;
        }

        return [
            'projet' => $projet,
            'devis' => $devis,
            'infos' => [
                'projet_nom' => $projet['nom'],
                'projet_client' => $projet['client'],
                'devis_numero' => $devis['numero'],
                'devis_statut' => $devis['statut']
            ]
        ];
    } catch (PDOException $e) {
        error_log("Erreur getProjetDevisInfo: " . $e->getMessage());
        return false;
    }
}

/**
 * Initialisation des catégories dans le récapitulatif
 * @param PDO $pdo Connexion à la base
 * @param int $projet_id ID du projet
 * @param int $devis_id ID du devis
 */
function initializeRecapitulatifCategories($pdo, $projet_id, $devis_id) {
    $categories = [
        'materiaux_base' => 'Matériaux de base',
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
        foreach ($categories as $categorie => $nom) {
            $stmt = $pdo->prepare("
                INSERT IGNORE INTO recapitulatif 
                (projet_id, devis_id, categorie, total_materiaux, total_transport, main_oeuvre, total_ht, taux_tva, montant_tva, total_ttc, date_rapport)
                VALUES (?, ?, ?, 0, 0, 0, 0, 18.0, 0, 0, NOW())
            ");
            $stmt->execute([$projet_id, $devis_id, $categorie]);
        }
    } catch (PDOException $e) {
        error_log("Erreur initializeRecapitulatifCategories : " . $e->getMessage());
    }
}

/**
 * Mise à jour du récapitulatif pour une catégorie
 * @param PDO $pdo Connexion à la base
 * @param int $projet_id ID du projet
 * @param int $devis_id ID du devis
 * @param string $categorie Nom de la catégorie (optionnel)
 */
function updateRecapitulatif($pdo, $projet_id, $devis_id, $categorie = null) {
    try {
        // Configuration des catégories avec leurs mains d'œuvre
        $categories_config = [
            'materiaux_base' => ['table' => 'materiaux_base', 'main_oeuvre' => 600000],
            'plomberie' => ['table' => 'plomberie', 'main_oeuvre' => 400000],
            'menuiserie' => ['table' => 'menuiserie', 'main_oeuvre' => 350000],
            'electricite' => ['table' => 'electricite', 'main_oeuvre' => 300000],
            'peinture' => ['table' => 'peinture', 'main_oeuvre' => 250000],
            'charpenterie' => ['table' => 'charpenterie', 'main_oeuvre' => 500000],
            'carrelage' => ['table' => 'carrelage', 'main_oeuvre' => 450000],
            'ferraillage' => ['table' => 'ferraillage', 'main_oeuvre' => 200000],
            'ferronnerie' => ['table' => 'ferronnerie', 'main_oeuvre' => 300000]
        ];
        
        // Si aucune catégorie spécifiée, mettre à jour toutes
        $categories_to_update = $categorie ? [$categorie => $categories_config[$categorie]] : $categories_config;
        
        foreach ($categories_to_update as $cat_name => $config) {
            // Calculer les totaux pour cette catégorie
            $stmt = $pdo->prepare("
                SELECT 
                    COALESCE(SUM(pt), 0) as total_materiaux,
                    COALESCE(SUM(transport), 0) as total_transport
                FROM {$config['table']}
                WHERE projet_id = ? AND devis_id = ?
            ");
            $stmt->execute([$projet_id, $devis_id]);
            $totals = $stmt->fetch();
            
            // Calculs
            $main_oeuvre = $config['main_oeuvre'];
            $taux_tva = 18.0;
            $total_ht = $totals['total_materiaux'] + $totals['total_transport'] + $main_oeuvre;
            $montant_tva = $total_ht * $taux_tva / 100;
            $total_ttc = $total_ht + $montant_tva;
            
            // Mise à jour du récapitulatif
            $stmt = $pdo->prepare("
                INSERT INTO recapitulatif 
                (projet_id, devis_id, categorie, total_materiaux, total_transport, main_oeuvre, total_ht, taux_tva, montant_tva, total_ttc, date_rapport)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                total_materiaux = VALUES(total_materiaux),
                total_transport = VALUES(total_transport),
                main_oeuvre = VALUES(main_oeuvre),
                total_ht = VALUES(total_ht),
                montant_tva = VALUES(montant_tva),
                total_ttc = VALUES(total_ttc),
                updated_at = NOW()
            ");
            $stmt->execute([
                $projet_id,
                $devis_id,
                $cat_name,
                $totals['total_materiaux'],
                $totals['total_transport'],
                $main_oeuvre,
                $total_ht,
                $taux_tva,
                $montant_tva,
                $total_ttc
            ]);
        }
    } catch (PDOException $e) {
        error_log("Erreur updateRecapitulatif : " . $e->getMessage());
        throw $e;
    }
}

/**
 * Conversion d'un nombre en lettres (français)
 * @param float $nombre Le nombre à convertir
 * @return string Le nombre en lettres
 */
function nombreEnLettres($nombre) {
    if ($nombre == 0) return 'zéro';
    if ($nombre > 999999999) return 'montant trop élevé pour conversion';
    
    $unite = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
    $dixaine = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
    $centaine = ['', 'cent', 'deux cent', 'trois cent', 'quatre cent', 'cinq cent', 'six cent', 'sept cent', 'huit cent', 'neuf cent'];
    
    $resultat = '';
    $nombre = (int)$nombre;
    
    // Millions
    if ($nombre >= 1000000) {
        $millions = intval($nombre / 1000000);
        if ($millions == 1) {
            $resultat .= 'un million ';
        } else {
            $resultat .= convertirNombreBasique($millions) . ' millions ';
        }
        $nombre = $nombre % 1000000;
    }
    
    // Milliers
    if ($nombre >= 1000) {
        $milliers = intval($nombre / 1000);
        if ($milliers == 1) {
            $resultat .= 'mille ';
        } else {
            $resultat .= convertirNombreBasique($milliers) . ' mille ';
        }
        $nombre = $nombre % 1000;
    }
    
    // Centaines, dizaines, unités
    if ($nombre > 0) {
        $resultat .= convertirNombreBasique($nombre);
    }
    
    return trim($resultat);
}


/**
 * Formatage sécurisé pour l'affichage
 * @param mixed $value Valeur à formatter
 * @param string $type Type de formatage (text, number, currency)
 * @return string Valeur formatée
 */
function formatDisplay($value, $type = 'text') {
    switch ($type) {
        case 'number':
            return number_format((float)$value, 0, ',', ' ');
        case 'currency':
            return number_format((float)$value, 0, ',', ' ') . ' FCFA';
        case 'text':
        default:
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Génération d'un token CSRF
 * @return string Token CSRF
 */
function generateCSRFToken() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Validation d'un token CSRF
 * @param string $token Token à valider
 * @return bool True si valide
 */
function validateCSRFToken($token) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Log d'activité utilisateur
 * @param string $action Action effectuée
 * @param int $projet_id ID du projet (optionnel)
 * @param int $devis_id ID du devis (optionnel)
 */
function logActivity($action, $projet_id = null, $devis_id = null) {
    $log_entry = date('Y-m-d H:i:s') . " - " . $action;
    if ($projet_id) $log_entry .= " - Projet: $projet_id";
    if ($devis_id) $log_entry .= " - Devis: $devis_id";
    $log_entry .= " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . PHP_EOL;
    
    error_log($log_entry, 3, 'logs/prodevis_activity.log');
}

/**
 * Validation des droits d'accès (pour extensions futures)
 * @param int $projet_id ID du projet
 * @param int $devis_id ID du devis
 * @return bool True si autorisé
 */
function checkAccess($projet_id, $devis_id) {
    // Pour l'instant, accès libre
    // À étendre avec un système d'authentification
    return true;
}

/**
 * Nettoyage et optimisation de la base de données
 * @param PDO $pdo Connexion à la base
 */
function cleanupDatabase($pdo) {
    try {
        // Suppression des récapitulatifs orphelins
        $pdo->exec("DELETE r FROM recapitulatif r 
                   LEFT JOIN devis d ON r.projet_id = d.projet_id AND r.devis_id = d.id 
                   WHERE d.id IS NULL");
        
        // Suppression des devis orphelins
        $pdo->exec("DELETE d FROM devis d 
                   LEFT JOIN projets p ON d.projet_id = p.id 
                   WHERE p.id IS NULL");
                   
        logActivity("Database cleanup completed");
    } catch (PDOException $e) {
        error_log("Erreur cleanup : " . $e->getMessage());
    }
}

// ===== CONSTANTES GLOBALES =====
define('PRODEVIS_VERSION', '2.0.0');
define('TVA_RATE', 18.0);
define('CURRENCY', 'FCFA');
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i');

// ===== CONFIGURATION D'ERREURS =====
if (!defined('PRODUCTION')) {
    define('PRODUCTION', false);
}

if (PRODUCTION) {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', 'logs/prodevis_errors.log');
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// ===== AUTO-NETTOYAGE PÉRIODIQUE =====
// Nettoie la base une fois par jour automatiquement
if (rand(1, 100) === 1) {
    $pdo = getDbConnection();
    cleanupDatabase($pdo);
}

// ===== FIN FUNCTIONS.PHP =====
?>