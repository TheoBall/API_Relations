<?php

namespace App\Http\Controllers;


use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TacheController extends Controller
{
    /**
     * Affiche toutes les taches
     *
     * @response 200
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function __construct()
    {
        // Ce controlleur n'est accessible qu'avec un JWT valide
        $this->middleware('auth');
    }


    public function showAllTasks()
    {
        return Tache::orderBy('date_fin', 'ASC')->get();
    }

    /**
     * Affiche une tache selon son id
     *
     * @response 200
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showOneTask($id)
    {
        return Tache::findOrFail($id);
    }

    /**
     * Valide la saisie des données dans la requête
     * Ajoute une une tache
     *
     * @response 201
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $this->validate($request, Tache::validateRules());
        $data = $request->all();
        $data['utilisateur_id'] = Auth::user()->id;
        return Tache::create($data);
    }

    /**
     * Valide la saisie des données dans la requête
     * Met à jour une tache
     *
     * @response 200
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function update($id, Request $request)
    {
        $this->validate($request, Tache::validateRules());
        Tache::findOrFail($id)->update($request->all());
        return Tache::findOrFail($id);
    }

    /**
     * Supprime une tache
     *
     * @response 204
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        Tache::findOrFail($id)->delete();
        return response('', 204);
    }

    /**
     * Valide la saisie des données dans la requête
     * Change l'état d'une tache à terminé
     *
     * @response 200
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function completed($id)
    {
        $task = Tache::findOrFail($id);
        $task->complet = 1;
        $task->update();
        return Tache::findOrFail($id);
    }

}
