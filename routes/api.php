<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportApiController;
use App\Http\Controllers\CompanyApiController;
use App\Http\Controllers\ProjectApiController;
use App\Http\Controllers\CustomerApiController;
use App\Http\Controllers\EmployeeApiController;
use App\Http\Controllers\PositionApiController;
use App\Http\Controllers\DepartmentApiController;
use App\Http\Controllers\AssignedTasksApiController;
use App\Http\Controllers\DesignApiController;
use App\Http\Controllers\ShootingApiController;
use App\Http\Controllers\TaskProjectTypeApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return Auth::user();
});

Route::post('login',[CustomerApiController::class,'loginProcess']);
//Customers
Route::post('customer-create',[CustomerApiController::class,'customerCreate']);
Route::get('customers',[CustomerApiController::class,'customer']);
Route::post('customers-update/{id}',[CustomerApiController::class,'customerUpdate']);
Route::delete('customers-delete/{id}',[CustomerApiController::class,'customerDelete']);

//Departments
Route::post('department-create',[DepartmentApiController::class, 'departmentCreate']);
Route::get('departments',[DepartmentApiController::class, 'department']);
Route::post('departments-update/{id}',[DepartmentApiController::class, 'departmentUpdate']);
Route::delete('departments-delete/{id}',[DepartmentApiController::class, 'departmentDelete']);

//Employee
Route::post('employee-create',[EmployeeApiController::class, 'employeeCreate']);
Route::get('employees',[EmployeeApiController::class, 'employee']);
Route::post('employees-update/{id}',[EmployeeApiController::class, 'employeeUpdate']);
Route::delete('employees-delete/{id}',[EmployeeApiController::class, 'employeeDelete']);

//Company
Route::post('company-create',[CompanyApiController::class,'companyCreate']);
Route::get('companies',[CompanyApiController::class,'company']);
Route::post('companies-update/{id}',[CompanyApiController::class,'companyUpdate']);
Route::delete('companies-delete/{id}',[CompanyApiController::class,'companyDelete']);


//Position
Route::post('position-create',[PositionApiController::class,'positionCreate']);
Route::get('positions',[PositionApiController::class,'position']);
Route::post('positions-update/{id}',[PositionApiController::class,'positionUpdate']);
Route::delete('positions-delete/{id}',[PositionApiController::class,'positionDelete']);

//Project
Route::post('project-create',[ProjectApiController::class,'projectCreate']);
Route::get('projects',[ProjectApiController::class,'project']);
Route::delete('projects-delete/{id}',[ProjectApiController::class,'projectDelete']);
Route::post('projects-update/{id}',[ProjectApiController::class,'projectUpdate']);
//Supervisor
Route::get('projects/supervisor/{id}',[ProjectApiController::class,'getSupervisor']);

//CSRF
// Route::post('clearCSRFToken', 'CSRFTokenController@clearCSRFToken');

//Assigned Tasks
Route::post('assigned-tasks-create',[AssignedTasksApiController::class,'assignedTasksCreate']);
Route::get('assigned-tasks',[AssignedTasksApiController::class,'assignedTasks']);
Route::delete('assigned-tasks/delete/{id}',[AssignedTasksApiController::class,'assignedTasksDelete']);
Route::post('assigned-tasks/update/{id}',[AssignedTasksApiController::class,'assignedTasksUpdate']);

Route::get('assigned-tasks/{id}',[AssignedTasksApiController::class,'assignedTasksDetails']);

//Report
Route::post('report-create',[ReportApiController::class,'reportCreate']);
Route::get  ('reports',[ReportApiController::class,'report']);
Route::post('reports-update/{id}',[ReportApiController::class,'reportUpdate']);
Route::delete('reports-delete/{id}',[ReportApiController::class,'reportDelete']);
Route::get('reports/{id}',[ReportApiController::class,'index']);

//TaskProjectType
Route::post('task-types',[TaskProjectTypeApiController::class,'create']);
Route::get('task-types',[TaskProjectTypeApiController::class,'index']);
Route::post('task-types/{id}',[TaskProjectTypeApiController::class,'update']);
Route::delete('task-types/{id}',[TaskProjectTypeApiController::class,'delete']);

Route::post('project-types',[TaskProjectTypeApiController::class,'createProjectType']);
Route::get('project-types',[TaskProjectTypeApiController::class,'indexProjectType']);
Route::post('project-types/{id}',[TaskProjectTypeApiController::class,'updateProjectType']);
Route::delete('project-types/{id}',[TaskProjectTypeApiController::class,'deleteProjectType']);

//Design
Route::post('designs',[DesignApiController::class,'create']);
Route::get('designs',[DesignApiController::class,'index']);
Route::post('designs/{id}',[DesignApiController::class,'update']);
Route::delete('designs/{id}',[DesignApiController::class,'delete']);

//ShootingCategory
Route::post('shooting-categories',[ShootingApiController::class,'create']);
Route::get('shooting-categories',[ShootingApiController::class,'index']);
Route::put('shooting-categories/{id}',[ShootingApiController::class,'update']);
Route::delete('shooting-categories/{id}/soft-delete',[ShootingApiController::class,'softDeleteCategoryItems']);

Route::post('shooting-accessories',[ShootingApiController::class,'createShootingAccessory']);
Route::get('shooting-accessories',[ShootingApiController::class,'indexShootingAccessory']);
Route::put('shooting-accessories/{id}',[ShootingApiController::class,'updateShootingAccessory']);
Route::delete('shooting-accessories/{id}',[ShootingApiController::class,'deleteShootingAccessory']);

Route::get('shooting-accessories/{id}',[ShootingApiController::class,'getShootingAccessory']);


