<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Models\Appointment;
use App\Models\Document;
use App\Utils\Filters\NestedFilter;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/appointments', function (Request $request) {   
    $filters = $request->input('filters', []);
    $filters = [
        'user.name' => 'John Doe',
        'patient.name' => 'Patient 3',
        'status' => 'cancelled',
        'location' => 'Dallas',
    ];

    $appointments = (new NestedFilter)->apply(Appointment::query(), $filters)->get();

    return response()->json($appointments);
});

Route::get('/documents', function (Request $request) {
    $document = Document::find(1);
    $document->transitionTo('submitted');
});