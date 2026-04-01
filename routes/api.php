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
// == Avnish ---

Route::post('loginlatest', [App\Http\Controllers\LoginController::class, 'loginlatest']);
// == Buisness ---

Route::get('/test', function () {
        return response()->json(['status' => 'API Working']);
    });
    
Route::post('admin/add-buisness', [App\Http\Controllers\BuisnessController::class,'add_buisness']);
Route::get('admin/edit-buisness/{id?}', [App\Http\Controllers\BuisnessController::class,'edit_buisness']);
Route::get('admin/all-buisness', [App\Http\Controllers\BuisnessController::class,'all_buisness']);
Route::post('admin/update-buisness/{id?}', [App\Http\Controllers\BuisnessController::class,'update_buisness']);
Route::get('admin/delete-buisness/{id?}', [App\Http\Controllers\BuisnessController::class,'delete_buisness']);

// == Project ---
Route::post('admin/start-project', [App\Http\Controllers\ProjectController::class,'start_project']);
Route::post('admin/add-projectav', [App\Http\Controllers\ProjectController::class,'add_projectav']);
Route::get('admin/edit-projectav/{id?}', [App\Http\Controllers\ProjectController::class,'edit_projectav']);
Route::get('admin/all-projectav/{id?}', [App\Http\Controllers\ProjectController::class,'all_projectav']);
Route::post('admin/update-projectav/{id?}', [App\Http\Controllers\ProjectController::class,'update_projectav']);
Route::get('admin/delete-projectav/{id?}', [App\Http\Controllers\ProjectController::class,'delete_projectav']);
Route::get('admin/get-projectonbuisness/{id?}', [App\Http\Controllers\ProjectController::class,'get_projectbuis']);


//==role--

Route::post('admin/add-roles', [App\Http\Controllers\RolesController::class,'add_roles']);
Route::get('admin/edit-roles/{id?}', [App\Http\Controllers\RolesController::class,'edit_roles']);
Route::get('admin/all-roles', [App\Http\Controllers\RolesController::class,'all_roles']);
Route::post('admin/update-roles/{id?}', [App\Http\Controllers\RolesController::class,'update_roles']);
Route::get('admin/delete-roles/{id?}', [App\Http\Controllers\RolesController::class,'delete_roles']);

//==employee--

Route::post('admin/add-employee', [App\Http\Controllers\EmployeeController::class,'add_employee']);
Route::get('admin/edit-employee/{id?}', [App\Http\Controllers\EmployeeController::class,'edit_employee']);
Route::get('admin/all-employee', [App\Http\Controllers\EmployeeController::class,'all_employee']);
Route::post('admin/update-employee/{id?}', [App\Http\Controllers\EmployeeController::class,'update_employee']);
Route::get('admin/delete-employee/{id?}', [App\Http\Controllers\EmployeeController::class,'delete_employee']);

//==module--
Route::post('admin/add-module1', [App\Http\Controllers\ModuleController::class,'add_module1']);
Route::get('admin/edit-module1/{id?}', [App\Http\Controllers\ModuleController::class,'edit_module1']);
Route::get('admin/all-module1/{id?}', [App\Http\Controllers\ModuleController::class,'all_Module1']);
Route::post('admin/update-module1/{id?}', [App\Http\Controllers\ModuleController::class,'update_Module1']);
Route::get('admin/delete-module1/{id?}', [App\Http\Controllers\ModuleController::class,'delete_module1']);

//==Team--
Route::post('admin/add-team',[App\Http\Controllers\TeamController::class,'add_team']);
Route::get('admin/edit-team/{id?}', [App\Http\Controllers\TeamController::class,'edit_team']);
Route::get('admin/all-team/{id?}', [App\Http\Controllers\TeamController::class,'all_team']);
Route::post('admin/update-team/{id?}', [App\Http\Controllers\TeamController::class,'update_team']);
Route::get('admin/delete-team/{id?}', [App\Http\Controllers\TeamController::class,'delete_team']);

