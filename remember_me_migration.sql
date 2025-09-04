-- Migration script to add remember_token to existing admins table
-- Run this if you already have the portfolio database set up

USE portfolio;

-- Add remember_token column to admins table
ALTER TABLE admins 
ADD COLUMN remember_token VARCHAR(64) DEFAULT NULL,
ADD COLUMN remember_token_created TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Add index for faster remember_token lookups
CREATE INDEX idx_remember_token ON admins(remember_token);

-- Show the updated table structure
DESCRIBE admins;
