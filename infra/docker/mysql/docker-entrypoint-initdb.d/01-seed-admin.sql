-- PatchStash bootstrap seed
-- This file is executed only when the MySQL data directory is first created.
-- It waits for the `user` table to exist (created by Doctrine migrations)
-- and then upserts a default admin/admin account you can change later.

CREATE DATABASE IF NOT EXISTS `patchstash`;
USE `patchstash`;

-- Make sure scheduled events can run so we can defer seeding until after migrations.
SET GLOBAL event_scheduler = ON;

SET @password_hash = '$2b$10$5iINcCbHor4KgyLdG6MhPO25J//qHEUxP4NUXMkYxHeNhnBjCXL.G';

DROP EVENT IF EXISTS seed_patchstash_admin;
DELIMITER $$
CREATE EVENT seed_patchstash_admin
    ON SCHEDULE EVERY 30 SECOND
    STARTS CURRENT_TIMESTAMP + INTERVAL 10 SECOND
    ENDS CURRENT_TIMESTAMP + INTERVAL 1 HOUR
    DO
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name = 'user'
    ) THEN
        INSERT INTO `user` (
            username,
            email,
            password,
            role,
            is_verified,
            verification_code,
            verification_expires_at,
            data,
            reset_token,
            reset_expires_at
        ) VALUES (
            'admin',
            'admin@example.com',
            @password_hash,
            'ROLE_ADMIN',
            1,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL
        )
        ON DUPLICATE KEY UPDATE
            email = VALUES(email),
            password = VALUES(password),
            role = VALUES(role),
            is_verified = VALUES(is_verified),
            reset_token = VALUES(reset_token),
            reset_expires_at = VALUES(reset_expires_at);
    END IF;
END$$
DELIMITER ;

-- Update @password_hash above to change the admin password.
