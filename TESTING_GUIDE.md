# Complete Testing Guide for Critical Fixes

## Overview
This guide validates all 5 critical fixes deployed to the SBES Survey Estimation System.

---

## PREREQUISITE: Fresh Migration & Database Setup

### Step 0: Database Reset
```bash
# Navigate to project root
cd C:\Users\ACER\Desktop\survey-estimation-system.worktrees\code-review-and-debugging-openstreetmap

# Run fresh migrations (this will also verify the empty multi-location migration runs without errors)
php artisan migrate:fresh

# Seed with test data (if seeders exist)
php artisan db:seed

# Verify tables
php artisan tinker
>>> DB::table('cost_rates')->first();
// Should return object with 'base_multiplier' column
```

**Expected Result:** ✅ Migrations complete without errors, `cost_rates` table has `base_multiplier` column with default `'Total Duration'`

---

## TEST SUITE 1: Cost Rate Base Multiplier Persistence

### Test 1.1: Create Cost Rate with Execution Days Multiplier

**Steps:**
1. Open application and log in
2. Navigate to **Settings > Master Cost Rates**
3. Click **"Add New Rate"** button
4. Fill form:
   - Category: `Equipment`
   - Item Name: `Survey Vessel`
   - Unit Type: `Per Day`
   - Base Multiplier: `Execution Days` ← **KEY FIX VALIDATION**
   - Default Rate: `5000.00`
5. Click **Submit**
6. Verify success message appears

**Expected Result:** ✅ 
- Success message: "Cost rate added successfully"
- Rate appears in table with Base Multiplier showing as `Execution Days`
- Database verification:
  ```bash
  php artisan tinker
  >>> $rate = DB::table('cost_rates')->where('name', 'Survey Vessel')->first();
  >>> $rate->base_multiplier;  // Should output: "Execution Days" (NOT "Total Duration")
  >>> dd($rate);
  ```

**Validation Passes If:** `base_multiplier` column contains `"Execution Days"` (not the default)

---

### Test 1.2: Update Cost Rate Multiplier

**Steps:**
1. In Settings > Master Cost Rates
2. Click edit icon on "Survey Vessel" rate
3. Change Base Multiplier: `Execution Days` → `MOB/DEMOB Days`
4. Click **Update Rate**
5. Verify it now shows `MOB/DEMOB Days` in the table

**Expected Result:** ✅ 
- Base Multiplier updated in database
- On refresh, still shows `MOB/DEMOB Days`

---

### Test 1.3: Validation Rejects Invalid Multiplier

**Steps:**
1. Open browser DevTools (F12) → **Console**
2. Run:
```javascript
fetch('/settings/costs', {
  method: 'POST',
  headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value},
  body: JSON.stringify({
    category: 'Equipment',
    name: 'Invalid Test',
    unit_type: 'Per Day',
    base_multiplier: 'INVALID_VALUE',
    default_rate: 1000
  })
})
.then(r => r.json())
.then(console.log);
```

**Expected Result:** ✅ 
- 422 Validation Error (or 400)
- Response contains validation error for `base_multiplier`
- Invalid value NOT saved to database

---

## TEST SUITE 2: Survey Line Type Preservation

### Test 2.1: Create Project and Generate Lines

**Steps:**
1. Navigate to **Projects > Create New Project**
2. Fill form and create project
3. Click **"Go to Map"**
4. On map page:
   - Draw a **boundary polygon** (blue, filled)
   - Set Main Line spacing: `500` meters
   - Set angle: `45` degrees
   - Click **"Generate Main Lines"**
5. Verify red lines appear on map
6. Set Cross Line spacing: `200` meters
7. Click **"Generate Cross Lines"**
8. Verify amber dashed lines appear

**Expected Result:** ✅ 
- Main lines: RED solid (1.5pt)
- Cross lines: AMBER dashed
- No console errors

---

### Test 2.2: Inspect Line Properties Before Save

**Steps:**
1. Still on map page with generated lines
2. Open DevTools → **Console**
3. Run:
```javascript
let count = 0;
drawnItems.eachLayer(function(layer) {
  if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
    console.log(`Line ${count}:`, {
      lineType: layer.lineType,
      isGenerated: layer.isGenerated,
      geometry: layer.toGeoJSON().geometry.type
    });
    count++;
  }
});
```

**Expected Result:** ✅ 
- Output shows lines with `lineType: 'main'` or `lineType: 'cross'`
- `isGenerated: true`

---

### Test 2.3: Save Planning and Inspect Payload

**Steps:**
1. Click **"Save Planning"** button
2. Before submission, open DevTools → **Network**
3. Look for POST request to `projects/{id}/map/save`
4. Click the request and view **Payload** tab
5. Inspect the `lines.features[0].properties`:
```json
{
  "line_type": "main",
  "is_generated": true
}
```

**Expected Result:** ✅ 
- Each feature in the GeoJSON has `line_type` property set correctly
- **NOT** a global `is_generated: true` flag

---

### Test 2.4: Reload and Verify Line Types Preserved

