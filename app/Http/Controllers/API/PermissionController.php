<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
   public function index()
   {
       return response()->json(Permission::all());
   }

   public function store(Request $request)
   {
       $request->validate([
           'name' => 'required|unique:permissions,name',
       ]);

       $permission = Permission::create(['name' => $request->name]);

       return response()->json([
           'message' => 'Permission created successfully',
           'permission' => $permission
       ]);
   }

   public function show($id)
   {
       return response()->json(
           Permission::findOrFail($id)
       );
   }

   public function update(Request $request, $id)
   {
       $permission = Permission::findOrFail($id);

       $request->validate([
           'name' => 'required|unique:permissions,name,' . $permission->id,
       ]);

       $permission->update(['name' => $request->name]);

       return response()->json([
           'message' => 'Permission updated successfully',
           'permission' => $permission
       ]);
   }

   public function destroy($id)
   {
       Permission::findOrFail($id)->delete();
       return response()->json(['message' => 'Permission deleted successfully']);
   }
}
