<?php

namespace Database\Factories;

use App\Models\Tache;
use Illuminate\Database\Eloquent\Factories\Factory;


class TacheFactory extends Factory
{
    /**
     * Le nom du modèle correspondant.
     *
     * @var string
     */
    protected $model = Tache::class;

    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'titre' => $this->faker->sentence, // Phrase avec texte aléatoire
            'contenu' => $this->faker->paragraph, // Paragraphe de textes aléatoires
            'ordre' => $this->faker->numberBetween(1,100), // Nombre aléatoire entre 1 et 100
            'complet' => (int) $this->faker->boolean, // Booléan aléatoire converti en entier
            'date_fin' => $this->faker->date('Y-m-d H:i:s') // Date aléatoire au format MySQL
        ];
    }
}