//==task--
Route::post('admin/add-task1', [App\Http\Controllers\TaskController::class,'add_task1']);
Route::get('admin/edit-task/{id?}', [App\Http\Controllers\TaskController::class,'edit_task1']);
Route::get('admin/all-task1/{id?}', [App\Http\Controllers\TaskController::class,'all_task1']);
Route::post('admin/update-task1/{id?}', [App\Http\Controllers\TaskController::class,'update_task1']);
Route::get('admin/delete-task/{id?}', [App\Http\Controllers\TaskController::class,'delete_task1']);

//==status--
Route::post('admin/add-statusnew', [App\Http\Controllers\StatusController::class,'add_statusnew']);
Route::get('admin/edit-statusnew/{id?}', [App\Http\Controllers\StatusController::class,'edit_statusnew']);
Route::get('admin/all-statusnew/{id?}', [App\Http\Controllers\StatusController::class,'all_statusnew']);
Route::post('admin/update-status/{id?}', [App\Http\Controllers\StatusController::class,'update_statusnew']);
Route::get('admin/delete-status/{id?}', [App\Http\Controllers\StatusController::class,'delete_statusnew']);

// == Avnish Apies End Here---
// == Kamal Apes ==
Route::get('modules',[App\Http\Controllers\ProjectController::class,'GetModule']);
Route::get('tasks',[App\Http\Controllers\ProjectController::class,'GetTasks']);
Route::get('onlyteamlead/{tl_id}',[App\Http\Controllers\EmployeeController::class,'onlyTeamLead']);
Route::get('onlymanager/{bs_id}',[App\Http\Controllers\EmployeeController::class,'onlyManager']);
// == Kamal Apies End Here ==
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('login', [App\Http\Controllers\LoginController::class, 'login']);

// == Dashboard ---
Route::get('admin/get_to_task_count/{id?}', [App\Http\Controllers\LeadController::class, 'get_to_task_count']);


// Category==
Route::post('admin/add-category', [App\Http\Controllers\CategoryController::class, 'add_category']);
Route::get('admin/category', [App\Http\Controllers\CategoryController::class, 'category_list']);
Route::get('admin/edit-category/{id?}', [App\Http\Controllers\CategoryController::class, 'edit_category']);
Route::put('admin/update-category/{id?}', [App\Http\Controllers\CategoryController::class, 'update_category']);
Route::get('admin/category_delete/{id?}', [App\Http\Controllers\CategoryController::class, 'category_delete']);
Route::get('admin/all-category-list', [App\Http\Controllers\CategoryController::class, 'all_category_list']);
Route::get('admin/all-category-count', [App\Http\Controllers\CategoryController::class, 'all_category_count']);

Route::get('admin/all-status-count', [App\Http\Controllers\CategoryController::class, 'all_status_count']);
Route::get('admin/all-status-count-project/{id?}', [App\Http\Controllers\CategoryController::class, 'all_status_count_project']);



Route::get('admin/all_memebr_list', [App\Http\Controllers\AdminController::class, 'all_memebr_list']);
Route::get('admin/memebr_block/{id?}', [App\Http\Controllers\AdminController::class, 'memebr_block']);
Route::get('admin/memebr_active/{id?}', [App\Http\Controllers\AdminController::class, 'memebr_active']);
//module
Route::post('admin/add-module',[App\Http\Controllers\AdminController::class, 'addModule']);
Route::get('admin/read-module',[App\Http\Controllers\AdminController::class,'readModule']);
Route::get('admin/search-module',[App\Http\Controllers\AdminController::class,'searchModule']);
Route::get('admin/read_module_all',[App\Http\Controllers\AdminController::class,'read_module_all']);
Route::get('admin/delete-module/{id}',[App\Http\Controllers\AdminController::class,'deleteModule']);
Route::post('admin/update-module/{id}',[App\Http\Controllers\AdminController::class, 'updateModule']);
Route::get('admin/update-module/{id}',[App\Http\Controllers\AdminController::class, 'updateModuleData']);
// CallerDeskAPI
Route::get('callerdesk', [App\Http\Controllers\CallerDeskControlloer::class, 'caller_desk_webhook']);
Route::get('click_to_call/{id?}/{AstroEdtId?}', [App\Http\Controllers\CallerDeskControlloer::class, 'click_to_call']);

