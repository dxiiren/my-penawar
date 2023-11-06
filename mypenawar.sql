-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Jul 24, 2022 at 04:29 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mypenawar`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `bookingID` int(5) NOT NULL,
  `bookingDate` date DEFAULT NULL,
  `bookingTime` varchar(10) DEFAULT NULL,
  `bookingDesc` varchar(250) DEFAULT NULL,
  `bookingStatus` varchar(15) DEFAULT NULL,
  `empID` varchar(10) DEFAULT NULL,
  `patientIC` varchar(12) DEFAULT NULL,
  `serviceID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`bookingID`, `bookingDate`, `bookingTime`, `bookingDesc`, `bookingStatus`, `empID`, `patientIC`, `serviceID`) VALUES
(1, '2022-01-11', '10:00 A.M.', 'Had facial pain and runny nose', 'Completed', NULL, '730727031099', 'selectS'),
(2, '2021-09-12', '03:15 P.M.', 'Do medical check-up', 'Approved', 'D1946', '790612160096', 'SV003'),
(3, '2021-10-24', '12:30 P.M.', 'Weekly dressing', 'Completed', 'D1337', '670411011237', 'SV009'),
(4, '2021-12-05', '02:45 P.M.', 'Suppress inflammation in airways', 'Completed', 'D1337', '580901100691', 'SV008'),
(5, '2022-01-25', '11:30 A.M.', 'Has extreme headache', 'Completed', 'D1036', '660416082545', 'SV007');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `empID` varchar(10) NOT NULL,
  `empName` varchar(50) DEFAULT NULL,
  `empBirthDate` date DEFAULT NULL,
  `empPassword` varchar(15) DEFAULT NULL,
  `empPhoneNum` varchar(12) DEFAULT NULL,
  `empEmail` varchar(50) DEFAULT NULL,
  `empAddress` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`empID`, `empName`, `empBirthDate`, `empPassword`, `empPhoneNum`, `empEmail`, `empAddress`) VALUES
('D1036', 'Anis Idyana binti Zaki', '1988-04-26', 'AN@1036', '01131388254', 'anisidyana@gmail.com', 'No.3, Jalan Kenanga 9/2, Bandar Semenyih, 43500, Semenyih, Selangor'),
('D1337', 'Muhammad Azwan bin Adha', '1988-02-06', 'AZ@1337', '01160708004', 'muhdazwan@gmail.com', 'No.38, Jalan Sahabat 10/1b, Bandar Semenyih, 43500, Semenyih, Selangor'),
('D1422', 'Priscilla Rose Moses', '1977-09-21', 'PR@1422', '01161771308', 'priscilla@gmail.com', 'No.2, Jalan Bistari 2/1b, Taman Makhota, 43500, Semenyih, Selangor'),
('D1795', 'Kayalvili a/p Vinavaran', '1974-03-14', 'KA@1795', '01162306269', 'kayalvili@gmail.com', 'No. 23, Jalan TP 7/7, Taman Perindustrian Semenyih, 43500, Semenyih, Selangor'),
('D1946', 'Desmond Soo', '1989-11-18', 'DE@1946', '01112175833', 'desmond@gmail.com', 'No.38, Jalan Tun Teja 35/19A, Alam Impian, 43500, Semenyih, Selangor'),
('N1124', 'Ng Chee Ah', '1988-06-12', 'NG@1124', '0179868015', 'cheeah@gmail.com', 'No.14B Jalan Perai 2/4 Taman Perai, 43500, Semenyih, Selangor'),
('N1272', 'Kang Suat Ling', '1989-08-12', 'KA@1272', '01127236209', 'suatling@gmail.com', 'No.51G Jalan USJ 10/1 Taipan, 43500, Semenyih, Selangor'),
('N1328', 'Shamini a/p Anbalagan', '1979-09-21', 'SH@1328', '0187726392', 'shamini@gmail.com', 'No.1, Jalan U1/84a, Taman Perindustrian Semenyih, 43500, Semenyih, Selangor'),
('N1365', 'Suvaletchmi a/p Sivakumar', '1985-05-27', 'SU@1365', '0134425786', 'suvaletchmi@gmail.com', 'Lot 53 Lorong Jelawat 2 Kg Baru Semenyih, 43500, Semenyih, Selangor'),
('N1526', 'Aisyah Farhana binti Hashim', '1980-01-21', 'AI@1526', '0129186296', 'aisyahfarhana@gmail.com', 'No.17 Jalan U 10/1CA, Bandar Semenyih, 43500, Semenyih, Selangor'),
('N1775', 'Nur Izah Izzati binti Ghaffar', '1986-03-23', 'NU@1775', '0123420904', 'izahizzati@gmail.com', 'No.35, Jalan U 10/D, Taman Makhota, 43500, Semenyih, Selangor'),
('N2756', 'Leong Wei Qin', '1985-08-26', 'LE@2756', '01132378564', 'weiqin@gmail.com', 'No.27, Jalan BU 3/8, Bandar Utama, 47800, Petaling Jaya, Selangor'),
('S1006', 'Diviya a/p Indran', '1982-09-29', 'DI@1006', '01158543309', 'diviya@gmail.com', 'No.19, Jalan Kenanga, Taman Paling Jaya, 43500, Semenyih, Selangor'),
('S1565', 'Syawal bin Lukman', '1978-04-15', 'SY@1565', '0185379076', 'syawal@gmail.com', 'No.2, Jalan Kemuning, Taman Paling Jaya, 43500, Semenyih, Selangor');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patientIC` varchar(12) NOT NULL,
  `patientName` varchar(60) DEFAULT NULL,
  `patientUser` varchar(15) DEFAULT NULL,
  `patientBirthDate` date DEFAULT NULL,
  `patientPassword` varchar(15) DEFAULT NULL,
  `patientPhoneNum` varchar(12) DEFAULT NULL,
  `patientEmail` varchar(50) DEFAULT NULL,
  `patientAddress` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patientIC`, `patientName`, `patientUser`, `patientBirthDate`, `patientPassword`, `patientPhoneNum`, `patientEmail`, `patientAddress`) VALUES
