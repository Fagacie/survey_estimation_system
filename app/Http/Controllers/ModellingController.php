<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\CatalogModule;
use App\Models\Package;
use App\Models\ProjectModellingItem;
use App\Models\ProjectModellingSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModellingController extends Controller
{
    public function moduleIndex()
    {
        $modules = CatalogModule::withCount('items')->orderBy('group')->orderBy('name')->get();
        $groups  = CatalogModule::whereNotNull('group')->distinct()->orderBy('group')->pluck('group');

        return view('projects.modeling.modules', compact('modules', 'groups'));
    }

    public function moduleStore(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'group' => 'required|string|max:255',
        ]);

        CatalogModule::create($validated);

        return redirect()->route('projects.modeling.modules.index')->with('success', 'Module created.');
    }

    public function moduleUpdate(Request $request, CatalogModule $catalogModule)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'group' => 'required|string|max:255',
        ]);

        $catalogModule->update($validated);

        return redirect()->route('projects.modeling.modules.index')->with('success', 'Module updated.');
    }

    public function moduleDestroy(CatalogModule $catalogModule)
    {
        $catalogModule->delete();

        return redirect()->route('projects.modeling.modules.index')->with('success', 'Module deleted.');
    }

    public function itemIndex(Request $request)
    {
        $modules = CatalogModule::orderBy('group')->orderBy('name')->get();

        $selectedModuleId = $request->get('module_id', $modules->first()->id ?? null);

        $items = CatalogItem::with('module')
            ->when($selectedModuleId, fn ($q) => $q->where('module_id', $selectedModuleId))
            ->orderBy('work_package')
            ->orderBy('name')
            ->get();

        return view('projects.modeling.items', compact('modules', 'items', 'selectedModuleId'));
    }

    public function itemStore(Request $request)
    {
        $validated = $request->validate([
            'module_id'    => 'required|exists:catalog_modules,id',
            'work_package' => 'required|string|max:255',
            'name'         => 'required|string|max:255',
            'qty'          => 'required|numeric|min:0',
            'rate'         => 'required|numeric|min:0',
            'days'         => 'required|numeric|min:0',
            'markup'       => 'required|numeric|min:0|max:99.99',
        ]);

        CatalogItem::create($validated);

        return redirect()->route('projects.modeling.items.index', ['module_id' => $validated['module_id']])
            ->with('success', 'Item created.');
    }

    public function itemUpdate(Request $request, CatalogItem $catalogItem)
    {
        $validated = $request->validate([
            'module_id'    => 'required|exists:catalog_modules,id',
            'work_package' => 'required|string|max:255',
            'name'         => 'required|string|max:255',
            'qty'          => 'required|numeric|min:0',
            'rate'         => 'required|numeric|min:0',
            'days'         => 'required|numeric|min:0',
            'markup'       => 'required|numeric|min:0|max:99.99',
        ]);

        $catalogItem->update($validated);

        return redirect()->route('projects.modeling.items.index', ['module_id' => $validated['module_id']])
            ->with('success', 'Item updated.');
    }

    public function itemDestroy(CatalogItem $catalogItem)
    {
        $moduleId = $catalogItem->module_id;
        $catalogItem->delete();

        return redirect()->route('projects.modeling.items.index', ['module_id' => $moduleId])
            ->with('success', 'Item deleted.');
    }

    public function packageIndex()
    {
        $packages = Package::with('modules')->orderBy('name')->get();
        $modules  = CatalogModule::orderBy('group')->orderBy('name')->get();

        return view('projects.modeling.packages', compact('packages', 'modules'));
    }

    public function packageStore(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'modules'   => 'required|array|min:1',
            'modules.*' => 'exists:catalog_modules,id',
        ]);

        $package = Package::create(['name' => $validated['name']]);
        $package->modules()->sync($validated['modules']);

        return redirect()->route('projects.modeling.packages.index')->with('success', 'Package created.');
    }

    public function packageUpdate(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'modules'   => 'required|array|min:1',
            'modules.*' => 'exists:catalog_modules,id',
        ]);

        $package->update(['name' => $validated['name']]);
        $package->modules()->sync($validated['modules']);

        return redirect()->route('projects.modeling.packages.index')->with('success', 'Package updated.');
    }

    public function packageDestroy(Package $package)
    {
        $package->delete();

        return redirect()->route('projects.modeling.packages.index')->with('success', 'Package deleted.');
    }

    public function builder(Request $request)
    {
        // Project comes from "Proceed to Modeling" (?project_id=...).
        // It is remembered in the session so the Builder tab still works
        // after visiting Items / Modules / Packages.
        $project = null;

        if ($request->filled('project_id')) {
            $project = auth()->user()->projects()
                ->with('client')
                ->whereKey($request->project_id)
                ->firstOrFail();

            session(['modeling_project_id' => $project->project_Id]);
        } elseif (session()->has('modeling_project_id')) {
            $project = auth()->user()->projects()
                ->with('client')
                ->whereKey(session('modeling_project_id'))
                ->first();

            // Project was deleted or is no longer accessible
            if (! $project) {
                session()->forget('modeling_project_id');
            }
        }

        // "General" group first, then the rest alphabetically
        $modules = CatalogModule::with('items')->get()
            ->sortBy(fn ($m) => [strtolower($m->group) === 'general' ? 0 : 1, $m->group, $m->name])
            ->values();

        $catalog = $modules->map(function ($module) {
            $items = $module->items->sortBy(fn ($i) => [$i->work_package, $i->name])->values();

            return [
                'id'             => $module->id,
                'name'           => $module->name,
                'group'          => $module->group,
                'required'       => strtolower($module->group) === 'general',
                'items'          => $items->map(fn ($i) => [
                    'name'         => $i->name,
                    'work_package' => $i->work_package,
                    'internal'     => round($i->internal_cost, 2),
                    'client'       => round($i->client_cost, 2),
                ])->all(),
                'internal_total' => round($items->sum(fn ($i) => round($i->internal_cost, 2)), 2),
                'client_total'   => round($items->sum(fn ($i) => round($i->client_cost, 2)), 2),
            ];
        })->values();

        $presets = Package::with('modules')->orderBy('name')->get()->map(fn ($p) => [
            'id'      => $p->id,
            'name'    => $p->name,
            'modules' => $p->modules->pluck('id')->values()->all(),
        ])->values();

        return view('projects.modeling.builder', compact('project', 'catalog', 'presets'));
    }

    public function saveQuote(Request $request)
    {
        $validated = $request->validate([
            'project_id'          => 'required|exists:projects,project_Id',
            'module_ids'          => 'required|array|min:1',
            'module_ids.*'        => 'exists:catalog_modules,id',
            'package_id'          => 'nullable|exists:packages,id',
            'contingency_percent' => 'required|numeric|min:0|max:100',
            'tax_percent'         => 'required|numeric|min:0|max:100',
        ]);

        // Ownership check: the project must belong to the logged-in user
        $project = auth()->user()->projects()->whereKey($validated['project_id'])->firstOrFail();

        DB::transaction(function () use ($validated, $project) {
            // A project only has one Modelling configuration, so clear the old one first
            ProjectModellingItem::where('project_Id', $project->project_Id)->delete();

            $items = CatalogItem::with('module')
                ->whereIn('module_id', $validated['module_ids'])
                ->get();

            $internalSubtotal = 0;
            $clientSubtotal   = 0;

            foreach ($items as $item) {
                $internalSubtotal += $item->internal_cost;
                $clientSubtotal   += $item->client_cost;

                ProjectModellingItem::create([
                    'project_Id'        => $project->project_Id,
                    'catalog_module_id' => $item->module_id,
                    'catalog_item_id'   => $item->id,
                    'unit_qty'          => $item->qty,
                    'days'              => $item->days,
                    'daily_rate'        => $item->rate,
                    'mark_up'           => $item->markup,
                    'line_total'        => round($item->client_cost, 2),
                ]);
            }

            $contingencyAmount = $clientSubtotal * ($validated['contingency_percent'] / 100);
            $taxAmount         = ($clientSubtotal + $contingencyAmount) * ($validated['tax_percent'] / 100);
            $grandTotal        = $clientSubtotal + $contingencyAmount + $taxAmount;

            $packageName = null;
            if (! empty($validated['package_id'])) {
                $packageName = Package::find($validated['package_id'])?->name;
            }

            ProjectModellingSummary::updateOrCreate(
                ['project_Id' => $project->project_Id],
                [
                    'package_id'          => $validated['package_id'] ?? null,
                    'package_name'        => $packageName,
                    'contingency_percent' => $validated['contingency_percent'],
                    'tax_percent'         => $validated['tax_percent'],
                    'internal_subtotal'   => round($internalSubtotal, 2),
                    'client_subtotal'     => round($clientSubtotal, 2),
                    'contingency_amount'  => round($contingencyAmount, 2),
                    'tax_amount'          => round($taxAmount, 2),
                    'grand_total'         => round($grandTotal, 2),
                    'created_by'          => auth()->id(),
                ]
            );
        });

        return response()->json(['success' => true]);
    }

    public function destroyQuote(Request $request, $projectId)
    {
        $project = auth()->user()->projects()->whereKey($projectId)->firstOrFail();

        ProjectModellingItem::where('project_Id', $project->project_Id)->delete();
        ProjectModellingSummary::where('project_Id', $project->project_Id)->delete();

        return redirect()->route('projects.show', $project->project_Id)
            ->with('success', 'Modelling data removed.');
    }
}