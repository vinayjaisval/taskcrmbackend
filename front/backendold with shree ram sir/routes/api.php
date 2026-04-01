<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', [App\Http\Controllers\LoginController::class, 'login']);

// Category==
Route::post('admin/add-category', [App\Http\Controllers\CategoryController::class, 'add_category']);
Route::get('admin/category', [App\Http\Controllers\CategoryController::class, 'category_list']);
Route::get('admin/edit-category/{id?}', [App\Http\Controllers\CategoryController::class, 'edit_category']);
Route::put('admin/update-category/{id?}', [App\Http\Controllers\CategoryController::class, 'update_category']);
Route::get('admin/category_delete/{id?}', [App\Http\Controllers\CategoryController::class, 'category_delete']);
Route::get('admin/all-category-list', [App\Http\Controllers\CategoryController::class, 'all_category_list']);
Route::get('admin/all-category-count', [App\Http\Controllers\CategoryController::class, 'all_category_count']);

Route::get('admin/all_memebr_list', [App\Http\Controllers\AdminController::class, 'all_memebr_list']);
Route::get('admin/memebr_block/{id?}', [App\Http\Controllers\AdminController::class, 'memebr_block']);
Route::get('admin/memebr_active/{id?}', [App\Http\Controllers\AdminController::class, 'memebr_active']);
//module route
// Route::post('admin/add-module',[App\Http\Controllers\AdminController::class, 'addModule']);
// CallerDeskAPI
Route::get('callerdesk', [App\Http\Controllers\CallerDeskControlloer::class, 'caller_desk_webhook']);
Route::get('click_to_call/{id?}/{AstroEdtId?}', [App\Http\Controllers\CallerDeskControlloer::class, 'click_to_call']);

Route::put('admin/update-profile/{id?}', [App\Http\Controllers\AdminController::class, 'update_profile']);
Route::get('admin/edit-enduser/{id?}', [App\Http\Controllers\AdminController::class, 'end_edit_user']);

// Department
Route::post('admin/add-department', [App\Http\Controllers\AdminController::class, 'add_department']);
Route::get('admin/departments', [App\Http\Controllers\AdminController::class, 'department_list']);
Route::get('admin/edit-department/{id?}', [App\Http\Controllers\AdminController::class, 'edit_department']);
Route::put('admin/update-department/{id?}', [App\Http\Controllers\AdminController::class, 'update_department']);
Route::get('admin/department_delete/{id?}', [App\Http\Controllers\AdminController::class, 'department_delete']);
Route::get('admin/all-department-list', [App\Http\Controllers\AdminController::class, 'all_department_list']);

Route::get('admin/all-department-count', [App\Http\Controllers\AdminController::class, 'all_department_count']);

// Agent
Route::post('admin/add-agent', [App\Http\Controllers\AdminController::class, 'add_agent']);
Route::get('admin/agents', [App\Http\Controllers\AdminController::class, 'agent_list']);
Route::get('admin/edit-agent/{id?}', [App\Http\Controllers\AdminController::class, 'edit_agent']);
Route::put('admin/update-agent/{id?}', [App\Http\Controllers\AdminController::class, 'update_agent']);
Route::get('admin/agent_delete/{id?}', [App\Http\Controllers\AdminController::class, 'agent_delete']);
Route::get('admin/all-agent-list', [App\Http\Controllers\AdminController::class, 'all_agent_list']);

// Lead Management
Route::post('admin/add-lead/{id?}', [App\Http\Controllers\LeadController::class, 'add_lead']);
Route::get('admin/leads', [App\Http\Controllers\LeadController::class, 'lead_list']);
Route::get('admin/leads_school', [App\Http\Controllers\LeadController::class, 'lead_list_school']);
Route::get('admin/edit-lead/{id?}', [App\Http\Controllers\LeadController::class, 'edit_lead']);
Route::put('admin/update-lead/{id?}', [App\Http\Controllers\LeadController::class, 'update_lead']);
Route::put('admin/update-lead-followup/{id?}', [App\Http\Controllers\LeadController::class, 'update_lead_followup']);
Route::get('admin/lead_delete/{id?}', [App\Http\Controllers\LeadController::class, 'lead_delete']);
Route::get('admin/leads_assign', [App\Http\Controllers\LeadController::class, 'lead_list_assign']);
Route::get('admin/leads_to_assign', [App\Http\Controllers\LeadController::class, 'leads_to_assign']);



Route::get('admin/lead_count', [App\Http\Controllers\LeadController::class, 'lead_count']);
Route::get('admin/agent_lead_count/{id?}', [App\Http\Controllers\LeadController::class, 'agent_lead_count']);

Route::post('agent/add-lead/{id?}', [App\Http\Controllers\LeadController::class, 'add_lead_agent']);
Route::get('agent/leads/{id?}', [App\Http\Controllers\LeadController::class, 'lead_list_agent']);
Route::get('agent/all-agent-list/{id?}', [App\Http\Controllers\AdminController::class, 'all_agent_list_agent']);
Route::post('admin/lead-assign-agent/{id?}', [App\Http\Controllers\LeadController::class, 'lead_assign_agent']);




Route::post('department/add-lead/{id?}', [App\Http\Controllers\LeadController::class, 'add_lead_department']);
Route::get('department/leads/{id?}', [App\Http\Controllers\LeadController::class, 'lead_list_department']);
Route::get('department/leads_school/{id?}', [App\Http\Controllers\LeadController::class, 'lead_list_department_school']);
Route::get('department/all-department-list/{id?}', [App\Http\Controllers\AdminController::class, 'all_department_list_dep']);

