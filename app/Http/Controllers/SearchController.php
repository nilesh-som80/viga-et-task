<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Asset;
use App\Models\Department;




class SearchController extends Controller
{
    public function search(Request $request){
        $request->validate([
            "search"=>"required"
        ]);
        $result = [];
        $result["projects"] = [];
        $result["departments"] = [];
        $result["assets"] = [];

        $project = Project::where("name","LIKE","%{$request->search}%")->get();
        $department = Department::where("name","LIKE","%{$request->search}%")->get();
        $assets = Asset::where("name","LIKE","%{$request->search}%")->get();

        if (count($project)) {
            $result["projects"] = Project::where("projects.name","LIKE","%{$request->search}%")
            ->leftJoin("departments","departments.id","=","projects.department")
            ->leftJoin("assets","assets.id","=","departments.id")
            ->select("projects.name as project_name","departments.name as department_name","assets.name as asset_name")
            ->get();
        }
        if (count($department)) {

            $result["departments"] = Department::where("departments.name","LIKE","%{$request->search}%")
            ->leftJoin("projects","departments.id","=","projects.department")
            ->leftJoin("assets","assets.id","=","departments.asset")
            ->select("projects.name as project_name","departments.name as department_name","assets.name as asset_name")
            ->get();
        }

        if (count($assets)) {

            $result["assets"] = Asset::where("assets.name","LIKE","%{$request->search}%")
            ->leftJoin("departments","assets.id","=","departments.asset")
            ->leftJoin("projects","departments.id","=","projects.department")
            ->select("assets.name as asset_name","projects.name as project_name","departments.name as department_name")
            ->get();
        }

        return response()->json($result);

    }

}
