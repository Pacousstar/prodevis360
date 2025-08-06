-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 06 août 2025 à 14:41
-- Version du serveur : 10.11.10-MariaDB
-- Version de PHP : 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `u370633571_ProDevis360`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`u370633571_Pacous07`@`127.0.0.1` PROCEDURE `generer_numero_devis` (IN `p_projet_id` INT, OUT `p_numero` VARCHAR(20))   BEGIN
    DECLARE v_count INT;
    SELECT COUNT(*) + 1 INTO v_count FROM devis WHERE projet_id = p_projet_id;
    SET p_numero = CONCAT('DEV-', LPAD(p_projet_id, 3, '0'), '-', LPAD(v_count, 3, '0'));
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `archives_reports`
--

CREATE TABLE `archives_reports` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) DEFAULT NULL,
  `devis_id` int(11) DEFAULT NULL,
  `date_rapport` varchar(50) NOT NULL,
  `total_ttc` decimal(15,2) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `carrelage`
--

CREATE TABLE `carrelage` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `pu` int(11) DEFAULT NULL,
  `pt` int(11) DEFAULT NULL,
  `transport` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `carrelage`
--

INSERT INTO `carrelage` (`id`, `projet_id`, `devis_id`, `designation`, `quantite`, `pu`, `pt`, `transport`, `created_at`, `updated_at`) VALUES
(12, 16, 21, 'Carreau céramique', 32, 4000, 128000, 2000, '2025-05-22 10:40:46', '2025-05-22 10:40:46'),
(13, 16, 21, 'Joint carrelage', 5, 5000, 25000, 1000, '2025-05-22 10:42:00', '2025-05-22 10:42:00'),
(14, 18, 22, 'Carreau céramique', 98, 4000, 392000, 3000, '2025-05-22 17:50:13', '2025-05-22 17:50:13'),
(15, 18, 22, 'Joint carrelage', 13, 5000, 65000, 1000, '2025-05-22 17:51:00', '2025-05-22 17:51:00');

-- --------------------------------------------------------

--
-- Structure de la table `charpenterie`
--

CREATE TABLE `charpenterie` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `pu` int(11) DEFAULT NULL,
  `pt` int(11) DEFAULT NULL,
  `transport` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `charpenterie`
--

INSERT INTO `charpenterie` (`id`, `projet_id`, `devis_id`, `designation`, `quantite`, `pu`, `pt`, `transport`, `created_at`, `updated_at`) VALUES
(15, 16, 21, 'Chevron 6/4', 150, 3500, 525000, 3000, '2025-05-22 10:23:44', '2025-05-22 10:23:44'),
(16, 16, 21, 'Contreplaqué 2,5', 30, 3000, 90000, 1000, '2025-05-22 10:24:40', '2025-05-22 10:24:40'),
(17, 16, 21, 'Pointe N 12', 8, 1000, 8000, 200, '2025-05-22 10:25:37', '2025-05-22 10:31:24'),
(18, 16, 21, 'Pointe N 8', 12, 1000, 12000, 200, '2025-05-22 10:26:18', '2025-05-22 10:30:54'),
(19, 16, 21, 'Pointe N 6', 3, 1000, 3000, 200, '2025-05-22 10:27:05', '2025-05-22 10:30:23'),
(20, 16, 21, 'Pointe N 4', 6, 1000, 6000, 200, '2025-05-22 10:27:46', '2025-05-22 10:30:02'),
(21, 16, 21, 'Pointe acier N 10', 3, 1500, 4500, 200, '2025-05-22 10:29:47', '2025-05-22 10:29:47'),
(22, 18, 22, 'Chevron 6/8', 130, 5000, 650000, 3000, '2025-05-27 14:42:15', '2025-05-27 14:42:15'),
(23, 18, 22, 'Chevron 6/4', 220, 3500, 770000, 3000, '2025-05-27 14:43:50', '2025-05-27 14:43:50'),
(24, 18, 22, 'Tôle ordinaire', 120, 4000, 480000, 3000, '2025-05-27 14:45:50', '2025-05-27 14:45:50'),
(25, 18, 22, 'Contreplaqué 2,5', 110, 3000, 330000, 3000, '2025-05-27 14:46:52', '2025-05-27 14:46:52'),
(26, 18, 22, 'Baguette de bois', 10, 2500, 25000, 1000, '2025-05-27 14:48:55', '2025-05-27 14:48:55'),
(27, 18, 22, 'Pointe N 12', 13, 1000, 13000, 200, '2025-05-27 14:49:39', '2025-05-27 14:49:39'),
(28, 18, 22, 'Pointe N 8', 13, 1000, 13000, 200, '2025-05-27 14:50:04', '2025-05-27 14:50:04'),
(29, 18, 22, 'Pointe N 6', 9, 1000, 9000, 200, '2025-05-27 14:50:54', '2025-05-27 14:50:54'),
(30, 18, 22, 'Pointe N 4', 14, 1000, 14000, 200, '2025-05-27 14:51:34', '2025-05-27 14:51:34'),
(31, 18, 22, 'Pointe acier N 10', 8, 1500, 12000, 200, '2025-05-27 14:52:23', '2025-05-27 14:52:23'),
(32, 18, 22, 'Fer d\'attache', 8, 5000, 40000, 500, '2025-05-27 14:53:30', '2025-05-27 14:53:30');

