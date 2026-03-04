<?php
namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ManagerController extends Controller
{
    public function index()
    {
        try {
            $departments = Department::all();
            return view('Managers.list', compact('departments'));
        } catch (Exception $e) {

            return response()->json([
                'status'  => 403,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ]);
        }
    }

    public function fetchManagers(Request $request)
    {
        try {
            $query = Employee::with('department')->where('role_id', 2);
            if ($request->search) {
                $query->where('full_name', 'like', '%' . $request->search . '%');
            }
            $managers = $query->latest()->paginate(10);
            return response()->json([
                'status'   => 200,
                'managers' => $managers
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 403,
                'message' => 'Failed to fetch managers',
                'error'   => $e->getMessage()
            ]);
        }
    }

    

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name'     => 'required|string|max:255',
                'email'         => 'required|email|unique:employees,email',
                'department_id' => 'nullable|exists:departments,id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()
                ]);
            }
            $employeeCode = 'MNG' . rand(1000, 9999);
            while(Employee::where('employee_code', $employeeCode)->exists()) {
                $employeeCode = 'MNG' . rand(1000, 9999);
            }
            $manager = new Employee();
            $manager->full_name = $request->full_name;
            $manager->email = $request->email;
            $manager->department_id = $request->department_id ?? null;
            $manager->employee_code = $employeeCode;
            $manager->role_id = 2;
            $manager->save();

            return response()->json([
                'status' => 200,
                'message' => 'Manager Added Successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong while adding manager',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Request $request)
    {
        try {
            $manager = Employee::where('role_id', 2)->find($request->id);
            if (!$manager) {
                return response()->json([
                    'status'  => 404,
                    'message' => 'Manager Not Found'
                ]);
            }
            return response()->json([
                'status'  => 200,
                'manager' => $manager
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 403,
                'message' => 'Error fetching manager',
                'error'   => $e->getMessage()
            ]);
        }
    }
    public function update(Request $request)
    {
        try {
            // Find the manager
            $manager = Employee::where('role_id', 2)->find($request->id);
            if (!$manager) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Manager Not Found'
                ]);
            }
            $validator = Validator::make($request->all(), [
                'full_name'     => 'required|string|max:255',
                'email'         => 'required|email|unique:employees,email,' . $manager->id,
                'department_id' => 'nullable|exists:departments,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()
                ]);
            }
            $manager->full_name     = $request->full_name;
            $manager->email         = $request->email;
            $manager->department_id = $request->department_id ?? null;
            $manager->save();
            return response()->json([
                'status' => 200,
                'message' => 'Manager Updated Successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong while updating manager',
                'error'   => $e->getMessage()
            ]);
        }
    }
    public function destroy(Request $request)
    {
        try {
            $manager = Employee::where('role_id', 2)->find($request->id);
            if (!$manager) {
                return response()->json([
                    'status'  => 404,
                    'message' => 'Manager Not Found'
                ]);
            }
            $manager->delete();
            return response()->json([
                'status'  => 200,
                'message' => 'Manager Deleted Successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 403,
                'message' => 'Failed to delete manager',
                'error'   => $e->getMessage()
            ]);
        }
    }
}