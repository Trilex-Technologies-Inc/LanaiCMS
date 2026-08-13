-- Add active status to the carousel table. Run this command once.
-- Change tbl_ln_ below if your installation uses a different table prefix.
ALTER TABLE `tbl_ln_banner`
    ADD COLUMN `banActive` ENUM('y', 'n') NOT NULL DEFAULT 'y'
    AFTER `banPosition`;
