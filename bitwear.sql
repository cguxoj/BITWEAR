-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql2.small.pl
-- Generation Time: Dec 06, 2025 at 09:43 AM
-- Wersja serwera: 8.0.39
-- Wersja PHP: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `m2358_bitwear`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `atrybuty_produktow`
--

CREATE TABLE `atrybuty_produktow` (
  `id_atrybutu` int NOT NULL,
  `id_produktu` int NOT NULL,
  `nazwa_atrybutu` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `wartosc_atrybutu` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `atrybuty_produktow`
--

INSERT INTO `atrybuty_produktow` (`id_atrybutu`, `id_produktu`, `nazwa_atrybutu`, `wartosc_atrybutu`) VALUES
(1, 1, 'kolor', 'czarny'),
(2, 2, 'kolor', 'biały'),
(3, 3, 'kolor', 'czarny'),
(4, 4, 'kolor', 'biały'),
(5, 5, 'kolor', 'czarny'),
(6, 6, 'kolor', 'biały');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorie`
--

CREATE TABLE `kategorie` (
  `id_kategorii` int NOT NULL,
  `nazwa` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorie`
--

INSERT INTO `kategorie` (`id_kategorii`, `nazwa`) VALUES
(1, 'jeansy'),
(2, 'sneakersy'),
(3, 'Skarpety');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kody_rabatowe`
--

CREATE TABLE `kody_rabatowe` (
  `id_kodu` int NOT NULL,
  `kod` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `typ` enum('procent','kwota') COLLATE utf8mb4_general_ci NOT NULL,
  `wartosc` decimal(10,2) NOT NULL,
  `minimalna_wartosc` decimal(10,2) DEFAULT '0.00',
  `data_waznosci` date DEFAULT NULL,
  `jednorazowy` tinyint(1) DEFAULT '0',
  `uzyty_przez` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kody_rabatowe`
--

INSERT INTO `kody_rabatowe` (`id_kodu`, `kod`, `typ`, `wartosc`, `minimalna_wartosc`, `data_waznosci`, `jednorazowy`, `uzyty_przez`) VALUES
(10, 'DENIMDROP', 'procent', 15.00, 0.00, NULL, 0, NULL),
(11, 'BLACKFRIDAY', 'kwota', 50.00, 300.00, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `koszyk`
--

CREATE TABLE `koszyk` (
  `id_uzytkownika` int NOT NULL,
  `id_produktu` int NOT NULL,
  `id_rozmiaru` int NOT NULL,
  `ilosc` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `koszyk`
--

INSERT INTO `koszyk` (`id_uzytkownika`, `id_produktu`, `id_rozmiaru`, `ilosc`) VALUES
(1, 14, 1, 1),
(9, 2, 18, 3),
(9, 30, 7, 1),
(9, 34, 20, 1),
(11, 3, 20, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `materialy`
--

CREATE TABLE `materialy` (
  `id_materialu` int NOT NULL,
  `nazwa` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materialy`
--

INSERT INTO `materialy` (`id_materialu`, `nazwa`) VALUES
(1, 'bawełna'),
(2, 'denim'),
(3, 'skóra'),
(4, 'syntetyk');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `plec`
--

CREATE TABLE `plec` (
  `id_pleci` int NOT NULL,
  `nazwa` enum('on','ona','dziecko') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plec`
--

INSERT INTO `plec` (`id_pleci`, `nazwa`) VALUES
(1, 'on'),
(2, 'ona'),
(3, 'dziecko');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownicy`
--

CREATE TABLE `pracownicy` (
  `id_pracownika` int NOT NULL,
  `id_uzytkownika` int NOT NULL,
  `typ` enum('szef','pracownik') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pracownik'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pracownicy`
--

INSERT INTO `pracownicy` (`id_pracownika`, `id_uzytkownika`, `typ`) VALUES
(7, 7, 'pracownik'),
(8, 8, 'pracownik'),
(9, 9, 'szef');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `produkty`
--

CREATE TABLE `produkty` (
  `id_produktu` int NOT NULL,
  `nazwa` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `opis` text COLLATE utf8mb4_general_ci,
  `id_materialu` int NOT NULL,
  `cena` decimal(10,2) NOT NULL,
  `id_kategorii` int NOT NULL,
  `plec` enum('on','ona','dziecko','uni') COLLATE utf8mb4_general_ci DEFAULT 'uni',
  `data_dodania` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produkty`
--

INSERT INTO `produkty` (`id_produktu`, `nazwa`, `opis`, `id_materialu`, `cena`, `id_kategorii`, `plec`, `data_dodania`) VALUES
(1, 'Skarpety 5-pak', 'Czarne skarpety z miękkiej bawełny – komfort na co dzień.', 1, 19.99, 3, 'on', '2025-11-29 17:54:52'),
(2, 'Skarpety 5-pak', 'Białe skarpety z miękkiej bawełny – wygoda i styl.', 1, 19.99, 3, 'on', '2025-11-29 17:54:52'),
(3, 'Skarpety 5-pak', 'Czarne skarpety z miękkiej bawełny – idealne do każdej stylizacji.', 1, 19.99, 3, 'ona', '2025-11-29 17:54:52'),
(4, 'Skarpety 5-pak', 'Białe skarpety z miękkiej bawełny – klasyka w każdym stroju.', 1, 19.99, 3, 'ona', '2025-11-29 17:54:52'),
(5, 'Skarpety 5-pak', 'Czarne skarpety dla dzieci – wygoda i trwałość.', 1, 19.99, 3, 'dziecko', '2025-11-29 17:54:52'),
(6, 'Skarpety 5-pak', 'Białe skarpety dla dzieci – miękkie i przyjemne.', 1, 19.99, 3, 'dziecko', '2025-11-29 17:54:52'),
(13, 'Baggy High Jeans', 'Jeansy baggy, niebieskie, miękkie i wygodne – idealne na co dzień.', 2, 199.99, 1, 'ona', '2025-11-29 17:54:52'),
(14, 'Barrel High Ankle Jeans', 'Jeansy typu barrel, jasnoniebieskie, wygodne i modne.', 2, 209.99, 1, 'ona', '2025-11-29 17:54:52'),
(15, 'Wide High Jeans', 'Szerokie jeansy wysokiej talii, granatowe, komfortowe i stylowe.', 2, 189.99, 1, 'ona', '2025-11-29 17:54:52'),
(16, 'Wide Regular Jeans', 'Jeansy szerokie regular, klasyczne niebieskie – wygodne i modne.', 2, 179.99, 1, 'ona', '2025-11-29 17:54:52'),
(17, 'Wide Ultra High Jeans', 'Ultra high waist jeans, granatowe, idealne do każdej stylizacji.', 2, 219.99, 1, 'ona', '2025-11-29 17:54:52'),
(18, 'Astro Loose Jeans', 'Jeansy luźne, czarne, wygodne i nowoczesne.', 2, 199.99, 1, 'on', '2025-11-29 17:54:52'),
(20, 'Baggy Jeans', 'Baggy jeansy, zielone z kropkami, wygodne i modne.', 2, 189.99, 1, 'on', '2025-11-29 17:54:52'),
(21, 'Baggy Jeans', 'Baggy jeansy, klasyczne niebieskie – komfort i styl.', 2, 189.99, 1, 'on', '2025-11-29 17:54:52'),
(22, 'Relaxed Bootcut Jeans', 'Jeansy bootcut, granatowe, wygodne i swobodne.', 2, 209.99, 1, 'on', '2025-11-29 17:54:52'),
(23, 'Baggy Fit Bootcut Leg Jeans', 'Dziecięce jeansy baggy, niebieskie, miękkie i wygodne.', 2, 129.99, 1, 'dziecko', '2025-11-29 17:54:52'),
(24, 'Baggy Fit Jeans', 'Jeansy dziecięce, niebieskie, wygodne i wytrzymałe.', 2, 119.99, 1, 'dziecko', '2025-11-29 17:54:52'),
(25, 'Comfort Stretch Slim Fit Jeans', 'Jeansy dziecięce, elastyczne, komfortowe, jasnoniebieskie.', 2, 129.99, 1, 'dziecko', '2025-11-29 17:54:52'),
(26, 'Loose Fit Jeans z podszewką', 'Jeansy dziecięce, niebieskie z podszewką, wygodne i trwałe.', 2, 139.99, 1, 'dziecko', '2025-11-29 17:54:52'),
(27, 'Adidas Gazelle Bold W', 'Białe skórzane sneakersy, lekkie i stylowe – idealne do miasta.', 4, 399.99, 2, 'ona', '2025-11-29 17:54:52'),
(28, 'Adidas Superstar 2', 'Białe sneakersy z charakterystycznym designem, wygodne na co dzień.', 4, 399.99, 2, 'ona', '2025-11-29 17:54:52'),
(29, 'New Balance 530', 'Granatowe sneakersy, komfortowe i modne, idealne na spacer.', 4, 399.99, 2, 'ona', '2025-11-29 17:54:52'),
(30, 'Adidas Campus 00s', 'Czarne sneakersy, klasyczne i wygodne na co dzień.', 4, 399.99, 2, 'on', '2025-11-29 17:54:52'),
(31, 'New Balance 2002R', 'Nowoczesne sneakersy, białe, komfortowe i stylowe.', 4, 399.99, 2, 'on', '2025-11-29 17:54:52'),
(32, 'Nike Air Force 1', 'Białe sneakersy kultowe, wygodne i uniwersalne.', 4, 399.99, 2, 'on', '2025-11-29 17:54:52'),
(33, 'Nike P6000', 'Czarne sneakersy sportowe, wygodne do codziennego noszenia.', 4, 399.99, 2, 'on', '2025-11-29 17:54:52'),
(34, 'New Balance 740', 'Dziecięce sneakersy, niebieskie, wygodne i trwałe.', 4, 299.99, 2, 'dziecko', '2025-11-29 17:54:52'),
(35, 'Nike Air Force 1 GS', 'Dziecięce białe sneakersy, wygodne i uniwersalne.', 4, 299.99, 2, 'dziecko', '2025-11-29 17:54:52'),
(36, 'Nike Dunk Low BG', 'Sneakersy dziecięce, kolorowe, stylowe i komfortowe.', 4, 299.99, 2, 'dziecko', '2025-11-29 17:54:52');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `produkty_rozmiary`
--

CREATE TABLE `produkty_rozmiary` (
  `id_p_r` int NOT NULL,
  `id_produktu` int NOT NULL,
  `id_rozmiaru` int NOT NULL,
  `ilosc` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produkty_rozmiary`
--

INSERT INTO `produkty_rozmiary` (`id_p_r`, `id_produktu`, `id_rozmiaru`, `ilosc`) VALUES
(1, 1, 18, 50),
(2, 1, 19, 50),
(3, 1, 20, 50),
(4, 2, 18, 46),
(5, 2, 19, 50),
(6, 2, 20, 50),
(7, 3, 18, 48),
(8, 3, 19, 50),
(9, 3, 20, 49),
(10, 4, 18, 46),
(11, 4, 19, 50),
(12, 4, 20, 48),
(13, 5, 18, 48),
(14, 5, 19, 49),
(15, 5, 20, 50),
(16, 6, 18, 50),
(17, 6, 19, 50),
(18, 6, 20, 50),
(19, 13, 1, 19),
(20, 13, 2, 20),
(21, 13, 3, 20),
(22, 13, 4, 20),
(23, 13, 5, 20),
(24, 13, 6, 20),
(25, 14, 1, 19),
(26, 14, 2, 20),
(27, 14, 3, 20),
(28, 14, 4, 20),
(29, 14, 5, 20),
(30, 14, 6, 20),
(31, 15, 1, 20),
(32, 15, 2, 20),
(33, 15, 3, 20),
(34, 15, 4, 20),
(35, 15, 5, 20),
(36, 15, 6, 20),
(37, 16, 1, 20),
(38, 16, 2, 20),
(39, 16, 3, 20),
(40, 16, 4, 20),
(41, 16, 5, 20),
(42, 16, 6, 20),
(43, 17, 1, 20),
(44, 17, 2, 20),
(45, 17, 3, 20),
(46, 17, 4, 20),
(47, 17, 5, 20),
(48, 17, 6, 20),
(49, 18, 1, 18),
(50, 18, 2, 20),
(51, 18, 3, 20),
(52, 18, 4, 20),
(53, 18, 5, 20),
(54, 18, 6, 20),
(61, 20, 1, 18),
(62, 20, 2, 20),
(63, 20, 3, 20),
(64, 20, 4, 20),
(65, 20, 5, 20),
(66, 20, 6, 20),
(67, 21, 1, 20),
(68, 21, 2, 20),
(69, 21, 3, 20),
(70, 21, 4, 20),
(71, 21, 5, 20),
(72, 21, 6, 20),
(73, 22, 1, 20),
(74, 22, 2, 20),
(75, 22, 3, 20),
(76, 22, 4, 20),
(77, 22, 5, 20),
(78, 22, 6, 20),
(79, 23, 18, 20),
(80, 23, 19, 20),
(81, 23, 20, 18),
(82, 24, 18, 20),
(83, 24, 19, 20),
(84, 24, 20, 20),
(85, 25, 18, 20),
(86, 25, 19, 20),
(87, 25, 20, 20),
(88, 26, 18, 19),
(89, 26, 19, 20),
(90, 26, 20, 20),
(91, 27, 7, 4),
(92, 27, 8, 10),
(93, 27, 9, 10),
(94, 27, 10, 10),
(95, 27, 11, 10),
(96, 27, 12, 10),
(97, 27, 13, 10),
(98, 27, 14, 8),
(99, 27, 15, 10),
(100, 27, 16, 10),
(101, 28, 7, 10),
(102, 28, 8, 10),
(103, 28, 9, 10),
(104, 28, 10, 10),
(105, 28, 11, 10),
(106, 28, 12, 10),
(107, 28, 13, 10),
(108, 28, 14, 10),
(109, 28, 15, 10),
(110, 28, 16, 10),
(111, 29, 7, 10),
(112, 29, 8, 10),
(113, 29, 9, 10),
(114, 29, 10, 10),
(115, 29, 11, 10),
(116, 29, 12, 10),
(117, 29, 13, 10),
(118, 29, 14, 10),
(119, 29, 15, 10),
(120, 29, 16, 10),
(121, 30, 7, 7),
(122, 30, 8, 10),
(123, 30, 9, 10),
(124, 30, 10, 10),
(125, 30, 11, 10),
(126, 30, 12, 10),
(127, 30, 13, 10),
(128, 30, 14, 10),
(129, 30, 15, 10),
(130, 30, 16, 10),
(131, 31, 7, 10),
(132, 31, 8, 10),
(133, 31, 9, 10),
(134, 31, 10, 10),
(135, 31, 11, 10),
(136, 31, 12, 10),
(137, 31, 13, 10),
(138, 31, 14, 10),
(139, 31, 15, 10),
(140, 31, 16, 10),
(141, 32, 7, 10),
(142, 32, 8, 10),
(143, 32, 9, 10),
(144, 32, 10, 10),
(145, 32, 11, 10),
(146, 32, 12, 10),
(147, 32, 13, 10),
(148, 32, 14, 10),
(149, 32, 15, 10),
(150, 32, 16, 10),
(151, 33, 7, 10),
(152, 33, 8, 10),
(153, 33, 9, 10),
(154, 33, 10, 10),
(155, 33, 11, 10),
(156, 33, 12, 10),
(157, 33, 13, 10),
(158, 33, 14, 10),
(159, 33, 15, 10),
(160, 33, 16, 10),
(161, 34, 18, 9),
(162, 34, 19, 10),
(163, 34, 20, 9),
(164, 35, 18, 10),
(165, 35, 19, 10),
(166, 35, 20, 10),
(167, 36, 18, 8),
(168, 36, 19, 10),
(169, 36, 20, 9);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rozmiary`
--

CREATE TABLE `rozmiary` (
  `id_rozmiaru` int NOT NULL,
  `nazwa` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rozmiary`
--

INSERT INTO `rozmiary` (`id_rozmiaru`, `nazwa`) VALUES
(1, 'XS'),
(2, 'S'),
(3, 'M'),
(4, 'L'),
(5, 'XL'),
(6, 'XXL'),
(7, '36'),
(8, '37'),
(9, '38'),
(10, '39'),
(11, '40'),
(12, '41'),
(13, '42'),
(14, '43'),
(15, '44'),
(16, '45'),
(17, 'uni'),
(18, '35-37'),
(19, '38-40'),
(20, '41-43');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rozmiary_typy`
--

CREATE TABLE `rozmiary_typy` (
  `id_typu` int NOT NULL,
  `nazwa` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rozmiary_typy`
--

INSERT INTO `rozmiary_typy` (`id_typu`, `nazwa`) VALUES
(1, 'skarpetki');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ulubione`
--

CREATE TABLE `ulubione` (
  `id_uzytkownika` int NOT NULL,
  `id_produktu` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ulubione`
--

INSERT INTO `ulubione` (`id_uzytkownika`, `id_produktu`) VALUES
(1, 1),
(1, 3),
(1, 4),
(1, 13),
(1, 16),
(1, 32),
(9, 34),
(1, 36);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id_uzytkownika` int NOT NULL,
  `imie` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `haslo_hash` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefon` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `poczta` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `kod_pocztowy` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `adres` text COLLATE utf8mb4_general_ci,
  `rola` enum('klient','admin','staff') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'klient',
  `data_rejestracji` datetime DEFAULT CURRENT_TIMESTAMP,
  `token_resetu_hasla` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_wygasa` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id_uzytkownika`, `imie`, `email`, `haslo_hash`, `telefon`, `poczta`, `kod_pocztowy`, `adres`, `rola`, `data_rejestracji`, `token_resetu_hasla`, `token_wygasa`) VALUES
(1, 'Janek', 'jakisjanZAQ123@gmail.pl', '$2y$10$A2SUn7nv2x6xMyuvkJckgu90j.k7UL/Db5WsoC83k3Dtug.pLHzmO', '121312312', 'Warszawa', '00-010', 'ul. Mickiewicza 30/45', 'klient', '2025-11-29 17:54:52', NULL, NULL),
(6, 'Kasia Nowak', 'kasia@bitwear.pl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234321444', 'Siedlce', '08-110', 'ul. Mazurska 2', 'staff', '2025-11-30 18:16:31', NULL, NULL),
(7, 'Maciej Wiśniewski', 'maciej@bitwear.pl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '928391820', 'Warszawa', '00-010', 'ul. Józefa Piłsudskiego 12/39', 'staff', '2025-11-30 18:16:31', NULL, NULL),
(8, 'Ola Zielińska', 'ola@bitwear.pl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '928192031', 'Siedlce', '08-110', 'ul. Leśna 30', 'staff', '2025-11-30 18:16:31', NULL, NULL),
(9, 'Adam Kowalski', 'szef@bitwear.pl', '$2y$10$V1wJMuiwJBTVINYnPXb9tO.rlbRfLdoBnMK9dlL3newa69OenUrUy', '501234112', 'Łuków', '21-410', 'ul. Konarskiego 23', 'admin', '2025-11-30 18:16:58', NULL, NULL),
(11, 'Brzytwa Ostra', 'cguxoj@gmail.com', '$2y$10$JpO/A8bsDtSAjQN/95Hh2OwwsLlUXlPQig.4IgpbrJdnUslOpqhc2', '091283719', 'Warszawa', '00-010', 'elo 123', 'klient', '2025-12-05 18:08:39', NULL, NULL),
(13, 'd3', 'd32360864@gmail.com', '$2y$10$hBxsVcyNXR9ii2FoUlwuj.dZCqyvvybuM2RimOevBDMl82/d3K.8y', '981813178', 'Siedlce', '08-110', 'ul. Leśna 21', 'klient', '2025-12-05 21:25:13', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia`
--

CREATE TABLE `zamowienia` (
  `id_zamowienia` int NOT NULL,
  `id_uzytkownika` int DEFAULT NULL,
  `data_zamowienia` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('nowe','w realizacji','wysłane','zakończone') COLLATE utf8mb4_general_ci DEFAULT 'nowe',
  `cena_calkowita` decimal(10,2) NOT NULL,
  `kod_rabatowy` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rabat` decimal(10,2) DEFAULT '0.00',
  `id_pracownika` int DEFAULT NULL,
  `email_wyslano` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zamowienia`
--

INSERT INTO `zamowienia` (`id_zamowienia`, `id_uzytkownika`, `data_zamowienia`, `status`, `cena_calkowita`, `kod_rabatowy`, `rabat`, `id_pracownika`, `email_wyslano`) VALUES
(2, 1, '2025-12-02 15:27:42', 'wysłane', 619.97, NULL, 0.00, 9, 0),
(3, 1, '2025-12-02 20:18:44', 'nowe', 254.99, '0', 45.00, NULL, 0),
(4, 1, '2025-12-02 20:31:25', 'nowe', 118.99, '0', 21.00, NULL, 0),
(5, NULL, '2025-12-05 09:22:03', 'nowe', 399.99, '', 0.00, NULL, 0),
(6, 11, '2025-12-05 18:11:37', 'wysłane', 299.99, '', 0.00, 8, 1),
(7, 11, '2025-12-05 19:56:16', 'nowe', 39.98, '', 0.00, NULL, 0),
(8, 11, '2025-12-05 20:47:09', 'nowe', 19.99, '', 0.00, NULL, 0),
(9, NULL, '2025-12-05 20:59:43', 'nowe', 399.99, '', 0.00, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia_dane_goscie`
--

CREATE TABLE `zamowienia_dane_goscie` (
  `id_zamowienia` int NOT NULL,
  `imie` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telefon` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kod_pocztowy` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `poczta` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `adres` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zamowienia_dane_goscie`
--

INSERT INTO `zamowienia_dane_goscie` (`id_zamowienia`, `imie`, `email`, `telefon`, `kod_pocztowy`, `poczta`, `adres`) VALUES
(5, 'Andrzej Walicki', 'ktostam@gmail.com', '', '00-010', 'Warszawa', 'ul. Spadowa 13'),
(9, 'Ktoś', 'd32360864@gmail.com', '', '00-010', 'Warszawa', 'Leśna 12');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zamowienia_produkty`
--

CREATE TABLE `zamowienia_produkty` (
  `id_zamowienia` int NOT NULL,
  `id_produktu` int NOT NULL,
  `id_rozmiaru` int NOT NULL,
  `ilosc` int NOT NULL,
  `cena_jedn` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zamowienia_produkty`
--

INSERT INTO `zamowienia_produkty` (`id_zamowienia`, `id_produktu`, `id_rozmiaru`, `ilosc`, `cena_jedn`) VALUES
(2, 3, 18, 1, 19.99),
(2, 18, 1, 1, 199.99),
(2, 27, 7, 1, 399.99),
(3, 36, 20, 1, 299.99),
(4, 26, 18, 1, 139.99),
(5, 27, 14, 1, 399.99),
(6, 36, 18, 1, 299.99),
(7, 4, 18, 2, 19.99),
(8, 5, 18, 1, 19.99),
(9, 30, 7, 1, 399.99);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zdjecia_produktow`
--

CREATE TABLE `zdjecia_produktow` (
  `id_zdjecia` int NOT NULL,
  `id_produktu` int NOT NULL,
  `sciezka` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `typ` enum('main','thumbnail','gallery') COLLATE utf8mb4_general_ci DEFAULT 'main'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zdjecia_produktow`
--

INSERT INTO `zdjecia_produktow` (`id_zdjecia`, `id_produktu`, `sciezka`, `typ`) VALUES
(1, 1, 'zdjecia/produkty/on/Dodatki/Skarpety 5-pak v1.png', 'main'),
(3, 2, 'zdjecia/produkty/on/Dodatki/Skarpety 5-pak v2.png', 'main'),
(5, 3, 'zdjecia/produkty/ona/Dodatki/Skarpety 5-pak v1.png', 'main'),
(7, 4, 'zdjecia/produkty/ona/Dodatki/Skarpety 5-pak v2.png', 'main'),
(9, 5, 'zdjecia/produkty/dziecko/Dodatki/Skarpety 5-pak v1.png', 'main'),
(11, 6, 'zdjecia/produkty/dziecko/Dodatki/Skarpety 5-pak v2.png', 'main'),
(13, 13, 'zdjecia/produkty/ona/Jeansy/Baggy High Jeans v1.png', 'main'),
(14, 13, 'zdjecia/produkty/ona/Jeansy/Baggy High Jeans v2.png', 'gallery'),
(15, 14, 'zdjecia/produkty/ona/Jeansy/Barrel High Ankle Jeans v1.png', 'main'),
(16, 14, 'zdjecia/produkty/ona/Jeansy/Barrel High Ankle Jeans v2.png', 'gallery'),
(17, 15, 'zdjecia/produkty/ona/Jeansy/Wide High Jeans v1.png', 'main'),
(18, 15, 'zdjecia/produkty/ona/Jeansy/Wide High Jeans v2.png', 'gallery'),
(19, 16, 'zdjecia/produkty/ona/Jeansy/Wide Regular Jeans v1.png', 'main'),
(20, 16, 'zdjecia/produkty/ona/Jeansy/Wide Regular Jeans v2.png', 'gallery'),
(21, 17, 'zdjecia/produkty/ona/Jeansy/Wide Ultra High Jeans v1.png', 'main'),
(22, 17, 'zdjecia/produkty/ona/Jeansy/Wide Ultra High Jeans v2.png', 'gallery'),
(23, 18, 'zdjecia/produkty/on/Jeansy/astro loose jeans - czarne v1.png', 'main'),
(27, 20, 'zdjecia/produkty/on/Jeansy/baggy jeans - zielone z kropkami v1.png', 'main'),
(28, 20, 'zdjecia/produkty/on/Jeansy/baggy jeans - zielone z kropkami v2.png', 'gallery'),
(29, 21, 'zdjecia/produkty/on/Jeansy/Baggy Jeans v1.png', 'main'),
(30, 21, 'zdjecia/produkty/on/Jeansy/Baggy Jeans v2.png', 'gallery'),
(31, 22, 'zdjecia/produkty/on/Jeansy/Relaxed Bootcut Jeans v1.png', 'main'),
(32, 22, 'zdjecia/produkty/on/Jeansy/Relaxed Bootcut Jeans v2.png', 'gallery'),
(33, 23, 'zdjecia/produkty/dziecko/Jeansy/Baggy Fit Bootcut Leg Jeans v1.png', 'main'),
(34, 23, 'zdjecia/produkty/dziecko/Jeansy/Baggy Fit Bootcut Leg Jeans v2.png', 'gallery'),
(35, 24, 'zdjecia/produkty/dziecko/Jeansy/Baggy Fit Jeans v1.png', 'main'),
(36, 24, 'zdjecia/produkty/dziecko/Jeansy/Baggy Fit Jeans v2.png', 'gallery'),
(37, 25, 'zdjecia/produkty/dziecko/Jeansy/Comfort Stretch Slim Fit Jeans v1.png', 'main'),
(38, 25, 'zdjecia/produkty/dziecko/Jeansy/Comfort Stretch Slim Fit Jeans v2.png', 'gallery'),
(39, 26, 'zdjecia/produkty/dziecko/Jeansy/Loose Fit Jeans z podszewką v1.png', 'main'),
(40, 26, 'zdjecia/produkty/dziecko/Jeansy/Loose Fit Jeans z podszewką v2.png', 'gallery'),
(41, 27, 'zdjecia/produkty/ona/Sneakersy/adidas gazelle bold w v1.png', 'main'),
(42, 27, 'zdjecia/produkty/ona/Sneakersy/adidas gazelle bold w v2.png', 'gallery'),
(43, 28, 'zdjecia/produkty/ona/Sneakersy/adidas superstar 2 v1.png', 'main'),
(44, 28, 'zdjecia/produkty/ona/Sneakersy/adidas superstar 2 v2.png', 'gallery'),
(45, 29, 'zdjecia/produkty/ona/Sneakersy/new balance 530 v1.png', 'main'),
(46, 29, 'zdjecia/produkty/ona/Sneakersy/new balance 530 v2.png', 'gallery'),
(47, 30, 'zdjecia/produkty/on/Sneakersy/adidas campus 00s v1.png', 'main'),
(48, 30, 'zdjecia/produkty/on/Sneakersy/adidas campus 00s v2.png', 'gallery'),
(49, 31, 'zdjecia/produkty/on/Sneakersy/new balance 2002r v1.png', 'main'),
(50, 31, 'zdjecia/produkty/on/Sneakersy/new balance 2002r v2.png', 'gallery'),
(51, 32, 'zdjecia/produkty/on/Sneakersy/nike air force 1 v1.png', 'main'),
(52, 32, 'zdjecia/produkty/on/Sneakersy/nike air force 1 v2.png', 'gallery'),
(53, 33, 'zdjecia/produkty/on/Sneakersy/nike p6000 v1.png', 'main'),
(54, 33, 'zdjecia/produkty/on/Sneakersy/nike p6000 v2.png', 'gallery'),
(55, 34, 'zdjecia/produkty/dziecko/Sneakersy/new balance 740 v1.png', 'main'),
(56, 34, 'zdjecia/produkty/dziecko/Sneakersy/new balance 740 v2.png', 'gallery'),
(57, 35, 'zdjecia/produkty/dziecko/Sneakersy/nike air force 1 gs v1.png', 'main'),
(58, 35, 'zdjecia/produkty/dziecko/Sneakersy/nike air force 1 gs v2.png', 'gallery'),
(59, 36, 'zdjecia/produkty/dziecko/Sneakersy/nike dunk low bg v1.png', 'main'),
(60, 36, 'zdjecia/produkty/dziecko/Sneakersy/nike dunk low bg v2.png', 'gallery');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `atrybuty_produktow`
--
ALTER TABLE `atrybuty_produktow`
  ADD PRIMARY KEY (`id_atrybutu`),
  ADD KEY `id_produktu` (`id_produktu`);

--
-- Indeksy dla tabeli `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`id_kategorii`);

--
-- Indeksy dla tabeli `kody_rabatowe`
--
ALTER TABLE `kody_rabatowe`
  ADD PRIMARY KEY (`id_kodu`),
  ADD UNIQUE KEY `kod` (`kod`),
  ADD UNIQUE KEY `jednorazowy_uzytkownik` (`kod`,`uzyty_przez`),
  ADD KEY `kod_2` (`kod`);

--
-- Indeksy dla tabeli `koszyk`
--
ALTER TABLE `koszyk`
  ADD PRIMARY KEY (`id_uzytkownika`,`id_produktu`,`id_rozmiaru`),
  ADD KEY `id_produktu` (`id_produktu`),
  ADD KEY `id_rozmiaru` (`id_rozmiaru`);

--
-- Indeksy dla tabeli `materialy`
--
ALTER TABLE `materialy`
  ADD PRIMARY KEY (`id_materialu`);

--
-- Indeksy dla tabeli `plec`
--
ALTER TABLE `plec`
  ADD PRIMARY KEY (`id_pleci`),
  ADD UNIQUE KEY `nazwa` (`nazwa`);

--
-- Indeksy dla tabeli `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD PRIMARY KEY (`id_pracownika`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`);

--
-- Indeksy dla tabeli `produkty`
--
ALTER TABLE `produkty`
  ADD PRIMARY KEY (`id_produktu`),
  ADD KEY `id_kategorii` (`id_kategorii`),
  ADD KEY `id_materialu` (`id_materialu`);

--
-- Indeksy dla tabeli `produkty_rozmiary`
--
ALTER TABLE `produkty_rozmiary`
  ADD PRIMARY KEY (`id_p_r`),
  ADD KEY `id_produktu` (`id_produktu`),
  ADD KEY `id_rozmiaru` (`id_rozmiaru`);

--
-- Indeksy dla tabeli `rozmiary`
--
ALTER TABLE `rozmiary`
  ADD PRIMARY KEY (`id_rozmiaru`);

--
-- Indeksy dla tabeli `rozmiary_typy`
--
ALTER TABLE `rozmiary_typy`
  ADD PRIMARY KEY (`id_typu`),
  ADD UNIQUE KEY `nazwa` (`nazwa`);

--
-- Indeksy dla tabeli `ulubione`
--
ALTER TABLE `ulubione`
  ADD PRIMARY KEY (`id_uzytkownika`,`id_produktu`),
  ADD KEY `id_produktu` (`id_produktu`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id_uzytkownika`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD PRIMARY KEY (`id_zamowienia`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`),
  ADD KEY `id_pracownika` (`id_pracownika`);

--
-- Indeksy dla tabeli `zamowienia_dane_goscie`
--
ALTER TABLE `zamowienia_dane_goscie`
  ADD PRIMARY KEY (`id_zamowienia`);

--
-- Indeksy dla tabeli `zamowienia_produkty`
--
ALTER TABLE `zamowienia_produkty`
  ADD PRIMARY KEY (`id_zamowienia`,`id_produktu`,`id_rozmiaru`);

--
-- Indeksy dla tabeli `zdjecia_produktow`
--
ALTER TABLE `zdjecia_produktow`
  ADD PRIMARY KEY (`id_zdjecia`),
  ADD KEY `id_produktu` (`id_produktu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atrybuty_produktow`
--
ALTER TABLE `atrybuty_produktow`
  MODIFY `id_atrybutu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `id_kategorii` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kody_rabatowe`
--
ALTER TABLE `kody_rabatowe`
  MODIFY `id_kodu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `materialy`
--
ALTER TABLE `materialy`
  MODIFY `id_materialu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `plec`
--
ALTER TABLE `plec`
  MODIFY `id_pleci` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pracownicy`
--
ALTER TABLE `pracownicy`
  MODIFY `id_pracownika` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `produkty`
--
ALTER TABLE `produkty`
  MODIFY `id_produktu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `produkty_rozmiary`
--
ALTER TABLE `produkty_rozmiary`
  MODIFY `id_p_r` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `rozmiary`
--
ALTER TABLE `rozmiary`
  MODIFY `id_rozmiaru` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `rozmiary_typy`
--
ALTER TABLE `rozmiary_typy`
  MODIFY `id_typu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id_uzytkownika` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `zamowienia`
--
ALTER TABLE `zamowienia`
  MODIFY `id_zamowienia` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `zdjecia_produktow`
--
ALTER TABLE `zdjecia_produktow`
  MODIFY `id_zdjecia` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `atrybuty_produktow`
--
ALTER TABLE `atrybuty_produktow`
  ADD CONSTRAINT `atrybuty_produktow_ibfk_1` FOREIGN KEY (`id_produktu`) REFERENCES `produkty` (`id_produktu`) ON DELETE CASCADE;

--
-- Constraints for table `koszyk`
--
ALTER TABLE `koszyk`
  ADD CONSTRAINT `koszyk_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`) ON DELETE CASCADE,
  ADD CONSTRAINT `koszyk_ibfk_2` FOREIGN KEY (`id_produktu`) REFERENCES `produkty` (`id_produktu`) ON DELETE CASCADE,
  ADD CONSTRAINT `koszyk_ibfk_3` FOREIGN KEY (`id_rozmiaru`) REFERENCES `rozmiary` (`id_rozmiaru`) ON DELETE CASCADE;

--
-- Constraints for table `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD CONSTRAINT `pracownicy_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`) ON DELETE CASCADE;

--
-- Constraints for table `produkty`
--
ALTER TABLE `produkty`
  ADD CONSTRAINT `produkty_ibfk_1` FOREIGN KEY (`id_kategorii`) REFERENCES `kategorie` (`id_kategorii`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produkty_ibfk_2` FOREIGN KEY (`id_materialu`) REFERENCES `materialy` (`id_materialu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `produkty_rozmiary`
--
ALTER TABLE `produkty_rozmiary`
  ADD CONSTRAINT `produkty_rozmiary_ibfk_1` FOREIGN KEY (`id_produktu`) REFERENCES `produkty` (`id_produktu`) ON DELETE CASCADE,
  ADD CONSTRAINT `produkty_rozmiary_ibfk_2` FOREIGN KEY (`id_rozmiaru`) REFERENCES `rozmiary` (`id_rozmiaru`) ON DELETE CASCADE;

--
-- Constraints for table `ulubione`
--
ALTER TABLE `ulubione`
  ADD CONSTRAINT `ulubione_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`) ON DELETE CASCADE,
  ADD CONSTRAINT `ulubione_ibfk_2` FOREIGN KEY (`id_produktu`) REFERENCES `produkty` (`id_produktu`) ON DELETE CASCADE;

--
-- Constraints for table `zamowienia`
--
ALTER TABLE `zamowienia`
  ADD CONSTRAINT `zamowienia_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`) ON DELETE CASCADE,
  ADD CONSTRAINT `zamowienia_ibfk_2` FOREIGN KEY (`id_pracownika`) REFERENCES `pracownicy` (`id_pracownika`) ON DELETE SET NULL;

--
-- Constraints for table `zamowienia_dane_goscie`
--
ALTER TABLE `zamowienia_dane_goscie`
  ADD CONSTRAINT `zamowienia_dane_goscie_ibfk_1` FOREIGN KEY (`id_zamowienia`) REFERENCES `zamowienia` (`id_zamowienia`) ON DELETE CASCADE;

--
-- Constraints for table `zamowienia_produkty`
--
ALTER TABLE `zamowienia_produkty`
  ADD CONSTRAINT `zamowienia_produkty_ibfk_1` FOREIGN KEY (`id_zamowienia`) REFERENCES `zamowienia` (`id_zamowienia`) ON DELETE CASCADE;

--
-- Constraints for table `zdjecia_produktow`
--
ALTER TABLE `zdjecia_produktow`
  ADD CONSTRAINT `zdjecia_produktow_ibfk_1` FOREIGN KEY (`id_produktu`) REFERENCES `produkty` (`id_produktu`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