Route::put('admin/update-profile/{id?}', [App\Http\Controllers\AdminController::class, 'update_profile']);
Route::get('admin/edit-enduser/{id?}', [App\Http\Controllers\AdminController::class, 'end_edit_user']);

// Projects
Route::post('admin/add-project', [App\Http\Controllers\AdminController::class, 'add_project']);
Route::get('admin/projects', [App\Http\Controllers\AdminController::class, 'project_list']);
Route::get('admin/edit-project/{id?}', [App\Http\Controllers\AdminController::class, 'edit_project']);
Route::put('admin/update-project/{id?}', [App\Http\Controllers\AdminController::class, 'update_project']);
Route::get('admin/project_delete/{id?}', [App\Http\Controllers\AdminController::class, 'project_delete']);
Route::get('admin/all-project-list', [App\Http\Controllers\AdminController::class, 'all_project_list']);

Route::get('admin/all-project-count', [App\Http\Controllers\AdminController::class, 'all_project_count']);

Route::get('admin/skills_details/{id?}', [App\Http\Controllers\AdminController::class, 'skills_details']);
Route::get('admin/emp_details/{id?}', [App\Http\Controllers\AdminController::class, 'emp_details']);

// Agent
Route::post('admin/add-agent', [App\Http\Controllers\AdminController::class, 'add_agent']);
Route::get('admin/agents', [App\Http\Controllers\AdminController::class, 'agent_list']);
Route::get('admin/edit-agent/{id?}', [App\Http\Controllers\AdminController::class, 'edit_agent']);
Route::put('admin/update-agent/{id?}', [App\Http\Controllers\AdminController::class, 'update_agent']);
Route::get('admin/agent_delete/{id?}', [App\Http\Controllers\AdminController::class, 'agent_delete']);
Route::get('admin/all-agent-list', [App\Http\Controllers\AdminController::class, 'all_agent_list']);
Route::get('admin/all-agent-list-data', [App\Http\Controllers\AdminController::class, 'all_agent_list_data']);
Route::get('admin/all-agent-list-data-admin/{id?}', [App\Http\Controllers\AdminController::class, 'all_agent_list_data_admin']);

Route::get('admin/all-agent-list-data-project/{id?}', [App\Http\Controllers\AdminController::class, 'all_agent_list_data_project']);

Route::get('admin/all-agent-chat-group/{id?}', [App\Http\Controllers\AdminController::class, 'all_agent_chat_group']);





// Team Lead
Route::post('admin/add-teamlead', [App\Http\Controllers\AdminController::class, 'add_teamlead']);
Route::get('admin/teamleads', [App\Http\Controllers\AdminController::class, 'teamlead_list']);
Route::get('admin/edit-teamlead/{id?}', [App\Http\Controllers\AdminController::class, 'edit_teamlead']);
Route::put('admin/update-teamlead/{id?}', [App\Http\Controllers\AdminController::class, 'update_teamlead']);
Route::get('admin/teamlead_delete/{id?}', [App\Http\Controllers\AdminController::class, 'teamlead_delete']);
Route::get('admin/all-teamlead-list', [App\Http\Controllers\AdminController::class, 'all_teamlead_list']);
Route::get('admin/all-teamlead-list-data', [App\Http\Controllers\AdminController::class, 'all_teamlead_list_data']);

// Agent Admin
Route::post('admin/add-agent_admin/{id?}', [App\Http\Controllers\AdminController::class, 'add_agent_admin']);
Route::get('admin/agents_admin/{id?}', [App\Http\Controllers\AdminController::class, 'agent_list_admin']);
Route::get('admin/all-agent-list_admin/{id?}', [App\Http\Controllers\AdminController::class, 'all_agent_list_admin']);
Route::get('admin/all-agent-list_project/{id?}', [App\Http\Controllers\AdminController::class, 'all_agent_list_project']);

// Lead Management
Route::post('admin/add-lead/{id?}', [App\Http\Controllers\LeadController::class, 'add_lead']);
Route::get('admin/leads/{id?}', [App\Http\Controllers\LeadController::class, 'lead_list']);
Route::get('admin/leads_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_project']);