**Steps:**
1. Wait for success message: "Planning data saved successfully"
2. Page will auto-reload
3. After page loads, observe the map:
   - Main lines should display as **RED** solid
   - Cross lines should display as **AMBER** dashed
4. Open DevTools → **Console**
5. Run:
```javascript
mainLineLayerGroup.eachLayer(l => console.log('Main:', l.toGeoJSON().geometry.coordinates.length));
crossLineLayerGroup.eachLayer(l => console.log('Cross:', l.toGeoJSON().geometry.coordinates.length));
```

**Expected Result:** ✅ 
- Main lines rendered in main layer (RED)
- Cross lines rendered in cross layer (AMBER dashed)
- Correct separation between line types
- **NOT** all lines showing as cross lines

---

### Test 2.5: Centerline Mode (Optional - Advanced)

**Steps:**
1. Create NEW project
2. Draw boundary
3. Switch to **Centerline Mode** in generation accordion
4. Draw a **single polyline** (reference centerline)
5. Generate main lines (will offset from centerline)
6. Generate cross lines
7. Click **"Save Planning"**
8. Reload page
9. In map, try to generate main lines again:
   - The system should recognize and reuse the existing centerline
   - NOT treat it as a generated line

**Expected Result:** ✅ 
- Centerline remains selectable for regeneration
- No error: "No Centerline found"
- Can regenerate with same reference

---

## TEST SUITE 3: Custom Cost Row Addition

### Test 3.1: Add Custom Row to Cost Estimation

**Steps:**
1. In a project with saved boundaries and lines, click **"Continue to Cost"**
2. On Cost Estimation page, scroll to cost items table
3. At bottom, click **"+ Add Custom Line Item"** button
4. Verify a new row appears at end of the table
5. Fill the row:
   - Description: `Custom Equipment Rental`
   - Days: `3`
   - Units: `2`
   - Unit Rate: `500`
6. Verify total shows: `3000` (3 × 2 × 500)

**Expected Result:** ✅ 
- Row inserted successfully (not silent failure)
- Row appears in last section (or Miscellaneous)
- Totals calculated correctly
- Grand total updated

---

### Test 3.2: Add Multiple Custom Rows

**Steps:**
1. Click **"Add Custom Line Item"** again
2. Add another row with different values
3. Verify both rows calculate and grand total updates

**Expected Result:** ✅ 
- Multiple rows can be added
- Each calculates independently
- Grand total is sum of all rows

---

### Test 3.3: Verify Form Submission

**Steps:**
1. Fill a few cost items (auto-generated + custom)
2. Click **"Save Estimation"** button
3. Verify success message

**Expected Result:** ✅ 
- Form submits without error
- Validation passes
- Cost estimation saved

---

## TEST SUITE 4: Report Field Display

### Test 4.1: Download and Inspect Report

**Steps:**
1. After saving cost estimation, click **"Download Report"** button
2. PDF downloads and opens
3. Scroll to **Cost Breakdown** table
4. Check columns: `No. | Description | Days/Qty | Units/Pax | Unit Rate | Total`
5. Verify the **Days/Qty column** shows numeric values (e.g., `5.50`, not blank/undefined)

**Expected Result:** ✅ 
- Days/Qty column populated with correct duration values
- All cost items display complete information
- No blank cells or undefined values
- Total matches cost estimation page

**Validation Query:**
```bash
php artisan tinker
>>> $items = DB::table('cost_items')->where('cost_estimation_id', 1)->get();
>>> $items->first()->days;  // Should show numeric value
>>> $items->first()->units; // Should show numeric value
```

---

## TEST SUITE 5: Database & Migration Integrity

### Test 5.1: Verify All Columns Exist

**Steps:**
```bash
php artisan tinker

# Check cost_rates table
>>> DB::table('cost_rates')->getConnection()->getSchemaBuilder()->getColumnListing('cost_rates');
// Should include: 'base_multiplier'

# Check cost_items table
>>> DB::table('cost_items')->getConnection()->getSchemaBuilder()->getColumnListing('cost_items');
// Should include: 'days', 'units' (NOT 'quantity')

# Verify migration record
>>> DB::table('migrations')->where('migration', 'like', '%restructure_database%')->first();
// Should exist and be marked as run
```

**Expected Result:** ✅ 
- `cost_rates.base_multiplier` exists
- `cost_items.days` exists (renamed from quantity)
- `cost_items.units` exists
- All migrations recorded

---

### Test 5.2: Empty Migration Runs Without Error

**Steps:**
```bash
# During fresh migration, the multi-location restructure migration should run
php artisan migrate:fresh --step

# Watch output and verify:
# - "Migrating: 2026_08_28_032548_restructure_database_for_multi_locations"
# - No error following it
```

**Expected Result:** ✅ 
- Migration runs without errors
- No changes to schema (intentionally empty)
- Clear docstring in file explains purpose

---

## TEST SUITE 6: End-to-End Integration

### Test 6.1: Complete Workflow

**Steps:**
1. **Create Project**
   - Name: `Test Survey 1`
   - Location: anywhere
   - Submit

