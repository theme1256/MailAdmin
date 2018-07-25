-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 25, 2018 at 02:11 PM
-- Server version: 5.7.22-0ubuntu0.16.04.1-log
-- PHP Version: 7.0.30-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vmail`
--

-- --------------------------------------------------------

--
-- Table structure for table `ma_access`
--

CREATE TABLE `ma_access` (
  `domain` varchar(255) NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ma_content`
--

CREATE TABLE `ma_content` (
  `contentID` int(11) NOT NULL,
  `about` varchar(256) NOT NULL,
  `da` text NOT NULL,
  `en` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ma_content`
--

INSERT INTO `ma_content` (`contentID`, `about`, `da`, `en`) VALUES
(1, 'menu, domains', 'Domæner', 'Domains'),
(2, 'menu, edit user', 'Ret bruger', 'Edit user'),
(3, 'menu, log out', 'Log ud', 'Log out'),
(4, 'header, login', 'Du har måske ikke adgang her, log lige ind', 'You might not have access, log in'),
(5, 'label, username', 'Brugernavn', 'Username'),
(6, 'label, password', 'Kodeord', 'Password'),
(7, 'error, 404', 'Fejl under kommunikation med server, fejl: 404, kunne ikke finde filen på serveren.', 'Error during communication with server, Error: 404, file not found.'),
(8, 'error, 500', 'Fejl under kommunikation med server, fejl: 500, der skete en fejl på serveren.', 'Error during communication with server, Error: 500, an error happened on the server.'),
(9, 'error, general', 'Fejl under kommunikation med server, fejl: ', 'Error during communication with server, error: '),
(10, 'header, domains', 'Domæner du er admin over', 'Domains you control'),
(11, 'reply-error, empty input', 'Du skal udfylde brugernavn og kodeord.', 'You have to provide username and password.'),
(12, 'reply-error, not implemented', 'Funktion ikke implementeret endnu', 'Action not implemented yet'),
(13, 'reply-error, db-error', 'Fejl under kommunikation med databasen.', 'Error during communication with the datbase.'),
(14, 'reply-error, wrong creds', 'Brugernavn eller kodeord er forkert.', 'Username or password is wrong.'),
(15, 'reply, login success', 'Login lykkedes, reloader.', 'Login successful, reloading.'),
(16, 'header, edit your user', 'Ret din bruger', 'Edit your user'),
(17, 'button, update user', 'Opdater bruger', 'Update user'),
(18, 'reply, updated user', 'Opdaterede informationer, reloader.', 'Update successful, reloading.'),
(19, 'reply-error, not recognized', 'Handling blev ikke genkendt', 'Action not recognized'),
(20, 'content, about list of domains', 'Klik på en af dem for at blive vist informationer om mails på det domæne.', 'Click on one to edit informations on emails under that domain.'),
(21, 'header, domain', 'Mails til domænet:', 'Mails under the domain: '),
(22, 'button, create alias/list', 'Opret ny liste/alias', 'Create new list/alias'),
(23, 'header, new list/alias', 'Opret ny liste/alias under', 'Create new list/alias under'),
(24, 'header, edit list/alias', 'Ret listen/aliaset under', 'Edit the list/alias under'),
(25, 'content, mailtype - local', 'Lokal email', 'Local email'),
(26, 'content, mailtype - alias', 'Email alias', 'Email alias'),
(27, 'content, mailtype - list', 'Maillinglist', 'Maillist'),
(28, 'label, receiving email', 'Email, uden domæne', 'Email, without the domain'),
(29, 'label, members of list', 'Emails på listen', 'Emails on this list'),
(30, 'label, about empty inputs', 'Tomme felter bliver slettet', 'Empty fields will be removed'),
(31, 'button, create list', 'Opret liste', 'Create list'),
(32, 'button, another field', 'Endnu et felt', 'Another field'),
(33, 'link, back to domain', 'Tilbage til domæne', 'Back to domain'),
(34, 'link, back to domains', 'Tilbage til domæner', 'Back to domains'),
(35, 'reply-error, missing address and/or receiver', 'Du skal udfylde adresse og give mindst en modtager', 'You have to enter address and at least one receiver'),
(36, 'reply-error, not an email', 'Det var ikke en email', 'That wasn\'t an email'),
(37, 'reply, alias created', 'Alias blev oprettet', 'Alias created'),
(38, 'reply, list created', 'Liste blev oprettet', 'List created'),
(39, 'button, update list', 'Ret liste', 'Edit list'),
(40, 'button, delete list', 'Slet liste', 'Delete list'),
(41, 'reply, alias updated', 'Alias blev rettet', 'Alias modified'),
(42, 'reply, list updated', 'Liste blev rettet', 'List modified'),
(43, 'reply-error, list exists', 'Den adresse finde allerede', 'That address already exists'),
(44, 'reply, alias/list deleted', 'Alias/liste blev slettet', 'Alias/list deleted'),
(45, 'reply-error, empty field', 'Et eller flere felter er tomme', 'One or more fields are empty'),
(46, 'reply-error, missing userID', 'ID feltet er tomt', 'The ID is missing'),
(47, 'reply-error, missing permissions', 'Du har ikke rettighed til at rette her', 'You don\'t have access here'),
(48, 'reply-error, no input', 'Der er ikke angivet hvilke email(s) der skal sendes videre til', 'You didn\'t pass any emails that should be forwarded to'),
(49, 'input, disabled because of iRedMail', 'Dette felt kan ikke rettes fordi det er den lokale mail', 'You can\'t edit this field because it\'s the local mail-user'),
(50, 'link, add forwardings', 'Tilføj forwarding', 'Add forwarding'),
(51, 'link, edit forwardings', 'Ret forwarding', 'Edit forwarding'),
(52, 'header, edit list/alias', 'Ret forwardings under', 'Edit forwardings under'),
(53, 'reply, removed forwardings', 'Fjernede alle forwardings til mail', 'Removed all forwardings'),
(54, 'reply, updated forwardings', 'Opdaterede forwardings til mail', 'Updated forwardings'),
(55, 'link, superuser - add end edit users', 'Ret og opret brugere', 'Edit and add users'),
(56, 'link, superuser - add a user', 'Opret ny bruger', 'Add new user'),
(57, 'link, superuser - back to users', 'Tilbage til brugeroversigt', 'Back to users'),
(58, 'label, superuser - domains a user admins', 'Domæner', 'Domains'),
(59, 'help, superuser - domains a user admins', 'Tomme felter bliver slettet.', 'Empty inputs will be deleted.'),
(60, 'input, superuser - default', 'Vælg en', 'Select one'),
(61, 'button, superuser - add domain to user', 'Endnu et domæne', 'Add another domain'),
(62, 'button, superuser - update user', 'Ret bruger', 'Update user'),
(63, 'button, superuser - delete user', 'Slet bruger', 'Delete user'),
(64, 'button, superuser - create user', 'Opret bruger', 'Create user'),
(65, 'header, superuser - create user', 'Opret ny bruger', 'Add a user'),
(66, 'header, superuser - edit user', 'Ret bruger', 'Edit user'),
(67, 'header, superuser - user list', 'Brugere i systemet', 'Registered users'),
(68, 'reply, superuser - created user', 'Oprettede bruger korrekt', 'Created user correctly'),
(69, 'reply-error, superuser - no domains', 'Der var ikke nogen domæner', 'You didn\'t specify any domains'),
(70, 'reply, superuser - update user', 'Opdaterede bruger korrekt', 'Updated user correctly'),
(71, 'reply, superuser - delete user', 'Slettede bruger korrekt', 'Deleted user correctly');

-- --------------------------------------------------------

--
-- Table structure for table `ma_login`
--

CREATE TABLE `ma_login` (
  `userID` int(11) NOT NULL,
  `user` varchar(128) NOT NULL,
  `pass` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ma_access`
--
ALTER TABLE `ma_access`
  ADD PRIMARY KEY (`domain`,`userID`),
  ADD KEY `uID` (`userID`),
  ADD KEY `d` (`domain`) USING BTREE;

--
-- Indexes for table `ma_content`
--
ALTER TABLE `ma_content`
  ADD PRIMARY KEY (`contentID`);

--
-- Indexes for table `ma_login`
--
ALTER TABLE `ma_login`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `user` (`user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ma_content`
--
ALTER TABLE `ma_content`
  MODIFY `contentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `ma_login`
--
ALTER TABLE `ma_login`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ma_access`
--
ALTER TABLE `ma_access`
  ADD CONSTRAINT `ma_access_ibfk_1` FOREIGN KEY (`domain`) REFERENCES `domain` (`domain`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ma_access_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `ma_login` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
