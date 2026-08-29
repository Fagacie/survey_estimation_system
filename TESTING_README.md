# 🎯 TESTING ROADMAP - ALL FIXES READY FOR VALIDATION

## 📍 Where to Start

You have **3 testing document options** depending on your time:

### ⚡ FAST TRACK (5-10 minutes)
👉 **[QUICK_TESTING_CHECKLIST.md](./QUICK_TESTING_CHECKLIST.md)**

5 essential tests covering all critical fixes:
1. Cost rate multiplier persistence
2. Survey line type preservation
3. Custom cost row addition
4. Report field display
5. Database columns

**Use this if:** You need quick validation before code review

---

### 📋 COMPREHENSIVE (20-30 minutes)
👉 **[TESTING_GUIDE.md](./TESTING_GUIDE.md)**

12 detailed test suites with step-by-step instructions:
- Test Suite 1: Cost Rate Multiplier (3 tests)
- Test Suite 2: Survey Line Preservation (5 tests)
- Test Suite 3: Custom Rows (3 tests)
- Test Suite 4: Report Display (1 test)
- Test Suite 5: Database Integrity (2 tests)
- Test Suite 6: End-to-End Integration
- Test Suite 7: Edge Cases

**Use this if:** You need thorough validation before deployment

---

### 🚀 DEPLOYMENT (Complete Workflow)
👉 **[DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)**

Complete deployment checklist including:
- Pre-deployment validation
- Functional testing roadmap
- Regression testing matrix
- Troubleshooting guide
- Sign-off checklist

**Use this if:** You're ready for production deployment

---

## 🛠️ Automated Validation Tool

### Run Database Validation Script

```bash
php artisan tinker
>>> include 'scripts/validate_fixes.php';
```

This script automatically validates:
- ✅ base_multiplier column exists
- ✅ days/units columns exist (quantity removed)
- ✅ GeoJSON line_type in properties
- ✅ All migrations completed
- ✅ No stale old columns

**Use this:** As first quick check after migration

---

## 📊 Quick Fix Reference

### Fix #1: Cost Rate Base Multiplier
**Files Changed:**
- `app/Models/CostRate.php` - Added to `$fillable`
- `app/Http/Controllers/SettingsController.php` - Validated multiplier values

**What to Test:**
1. Create cost rate with "Execution Days" multiplier
2. Verify it saves (not defaulted to "Total Duration")
3. Cost calculation uses it correctly

**Validation Command:**
```bash
php artisan tinker
>>> DB::table('cost_rates')->first()->base_multiplier;
// Expected: "Execution Days" (or other selected value)
```

---

### Fix #2: Survey Line Type Preservation
**Files Changed:**
- `resources/views/projects/show.blade.php` - Include line_type per feature
- `app/Services/Map/SurveyLineService.php` - Read line_type from properties

**What to Test:**
1. Draw boundary + generate main lines (RED should appear)
2. Generate cross lines (AMBER dashed should appear)
3. Save Planning
4. Reload page
5. Lines should stay RED (main) and AMBER (cross)

**Visual Validation:**
- Main lines: 🔴 RED solid
- Cross lines: 🟠 AMBER dashed

---

### Fix #3: Custom Cost Row Addition
**Files Changed:**
- `resources/views/projects/cost.blade.php` - Improved selector logic for row insertion

**What to Test:**
1. Go to Cost Estimation
2. Click "Add Custom Line Item"
3. Row should appear (not silently fail)
4. Fill values and verify calculation

**Expected:** Row adds instantly, calculation updates

---

### Fix #4: Report Field Display
**Files Changed:**
- `resources/views/reports/unified.blade.php` - Changed `quantity` to `days`

**What to Test:**
1. Download PDF report
2. Check Cost Breakdown table
3. Days/Qty column should show numbers (not blank)

**Expected:** All fields populated correctly

---

### Fix #5: Database & Migration Integrity
**Files Changed:**
- Multiple migrations for schema changes
- `database/migrations/2026_08_28_032548_restructure_database_for_multi_locations.php` - Documented as placeholder

**What to Test:**
1. Run `php artisan migrate:fresh`
2. All migrations should complete without errors
3. New columns should exist, old should be gone

---

## 🧪 Test Data Checklist

Before running tests, you'll need:

- [ ] An active user account (can create during testing)
- [ ] Fresh database (run `php artisan migrate:fresh`)
- [ ] Browser with DevTools (F12)
- [ ] PDF viewer (for report testing)
- [ ] About 20-30 minutes

---

## ✅ Sign-Off Checklist

**Print this and mark off as you test:**

### Automated Validation
- [ ] `php artisan migrate:fresh` completes without errors
- [ ] `scripts/validate_fixes.php` shows all ✅
- [ ] Database columns confirmed with tinker

### Fix #1: Cost Multiplier
- [ ] Can create cost rate with Execution Days multiplier
- [ ] Value saves to database (tinker confirms)
- [ ] Validation rejects invalid multipliers

### Fix #2: Line Types
- [ ] Main lines display RED after generation
- [ ] Cross lines display AMBER after generation
- [ ] After save → reload, colors are correct
- [ ] GeoJSON includes line_type per feature

### Fix #3: Custom Rows
- [ ] "Add Custom Line Item" button works
- [ ] Row adds to correct section
- [ ] Calculation updates correctly

### Fix #4: Report
- [ ] PDF downloads without error
- [ ] Days column shows numeric values
- [ ] No undefined/blank fields

### General
- [ ] No red console errors (F12 → Console)
- [ ] No database errors in Laravel logs
- [ ] Cost calculations verified manually
- [ ] Data persists on page reload

---

## 🎯 Success Criteria

**You're ready to deploy when:**

✅ All 5 fixes individually validated  
✅ End-to-end workflow tests pass  
✅ No console errors  
✅ No data loss on reload  
✅ Report generates correctly  
✅ Calculations accurate  
✅ All sign-off boxes checked  

**DO NOT DEPLOY if:**

❌ Any test fails  
❌ Database validation shows errors  
❌ Console has red errors  
❌ Data doesn't persist  
❌ Report has blank fields  

---

## 📱 Testing Flow Chart

```
START
  ↓
[1] php artisan migrate:fresh
    ↓
    ✅ All migrations pass? → Continue
    ❌ NO → Troubleshoot migrations
  ↓
[2] Run validate_fixes.php
    ↓
    ✅ All ✅? → Continue
    ❌ NO → Check specific column
  ↓
[3] Choose testing path:
    ⚡ Fast (10 min) → QUICK_TESTING_CHECKLIST.md
    📋 Full (30 min) → TESTING_GUIDE.md
    🚀 Deploy (60 min) → DEPLOYMENT_GUIDE.md
  ↓
[4] Run selected tests
    ↓
    ✅ All pass? → Ready to commit/deploy
    ❌ NO → Troubleshoot & fix → Go back to [1]
  ↓
[5] Sign off and deploy
  ↓
END
```

---

## 🚨 Emergency Troubleshooting

### Quick Fixes During Testing

**Issue: Migration fails**
```bash
php artisan migrate:rollback
php artisan migrate
```

**Issue: Cost multiplier lost**
```bash
# Check CostRate model
grep -n "base_multiplier" app/Models/CostRate.php
# Should show in $fillable array
```

**Issue: Lines wrong color**
```bash
# Check SurveyLineService.php line 21
# Should read: $type = $feature['properties']['line_type'] ?? ...
```

**Issue: Custom row doesn't add**
```bash
# Open F12 → Console
# Look for JavaScript errors
# Check if .cost-section exists in Elements tab
```

---

## 📞 Need Help?

| Question | Answer |
|----------|--------|
| Where's the fast test? | [QUICK_TESTING_CHECKLIST.md](./QUICK_TESTING_CHECKLIST.md) |
| Where's detailed steps? | [TESTING_GUIDE.md](./TESTING_GUIDE.md) |
| Deployment checklist? | [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) |
| Run auto validation? | `php artisan tinker` → `include 'scripts/validate_fixes.php';` |
| Database query help? | `php artisan tinker` → `DB::table(...)->first()` |
| Code changes summary? | See "📊 Quick Fix Reference" above |

---

## 🎉 Ready?

**START HERE based on your timeline:**

⏱️ **5 minutes?**  
→ Run automated validation + quick checklist

⏱️ **30 minutes?**  
→ Run comprehensive testing guide

⏱️ **60 minutes?**  
→ Follow full deployment guide + sign-off

---

**Last Updated:** 2026-08-29  
**Status:** ✅ All Fixes Ready for Testing  
**Expected Test Duration:** 5-30 minutes  
**Deployment Status:** Ready after validation ✅