Route::get('admin/leads_school', [App\Http\Controllers\LeadController::class, 'lead_list_school']);
Route::get('admin/edit-lead/{id?}', [App\Http\Controllers\LeadController::class, 'edit_lead']);
Route::post('admin/update-lead/{id?}', [App\Http\Controllers\LeadController::class, 'update_lead']);
Route::post('admin/update-lead-followup/{id?}/{sess_id?}', [App\Http\Controllers\LeadController::class, 'update_lead_followup']);
Route::get('admin/lead_delete/{id?}', [App\Http\Controllers\LeadController::class, 'lead_delete']);
Route::get('admin/leads_assign', [App\Http\Controllers\LeadController::class, 'lead_list_assign']);
Route::get('admin/leads_to_assign', [App\Http\Controllers\LeadController::class, 'leads_to_assign']);
Route::get('admin/leads-log/{id?}', [App\Http\Controllers\LeadController::class, 'leads_log']);

Route::get('admin/leads_todo/{id?}', [App\Http\Controllers\LeadController::class, 'leads_todo']);
Route::get('admin/leads_todo_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_todo_project']);
Route::get('admin/leads_group/{id?}', [App\Http\Controllers\LeadController::class, 'leads_group']);
Route::get('admin/leads_group_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_group_project']);
Route::get('admin/leads_pending/{id?}', [App\Http\Controllers\LeadController::class, 'leads_pending']);
Route::get('admin/leads_pending_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_pending_project']);
Route::get('admin/leads_inprogress/{id?}', [App\Http\Controllers\LeadController::class, 'leads_inprogress']);
Route::get('admin/leads_inprogress_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_inprogress_project']);
Route::get('admin/leads_completed/{id?}', [App\Http\Controllers\LeadController::class, 'leads_completed']);
Route::get('admin/leads_completed_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_completed_project']);
Route::get('admin/leads_due/{id?}', [App\Http\Controllers\LeadController::class, 'leads_due']);
Route::get('admin/leads_due_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_due_project']);


Route::get('admin/leads_users_list/{id?}', [App\Http\Controllers\LeadController::class, 'leads_users_list']);
Route::get('admin/leads_users_list_project/{id?}/{sessid?}', [App\Http\Controllers\LeadController::class, 'leads_users_list_project']);
Route::get('admin/leads_projects_list/{id?}', [App\Http\Controllers\LeadController::class, 'leads_projects_list']);
Route::get('admin/leads_category_list/{id?}', [App\Http\Controllers\LeadController::class, 'leads_category_list']);
Route::get('admin/leads_status_list/{id?}', [App\Http\Controllers\LeadController::class, 'leads_status_list']);




Route::get('admin/timer-calculation/{id?}/{lead?}', [App\Http\Controllers\LeadController::class, 'timer_calculation']);
Route::get('admin/task_dashboard_history/{id?}/{status?}', [App\Http\Controllers\LeadController::class, 'task_dashboard_history']);

Route::get('admin/assignee_details/{id?}', [App\Http\Controllers\LeadController::class, 'assignee_details']);
Route::get('admin/get_user_tot_task/{id?}', [App\Http\Controllers\LeadController::class, 'get_user_tot_task']);
Route::get('admin/get_user_tot_delay_task/{id?}', [App\Http\Controllers\LeadController::class, 'get_user_tot_delay_task']);








// Reports --
Route::get('admin/leads_rep_deadline/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_deadline']);
Route::get('admin/leads_rep_owner/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_owner']);
Route::get('admin/leads_rep_assignee/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_assignee']);
Route::get('admin/leads_rep_del_one_day/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_del_one_day']);
Route::get('admin/leads_rep_del_two_day/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_del_two_day']);
Route::get('admin/leads_rep_del_three_day/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_del_three_day']);
Route::get('admin/leads_rep_del_week/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_del_week']);
Route::get('admin/leads_rep_del_month/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_del_month']);
Route::get('admin/leads_rep_today/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_today']);