-- --------------------------------------------------------

--
-- Structure de la table `devis`
--

CREATE TABLE `devis` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `version` varchar(20) DEFAULT '1.0',
  `description` text DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `statut` enum('brouillon','validé','facturé','payé') DEFAULT 'brouillon'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `devis`
--

INSERT INTO `devis` (`id`, `projet_id`, `numero`, `version`, `description`, `date_creation`, `date_modification`, `statut`) VALUES
(29, 26, 'DEV-026-001', '1.0', 'ok', '2025-07-27 00:14:28', '2025-08-04 14:48:40', ''),
(30, 26, 'DEV-026-002', '1.0', 'ok', '2025-07-27 01:13:40', '2025-08-04 14:48:40', ''),
(31, 26, 'DEV-026-003', '1.0', 'ok', '2025-07-27 01:48:15', '2025-08-04 14:48:40', ''),
(35, 26, 'DEV-026-004', '1.0', '111', '2025-08-04 20:18:13', '2025-08-04 20:18:13', 'brouillon');

--
-- Déclencheurs `devis`
--
DELIMITER $$
CREATE TRIGGER `before_devis_insert` BEFORE INSERT ON `devis` FOR EACH ROW BEGIN
    IF NEW.numero IS NULL THEN
        CALL generer_numero_devis(NEW.projet_id, NEW.numero);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `electricite`
--

CREATE TABLE `electricite` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `pu` int(11) DEFAULT NULL,
  `pt` int(11) DEFAULT NULL,
  `transport` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `electricite`
--

