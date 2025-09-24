<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\ThreadLike;
use Illuminate\Support\Facades\Auth;

class ThreadLikeController extends Controller
{
    // Controller retained for compatibility; logic moved to ThreadController::like()
}
