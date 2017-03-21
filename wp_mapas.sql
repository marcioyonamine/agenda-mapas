-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: 179.188.16.84
-- Generation Time: 21-Mar-2017 às 08:49
-- Versão do servidor: 5.6.35-80.0-log
-- PHP Version: 5.6.30-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ccspsite`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `wp_mapas`
--

DROP TABLE IF EXISTS `wp_mapas`;
CREATE TABLE `wp_mapas` (
  `id` int(11) NOT NULL,
  `wp` longtext COLLATE latin1_general_ci NOT NULL,
  `mapas` longtext COLLATE latin1_general_ci NOT NULL,
  `type` varchar(120) COLLATE latin1_general_ci NOT NULL,
  `edit` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_mapas`
--
ALTER TABLE `wp_mapas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wp_mapas`
--
ALTER TABLE `wp_mapas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
