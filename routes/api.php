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

Route::post('admin/login',[CustomerApiController::class,'loginProcess']);

Route::middleware(['auth:sanctum'])->group(function(){
    //Company
    Route::post('company-create',[CompanyApiController::class,'companyCreate']);
    Route::post('companies-update/{id}',[CompanyApiController::class,'companyUpdate']);
    Route::delete('companies-delete/{id}',[CompanyApiController::class,'companyDelete']);
    Route::get('companies',[CompanyApiController::class,'company']);
    Route::get('companies/{id}',[CompanyApiController::class,'companyDetail']);

    //Position
    Route::post('position-create',[PositionApiController::class,'positionCreate']);
    Route::get('positions',[PositionApiController::class,'position']);
    Route::get('positions/{id}',[PositionApiController::class,'positionDetail']);
    Route::post('positions-update/{id}',[PositionApiController::class,'positionUpdate']);
    Route::delete('positions-delete/{id}',[PositionApiController::class,'positionDelete']);

    //Customers
    Route::post('customer-create',[CustomerApiController::class,'customerCreate']);
    Route::get('customers',[CustomerApiController::class,'customer']);
    Route::post('customers-update/{id}',[CustomerApiController::class,'customerUpdate']);
    Route::delete('customers-delete/{id}',[CustomerApiController::class,'customerDelete']);
    Route::get('customers/{id}',[CustomerApiController::class,'customerDetails']);

    //Departments
    Route::post('department-create',[DepartmentApiController::class, 'departmentCreate']);
    Route::get('departments',[DepartmentApiController::class, 'department']);
    Route::get('departments/{id}',[DepartmentApiController::class,'departmentDetail']);
    Route::post('departments-update/{id}',[DepartmentApiController::class, 'departmentUpdate']);
    Route::delete('departments-delete/{id}',[DepartmentApiController::class, 'departmentDelete']);

    //Employee
    Route::post('employee-create',[EmployeeApiController::class, 'employeeCreate']);
    Route::get('employees',[EmployeeApiController::class, 'employee']);
    Route::get('employees/{id}',[EmployeeApiController::class,'employeeDetail']);
    Route::post('employees-update/{id}',[EmployeeApiController::class, 'employeeUpdate']);
    Route::delete('employees-delete/{id}',[EmployeeApiController::class, 'employeeDelete']);

    //Project
    Route::post('project-create',[ProjectApiController::class,'projectCreate']);
    Route::get('projects',[ProjectApiController::class,'project']);
    Route::get('projects/{id}',[ProjectApiController::class,'projectDetail']);
    Route::delete('projects-delete/{id}',[ProjectApiController::class,'projectDelete']);
    Route::post('projects-update/{id}',[ProjectApiController::class,'projectUpdate']);
    //Supervisor
    Route::get('projects/supervisor/{id}',[ProjectApiController::class,'getSupervisor']);

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
    Route::get('task-types/{id}',[TaskProjectTypeApiController::class,'taskTypeDetail']);
    Route::post('task-types/{id}',[TaskProjectTypeApiController::class,'update']);
    Route::delete('task-types/{id}',[TaskProjectTypeApiController::class,'delete']);

    //ProjectTypes
    Route::post('project-types',[TaskProjectTypeApiController::class,'createProjectType']);
    Route::get('project-types',[TaskProjectTypeApiController::class,'indexProjectType']);
    Route::get('project-types/{id}',[TaskProjectTypeApiController::class,'projectTypeDetail']);
    Route::post('project-types/{id}',[TaskProjectTypeApiController::class,'updateProjectType']);
    Route::delete('project-types/{id}',[TaskProjectTypeApiController::class,'deleteProjectType']);

    //Design
    Route::post('designs',[DesignApiController::class,'create']);
    Route::get('designs',[DesignApiController::class,'index']);
    Route::get('designs/{id}',[DesignApiController::class,'designDetail']);
    Route::post('designs/{id}',[DesignApiController::class,'update']);
    Route::delete('designs/{id}',[DesignApiController::class,'delete']);

    //ShootingCategory
    Route::post('shooting-categories',[ShootingApiController::class,'create']);
    Route::get('shooting-categories',[ShootingApiController::class,'index']);
    Route::get('shooting-categories/{id}',[ShootingApiController::class,'shootingCategoryDetail']);
    // Route::put('shooting-categories/{id}',[ShootingApiController::class,'update']);
    Route::post('shooting-categories/{id}',[ShootingApiController::class,'updateDetail']);
    Route::delete('shooting-categories/{id}/soft-delete',[ShootingApiController::class,'softDeleteCategoryItems']);

    //ShootingAccessory
    Route::post('shooting-accessories',[ShootingApiController::class,'createShootingAccessory']);
    Route::get('shooting-accessories',[ShootingApiController::class,'indexShootingAccessory']);
    Route::get('shooting-accessories/{id}',[ShootingApiController::class,'shootingAccessoryDetail']);
    // Route::put('shooting-accessories/{id}',[ShootingApiController::class,'updateShootingAccessory']);
    Route::post('shooting-accessories/{id}',[ShootingApiController::class,'updateShootingAccessoryDetail']);
    Route::delete('shooting-accessories/{id}',[ShootingApiController::class,'deleteShootingAccessory']);
    Route::get('shooting-accessories/{id}',[ShootingApiController::class,'getShootingAccessory']);
});