INSERT INTO `electricite` (`id`, `projet_id`, `devis_id`, `designation`, `quantite`, `pu`, `pt`, `transport`, `created_at`, `updated_at`) VALUES
(20, 14, 19, 'rouleau de tuyau orange', 1, 20000, 20000, 500, '2025-05-19 17:09:54', '2025-05-19 17:09:54'),
(21, 14, 19, 'boîte étanche 240', 1, 1000, 1000, 500, '2025-05-19 17:14:09', '2025-05-19 17:14:09'),
(22, 14, 19, 'boîte étanche 180', 1, 1000, 1000, 500, '2025-05-19 17:14:46', '2025-05-19 17:14:46'),
(23, 14, 19, 'coffré de six module', 1, 10000, 10000, 500, '2025-05-19 17:16:31', '2025-05-19 17:18:31'),
(24, 14, 19, 'boîtier rond', 20, 100, 2000, 500, '2025-05-19 17:18:05', '2025-05-19 17:18:05'),
(25, 16, 21, 'tuyaux orange', 1, 25000, 25000, 500, '2025-05-22 09:02:25', '2025-05-22 09:02:25'),
(26, 16, 21, 'Câble électrique', 3, 25000, 75000, 500, '2025-05-22 09:42:11', '2025-05-22 09:42:11'),
(27, 16, 21, 'boîte étanche 240', 1, 4500, 4500, 200, '2025-05-22 09:43:40', '2025-05-22 09:43:40'),
(28, 16, 21, 'boîte étanche 180', 1, 4000, 4000, 200, '2025-05-22 09:44:10', '2025-05-22 09:44:10'),
(29, 16, 21, 'Coffré de six module', 1, 4000, 4000, 200, '2025-05-22 09:45:03', '2025-05-22 09:45:03'),
(30, 16, 21, 'Boîtier ronde', 200, 100, 20000, 200, '2025-05-22 09:46:06', '2025-05-22 09:46:06'),
(31, 16, 21, 'Interrupteur simple', 8, 1000, 8000, 200, '2025-05-22 09:49:07', '2025-05-22 09:56:24'),
(32, 16, 21, 'Sonnerie ', 1, 4000, 4000, 200, '2025-05-22 09:49:43', '2025-05-22 09:49:43'),
(33, 16, 21, 'Ampoule', 6, 1000, 6000, 200, '2025-05-22 09:50:24', '2025-05-22 09:50:24'),
(34, 16, 21, 'Barette de domino 25', 1, 2000, 2000, 200, '2025-05-22 09:53:13', '2025-05-22 09:53:13'),
(35, 16, 21, 'Barette de domino 40', 1, 4000, 4000, 200, '2025-05-22 09:53:49', '2025-05-22 09:53:49'),
(36, 16, 21, 'Douille ', 6, 1000, 6000, 200, '2025-05-22 09:54:35', '2025-05-22 09:54:35'),
(37, 16, 21, 'Prise électrique', 6, 1000, 6000, 200, '2025-05-22 09:55:49', '2025-05-22 09:55:49'),
(38, 18, 22, 'tuyau orange', 5, 25000, 125000, 1000, '2025-05-22 17:12:48', '2025-05-22 17:12:48'),
(39, 18, 22, 'Câble électrique', 13, 25000, 325000, 500, '2025-05-22 17:16:32', '2025-05-22 17:16:32'),
(40, 18, 22, 'Boîtier ronde', 38, 100, 3800, 200, '2025-05-22 17:19:42', '2025-05-22 17:19:42'),
(41, 18, 22, 'boîte étanche 240', 3, 4500, 13500, 200, '2025-05-22 17:25:43', '2025-05-22 17:25:43'),
(42, 18, 22, 'boîte étanche 180', 10, 4000, 40000, 200, '2025-05-22 17:26:24', '2025-05-22 17:26:24'),
(43, 18, 22, 'Coffré de six module', 3, 4000, 12000, 200, '2025-05-22 17:28:09', '2025-05-22 17:28:09'),
(44, 18, 22, 'Coffré de trois module', 4, 3000, 12000, 200, '2025-05-22 17:29:53', '2025-05-22 17:29:53'),
(45, 18, 22, 'DPN de six modules', 3, 10000, 30000, 200, '2025-05-22 17:31:37', '2025-05-22 17:31:37'),
(46, 16, 21, 'DPN de six modules', 1, 10000, 10000, 200, '2025-05-22 17:32:23', '2025-05-22 17:32:23'),
(47, 18, 22, 'DPN de trois modules', 4, 8000, 32000, 200, '2025-05-22 17:35:27', '2025-05-22 17:35:27'),
(48, 18, 22, 'Interrupteur ', 26, 1000, 26000, 200, '2025-05-22 17:36:24', '2025-05-22 17:36:24'),
(49, 18, 22, 'Sonnerie ', 3, 4000, 12000, 200, '2025-05-22 17:37:00', '2025-05-22 17:37:00'),
(50, 18, 22, 'Ampoule', 26, 1000, 26000, 200, '2025-05-22 17:38:22', '2025-05-22 17:38:22'),
(51, 18, 22, 'Barette de domino 40', 1, 4000, 4000, 200, '2025-05-22 17:42:04', '2025-05-22 17:44:22'),
(52, 18, 22, 'Barette de domino 25', 2, 2000, 4000, 200, '2025-05-22 17:42:27', '2025-05-22 17:44:52'),
(53, 18, 22, 'Douille ', 26, 500, 13000, 200, '2025-05-22 17:43:15', '2025-05-22 17:43:15'),
(54, 18, 22, 'Prise électrique', 19, 1000, 19000, 200, '2025-05-22 17:46:08', '2025-05-22 17:46:08');

