<?php

namespace Hamzasgd\LaravelOps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LaraOpsController extends Controller
{
    /**
     * Serve the LaraOps React application
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('laravelops::app');
    }
} 