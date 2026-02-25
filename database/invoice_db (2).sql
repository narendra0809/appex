-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2026 at 08:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `invoice_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `agreements`
--

CREATE TABLE `agreements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `agreement_no` varchar(255) NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `word_path` varchar(255) DEFAULT NULL,
  `agreement_sent_at` timestamp NULL DEFAULT NULL,
  `invoice_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `agreements`
--

INSERT INTO `agreements` (`id`, `client_id`, `agreement_no`, `pdf_path`, `word_path`, `agreement_sent_at`, `invoice_sent_at`, `created_at`, `updated_at`) VALUES
(2, 33, 'AGR-20260223-0033', NULL, NULL, '2026-02-23 04:23:19', NULL, '2026-02-23 03:33:08', '2026-02-23 04:23:19');

-- --------------------------------------------------------

--
-- Table structure for table `bulk_kyc_batches`
--

CREATE TABLE `bulk_kyc_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_name` varchar(255) DEFAULT NULL,
  `original_filename` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `total_records` int(11) NOT NULL DEFAULT 0,
  `processed_records` int(11) NOT NULL DEFAULT 0,
  `success_count` int(11) NOT NULL DEFAULT 0,
  `failed_count` int(11) NOT NULL DEFAULT 0,
  `error_log` text DEFAULT NULL,
  `result_zip_path` varchar(255) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bulk_kyc_batches`
--

INSERT INTO `bulk_kyc_batches` (`id`, `batch_name`, `original_filename`, `status`, `total_records`, `processed_records`, `success_count`, `failed_count`, `error_log`, `result_zip_path`, `started_at`, `completed_at`, `user_id`, `created_at`, `updated_at`) VALUES
(7, 'FIRST', 'Pancards.xlsx', 'completed', 2, 2, 2, 0, NULL, 'bulk_kyc_zips/bulk_kyc_batch_7_20260223090442.zip', '2026-02-23 03:34:36', '2026-02-23 03:34:42', 1, '2026-02-23 03:34:24', '2026-02-23 03:34:42');

-- --------------------------------------------------------

--
-- Table structure for table `bulk_kyc_records`
--

CREATE TABLE `bulk_kyc_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED NOT NULL,
  `pan` varchar(255) NOT NULL,
  `dob` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `kyc_record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bulk_kyc_records`
--

INSERT INTO `bulk_kyc_records` (`id`, `batch_id`, `pan`, `dob`, `status`, `error_message`, `kyc_record_id`, `document_path`, `created_at`, `updated_at`) VALUES
(11, 7, 'BLSPJ0470P', '21-05-1997', 'success', NULL, 19, 'kyc_documents/BLSPJ0470P_KYC_DOCUMENTS.zip', '2026-02-23 03:34:29', '2026-02-23 03:34:39'),
(12, 7, 'DBFPP6463M', '08-09-1997', 'success', NULL, 18, 'kyc_documents/DBFPP6463M_KYC_DOCUMENTS.zip', '2026-02-23 03:34:29', '2026-02-23 03:34:42');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `pan_card` varchar(255) DEFAULT NULL,
  `aadhaar_card` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `gross_amount` decimal(10,2) DEFAULT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL,
  `amount_type` varchar(255) DEFAULT NULL,
  `segment` varchar(255) DEFAULT NULL,
  `assigned_to` varchar(255) DEFAULT NULL,
  `plan` varchar(255) DEFAULT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `service_start` date DEFAULT NULL,
  `service_end` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `payment_date`, `client_name`, `mobile`, `email`, `father_name`, `pan_card`, `aadhaar_card`, `dob`, `city`, `state`, `gross_amount`, `net_amount`, `amount_type`, `segment`, `assigned_to`, `plan`, `bank`, `remark`, `service_start`, `service_end`, `created_at`, `updated_at`) VALUES
(33, '2026-02-23', 'Narendra patidar', '9589572990', 'np4375@gmail.com', 'test', 'DBFPP6463M', '220481582620', '1997-08-09', 'indore', 'MP', 11800.00, 10000.00, 'New Enrollment', 'Systematic Trading Plan - STP', 'Narendra', 'Basic', 'Canara', NULL, '2026-02-23', '2026-03-24', '2026-02-23 03:32:51', '2026-02-23 03:32:51');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(255) NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `word_path` varchar(255) DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `client_id`, `invoice_no`, `pdf_path`, `word_path`, `sent_at`, `created_at`, `updated_at`) VALUES
(2, 33, 'INV-20260223-0033', NULL, NULL, '2026-02-23 04:34:44', '2026-02-23 03:33:11', '2026-02-23 04:34:44');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_records`
--

