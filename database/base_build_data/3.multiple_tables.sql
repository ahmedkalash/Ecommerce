--
-- Truncate table before insert `attributes`
--

TRUNCATE TABLE `attributes`;
--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `name`, `created_at`, `updated_at`)
VALUES (1, 'Size', '2020-02-24 05:55:07', '2020-02-24 05:55:07'),
       (2, 'Fabric', '2020-02-24 05:55:13', '2020-02-24 05:55:13');


-- Truncate table before insert `blogs`
--

TRUNCATE TABLE `blogs`;
--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `category_id`, `title`, `slug`, `short_description`, `description`, `banner`, `meta_title`,
                     `meta_img`, `meta_description`, `meta_keywords`, `status`, `created_at`, `updated_at`,
                     `deleted_at`)
VALUES (1, 1, 'T-Shirts Every Man Needs in His Wardrobe', 't-shirts-every-man-needs-in-his-wardrobe',
        'Let’s start off with White is a color that suits everyone, but it can be slightly tricky to pick the right one, and style it correctly too.\r\n\r\nIf you think that plain white shirts arent meant for you, then we suggest you go for patterned ones perhaps stripes or checks.\r\n\r\nThen pair it with blue or black jeans and you’re all set to rock your white polo shirt!\r\n\r\nSpeaking of colored t-shirts, are always a safe bet too. When in doubt, go for blue! Thats our motto, simply because you cant go wrong with blue color when its men.',
        '<div class=\"wp-block-media-text alignwide\" style=\"color: rgb(27, 27, 40); font-size: 13px; grid-template-columns: 43% auto;\"><div class=\"wp-block-media-text__content\"><p>Let’s start off with White is a color that suits everyone, but it can be slightly tricky to pick the right one, and style it correctly too.</p><p>If you think that plain white shirts arent meant for you, then we suggest you go for patterned ones perhaps stripes or checks.</p><p>Then pair it with blue or black jeans and you’re all set to rock your white polo shirt!</p></div></div><p style=\"color: rgb(27, 27, 40); font-size: 13px;\">Speaking of colored t-shirts, are always a safe bet too. When in doubt, go for blue! Thats our motto, simply because you cant go wrong with blue color when its men.Whether you want to go for a button-down shirt, a long sleeve t-shirts or a short-sleeve t-shirts, blue or&nbsp;<a rel=\"noreferrer noopener\" href=\"https://www.daraz.pk/tag/mens-navy-blue-t-shirts/?utm_source=blog&amp;utm_medium=community&amp;utm_campaign=https://blog.daraz.pk/9-t-shirts-every-man-needs-in-his-wardrobe/?spm=a2a0e.blog-315315-13216.0.0.5cef345fi5VKih\" target=\"_blank\" style=\"background-color: rgb(255, 255, 255);\"><span style=\"font-weight: bolder;\"><em>navy blue t-shirts</em></span></a>&nbsp;would never disappoint.</p><div class=\"wp-block-media-text alignwide has-media-on-the-right\" data-spm-anchor-id=\"a2a0e.blog-1010-22609.0.i6.101e4da1pwRLNC\" style=\"color: rgb(27, 27, 40); font-size: 13px; grid-template-columns: auto 46%;\"><div class=\"wp-block-media-text__content\"><p>So dont have any second thoughts about this were giving you golden advice and nothing else!</p></div></div><p style=\"color: rgb(27, 27, 40); font-size: 13px;\">Now lets talk about other solid color jerseys. Plain casual t-shirts in solid colors can be your go-to look</p><div class=\"wp-block-media-text alignwide has-media-on-the-right\" data-spm-anchor-id=\"a2a0e.blog-1010-22609.0.i6.101e4da1pwRLNC\" style=\"color: rgb(27, 27, 40); font-size: 13px; grid-template-columns: auto 46%;\"><div class=\"wp-block-media-text__content\"><p>Whether you want to go for a button-down shirt, a long sleeve t-shirts or a short-sleeve t-shirts, blue or&nbsp;<a rel=\"noreferrer noopener\" href=\"https://www.daraz.pk/tag/mens-navy-blue-t-shirts/?utm_source=blog&amp;utm_medium=community&amp;utm_campaign=https://blog.daraz.pk/9-t-shirts-every-man-needs-in-his-wardrobe/?spm=a2a0e.blog-315315-13216.0.0.5cef345fi5VKih\" target=\"_blank\"><span style=\"font-weight: bolder;\"><em>navy blue t-shirts</em></span></a>&nbsp;would never disappoint.</p><p>So dont have any second thoughts about this were giving you golden advice and nothing else!</p></div></div><p style=\"color: rgb(27, 27, 40); font-size: 13px;\">Now lets talk about other solid color jerseys. Plain casual t-shirts in solid colors can be your go-to look</p>',
        164, NULL, NULL, NULL, NULL, 1, '2023-12-18 15:34:48', '2024-01-10 13:59:33', NULL);