('0206', 'NINA', NULL, NULL, NULL, NULL, NULL, NULL),
('580901100691', 'Vimalan a/l Selvarajoo', 'vimalancool', '1958-09-01', 'vmalan58', '0196752861', 'vimalanselvarajoo58@gmail.com', 'No.5, Jalan Jugra Batu 1, 42700, Banting, Selangor'),
('660416082545', 'Tan Wei Khang', 'weikhang', '1966-04-16', 'tweikhang2545', '0127893720', 'tanweikhangg@gmail.com', 'No.8, Jalan Kesuma 1/11, Bandar Tasik Kesuma, 43700, Beranang, Selangor'),
('670411011237', 'Muhammad Hamzah bin Zaid', 'hamzahzaid', '1967-04-22', 'hamzaid0422', '0127172755', 'muhdhamzah0422@gmail.com', 'No.2, Jalan 5/B Kawasan Industri Sungai Chua, 43000, Kajang, Selangor'),
('730727031099', 'Angela Marie Sulim', 'angela', '1973-07-27', 'marieAngela2707', '01146539981', 'angelamarie.sulim@gmail.com', 'No.21, Jalan Semenyih Indah 13, Taman Semenyih Indah, 43500, Semenyih, Selangor'),
('790612160096', 'Kirtinia a/p Mogan', 'kirtyy79', '1979-06-12', 'kt0011', '0106269051', 'kirtiniaMogan79@gmail.com', 'No.33, Jalan 4/9, Seksyen 4, Tambahan Bandar Baru Bangi, 43500, Semenyih, Selangor');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `paymentID` int(5) NOT NULL,
  `paymentAmount` decimal(5,2) DEFAULT NULL,
  `paymentMethod` varchar(15) DEFAULT NULL,
  `paymentDate` date DEFAULT NULL,
  `bookingID` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`paymentID`, `paymentAmount`, `paymentMethod`, `paymentDate`, `bookingID`) VALUES
(1, '20.00', 'Cash', '2021-12-11', 4),
(2, '48.00', 'Credit card', '2021-11-01', 3),
(3, '55.00', 'Cash', '2021-09-18', 2),
(4, '80.00', 'Cash', '2022-01-13', 1),
(5, '30.00', 'Debit card', '2022-01-25', 5);

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `serviceID` varchar(10) NOT NULL,
  `serviceName` varchar(30) DEFAULT NULL,
  `servicePrice` decimal(5,2) DEFAULT NULL,
  `serviceDesc` varchar(250) DEFAULT NULL,
  `empID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`serviceID`, `serviceName`, `servicePrice`, `serviceDesc`, `empID`) VALUES
('SV001', 'Medical checkup', '55.00', 'Obtain patient’s height, weight, blood pressure, urine test and blood test', 'D1036'),
('SV002', 'Laboratory testing', '102.00', 'Conduct urine test, drug test or  blood analysis', 'D1036'),
('SV003', 'Screening and treatment', '80.00', 'Detect a probable health condition or disease in someone who has no signs or symptoms', 'D1946'),
('SV004', 'Prenatal Check-up', '25.00', 'Blood testing, ultrasound, and prenatal cell-free DNA screening', 'D1795'),
('SV005', 'Postnatal Check-up', '150.00', 'Perform a test on the baby after delivery that allows doctors to diagnose diseases', 'D1795'),
('SV006', 'Minor surgeries', '102.00', 'Study patient’s medical history, including family history, and may do an ultrasound or indirect laryngoscopy to make better treatment decisions', 'D1422'),
('SV007', 'Care for minor symptoms', '30.00', 'Require frequent check-ups and appointments', 'D1036'),
('SV008', 'Treatment for common illness', '20.00', 'Prescribe medicine or make treatment recommendations to patients based on their illness', 'D1337'),
('SV009', 'Treatment for minor injuries', '48.00', 'Basic first aid treatments such as wound cleansing, wound dressings, rest, ice treatment, compression, and elevation', 'D1337'),
('SV010', 'Vaccination', '39.00', 'The doctor will analyse the patient’s medical history and family history to determine whether or not the patient can be vaccinated', 'D1946');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`bookingID`),
  ADD KEY `fk_booking1` (`empID`),
  ADD KEY `fk_booking2` (`patientIC`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`empID`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patientIC`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`paymentID`),
  ADD KEY `fk_payment1` (`bookingID`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`serviceID`),
  ADD KEY `fk_service` (`empID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `bookingID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `paymentID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking1` FOREIGN KEY (`empID`) REFERENCES `employee` (`empID`),
  ADD CONSTRAINT `fk_booking2` FOREIGN KEY (`patientIC`) REFERENCES `patient` (`patientIC`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment1` FOREIGN KEY (`bookingID`) REFERENCES `booking` (`bookingID`);

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `fk_service` FOREIGN KEY (`empID`) REFERENCES `employee` (`empID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
