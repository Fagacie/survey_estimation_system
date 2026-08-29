# QUICK TESTING CHECKLIST (5-10 mins)

Use this for rapid validation of critical fixes.

## ✅ Prerequisite (2 mins)
```bash
php artisan migrate:fresh
# Verify: No errors, migrations complete
```

---

## ✅ TEST 1: Base Multiplier Persistence (2 mins)

**Action:**
1. Settings > Master Cost Rates > Add New Rate
2. Create: Category=Equipment, Name=Test Vessel, Unit Type=**Per Day**, Base Multiplier=**Execution Days**, Rate=5000
3. Submit

**Validation:**
```bash
php artisan tinker
>>> DB::table('cost_rates')->where('name', 'Test Vessel')->first()->base_multiplier;
// Expected: "Execution Days" ✅ (NOT "Total Duration")
```

**Result:** ✅ PASS / ❌ FAIL

---

## ✅ TEST 2: Survey Line Type Preservation (3 mins)

**Action:**
1. Create Project → Map
2. Draw boundary, generate main lines (red should appear)
3. Generate cross lines (amber dashed should appear)
4. Click "Save Planning"
5. Wait for reload

**Validation:**
- After reload, are main lines RED? → ✅ YES / ❌ NO
- After reload, are cross lines AMBER DASHED? → ✅ YES / ❌ NO

**Result:** ✅ PASS / ❌ FAIL

---

## ✅ TEST 3: Custom Cost Row (1 min)

**Action:**
1. Go to Cost Estimation for that project
2. Scroll to table
3. Click "Add Custom Line Item"
4. Fill: Description=Test, Days=2, Units=1, Rate=1000
5. Verify total shows 2000

**Validation:**
- Did row add without error? → ✅ YES / ❌ NO
- Does calculation show 2000? → ✅ YES / ❌ NO

**Result:** ✅ PASS / ❌ FAIL

---

## ✅ TEST 4: Report Fields (1 min)

**Action:**
1. Click "Download Report"
2. Open PDF → Cost Breakdown table
3. Look at "Days/Qty" column

**Validation:**
- Does it show numeric values (NOT blank/undefined)? → ✅ YES / ❌ NO

**Result:** ✅ PASS / ❌ FAIL

---

## ✅ TEST 5: Database Columns (1 min)

```bash
php artisan tinker
>>> DB::table('cost_items')->first();
// Check: 'days' field exists (NOT 'quantity') ✅
// Check: 'units' field exists ✅
```

**Result:** ✅ PASS / ❌ FAIL

---

## OVERALL RESULT

**Total Checks:** 5
- ✅ All Passed = **SUCCESS - Deploy Ready**
- ❌ Any Failed = **ISSUES REMAIN - Do Not Deploy**

---

## Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| Migration errors | Roll back: `php artisan migrate:rollback` |
| base_multiplier shows "Total Duration" | Check CostRate model `$fillable` was updated |
| Lines all show as amber after reload | Check SurveyLineService reads `line_type` from properties |
| Custom row doesn't add | Check console (F12) for JavaScript errors |
| Report column blank | Verify `cost_items.days` column in database (not `quantity`) |