--
-- Truncate table before insert `blog_categories`
--

TRUNCATE TABLE `blog_categories`;
--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `category_name`, `slug`, `created_at`, `updated_at`, `deleted_at`)
VALUES (1, 'T-Shirts Every Man Needs in His Wardrobe', 'T-Shirts-Every-Man-Needs-in-His-Wardrobe',
        '2023-12-18 15:32:46', '2023-12-18 15:32:46', NULL);

--
-- Truncate table before insert `brands`
--

TRUNCATE TABLE `brands`;
--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `logo`, `top`, `slug`, `meta_title`, `meta_description`, `created_at`, `updated_at`)
VALUES (1, 'Demo brand', 'uploads/brands/brand.jpg', 1, 'Demo-brand-12', 'Demo brand', NULL, '2019-03-12 06:05:56',
        '2019-08-06 06:52:40'),
       (2, 'Demo brand1', 'uploads/brands/brand.jpg', 1, 'Demo-brand1', 'Demo brand1', NULL, '2019-03-12 06:06:13',
        '2019-08-06 06:07:26'),
       (3, 'Audi', '128', 1, 'audi', 'Audi', NULL, '2019-03-12 04:05:56', '2024-01-09 10:44:14'),
       (4, 'BMW', '130', 1, 'bmw', 'BMW', NULL, '2019-03-12 04:06:13', '2024-01-09 10:47:27'),
       (5, 'Denim', '133', 0, 'denim-cpfva', 'Denim', 'Denim', '2023-12-12 13:32:22', '2024-01-09 11:27:39'),
       (6, 'lucky Brand', '139', 0, 'lucky-brand-nt3vj', 'lucky Brand', 'lucky Brand', '2023-12-12 13:33:41',
        '2024-01-09 11:35:37'),
       (7, 'Dior', '134', 0, 'dior-kblrr', 'Dior', 'Dior', '2023-12-12 14:00:42', '2024-01-09 11:29:02'),
       (8, 'Ford', '135', 0, 'ford-lwal0', 'Ford', 'Ford', '2023-12-12 14:01:32', '2024-01-09 11:29:43'),
       (9, 'Dell', '132', 0, 'dell-2z7sh', 'Dell', 'Dell', '2023-12-12 14:03:59', '2024-01-09 11:26:55'),
       (10, 'Honda', '136', 0, 'honda-8iwm4', NULL, NULL, '2023-12-12 14:04:54', '2024-01-09 11:32:10'),
       (11, 'Huwaei', '137', 0, 'huwaei-vzq1h', 'Huwaei', 'Huwaei', '2023-12-12 14:05:57', '2024-01-09 11:32:57'),
       (12, 'Apple', '127', 0, 'apple-di1qg', NULL, NULL, '2023-12-12 14:08:09', '2024-01-09 10:22:55'),
       (13, 'Canon', '131', 0, 'canon-m53og', 'Canon', 'Canon', '2023-12-12 14:08:55', '2024-01-09 11:26:13'),
       (14, 'Hyundai', '138', 0, 'hyundai-58tsa', 'Hyundai', 'Hyundai', '2023-12-12 14:09:59', '2024-01-09 11:33:51'),
       (15, 'Addidas', '125', 0, 'addidas-ysxch', 'Addidas', 'Addidas', '2023-12-12 14:10:57', '2024-01-09 10:09:32'),
       (16, 'Nike', '140', 0, 'nike-lgnbe', 'Nike', 'Nike', '2023-12-13 15:02:12', '2024-01-09 11:36:45'),
       (17, 'Baby Care', '126', 0, 'baby-care-pgtia', 'Baby Care', 'Baby Care', '2023-12-13 15:03:23',
        '2024-01-09 10:45:27'),
       (18, 'Baby & me', '124', 0, 'baby--me-hfay2', 'Baby & me', 'Baby & me', '2023-12-13 15:04:46',
        '2024-01-09 10:45:09'),
       (19, 'Baby TV', '129', 0, 'baby-tv-ckcqw', 'Baby TV', 'Baby TV', '2023-12-13 15:06:01', '2024-01-09 10:46:06'),
       (20, 'Nissan', '141', 0, 'nissan-uonoy', 'Nissan', 'Nissan', '2023-12-13 15:10:42', '2024-01-09 11:37:35'),
       (21, 'One Plus', '142', 0, 'one-plus-zx0wj', 'One Plus', 'One Plus', '2023-12-13 15:15:55',
        '2024-01-09 11:38:30'),
       (22, 'Pampers', '143', 0, 'pampers-fwaxx', 'Pampers', 'Pampers', '2023-12-13 15:17:33', '2024-01-09 11:39:31'),
       (23, 'Tanishq', '144', 0, 'tanishq-qfcjx', 'Tanishq', 'Tanishq', '2023-12-13 15:25:21', '2024-01-09 11:40:17');

