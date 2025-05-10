<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Course;             
use App\Models\Module;             
use App\Models\Content;           

class CourseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'level' => 'required|string',
            'price' => 'required|string',
            'modules' => 'required|array',
            'modules.*.title' => 'required|string',
            'modules.*.contents' => 'required|array',
            'modules.*.contents.*.title' => 'required|string',
            'modules.*.contents.*.video_type' => 'required|string',
            'modules.*.contents.*.url' => 'required|string',
            'modules.*.contents.*.length' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $course = Course::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'level' => $request->level,
                'price' => $request->price,
            ]);

            foreach ($request->modules as $moduleData) {
                $module = $course->modules()->create([
                    'title' => $moduleData['title']
                ]);

                foreach ($moduleData['contents'] as $contentData) {
                    $module->contents()->create([
                        'title' => $contentData['title'],
                        'video_type' => $contentData['video_type'],
                        'url' => $contentData['url'],
                        'length' => $contentData['length'],
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Course, modules, and contents saved successfully.'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to store course: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('courses.create');
    }
}
