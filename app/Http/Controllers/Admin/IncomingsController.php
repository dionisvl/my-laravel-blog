<?php

namespace App\Http\Controllers\Admin;

use App\Incoming;
use Illuminate\Routing\Controller;

class IncomingsController extends Controller
{
    public function index()
    {
        $incomings = Incoming::all();
        return view('admin.incomings.index', ['incomings'	=>	$incomings]);
    }


    public function destroy($id)
    {
        Incoming::find($id)->remove();
        return redirect()->back();
    }
}