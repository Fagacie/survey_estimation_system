<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$modules = App\Models\Module::with(['categories.services.items.unit'])->get();

$adminModulesTree = $modules->map(function ($module) {
    return [
        'module_id'   => $module->getKey(),
        'module_name' => $module->module_name ?? $module->name ?? 'N/A',
        'categories'  => $module->categories->map(function ($cat) {
            return [
                'category_id'   => $cat->getKey(),
                'category_name' => $cat->category_name ?? $cat->name ?? 'N/A',
                'services'     => $cat->services->map(function ($serv) {
                    return [
                        'service_id'   => $serv->getKey(),
                        'service_name' => $serv->service_name ?? $serv->name ?? 'N/A',
                        'items'        => $serv->items->map(function ($item) {
                            return [
                                'item_id'   => $item->getKey(),
                                'item_name' => $item->item_name ?? $item->name ?? 'N/A',
                                'rate'      => $item->internal_rate ?? 0,
                                'unit'      => $item->unit->unit_name ?? $item->unit_name ?? $item->unit ?? '',
                            ];
                        })->values()->toArray(),
                    ];
                })->values()->toArray()
            ];
        })->values()->toArray()
    ];
})->values()->toArray();

file_put_contents('tree_dump.json', json_encode($adminModulesTree, JSON_PRETTY_PRINT));
echo "Done";