-- --------------------------------------------------------

--
-- Structure de la table `ferraillage`
--

CREATE TABLE `ferraillage` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `pu` int(11) DEFAULT NULL,
  `pt` int(11) DEFAULT NULL,
  `transport` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ferraillage`
--

INSERT INTO `ferraillage` (`id`, `projet_id`, `devis_id`, `designation`, `quantite`, `pu`, `pt`, `transport`, `created_at`, `updated_at`) VALUES
(10, 16, 21, 'Fer AH 10', 24, 4500, 108000, 2000, '2025-05-22 10:16:38', '2025-05-22 10:21:28'),
(11, 16, 21, 'Fer AH 6', 3, 1500, 4500, 1000, '2025-05-22 10:19:59', '2025-05-22 10:19:59'),
(12, 18, 22, 'Fer AH 10', 3, 4500, 13500, 500, '2025-05-27 11:36:27', '2025-05-27 11:36:27'),
(13, 18, 22, 'Fer AH 6', 2, 1000, 2000, 500, '2025-05-27 11:37:20', '2025-05-27 11:37:20'),
(14, 18, 22, 'Fil de fer', 1, 3000, 3000, 200, '2025-05-27 11:38:02', '2025-05-27 11:38:02');

-- --------------------------------------------------------

--
-- Structure de la table `ferronnerie`
--

CREATE TABLE `ferronnerie` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `pu` int(11) DEFAULT NULL,
  `pt` int(11) DEFAULT NULL,
  `transport` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ferronnerie`
--

INSERT INTO `ferronnerie` (`id`, `projet_id`, `devis_id`, `designation`, `quantite`, `pu`, `pt`, `transport`, `created_at`, `updated_at`) VALUES
(10, 16, 21, 'Anti-vol', 1, 10000, 10000, 500, '2025-05-22 10:14:52', '2025-05-22 10:14:52'),
(11, 18, 22, 'Anti-vol', 4, 10000, 40000, 500, '2025-05-27 11:42:10', '2025-05-27 11:42:10'),
(12, 18, 22, 'Porte 110x210', 4, 75000, 300000, 1000, '2025-05-27 11:46:51', '2025-05-27 15:00:51'),
(13, 18, 22, 'Portail en fer 300x250', 1, 150000, 150000, 5000, '2025-05-27 15:02:58', '2025-05-27 15:02:58');

-- --------------------------------------------------------

--
-- Structure de la table `historique_devis`
--

CREATE TABLE `historique_devis` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `date_action` datetime NOT NULL,
  `utilisateur` varchar(100) DEFAULT 'Utilisateur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `materiaux_base`
--

CREATE TABLE `materiaux_base` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `quantite` decimal(10,2) NOT NULL,
  `pu` decimal(10,2) NOT NULL,
  `pt` decimal(10,2) NOT NULL,
  `transport` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `materiaux_base`
--

