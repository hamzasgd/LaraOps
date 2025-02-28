<?php

namespace Hamzasgd\LaravelOps\Http\Controllers;

use Hamzasgd\LaravelOps\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LogViewerController extends Controller
{
    protected LogService $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function index()
    {
        $files = $this->logService->getLogFiles();
        return view('laravelops::logs.index', compact('files'));
    }

    public function show(Request $request, string $filename)
    {
        $logs = $this->logService->getLogContent($filename);
        return view('laravelops::logs.show', compact('logs', 'filename'));
    }
} 