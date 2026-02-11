# Migration v2.5.10 - Additional Cost Fields Fix

## Problem
The Additional Cost Fields (Pearl Cost, Stone Cost, Extra Fee) were not showing in the frontend price breakup because of a meta key mismatch:

- **Product Editor** was saving: `_jpc_pearl_cost`, `_jpc_stone_cost`, `_jpc_extra_fee`
- **Calculator** was expecting: `_jpc_pearl_cost_value` + `_jpc_pearl_cost_type`, etc.

## Solution
This migration converts existing data from the old format to the new format that supports both fixed prices and percentages.

## Files Changed

### 1. **templates/admin/product-meta-box.php** ✅ UPDATED
- Changed input fields to use `_value` and `_type` suffixes
- Added hidden inputs for type from settings
- Shows correct labels from settings

### 2. **includes/class-jpc-data-migration-v2510.php** ✅ CREATED
- Migration class that converts old meta keys to new format
- Deletes old meta keys after migration
- Regenerates price breakups for all migrated products

### 3. **templates/admin/migration-v2510.php** ✅ CREATED
- Admin page to run the migration
- Shows migration status and progress
- Allows re-running if needed

### 4. **jewellery-price-calculator.php** ✅ UPDATED
- Added migration class to includes
- Added admin notice for pending migration
- Version bumped to 2.5.10

### 5. **includes/class-jpc-product-meta-box-v2.php** ⚠️ NEEDS MANUAL UPDATE
- Lines 338-340 need to be updated to save correct meta keys
- See `includes/class-jpc-product-meta-box-save-fix.php` for the correct code

### 6. **includes/class-jpc-admin.php** ⚠️ NEEDS MANUAL UPDATE
- Add migration menu item
- Add render function
- See `includes/admin-migration-menu-patch.txt` for instructions

## How to Apply the Fix

### Option 1: Automatic Migration (Recommended)

1. **Update the plugin files** (already done via commits)

2. **Manually update class-jpc-product-meta-box-v2.php**:
   - Replace lines 338-340 with the code from `includes/class-jpc-product-meta-box-save-fix.php`

3. **Manually update class-jpc-admin.php**:
   - Follow instructions in `includes/admin-migration-menu-patch.txt`

4. **Run the migration**:
   - Go to **Jewellery Price Calculator → ⚠️ Migration**
   - Click "Run Migration Now"
   - Wait for completion

5. **Verify**:
   - Go to any product with additional costs
   - Check the "Price Breakup" tab on the frontend
   - The fields should now show with correct labels

### Option 2: Manual Fix (Per Product)

If you prefer not to run the migration:

1. Go to each product with additional costs
2. Re-enter the values in the "Other Costs" section
3. Click "Update" to save
4. The price breakup will be automatically regenerated

## What Gets Migrated

For each product with additional costs:

| Old Meta Key | New Meta Keys |
|--------------|---------------|
| `_jpc_pearl_cost` | `_jpc_pearl_cost_value` + `_jpc_pearl_cost_type` |
| `_jpc_stone_cost` | `_jpc_stone_cost_value` + `_jpc_stone_cost_type` |
| `_jpc_extra_fee` | `_jpc_extra_fee_value` + `_jpc_extra_fee_type` |

The `_type` values are fetched from plugin settings:
- `jpc_pearl_cost_type` (default: 'fixed')
- `jpc_stone_cost_type` (default: 'fixed')
- `jpc_extra_fee_type` (default: 'fixed')

## Testing

After migration, test with a product:

1. **Backend Test**:
   - Edit a product
   - Enter values in "Test 6.1", "Test 7.1", "Test 8.1"
   - Save the product
   - Check that meta keys are saved correctly

2. **Frontend Test**:
   - View the product on frontend
   - Click "Price Breakup" tab
   - Verify that "Test 6.1", "Test 7.1", "Test 8.1" show with correct values

## Rollback

If something goes wrong:

1. The old meta keys are deleted after migration
2. To rollback, you'll need to manually re-enter values in each product
3. Or restore from a database backup taken before migration

## Support

If you encounter issues:

1. Check the migration log in WordPress admin
2. Verify that all files were updated correctly
3. Check for PHP errors in debug.log
4. Contact support with error details

## Version History

- **v2.5.10**: Fixed Additional Cost Fields meta key mismatch
- **v2.5.9**: Previous version with the bug