// Reports Project --
Route::get('admin/leads_rep_deadline_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_deadline_project']);
Route::get('admin/leads_rep_owner_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_owner_project']);
Route::get('admin/leads_rep_assignee_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_assignee_project']);
Route::get('admin/leads_rep_del_one_day_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_del_one_day_project']);
Route::get('admin/leads_rep_del_two_day_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_del_two_day_project']);
Route::get('admin/leads_rep_del_three_day_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_del_three_day_project']);
Route::get('admin/leads_rep_del_week_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_del_week_project']);
Route::get('admin/leads_rep_del_month_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_del_month_project']);
Route::get('admin/leads_rep_today_project/{id?}', [App\Http\Controllers\LeadController::class, 'leads_rep_today_project']);



Route::get('admin/get_sel_assignee/{id?}', [App\Http\Controllers\LeadController::class, 'get_sel_assignee']);

Route::get('admin/get_sel_assignee_by_skills/{id?}', [App\Http\Controllers\LeadController::class, 'get_sel_assignee_by_skills']);

Route::get('admin/get_sel_assignee_by_project/{id?}', [App\Http\Controllers\LeadController::class, 'get_sel_assignee_by_project']);

Route::get('admin/get_sel_assignee_by_project_team_lead/{id?}', [App\Http\Controllers\LeadController::class, 'get_sel_assignee_by_project_team_lead']);






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
Route::get('admin/all-department-count/{id?}', [App\Http\Controllers\AdminController::class, 'all_department_count']);



Route::put('admin/update-lead-assign/{id?}', [App\Http\Controllers\LeadController::class, 'update_lead_assign']);

Route::get('agent/leads_school/{id?}', [App\Http\Controllers\LeadController::class, 'lead_list_agent_school']);

// Bulk Upload
Route::post('admin/upload_bulk_lead/{id?}', [App\Http\Controllers\LeadController::class, 'upload_bulk_lead']);


// Source==
Route::post('admin/add-source', [App\Http\Controllers\SourceController::class, 'add_source']);
Route::get('admin/source', [App\Http\Controllers\SourceController::class, 'source_list']);
Route::get('admin/edit-source/{id?}', [App\Http\Controllers\SourceController::class, 'edit_source']);
Route::put('admin/update-source/{id?}', [App\Http\Controllers\SourceController::class, 'update_source']);
Route::get('admin/source_delete/{id?}', [App\Http\Controllers\SourceController::class, 'source_delete']);
Route::get('admin/all-source-list/{id?}', [App\Http\Controllers\SourceController::class, 'all_source_list']);
Route::get('admin/all-source-count', [App\Http\Controllers\SourceController::class, 'all_source_count']);



// Skills==
Route::post('admin/add-skills', [App\Http\Controllers\SkillsController::class, 'add_skills']);
Route::get('admin/skills', [App\Http\Controllers\SkillsController::class, 'skills_list']);
Route::get('admin/edit-skills/{id?}', [App\Http\Controllers\SkillsController::class, 'edit_skills']);
Route::put('admin/update-skills/{id?}', [App\Http\Controllers\SkillsController::class, 'update_skills']);
Route::get('admin/skills_delete/{id?}', [App\Http\Controllers\SkillsController::class, 'skills_delete']);
Route::get('admin/all-skills-list', [App\Http\Controllers\SkillsController::class, 'all_skills_list']);
Route::get('admin/all-skills-count', [App\Http\Controllers\SkillsController::class, 'all_skills_count']);
Route::get('admin/get_sel_skills/{id?}', [App\Http\Controllers\SkillsController::class, 'get_sel_skills']);


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


// ======== Mail Send ----

Route::get('mail_send', [App\Http\Controllers\HomeController::class, 'mail_send']);



// === Kamal Apis
Route::put('admin/update-task-module/{id?}', [App\Http\Controllers\AdminController::class, 'update_task_module']);


// Chat APIs
Route::post('agent/start-chat/{id?}/{chatid?}', [App\Http\Controllers\ChatController::class, 'start_chat']);
Route::put('agent/read-chat/{id?}/{sessid?}', [App\Http\Controllers\ChatController::class, 'read_chat']);
Route::get('admin/chat-list-history/{id?}/{sessid?}', [App\Http\Controllers\ChatController::class, 'chat_list_history']);
Route::get('admin/chat-list-history-group/{id?}/{sessid?}', [App\Http\Controllers\ChatController::class, 'chat_list_history_group']);





