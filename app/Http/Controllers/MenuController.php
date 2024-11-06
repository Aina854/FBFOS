<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\Log; // Ensure you have the Log model imported
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $menus = Menu::when($keyword, function ($query, $keyword) {
            return $query->where('menuName', 'like', "%{$keyword}%");
        })->get();

        // Count low stock and out of stock items
        $lowStockCount = Menu::where('quantityStock', '<=', 10)
                     ->where('quantityStock', '>', 0)
                     ->count();

        $outOfStockCount = Menu::where('quantityStock', '=', 0)->count();

        return view('menus.index', compact('menus', 'lowStockCount', 'outOfStockCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('menus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'menuImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'menuName' => 'required|string|max:255',
            'menuCategory' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantityStock' => 'required|integer|min:0', // Updated to handle stock quantity
            'description' => 'nullable|string',
        ]);

        $menu = new Menu($request->except('menuImage'));
        if ($request->hasFile('menuImage')) {
            $image = $request->file('menuImage');
            $imagePath = $image->store('menu_images', 'public');
            $menu->menuImage = $imagePath;
        }
        $menu->save();

        // Log the creation of the menu item
        Log::create([
            'user_id' => Auth::id(), // The ID of the admin performing the action
            'action' => 'Create Menu Item',
            'details' => Auth::user()->name . ' (staff) added a new menu item: ' . $menu->menuName,
            'created_at' => now(), // Current timestamp
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        return view('menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        return view('menus.edit', compact('menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'menuImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'menuName' => 'required|string|max:255',
            'menuCategory' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantityStock' => 'required|integer|min:0', // Updated to handle stock quantity
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('menuImage')) {
            $image = $request->file('menuImage');
            $imagePath = $image->store('menu_images', 'public');
            $menu->menuImage = $imagePath;
        }
        $menu->update($request->except('menuImage'));

        // Log the creation of the menu item
        Log::create([
            'user_id' => Auth::id(), // The ID of the admin performing the action
            'action' => 'Edit Menu Item',
            'details' => Auth::user()->name . ' (staff) edit a menu item: ' . $menu->menuName,
            'created_at' => now(), // Current timestamp
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        // Delete image from storage if it exists
        if ($menu->menuImage) {
            Storage::disk('public')->delete($menu->menuImage);
        }

        $menu->delete();

        // Log the creation of the menu item
        Log::create([
            'user_id' => Auth::id(), // The ID of the admin performing the action
            'action' => 'Delete Menu Item',
            'details' => Auth::user()->name . ' (staff) delete a menu item: ' . $menu->menuName,
            'created_at' => now(), // Current timestamp
        ]);
        return redirect()->route('menus.index')->with('success', 'Menu item deleted successfully.');
    }
}
