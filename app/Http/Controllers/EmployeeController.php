<?php
namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Exception;

class EmployeeController extends Controller
{
    public function index()
    {
        try {
            $departments = Department::all();
            $managers    = Employee::where('role_id', 2)->get(); 
            return view('Employees.list', compact('departments', 'managers'));
        } catch (Exception $e) {
            return response()->json([
                'status' => 403, 
                'message' => 'Something went wrong', 
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getManagersByDept(Request $request)
    {
        try {
            $managers = Employee::where('role_id', 2)
                ->where('department_id', $request->department_id)
                ->get(['id', 'full_name']);
            return response()->json($managers);
        } catch (Exception $e) {
            return response()->json([
                'status' => 403, 
                'message' => 'Failed to fetch managers', 
                'error' => $e->getMessage()
            ]);
        }
    }

    public function fetchEmployees(Request $request)
    {
        try {
            $employees = Employee::with(['department', 'manager'])->where('role_id', 3)
                ->when($request->search, function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('full_name', 'like', "%{$request->search}%")
                          ->orWhere('employee_code', 'like', "%{$request->search}%");
                    });
                })
                ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
                ->when($request->manager_id, fn($q) => $q->where('manager_id', $request->manager_id))
                ->when($request->from_date, fn($q) => $q->whereDate('joining_date', '>=', $request->from_date))
                ->when($request->to_date, fn($q) => $q->whereDate('joining_date', '<=', $request->to_date))
                ->latest()
                ->paginate(10);

            return response()->json($employees);
        } catch (Exception $e) {
            return response()->json([
                'status' => 403, 
                'message' => 'Failed to fetch employees', 
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name'     => 'required|string|max:255',
                'employee_code' => 'required|string|unique:employees,employee_code',
                'email'         => 'required|email|unique:employees,email',
                'department_id' => 'required|exists:departments,id',
                'manager_id'    => 'nullable|exists:employees,id',
                'joining_date'  => 'nullable|date',
                'phone'         => 'nullable|string',
                'address'       => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 400);
            }

            $employee = new Employee();
            $employee->full_name     = $request->full_name;
            $employee->email         = $request->email;
            $employee->employee_code = $request->employee_code;
            $employee->department_id = $request->department_id;
            $employee->manager_id    = $request->manager_id;
            $employee->joining_date  = $request->joining_date;
            $employee->phone         = $request->phone;
            $employee->address       = $request->address;
            $employee->role_id       = 3;
            $employee->save();

            return response()->json([
                'status' => 200,
                'message' => 'Employee Added Successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 403, 
                'message' => 'Failed to add employee', 
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Request $request)
    {
        try {
            $employee = Employee::find($request->id);
            if (!$employee) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Employee Not Found'
                ]);
            }
            if ($employee->joining_date) {
                $employee->joining_date = Carbon::parse($employee->joining_date)->format('Y-m-d');
            }

            return response()->json([
                'status' => 200,
                'message' => 'Employee fetched successfully',
                'employee' => $employee
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 403,
                'message' => 'Failed to fetch employee',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            $employee = Employee::find($request->id);
            if (!$employee) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Employee Not Found'
                ]);
            }

            $validator = Validator::make($request->all(), [
                'full_name'     => 'required|string|max:255',
                'employee_code' => ['required','string',Rule::unique('employees','employee_code')->ignore($employee->id)],
                'email'         => ['required','email',Rule::unique('employees','email')->ignore($employee->id)],
                'department_id' => 'required|exists:departments,id',
                'manager_id'    => 'nullable|exists:employees,id',
                'joining_date'  => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 400);
            }

            $employee->full_name     = $request->full_name;
            $employee->email         = $request->email;
            $employee->employee_code = $request->employee_code;
            $employee->department_id = $request->department_id;
            $employee->manager_id    = $request->manager_id;
            $employee->joining_date  = $request->joining_date;
            $employee->phone         = $request->phone;
            $employee->address       = $request->address;
            $employee->save();

            return response()->json([
                'status' => 200,
                'message' => 'Employee Updated Successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 403, 
                'message' => 'Failed to update employee', 
                'error' => $e->getMessage()
            ]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $employee = Employee::find($request->id);
            if (!$employee) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Employee Not Found'
                ], 404);
            }

            $employee->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Employee Deleted Successfully!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 403,
                'message' => 'Failed to delete employee',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Request $request)
    {
        try {
            $employee = Employee::with(['department', 'manager'])->find($request->id);
            if (!$employee) abort(404, 'Employee Not Found');
            return view('Employees.profile', compact('employee'));
        } catch (Exception $e) {
            return response()->json([
                'status' => 403, 
                'message' => 'Failed to fetch employee details', 
                'error' => $e->getMessage()
            ]);
        }
    }
}