INSERT INTO `materiaux_base` (`id`, `projet_id`, `devis_id`, `designation`, `quantite`, `pu`, `pt`, `transport`, `created_at`, `updated_at`) VALUES
(15, 16, 21, 'Ciment', 30.00, 5000.00, 150000.00, 2000.00, '2025-05-22 10:07:54', '2025-05-22 10:07:54'),
(16, 16, 21, 'Sable', 1.00, 40000.00, 40000.00, 0.00, '2025-05-22 10:08:17', '2025-05-22 10:08:17'),
(17, 16, 21, 'Gravier', 1.00, 300000.00, 300000.00, 0.00, '2025-05-22 10:09:44', '2025-05-22 10:09:44'),
(18, 16, 21, 'Brique', 250.00, 400.00, 100000.00, 3000.00, '2025-05-22 10:12:00', '2025-05-22 10:12:00'),
(19, 16, 21, 'Foille', 1.00, 300000.00, 300000.00, 0.00, '2025-05-22 16:37:10', '2025-05-22 16:37:10'),
(20, 18, 22, 'Sable', 3.00, 40000.00, 120000.00, 0.00, '2025-05-27 12:38:53', '2025-05-27 12:38:53'),
(21, 18, 22, 'Gravier', 1.00, 40000.00, 40000.00, 0.00, '2025-05-27 12:39:36', '2025-05-27 12:39:36'),
(22, 18, 22, 'Ciment', 450.00, 5000.00, 2250000.00, 0.00, '2025-05-27 12:40:29', '2025-05-27 12:40:29'),
(23, 18, 22, 'Brique 12', 50.00, 400.00, 20000.00, 2000.00, '2025-05-27 12:41:17', '2025-05-27 12:41:17');

-- --------------------------------------------------------

--
-- Structure de la table `menuiserie`
--

CREATE TABLE `menuiserie` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `pu` int(11) DEFAULT NULL,
  `pt` int(11) DEFAULT NULL,
  `transport` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `menuiserie`
--

INSERT INTO `menuiserie` (`id`, `projet_id`, `devis_id`, `designation`, `quantite`, `pu`, `pt`, `transport`, `created_at`, `updated_at`) VALUES
(20, 16, 21, 'Porte en bois ', 4, 30000, 120000, 2000, '2025-05-22 10:32:50', '2025-05-22 10:32:50'),
(21, 16, 21, 'Fenêtre ', 1, 15000, 15000, 500, '2025-05-22 10:33:57', '2025-05-22 10:33:57'),
(22, 16, 21, 'Support de cuisine', 1, 30000, 30000, 0, '2025-05-22 10:38:36', '2025-05-22 10:38:36'),
(23, 18, 22, 'Porte 95x210', 2, 45000, 90000, 1000, '2025-05-27 11:54:37', '2025-05-27 11:54:37'),
(24, 18, 22, 'Porte 70x210', 4, 35000, 140000, 1000, '2025-05-27 11:55:34', '2025-05-27 11:55:34'),
(25, 18, 22, 'Fenêtre 60x50', 2, 20000, 40000, 500, '2025-05-27 11:56:57', '2025-05-27 11:56:57'),
(26, 18, 22, 'Fenêtre 90x70', 2, 25000, 50000, 500, '2025-05-27 11:57:29', '2025-05-27 11:57:29'),
(27, 18, 22, 'Support d\'évier', 2, 20000, 40000, 0, '2025-05-27 12:00:10', '2025-05-27 12:00:10');

-- --------------------------------------------------------

--
-- Structure de la table `peinture`
--

CREATE TABLE `peinture` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `pu` int(11) DEFAULT NULL,
  `pt` int(11) DEFAULT NULL,
  `transport` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `peinture`
--

