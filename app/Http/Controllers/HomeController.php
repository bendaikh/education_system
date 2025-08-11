<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Teacher;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke()
    {
        $courses = Course::query()->latest()->take(6)->get(['id','title','stream','description','image']);
        $teachers = Teacher::query()->latest()->take(8)->get(['id','name','role','image']);

        return Inertia::render('Home', [
            'courses' => $courses,
            'staff' => $teachers,
        ]);
    }
}
