<?php
// ===== CORRECTIONS_SYSTEME.PHP - Script de Corrections GSN ProDevis360° =====
// Script pour corriger les problèmes identifiés : adresses "Non définie", statuts "Brouillon", montants en lettres

require_once 'functions.php';

// Configuration
$corrections_effectuees = 0;
$erreurs_rencontrees = [];
$rapport_corrections = [];

try {
    $pdo = getDbConnection();
    echo "<h1>🔧 GSN ProDevis360° - Script de Corrections Système</h1>";
    echo "<div style='font-family: Arial; background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px;'>";
    
    echo "<h2>📊 Diagnostic initial</h2>";
    $diagnostic = diagnosticSysteme($pdo);
    
    echo "<div style='background: white; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>Connexion BDD :</strong> " . ($diagnostic['bdd_connexion'] ? '✅ OK' : '❌ ERREUR') . "<br>";
    echo "<strong>Tables existantes :</strong> " . count($diagnostic['tables_existantes']) . "/12<br>";
    echo "<strong>Données cohérentes :</strong> " . ($diagnostic['donnees_coherentes'] ? '✅ OK' : '⚠️ Problèmes détectés') . "<br>";
    
    if (!empty($diagnostic['erreurs'])) {
        echo "<strong>Erreurs détectées :</strong><br>";
        foreach ($diagnostic['erreurs'] as $erreur) {
            echo "• " . htmlspecialchars($erreur) . "<br>";
        }
    }
    echo "</div>";
    
    // CORRECTION 1 : Adresses "Non définie"
    echo "<h2>🏠 Correction des adresses 'Non définie'</h2>";
    
    $stmt = $pdo->query("SELECT id, nom, client, adresse FROM projets WHERE adresse IS NULL OR adresse = '' OR adresse = 'Non définie'");
    $projets_sans_adresse = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($projets_sans_adresse)) {
        echo "<div style='color: green;'>✅ Aucune adresse à corriger</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>Projets avec adresses manquantes :</strong> " . count($projets_sans_adresse) . "<br>";
        
        foreach ($projets_sans_adresse as $projet) {
            $nouvelle_adresse = "Adresse à préciser - " . htmlspecialchars($projet['client']);
            
            try {
                $stmt = $pdo->prepare("UPDATE projets SET adresse = ? WHERE id = ?");
                $stmt->execute([$nouvelle_adresse, $projet['id']]);
                
                echo "✅ Projet #{$projet['id']} ({$projet['nom']}) : Adresse corrigée<br>";
                $corrections_effectuees++;
                $rapport_corrections[] = "Adresse corrigée pour projet #{$projet['id']}";
                
            } catch (PDOException $e) {
                $erreur = "Erreur correction adresse projet #{$projet['id']} : " . $e->getMessage();
                echo "❌ $erreur<br>";
                $erreurs_rencontrees[] = $erreur;
            }
        }
        echo "</div>";
    }
    
    // CORRECTION 2 : Statuts "Brouillon" vers "En cours"
    echo "<h2>📝 Correction des statuts 'Brouillon'</h2>";
    
    $stmt = $pdo->query("SELECT id, numero, statut FROM devis WHERE statut = 'brouillon' OR statut = 'Brouillon'");
    $devis_brouillon = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($devis_brouillon)) {
        echo "<div style='color: green;'>✅ Aucun statut 'Brouillon' à corriger</div>";
    } else {
        echo "<div style='background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>Devis en statut 'Brouillon' :</strong> " . count($devis_brouillon) . "<br>";
        
        foreach ($devis_brouillon as $devis) {
            try {
                $stmt = $pdo->prepare("UPDATE devis SET statut = 'en_cours' WHERE id = ?");
                $stmt->execute([$devis['id']]);
                
                echo "✅ Devis {$devis['numero']} : Statut changé de 'Brouillon' vers 'En cours'<br>";
                $corrections_effectuees++;
                $rapport_corrections[] = "Statut corrigé pour devis {$devis['numero']}";
                
            } catch (PDOException $e) {
                $erreur = "Erreur correction statut devis {$devis['numero']} : " . $e->getMessage();
                echo "❌ $erreur<br>";
                $erreurs_rencontrees[] = $erreur;
            }
        }
        echo "</div>";
    }
    
    // CORRECTION 3 : Vérification et correction des récapitulatifs
    echo "<h2>📋 Vérification des récapitulatifs</h2>";
    
    $stmt = $pdo->query("
        SELECT d.id as devis_id, d.projet_id, d.numero, COUNT(r.id) as nb_categories
        FROM devis d 
        LEFT JOIN recapitulatif r ON d.id = r.devis_id AND d.projet_id = r.projet_id
        GROUP BY d.id, d.projet_id, d.numero
        HAVING nb_categories < 9
    ");
    $devis_recaps_incomplets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($devis_recaps_incomplets)) {
        echo "<div style='color: green;'>✅ Tous les récapitulatifs sont complets</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>Devis avec récapitulatifs incomplets :</strong> " . count($devis_recaps_incomplets) . "<br>";
        
        foreach ($devis_recaps_incomplets as $devis) {
            try {
                initializeRecapitulatifCategories($pdo, $devis['projet_id'], $devis['devis_id']);
                
                echo "✅ Devis {$devis['numero']} : Récapitulatif initialisé ({$devis['nb_categories']}/9 catégories)<br>";
                $corrections_effectuees++;
                $rapport_corrections[] = "Récapitulatif initialisé pour devis {$devis['numero']}";
                
            } catch (Exception $e) {
                $erreur = "Erreur initialisation récapitulatif devis {$devis['numero']} : " . $e->getMessage();
                echo "❌ $erreur<br>";
                $erreurs_rencontrees[] = $erreur;
            }
        }
        echo "</div>";
    }
    
    // CORRECTION 4 : Mise à jour des totaux dans les récapitulatifs
    echo "<h2>🔢 Mise à jour des totaux des récapitulatifs</h2>";
    
    $modules = ['materiaux_base', 'plomberie', 'menuiserie', 'electricite', 'peinture', 'charpenterie', 'carrelage', 'ferraillage', 'ferronnerie'];
    $totaux_mis_a_jour = 0;
    
    $stmt = $pdo->query("SELECT DISTINCT projet_id, devis_id FROM recapitulatif ORDER BY projet_id, devis_id");
    $devis_a_recalculer = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($devis_a_recalculer as $devis) {
        foreach ($modules as $module) {
            try {
                if (updateRecapitulatif($pdo, $devis['projet_id'], $devis['devis_id'], $module)) {
                    $totaux_mis_a_jour++;
                }
            } catch (Exception $e) {
                $erreurs_rencontrees[] = "Erreur mise à jour $module pour projet {$devis['projet_id']}/devis {$devis['devis_id']} : " . $e->getMessage();
            }
        }
    }
    
    echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ Totaux mis à jour : $totaux_mis_a_jour modules traités<br>";
    echo "</div>";
    
    // CORRECTION 5 : Nettoyage des données orphelines
    echo "<h2>🧹 Nettoyage des données orphelines</h2>";
    
    try {
        if (nettoyerDonnees($pdo)) {
            echo "<div style='color: green;'>✅ Nettoyage des données orphelines effectué</div>";
            $corrections_effectuees++;
            $rapport_corrections[] = "Nettoyage des données orphelines";
        } else {
            echo "<div style='color: orange;'>⚠️ Erreur lors du nettoyage des données</div>";
        }
    } catch (Exception $e) {
        $erreur = "Erreur nettoyage données : " . $e->getMessage();
        echo "<div style='color: red;'>❌ $erreur</div>";
        $erreurs_rencontrees[] = $erreur;
    }
    
    // CORRECTION 6 : Test de la fonction montant en lettres
    echo "<h2>💰 Test de la conversion montant en lettres</h2>";
    
    $montants_test = [1, 50, 100, 1000, 1500, 25000, 100000, 1000000, 1500000];
    
    echo "<div style='background: white; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<table style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #f8f9fa;'><th style='padding: 8px; border: 1px solid #ddd;'>Montant (FCFA)</th><th style='padding: 8px; border: 1px solid #ddd;'>En lettres</th></tr>";
    
    foreach ($montants_test as $montant) {
        $en_lettres = montantEnLettres($montant);
        $couleur = (strpos($en_lettres, 'trop élevé') !== false) ? 'color: red;' : 'color: green;';
        echo "<tr>";
        echo "<td style='padding: 8px; border: 1px solid #ddd; text-align: right;'>" . number_format($montant, 0, ',', ' ') . "</td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd; $couleur'>$en_lettres</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // CORRECTION 7 : Création de la table historique si manquante
    echo "<h2>📚 Vérification table historique</h2>";
    
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'historique_devis'");
        if (!$stmt->fetch()) {
            if (creerTableHistorique($pdo)) {
                echo "<div style='color: green;'>✅ Table historique_devis créée</div>";
                $corrections_effectuees++;
                $rapport_corrections[] = "Table historique_devis créée";
            } else {
                echo "<div style='color: red;'>❌ Erreur création table historique_devis</div>";
            }
        } else {
            echo "<div style='color: green;'>✅ Table historique_devis existe déjà</div>";
        }
    } catch (Exception $e) {
        $erreur = "Erreur vérification table historique : " . $e->getMessage();
        echo "<div style='color: red;'>❌ $erreur</div>";
        $erreurs_rencontrees[] = $erreur;
    }
    
    // RAPPORT FINAL
    echo "<h2>📊 Rapport final des corrections</h2>";
    
    echo "<div style='background: white; padding: 20px; border-radius: 8px; border: 2px solid #28a745;'>";
    echo "<h3 style='color: #28a745; margin-top: 0;'>✅ Corrections effectuées : $corrections_effectuees</h3>";
    
    if (!empty($rapport_corrections)) {
        echo "<ul>";
        foreach ($rapport_corrections as $correction) {
            echo "<li>" . htmlspecialchars($correction) . "</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($erreurs_rencontrees)) {
        echo "<h3 style='color: #dc3545;'>❌ Erreurs rencontrées : " . count($erreurs_rencontrees) . "</h3>";
        echo "<ul>";
        foreach ($erreurs_rencontrees as $erreur) {
            echo "<li style='color: #dc3545;'>" . htmlspecialchars($erreur) . "</li>";
        }
        echo "</ul>";
    }
    
    // Diagnostic final
    echo "<h3>🔍 Diagnostic final</h3>";
    $diagnostic_final = diagnosticSysteme($pdo);
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
    echo "<strong>Connexion BDD :</strong> " . ($diagnostic_final['bdd_connexion'] ? '✅ OK' : '❌ ERREUR') . "<br>";
    echo "<strong>Tables existantes :</strong> " . count($diagnostic_final['tables_existantes']) . "/12<br>";
    echo "<strong>Données cohérentes :</strong> " . ($diagnostic_final['donnees_coherentes'] ? '✅ OK' : '⚠️ Problèmes persistants') . "<br>";
    
    if (!empty($diagnostic_final['erreurs'])) {
        echo "<strong>Problèmes persistants :</strong><br>";
        foreach ($diagnostic_final['erreurs'] as $erreur) {
            echo "• " . htmlspecialchars($erreur) . "<br>";
        }
    } else {
        echo "<strong style='color: #28a745;'>🎉 Système entièrement corrigé !</strong>";
    }
    echo "</div>";
    
    echo "</div>";
    
    // Boutons d'action
    echo "<div style='margin: 20px 0; text-align: center;'>";
    echo "<a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🏠 Retour à l'accueil</a>";
    echo "<a href='liste_projets.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>📁 Liste des projets</a>";
    echo "<a href='?run_again=1' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔄 Relancer les corrections</a>";
    echo "</div>";
    
    echo "</div>";
    
    // Log des corrections
    $log_message = date('Y-m-d H:i:s') . " - Corrections système : $corrections_effectuees corrections, " . count($erreurs_rencontrees) . " erreurs";
    error_log($log_message);
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin: 20px;'>";
    echo "<h2>❌ Erreur critique</h2>";
    echo "<p><strong>Message :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Fichier :</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Ligne :</strong> " . $e->getLine() . "</p>";
    echo "<p><a href='index.php' style='color: #721c24;'>← Retour à l'accueil</a></p>";
    echo "</div>";
    
    error_log("Erreur critique corrections_systeme.php : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corrections Système - GSN ProDevis360°</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        h1 {
            background: linear-gradient(135deg, #FF6B35, #F44336);
            color: white;
            margin: 0;
            padding: 30px;
            text-align: center;
            font-size: 2rem;
        }
        
        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
            margin-top: 30px;
        }
        
        h3 {
            color: #34495e;
            margin-top: 20px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            margin: 10px 0;
        }
        
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ffeaa7;
            margin: 10px 0;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            margin: 10px 0;
        }
        
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #bee5eb;
            margin: 10px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 5px;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: black;
        }
        
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #ecf0f1;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 1s ease;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 10px;
            }
            
            h1 {
                font-size: 1.5rem;
                padding: 20px;
            }
            
            table {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Le contenu PHP est affiché ici -->
    </div>
    
    <script>
        // Animation de progression
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });
            
            // Scroll automatique vers le rapport final
            setTimeout(() => {
                const rapport = document.querySelector('h2:last-of-type');
                if (rapport) {
                    rapport.scrollIntoView({ behavior: 'smooth' });
                }
            }, 2000);
        });
        
        // Confirmation pour relancer
        document.addEventListener('click', function(e) {
            if (e.target.href && e.target.href.includes('run_again=1')) {
                if (!confirm('Êtes-vous sûr de vouloir relancer toutes les corrections ?')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>