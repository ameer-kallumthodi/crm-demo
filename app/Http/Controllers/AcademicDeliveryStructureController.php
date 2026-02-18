<?php

namespace App\Http\Controllers;

use App\Models\AcademicDeliveryStructure;
use App\Models\Course;
use Illuminate\Http\Request;

class AcademicDeliveryStructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicDeliveryStructures = AcademicDeliveryStructure::with('course')->get();
        return view('admin.master-data.academic-delivery-structures.index', compact('academicDeliveryStructures'));
    }

    public function ajax_add()
    {
        $courses = Course::where('is_active', 1)->get();
        return view('admin.master-data.academic-delivery-structures.add', compact('courses'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'course_id' => 'required',
        ]);

        AcademicDeliveryStructure::create([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'status' => 1,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.academic-delivery-structures.index')->with('success', 'Academic Delivery Structure saved successfully.');
    }

    public function ajax_edit($id)
    {
        $academicDeliveryStructure = AcademicDeliveryStructure::find($id);
        $courses = Course::where('is_active', 1)->get();
        return view('admin.master-data.academic-delivery-structures.edit', compact('academicDeliveryStructure', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'course_id' => 'required',
        ]);

        $academicDeliveryStructure = AcademicDeliveryStructure::find($id);
        $academicDeliveryStructure->update([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.academic-delivery-structures.index')->with('success', 'Academic Delivery Structure updated successfully.');
    }

    public function delete($id)
    {
        AcademicDeliveryStructure::find($id)->delete();
        return redirect()->route('admin.academic-delivery-structures.index')->with('success', 'Academic Delivery Structure deleted successfully.');
    }
}