Route::put('admin/update-lead-assign/{id?}', [App\Http\Controllers\LeadController::class, 'update_lead_assign']);

Route::get('agent/leads_school/{id?}', [App\Http\Controllers\LeadController::class, 'lead_list_agent_school']);

// Bulk Upload
Route::post('admin/upload_bulk_lead/{id?}', [App\Http\Controllers\LeadController::class, 'upload_bulk_lead']);

Route::post('admin/compose-message/{id?}', [App\Http\Controllers\LeadController::class, 'compose_message']);




// Source==
Route::post('admin/add-source', [App\Http\Controllers\SourceController::class, 'add_source']);
Route::get('admin/source', [App\Http\Controllers\SourceController::class, 'source_list']);
Route::get('admin/edit-source/{id?}', [App\Http\Controllers\SourceController::class, 'edit_source']);
Route::put('admin/update-source/{id?}', [App\Http\Controllers\SourceController::class, 'update_source']);
Route::get('admin/source_delete/{id?}', [App\Http\Controllers\SourceController::class, 'source_delete']);
Route::get('admin/all-source-list', [App\Http\Controllers\SourceController::class, 'all_source_list']);
Route::get('admin/all-source-count', [App\Http\Controllers\SourceController::class, 'all_source_count']);

// Sender==
Route::post('admin/add-sender', [App\Http\Controllers\SenderController::class, 'add_sender']);
Route::get('admin/sender', [App\Http\Controllers\SenderController::class, 'sender_list']);
Route::get('admin/edit-sender/{id?}', [App\Http\Controllers\SenderController::class, 'edit_sender']);
Route::put('admin/update-sender/{id?}', [App\Http\Controllers\SenderController::class, 'update_sender']);
Route::get('admin/sender_delete/{id?}', [App\Http\Controllers\SenderController::class, 'sender_delete']);
Route::get('admin/all-sender-list', [App\Http\Controllers\SenderController::class, 'all_sender_list']);
Route::get('admin/all-sender-count', [App\Http\Controllers\SenderController::class, 'all_sender_count']);

// Template==
Route::post('admin/add-template', [App\Http\Controllers\TemplateController::class, 'add_template']);
Route::get('admin/template', [App\Http\Controllers\TemplateController::class, 'template_list']);
Route::get('admin/edit-template/{id?}', [App\Http\Controllers\TemplateController::class, 'edit_template']);
Route::put('admin/update-template/{id?}', [App\Http\Controllers\TemplateController::class, 'update_template']);
Route::get('admin/template_delete/{id?}', [App\Http\Controllers\TemplateController::class, 'template_delete']);
Route::get('admin/all-template-list', [App\Http\Controllers\TemplateController::class, 'all_template_list']);
Route::get('admin/all-template-count', [App\Http\Controllers\TemplateController::class, 'all_template_count']);


// Locations
// country==
Route::post('admin/add-country', [App\Http\Controllers\LocationController::class, 'add_country']);
Route::get('admin/country', [App\Http\Controllers\LocationController::class, 'country_list']);
Route::get('admin/edit-country/{id?}', [App\Http\Controllers\LocationController::class, 'edit_country']);
Route::put('admin/update-country/{id?}', [App\Http\Controllers\LocationController::class, 'update_country']);
Route::get('admin/country_delete/{id?}', [App\Http\Controllers\LocationController::class, 'country_delete']);
Route::get('admin/all-country-list', [App\Http\Controllers\LocationController::class, 'all_country_list']);
Route::get('admin/all-country-count', [App\Http\Controllers\LocationController::class, 'all_country_count']);

// State==
Route::post('admin/add-state', [App\Http\Controllers\LocationController::class, 'add_state']);
Route::get('admin/state', [App\Http\Controllers\LocationController::class, 'state_list']);
Route::get('admin/edit-state/{id?}', [App\Http\Controllers\LocationController::class, 'edit_state']);
Route::put('admin/update-state/{id?}', [App\Http\Controllers\LocationController::class, 'update_state']);
Route::get('admin/state_delete/{id?}', [App\Http\Controllers\LocationController::class, 'state_delete']);
Route::get('admin/all-state-list', [App\Http\Controllers\LocationController::class, 'all_state_list']);
Route::get('admin/all-state-count', [App\Http\Controllers\LocationController::class, 'all_state_count']);


// city==
Route::post('admin/add-city', [App\Http\Controllers\LocationController::class, 'add_city']);
Route::get('admin/city', [App\Http\Controllers\LocationController::class, 'city_list']);
Route::get('admin/edit-city/{id?}', [App\Http\Controllers\LocationController::class, 'edit_city']);
Route::put('admin/update-city/{id?}', [App\Http\Controllers\LocationController::class, 'update_city']);
Route::get('admin/city_delete/{id?}', [App\Http\Controllers\LocationController::class, 'city_delete']);
Route::get('admin/all-city-list', [App\Http\Controllers\LocationController::class, 'all_city_list']);
Route::get('admin/all-city-count', [App\Http\Controllers\LocationController::class, 'all_city_count']);


// ====== Location Punch
Route::post('admin/add-punch-in/{id?}', [App\Http\Controllers\LocationController::class, 'add_punch_in']);
Route::get('admin/punch_list', [App\Http\Controllers\LocationController::class, 'punch_list']);
