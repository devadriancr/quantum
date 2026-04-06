<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPrefix;
use Illuminate\Http\Request;

class ProjectPrefixController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectPrefix::with('project');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $prefixes = $query->orderBy('code')->paginate(10)->withQueryString();

        return view('project-prefixes.index', compact('prefixes'));
    }

    public function create()
    {
        $projects = Project::where('status', 'ACTIVE')->orderBy('code')->get();
        return view('project-prefixes.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'required|string|max:255',
            'project_id'  => 'required|exists:projects,id',
            'description' => 'nullable|string|max:255',
            'status'      => 'required|in:ACTIVE,INACTIVE',
        ]);

        ProjectPrefix::create($data);

        return redirect()->route('project-prefixes.index')
            ->with('success', __('Prefijo creado correctamente.'));
    }

    public function show(ProjectPrefix $projectPrefix)
    {
        $projectPrefix->load('project');
        return view('project-prefixes.show', compact('projectPrefix'));
    }

    public function edit(ProjectPrefix $projectPrefix)
    {
        $projects = Project::where('status', 'ACTIVE')->orderBy('code')->get();
        return view('project-prefixes.edit', compact('projectPrefix', 'projects'));
    }

    public function update(Request $request, ProjectPrefix $projectPrefix)
    {
        $data = $request->validate([
            'code'        => 'required|string|max:255',
            'project_id'  => 'required|exists:projects,id',
            'description' => 'nullable|string|max:255',
            'status'      => 'required|in:ACTIVE,INACTIVE',
        ]);

        $projectPrefix->update($data);

        return redirect()->route('project-prefixes.show', $projectPrefix)
            ->with('success', __('Prefijo actualizado correctamente.'));
    }

    public function destroy(ProjectPrefix $projectPrefix)
    {
        $projectPrefix->delete();

        return redirect()->route('project-prefixes.index')
            ->with('success', __('Prefijo eliminado correctamente.'));
    }
}
