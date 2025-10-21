<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class mutationController extends Controller
{
    public function mutation(){
        return 'hello world! desde el controller';
    }

    public function stats(){
        return 'stats';
    }
    public function list(){
        return 'list';
    }
}
