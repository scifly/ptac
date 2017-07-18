<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', 'HomeController@index');
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');*/
Route::get('groups', function (){
    return view('admin.config.group.groups_index');
});
Route::get('/', function() { return 'Dashboard'; });

Route::get('/', 'HomeController@index');

Route::get('schools', function() {
    $shools = App\Models\School::all();
    return view('configuration.schools')->with('schools', $shools);
});
Route::get('schools/types/{name}', function($name) {
    $schoolType = App\Models\SchoolType::with('schools')
        ->whereName($name)
        ->first();
    return view('admin.config.school.schools_index')
        ->with('schoolType', $schoolType)
        ->with('schools', $schoolType->schools);
});



Route::get('schools/create', function() {
    return view('admin.config.school.schools_create');
});
Route::post('schools', function() {
    $school = App\Models\School::create(Request::all());
    return redirect('schools/' . $school->id)->withSuccess('成功创建学校');
});
Route::get('schools/{school}', function(App\Models\School $school) {
    return view('admin.config.school.schools_show')->with('school', $school);
});
Route::get('schools/{school}/edit', function(App\Models\School $school) {
    return view('admin.config.school.schools_edit')->with('school', $school);
});
Route::put('schools/{school}', function(App\Models\School $school) {
    $school->update(Request::all());
    return redirect('schools/' . $school->id)->withSuccess('成功更新学校');
});
Route::delete('schools/{school}', function(App\Models\School $school) {
    $school->delete();
    return redirect('schools')->withSuccess('该学校已被删除');
});

# 系统设置
// 学校设置
