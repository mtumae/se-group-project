-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
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

-- Dumping structure for table iap_db.categories
CREATE TABLE IF NOT EXISTS categories (
  category_name varchar(100) NOT NULL,
  PRIMARY KEY (category_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table iap_db.categories: ~8 rows (approximately)
DELETE FROM categories;
INSERT INTO categories (category_name) VALUES
	('Accessories'),
	('Appliances'),
	('Clothing'),
	('Electronics'),
	('Footwear'),
	('Furniture'),
	('Home Decor'),
	('Music');

-- Dumping structure for table iap_db.items
CREATE TABLE IF NOT EXISTS items (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(10) unsigned NOT NULL,
  item_name varchar(100) NOT NULL,
  quantity int(11) NOT NULL,
  item_description text DEFAULT NULL,
  item_category varchar(100) DEFAULT NULL,
  Price double DEFAULT NULL,
  ImageUrl varchar(1000) DEFAULT NULL,
  item_condition varchar(50) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY fk_user (user_id),
  KEY FK_items_categories (item_category),
  CONSTRAINT FK_items_categories FOREIGN KEY (item_category) REFERENCES categories (category_name) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_item_category FOREIGN KEY (item_category) REFERENCES categories (category_name) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT items_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table iap_db.items: ~13 rows (approximately)
DELETE FROM items;
INSERT INTO items (id, user_id, item_name, quantity, item_description, item_category, Price, ImageUrl, item_condition, created_at) VALUES
	(2, 3, 'iPhone 13 Pro', 5, '128GB, Graphite color, excellent con...', 'Electronics', 115000, 'https://images.unsplash.com/photo-1634568603766-8848d706a782?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTF8fGlwaG9uZSUyMDEzJTIwcHJvfGVufDB8fDB8fHww&auto=format&fit=crop&w=800&q=60', 'Used - Like New', '2025-10-31 21:00:00'),
	(4, 3, 'Wooden Study Desk', 7, 'Modern wooden desk for study or off...', 'Furniture', 14500, 'https://images.unsplash.com/photo-1596276707127-d4ee5c56d11a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8d29vZGVuJTIwc3R1ZHklMjBkZXNrfGVufDB8fDB8fHww&auto=format&fit=crop&w=800&q=60', 'New', '2025-10-31 21:00:00'),
	(6, 3, 'Kitchen Blender', 10, 'High-speed blender ideal for smoothi...', 'Appliances', 5500, 'https://images.unsplash.com/photo-1627914434913-9a3b6d21d607?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8a2l0Y2hlbiUyMGJsZW5kZXJ8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=800&q=60', 'New', '2025-10-31 21:00:00'),
	(8, 3, 'Gaming Chair', 2, 'Ergonomic gaming chair with adjusta...', 'Furniture', 25000, 'https://images.unsplash.com/photo-1616719875458-750ca8e09f5e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Z2FtaW5nJTIwY2hhaXJ8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=800&q=60', 'Used - Good', '2025-10-31 21:00:00'),
	(10, 3, 'Wireless Headphones', 9, 'Noise-cancelling Bluetooth headphones.', 'Electronics', 6500, 'https://images.unsplash.com/photo-1505740420928-5e560c06f2ae?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8d2lyZWxlc3MlMjBoZWFkcGhvbmVzfGVufDB8fDB8fHww&auto=format&fit=crop&w=800&q=60', 'Used - Excellent', '2025-10-31 21:00:00'),
	(12, 3, 'Apple Watch SE', 4, 'Smartwatch with health tracking and ...', 'Electronics', 42000, 'https://images.unsplash.com/photo-1601004890684-d6210214a1a5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8YXBwbGUlMjB3YXRjaCUyMHNlfGVufDB8fDB8fHww&auto=format&fit=crop&w=800&q=60', 'Used - Excellent', '2025-10-31 21:00:00'),
	(14, 3, 'Smart TV 55-inch', 2, '4K Ultra HD smart TV with built-in ap...', 'Electronics', 78000, 'https://images.unsplash.com/photo-1588647247715-4ad978519d1c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8c21hcnQlMjB0dnxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=800&q=60', 'Used - Like New', '2025-10-31 21:00:00'),
	(16, 3, 'Bluetooth Speaker', 10, 'Portable waterproof Bluetooth speaker.', 'Electronics', 4800, 'https://images.unsplash.com/photo-1545657069-7c1537237ce7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Ymx1ZXRvb3RoJTIwc3BlYWtlcnxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=800&q=60', 'New', '2025-10-31 21:00:00'),
	(18, 3, 'Gaming Mouse', 6, 'RGB gaming mouse with 7 program...', 'Electronics', 2600, 'https://images.unsplash.com/photo-1527864550417-7fd91ffc35b8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Z2FtaW5nJTIwbW91c2V8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&w=800&q=60', 'New', '2025-10-31 21:00:00'),
	(20, 3, 'Women\'s Handbag', 8, 'Stylish handbag with shoulder strap.', 'Accessories', 3500, 'https://images.unsplash.com/photo-1601925348325-1a86851b4731?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8d29tZW4lMjBoYW5kYmFnfGVufDB8fDB8fHww&auto=format&fit=crop&w=800&q=60', 'New', '2025-10-31 21:00:00'),
	(23, 1, 'Screenshot', 1, 'A screenshot', 'Accessories', 10000, 'images/items/item_690f7f2d52daa.png', 'New', '2025-11-08 17:34:37'),
	(26, 1, 'iPhone 16 Pro Max', 1, 'An out of the box new-like iPhone. Hardly used and only charged once', 'Electronics', 125000, 'images/items/item_69102374d19d2.jpg', 'Like New', '2025-11-09 05:15:32'),
	(27, 1, 'Hoodie', 1, 'Very warm. Slightly used', 'Clothing', 4500, 'images/items/item_691061c0a8b09.png', 'Good', '2025-11-09 09:41:20'),
	(28, 4, 'Couch cushion', 3, 'Some comfy cushions that have been slightly used fitting for making your hostel feel more like home', 'Home Decor', 2300, 'images/items/item_69108a6d34834.png', 'Fair', '2025-11-09 12:34:53');

-- Dumping structure for table iap_db.orders
CREATE TABLE IF NOT EXISTS orders (
  id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(10) unsigned NOT NULL,
  buyer_id int(10) unsigned NOT NULL,
  buyer_name varchar(255) DEFAULT NULL,
  buyer_email varchar(255) NOT NULL,
  buyer_phone varchar(255) DEFAULT NULL,
  pickup_time time DEFAULT NULL,
  notes text DEFAULT NULL,
  order_status enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  created_at datetime DEFAULT current_timestamp(),
  updated_at datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  KEY idx_buyer_id (buyer_id),
  KEY idx_item_id (item_id),
  KEY idx_order_status (order_status),
  KEY idx_created_at (created_at),
  CONSTRAINT orders_ibfk_1 FOREIGN KEY (item_id) REFERENCES items (id) ON DELETE CASCADE,
  CONSTRAINT orders_ibfk_2 FOREIGN KEY (buyer_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table iap_db.orders: ~2 rows (approximately)
DELETE FROM orders;
INSERT INTO orders (id, item_id, buyer_id, buyer_name, buyer_email, buyer_phone, pickup_time, notes, order_status, created_at, updated_at) VALUES
	(1, 26, 1, 'Jesse', 'kariukijesse4633@gmail.com', '0741999888', '10:32:00', 'Near Stc 3rd floor study are', 'pending', '2025-11-09 10:37:54', '2025-11-09 10:37:54'),
	(2, 23, 1, 'Jesse', 'kariukijesse4633@gmail.com', '0741999888', '11:43:00', '', 'pending', '2025-11-09 11:43:22', '2025-11-09 11:43:22'),
	(3, 23, 1, 'Jesse', 'kariukijesse4633@gmail.com', '0741999888', '11:43:00', '', 'pending', '2025-11-09 11:44:29', '2025-11-09 11:44:29'),
	(4, 23, 1, 'Jesse', 'kariukijesse4633@gmail.com', '0741999888', '11:50:00', '', 'pending', '2025-11-09 11:51:09', '2025-11-09 11:51:09'),
	(5, 27, 1, 'Jesse', 'kariukijesse4633@gmail.com', '0741999888', '12:43:00', 'I am  mostly at Siwaka. Meet me there', 'pending', '2025-11-09 12:43:49', '2025-11-09 12:43:49'),
	(6, 27, 1, 'Jesse', 'kariukijesse4633@gmail.com', '0741999888', '12:43:00', 'I am  mostly at Siwaka. Meet me there', 'pending', '2025-11-09 12:45:22', '2025-11-09 12:45:22'),
	(7, 27, 1, 'Jesse', 'kariukijesse4633@gmail.com', '0741999888', '12:43:00', 'I am  mostly at Siwaka. Meet me there', 'pending', '2025-11-09 12:45:36', '2025-11-09 12:45:36');

-- Dumping structure for table iap_db.roles
CREATE TABLE IF NOT EXISTS roles (
  id int(11) NOT NULL AUTO_INCREMENT,
  role_name varchar(100) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY role_name (role_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table iap_db.roles: ~0 rows (approximately)
DELETE FROM roles;

-- Dumping structure for table iap_db.users
CREATE TABLE IF NOT EXISTS users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  email varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  reset_code varchar(6) DEFAULT NULL,
  reset_code_expiry datetime DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY username (username),
  UNIQUE KEY email (email)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table iap_db.users: ~2 rows (approximately)
DELETE FROM users;
INSERT INTO users (id, username, email, password, created_at, updated_at, reset_code, reset_code_expiry) VALUES
	(1, 'Jesse Kariuki', 'kariukijesse4633@gmail.com', '$2y$10$rO8yoj4C0gSBfafw2O2N7.nX/iQMldgccwIdA2S2kdfPnSJ390fYe', '2025-10-27 13:57:02', '2025-10-27 13:57:02', NULL, NULL),
	(3, 'Jesse Kay', 'indradamenace@gmail.com', '$2y$10$q0wqWi4dQqqdZpaZZYZLk.4GKFEk11INd5j6sH6hhJNJ0SAQdplty', '2025-11-01 09:23:47', '2025-11-01 09:23:47', NULL, NULL),
	(4, 'Silly ', 'kariuki.jesse@strathmore.edu', '$2y$10$fyL7Dxc29XSS9Ivfma2mDeOE9UkaoyZtAIQPlaXRugDsYNEqkA3Ne', '2025-11-09 12:27:40', '2025-11-09 12:27:40', NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;