-- ============================================
-- JPC DATABASE FIX v2.5.37
-- Manual SQL Commands
-- ============================================
-- 
-- INSTRUCTIONS:
-- 1. Go to phpMyAdmin in your hosting control panel
-- 2. Select your WordPress database
-- 3. Click "SQL" tab
-- 4. Copy and paste these commands
-- 5. Click "Go"
-- 
-- NOTE: Replace 'wp_' with your actual table prefix if different
-- ============================================

-- Add enable_making_charge column
ALTER TABLE `wp_jpc_metal_groups` 
ADD COLUMN `enable_making_charge` tinyint(1) DEFAULT 1 
AFTER `unit`;

-- Add making_charge_type column
ALTER TABLE `wp_jpc_metal_groups` 
ADD COLUMN `making_charge_type` varchar(20) DEFAULT 'percentage' 
AFTER `enable_making_charge`;

-- Add enable_wastage_charge column
ALTER TABLE `wp_jpc_metal_groups` 
ADD COLUMN `enable_wastage_charge` tinyint(1) DEFAULT 1 
AFTER `making_charge_type`;

-- Add wastage_charge_type column
ALTER TABLE `wp_jpc_metal_groups` 
ADD COLUMN `wastage_charge_type` varchar(20) DEFAULT 'percentage' 
AFTER `enable_wastage_charge`;

-- ============================================
-- VERIFICATION QUERY
-- Run this after the above commands to verify:
-- ============================================

SHOW COLUMNS FROM `wp_jpc_metal_groups`;

-- You should see these columns in the results:
-- - enable_making_charge (tinyint(1))
-- - making_charge_type (varchar(20))
-- - enable_wastage_charge (tinyint(1))
-- - wastage_charge_type (varchar(20))

-- ============================================
-- DONE!
-- After running these commands:
-- 1. Clear all caches
-- 2. Go to Jewellery Price → Metals
-- 3. Should work perfectly now!
-- ============================================
