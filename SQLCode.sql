

create table roles(
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(100) NOT NULL UNIQUE
)

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    role_id INT,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);


create table categories(
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
)


create table items(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    item_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    item_description TEXT,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
)


create table orders(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    item_id INT,
    quantity INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
)



ALTER TABLE items
ADD price INT NOT NULL DEFAULT 0;



-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for iap_db
CREATE DATABASE IF NOT EXISTS iap_db /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE iap_db;

--
-- Table structure for table `users`
-- (Depends on nothing)
--
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_code` varchar(6) DEFAULT NULL,
  `reset_code_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `categories`
-- (Depends on nothing)
--
CREATE TABLE IF NOT EXISTS `categories` (
  `category_name` varchar(100) NOT NULL,
  PRIMARY KEY (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `roles`
-- (Depends on nothing)
--
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `items`
-- (Depends on `users` and `categories`)
--
CREATE TABLE IF NOT EXISTS `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_description` text DEFAULT NULL,
  `item_category` varchar(100) DEFAULT NULL,
  `Price` double DEFAULT NULL,
  `ImageUrl` varchar(1000) DEFAULT NULL,
  `item_condition` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_user_idx` (`user_id`),
  KEY `fk_item_category_idx` (`item_category`),
  CONSTRAINT `fk_item_category` FOREIGN KEY (`item_category`) REFERENCES `categories` (`category_name`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `orders`
-- (Depends on `users` and `items`)
--
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ---------------------------------
-- Dumping data for all tables
-- ---------------------------------

-- Data for table `users`
DELETE FROM `users`;
INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`, `reset_code`, `reset_code_expiry`) VALUES
	(1, 'Jesse Kariuki', 'kariukijesse4633@gmail.com', '$2y$10$rO8yoj4C0gSBfafw2O2N7.nX/iQMldgccwIdA2S2kdfPnSJ390fYe', '2025-10-27 13:57:02', '2025-10-27 13:57:02', NULL, NULL),
	(3, 'Jesse Kay', 'indradamenace@gmail.com', '$2y$10$q0wqWi4dQqqdZpaZZYZLk.4GKFEk11INd5j6sH6hhJNJ0SAQdplty', '2025-11-01 09:23:47', '2025-11-01 09:23:47', NULL, NULL);

-- Data for table `categories`
DELETE FROM `categories`;
INSERT INTO `categories` (`category_name`) VALUES
	('Accessories'),
	('Appliances'),
	('Clothing'),
	('Electronics'),
	('Footwear'),
	('Furniture'),
	('Home Decor'),
	('Music');

-- Data for table `roles`
DELETE FROM `roles`;

-- Data for table `items`
DELETE FROM `items`;
INSERT INTO `items` (`id`, `user_id`, `item_name`, `quantity`, `item_description`, `item_category`, `Price`, `ImageUrl`, `item_condition`, `created_at`) VALUES
	(82, 1, 'Dell XPS 13 Laptop', 5, 'Lightweight ultrabook with Intel i7 processor.', 'Electronics', 95000, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8', 'Used - Like New', '2025-11-01 10:13:15'),
	(83, 3, 'iPhone 13 Pro', 3, '128GB, Graphite color, excellent condition.', 'Electronics', 115000, 'https://images.unsplash.com/photo-1603898037225-44e9b8af9e4c', 'Used - Excellent', '2025-11-01 10:13:15'),
	(84, 1, 'Nike Air Max 270', 8, 'Comfortable running shoes, size 9.', 'Footwear', 12500, 'https://images.unsplash.com/photo-1606813902529-3a55c4d5c66a', 'New', '2025-11-01 10:13:15'),
	(85, 3, 'Wooden Study Desk', 4, 'Modern wooden desk for study or office.', 'Furniture', 14500, 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7', 'New', '2025-11-01 10:13:15'),
	(86, 1, 'Samsung Galaxy S21', 6, 'Smartphone with 128GB storage and AMOLED display.', 'Electronics', 87000, 'https://images.unsplash.com/photo-1611221956446-7d2e3b4f88a1', 'Used - Good', '2025-11-01 10:13:15'),
	(87, 3, 'Kitchen Blender', 10, 'High-speed blender ideal for smoothies.', 'Appliances', 5500, 'https://images.unsplash.com/photo-1621244064089-b34d822c8a07', 'New', '2025-11-01 10:13:15'),
	(88, 1, 'Acoustic Guitar', 3, 'Yamaha acoustic guitar with carrying case.', 'Music', 25000, 'https://images.unsplash.com/photo-1511376777868-611b54f68947', 'Used - Good', '2025-11-01 10:13:15'),
	(89, 3, 'Gaming Chair', 2, 'Ergonomic gaming chair with adjustable armrests.', 'Furniture', 19000, 'https://images.unsplash.com/photo-1606813902927-1c90a3f74a3e', 'New', '2025-11-01 10:13:15'),
	(90, 1, 'LED Desk Lamp', 15, 'Adjustable LED desk lamp with USB charging port.', 'Home Decor', 2200, 'https://images.unsplash.com/photo-1567016538199-859aa10e7b5b', 'New', '2025-11-01 10:13:15'),
	(91, 3, 'Wireless Headphones', 9, 'Noise-cancelling Bluetooth headphones.', 'Electronics', 6500, 'https://images.unsplash.com/photo-1518443895911-56d95e8e42b1', 'Used - Excellent', '2025-11-01 10:13:15'),
	(92, 1, 'Men’s Leather Wallet', 20, 'Handcrafted genuine leather wallet.', 'Accessories', 1800, 'https://images.unsplash.com/photo-1590080875831-8b2a4c1e9af9', 'New', '2025-11-01 10:13:15'),
	(93, 3, 'Apple Watch SE', 4, 'Smartwatch with health tracking and notifications.', 'Electronics', 42000, 'https://images.unsplash.com/photo-1579427422367-92e70b4b87e9', 'Used - Excellent', '2025-11-01 10:13:15'),
	(94, 1, 'Office Chair', 7, 'Comfortable office chair with lumbar support.', 'Furniture', 13000, 'https://images.unsplash.com/photo-1606813902931-89e5cbbd689b', 'New', '2025-11-01 10:13:15'),
	(95, 3, 'Smart TV 55-inch', 2, '4K Ultra HD smart TV with built-in apps.', 'Electronics', 78000, 'https://images.unsplash.com/photo-1573497019418-b400bb3ab074', 'Used - Like New', '2025-11-01 10:13:15'),
	(96, 1, 'Canon DSLR Camera', 3, 'Canon EOS 200D with 18–55mm lens kit.', 'Electronics', 59000, 'https://images.unsplash.com/photo-1519183071298-a2962be90b8e', 'Used - Good', '2025-11-01 10:13:15'),
	(97, 3, 'Bluetooth Speaker', 10, 'Portable waterproof Bluetooth speaker.', 'Electronics', 4800, 'https://images.unsplash.com/photo-1588854337236-6889d631faa8', 'New', '2025-11-01 10:13:15'),
	(98, 1, 'Running Shorts', 12, 'Breathable men’s running shorts, size M.', 'Clothing', 1300, 'https://images.unsplash.com/photo-1600185365483-f471e4728348', 'New', '2025-11-01 10:13:15'),
	(99, 3, 'Gaming Mouse', 6, 'RGB gaming mouse with 7 programmable buttons.', 'Electronics', 2600, 'https://images.unsplash.com/photo-1593642532973-d31b6557fa68', 'New', '2025-11-01 10:13:15'),
	(100, 1, 'Cookware Set', 5, 'Non-stick cookware set with 5 pieces.', 'Appliances', 8700, 'https://images.unsplash.com/photo-1621905252472-46f83b5c1c68', 'New', '2025-11-01 10:13:15'),
	(101, 3, 'Women’s Handbag', 8, 'Stylish handbag with shoulder strap.', 'Accessories', 3500, 'https://images.unsplash.com/photo-1598033129183-cd6c9b95c1a0', 'New', '2025-11-01 10:13:15'),
	(107, 1, 'ajda', 2, 'sdasa', 'Electronics', 1234, 'images/items/item_6906049e8d08c.png', 'Like New', '2025-11-01 13:01:18'),
	(108, 1, 'Hoodie', 1, 'A Naruto hoodie from Otamatsuri anime convention', 'Clothing', 3500, 'images/items/item_69074a7e92201.jpg', 'New', '2025-11-02 12:11:42');

-- Data for table `orders`
DELETE FROM `orders`;


/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;