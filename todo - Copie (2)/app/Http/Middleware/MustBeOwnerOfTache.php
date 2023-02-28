<?php

namespace App\Http\Middleware;

use App\Models\Tache;
use Closure;
use Illuminate\Support\Facades\Auth;

class MustBeOwnerOfTache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Récupération de la tâche
        $tache = Tache::find($request->id);

        // Si PAS propriétaire de la tâche
        if($tache AND $tache->utilisateur_id != Auth::user()->id)
                        return response()->json(['message' => "Vous n'êtes pas le propriétaire de la tâche"], 401);

        return $next($request);
    }
}
