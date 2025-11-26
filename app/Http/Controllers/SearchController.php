<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    private function safeSearch($model, $fields, $q)
    {
        $query = $model::query();

        foreach ($fields as $field) {
            // Only search column if it exists in the table
            if (DB::getSchemaBuilder()->hasColumn($model->getTable(), $field)) {
                $query->orWhere($field, 'LIKE', "%$q%");
            }
        }

        return $query->get();
    }

    public function search(Request $request)
    {
        $q = $request->q;

        return response()->json([

            'departments' => $this->safeSearch(new \App\Models\Department, 
                ['name', 'description'], $q),

            'department_managers' => $this->safeSearch(new \App\Models\DepartmentManager, 
                ['name', 'position'], $q),

            'department_staff' => $this->safeSearch(new \App\Models\DepartmentStaff, 
                ['name', 'position'], $q),

            'documents' => $this->safeSearch(new \App\Models\Document, 
                ['title', 'summary', 'file'], $q),

            'executive_directors' => $this->safeSearch(new \App\Models\ExecutiveDirector, 
                ['name', 'bio'], $q),

            'jobs' => $this->safeSearch(new \App\Models\Job, 
                ['title', 'description'], $q),

            'library' => $this->safeSearch(new \App\Models\Library, 
                ['title', 'summary'], $q),

            'members' => $this->safeSearch(new \App\Models\Member, 
                ['name', 'organization'], $q),

            'people' => $this->safeSearch(new \App\Models\People, 
                ['name', 'bio'], $q),

            'posts' => $this->safeSearch(new \App\Models\Post, 
                ['title', 'content'], $q),

            'projects' => $this->safeSearch(new \App\Models\Project, 
                ['name', 'summary', 'donor'], $q),

            'impacts' => $this->safeSearch(new \App\Models\Impact, 
                ['name', 'summary', 'program'], $q),

            'volunteers' => $this->safeSearch(new \App\Models\Volunteer, 
                ['name', 'skills'], $q),
        ]);
    }
}
