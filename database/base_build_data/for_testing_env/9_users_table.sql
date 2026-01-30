--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `referred_by`, `provider`, `provider_id`, `refresh_token`, `access_token`, `user_type`,
                     `name`, `email`, `email_verified_at`, `verification_code`, `new_email_verificiation_code`,
                     `password`, `remember_token`, `device_token`, `avatar`, `avatar_original`, `address`, `country`,
                     `state`, `city`, `postal_code`, `phone`, `balance`, `banned`, `is_suspicious`, `referral_code`,
                     `customer_package_id`, `remaining_uploads`, `created_at`, `updated_at`)
VALUES (3, NULL, NULL, NULL, NULL, NULL, 'seller', 'Mr. Seller', 'seller@example.com', '2018-12-11 18:00:00', NULL,
        NULL, '$2y$10$eUKRlkmm2TAug75cfGQ4i.WoUbcJ2uVPqUlVkox.cv4CCyGEIMQEm',
        'UzM5bSoEfKPZ18wsKeg7JVpckN3kd7bamZR4Z1XBcp6j6q8blylLg3jIdePj', NULL,
        'https://lh3.googleusercontent.com/-7OnRtLyua5Q/AAAAAAAAAAI/AAAAAAAADRk/VqWKMl4f8CI/photo.jpg?sz=50', NULL,
        'Demo address', 'US', NULL, 'Demo city', '1234', NULL, 0.00, 0, 0, '3dLUoHsR1l', NULL, NULL,
        '2018-10-07 04:42:57', '2020-03-05 01:33:22'),

       (4, NULL, NULL, NULL, NULL, NULL, 'seller', 'Samuel Hoffman', 'seller1@example.com', '2023-12-12 15:12:54', NULL,
        NULL, '$2y$10$6Mhzu/GS3wbc1DFHwB9duOBA3Rayqna5F3li/kyVBCWC.nUgpU9Ge', NULL, NULL, NULL, '23', NULL, NULL, NULL,
        NULL, NULL, NULL, 0.00, 0, 0, NULL, NULL, 0, '2023-12-12 15:06:54', '2023-12-12 15:07:28'),

       (5, NULL, NULL, NULL, NULL, NULL, 'seller', 'Gareth Gilbert', 'seller2@example.com', '2024-01-09 12:01:59', NULL,
        NULL, '$2y$10$1h1Ym/pjUlG5XGfgNrMDMuygwY99du9MvnQWCNYhh1TlsNT3uirm6', NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, 0.00, 0, 0, NULL, NULL, 0, '2024-01-09 12:13:59', '2024-01-09 12:13:59'),

       (6, NULL, NULL, NULL, NULL, NULL, 'seller', 'Rahim Underwood', 'seller3@example.com', '2024-01-09 12:01:40',
        NULL, NULL, '$2y$10$cE5mfsKm9LFDkv8MGEMtlu.Pe/X01OfhSwrfSxAVbGOa9Vl5BmOPe', NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, 0.00, 0, 0, NULL, NULL, 0, '2024-01-09 12:16:40', '2024-01-09 12:16:40'),

       (7, NULL, NULL, NULL, NULL, NULL, 'seller', 'Deanna Velez', 'seller4@example.com', '2024-01-09 12:01:07', NULL,
        NULL, '$2y$10$QCsxNi3afWQ6khhGN3hatuf1cDZO4WNJLR4LSqiBYmm4HLrGmaAzy', NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, 0.00, 0, 0, NULL, NULL, 0, '2024-01-09 12:18:07', '2024-01-09 12:18:07'),

       (8, NULL, NULL, NULL, NULL, NULL, 'customer', 'Mr. Customer', 'customer@example.com', '2018-12-11 18:00:00',
        NULL, NULL, '$2y$10$eUKRlkmm2TAug75cfGQ4i.WoUbcJ2uVPqUlVkox.cv4CCyGEIMQEm',
        '9ndcz5o7xgnuxctJIbvUQcP41QKmgnWCc7JDSnWdHOvipOP2AijpamCNafEe', NULL,
        'https://lh3.googleusercontent.com/-7OnRtLyua5Q/AAAAAAAAAAI/AAAAAAAADRk/VqWKMl4f8CI/photo.jpg?sz=50', NULL,
        'Demo address', 'US', NULL, 'Demo city', '1234', NULL, 0.00, 0, 0, '8zJTyXTlTT', NULL, NULL,
        '2018-10-07 04:42:57', '2020-03-03 04:26:11'),

       (9, NULL, NULL, NULL, NULL, NULL, 'admin', 'Ahmed', 'Ahmedkalash513@gmail.com', '2026-01-17 15:01:20', NULL,
        NULL, '$2y$10$Cu.uFDmdH7ac9QOzO5b06O4.y7IR373m/BfvDFPtVhRishB9nYi1S',
        'Zql14zHdae0LZjMuBQGlhkApqtSz3AQFapKdGlI7kTiP2oWM3Gl5DIwyokX4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 0.00, 0, 0, NULL, NULL, 0, '2026-01-17 15:47:20', '2026-01-17 15:47:20');