INSERT INTO `peinture` (`id`, `projet_id`, `devis_id`, `designation`, `quantite`, `pu`, `pt`, `transport`, `created_at`, `updated_at`) VALUES
(13, 16, 21, 'Peinture à eau', 2, 16000, 32000, 500, '2025-05-22 09:59:33', '2025-05-22 09:59:33'),
(14, 16, 21, 'Colorant', 6, 1000, 6000, 200, '2025-05-22 10:00:33', '2025-05-22 10:00:33'),
(15, 16, 21, 'peinture à huile', 1, 8000, 8000, 200, '2025-05-22 10:01:15', '2025-05-22 10:01:15'),
(16, 16, 21, 'Rouleau', 2, 1000, 2000, 200, '2025-05-22 10:02:26', '2025-05-22 10:02:26'),
(17, 16, 21, 'Pinceau', 2, 500, 1000, 200, '2025-05-22 10:03:07', '2025-05-22 10:03:07'),
(18, 16, 21, 'Scotche', 5, 500, 2500, 200, '2025-05-22 10:04:33', '2025-05-22 10:04:33'),
(19, 16, 21, 'Diluant ', 1, 2000, 2000, 200, '2025-05-22 10:05:27', '2025-05-22 10:05:27'),
(20, 18, 22, 'Peinture à eau', 8, 16000, 128000, 1000, '2025-05-27 12:27:49', '2025-05-27 12:28:23'),
(21, 18, 22, 'Peinture à huile', 2, 8000, 16000, 500, '2025-05-27 12:31:01', '2025-05-27 12:31:01'),
(22, 18, 22, 'Colorant', 6, 1000, 6000, 500, '2025-05-27 12:31:36', '2025-05-27 12:31:36'),
(23, 18, 22, 'Rouleau', 4, 1000, 4000, 200, '2025-05-27 12:37:03', '2025-05-27 12:37:03'),
(24, 18, 22, 'Pinceaux ', 4, 500, 2000, 200, '2025-05-27 12:37:39', '2025-05-27 12:37:39'),
(25, 18, 22, 'Scotche', 6, 500, 3000, 200, '2025-05-27 12:38:05', '2025-05-27 12:38:05');

-- --------------------------------------------------------

--
-- Structure de la table `plomberie`
--

CREATE TABLE `plomberie` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `quantite` decimal(10,3) DEFAULT 0.000,
  `unite` varchar(50) DEFAULT 'unité',
  `pu` decimal(10,2) DEFAULT 0.00,
  `pt` decimal(15,2) DEFAULT 0.00,
  `prix_unitaire` decimal(10,2) DEFAULT 0.00,
  `total` decimal(15,2) DEFAULT 0.00,
  `transport` decimal(10,2) DEFAULT 0.00,
  `diametre` varchar(50) DEFAULT '',
  `longueur` decimal(8,2) DEFAULT 0.00,
  `materiau` varchar(100) DEFAULT '',
  `type_raccord` varchar(100) DEFAULT '',
  `pression` varchar(50) DEFAULT '',
  `observation` text DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `plomberie`
--

INSERT INTO `plomberie` (`id`, `projet_id`, `devis_id`, `designation`, `quantite`, `unite`, `pu`, `pt`, `prix_unitaire`, `total`, `transport`, `diametre`, `longueur`, `materiau`, `type_raccord`, `pression`, `observation`, `date_creation`, `date_modification`) VALUES
(3, 26, 35, 'Baignoire acrylique 170x75cm', 2.000, 'forfait', 10000.00, 20000.00, 0.00, 0.00, 2000.00, '100mm', 5.00, 'Céramique', 'Té de dérivation', 'Basse pression', NULL, '2025-08-06 08:48:20', '2025-08-06 08:48:20'),
(4, 26, 35, '555', 55.000, 'unité', 1000.00, 55000.00, 0.00, 0.00, 2000.00, '5', 5.00, 'pvc', '5', '10', NULL, '2025-08-06 09:50:41', '2025-08-06 09:50:41'),
(5, 26, 31, 'Coude PVC 45° Ø110', 2.000, 'unité', 1000.00, 2000.00, 0.00, 0.00, 500.00, '40mm', 5.00, 'Cuivre', 'Coude 30°', '16 bars', 'Bon', '2025-08-06 11:03:28', '2025-08-06 11:03:28'),
(6, 26, 31, 'Pomme de douche', 10.000, 'unité', 1000.00, 10000.00, 0.00, 0.00, 200.00, '50mm', 5.00, 'Multicouche', 'Té simple', '25 bars', 'C\'est du top', '2025-08-06 11:04:40', '2025-08-06 11:07:09');

-- --------------------------------------------------------

--
-- Structure de la table `projets`
--

