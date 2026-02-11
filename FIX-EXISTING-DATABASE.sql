-- ============================================================================
-- FIX EXISTING DATABASE - Enable Making/Wastage Charges for Metal Groups
-- ============================================================================
-- 
-- PROBLEM: Existing metal groups have enable_making_charge and 
--          enable_wastage_charge set to 0 (disabled) by default
--
-- SOLUTION: Update all existing metal groups to enable both charges
--
-- HOW TO RUN:
-- 1. Go to phpMyAdmin or your database management tool
-- 2. Select your WordPress database
-- 3. Click "SQL" tab
-- 4. Copy and paste this entire script
-- 5. Click "Go" to execute
--
-- OR use WP-CLI:
-- wp db query < FIX-EXISTING-DATABASE.sql
--
-- ============================================================================

-- Update all metal groups to enable making and wastage charges
UPDATE wp_jpc_metal_groups 
SET 
    enable_making_charge = 1,
    enable_wastage_charge = 1
WHERE 
    enable_making_charge = 0 
    OR enable_wastage_charge = 0;

-- Verify the update
SELECT 
    id,
    name,
    unit,
    enable_making_charge,
    enable_wastage_charge,
    making_charge_type,
    wastage_charge_type
FROM wp_jpc_metal_groups;

-- ============================================================================
-- EXPECTED RESULT:
-- All metal groups should now show:
-- - enable_making_charge = 1
-- - enable_wastage_charge = 1
-- ============================================================================