2. **Plan Survey**
   - Draw boundary polygon
   - Generate main lines (500m spacing, 45°)
   - Generate cross lines (200m spacing)
   - Set parameters:
     - Speed: `6` knots
     - Working hours: `8` hours/day
     - Weather days: `1`
     - MOB/DEMOB: `2` days
   - Click **"Save Planning"**

3. **Cost Estimation**
   - System auto-calculates based on distance and time
   - Verify engineering card shows:
     - Distance (NM)
     - Speed (knots)
     - Execution days (calculated)
     - Weather days (1)
     - MOB/DEMOB days (2)
     - **Total Duration** (sum of all)
   - Verify cost items use correct multipliers
   - Add custom item: Description=`Survey Report`, Days=1, Units=1, Rate=2000
   - Verify it calculates as 1 × 1 × 2000 = 2000
   - Click **"Save Estimation"**

4. **Generate Report**
   - Click **"Download Report"**
   - Verify all fields display correctly
   - Check cost breakdown table

5. **Reload and Verify Persistence**
   - Refresh page (Ctrl+R)
   - Verify:
     - Boundary still visible
     - Main lines RED, cross lines AMBER
     - Cost data still loaded
     - All calculations consistent

**Expected Result:** ✅ 
- Complete workflow executes without errors
- All data persists correctly
- Calculations accurate
- Report displays properly
- No undefined values or blank cells

---

## TEST SUITE 7: Error Cases & Edge Cases

### Test 7.1: Empty Cost Section

**Steps:**
1. Create project with NO master cost rates configured
2. Go to Cost Estimation
3. Click **"Add Custom Line Item"**

**Expected Result:** ✅ 
- Row adds successfully (NOT silent failure)
- Either shows warning OR adds to Miscellaneous
- No console errors

---

### Test 7.2: Lump Sum Rate Configuration

**Steps:**
1. In Settings, create new cost rate:
   - Unit Type: `Lump Sum`
   - Base Multiplier: (should be hidden/disabled)
   - Rate: `1000`
2. Save
3. Go to Cost Estimation for a project
4. Find that Lump Sum item
5. Verify Days and Units fields are **readonly/disabled** with gray background

**Expected Result:** ✅ 
- Lump Sum rates show with Days and Units disabled
- Calculation: 1 × 1 × 1000 = 1000 (fixed)
- Cannot edit those fields

---

### Test 7.3: Boundary Reload After Clear

**Steps:**
1. Create project, draw boundary, save planning
2. Reload page
3. Verify boundary visible
4. Edit boundary (click polygon to move vertices)
5. Click **"Save Planning"** again
6. Reload
7. Verify boundary persists with edits

**Expected Result:** ✅ 
- Boundary geometry preserved
- Edits saved and reloaded correctly
- Statistics recalculated

---

## VALIDATION CHECKLIST

Run this checklist after all tests:

- [ ] **Migration**: `php artisan migrate:fresh` completes without errors
- [ ] **Cost Rate**: `base_multiplier` persisted in database (verified via tinker)
- [ ] **Validation**: Invalid multiplier values rejected
- [ ] **Line Types**: After save → reload, main lines RED and cross lines AMBER
- [ ] **Line Properties**: GeoJSON features include `line_type` and `is_generated` in properties
- [ ] **Custom Rows**: "Add Custom Line Item" works and calculates totals
- [ ] **Report Fields**: Days column shows numeric values (not undefined/blank)
- [ ] **Database**: `cost_items.quantity` column gone, replaced with `days` and `units`
- [ ] **Calculations**: Cost = days × units × rate (correctly multiplied)
- [ ] **Persistence**: Data survives page reload
- [ ] **No Console Errors**: Open DevTools, no red errors in console during workflow

---

## ROLLBACK / EMERGENCY PROCEDURES

### If Tests Fail

**Issue: Migration Fails**
```bash
php artisan migrate:rollback
# Fix the issue, then re-run
php artisan migrate
```

**Issue: Cost Rate Changes Lost**
```bash
php artisan db:seed
# Or manually update via tinker
```

**Issue: Survey Lines Not Displaying**
- Check browser console for JavaScript errors
- Verify GeoJSON feature structure in Network tab
- Confirm `line_type` property in payload

**Issue: Custom Row Not Adding**
- Open DevTools → Elements
- Verify `.cost-section` divs exist in DOM
- Check console for JavaScript errors
- Try different browser

---

## Success Criteria

✅ **All tests pass** = All 5 critical issues are FIXED and VALIDATED
✅ **No regressions** = Existing functionality still works
✅ **Data integrity** = No data loss or corruption
✅ **Database clean** = Migrations run cleanly with no conflicts

---

## Questions During Testing?

1. **Cost calculation wrong?** → Check `base_multiplier` in database matches settings UI
2. **Lines wrong color after reload?** → Check Network tab payload has per-feature `line_type`
3. **Custom row silent failure?** → Check browser console for JavaScript errors
4. **Report blank fields?** → Check database has `days` column (not `quantity`)

---

**Testing Date:** _______________
**Tester Name:** _______________
**Result:** ⬜ PASS / ⬜ FAIL