--
-- Truncate table before insert `brand_translations`
--

TRUNCATE TABLE `brand_translations`;
--
-- Dumping data for table `brand_translations`
--

INSERT INTO `brand_translations` (`id`, `brand_id`, `name`, `lang`, `created_at`, `updated_at`)
VALUES (1, 3, 'Audi', 'en', '2023-12-12 13:30:13', '2023-12-12 13:30:13'),
       (2, 4, 'BMW', 'en', '2023-12-12 13:31:28', '2023-12-12 13:31:28'),
       (3, 5, 'Denim', 'en', '2023-12-12 13:32:23', '2023-12-12 13:32:23'),
       (4, 6, 'lucky Brand', 'en', '2023-12-12 13:33:41', '2023-12-12 13:33:41'),
       (5, 7, 'Dior', 'en', '2023-12-12 14:00:42', '2023-12-12 14:00:42'),
       (6, 8, 'Ford', 'en', '2023-12-12 14:01:32', '2023-12-12 14:01:32'),
       (7, 9, 'Dell', 'en', '2023-12-12 14:03:59', '2023-12-12 14:03:59'),
       (8, 10, 'Honda', 'en', '2023-12-12 14:04:54', '2023-12-12 14:04:54'),
       (9, 11, 'Huwaei', 'en', '2023-12-12 14:05:57', '2023-12-12 14:05:57'),
       (10, 12, 'Apple', 'en', '2023-12-12 14:08:09', '2023-12-12 14:08:09'),
       (11, 13, 'Canon', 'en', '2023-12-12 14:08:55', '2023-12-12 14:08:55'),
       (12, 14, 'Hyundai', 'en', '2023-12-12 14:09:59', '2023-12-12 14:09:59'),
       (13, 15, 'Addidas', 'en', '2023-12-12 14:10:57', '2023-12-12 14:10:57'),
       (14, 16, 'Nike', 'en', '2023-12-13 15:02:12', '2023-12-13 15:02:12'),
       (15, 17, 'Baby Care', 'en', '2023-12-13 15:03:23', '2023-12-13 15:03:23'),
       (16, 18, 'Baby & me', 'en', '2023-12-13 15:04:46', '2023-12-13 15:04:46'),
       (17, 19, 'Baby TV', 'en', '2023-12-13 15:06:01', '2023-12-13 15:06:01'),
       (18, 20, 'Nissan', 'en', '2023-12-13 15:10:42', '2023-12-13 15:10:42'),
       (19, 21, 'One Plus', 'en', '2023-12-13 15:15:55', '2023-12-13 15:15:55'),
       (20, 22, 'Pampers', 'en', '2023-12-13 15:17:33', '2023-12-13 15:17:33'),
       (21, 23, 'Tanishq', 'en', '2023-12-13 15:25:21', '2023-12-13 15:25:21');


--
-- Truncate table before insert `categories`
--

