<?php
// ===== DÉBUT FAQ.PHP - PARTIE 3/6 =====
// Page FAQ avec design moderne pour ProDevis360°

require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Questions Fréquentes | ProDevis360°</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== STYLES FAQ ULTRA-MODERNE ===== */
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --orange: #fd7e14;
            --purple: #6f42c1;
            --glass: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-soft: 0 8px 32px rgba(0, 0, 0, 0.1);
            --gradient-bg: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI',
            
body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: var(--gradient-bg);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Hero */
        .faq-hero {
            text-align: center;
            padding: 80px 0;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .faq-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.1), transparent 50%);
            animation: heroGlow 8s ease-in-out infinite alternate;
        }

        @keyframes heroGlow {
            0% { transform: translateX(-20px) scale(1); }
            100% { transform: translateX(20px) scale(1.1); }
        }

        .faq-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #fff, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 2;
        }

        .faq-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 40px;
            position: relative;
            z-index: 2;
        }

        .search-box {
            position: relative;
            max-width: 500px;
            margin: 0 auto;
            z-index: 2;
        }

        .search-input {
            width: 100%;
            padding: 18px 60px 18px 20px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            color: white;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .search-input:focus {
            transform: scale(1.02);
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.3);
        }

        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--orange);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background: #e76e14;
            transform: translateY(-50%) scale(1.1);
        }

        /* Navigation */
        .nav-faq {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-soft);
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .nav-link {
            padding: 12px 24px;
            background: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--orange);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(253, 126, 20, 0.3);
        }

        /* Grille FAQ */
        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .faq-card {
            background: white;
            border-radius: 20px;
            padding: 0;
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            transition: all 0.4s ease;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.6s ease forwards;
        }

        .faq-card:nth-child(1) { animation-delay: 0.1s; }
        .faq-card:nth-child(2) { animation-delay: 0.2s; }
        .faq-card:nth-child(3) { animation-delay: 0.3s; }
        .faq-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .faq-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 25px;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255,255,255,0.1), transparent 70%);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .card-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .card-header .category-badge {
            display: inline-block;
            padding: 5px 12px;
            background: rgba(255,255,255,0.2);
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .card-body {
            padding: 30px;
        }

        .faq-item {
            margin-bottom: 20px;
            border: 1px solid #eee;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            border-color: var(--secondary);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.1);
        }

        .faq-question {
            padding: 18px 20px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--primary);
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            background: #e9ecef;
            color: var(--secondary);
        }

        .faq-question.active {
            background: var(--secondary);
            color: white;
        }

        .faq-toggle {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .faq-question.active .faq-toggle {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s ease;
            background: white;
        }

        .faq-answer.active {
            padding: 20px;
            max-height: 500px;
        }

        .faq-answer p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .faq-answer ul {
            color: #666;
            margin-left: 20px;
        }

        .faq-answer li {
            margin-bottom: 8px;
        }

        /* Contact Section */
        .contact-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: var(--shadow-soft);
            margin-bottom: 40px;
        }

        .contact-title {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 15px;
            font-weight: 700;
        }

        .contact-subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .contact-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .contact-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .contact-btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
        }

        .contact-btn-success {
            background: linear-gradient(135deg, var(--success), #2ecc71);
            color: white;
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
        }

        .contact-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.2);
        }

        /* Footer Navigation */
        .footer-nav {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
        }

        .footer-nav h4 {
            color: white;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--orange);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .faq-title {
                font-size: 2.5rem;
            }
            
            .faq-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                flex-direction: column;
                align-items: center;
            }
            
            .contact-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="faq-hero">
            <h1 class="faq-title">
                <i class="fas fa-question-circle"></i>
                Questions Fréquentes
            </h1>
            <p class="faq-subtitle">
                Trouvez rapidement les réponses à toutes vos questions sur ProDevis360°. 
                Notre équipe d'experts a compilé les questions les plus courantes pour vous aider.
            </p>
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Rechercher dans la FAQ..." id="searchFaq">
                <button class="search-btn" onclick="searchFAQ()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Navigation -->
        <div class="nav-faq">
            <div class="nav-links">
                <a href="#" class="nav-link active" onclick="filterFAQ('all')">
                    <i class="fas fa-th-large"></i> Toutes
                </a>
                <a href="#" class="nav-link" onclick="filterFAQ('general')">
                    <i class="fas fa-info-circle"></i> Général
                </a>
                <a href="#" class="nav-link" onclick="filterFAQ('technique')">
                    <i class="fas fa-cogs"></i> Technique
                </a>
                <a href="#" class="nav-link" onclick="filterFAQ('facturation')">
                    <i class="fas fa-credit-card"></i> Facturation
                </a>
                <a href="#" class="nav-link" onclick="filterFAQ('support')">
                    <i class="fas fa-headset"></i> Support
                </a>
            </div>
        </div>

        <!-- FAQ Grid -->
        <div class="faq-grid">
            <!-- Card 1: Questions Générales -->
            <div class="faq-card" data-category="general">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Questions Générales</h3>
                    <span class="category-badge">Essentiel</span>
                </div>
                <div class="card-body">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Qu'est-ce que ProDevis360° exactement ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>ProDevis360° est une plateforme intelligente spécialement conçue pour les professionnels du bâtiment en Côte d'Ivoire. Elle vous permet de créer des devis précis et professionnels en quelques minutes grâce à :</p>
                            <ul>
                                <li>9 modules spécialisés (plomberie, électricité, menuiserie, etc.)</li>
                                <li>Une base de données de prix actualisée</li>
                                <li>Des calculs automatiques de totaux, TVA et marges</li>
                                <li>Une génération PDF professionnelle</li>
                                <li>Une interface mobile-friendly</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Qui peut utiliser ProDevis360° ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>ProDevis360° s'adresse à tous les professionnels du secteur du bâtiment :</p>
                            <ul>
                                <li>Entrepreneurs en bâtiment</li>
                                <li>Artisans spécialisés (plombiers, électriciens, menuisiers, etc.)</li>
                                <li>Bureaux d'études</li>
                                <li>Architectes</li>
                                <li>Promoteurs immobiliers</li>
                                <li>Particuliers réalisant des travaux importants</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Pourquoi choisir ProDevis360° plutôt qu'Excel ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Contrairement à Excel, ProDevis360° offre :</p>
                            <ul>
                                <li><strong>Spécialisation :</strong> Conçu spécifiquement pour le bâtiment</li>
                                <li><strong>Base de données :</strong> Prix actualisés automatiquement</li>
                                <li><strong>Calculs intelligents :</strong> Pertes, chutes, coefficients automatiques</li>
                                <li><strong>Design professionnel :</strong> PDF avec votre identité visuelle</li>
                                <li><strong>Mobilité :</strong> Accessible partout, même sur chantier</li>
                                <li><strong>Conformité :</strong> Respect des normes ivoiriennes</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Les prix sont-ils adaptés au marché ivoirien ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Absolument ! Notre équipe met à jour régulièrement la base de données avec :</p>
                            <ul>
                                <li>Les prix du marché d'Abidjan et des principales villes</li>
                                <li>Les tarifs des fournisseurs locaux</li>
                                <li>Les coûts de transport selon les zones</li>
                                <li>Les taux de TVA et taxes en vigueur</li>
                                <li>Les matériaux disponibles localement</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Puis-je personnaliser mes devis ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Oui, ProDevis360° offre une personnalisation complète :</p>
                            <ul>
                                <li>Logo et couleurs de votre entreprise</li>
                                <li>Informations de contact personnalisées</li>
                                <li>Conditions générales sur mesure</li>
                                <li>Ajout de mentions spécifiques</li>
                                <li>Modification des taux de marge</li>
                                <li>Création de vos propres articles</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Questions Techniques -->
            <div class="faq-card" data-category="technique">
                <div class="card-header">
                    <h3><i class="fas fa-cogs"></i> Questions Techniques</h3>
                    <span class="category-badge">Technique</span>
                </div>
                <div class="card-body">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Comment les calculs sont-ils effectués ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>ProDevis360° utilise des algorithmes avancés qui prennent en compte :</p>
                            <ul>
                                <li><strong>Quantités :</strong> Calculs précis selon les unités métier</li>
                                <li><strong>Pertes :</strong> Coefficients de chute par matériau</li>
                                <li><strong>Transport :</strong> Coûts selon distance et volume</li>
                                <li><strong>Main d'œuvre :</strong> Temps unitaires par tâche</li>
                                <li><strong>TVA :</strong> Application automatique des taux</li>
                                <li><strong>Marges :</strong> Calculs selon vos objectifs</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Puis-je modifier les prix par défaut ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Oui, vous avez un contrôle total sur les prix :</p>
                            <ul>
                                <li>Modification ponctuelle pour un devis</li>
                                <li>Création de votre propre base de prix</li>
                                <li>Import de vos tarifs fournisseurs</li>
                                <li>Sauvegarde de vos prix préférés</li>
                                <li>Gestion des remises négociées</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Les devis sont-ils compatibles avec les logiciels comptables ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>ProDevis360° propose plusieurs options d'export :</p>
                            <ul>
                                <li>Export PDF haute qualité</li>
                                <li>Export Excel pour retraitement</li>
                                <li>Format CSV pour import comptable</li>
                                <li>API pour intégration directe</li>
                                <li>Compatibilité avec les logiciels majeurs</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Comment fonctionne la sauvegarde des données ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Vos données sont sécurisées à 100% :</p>
                            <ul>
                                <li>Sauvegarde automatique en temps réel</li>
                                <li>Serveurs sécurisés avec chiffrement SSL</li>
                                <li>Sauvegardes multiples et géolocalisées</li>
                                <li>Historique complet de vos modifications</li>
                                <li>Récupération possible à tout moment</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Puis-je travailler hors connexion ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>ProDevis360° offre des fonctionnalités hors-ligne :</p>
                            <ul>
                                <li>Cache local des projets récents</li>
                                <li>Modification possible sans connexion</li>
                                <li>Synchronisation automatique au retour</li>
                                <li>Mode déconnecté optimisé</li>
                                <li>Sauvegarde locale de sécurité</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Facturation et Tarifs -->
            <div class="faq-card" data-category="facturation">
                <div class="card-header">
                    <h3><i class="fas fa-credit-card"></i> Facturation & Tarifs</h3>
                    <span class="category-badge">Commercial</span>
                </div>
                <div class="card-body">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Combien coûte ProDevis360° ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Nous proposons plusieurs formules adaptées à vos besoins :</p>
                            <ul>
                                <li><strong>Starter :</strong> 15 000 FCFA/mois - Idéal pour débuter</li>
                                <li><strong>Pro :</strong> 35 000 FCFA/mois - Pour les professionnels</li>
                                <li><strong>Enterprise :</strong> 75 000 FCFA/mois - Équipes et grandes entreprises</li>
                                <li><strong>Essai gratuit :</strong> 14 jours sans engagement</li>
                                <li><strong>Réductions :</strong> -20% sur l'abonnement annuel</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Y a-t-il des frais cachés ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Non, notre politique tarifaire est transparente :</p>
                            <ul>
                                <li>Aucun frais d'installation</li>
                                <li>Aucun frais de paramétrage</li>
                                <li>Mises à jour incluses</li>
                                <li>Support technique inclus</li>
                                <li>Stockage illimité inclus</li>
                                <li>Pas de frais par utilisateur supplémentaire (formule Pro+)</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Comment se déroule l'essai gratuit ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>L'essai gratuit vous donne accès à toutes les fonctionnalités :</p>
                            <ul>
                                <li>14 jours d'accès complet</li>
                                <li>Tous les modules disponibles</li>
                                <li>Support technique inclus</li>
                                <li>Formation personnalisée</li>
                                <li>Aucune carte bancaire requise</li>
                                <li>Annulation possible à tout moment</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Quels modes de paiement acceptez-vous ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Nous acceptons tous les modes de paiement locaux :</p>
                            <ul>
                                <li>Mobile Money (Orange Money, MTN Money, Moov Money)</li>
                                <li>Virements bancaires</li>
                                <li>Cartes bancaires (Visa, Mastercard)</li>
                                <li>Chèques d'entreprise</li>
                                <li>Paiement comptant en agence</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Puis-je changer de formule en cours d'abonnement ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Oui, vous pouvez modifier votre abonnement à tout moment :</p>
                            <ul>
                                <li>Upgrade immédiat vers une formule supérieure</li>
                                <li>Downgrade en fin de période de facturation</li>
                                <li>Calcul au prorata pour les upgrades</li>
                                <li>Aucune pénalité de changement</li>
                                <li>Conservation de toutes vos données</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Support et Formation -->
            <div class="faq-card" data-category="support">
                <div class="card-header">
                    <h3><i class="fas fa-headset"></i> Support & Formation</h3>
                    <span class="category-badge">Assistance</span>
                </div>
                <div class="card-body">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Quel support est disponible ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Notre équipe support est à votre disposition via plusieurs canaux :</p>
                            <ul>
                                <li><strong>Chat en direct :</strong> Disponible 24h/24, 7j/7</li>
                                <li><strong>Téléphone :</strong> +225 07 07 77 77 77 (8h-18h)</li>
                                <li><strong>Email :</strong> support@prodevis360.com</li>
                                <li><strong>WhatsApp :</strong> +225 05 05 55 55 55</li>
                                <li><strong>Centre d'aide :</strong> Documentation complète en ligne</li>
                                <li><strong>Vidéos tutoriels :</strong> Formation vidéo pas-à-pas</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Proposez-vous des formations ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Oui, nous proposons plusieurs types de formations :</p>
                            <ul>
                                <li><strong>Formation initiale :</strong> 2h de formation personnalisée incluse</li>
                                <li><strong>Webinaires :</strong> Sessions collectives hebdomadaires</li>
                                <li><strong>Formation sur site :</strong> Déplacement dans vos locaux</li>
                                <li><strong>Tutoriels vidéo :</strong> Bibliothèque de plus de 50 vidéos</li>
                                <li><strong>Documentation :</strong> Guides utilisateur détaillés</li>
                                <li><strong>Formation avancée :</strong> Modules spécialisés par métier</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Combien de temps faut-il pour maîtriser l'outil ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>ProDevis360° est conçu pour être intuitif :</p>
                            <ul>
                                <li><strong>Premier devis :</strong> Possible en 15 minutes</li>
                                <li><strong>Maîtrise de base :</strong> 2-3 heures de pratique</li>
                                <li><strong>Fonctionnalités avancées :</strong> 1-2 semaines d'utilisation</li>
                                <li><strong>Expert :</strong> 1 mois d'utilisation régulière</li>
                                <li>Formation initiale recommandée : 2 heures</li>
                                <li>Support disponible pendant toute la phase d'apprentissage</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Comment signaler un bug ou une amélioration ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Nous encourageons vos retours pour améliorer la plateforme :</p>
                            <ul>
                                <li><strong>Bouton feedback :</strong> Directement dans l'interface</li>
                                <li><strong>Email technique :</strong> bugs@prodevis360.com</li>
                                <li><strong>Chat support :</strong> Signalement immédiat</li>
                                <li><strong>Forum utilisateurs :</strong> Échanges communautaires</li>
                                <li><strong>Programme bêta :</strong> Testez les nouveautés en avant-première</li>
                                <li>Réponse garantie sous 24h pour les bugs</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>À quelle fréquence sortent les mises à jour ?</span>
                            <i class="fas fa-chevron-down faq-toggle"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Nous publions régulièrement des améliorations :</p>
                            <ul>
                                <li><strong>Mises à jour mineures :</strong> Chaque semaine</li>
                                <li><strong>Nouvelles fonctionnalités :</strong> Chaque mois</li>
                                <li><strong>Mises à jour prix :</strong> Chaque trimestre</li>
                                <li><strong>Versions majeures :</strong> 2-3 fois par an</li>
                                <li>Corrections de bugs en temps réel</li>
                                <li>Toutes les mises à jour sont automatiques et gratuites</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Contact -->
        <div class="contact-section">
            <h2 class="contact-title">
                <i class="fas fa-comments"></i>
                Une question qui n'est pas dans la FAQ ?
            </h2>
            <p class="contact-subtitle">
                Notre équipe d'experts est là pour vous aider ! Contactez-nous par le canal de votre choix.
            </p>
            <div class="contact-buttons">
                <a href="mailto:support@prodevis360.com" class="contact-btn contact-btn-primary">
                    <i class="fas fa-envelope"></i>
                    Envoyer un Email
                </a>
                <a href="tel:+22507077777" class="contact-btn contact-btn-success">
                    <i class="fas fa-phone"></i>
                    Appeler Maintenant
                </a>
                <a href="https://wa.me/22505055555" class="contact-btn contact-btn-success" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp
                </a>
            </div>
        </div>

        <!-- Footer Navigation -->
        <div class="footer-nav">
            <h4><i class="fas fa-sitemap"></i> Navigation Rapide</h4>
            <div class="footer-links">
                <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
                <a href="liste_projets.php"><i class="fas fa-project-diagram"></i> Mes Projets</a>
                <a href="guide.php"><i class="fas fa-book"></i> Guide d'Utilisation</a>
                <a href="demo.php"><i class="fas fa-play-circle"></i> Démonstration</a>
                <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
                <a href="a-propos.php"><i class="fas fa-info-circle"></i> À Propos</a>
            </div>
        </div>
    </div>

    <script>
        // ===== SCRIPTS INTERACTIFS FAQ =====
        
        // Toggle FAQ Items
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const isActive = element.classList.contains('active');
            
            // Fermer tous les autres éléments de la même card
            const card = element.closest('.faq-card');
            const allQuestions = card.querySelectorAll('.faq-question');
            const allAnswers = card.querySelectorAll('.faq-answer');
            
            allQuestions.forEach(q => q.classList.remove('active'));
            allAnswers.forEach(a => a.classList.remove('active'));
            
            // Ouvrir l'élément cliqué s'il n'était pas actif
            if (!isActive) {
                element.classList.add('active');
                answer.classList.add('active');
            }
        }

        // Recherche dans la FAQ
        function searchFAQ() {
            const searchTerm = document.getElementById('searchFaq').value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question span').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    // Highlight le terme recherché
                    if (searchTerm.length > 2) {
                        highlightSearchTerm(item, searchTerm);
                    }
                } else {
                    item.style.display = searchTerm ? 'none' : 'block';
                }
            });

            // Masquer les cards vides
            const cards = document.querySelectorAll('.faq-card');
            cards.forEach(card => {
                const visibleItems = card.querySelectorAll('.faq-item[style*="block"], .faq-item:not([style*="none"])');
                card.style.display = visibleItems.length === 0 && searchTerm ? 'none' : 'block';
            });
        }

        // Highlight des termes de recherche
        function highlightSearchTerm(item, term) {
            const question = item.querySelector('.faq-question span');
            const text = question.textContent;
            const highlightedText = text.replace(
                new RegExp(term, 'gi'), 
                `<mark style="background: #ffeb3b; padding: 2px 4px; border-radius: 3px;">                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Quel support est disponible ?</span>
                            <i class="fas fa-chevron-down f</mark>`
            );
            question.innerHTML = highlightedText;
        }

        // Recherche en temps réel
        document.getElementById('searchFaq').addEventListener('input', function() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                searchFAQ();
            }, 300);
        });

        // Filtrage par catégorie
        function filterFAQ(category) {
            const cards = document.querySelectorAll('.faq-card');
            const navLinks = document.querySelectorAll('.nav-link');
            
            // Mise à jour navigation
            navLinks.forEach(link => link.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filtrage des cards
            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.6s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });

            // Reset de la recherche
            document.getElementById('searchFaq').value = '';
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                item.style.display = 'block';
                const question = item.querySelector('.faq-question span');
                question.innerHTML = question.textContent; // Remove highlights
            });
        }

        // Animation d'entrée des cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.faq-card');
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
                        }
                    });
                },
                { threshold: 0.1 }
            );

            cards.forEach(card => observer.observe(card));

            // Ouvrir automatiquement la première question de chaque card
            const firstQuestions = document.querySelectorAll('.faq-card .faq-item:first-child .faq-question');
            firstQuestions.forEach(question => {
                setTimeout(() => {
                    question.click();
                }, 1000);
            });
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.getElementById('searchFaq').focus();
            }
        });

        // Analytics des questions consultées
        function trackFAQInteraction(question) {
            console.log('FAQ viewed:', question);
            // Ici vous pouvez ajouter votre code d'analytics
        }

        // Ajout du tracking aux questions
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', function() {
                const questionText = this.querySelector('span').textContent;
                trackFAQInteraction(questionText);
            });
        });

        // Smooth scroll pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Notification de mise à jour de la FAQ
        function checkFAQUpdates() {
            // Simulation de vérification des mises à jour
            const lastUpdate = localStorage.getItem('faq_last_update');
            const currentVersion = '2024.01.23';
            
            if (lastUpdate !== currentVersion) {
                setTimeout(() => {
                    const notification = document.createElement('div');
                    notification.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: linear-gradient(135deg, #27ae60, #2ecc71);
                        color: white;
                        padding: 15px 20px;
                        border-radius: 10px;
                        box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
                        z-index: 10000;
                        animation: slideInRight 0.5s ease;
                        max-width: 300px;
                    `;
                    notification.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>FAQ mise à jour !</strong>
                                <p style="margin: 5px 0 0; font-size: 0.9rem;">Nouvelles réponses ajoutées</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: white; font-size: 18px; cursor: pointer;">×</button>
                        </div>
                    `;
                    
                    document.body.appendChild(notification);
                    localStorage.setItem('faq_last_update', currentVersion);
                    
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 5000);
                }, 2000);
            }
        }

        // Lancer la vérification des mises à jour
        checkFAQUpdates();

        // Ajout de styles CSS additionnels via JavaScript
        const additionalStyles = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            
            mark {
                animation: highlightPulse 1s ease-in-out;
            }
            
            @keyframes highlightPulse {
                0% { background-color: #ffeb3b; }
                50% { background-color: #ffc107; }
                100% { background-color: #ffeb3b; }
            }
        `;

        const styleSheet = document.createElement('style');
        styleSheet.textContent = additionalStyles;
        document.head.appendChild(styleSheet);
    </script>
</body>
</html>

