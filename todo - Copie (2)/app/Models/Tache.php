<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
  //affiche ce message lors d'une erreur 404
  public $modelNotFoundMessage = "Tache inexistante";
  
  use HasFactory;
  
     /**
     * La table associée au modèle.
     *
     * @var string
     */
    // protected $table = 'taches';

    /**
     * La clé primaire associée à la table.
     *
     * @var string
     */
    // protected $primaryKey = 'id';
    
     /**
     * Validation des données
     * @return array[] qui contient 
     */
    static function validateRules()
    {
        return [
            'titre' => 'required',
            'contenu' => 'string',
            'ordre' => 'required|numeric',
            'complet' => 'required|boolean',
            'date_fin' => 'required|date'
        ];
    } 
    
     /**
     * Liste des attributs modifiables
     *
     * @var array
     */
    protected $fillable = [
        'titre',
        'contenu',
        'ordre',
        'complet',
        'date_fin',
        //ajouter la nouvelle colonne
        'utilisateur_id'
    ];

    /**
     * Liste des attributs cachés
     * Seront exclus dans les réponses
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}