TRUNCATE TABLE `categories`;
--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `commision_rate`, `banner`, `icon`, `cover_image`, `featured`,
                          `top`, `digital`, `slug`, `refund_request_time`, `meta_title`, `meta_description`,
                          `created_at`, `updated_at`)
VALUES (1, NULL, 'Demo category 1', 0.00, 'uploads/categories/banner/category-banner.jpg',
        'uploads/categories/icon/KjJP9wuEZNL184XVUk3S7EiZ8NnBN99kiU4wdvp3.png', NULL, 1, 1, 0, 'Demo-category-1', NULL,
        'Demo category 1', NULL, '2019-08-06 12:06:58', '2019-08-06 06:06:58'),
       (2, NULL, 'Demo category 2', 0.00, 'uploads/categories/banner/category-banner.jpg',
        'uploads/categories/icon/h9XhWwI401u6sRoLITEk9SUMRAlWN8moGrpPfS6I.png', NULL, 1, 0, 0, 'Demo-category-2', NULL,
        'Demo category 2', NULL, '2019-08-06 12:06:58', '2019-08-06 06:06:58'),
       (3, NULL, 'Demo category 3', 0.00, 'uploads/categories/banner/category-banner.jpg',
        'uploads/categories/icon/rKAPw5rNlS84JtD9ZQqn366jwE11qyJqbzAe5yaA.png', NULL, 1, 1, 0, 'Demo-category-3', NULL,
        'Demo category 3', NULL, '2019-08-06 12:06:58', '2019-08-06 06:06:58'),
       (4, NULL, 'Men Clothing & Fashion', 0.00, '118', '119', '120', 1, 1, 0, 'men\'s fashion',
        NULL, 'Men\'s Fashion', 'Men\'s Fashion', '2024-01-08 08:23:52', '2024-01-08 13:23:52'),
       (5, NULL, 'Women Clothing & Fashion', 0.00, '115', '117', '116', 1, 0, 0,
        'women\'s fashion', NULL, 'Women\'s Fashion', 'Women\'s Fashion', '2024-01-08 08:21:46', '2024-01-08 13:21:46'),
       (6, NULL, 'Motor Bike Accessories', 0.00, 'uploads/categories/banner/category-banner.jpg',
        '157', NULL, 0, 1, 0, 'demo-category-3', NULL, 'Demo category 3', NULL, '2024-01-09 09:14:55',
        '2024-01-09 14:14:55'),
       (7, NULL, 'Smartphone Accessories', 0.00, NULL, '158', '155', 0, 0, 0,
        'smartphone-accessories-ww79z', NULL, NULL, 'Smartphone Accessories', '2024-01-09 09:16:16',
        '2024-01-09 14:16:16'),
       (8, NULL, 'Car Accessories', 0.00, '121', '122', '123', 1, 0, 0, 'car-accessories-wdgbh',
        NULL, 'Car Accessories', 'Car Accessories', '2024-01-08 09:09:00', '2024-01-08 14:09:00'),
       (9, 2, 'Hot Categories', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Hot-Categories-e5aCE', NULL,
        'Hot Categories', 'Hot Categories', '2023-12-12 14:48:35', '2023-12-12 14:48:35'),
       (10, 2, 'Wedding & events', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Wedding--events-IqPiS',
        NULL, 'Wedding & events', 'Wedding & events', '2023-12-12 14:48:56', '2023-12-12 14:48:56'),
       (11, 1, 'Outwear & jackets', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Outwear--jackets-Wn647',
        NULL, 'Outwear & jackets', 'Outwear & jackets', '2023-12-12 14:49:27', '2023-12-12 14:49:27'),
       (12, 1, 'Underwear & Loungewear Accessories', 0.00, NULL, NULL, NULL, 0, 0, 0,
        'Underwear--Loungewear-Accessories-mLj5J', NULL, 'Underwear & Loungewear Accessories',
        'Underwear & Loungewear Accessories', '2023-12-12 14:51:37', '2023-12-12 14:51:37'),
       (13, 4, 'Premium Ultrabook', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Premium-Ultrabook-pGmo1',
        NULL, NULL, NULL, '2023-12-12 14:56:04', '2023-12-12 14:56:04'),
       (14, 4, 'Laptop Accessories', 0.00, NULL, NULL, NULL, 0, 0, 0,
        'Laptop-Accessories-xjVRo', NULL, NULL, 'Laptop Accessories', '2023-12-12 14:56:43', '2023-12-12 14:56:43'),
       (15, 5, 'Floor Mats', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Floor-Mats-Y6Dl8', NULL,
        'Floor Mats', 'Floor Mats', '2023-12-12 14:58:41', '2023-12-12 14:58:41'),
       (16, 5, 'Vacuum cleaners', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Vacuum-cleaners-3c7d5',
        NULL, 'Vacuum cleaners', 'Vacuum cleaners', '2023-12-12 14:59:05', '2023-12-12 14:59:05'),
       (17, 3, 'Alarm', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Alarm-OFhKB', NULL, NULL, 'Alarm',
        '2023-12-12 15:00:15', '2023-12-12 15:00:15'),
       (18, 3, 'Motorcycle Stand', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Motorcycle-Stand-rzJwJ',
        NULL, 'Motorcycle Stand', 'Motorcycle Stand', '2023-12-12 15:01:01', '2023-12-12 15:01:01'),
       (19, NULL, 'Outdoor & Patio', 0.00, NULL, '160', NULL, 0, 0, 0, 'outdoor--patio-xuvpy',
        NULL, 'Outdoor & Patio', 'Outdoor & Patio', '2024-01-09 09:18:40', '2024-01-09 14:18:40'),
       (20, NULL, 'Kitchen', 0.00, NULL, '161', '156', 0, 0, 0, 'kitchen-mhwhp', NULL, 'Kitchen',
        'Kitchen', '2024-01-09 09:20:18', '2024-01-09 14:20:18'),
       (21, NULL, 'Household Appliances', 0.00, NULL, '159', NULL, 0, 0, 0,
        'household-appliances-ymtvn', NULL, 'Household Appliances', 'Household Appliances', '2024-01-09 09:17:36',
        '2024-01-09 14:17:36'),
       (22, NULL, 'Bakery & Bread', 0.00, NULL, '162', NULL, 0, 0, 0, 'bakery--bread-kclhl', NULL,
        'Bakery & Bread', 'Bakery & Bread', '2024-01-09 09:23:05', '2024-01-09 14:23:05'),
       (23, NULL, 'Body Fitness', 0.00, NULL, '163', NULL, 0, 0, 0, 'body-fitness-873u8', NULL,
        NULL, NULL, '2024-01-09 09:25:46', '2024-01-09 14:25:46'),
       (24, NULL, 'Vitamins & Supplements', 0.00, NULL, '32', NULL, 0, 0, 0,
        'Vitamins--Supplements-lMmHi', NULL, 'Vitamins & Supplements', 'Vitamins & Supplements', '2023-12-13 08:52:07',
        '2023-12-13 08:52:07'),
       (25, NULL, 'Kids & Toy', 0.00, NULL, '33', NULL, 0, 0, 0, 'Kids--Toy-fRtrx', NULL,
        'Kids & Toy', 'Kids & Toy', '2023-12-13 08:59:02', '2023-12-13 08:59:02'),
       (26, 4, 'Power Bank', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Power-Bank-JPRi1', NULL,
        'Power Bank', 'Power Bank', '2023-12-13 10:15:38', '2023-12-13 10:15:38'),
       (27, 16, 'Outdoor Tables', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Outdoor-Tables-XUNz2', NULL,
        'Outdoor Tables', 'Outdoor Tables', '2023-12-13 10:32:02', '2023-12-13 10:32:02'),
       (28, 16, 'Outdoor Seating & Patio Chairs', 0.00, NULL, NULL, NULL, 0, 0, 0,
        'Outdoor-Seating--Patio-Chairs-lwnEC', NULL, 'Outdoor Seating & Patio Chairs', 'Outdoor Seating & Patio Chairs',
        '2023-12-13 10:32:50', '2023-12-13 10:32:50'),
       (29, 17, 'Sinks and kitchen taps', 0.00, NULL, NULL, NULL, 0, 0, 0,
        'Sinks-and-kitchen-taps-Ykj7z', NULL, 'Sinks and kitchen taps', 'Sinks and kitchen taps', '2023-12-13 10:34:27',
        '2023-12-13 10:34:27'),
       (30, 17, 'Kitchen rugs', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Kitchen-rugs-QeV5E', NULL,
        'Kitchen rugs', 'Kitchen rugs', '2023-12-13 10:35:18', '2023-12-13 10:35:18'),
       (31, 17, 'Kitchen rugs', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Kitchen-rugs-cRqcz', NULL,
        'Kitchen rugs', 'Kitchen rugs', '2023-12-13 10:38:29', '2023-12-13 10:38:29'),
       (32, 17, 'Pot Holders', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Pot-Holders-29Sty', NULL,
        'Pot Holders', 'Pot Holders', '2023-12-13 10:38:55', '2023-12-13 10:38:55'),
       (33, 18, 'Paper & Plastic', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Paper--Plastic-HuMXb',
        NULL, 'Paper & Plastic', 'Paper & Plastic', '2023-12-13 10:41:49', '2023-12-13 10:41:49'),
       (34, 18, 'Cleaning Supplies', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Cleaning-Supplies-AArBZ',
        NULL, 'Cleaning Supplies', 'Cleaning Supplies', '2023-12-13 10:42:26', '2023-12-13 10:42:26'),
       (35, 19, 'Donuts & Breakfast Pastries', 0.00, NULL, NULL, NULL, 0, 0, 0,
        'Donuts--Breakfast-Pastries-oZNEQ', NULL, 'Donuts & Breakfast Pastries', 'Donuts & Breakfast Pastries',
        '2023-12-13 12:33:48', '2023-12-13 12:33:48'),
       (36, 19, 'Sliced Bread', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Sliced-Bread-Uipzc', NULL,
        'Sliced Bread', 'Sliced Bread', '2023-12-13 12:34:44', '2023-12-13 12:34:44'),
       (37, 20, 'Treadmill', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Treadmill-FL6lw', NULL,
        'Treadmill', 'Treadmill', '2023-12-13 12:35:48', '2023-12-13 12:35:48'),
       (38, 20, 'Dumbbells', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Dumbbells-QpPUl', NULL,
        'Dumbbells', 'Dumbbells', '2023-12-13 12:36:31', '2023-12-13 12:36:31'),
       (39, 20, 'Exercise Balls', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Exercise-Balls-Sr2pj', NULL,
        'Exercise Balls', 'Exercise Balls', '2023-12-13 12:37:08', '2023-12-13 12:37:08'),
       (40, 21, 'Energy Drinks', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Energy-Drinks-AAupX', NULL,
        'Energy Drinks', 'Energy Drinks', '2023-12-13 12:39:45', '2023-12-13 12:39:45'),
       (41, 21, 'Protein Powder', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Protein-Powder-6jXHp', NULL,
        'Protein Powder', 'Protein Powder', '2023-12-13 12:40:32', '2023-12-13 12:40:32'),
       (42, 21, 'multivitamin', 0.00, NULL, NULL, NULL, 0, 0, 0, 'multivitamin-DN1lF', NULL,
        'multivitamin', 'multivitamin', '2023-12-13 12:41:15', '2023-12-13 12:41:15'),
       (43, 22, 'Baby Clothing', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Baby-Clothing-LKjwK', NULL,
        'Baby Clothing', 'Baby Clothing', '2023-12-13 12:45:55', '2023-12-13 12:45:55'),
       (44, 22, 'Boys Clothing', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Boys-Clothing-4ZCFF', NULL,
        'Boys Clothing', 'Boys Clothing', '2023-12-13 12:50:29', '2023-12-13 12:50:29'),
       (45, 22, 'Girls Clothing', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Girls-Clothing-kKtir', NULL,
        'Girls Clothing', 'Girls Clothing', '2023-12-13 12:56:31', '2023-12-13 12:56:31'),
       (46, 2, 'Accessories', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Accessories-a83xY', NULL,
        'Accessories', 'Accessories', '2023-12-13 14:11:51', '2023-12-13 14:11:51'),
       (47, 1, 'Bottom', 0.00, NULL, NULL, NULL, 0, 0, 0, 'Bottom-KVdCm', NULL, 'Bottom',
        'Bottom', '2023-12-13 14:16:21', '2023-12-13 14:16:21');
