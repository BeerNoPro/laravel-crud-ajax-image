<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employee.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'phone' => 'required|max:191',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $employee = new Employee;
            $employee->name = $request->input('name');
            $employee->phone = $request->input('phone');

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('uploads/employee/', $filename);
                $employee->image = $filename;
            }
            $employee->save();

            return response()->json([
                'status' => 200,
                'message' => 'Employee Image Data Added Success'
            ]);
        }
    }

    public function api()
    {
        $employee = Employee::all();
        return response()->json([
            'employee' => $employee,
        ]);
    }

    public function show(Request $request)
    {

        $employee = DB::table('employees')->paginate(3);
        $page = 1;
        if (isset($request->page)) {
            $page = $request->page;
        }
        $index = ($page - 1) * 3 + 1;

        return response()->json([
            'employee' => $employee,
            'index' => $index
        ]);
    }

    public function edit($id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            return response()->json([
                'status' => 200,
                'employee' => $employee,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found!!',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'phone' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $employee = Employee::find($id);
            if ($employee) {
                $employee->name = $request->input('name');
                $employee->phone = $request->input('phone');
                if ($request->hasFile('image')) {
                    $path = 'uploads/employee/' . $employee->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }

                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('uploads/employee/', $filename);
                    $employee->image = $filename;
                }
                $employee->update();
    
                return response()->json([
                    'status' => 200,
                    'message' => 'Employee Image Data Uploaded Success'
                ]);

            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Employee not found!'
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            $path = 'uploads/employee/' . $employee->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $employee->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Employee deleted successfully'
            ]);

        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Employee not found!'
            ]);
        }
    }
}
