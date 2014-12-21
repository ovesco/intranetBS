-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Dim 21 Décembre 2014 à 19:02
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `intranet`
--

-- --------------------------------------------------------

--
-- Structure de la table `app_adresses`
--

CREATE TABLE IF NOT EXISTS `app_adresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `npa` int(11) NOT NULL,
  `localite` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facturable` tinyint(1) NOT NULL,
  `remarques` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

--
-- Contenu de la table `app_adresses`
--

INSERT INTO `app_adresses` (`id`, `rue`, `npa`, `localite`, `facturable`, `remarques`) VALUES
(1, 'Chemin des planches 1', 1074, 'Savignole', 0, NULL),
(8, 'Chemin des ordis 3', 1002, 'Lausanne', 1, NULL),
(9, 'Chemin des vaches 3', 1024, 'Goumoin-le-jux', 0, NULL),
(10, 'Chemin des mirettes 13', 1018, 'hhhh', 1, NULL),
(12, '', 1022, 'savignyole', 0, NULL),
(14, 'Chemin des bails 13', 10012, NULL, 1, NULL),
(15, 'chemin du swag 22', 1022, 'savignoche', 1, NULL),
(16, 'chemin du swag 12', 1022, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `app_attributions`
--

CREATE TABLE IF NOT EXISTS `app_attributions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupe_id` int(11) DEFAULT NULL,
  `membre_id` int(11) DEFAULT NULL,
  `fonction_id` int(11) DEFAULT NULL,
  `dateDebut` date NOT NULL,
  `dateFin` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D6477D537A45358C` (`groupe_id`),
  KEY `IDX_D6477D536A99F74A` (`membre_id`),
  KEY `IDX_D6477D5357889920` (`fonction_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Contenu de la table `app_attributions`
--

INSERT INTO `app_attributions` (`id`, `groupe_id`, `membre_id`, `fonction_id`, `dateDebut`, `dateFin`) VALUES
(1, 1, 3, 3, '2012-01-08', '2015-01-07'),
(2, 2, 4, 2, '2010-01-08', '2015-01-09');

-- --------------------------------------------------------

--
-- Structure de la table `app_distinctions`
--

CREATE TABLE IF NOT EXISTS `app_distinctions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remarques` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `app_distinctions`
--

INSERT INTO `app_distinctions` (`id`, `nom`, `remarques`) VALUES
(1, '1ère classe', 'Epreuve du première classe');

-- --------------------------------------------------------

--
-- Structure de la table `app_familles`
--

CREATE TABLE IF NOT EXISTS `app_familles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse_id` int(11) DEFAULT NULL,
  `pere_id` int(11) DEFAULT NULL,
  `mere_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `telephone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `validity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_97149D8A4DE7DC5C` (`adresse_id`),
  UNIQUE KEY `UNIQ_97149D8A3FD73900` (`pere_id`),
  UNIQUE KEY `UNIQ_97149D8A39DEC40E` (`mere_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Contenu de la table `app_familles`
--

INSERT INTO `app_familles` (`id`, `adresse_id`, `pere_id`, `mere_id`, `nom`, `telephone`, `email`, `validity`) VALUES
(1, 1, 9, 3, 'hochet', '021 781 16 41', 'guilonas@hotmail.com', 1),
(2, NULL, NULL, NULL, 'vendureux', '', '', 0),
(5, 9, NULL, NULL, 'Muller', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `app_fonctions`
--

CREATE TABLE IF NOT EXISTS `app_fonctions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `abreviation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Contenu de la table `app_fonctions`
--

INSERT INTO `app_fonctions` (`id`, `nom`, `abreviation`) VALUES
(1, 'adjoint', 'ADJ'),
(2, 'Chef de patrouille', 'CP'),
(3, 'Chef de troupe', 'CT');

-- --------------------------------------------------------

--
-- Structure de la table `app_geniteurs`
--

CREATE TABLE IF NOT EXISTS `app_geniteurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adresse_id` int(11) DEFAULT NULL,
  `profession` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prenom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sexe` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `telephone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9C88B0744DE7DC5C` (`adresse_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Contenu de la table `app_geniteurs`
--

INSERT INTO `app_geniteurs` (`id`, `adresse_id`, `profession`, `prenom`, `sexe`, `telephone`, `email`) VALUES
(2, NULL, 'Médecin', 'Geanine', 'f', '021 781 18 93', 'geanine.muller@bluewin.ch'),
(3, 12, NULL, 'Mom', 'f', NULL, NULL),
(5, NULL, NULL, 'robinet', 'm', NULL, NULL),
(6, NULL, NULL, 'robinou', 'm', NULL, NULL),
(7, NULL, NULL, 'robinou', 'm', NULL, NULL),
(8, NULL, NULL, 'alain', 'm', NULL, NULL),
(9, 16, NULL, 'Un père', 'm', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `app_groupes`
--

CREATE TABLE IF NOT EXISTS `app_groupes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BA3EE694727ACA70` (`parent_id`),
  KEY `IDX_BA3EE694C54C8C93` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Contenu de la table `app_groupes`
--

INSERT INTO `app_groupes` (`id`, `parent_id`, `type_id`, `nom`) VALUES
(1, 3, 2, 'Montfort'),
(2, 1, 1, 'Jean-Bart'),
(3, NULL, 3, 'Brigade de Sauvabelin');

-- --------------------------------------------------------

--
-- Structure de la table `app_membres`
--

CREATE TABLE IF NOT EXISTS `app_membres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `famille_id` int(11) DEFAULT NULL,
  `adresse_id` int(11) DEFAULT NULL,
  `naissance` date NOT NULL,
  `numero_bs` int(11) DEFAULT NULL,
  `numero_avs` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `statut` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inscription` date NOT NULL,
  `remarques` longtext COLLATE utf8_unicode_ci,
  `prenom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sexe` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `telephone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `validity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_B41763D14DE7DC5C` (`adresse_id`),
  KEY `IDX_B41763D197A77B84` (`famille_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Contenu de la table `app_membres`
--

INSERT INTO `app_membres` (`id`, `famille_id`, `adresse_id`, `naissance`, `numero_bs`, `numero_avs`, `statut`, `inscription`, `remarques`, `prenom`, `sexe`, `telephone`, `email`, `validity`) VALUES
(3, 1, 10, '2014-12-22', 5267, '58496749567439', NULL, '2014-12-08', 'voici guillaume', 'Guillaume', 'm', '077 411 77 18', 'g.h01@hotmail.com', 1),
(4, 5, 8, '1963-10-23', NULL, '5984058058305839', NULL, '2014-12-09', NULL, 'Christian', 'm', '021 345 23 54', 'christian.muller@sauvabelin.ch', 0);

-- --------------------------------------------------------

--
-- Structure de la table `app_modifications`
--

CREATE TABLE IF NOT EXISTS `app_modifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `modificationsContainer_id` int(11) DEFAULT NULL,
  `oldValue` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `newValue` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E941257AB845049E` (`modificationsContainer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=48 ;

--
-- Contenu de la table `app_modifications`
--

INSERT INTO `app_modifications` (`id`, `path`, `date`, `modificationsContainer_id`, `oldValue`, `newValue`) VALUES
(4, 'famille.1.mere.adresse.npa', '2014-12-20 17:47:36', 6, NULL, '1022'),
(5, 'famille.1.mere.prenom', '2014-12-20 17:47:44', 6, NULL, 'Mom'),
(6, 'famille.1.mere.adresse.localite', '2014-12-20 17:57:08', 6, NULL, 'savignyole'),
(7, 'famille.1.mere.adresse.facturable', '2014-12-20 17:57:37', 6, NULL, '0'),
(41, 'famille.1.pere.prenom', '2014-12-21 09:02:11', 6, NULL, 'Un père'),
(42, 'famille.1.pere.adresse.rue', '2014-12-21 09:02:19', 6, NULL, 'chemin du swag 12'),
(43, 'famille.1.pere.adresse.npa', '2014-12-21 09:02:26', 6, NULL, '1022'),
(44, 'famille.1.pere.adresse.localite', '2014-12-21 09:02:32', 6, NULL, 'yolocity'),
(46, 'famille.1.pere.adresse.facturable', '2014-12-21 14:59:23', 6, '1', '0'),
(47, 'famille.1.mere.adresse.rue', '2014-12-21 15:24:50', 6, '', 'yolochemin');

-- --------------------------------------------------------

--
-- Structure de la table `app_modifications_container`
--

CREATE TABLE IF NOT EXISTS `app_modifications_container` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `container_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entity_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Contenu de la table `app_modifications_container`
--

INSERT INTO `app_modifications_container` (`id`, `container_key`, `class`, `entity_id`) VALUES
(6, 'b559889404e7168c56ab34a2b74c021e', 'Famille', 1);

-- --------------------------------------------------------

--
-- Structure de la table `app_obtention_distinctions`
--

CREATE TABLE IF NOT EXISTS `app_obtention_distinctions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `distinction_id` int(11) DEFAULT NULL,
  `membre_id` int(11) DEFAULT NULL,
  `obtention` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2BE2DD4259F3DFC6` (`distinction_id`),
  KEY `IDX_2BE2DD426A99F74A` (`membre_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `app_obtention_distinctions`
--

INSERT INTO `app_obtention_distinctions` (`id`, `distinction_id`, `membre_id`, `obtention`) VALUES
(1, 1, 3, '2014-12-11');

-- --------------------------------------------------------

--
-- Structure de la table `app_types`
--

CREATE TABLE IF NOT EXISTS `app_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fonction_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8FE304FD57889920` (`fonction_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Contenu de la table `app_types`
--

INSERT INTO `app_types` (`id`, `nom`, `fonction_id`) VALUES
(1, 'patrouille', 2),
(2, 'troupe', 3),
(3, 'brigade', NULL);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `app_attributions`
--
ALTER TABLE `app_attributions`
  ADD CONSTRAINT `FK_D6477D5357889920` FOREIGN KEY (`fonction_id`) REFERENCES `app_fonctions` (`id`),
  ADD CONSTRAINT `FK_D6477D536A99F74A` FOREIGN KEY (`membre_id`) REFERENCES `app_membres` (`id`),
  ADD CONSTRAINT `FK_D6477D537A45358C` FOREIGN KEY (`groupe_id`) REFERENCES `app_groupes` (`id`);

--
-- Contraintes pour la table `app_familles`
--
ALTER TABLE `app_familles`
  ADD CONSTRAINT `FK_97149D8A39DEC40E` FOREIGN KEY (`mere_id`) REFERENCES `app_geniteurs` (`id`),
  ADD CONSTRAINT `FK_97149D8A3FD73900` FOREIGN KEY (`pere_id`) REFERENCES `app_geniteurs` (`id`),
  ADD CONSTRAINT `FK_97149D8A4DE7DC5C` FOREIGN KEY (`adresse_id`) REFERENCES `app_adresses` (`id`);

--
-- Contraintes pour la table `app_geniteurs`
--
ALTER TABLE `app_geniteurs`
  ADD CONSTRAINT `FK_9C88B0744DE7DC5C` FOREIGN KEY (`adresse_id`) REFERENCES `app_adresses` (`id`);

--
-- Contraintes pour la table `app_groupes`
--
ALTER TABLE `app_groupes`
  ADD CONSTRAINT `FK_BA3EE694727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `app_groupes` (`id`),
  ADD CONSTRAINT `FK_BA3EE694C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `app_types` (`id`);

--
-- Contraintes pour la table `app_membres`
--
ALTER TABLE `app_membres`
  ADD CONSTRAINT `FK_B41763D14DE7DC5C` FOREIGN KEY (`adresse_id`) REFERENCES `app_adresses` (`id`),
  ADD CONSTRAINT `FK_B41763D197A77B84` FOREIGN KEY (`famille_id`) REFERENCES `app_familles` (`id`);

--
-- Contraintes pour la table `app_modifications`
--
ALTER TABLE `app_modifications`
  ADD CONSTRAINT `FK_E941257AB845049E` FOREIGN KEY (`modificationsContainer_id`) REFERENCES `app_modifications_container` (`id`);

--
-- Contraintes pour la table `app_obtention_distinctions`
--
ALTER TABLE `app_obtention_distinctions`
  ADD CONSTRAINT `FK_2BE2DD4259F3DFC6` FOREIGN KEY (`distinction_id`) REFERENCES `app_distinctions` (`id`),
  ADD CONSTRAINT `FK_2BE2DD426A99F74A` FOREIGN KEY (`membre_id`) REFERENCES `app_membres` (`id`);

--
-- Contraintes pour la table `app_types`
--
ALTER TABLE `app_types`
  ADD CONSTRAINT `FK_8FE304FD57889920` FOREIGN KEY (`fonction_id`) REFERENCES `app_fonctions` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