CREATE TABLE `projets` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `client` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin_prevue` date DEFAULT NULL,
  `budget_previsionnel` decimal(15,2) DEFAULT 0.00,
  `statut` enum('En planification','En cours','Terminé','Suspendu') DEFAULT 'En planification',
  `date_creation` timestamp NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `projets`
--

INSERT INTO `projets` (`id`, `nom`, `description`, `client`, `adresse`, `date_debut`, `date_fin_prevue`, `budget_previsionnel`, `statut`, `date_creation`, `date_modification`) VALUES
(20, 'Copie de Finition , modification et construction ', 'Finition et modification de trois fois chambres-salons avec WC, douche et cuisine à l\'intérieur ', 'Mr. Koui Alain', 'Légbédji  (Duékoué)', NULL, NULL, 0.00, 'En planification', '2025-05-28 19:56:45', '2025-05-28 19:56:45'),
(26, 'Finition chambre-salon et studio', 'finition de 5 fois chambre salon et 5 fois studios ', 'DIE JOSEPH', 'Adresse à préciser - DIE JOSEPH', NULL, NULL, 0.00, 'En planification', '2025-07-23 16:38:57', '2025-08-04 14:48:40');

-- --------------------------------------------------------

--
-- Structure de la table `recapitulatif`
--

CREATE TABLE `recapitulatif` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) DEFAULT NULL,
  `devis_id` int(11) DEFAULT NULL,
  `categorie` varchar(100) NOT NULL,
  `total_materiaux` int(11) DEFAULT 0,
  `total_transport` int(11) DEFAULT 0,
  `main_oeuvre` int(11) NOT NULL DEFAULT 0,
  `main_oeuvre_maconnerie` int(11) DEFAULT 0,
  `total_ht` int(11) DEFAULT 0,
  `taux_tva` decimal(5,2) DEFAULT 18.00,
  `montant_tva` int(11) DEFAULT 0,
  `total_ttc` int(11) DEFAULT 0,
  `date_rapport` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `recapitulatif`
--

INSERT INTO `recapitulatif` (`id`, `projet_id`, `devis_id`, `categorie`, `total_materiaux`, `total_transport`, `main_oeuvre`, `main_oeuvre_maconnerie`, `total_ht`, `taux_tva`, `montant_tva`, `total_ttc`, `date_rapport`, `created_at`, `updated_at`) VALUES
(2912, 27, 36, 'plomberie', 4000, 1000, 0, 0, 5000, 18.00, 900, 5900, '2025-08-05 17:20:46', '2025-08-05 17:18:01', '2025-08-05 17:20:46'),
(2913, 26, 35, 'plomberie', 75000, 4000, 0, 0, 79000, 18.00, 14220, 93220, '2025-08-06 08:48:20', '2025-08-06 08:48:20', '2025-08-06 09:50:41'),
(2914, 26, 31, 'plomberie', 12000, 700, 1000, 0, 13700, 18.00, 2466, 16166, NULL, '2025-08-06 11:03:28', '2025-08-06 11:32:28');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vue_recapitulatif_complet`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vue_recapitulatif_complet` (
`projet_id` int(11)
,`projet_nom` varchar(255)
,`devis_id` int(11)
,`devis_numero` varchar(50)
,`categorie` varchar(100)
,`total_materiaux` int(11)
,`total_transport` int(11)
,`main_oeuvre` int(11)
,`total_ht` int(11)
,`taux_tva` decimal(5,2)
,`montant_tva` int(11)
,`total_ttc` int(11)
,`date_rapport` datetime
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vue_totaux_par_projet`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vue_totaux_par_projet` (
`projet_id` int(11)
,`projet_nom` varchar(255)
,`nombre_devis` bigint(21)
,`total_ttc_projet` decimal(32,0)
);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `archives_reports`
--
ALTER TABLE `archives_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projet_id` (`projet_id`),
  ADD KEY `devis_id` (`devis_id`);

--
-- Index pour la table `carrelage`
--
ALTER TABLE `carrelage`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `charpenterie`
--
ALTER TABLE `charpenterie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `devis`
--
ALTER TABLE `devis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projet_id` (`projet_id`);

