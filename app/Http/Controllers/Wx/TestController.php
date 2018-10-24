<?php

namespace App\Http\Controllers\Wx;

use Illuminate\Http\Request;
use Log;
use Excel;
use App\Http\Requests;
use App\Http\Controllers\Wx\WxController;

class TestController extends WxController
{
    function index(Request $request){

        Excel::create('Laravel Excel', function($excel) {

            $excel->sheet('Excel sheet', function($sheet) {

                $sheet->setOrientation('landscape');

            });

        })->export('xls');

        return redirect('/');
    }

    public function html()
    {
        return view('wx.test.index');
    }
}
