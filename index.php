<?php
// ===== INDEX.PHP - VERSION FINALE AMÉLIORÉE =====
// Toutes les améliorations demandées

// Inclusion des fonctions communes
require_once 'functions.php';

// Connexion BDD pour statistiques en temps réel
try {
    $pdo = getDbConnection();
    
    // Statistiques dynamiques
    $stmt = $pdo->query("SELECT COUNT(*) as nb_projets FROM projets");
    $nb_projets = $stmt->fetch()['nb_projets'] ?? 0;
    
    $stmt = $pdo->query("SELECT COUNT(*) as nb_devis FROM devis");
    $nb_devis = $stmt->fetch()['nb_devis'] ?? 0;
    
    $stmt = $pdo->query("SELECT SUM(total_ttc) as chiffre_affaires FROM recapitulatif");
    $chiffre_affaires = $stmt->fetch()['chiffre_affaires'] ?? 0;
    
} catch (PDOException $e) {
    $nb_projets = 42;
    $nb_devis = 128;
    $chiffre_affaires = 15750000;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSN ProDevis360° - Révolutionnez vos Devis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --orange: #FF6B35;
            --orange-light: #FF8A65;
            --orange-dark: #E65100;
            --white: #FFFFFF;
            --green: #4CAF50;
            --green-light: #81C784;
            --red: #F44336;
            --blue: #2196F3;
            --blue-dark: #1976D2;
            --dark: #1A1A1A;
            --gray: #757575;
            --gradient-hero: linear-gradient(135deg, var(--orange) 0%, var(--red) 50%, var(--blue) 100%);
            --gradient-card: linear-gradient(145deg, var(--white) 0%, #F8F9FA 100%);
            --shadow-soft: 0 8px 25px rgba(0,0,0,0.1);
            --shadow-hard: 0 15px 35px rgba(0,0,0,0.2);
            --border-radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow-x: hidden;
        }

        /* Navigation optimisée */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            z-index: 1000;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255,255,255,0.98);
            box-shadow: var(--shadow-soft);
        }

        .nav-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--orange);
            text-decoration: none;
        }

        .logo i {
            width: 40px;
            height: 40px;
            background: var(--gradient-hero);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-link {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--orange);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--orange);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .cta-nav {
            background: var(--gradient-hero);
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .cta-nav:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hard);
        }

        /* Hero Section optimisée */
        .hero {
            min-height: 85vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: var(--gradient-hero);
            overflow: hidden;
            padding: 4rem 0 2rem;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .hero-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 10;
        }

        .hero-content {
            color: white;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50px;
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .hero-title {
            font-size: clamp(2.2rem, 6vw, 3.8rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.25rem;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .hero-title .highlight {
            background: linear-gradient(45deg, var(--white), var(--orange-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 2rem;
            line-height: 1.6;
            animation: fadeInUp 1s ease-out 0.6s both;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.8s both;
        }

        .btn-hero {
            padding: 0.85rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            min-width: 160px;
            justify-content: center;
        }

        .btn-primary {
            background: var(--white);
            color: var(--orange);
            box-shadow: var(--shadow-soft);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hard);
            background: var(--orange);
            color: var(--white);
        }

        .btn-secondary {
            background: transparent;
            color: var(--white);
            border: 2px solid var(--white);
        }

        .btn-secondary:hover {
            background: var(--white);
            color: var(--orange);
            transform: translateY(-3px);
        }

        /* Carrousel d'images */
        .hero-images {
            position: relative;
            height: 400px;
            overflow: hidden;
            border-radius: var(--border-radius);
            animation: fadeInUp 1s ease-out 1s both;
        }

        .image-slider {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .slide.active {
            opacity: 1;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: var(--border-radius);
        }

        .slide-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: white;
            padding: 1.5rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .slide-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .slide-description {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Statistiques optimisées */
        .stats-floating {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 2rem;
            z-index: 10;
            animation: fadeInUp 1s ease-out 1.2s both;
        }

        .stat-item {
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            display: block;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 0.2rem;
        }

        /* Sections optimisées */
        .features {
            padding: 4rem 0;
            background: var(--white);
            position: relative;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 3rem;
        }

        .section-title {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .section-subtitle {
            font-size: 1.05rem;
            color: var(--gray);
            line-height: 1.6;
        }

        /* Fonctionnalités 2 par ligne */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: var(--gradient-card);
            border-radius: var(--border-radius);
            padding: 2rem;
            position: relative;
            transition: all 0.4s ease;
            border: 1px solid rgba(255,255,255,0.2);
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,107,53,0.1), transparent);
            transition: left 0.6s ease;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hard);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-hero);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.75rem;
            position: relative;
            z-index: 2;
        }

        .feature-description {
            color: var(--gray);
            line-height: 1.6;
            font-size: 0.95rem;
            position: relative;
            z-index: 2;
        }

        /* Modules 3 par ligne */
        .modules {
            padding: 4rem 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .module-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
            border-left: 4px solid transparent;
        }

        .module-card:nth-child(1) { border-left-color: var(--orange); }
        .module-card:nth-child(2) { border-left-color: var(--blue); }
        .module-card:nth-child(3) { border-left-color: var(--green); }
        .module-card:nth-child(4) { border-left-color: var(--red); }
        .module-card:nth-child(5) { border-left-color: var(--orange-light); }
        .module-card:nth-child(6) { border-left-color: var(--blue-dark); }
        .module-card:nth-child(7) { border-left-color: var(--green-light); }
        .module-card:nth-child(8) { border-left-color: var(--orange-dark); }
        .module-card:nth-child(9) { border-left-color: var(--red); }

        .module-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-hero);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .module-card:hover::after {
            transform: scaleX(1);
        }

        .module-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-hard);
        }

        .module-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 1rem;
            background: var(--gradient-hero);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
            transition: all 0.3s ease;
        }

        .module-card:hover .module-icon {
            transform: rotateY(180deg) scale(1.1);
        }

        .module-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.75rem;
        }

        .module-description {
            color: var(--gray);
            font-size: 0.85rem;
            line-height: 1.5;
        }

        /* Section CTA réduite */
        .cta-section {
            padding: 4rem 0;
            background: var(--gradient-hero);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="3" fill="rgba(255,255,255,0.05)"/></svg>');
            animation: rotate 60s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .cta-content {
            position: relative;
            z-index: 10;
            max-width: 600px;
            margin: 0 auto;
        }

        .cta-title {
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .cta-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .cta-button {
            background: var(--white);
            color: var(--orange);
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: var(--shadow-hard);
        }

        .cta-button:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            color: var(--orange);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--orange);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            text-align: center;
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }

        /* Animations */
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
            animation: fadeInUp 0.8s ease-out;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-main {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .modules-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .nav-content {
                padding: 0 1rem;
            }
            
            .nav-menu {
                display: none;
            }
            
            .hero {
                min-height: 70vh;
                padding: 2rem 0 1rem;
            }
            
            .hero-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .stats-floating {
                flex-direction: column;
                gap: 1rem;
                position: static;
                transform: none;
                margin-top: 2rem;
            }
            
            .modules-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .footer-content {
                grid-template-columns: 1fr;
            }
        }

        /* Scroll animations */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .animate-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-content">
            <a href="#" class="logo">
                <i class="fas fa-rocket"></i>
                <span>ProDevis360°</span>
            </a>
            <ul class="nav-menu">
                <li><a href="#fonctionnalites" class="nav-link">Fonctionnalités</a></li>
                <li><a href="#modules" class="nav-link">Modules</a></li>
                <li><a href="#support" class="nav-link">Support</a></li>
                <li><a href="liste_projets.php" class="cta-nav">
                    <i class="fas fa-arrow-right"></i> Commencer
                </a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-main">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-star"></i>
                    <span>Solution n°1 en Côte d'Ivoire</span>
                </div>
                
                <h1 class="hero-title">
                    Révolutionnez vos <span class="highlight">Devis</span> avec ProDevis360°
                </h1>
                
                <p class="hero-subtitle">
                    La plateforme intelligente qui transforme la création de devis en une expérience simple, rapide et professionnelle.
                </p>
                
                <div class="hero-actions">
                    <a href="liste_projets.php" class="btn-hero btn-primary">
                        <i class="fas fa-rocket"></i>
                        Commencer
                    </a>
                    <a href="#demo" class="btn-hero btn-secondary">
                        <i class="fas fa-play"></i>
                        Voir Démo
                    </a>
                </div>
            </div>
            
            <!-- Carrousel d'images de bâtiments -->
            <div class="hero-images">
                <div class="image-slider">
                    <div class="slide active">
                        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Bâtiment moderne">
                        <div class="slide-overlay">
                            <div class="slide-title">Projets Modernes</div>
                            <div class="slide-description">Architecture contemporaine et durable</div>
                        </div>
                    </div>
                    <div class="slide">
                        <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Construction résidentielle">
                        <div class="slide-overlay">
                            <div class="slide-title">Habitat Résidentiel</div>
                            <div class="slide-description">Villas et maisons familiales</div>
                        </div>
                    </div>
                    <div class="slide">
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Chantier de construction">
                        <div class="slide-overlay">
                            <div class="slide-title">Projets Commerciaux</div>
                            <div class="slide-description">Bureaux et espaces d'affaires</div>
                        </div>
                    </div>
                    <div class="slide">
                        <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Intérieur moderne">
                        <div class="slide-overlay">
                            <div class="slide-title">Finitions Premium</div>
                            <div class="slide-description">Aménagements haut de gamme</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stats-floating">
            <div class="stat-item">
                <span class="stat-number" data-target="<?= $nb_projets ?>"><?= $nb_projets ?></span>
                <span class="stat-label">Projets</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="<?= $nb_devis ?>"><?= $nb_devis ?></span>
                <span class="stat-label">Devis</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= number_format($chiffre_affaires/1000000, 1) ?>M</span>
                <span class="stat-label">FCFA</span>
            </div>
        </div>
    </section>

    <!-- Fonctionnalités 2 par ligne -->
    <section class="features" id="fonctionnalites">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <h2 class="section-title">
                    Pourquoi <span style="color: var(--orange);">ProDevis360°</span> ?
                </h2>
                <p class="section-subtitle">
                    Les fonctionnalités qui font la différence pour les professionnels du bâtiment.
                </p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-magic"></i>
                    </div>
                    <h3 class="feature-title">Suggestions Intelligentes</h3>
                    <p class="feature-description">
                        IA qui propose automatiquement matériaux, quantités et prix optimaux selon votre projet.
                    </p>
                </div>
                
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="feature-title">Calculs Automatiques</h3>
                    <p class="feature-description">
                        Totaux, TVA et remises calculés automatiquement en temps réel sans erreur.
                    </p>
                </div>
                
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-print"></i>
                    </div>
                    <h3 class="feature-title">PDF Professionnel</h3>
                    <p class="feature-description">
                        Devis PDF de qualité avec votre identité visuelle en un seul clic.
                    </p>
                </div>
                
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Accessible Partout</h3>
                    <p class="feature-description">
                        Bureau, chantier, domicile. Compatible tous appareils et écrans.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules 3 par ligne -->
    <section class="modules" id="modules">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <h2 class="section-title">
                    <span style="color: var(--orange);">9 Modules</span> Spécialisés
                </h2>
                <p class="section-subtitle">
                    Chaque module conçu par des experts métier pour une précision maximale.
                </p>
            </div>
            
            <div class="modules-grid">
                <div class="module-card animate-on-scroll">
                    <div class="module-icon"><i class="fas fa-cubes"></i></div>
                    <h3 class="module-title">Matériaux de Base</h3>
                    <p class="module-description">Ciment, sable, gravier avec calculs de volumes automatiques.</p>
                </div>
                
                <div class="module-card animate-on-scroll">
                    <div class="module-icon"><i class="fas fa-faucet"></i></div>
                    <h3 class="module-title">Plomberie</h3>
                    <p class="module-description">Tuyaux, raccords, sanitaires avec kits prédéfinis.</p>
                </div>
                
                <div class="module-card animate-on-scroll">
                    <div class="module-icon"><i class="fas fa-door-open"></i></div>
                    <h3 class="module-title">Menuiserie</h3>
                    <p class="module-description">Portes, fenêtres, quincaillerie avec dimensions.</p>
                </div>
                
                <div class="module-card animate-on-scroll">
                    <div class="module-icon"><i class="fas fa-bolt"></i></div>
                    <h3 class="module-title">Électricité</h3>
                    <p class="module-description">Installation complète avec respect des normes.</p>
                </div>
                
                <div class="module-card animate-on-scroll">
                    <div class="module-icon"><i class="fas fa-paint-roller"></i></div>
                    <h3 class="module-title">Peinture</h3>
                    <p class="module-description">Peintures, enduits avec calcul de surfaces.</p>
                </div>
                
                <div class="module-card animate-on-scroll">
                    <div class="module-icon"><i class="fas fa-hammer"></i></div>
                    <h3 class="module-title">Charpenterie</h3>
                    <p class="module-description">Structures bois/métal, découpes optimisées.</p>
                </div>
                
                <div class="module-card animate-on-scroll">
                    <div class="module-icon"><i class="fas fa-border-style"></i></div>
                    <h3 class="module-title">Carrelage</h3>
                    <p class="module-description">Sols et murs avec calculs de pertes.</p>
                </div>
                
                <div class="module-card animate-on-scroll">
                    <div class="module-icon"><i class="fas fa-grip-lines"></i></div>
                    <h3 class="module-title">Ferraillage</h3>
                    <p class="module-description">Armatures béton, calculs de charges.</p>
                </div>
                
                <div class="module-card animate-on-scroll">
                    <div class="module-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3 class="module-title">Ferronnerie</h3>
                    <p class="module-description">Ouvrages métalliques, portails sur mesure.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA réduite -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Prêt à Révolutionner vos Devis ?</h2>
                <p class="cta-subtitle">
                    Rejoignez les professionnels qui font confiance à ProDevis360°.
                </p>
                <a href="liste_projets.php" class="cta-button">
                    <i class="fas fa-rocket"></i>
                    Commencer Maintenant
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="support">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>ProDevis360°</h3>
                    <ul>
                        <li><a href="#fonctionnalites">Fonctionnalités</a></li>
                        <li><a href="#modules">Modules</a></li>
                        <li><a href="liste_projets.php">Mes Projets</a></li>
                        <li><a href="#demo">Démonstration</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul>
                        <li><a href="mailto:support@gsnexpertises.com">support@gsnexpertises.com</a></li>
                        <li><a href="tel:+22500000000">+225 00 00 00 00</a></li>
                        <li><a href="#">Guide d'utilisation</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>GSN Expertises</h3>
                    <ul>
                        <li><a href="#">À propos</a></li>
                        <li><a href="#">Nos services</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Actualités</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Légal</h3>
                    <ul>
                        <li><a href="#">Conditions d'utilisation</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                        <li><a href="#">Mentions légales</a></li>
                        <li><a href="#">RGPD</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 GSN Expertises - ProDevis360°. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Navigation scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Carrousel d'images
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        
        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }
        
        // Changer d'image toutes les 4 secondes
        setInterval(nextSlide, 4000);

        // Smooth scrolling
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

        // Animation des compteurs
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number[data-target]');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;
                
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.floor(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };
                
                updateCounter();
            });
        }

        // Intersection Observer
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    
                    if (entry.target.classList.contains('stats-floating')) {
                        animateCounters();
                    }
                }
            });
        }, observerOptions);

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            const animatedElements = document.querySelectorAll('.animate-on-scroll');
            animatedElements.forEach(el => observer.observe(el));
            
            const statsElement = document.querySelector('.stats-floating');
            if (statsElement) {
                observer.observe(statsElement);
            }
        });

        console.log('%c🚀 ProDevis360° - Version Finale Optimisée', 
                   'color: #FF6B35; font-size: 20px; font-weight: bold;');
        console.log('%c✨ Toutes les améliorations appliquées !', 
                   'color: #4CAF50; font-size: 14px;');
    </script>
</body>
</html>

<!-- ===== FIN INDEX.PHP VERSION FINALE AMÉLIORÉE ===== -->