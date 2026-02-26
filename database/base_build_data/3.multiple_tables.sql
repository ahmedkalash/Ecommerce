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
--
-- Dumping data for table `brands`
-- OMITTED SEED DATA FOR brands
--
--
-- Truncate table before insert `brand_translations`
--
/*
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
*/

--
-- Truncate table before insert `categories`
--
--
-- Dumping data for table `categories`
-- OMITTED SEED DATA FOR categories
--