--
-- Index pour la table `electricite`
--
ALTER TABLE `electricite`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ferraillage`
--
ALTER TABLE `ferraillage`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ferronnerie`
--
ALTER TABLE `ferronnerie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `historique_devis`
--
ALTER TABLE `historique_devis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_projet_devis` (`projet_id`,`devis_id`),
  ADD KEY `idx_date` (`date_action`);

--
-- Index pour la table `materiaux_base`
--
ALTER TABLE `materiaux_base`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `menuiserie`
--
ALTER TABLE `menuiserie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `peinture`
--
ALTER TABLE `peinture`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `plomberie`
--
ALTER TABLE `plomberie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_projet_devis` (`projet_id`,`devis_id`),
  ADD KEY `devis_id` (`devis_id`);

--
-- Index pour la table `projets`
--
ALTER TABLE `projets`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `recapitulatif`
--
ALTER TABLE `recapitulatif`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_devis_categorie` (`projet_id`,`devis_id`,`categorie`),
  ADD UNIQUE KEY `uk_projet_devis_categorie` (`projet_id`,`devis_id`,`categorie`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `archives_reports`
--
ALTER TABLE `archives_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `carrelage`
--
ALTER TABLE `carrelage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `charpenterie`
--
ALTER TABLE `charpenterie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `devis`
--
ALTER TABLE `devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT pour la table `electricite`
--
ALTER TABLE `electricite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT pour la table `ferraillage`
--
ALTER TABLE `ferraillage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `ferronnerie`
--
ALTER TABLE `ferronnerie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `historique_devis`
--
ALTER TABLE `historique_devis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `materiaux_base`
--
ALTER TABLE `materiaux_base`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `menuiserie`
--
ALTER TABLE `menuiserie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `peinture`
--
ALTER TABLE `peinture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `plomberie`
--
ALTER TABLE `plomberie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `projets`
--
ALTER TABLE `projets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `recapitulatif`
--
ALTER TABLE `recapitulatif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2915;

-- --------------------------------------------------------

--
-- Structure de la vue `vue_recapitulatif_complet`
--
DROP TABLE IF EXISTS `vue_recapitulatif_complet`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u370633571_Pacous07`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vue_recapitulatif_complet`  AS SELECT `p`.`id` AS `projet_id`, `p`.`nom` AS `projet_nom`, `d`.`id` AS `devis_id`, `d`.`numero` AS `devis_numero`, `r`.`categorie` AS `categorie`, `r`.`total_materiaux` AS `total_materiaux`, `r`.`total_transport` AS `total_transport`, `r`.`main_oeuvre` AS `main_oeuvre`, `r`.`total_ht` AS `total_ht`, `r`.`taux_tva` AS `taux_tva`, `r`.`montant_tva` AS `montant_tva`, `r`.`total_ttc` AS `total_ttc`, `r`.`date_rapport` AS `date_rapport` FROM ((`recapitulatif` `r` join `projets` `p` on(`r`.`projet_id` = `p`.`id`)) join `devis` `d` on(`r`.`devis_id` = `d`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure de la vue `vue_totaux_par_projet`
--
DROP TABLE IF EXISTS `vue_totaux_par_projet`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u370633571_Pacous07`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vue_totaux_par_projet`  AS SELECT `p`.`id` AS `projet_id`, `p`.`nom` AS `projet_nom`, count(`d`.`id`) AS `nombre_devis`, sum(`r`.`total_ttc`) AS `total_ttc_projet` FROM ((`projets` `p` left join `devis` `d` on(`p`.`id` = `d`.`projet_id`)) left join `recapitulatif` `r` on(`d`.`id` = `r`.`devis_id`)) GROUP BY `p`.`id`, `p`.`nom` ;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `devis`
--
ALTER TABLE `devis`
  ADD CONSTRAINT `devis_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `plomberie`
--
ALTER TABLE `plomberie`
  ADD CONSTRAINT `plomberie_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `plomberie_ibfk_2` FOREIGN KEY (`devis_id`) REFERENCES `devis` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
