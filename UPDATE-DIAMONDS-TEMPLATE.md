# Update Diamonds Template to Include 4Cs

## Quick Update Required

To enable the new Diamond 4Cs attributes on the Diamonds (Legacy) page, you need to update ONE line in the admin class.

### File to Edit

`includes/class-jpc-admin.php`

### Find This Line (around line 480):

```php
public function render_diamonds() {
    include JPC_PLUGIN_DIR . 'templates/admin/diamonds.php';
}
```

### Replace With:

```php
public function render_diamonds() {
    include JPC_PLUGIN_DIR . 'templates/admin/diamonds-v2.php';
}
```

### That's It!

After making this change, the Diamonds (Legacy) page will now include:

✅ **Shape** dropdown (Round, Princess, Cushion, etc.)  
✅ **Colour** dropdown (D, E, F, G, H, I, J, K-M)  
✅ **Clarity** dropdown (FL, IF, VVS1, VVS2, VS1, VS2, SI1, SI2, I1-I3)  
✅ **Cut** dropdown (Excellent, Very Good, Good, Fair, Poor)  

All with live price adjustments shown!

### What You'll See

**Add Diamond Form:**
- Basic Information section
- **NEW: Diamond 4Cs Quality Attributes section** with all 4 dropdowns
- Certification & Pricing section

**Diamonds List:**
- New columns for Shape, Colour, Clarity, Cut
- Edit modal includes all 4Cs fields

### Alternative: Keep Both Templates

If you want to keep the old template as backup:

1. Rename `diamonds.php` to `diamonds-old.php`
2. Rename `diamonds-v2.php` to `diamonds.php`
3. No code changes needed!

---

**Ready to use!** The new template is already uploaded to GitHub.
