# 🔧 How to Fix Additional Cost Fields - For Non-Coders

## The Problem
Your "Test 6.1", "Test 7.1", and "Test 8.1" fields are not showing in the price breakup on your website.

## The Solution (Super Simple!)
I've created a magic file that will fix everything automatically. Just follow these steps:

---

## 📋 Step-by-Step Instructions

### Step 1: Download the Fix File
1. Go to your GitHub repository
2. Find the file called: **`fix-additional-costs-SIMPLE.php`**
3. Click on it
4. Click the "Download" button (or right-click "Raw" and save)
5. Save it to your computer

### Step 2: Upload to Your Website
You can use either **FTP** or **File Manager** (whichever you're comfortable with):

#### Option A: Using File Manager (Easier)
1. Log in to your hosting control panel (cPanel, Plesk, etc.)
2. Open "File Manager"
3. Navigate to: `public_html/wp-content/plugins/jewellery-price-calculator/`
4. Click "Upload"
5. Select the `fix-additional-costs-SIMPLE.php` file you downloaded
6. Wait for upload to complete

#### Option B: Using FTP (FileZilla, etc.)
1. Connect to your website using FTP
2. Navigate to: `/wp-content/plugins/jewellery-price-calculator/`
3. Upload the `fix-additional-costs-SIMPLE.php` file
4. Wait for upload to complete

### Step 3: Run the Fix
1. Open your web browser
2. Go to: `https://YOURSITE.com/wp-content/plugins/jewellery-price-calculator/fix-additional-costs-SIMPLE.php`
   - Replace `YOURSITE.com` with your actual website address
   - Example: `https://myjewellerystore.com/wp-content/plugins/jewellery-price-calculator/fix-additional-costs-SIMPLE.php`
3. You'll see a page with a big blue button that says "🚀 Start Migration Now"
4. Click the button
5. Wait for it to finish (you'll see a green "SUCCESS!" message)
6. **DO NOT close the page** until you see "SUCCESS!"

### Step 4: Delete the Fix File (Important!)
1. Go back to File Manager or FTP
2. Find the `fix-additional-costs-SIMPLE.php` file
3. Delete it (for security reasons)

### Step 5: Test Your Website
1. Go to any product on your website
2. Click the "Price Breakup" tab
3. You should now see "Test 6.1", "Test 7.1", and "Test 8.1" with their values!

---

## ✅ What This Fix Does

- Converts all your products to the new format
- Makes the additional cost fields show in price breakup
- Regenerates all price calculations
- **Safe to run** - doesn't delete any important data
- **Can be run multiple times** if needed

---

## ❓ Troubleshooting

### "I can't find the file on GitHub"
- The file is called: `fix-additional-costs-SIMPLE.php`
- It's in the main folder of your repository
- Look for files starting with "fix-"

### "I get a blank page when I run it"
- Make sure you're logged in to WordPress as an administrator
- Try adding `?run=1` to the end of the URL
- Example: `https://yoursite.com/wp-content/plugins/jewellery-price-calculator/fix-additional-costs-SIMPLE.php?run=1`

### "It says 'No Migration Needed'"
- This means your products are already in the correct format
- The problem might be something else
- Contact me for further help

### "Fields still don't show after migration"
- Clear your website cache (if you use a caching plugin)
- Clear your browser cache (Ctrl+F5 or Cmd+Shift+R)
- Wait 5 minutes and try again

### "I see errors during migration"
- Take a screenshot of the errors
- Send them to your developer or contact support
- The migration will still work for products without errors

---

## 🆘 Need More Help?

If you're stuck at any step:
1. Take a screenshot of where you're stuck
2. Note down any error messages you see
3. Contact your developer or hosting support
4. They can help you upload and run the file

---

## 📝 Technical Details (For Your Developer)

If you need to show this to a developer:

**What the fix does:**
- Migrates meta keys from old format to new format
- Old: `_jpc_pearl_cost`, `_jpc_stone_cost`, `_jpc_extra_fee`
- New: `_jpc_pearl_cost_value` + `_jpc_pearl_cost_type`, etc.
- Regenerates price breakups using `JPC_Price_Calculator::calculate_and_store_breakup()`

**File location:**
- Upload to: `/wp-content/plugins/jewellery-price-calculator/`
- Access via: `https://site.com/wp-content/plugins/jewellery-price-calculator/fix-additional-costs-SIMPLE.php`

**Safety:**
- Only updates products with old meta keys
- Deletes old keys after successful migration
- Can be run multiple times safely
- Requires admin login

---

## ✨ After the Fix

Once the fix is complete:
- Your "Test 6.1", "Test 7.1", "Test 8.1" will show in price breakup
- All product prices will be recalculated correctly
- Everything will work as expected
- You can delete the fix file

**That's it! You're done!** 🎉
