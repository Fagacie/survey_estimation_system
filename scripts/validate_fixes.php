<?php

/**
 * DATABASE VALIDATION SCRIPT
 * Verify all critical fixes are correctly deployed to the database
 * 
 * Usage:
 *   php artisan tinker
 *   >>> include 'scripts/validate_fixes.php';
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n════════════════════════════════════════════════════════════\n";
echo "DATABASE VALIDATION FOR CRITICAL FIXES\n";
echo "════════════════════════════════════════════════════════════\n\n";

// FIX 1: base_multiplier in cost_rates
echo "✓ FIX 1: Cost Rate Base Multiplier\n";
echo "─────────────────────────────────────────────────────────────\n";

$hasBaseMultiplier = Schema::hasColumn('cost_rates', 'base_multiplier');
echo "  1.1 Column 'base_multiplier' exists in cost_rates: " 
    . ($hasBaseMultiplier ? "✅ YES" : "❌ NO") . "\n";

if ($hasBaseMultiplier) {
    $rate = DB::table('cost_rates')->first();
    if ($rate) {
        echo "  1.2 Sample rate base_multiplier value: '{$rate->base_multiplier}'\n";
        echo "      Expected: 'Total Duration', 'Execution Days', 'MOB/DEMOB Days', or 'Weather Days'\n";
    }
    
    $count = DB::table('cost_rates')->count();
    echo "  1.3 Total cost rates in database: {$count}\n";
}
echo "\n";

// FIX 2: Survey Line Type in GeoJSON Properties
echo "✓ FIX 2: Survey Line Type Preservation\n";
echo "─────────────────────────────────────────────────────────────\n";

$lineCount = DB::table('survey_lines')->count();
echo "  2.1 Total survey lines in database: {$lineCount}\n";

if ($lineCount > 0) {
    $sample = DB::table('survey_lines')->first();
    
    // Decode GeoJSON
    $geom = is_string($sample->geometry) ? json_decode($sample->geometry, true) : $sample->geometry;
    
    $hasLineType = isset($geom['properties']['line_type']);
    echo "  2.2 Sample line has 'line_type' in properties: " 
        . ($hasLineType ? "✅ YES" : "❌ NO (check backend fix)\n") . "\n";
    
    if ($hasLineType) {
        echo "      Line type value: '{$geom['properties']['line_type']}'\n";
        echo "      Expected: 'main', 'cross', or 'reference'\n";
    }
    
    $typeColumn = Schema::hasColumn('survey_lines', 'type');
    echo "  2.3 Column 'type' exists in survey_lines: " 
        . ($typeColumn ? "✅ YES" : "❌ NO") . "\n";
}
echo "\n";

// FIX 3: Cost Items days/units (not quantity)
echo "✓ FIX 3: Cost Item Column Rename (quantity → days)\n";
echo "─────────────────────────────────────────────────────────────\n";

$hasDays = Schema::hasColumn('cost_items', 'days');
$hasUnits = Schema::hasColumn('cost_items', 'units');
$hasQuantity = Schema::hasColumn('cost_items', 'quantity');

echo "  3.1 Column 'days' exists: " . ($hasDays ? "✅ YES" : "❌ NO") . "\n";
echo "  3.2 Column 'units' exists: " . ($hasUnits ? "✅ YES" : "❌ NO") . "\n";
echo "  3.3 Column 'quantity' exists (should be gone): " 
    . ($hasQuantity ? "⚠️  STILL EXISTS (migration may have failed)" : "✅ REMOVED") . "\n";

if ($hasDays && $hasUnits) {
    $itemCount = DB::table('cost_items')->count();
    echo "  3.4 Total cost items in database: {$itemCount}\n";
    
    if ($itemCount > 0) {
        $item = DB::table('cost_items')->first();
        echo "      Sample item: days={$item->days}, units={$item->units}, total_price={$item->total_price}\n";
        
        // Verify calculation
        $expected = $item->days * $item->units * $item->unit_rate;
        $actual = $item->total_price;
        $calcCorrect = abs($expected - $actual) < 0.01;
        
        echo "  3.5 Calculation correct (days×units×rate): " 
            . ($calcCorrect ? "✅ YES" : "❌ NO") . "\n";
        if (!$calcCorrect) {
            echo "      Expected: {$expected}, Got: {$actual}\n";
        }
    }
}
echo "\n";

// FIX 4: Migration Records
echo "✓ FIX 4: Migration Integrity\n";
echo "─────────────────────────────────────────────────────────────\n";

$baseMult = DB::table('migrations')
    ->where('migration', 'like', '%add_base_multiplier%')
    ->exists();
echo "  4.1 Migration 'add_base_multiplier_to_cost_rates' ran: "
    . ($baseMult ? "✅ YES" : "❌ NO") . "\n";

$renameCol = DB::table('migrations')
    ->where('migration', 'like', '%modify_columns_in_cost_items%')
    ->exists();
echo "  4.2 Migration 'modify_columns_in_cost_items' ran: "
    . ($renameCol ? "✅ YES" : "❌ NO") . "\n";

$multiLoc = DB::table('migrations')
    ->where('migration', 'like', '%restructure_database_for_multi%')
    ->exists();
echo "  4.3 Migration 'restructure_database_for_multi_locations' ran: "
    . ($multiLoc ? "✅ YES" : "❌ NO") . "\n";

echo "\n";

// SUMMARY
echo "════════════════════════════════════════════════════════════\n";
echo "VALIDATION SUMMARY\n";
echo "════════════════════════════════════════════════════════════\n\n";

$allPass = $hasBaseMultiplier && $hasDays && $hasUnits && !$hasQuantity && $baseMult && $renameCol && $multiLoc;

if ($allPass) {
    echo "🎉 ALL CRITICAL FIXES VALIDATED SUCCESSFULLY!\n";
    echo "\n✅ Safe to proceed with user acceptance testing.\n";
} else {
    echo "⚠️  SOME VALIDATION CHECKS FAILED\n";
    echo "\nPlease review:\n";
    if (!$hasBaseMultiplier) echo "  - CostRate model \$fillable missing 'base_multiplier'\n";
    if (!$hasDays) echo "  - cost_items table missing 'days' column\n";
    if (!$hasUnits) echo "  - cost_items table missing 'units' column\n";
    if ($hasQuantity) echo "  - cost_items table still has old 'quantity' column\n";
    if (!$baseMult) echo "  - base_multiplier migration did not run\n";
    if (!$renameCol) echo "  - column rename migration did not run\n";
    if (!$multiLoc) echo "  - multi-location migration did not run\n";
    echo "\n❌ Do NOT proceed until all issues are resolved.\n";
}

echo "\n════════════════════════════════════════════════════════════\n\n";

return $allPass;