CREATE TABLE `kyc_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pan` varchar(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `dob` varchar(255) NOT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `kyc_status` varchar(255) DEFAULT NULL,
  `kyc_json` text DEFAULT NULL,
  `api_raw_response` text DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `zip_path` varchar(255) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kyc_records`
--

INSERT INTO `kyc_records` (`id`, `pan`, `name`, `dob`, `father_name`, `address`, `pincode`, `state`, `city`, `status`, `kyc_status`, `kyc_json`, `api_raw_response`, `verified_at`, `document_path`, `zip_path`, `ref_no`, `notes`, `created_at`, `updated_at`) VALUES
(18, 'DBFPP6463M', 'NARENDRA PATIDAR', '08-09-1997', 'BHAGIRATH', 'S O BHAGIRATH MAKAN NO 154 LAXMINARAYAN MANDIR KE PASS GRAM DHAKANI TEH MANASA DHAKANI MANASA NALK HEDA NALKHEDA 458113', '458113', '023', 'NEEMUCH', 'pending', NULL, '\"{\\\"resdtls\\\":\\\"{\\\\\\\"KYC_DATA\\\\\\\":{\\\\\\\"APP_POS_CODE\\\\\\\":\\\\\\\"2500017240\\\\\\\",\\\\\\\"APP_TYPE\\\\\\\":\\\\\\\"I\\\\\\\",\\\\\\\"APP_NO\\\\\\\":\\\\\\\"54853232\\\\\\\",\\\\\\\"APP_DATE\\\\\\\":\\\\\\\"08\\\\\\/01\\\\\\/2026\\\\\\\",\\\\\\\"APP_PAN_NO\\\\\\\":\\\\\\\"DBFPP6463M\\\\\\\",\\\\\\\"APP_PAN_COPY\\\\\\\":\\\\\\\"Y\\\\\\\",\\\\\\\"APP_EXMT\\\\\\\":\\\\\\\"N\\\\\\\",\\\\\\\"APP_EXMT_CAT\\\\\\\":\\\\\\\"00\\\\\\\",\\\\\\\"APP_EXMT_ID_PROOF\\\\\\\":\\\\\\\"02\\\\\\\",\\\\\\\"APP_IPV_FLAG\\\\\\\":\\\\\\\"E\\\\\\\",\\\\\\\"APP_IPV_DATE\\\\\\\":\\\\\\\"08\\\\\\/01\\\\\\/2026\\\\\\\",\\\\\\\"APP_GEN\\\\\\\":\\\\\\\"M\\\\\\\",\\\\\\\"APP_NAME\\\\\\\":\\\\\\\"NARENDRA PATIDAR\\\\\\\",\\\\\\\"APP_F_NAME\\\\\\\":\\\\\\\"BHAGIRATH\\\\\\\",\\\\\\\"APP_REGNO\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_DOB_DT\\\\\\\":\\\\\\\"08\\\\\\/09\\\\\\/1997\\\\\\\",\\\\\\\"APP_COMMENCE_DT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_NATIONALITY\\\\\\\":\\\\\\\"01\\\\\\\",\\\\\\\"APP_OTH_NATIONALITY\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_COMP_STATUS\\\\\\\":\\\\\\\"00\\\\\\\",\\\\\\\"APP_OTH_COMP_STATUS\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_RES_STATUS\\\\\\\":\\\\\\\"R\\\\\\\",\\\\\\\"APP_RES_STATUS_PROOF\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_UID_NO\\\\\\\":\\\\\\\"N\\\\\\\",\\\\\\\"APP_COR_ADD1\\\\\\\":\\\\\\\"S O BHAGIRATH MAKAN NO 154 LAXMINARAYAN MANDIR KE\\\\\\\",\\\\\\\"APP_COR_ADD2\\\\\\\":\\\\\\\"PASS GRAM DHAKANI TEH MANASA DHAKANI MANASA NALK\\\\\\\",\\\\\\\"APP_COR_ADD3\\\\\\\":\\\\\\\"HEDA NALKHEDA 458113\\\\\\\",\\\\\\\"APP_COR_CITY\\\\\\\":\\\\\\\"NEEMUCH\\\\\\\",\\\\\\\"APP_COR_PINCD\\\\\\\":\\\\\\\"458113\\\\\\\",\\\\\\\"APP_COR_STATE\\\\\\\":\\\\\\\"023\\\\\\\",\\\\\\\"APP_COR_CTRY\\\\\\\":\\\\\\\"101\\\\\\\",\\\\\\\"APP_OFF_NO\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_RES_NO\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_MOB_NO\\\\\\\":\\\\\\\"7999426767\\\\\\\",\\\\\\\"APP_FAX_NO\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_EMAIL\\\\\\\":\\\\\\\"NP4375@GMAIL.COM\\\\\\\",\\\\\\\"APP_COR_ADD_PROOF\\\\\\\":\\\\\\\"31\\\\\\\",\\\\\\\"APP_COR_ADD_REF\\\\\\\":\\\\\\\"2620\\\\\\\",\\\\\\\"APP_COR_ADD_DT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_PER_ADD1\\\\\\\":\\\\\\\"S O BHAGIRATH MAKAN NO 154 LAXMINARAYAN MANDIR KE\\\\\\\",\\\\\\\"APP_PER_ADD2\\\\\\\":\\\\\\\"PASS GRAM DHAKANI TEH MANASA DHAKANI MANASA NALK\\\\\\\",\\\\\\\"APP_PER_ADD3\\\\\\\":\\\\\\\"HEDA NALKHEDA 458113\\\\\\\",\\\\\\\"APP_PER_CITY\\\\\\\":\\\\\\\"NEEMUCH\\\\\\\",\\\\\\\"APP_PER_PINCD\\\\\\\":\\\\\\\"458113\\\\\\\",\\\\\\\"APP_PER_STATE\\\\\\\":\\\\\\\"023\\\\\\\",\\\\\\\"APP_PER_CTRY\\\\\\\":\\\\\\\"101\\\\\\\",\\\\\\\"APP_PER_ADD_PROOF\\\\\\\":\\\\\\\"31\\\\\\\",\\\\\\\"APP_PER_ADD_REF\\\\\\\":\\\\\\\"2620\\\\\\\",\\\\\\\"APP_PER_ADD_DT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_INCOME\\\\\\\":\\\\\\\"00\\\\\\\",\\\\\\\"APP_OCC\\\\\\\":\\\\\\\"00\\\\\\\",\\\\\\\"APP_OTH_OCC\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_POL_CONN\\\\\\\":\\\\\\\"NA\\\\\\\",\\\\\\\"APP_DOC_PROOF\\\\\\\":\\\\\\\"E\\\\\\\",\\\\\\\"APP_INTERNAL_REF\\\\\\\":\\\\\\\"20260225230757-138CV\\\\\\\",\\\\\\\"APP_BRANCH_CODE\\\\\\\":\\\\\\\"HEADOFFICE\\\\\\\",\\\\\\\"APP_MAR_STATUS\\\\\\\":\\\\\\\"02\\\\\\\",\\\\\\\"APP_NETWRTH\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_NETWORTH_DT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_INCORP_PLC\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_OTHERINFO\\\\\\\":\\\\\\\"6 FIELD UPDATE\\\\\\\",\\\\\\\"APP_ACC_OPENDT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_ACC_ACTIVEDT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_ACC_UPDTDT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_FILLER1\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_FILLER2\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_FILLER3\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_STATUS\\\\\\\":\\\\\\\"07\\\\\\\",\\\\\\\"APP_STATUSDT\\\\\\\":\\\\\\\"31\\\\\\/01\\\\\\/2026 16:41:01\\\\\\\",\\\\\\\"APP_ERROR_DESC\\\\\\\":\\\\\\\"ERR-00000\\\\\\\",\\\\\\\"APP_DUMP_TYPE\\\\\\\":\\\\\\\"S\\\\\\\",\\\\\\\"APP_DNLDDT\\\\\\\":\\\\\\\"25\\\\\\/02\\\\\\/2026 23:07:57\\\\\\\",\\\\\\\"APP_REMARKS\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_KYC_MODE\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"APP_UID_TOKEN\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_VER_NO\\\\\\\":\\\\\\\"V33\\\\\\\",\\\\\\\"APP_KRA_INFO\\\\\\\":\\\\\\\"CVLKRA\\\\\\\",\\\\\\\"APP_IOP_FLG\\\\\\\":\\\\\\\"I\\\\\\\",\\\\\\\"APP_FATCA_APPLICABLE_FLAG\\\\\\\":\\\\\\\"N\\\\\\\",\\\\\\\"APP_FATCA_BIRTH_PLACE\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_FATCA_BIRTH_COUNTRY\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_FATCA_COUNTRY_RES\\\\\\\":null,\\\\\\\"APP_FATCA_COUNTRY_CITYZENSHIP\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_FATCA_DATE_DECLARATION\\\\\\\":\\\\\\\"08\\\\\\/01\\\\\\/2026\\\\\\\",\\\\\\\"APP_SIGNATURE\\\\\\\":\\\\\\\"\\\\\\\"},\\\\\\\"APP_PAN_SUMM\\\\\\\":{\\\\\\\"APP_OTHKRA_CODE\\\\\\\":\\\\\\\"CVLKRA\\\\\\\",\\\\\\\"APP_OTHKRA_BATCH\\\\\\\":\\\\\\\"25022026230757\\\\\\\",\\\\\\\"APP_REQ_DATE\\\\\\\":\\\\\\\"25\\\\\\/02\\\\\\/2026\\\\\\\",\\\\\\\"APP_RESPONSE_DATE\\\\\\\":\\\\\\\"25\\\\\\/02\\\\\\/2026 23:07:57\\\\\\\",\\\\\\\"APP_TOTAL_REC\\\\\\\":\\\\\\\"1\\\\\\\"}}\\\",\\\"error_code\\\":\\\"\\\",\\\"error_message\\\":\\\"\\\",\\\"KYC_DATA\\\":{\\\"APP_POS_CODE\\\":\\\"2500017240\\\",\\\"APP_TYPE\\\":\\\"I\\\",\\\"APP_NO\\\":\\\"54853232\\\",\\\"APP_DATE\\\":\\\"08\\\\\\/01\\\\\\/2026\\\",\\\"APP_PAN_NO\\\":\\\"DBFPP6463M\\\",\\\"APP_PAN_COPY\\\":\\\"Y\\\",\\\"APP_EXMT\\\":\\\"N\\\",\\\"APP_EXMT_CAT\\\":\\\"00\\\",\\\"APP_EXMT_ID_PROOF\\\":\\\"02\\\",\\\"APP_IPV_FLAG\\\":\\\"E\\\",\\\"APP_IPV_DATE\\\":\\\"08\\\\\\/01\\\\\\/2026\\\",\\\"APP_GEN\\\":\\\"M\\\",\\\"APP_NAME\\\":\\\"NARENDRA PATIDAR\\\",\\\"APP_F_NAME\\\":\\\"BHAGIRATH\\\",\\\"APP_REGNO\\\":\\\"\\\",\\\"APP_DOB_DT\\\":\\\"08\\\\\\/09\\\\\\/1997\\\",\\\"APP_COMMENCE_DT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_NATIONALITY\\\":\\\"01\\\",\\\"APP_OTH_NATIONALITY\\\":\\\"\\\",\\\"APP_COMP_STATUS\\\":\\\"00\\\",\\\"APP_OTH_COMP_STATUS\\\":\\\"\\\",\\\"APP_RES_STATUS\\\":\\\"R\\\",\\\"APP_RES_STATUS_PROOF\\\":\\\"\\\",\\\"APP_UID_NO\\\":\\\"N\\\",\\\"APP_COR_ADD1\\\":\\\"S O BHAGIRATH MAKAN NO 154 LAXMINARAYAN MANDIR KE\\\",\\\"APP_COR_ADD2\\\":\\\"PASS GRAM DHAKANI TEH MANASA DHAKANI MANASA NALK\\\",\\\"APP_COR_ADD3\\\":\\\"HEDA NALKHEDA 458113\\\",\\\"APP_COR_CITY\\\":\\\"NEEMUCH\\\",\\\"APP_COR_PINCD\\\":\\\"458113\\\",\\\"APP_COR_STATE\\\":\\\"023\\\",\\\"APP_COR_CTRY\\\":\\\"101\\\",\\\"APP_OFF_NO\\\":\\\"\\\",\\\"APP_RES_NO\\\":\\\"\\\",\\\"APP_MOB_NO\\\":\\\"7999426767\\\",\\\"APP_FAX_NO\\\":\\\"\\\",\\\"APP_EMAIL\\\":\\\"NP4375@GMAIL.COM\\\",\\\"APP_COR_ADD_PROOF\\\":\\\"31\\\",\\\"APP_COR_ADD_REF\\\":\\\"2620\\\",\\\"APP_COR_ADD_DT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_PER_ADD1\\\":\\\"S O BHAGIRATH MAKAN NO 154 LAXMINARAYAN MANDIR KE\\\",\\\"APP_PER_ADD2\\\":\\\"PASS GRAM DHAKANI TEH MANASA DHAKANI MANASA NALK\\\",\\\"APP_PER_ADD3\\\":\\\"HEDA NALKHEDA 458113\\\",\\\"APP_PER_CITY\\\":\\\"NEEMUCH\\\",\\\"APP_PER_PINCD\\\":\\\"458113\\\",\\\"APP_PER_STATE\\\":\\\"023\\\",\\\"APP_PER_CTRY\\\":\\\"101\\\",\\\"APP_PER_ADD_PROOF\\\":\\\"31\\\",\\\"APP_PER_ADD_REF\\\":\\\"2620\\\",\\\"APP_PER_ADD_DT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_INCOME\\\":\\\"00\\\",\\\"APP_OCC\\\":\\\"00\\\",\\\"APP_OTH_OCC\\\":\\\"\\\",\\\"APP_POL_CONN\\\":\\\"NA\\\",\\\"APP_DOC_PROOF\\\":\\\"E\\\",\\\"APP_INTERNAL_REF\\\":\\\"20260225230757-138CV\\\",\\\"APP_BRANCH_CODE\\\":\\\"HEADOFFICE\\\",\\\"APP_MAR_STATUS\\\":\\\"02\\\",\\\"APP_NETWRTH\\\":\\\"\\\",\\\"APP_NETWORTH_DT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_INCORP_PLC\\\":\\\"\\\",\\\"APP_OTHERINFO\\\":\\\"6 FIELD UPDATE\\\",\\\"APP_ACC_OPENDT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_ACC_ACTIVEDT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_ACC_UPDTDT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_FILLER1\\\":\\\"\\\",\\\"APP_FILLER2\\\":\\\"\\\",\\\"APP_FILLER3\\\":\\\"\\\",\\\"APP_STATUS\\\":\\\"07\\\",\\\"APP_STATUSDT\\\":\\\"31\\\\\\/01\\\\\\/2026 16:41:01\\\",\\\"APP_ERROR_DESC\\\":\\\"ERR-00000\\\",\\\"APP_DUMP_TYPE\\\":\\\"S\\\",\\\"APP_DNLDDT\\\":\\\"25\\\\\\/02\\\\\\/2026 23:07:57\\\",\\\"APP_REMARKS\\\":\\\"\\\",\\\"APP_KYC_MODE\\\":\\\"5\\\",\\\"APP_UID_TOKEN\\\":\\\"\\\",\\\"APP_VER_NO\\\":\\\"V33\\\",\\\"APP_KRA_INFO\\\":\\\"CVLKRA\\\",\\\"APP_IOP_FLG\\\":\\\"I\\\",\\\"APP_FATCA_APPLICABLE_FLAG\\\":\\\"N\\\",\\\"APP_FATCA_BIRTH_PLACE\\\":\\\"\\\",\\\"APP_FATCA_BIRTH_COUNTRY\\\":\\\"\\\",\\\"APP_FATCA_COUNTRY_RES\\\":null,\\\"APP_FATCA_COUNTRY_CITYZENSHIP\\\":\\\"\\\",\\\"APP_FATCA_DATE_DECLARATION\\\":\\\"08\\\\\\/01\\\\\\/2026\\\",\\\"APP_SIGNATURE\\\":\\\"\\\"},\\\"APP_PAN_SUMM\\\":{\\\"APP_OTHKRA_CODE\\\":\\\"CVLKRA\\\",\\\"APP_OTHKRA_BATCH\\\":\\\"25022026230757\\\",\\\"APP_REQ_DATE\\\":\\\"25\\\\\\/02\\\\\\/2026\\\",\\\"APP_RESPONSE_DATE\\\":\\\"25\\\\\\/02\\\\\\/2026 23:07:57\\\",\\\"APP_TOTAL_REC\\\":\\\"1\\\"}}\"', '{\"resdtls\":\"{\\\"KYC_DATA\\\":{\\\"APP_POS_CODE\\\":\\\"2500017240\\\",\\\"APP_TYPE\\\":\\\"I\\\",\\\"APP_NO\\\":\\\"54853232\\\",\\\"APP_DATE\\\":\\\"08\\/01\\/2026\\\",\\\"APP_PAN_NO\\\":\\\"DBFPP6463M\\\",\\\"APP_PAN_COPY\\\":\\\"Y\\\",\\\"APP_EXMT\\\":\\\"N\\\",\\\"APP_EXMT_CAT\\\":\\\"00\\\",\\\"APP_EXMT_ID_PROOF\\\":\\\"02\\\",\\\"APP_IPV_FLAG\\\":\\\"E\\\",\\\"APP_IPV_DATE\\\":\\\"08\\/01\\/2026\\\",\\\"APP_GEN\\\":\\\"M\\\",\\\"APP_NAME\\\":\\\"NARENDRA PATIDAR\\\",\\\"APP_F_NAME\\\":\\\"BHAGIRATH\\\",\\\"APP_REGNO\\\":\\\"\\\",\\\"APP_DOB_DT\\\":\\\"08\\/09\\/1997\\\",\\\"APP_COMMENCE_DT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_NATIONALITY\\\":\\\"01\\\",\\\"APP_OTH_NATIONALITY\\\":\\\"\\\",\\\"APP_COMP_STATUS\\\":\\\"00\\\",\\\"APP_OTH_COMP_STATUS\\\":\\\"\\\",\\\"APP_RES_STATUS\\\":\\\"R\\\",\\\"APP_RES_STATUS_PROOF\\\":\\\"\\\",\\\"APP_UID_NO\\\":\\\"N\\\",\\\"APP_COR_ADD1\\\":\\\"S O BHAGIRATH MAKAN NO 154 LAXMINARAYAN MANDIR KE\\\",\\\"APP_COR_ADD2\\\":\\\"PASS GRAM DHAKANI TEH MANASA DHAKANI MANASA NALK\\\",\\\"APP_COR_ADD3\\\":\\\"HEDA NALKHEDA 458113\\\",\\\"APP_COR_CITY\\\":\\\"NEEMUCH\\\",\\\"APP_COR_PINCD\\\":\\\"458113\\\",\\\"APP_COR_STATE\\\":\\\"023\\\",\\\"APP_COR_CTRY\\\":\\\"101\\\",\\\"APP_OFF_NO\\\":\\\"\\\",\\\"APP_RES_NO\\\":\\\"\\\",\\\"APP_MOB_NO\\\":\\\"7999426767\\\",\\\"APP_FAX_NO\\\":\\\"\\\",\\\"APP_EMAIL\\\":\\\"NP4375@GMAIL.COM\\\",\\\"APP_COR_ADD_PROOF\\\":\\\"31\\\",\\\"APP_COR_ADD_REF\\\":\\\"2620\\\",\\\"APP_COR_ADD_DT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_PER_ADD1\\\":\\\"S O BHAGIRATH MAKAN NO 154 LAXMINARAYAN MANDIR KE\\\",\\\"APP_PER_ADD2\\\":\\\"PASS GRAM DHAKANI TEH MANASA DHAKANI MANASA NALK\\\",\\\"APP_PER_ADD3\\\":\\\"HEDA NALKHEDA 458113\\\",\\\"APP_PER_CITY\\\":\\\"NEEMUCH\\\",\\\"APP_PER_PINCD\\\":\\\"458113\\\",\\\"APP_PER_STATE\\\":\\\"023\\\",\\\"APP_PER_CTRY\\\":\\\"101\\\",\\\"APP_PER_ADD_PROOF\\\":\\\"31\\\",\\\"APP_PER_ADD_REF\\\":\\\"2620\\\",\\\"APP_PER_ADD_DT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_INCOME\\\":\\\"00\\\",\\\"APP_OCC\\\":\\\"00\\\",\\\"APP_OTH_OCC\\\":\\\"\\\",\\\"APP_POL_CONN\\\":\\\"NA\\\",\\\"APP_DOC_PROOF\\\":\\\"E\\\",\\\"APP_INTERNAL_REF\\\":\\\"20260225230757-138CV\\\",\\\"APP_BRANCH_CODE\\\":\\\"HEADOFFICE\\\",\\\"APP_MAR_STATUS\\\":\\\"02\\\",\\\"APP_NETWRTH\\\":\\\"\\\",\\\"APP_NETWORTH_DT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_INCORP_PLC\\\":\\\"\\\",\\\"APP_OTHERINFO\\\":\\\"6 FIELD UPDATE\\\",\\\"APP_ACC_OPENDT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_ACC_ACTIVEDT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_ACC_UPDTDT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_FILLER1\\\":\\\"\\\",\\\"APP_FILLER2\\\":\\\"\\\",\\\"APP_FILLER3\\\":\\\"\\\",\\\"APP_STATUS\\\":\\\"07\\\",\\\"APP_STATUSDT\\\":\\\"31\\/01\\/2026 16:41:01\\\",\\\"APP_ERROR_DESC\\\":\\\"ERR-00000\\\",\\\"APP_DUMP_TYPE\\\":\\\"S\\\",\\\"APP_DNLDDT\\\":\\\"25\\/02\\/2026 23:07:57\\\",\\\"APP_REMARKS\\\":\\\"\\\",\\\"APP_KYC_MODE\\\":\\\"5\\\",\\\"APP_UID_TOKEN\\\":\\\"\\\",\\\"APP_VER_NO\\\":\\\"V33\\\",\\\"APP_KRA_INFO\\\":\\\"CVLKRA\\\",\\\"APP_IOP_FLG\\\":\\\"I\\\",\\\"APP_FATCA_APPLICABLE_FLAG\\\":\\\"N\\\",\\\"APP_FATCA_BIRTH_PLACE\\\":\\\"\\\",\\\"APP_FATCA_BIRTH_COUNTRY\\\":\\\"\\\",\\\"APP_FATCA_COUNTRY_RES\\\":null,\\\"APP_FATCA_COUNTRY_CITYZENSHIP\\\":\\\"\\\",\\\"APP_FATCA_DATE_DECLARATION\\\":\\\"08\\/01\\/2026\\\",\\\"APP_SIGNATURE\\\":\\\"\\\"},\\\"APP_PAN_SUMM\\\":{\\\"APP_OTHKRA_CODE\\\":\\\"CVLKRA\\\",\\\"APP_OTHKRA_BATCH\\\":\\\"25022026230757\\\",\\\"APP_REQ_DATE\\\":\\\"25\\/02\\/2026\\\",\\\"APP_RESPONSE_DATE\\\":\\\"25\\/02\\/2026 23:07:57\\\",\\\"APP_TOTAL_REC\\\":\\\"1\\\"}}\",\"error_code\":\"\",\"error_message\":\"\",\"KYC_DATA\":{\"APP_POS_CODE\":\"2500017240\",\"APP_TYPE\":\"I\",\"APP_NO\":\"54853232\",\"APP_DATE\":\"08\\/01\\/2026\",\"APP_PAN_NO\":\"DBFPP6463M\",\"APP_PAN_COPY\":\"Y\",\"APP_EXMT\":\"N\",\"APP_EXMT_CAT\":\"00\",\"APP_EXMT_ID_PROOF\":\"02\",\"APP_IPV_FLAG\":\"E\",\"APP_IPV_DATE\":\"08\\/01\\/2026\",\"APP_GEN\":\"M\",\"APP_NAME\":\"NARENDRA PATIDAR\",\"APP_F_NAME\":\"BHAGIRATH\",\"APP_REGNO\":\"\",\"APP_DOB_DT\":\"08\\/09\\/1997\",\"APP_COMMENCE_DT\":\"01\\/01\\/1900\",\"APP_NATIONALITY\":\"01\",\"APP_OTH_NATIONALITY\":\"\",\"APP_COMP_STATUS\":\"00\",\"APP_OTH_COMP_STATUS\":\"\",\"APP_RES_STATUS\":\"R\",\"APP_RES_STATUS_PROOF\":\"\",\"APP_UID_NO\":\"N\",\"APP_COR_ADD1\":\"S O BHAGIRATH MAKAN NO 154 LAXMINARAYAN MANDIR KE\",\"APP_COR_ADD2\":\"PASS GRAM DHAKANI TEH MANASA DHAKANI MANASA NALK\",\"APP_COR_ADD3\":\"HEDA NALKHEDA 458113\",\"APP_COR_CITY\":\"NEEMUCH\",\"APP_COR_PINCD\":\"458113\",\"APP_COR_STATE\":\"023\",\"APP_COR_CTRY\":\"101\",\"APP_OFF_NO\":\"\",\"APP_RES_NO\":\"\",\"APP_MOB_NO\":\"7999426767\",\"APP_FAX_NO\":\"\",\"APP_EMAIL\":\"NP4375@GMAIL.COM\",\"APP_COR_ADD_PROOF\":\"31\",\"APP_COR_ADD_REF\":\"2620\",\"APP_COR_ADD_DT\":\"01\\/01\\/1900\",\"APP_PER_ADD1\":\"S O BHAGIRATH MAKAN NO 154 LAXMINARAYAN MANDIR KE\",\"APP_PER_ADD2\":\"PASS GRAM DHAKANI TEH MANASA DHAKANI MANASA NALK\",\"APP_PER_ADD3\":\"HEDA NALKHEDA 458113\",\"APP_PER_CITY\":\"NEEMUCH\",\"APP_PER_PINCD\":\"458113\",\"APP_PER_STATE\":\"023\",\"APP_PER_CTRY\":\"101\",\"APP_PER_ADD_PROOF\":\"31\",\"APP_PER_ADD_REF\":\"2620\",\"APP_PER_ADD_DT\":\"01\\/01\\/1900\",\"APP_INCOME\":\"00\",\"APP_OCC\":\"00\",\"APP_OTH_OCC\":\"\",\"APP_POL_CONN\":\"NA\",\"APP_DOC_PROOF\":\"E\",\"APP_INTERNAL_REF\":\"20260225230757-138CV\",\"APP_BRANCH_CODE\":\"HEADOFFICE\",\"APP_MAR_STATUS\":\"02\",\"APP_NETWRTH\":\"\",\"APP_NETWORTH_DT\":\"01\\/01\\/1900\",\"APP_INCORP_PLC\":\"\",\"APP_OTHERINFO\":\"6 FIELD UPDATE\",\"APP_ACC_OPENDT\":\"01\\/01\\/1900\",\"APP_ACC_ACTIVEDT\":\"01\\/01\\/1900\",\"APP_ACC_UPDTDT\":\"01\\/01\\/1900\",\"APP_FILLER1\":\"\",\"APP_FILLER2\":\"\",\"APP_FILLER3\":\"\",\"APP_STATUS\":\"07\",\"APP_STATUSDT\":\"31\\/01\\/2026 16:41:01\",\"APP_ERROR_DESC\":\"ERR-00000\",\"APP_DUMP_TYPE\":\"S\",\"APP_DNLDDT\":\"25\\/02\\/2026 23:07:57\",\"APP_REMARKS\":\"\",\"APP_KYC_MODE\":\"5\",\"APP_UID_TOKEN\":\"\",\"APP_VER_NO\":\"V33\",\"APP_KRA_INFO\":\"CVLKRA\",\"APP_IOP_FLG\":\"I\",\"APP_FATCA_APPLICABLE_FLAG\":\"N\",\"APP_FATCA_BIRTH_PLACE\":\"\",\"APP_FATCA_BIRTH_COUNTRY\":\"\",\"APP_FATCA_COUNTRY_RES\":null,\"APP_FATCA_COUNTRY_CITYZENSHIP\":\"\",\"APP_FATCA_DATE_DECLARATION\":\"08\\/01\\/2026\",\"APP_SIGNATURE\":\"\"},\"APP_PAN_SUMM\":{\"APP_OTHKRA_CODE\":\"CVLKRA\",\"APP_OTHKRA_BATCH\":\"25022026230757\",\"APP_REQ_DATE\":\"25\\/02\\/2026\",\"APP_RESPONSE_DATE\":\"25\\/02\\/2026 23:07:57\",\"APP_TOTAL_REC\":\"1\"}}', '2026-02-25 17:38:02', 'kyc_documents/DBFPP6463M_KYC_DOCUMENTS.zip', 'kyc_documents/DBFPP6463M_KYC_DOCUMENTS.zip', '20260225230757-138CV', NULL, '2026-02-23 03:31:35', '2026-02-25 12:08:02'),
(19, 'BLSPJ0470P', 'KASHISH JOSHI', '21-05-1997', 'SURESH JOSHI', '712.WARD NUMBER 11, HIRAN MAGRI SECTOR 3 SURYA NAGAR HIRAN MAGRI SECTOR 3 ', '313002', '008', 'UDAIPUR', 'pending', NULL, '\"{\\\"resdtls\\\":\\\"{\\\\\\\"KYC_DATA\\\\\\\":{\\\\\\\"APP_POS_CODE\\\\\\\":\\\\\\\"2500017240\\\\\\\",\\\\\\\"APP_TYPE\\\\\\\":\\\\\\\"I\\\\\\\",\\\\\\\"APP_NO\\\\\\\":\\\\\\\"5200313\\\\\\\",\\\\\\\"APP_DATE\\\\\\\":\\\\\\\"01\\\\\\/03\\\\\\/2021\\\\\\\",\\\\\\\"APP_PAN_NO\\\\\\\":\\\\\\\"BLSPJ0470P\\\\\\\",\\\\\\\"APP_PAN_COPY\\\\\\\":\\\\\\\"Y\\\\\\\",\\\\\\\"APP_EXMT\\\\\\\":\\\\\\\"N\\\\\\\",\\\\\\\"APP_EXMT_CAT\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_EXMT_ID_PROOF\\\\\\\":\\\\\\\"01\\\\\\\",\\\\\\\"APP_IPV_FLAG\\\\\\\":\\\\\\\"Y\\\\\\\",\\\\\\\"APP_IPV_DATE\\\\\\\":\\\\\\\"28\\\\\\/02\\\\\\/2021\\\\\\\",\\\\\\\"APP_GEN\\\\\\\":\\\\\\\"M\\\\\\\",\\\\\\\"APP_NAME\\\\\\\":\\\\\\\"KASHISH JOSHI\\\\\\\",\\\\\\\"APP_F_NAME\\\\\\\":\\\\\\\"SURESH JOSHI\\\\\\\",\\\\\\\"APP_REGNO\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_DOB_DT\\\\\\\":\\\\\\\"21\\\\\\/05\\\\\\/1997\\\\\\\",\\\\\\\"APP_COMMENCE_DT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_NATIONALITY\\\\\\\":\\\\\\\"01\\\\\\\",\\\\\\\"APP_OTH_NATIONALITY\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_COMP_STATUS\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_OTH_COMP_STATUS\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_RES_STATUS\\\\\\\":\\\\\\\"R\\\\\\\",\\\\\\\"APP_RES_STATUS_PROOF\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_UID_NO\\\\\\\":\\\\\\\"N\\\\\\\",\\\\\\\"APP_COR_ADD1\\\\\\\":\\\\\\\"712.WARD NUMBER 11, HIRAN MAGRI SECTOR 3 SURYA NAGAR\\\\\\\",\\\\\\\"APP_COR_ADD2\\\\\\\":\\\\\\\"HIRAN MAGRI SECTOR 3\\\\\\\",\\\\\\\"APP_COR_ADD3\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_COR_CITY\\\\\\\":\\\\\\\"UDAIPUR\\\\\\\",\\\\\\\"APP_COR_PINCD\\\\\\\":\\\\\\\"313002\\\\\\\",\\\\\\\"APP_COR_STATE\\\\\\\":\\\\\\\"008\\\\\\\",\\\\\\\"APP_COR_CTRY\\\\\\\":\\\\\\\"101\\\\\\\",\\\\\\\"APP_OFF_NO\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_RES_NO\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_MOB_NO\\\\\\\":\\\\\\\"9079096751\\\\\\\",\\\\\\\"APP_FAX_NO\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_EMAIL\\\\\\\":\\\\\\\"KASHISHJOSHI49@GMAIL.COM\\\\\\\",\\\\\\\"APP_COR_ADD_PROOF\\\\\\\":\\\\\\\"31\\\\\\\",\\\\\\\"APP_COR_ADD_REF\\\\\\\":\\\\\\\"6955\\\\\\\",\\\\\\\"APP_COR_ADD_DT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_PER_ADD1\\\\\\\":\\\\\\\"712.WARD NUMBER 11, HIRAN MAGRI SECTOR 3 SURYA NAGAR\\\\\\\",\\\\\\\"APP_PER_ADD2\\\\\\\":\\\\\\\"HIRAN MAGRI SECTOR 3\\\\\\\",\\\\\\\"APP_PER_ADD3\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_PER_CITY\\\\\\\":\\\\\\\"UDAIPUR\\\\\\\",\\\\\\\"APP_PER_PINCD\\\\\\\":\\\\\\\"313002\\\\\\\",\\\\\\\"APP_PER_STATE\\\\\\\":\\\\\\\"008\\\\\\\",\\\\\\\"APP_PER_CTRY\\\\\\\":\\\\\\\"101\\\\\\\",\\\\\\\"APP_PER_ADD_PROOF\\\\\\\":\\\\\\\"31\\\\\\\",\\\\\\\"APP_PER_ADD_REF\\\\\\\":\\\\\\\"6955\\\\\\\",\\\\\\\"APP_PER_ADD_DT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_INCOME\\\\\\\":\\\\\\\"02\\\\\\\",\\\\\\\"APP_OCC\\\\\\\":\\\\\\\"99\\\\\\\",\\\\\\\"APP_OTH_OCC\\\\\\\":\\\\\\\"OTHERS\\\\\\\",\\\\\\\"APP_POL_CONN\\\\\\\":\\\\\\\"NA\\\\\\\",\\\\\\\"APP_DOC_PROOF\\\\\\\":\\\\\\\"S\\\\\\\",\\\\\\\"APP_INTERNAL_REF\\\\\\\":\\\\\\\"202602231434369573CV\\\\\\\",\\\\\\\"APP_BRANCH_CODE\\\\\\\":\\\\\\\"HEADOFFICE\\\\\\\",\\\\\\\"APP_MAR_STATUS\\\\\\\":\\\\\\\"02\\\\\\\",\\\\\\\"APP_NETWRTH\\\\\\\":\\\\\\\"02\\\\\\\",\\\\\\\"APP_NETWORTH_DT\\\\\\\":\\\\\\\"28\\\\\\/02\\\\\\/2021\\\\\\\",\\\\\\\"APP_INCORP_PLC\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_OTHERINFO\\\\\\\":\\\\\\\"FATCA DETAILS RECEIVED - BATCH\\\\\\\",\\\\\\\"APP_ACC_OPENDT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_ACC_ACTIVEDT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_ACC_UPDTDT\\\\\\\":\\\\\\\"01\\\\\\/01\\\\\\/1900\\\\\\\",\\\\\\\"APP_FILLER1\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_FILLER2\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_FILLER3\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_STATUS\\\\\\\":\\\\\\\"07\\\\\\\",\\\\\\\"APP_STATUSDT\\\\\\\":\\\\\\\"01\\\\\\/05\\\\\\/2023 00:00:00\\\\\\\",\\\\\\\"APP_ERROR_DESC\\\\\\\":\\\\\\\"ERR-00000\\\\\\\",\\\\\\\"APP_DUMP_TYPE\\\\\\\":\\\\\\\"S\\\\\\\",\\\\\\\"APP_DNLDDT\\\\\\\":\\\\\\\"23\\\\\\/02\\\\\\/2026 14:34:36\\\\\\\",\\\\\\\"APP_REMARKS\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_KYC_MODE\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"APP_UID_TOKEN\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_VER_NO\\\\\\\":\\\\\\\"V33\\\\\\\",\\\\\\\"APP_KRA_INFO\\\\\\\":\\\\\\\"CVLKRA\\\\\\\",\\\\\\\"APP_IOP_FLG\\\\\\\":\\\\\\\"I\\\\\\\",\\\\\\\"APP_FATCA_APPLICABLE_FLAG\\\\\\\":\\\\\\\"N\\\\\\\",\\\\\\\"APP_FATCA_BIRTH_PLACE\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_FATCA_BIRTH_COUNTRY\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_FATCA_COUNTRY_RES\\\\\\\":null,\\\\\\\"APP_FATCA_COUNTRY_CITYZENSHIP\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"APP_FATCA_DATE_DECLARATION\\\\\\\":\\\\\\\"20\\\\\\/08\\\\\\/2020\\\\\\\",\\\\\\\"APP_SIGNATURE\\\\\\\":\\\\\\\"\\\\\\\"},\\\\\\\"APP_PAN_SUMM\\\\\\\":{\\\\\\\"APP_OTHKRA_CODE\\\\\\\":\\\\\\\"CVLKRA\\\\\\\",\\\\\\\"APP_OTHKRA_BATCH\\\\\\\":\\\\\\\"23022026143436\\\\\\\",\\\\\\\"APP_REQ_DATE\\\\\\\":\\\\\\\"23\\\\\\/02\\\\\\/2026\\\\\\\",\\\\\\\"APP_RESPONSE_DATE\\\\\\\":\\\\\\\"23\\\\\\/02\\\\\\/2026 14:34:36\\\\\\\",\\\\\\\"APP_TOTAL_REC\\\\\\\":\\\\\\\"1\\\\\\\"}}\\\",\\\"error_code\\\":\\\"\\\",\\\"error_message\\\":\\\"\\\",\\\"KYC_DATA\\\":{\\\"APP_POS_CODE\\\":\\\"2500017240\\\",\\\"APP_TYPE\\\":\\\"I\\\",\\\"APP_NO\\\":\\\"5200313\\\",\\\"APP_DATE\\\":\\\"01\\\\\\/03\\\\\\/2021\\\",\\\"APP_PAN_NO\\\":\\\"BLSPJ0470P\\\",\\\"APP_PAN_COPY\\\":\\\"Y\\\",\\\"APP_EXMT\\\":\\\"N\\\",\\\"APP_EXMT_CAT\\\":\\\"\\\",\\\"APP_EXMT_ID_PROOF\\\":\\\"01\\\",\\\"APP_IPV_FLAG\\\":\\\"Y\\\",\\\"APP_IPV_DATE\\\":\\\"28\\\\\\/02\\\\\\/2021\\\",\\\"APP_GEN\\\":\\\"M\\\",\\\"APP_NAME\\\":\\\"KASHISH JOSHI\\\",\\\"APP_F_NAME\\\":\\\"SURESH JOSHI\\\",\\\"APP_REGNO\\\":\\\"\\\",\\\"APP_DOB_DT\\\":\\\"21\\\\\\/05\\\\\\/1997\\\",\\\"APP_COMMENCE_DT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_NATIONALITY\\\":\\\"01\\\",\\\"APP_OTH_NATIONALITY\\\":\\\"\\\",\\\"APP_COMP_STATUS\\\":\\\"\\\",\\\"APP_OTH_COMP_STATUS\\\":\\\"\\\",\\\"APP_RES_STATUS\\\":\\\"R\\\",\\\"APP_RES_STATUS_PROOF\\\":\\\"\\\",\\\"APP_UID_NO\\\":\\\"N\\\",\\\"APP_COR_ADD1\\\":\\\"712.WARD NUMBER 11, HIRAN MAGRI SECTOR 3 SURYA NAGAR\\\",\\\"APP_COR_ADD2\\\":\\\"HIRAN MAGRI SECTOR 3\\\",\\\"APP_COR_ADD3\\\":\\\"\\\",\\\"APP_COR_CITY\\\":\\\"UDAIPUR\\\",\\\"APP_COR_PINCD\\\":\\\"313002\\\",\\\"APP_COR_STATE\\\":\\\"008\\\",\\\"APP_COR_CTRY\\\":\\\"101\\\",\\\"APP_OFF_NO\\\":\\\"\\\",\\\"APP_RES_NO\\\":\\\"\\\",\\\"APP_MOB_NO\\\":\\\"9079096751\\\",\\\"APP_FAX_NO\\\":\\\"\\\",\\\"APP_EMAIL\\\":\\\"KASHISHJOSHI49@GMAIL.COM\\\",\\\"APP_COR_ADD_PROOF\\\":\\\"31\\\",\\\"APP_COR_ADD_REF\\\":\\\"6955\\\",\\\"APP_COR_ADD_DT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_PER_ADD1\\\":\\\"712.WARD NUMBER 11, HIRAN MAGRI SECTOR 3 SURYA NAGAR\\\",\\\"APP_PER_ADD2\\\":\\\"HIRAN MAGRI SECTOR 3\\\",\\\"APP_PER_ADD3\\\":\\\"\\\",\\\"APP_PER_CITY\\\":\\\"UDAIPUR\\\",\\\"APP_PER_PINCD\\\":\\\"313002\\\",\\\"APP_PER_STATE\\\":\\\"008\\\",\\\"APP_PER_CTRY\\\":\\\"101\\\",\\\"APP_PER_ADD_PROOF\\\":\\\"31\\\",\\\"APP_PER_ADD_REF\\\":\\\"6955\\\",\\\"APP_PER_ADD_DT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_INCOME\\\":\\\"02\\\",\\\"APP_OCC\\\":\\\"99\\\",\\\"APP_OTH_OCC\\\":\\\"OTHERS\\\",\\\"APP_POL_CONN\\\":\\\"NA\\\",\\\"APP_DOC_PROOF\\\":\\\"S\\\",\\\"APP_INTERNAL_REF\\\":\\\"202602231434369573CV\\\",\\\"APP_BRANCH_CODE\\\":\\\"HEADOFFICE\\\",\\\"APP_MAR_STATUS\\\":\\\"02\\\",\\\"APP_NETWRTH\\\":\\\"02\\\",\\\"APP_NETWORTH_DT\\\":\\\"28\\\\\\/02\\\\\\/2021\\\",\\\"APP_INCORP_PLC\\\":\\\"\\\",\\\"APP_OTHERINFO\\\":\\\"FATCA DETAILS RECEIVED - BATCH\\\",\\\"APP_ACC_OPENDT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_ACC_ACTIVEDT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_ACC_UPDTDT\\\":\\\"01\\\\\\/01\\\\\\/1900\\\",\\\"APP_FILLER1\\\":\\\"\\\",\\\"APP_FILLER2\\\":\\\"\\\",\\\"APP_FILLER3\\\":\\\"\\\",\\\"APP_STATUS\\\":\\\"07\\\",\\\"APP_STATUSDT\\\":\\\"01\\\\\\/05\\\\\\/2023 00:00:00\\\",\\\"APP_ERROR_DESC\\\":\\\"ERR-00000\\\",\\\"APP_DUMP_TYPE\\\":\\\"S\\\",\\\"APP_DNLDDT\\\":\\\"23\\\\\\/02\\\\\\/2026 14:34:36\\\",\\\"APP_REMARKS\\\":\\\"\\\",\\\"APP_KYC_MODE\\\":\\\"5\\\",\\\"APP_UID_TOKEN\\\":\\\"\\\",\\\"APP_VER_NO\\\":\\\"V33\\\",\\\"APP_KRA_INFO\\\":\\\"CVLKRA\\\",\\\"APP_IOP_FLG\\\":\\\"I\\\",\\\"APP_FATCA_APPLICABLE_FLAG\\\":\\\"N\\\",\\\"APP_FATCA_BIRTH_PLACE\\\":\\\"\\\",\\\"APP_FATCA_BIRTH_COUNTRY\\\":\\\"\\\",\\\"APP_FATCA_COUNTRY_RES\\\":null,\\\"APP_FATCA_COUNTRY_CITYZENSHIP\\\":\\\"\\\",\\\"APP_FATCA_DATE_DECLARATION\\\":\\\"20\\\\\\/08\\\\\\/2020\\\",\\\"APP_SIGNATURE\\\":\\\"\\\"},\\\"APP_PAN_SUMM\\\":{\\\"APP_OTHKRA_CODE\\\":\\\"CVLKRA\\\",\\\"APP_OTHKRA_BATCH\\\":\\\"23022026143436\\\",\\\"APP_REQ_DATE\\\":\\\"23\\\\\\/02\\\\\\/2026\\\",\\\"APP_RESPONSE_DATE\\\":\\\"23\\\\\\/02\\\\\\/2026 14:34:36\\\",\\\"APP_TOTAL_REC\\\":\\\"1\\\"}}\"', '{\"resdtls\":\"{\\\"KYC_DATA\\\":{\\\"APP_POS_CODE\\\":\\\"2500017240\\\",\\\"APP_TYPE\\\":\\\"I\\\",\\\"APP_NO\\\":\\\"5200313\\\",\\\"APP_DATE\\\":\\\"01\\/03\\/2021\\\",\\\"APP_PAN_NO\\\":\\\"BLSPJ0470P\\\",\\\"APP_PAN_COPY\\\":\\\"Y\\\",\\\"APP_EXMT\\\":\\\"N\\\",\\\"APP_EXMT_CAT\\\":\\\"\\\",\\\"APP_EXMT_ID_PROOF\\\":\\\"01\\\",\\\"APP_IPV_FLAG\\\":\\\"Y\\\",\\\"APP_IPV_DATE\\\":\\\"28\\/02\\/2021\\\",\\\"APP_GEN\\\":\\\"M\\\",\\\"APP_NAME\\\":\\\"KASHISH JOSHI\\\",\\\"APP_F_NAME\\\":\\\"SURESH JOSHI\\\",\\\"APP_REGNO\\\":\\\"\\\",\\\"APP_DOB_DT\\\":\\\"21\\/05\\/1997\\\",\\\"APP_COMMENCE_DT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_NATIONALITY\\\":\\\"01\\\",\\\"APP_OTH_NATIONALITY\\\":\\\"\\\",\\\"APP_COMP_STATUS\\\":\\\"\\\",\\\"APP_OTH_COMP_STATUS\\\":\\\"\\\",\\\"APP_RES_STATUS\\\":\\\"R\\\",\\\"APP_RES_STATUS_PROOF\\\":\\\"\\\",\\\"APP_UID_NO\\\":\\\"N\\\",\\\"APP_COR_ADD1\\\":\\\"712.WARD NUMBER 11, HIRAN MAGRI SECTOR 3 SURYA NAGAR\\\",\\\"APP_COR_ADD2\\\":\\\"HIRAN MAGRI SECTOR 3\\\",\\\"APP_COR_ADD3\\\":\\\"\\\",\\\"APP_COR_CITY\\\":\\\"UDAIPUR\\\",\\\"APP_COR_PINCD\\\":\\\"313002\\\",\\\"APP_COR_STATE\\\":\\\"008\\\",\\\"APP_COR_CTRY\\\":\\\"101\\\",\\\"APP_OFF_NO\\\":\\\"\\\",\\\"APP_RES_NO\\\":\\\"\\\",\\\"APP_MOB_NO\\\":\\\"9079096751\\\",\\\"APP_FAX_NO\\\":\\\"\\\",\\\"APP_EMAIL\\\":\\\"KASHISHJOSHI49@GMAIL.COM\\\",\\\"APP_COR_ADD_PROOF\\\":\\\"31\\\",\\\"APP_COR_ADD_REF\\\":\\\"6955\\\",\\\"APP_COR_ADD_DT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_PER_ADD1\\\":\\\"712.WARD NUMBER 11, HIRAN MAGRI SECTOR 3 SURYA NAGAR\\\",\\\"APP_PER_ADD2\\\":\\\"HIRAN MAGRI SECTOR 3\\\",\\\"APP_PER_ADD3\\\":\\\"\\\",\\\"APP_PER_CITY\\\":\\\"UDAIPUR\\\",\\\"APP_PER_PINCD\\\":\\\"313002\\\",\\\"APP_PER_STATE\\\":\\\"008\\\",\\\"APP_PER_CTRY\\\":\\\"101\\\",\\\"APP_PER_ADD_PROOF\\\":\\\"31\\\",\\\"APP_PER_ADD_REF\\\":\\\"6955\\\",\\\"APP_PER_ADD_DT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_INCOME\\\":\\\"02\\\",\\\"APP_OCC\\\":\\\"99\\\",\\\"APP_OTH_OCC\\\":\\\"OTHERS\\\",\\\"APP_POL_CONN\\\":\\\"NA\\\",\\\"APP_DOC_PROOF\\\":\\\"S\\\",\\\"APP_INTERNAL_REF\\\":\\\"202602231434369573CV\\\",\\\"APP_BRANCH_CODE\\\":\\\"HEADOFFICE\\\",\\\"APP_MAR_STATUS\\\":\\\"02\\\",\\\"APP_NETWRTH\\\":\\\"02\\\",\\\"APP_NETWORTH_DT\\\":\\\"28\\/02\\/2021\\\",\\\"APP_INCORP_PLC\\\":\\\"\\\",\\\"APP_OTHERINFO\\\":\\\"FATCA DETAILS RECEIVED - BATCH\\\",\\\"APP_ACC_OPENDT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_ACC_ACTIVEDT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_ACC_UPDTDT\\\":\\\"01\\/01\\/1900\\\",\\\"APP_FILLER1\\\":\\\"\\\",\\\"APP_FILLER2\\\":\\\"\\\",\\\"APP_FILLER3\\\":\\\"\\\",\\\"APP_STATUS\\\":\\\"07\\\",\\\"APP_STATUSDT\\\":\\\"01\\/05\\/2023 00:00:00\\\",\\\"APP_ERROR_DESC\\\":\\\"ERR-00000\\\",\\\"APP_DUMP_TYPE\\\":\\\"S\\\",\\\"APP_DNLDDT\\\":\\\"23\\/02\\/2026 14:34:36\\\",\\\"APP_REMARKS\\\":\\\"\\\",\\\"APP_KYC_MODE\\\":\\\"5\\\",\\\"APP_UID_TOKEN\\\":\\\"\\\",\\\"APP_VER_NO\\\":\\\"V33\\\",\\\"APP_KRA_INFO\\\":\\\"CVLKRA\\\",\\\"APP_IOP_FLG\\\":\\\"I\\\",\\\"APP_FATCA_APPLICABLE_FLAG\\\":\\\"N\\\",\\\"APP_FATCA_BIRTH_PLACE\\\":\\\"\\\",\\\"APP_FATCA_BIRTH_COUNTRY\\\":\\\"\\\",\\\"APP_FATCA_COUNTRY_RES\\\":null,\\\"APP_FATCA_COUNTRY_CITYZENSHIP\\\":\\\"\\\",\\\"APP_FATCA_DATE_DECLARATION\\\":\\\"20\\/08\\/2020\\\",\\\"APP_SIGNATURE\\\":\\\"\\\"},\\\"APP_PAN_SUMM\\\":{\\\"APP_OTHKRA_CODE\\\":\\\"CVLKRA\\\",\\\"APP_OTHKRA_BATCH\\\":\\\"23022026143436\\\",\\\"APP_REQ_DATE\\\":\\\"23\\/02\\/2026\\\",\\\"APP_RESPONSE_DATE\\\":\\\"23\\/02\\/2026 14:34:36\\\",\\\"APP_TOTAL_REC\\\":\\\"1\\\"}}\",\"error_code\":\"\",\"error_message\":\"\",\"KYC_DATA\":{\"APP_POS_CODE\":\"2500017240\",\"APP_TYPE\":\"I\",\"APP_NO\":\"5200313\",\"APP_DATE\":\"01\\/03\\/2021\",\"APP_PAN_NO\":\"BLSPJ0470P\",\"APP_PAN_COPY\":\"Y\",\"APP_EXMT\":\"N\",\"APP_EXMT_CAT\":\"\",\"APP_EXMT_ID_PROOF\":\"01\",\"APP_IPV_FLAG\":\"Y\",\"APP_IPV_DATE\":\"28\\/02\\/2021\",\"APP_GEN\":\"M\",\"APP_NAME\":\"KASHISH JOSHI\",\"APP_F_NAME\":\"SURESH JOSHI\",\"APP_REGNO\":\"\",\"APP_DOB_DT\":\"21\\/05\\/1997\",\"APP_COMMENCE_DT\":\"01\\/01\\/1900\",\"APP_NATIONALITY\":\"01\",\"APP_OTH_NATIONALITY\":\"\",\"APP_COMP_STATUS\":\"\",\"APP_OTH_COMP_STATUS\":\"\",\"APP_RES_STATUS\":\"R\",\"APP_RES_STATUS_PROOF\":\"\",\"APP_UID_NO\":\"N\",\"APP_COR_ADD1\":\"712.WARD NUMBER 11, HIRAN MAGRI SECTOR 3 SURYA NAGAR\",\"APP_COR_ADD2\":\"HIRAN MAGRI SECTOR 3\",\"APP_COR_ADD3\":\"\",\"APP_COR_CITY\":\"UDAIPUR\",\"APP_COR_PINCD\":\"313002\",\"APP_COR_STATE\":\"008\",\"APP_COR_CTRY\":\"101\",\"APP_OFF_NO\":\"\",\"APP_RES_NO\":\"\",\"APP_MOB_NO\":\"9079096751\",\"APP_FAX_NO\":\"\",\"APP_EMAIL\":\"KASHISHJOSHI49@GMAIL.COM\",\"APP_COR_ADD_PROOF\":\"31\",\"APP_COR_ADD_REF\":\"6955\",\"APP_COR_ADD_DT\":\"01\\/01\\/1900\",\"APP_PER_ADD1\":\"712.WARD NUMBER 11, HIRAN MAGRI SECTOR 3 SURYA NAGAR\",\"APP_PER_ADD2\":\"HIRAN MAGRI SECTOR 3\",\"APP_PER_ADD3\":\"\",\"APP_PER_CITY\":\"UDAIPUR\",\"APP_PER_PINCD\":\"313002\",\"APP_PER_STATE\":\"008\",\"APP_PER_CTRY\":\"101\",\"APP_PER_ADD_PROOF\":\"31\",\"APP_PER_ADD_REF\":\"6955\",\"APP_PER_ADD_DT\":\"01\\/01\\/1900\",\"APP_INCOME\":\"02\",\"APP_OCC\":\"99\",\"APP_OTH_OCC\":\"OTHERS\",\"APP_POL_CONN\":\"NA\",\"APP_DOC_PROOF\":\"S\",\"APP_INTERNAL_REF\":\"202602231434369573CV\",\"APP_BRANCH_CODE\":\"HEADOFFICE\",\"APP_MAR_STATUS\":\"02\",\"APP_NETWRTH\":\"02\",\"APP_NETWORTH_DT\":\"28\\/02\\/2021\",\"APP_INCORP_PLC\":\"\",\"APP_OTHERINFO\":\"FATCA DETAILS RECEIVED - BATCH\",\"APP_ACC_OPENDT\":\"01\\/01\\/1900\",\"APP_ACC_ACTIVEDT\":\"01\\/01\\/1900\",\"APP_ACC_UPDTDT\":\"01\\/01\\/1900\",\"APP_FILLER1\":\"\",\"APP_FILLER2\":\"\",\"APP_FILLER3\":\"\",\"APP_STATUS\":\"07\",\"APP_STATUSDT\":\"01\\/05\\/2023 00:00:00\",\"APP_ERROR_DESC\":\"ERR-00000\",\"APP_DUMP_TYPE\":\"S\",\"APP_DNLDDT\":\"23\\/02\\/2026 14:34:36\",\"APP_REMARKS\":\"\",\"APP_KYC_MODE\":\"5\",\"APP_UID_TOKEN\":\"\",\"APP_VER_NO\":\"V33\",\"APP_KRA_INFO\":\"CVLKRA\",\"APP_IOP_FLG\":\"I\",\"APP_FATCA_APPLICABLE_FLAG\":\"N\",\"APP_FATCA_BIRTH_PLACE\":\"\",\"APP_FATCA_BIRTH_COUNTRY\":\"\",\"APP_FATCA_COUNTRY_RES\":null,\"APP_FATCA_COUNTRY_CITYZENSHIP\":\"\",\"APP_FATCA_DATE_DECLARATION\":\"20\\/08\\/2020\",\"APP_SIGNATURE\":\"\"},\"APP_PAN_SUMM\":{\"APP_OTHKRA_CODE\":\"CVLKRA\",\"APP_OTHKRA_BATCH\":\"23022026143436\",\"APP_REQ_DATE\":\"23\\/02\\/2026\",\"APP_RESPONSE_DATE\":\"23\\/02\\/2026 14:34:36\",\"APP_TOTAL_REC\":\"1\"}}', '2026-02-23 09:04:39', 'kyc_documents/BLSPJ0470P_KYC_DOCUMENTS.zip', 'kyc_documents/BLSPJ0470P_KYC_DOCUMENTS.zip', '202602231434369573CV', NULL, '2026-02-23 03:34:39', '2026-02-23 03:34:39');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_26_193625_create_clients_table', 1),
(5, '2026_01_26_193723_create_invoices_table', 1),
(6, '2026_01_26_193812_create_agreements_table', 1),
(7, '2026_01_27_000000_add_fields_to_clients_table', 1),
(8, '2026_000_01_29000_create_kyc_records_table', 2),
(9, '2026_000_01_29001_add_api_columns_to_kyc_records', 3),
(10, '2026_01_29_000002_add_kyc_zip_fields', 4),
(11, '2026_01_30_000000_add_sent_dates_to_agreements_table', 5),
(12, '2026_01_30_000001_add_sent_at_to_invoices_table', 5),
(13, '2026_01_01_000003_create_sessions_table', 6),
(14, '2026_02_22_000000_create_bulk_kyc_batches_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'narendra', 'np4375@gmail.com', NULL, '$2y$12$dRj6jLisazTjB1If.24SBezwvnUYPk8fHet1rEfyASZqPeApCb9Te', NULL, '2026-01-27 07:39:22', '2026-01-27 07:39:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agreements`
--
ALTER TABLE `agreements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agreements_client_id_foreign` (`client_id`);

--
-- Indexes for table `bulk_kyc_batches`
--
ALTER TABLE `bulk_kyc_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bulk_kyc_batches_user_id_foreign` (`user_id`);

--
-- Indexes for table `bulk_kyc_records`
--
ALTER TABLE `bulk_kyc_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bulk_kyc_records_batch_id_foreign` (`batch_id`),
  ADD KEY `bulk_kyc_records_kyc_record_id_foreign` (`kyc_record_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoices_client_id_foreign` (`client_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kyc_records`
--
ALTER TABLE `kyc_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kyc_records_pan_unique` (`pan`),
  ADD KEY `kyc_records_pan_index` (`pan`),
  ADD KEY `kyc_records_status_index` (`status`),
  ADD KEY `kyc_records_created_at_index` (`created_at`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agreements`
--
ALTER TABLE `agreements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bulk_kyc_batches`
--
ALTER TABLE `bulk_kyc_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bulk_kyc_records`
--
ALTER TABLE `bulk_kyc_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `kyc_records`
--
ALTER TABLE `kyc_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agreements`
--
ALTER TABLE `agreements`
  ADD CONSTRAINT `agreements_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bulk_kyc_batches`
--
ALTER TABLE `bulk_kyc_batches`
  ADD CONSTRAINT `bulk_kyc_batches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bulk_kyc_records`
--
ALTER TABLE `bulk_kyc_records`
  ADD CONSTRAINT `bulk_kyc_records_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `bulk_kyc_batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bulk_kyc_records_kyc_record_id_foreign` FOREIGN KEY (`kyc_record_id`) REFERENCES `kyc_records` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
