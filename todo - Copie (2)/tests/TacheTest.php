<?php
use App\Models\Tache;
//ajout de l'utilisateur
use App\Models\Utilisateur;
//ajout de la méthode de Hashage
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseTransactions;


class TacheTest extends TestCase
{

    //migre la bd lors de l'exécution des tests, puis annule la bd lorsque les tests sont terminés.
    use DatabaseTransactions;

    // Création de variables permettant d'effectuer les tests
    private $taches = "";
    //ajout de l'utilisateur
    private $utilisateur="";

    //création du token pour chaque requête
    private $token = "";

    /**
     * Affectation des variables avec la factory
     * Cette méthode est lancée avant l'exécution des tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->taches = Tache::factory()->count(2)->create();


        //création de l'utilisateur pour le test
        $this->utilisateur = Utilisateur::create([
            'nom' => 'Test',
            'email' => 'test@gmail.com',
            'password' => Hash::make('1234')
        ]);
        $this->utilisateur->password = '1234';

        //ajout de l'utilisateur dans les tâches créées
        Tache::findOrFail($this->taches[0]->id)->update( [
            'utilisateur_id' =>  $this->utilisateur->id]);
        Tache::findOrFail($this->taches[1]->id)->update([
            'utilisateur_id' =>  $this->utilisateur->id]);

        //authentification
        $this->post('api/login', [
            'email' =>  $this->utilisateur->email,
            'password' =>  $this->utilisateur->password]);

        //permet de s'authentifier et de sauvegarder le token
        $this->token = ($this->response->json()['access_token']);
    }
    /**
     * Test de la Methode GET en récupérant toutes les tâches
     *
     * @return void
     */
    public function testShowAllTasks()
    {
        $this->get('api/taches',[
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);
        $this->assertResponseOk(); //Affirme que la réponse a un code d'état 200:
    }
    /**
     * Test de la Methode GET en récupérant 1 tache selon le 1er id récupéré
     *
     * @return void
     */
    public function testShowOneTask()
    {
         $this->get('api/taches/'.$this->taches[0]->id,[
             'HTTP_AUTHORIZATION' => "{$this->token}",
             'CONTENT_TYPE' => 'application/ld+json',
             'HTTP_ACCEPT' => 'application/ld+json'
         ]);
         $this->assertResponseOk();//Affirme que la réponse a un code d'état 200:
    }

    public function testCreate()
    {
        $tache = Tache::factory()->make();


        $this->post('api/taches',
            [
                'titre' => $tache->titre,
                'contenu' => $tache->contenu,
                'ordre' => $tache->ordre,
                'complet' => $tache->complet,
                'date_fin' => $tache->date_fin,
                'HTTP_AUTHORIZATION' => "{$this->token}",
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json'
            ]);

             $this->assertResponseOk(); //Affirme que la réponse a un code d'état 201:
             $this->seeJsonContains(
        [
            'titre' => $tache->titre,
            'contenu' => $tache->contenu,
            'ordre' => $tache->ordre,
            'complet' => $tache->complet,
            'date_fin' => $tache->date_fin
        ]);
         $this->seeInDatabase('taches', [
            'titre' => $tache->titre,
            'contenu' => $tache->contenu,
            'ordre' => $tache->ordre,
            'complet' => $tache->complet,
            'date_fin' => $tache->date_fin
        ]);
    }

    public function testupdate()
    {
        $tache = $this->taches[0];
        $newtache  = [
            'id' => $tache->id,
            'titre' => $tache->titre.'test',
            'contenu' => $tache->contenu." test test",
            'ordre' => $tache->ordre+20,
            'complet' => $tache->complet,
            'date_fin' => $tache->date_fin,
        ];

        $tableParam = array_merge($newtache,['HTTP_AUTHORIZATION' => "$this->token",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json']);

        $this->put('api/taches/'.$tache->id, $tableParam);

            $this->assertResponseOk(); //Affirme que la réponse a un code d'état 200:
            $this->seeJsonContains($newtache);
            $this->seeInDatabase('taches', $newtache);
    }


    public function testcompleted()
    {
        $tache = $this->taches[0];
        $tache->complet = 0;
        $tache->update();
        $this->put('api/taches/'.$this->taches[0]->id.'/complet',[
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);
        $this->assertResponseOk(); //Affirme que la réponse a un code d'état 200:
        $this->seeJsonContains(
                ['complet' => 1])
            ->seeInDatabase('taches',
                [
                    'id' => $this->taches[0]->id,
                    'complet' => 1,
                ]);

 }

    public function testdelete()
    {
        $this->delete('api/taches/'.$this->taches[0]->id,[
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertResponseStatus(204);//Affirme que la réponse a un code d'état 204
         $this->notSeeInDatabase('taches', [
            'id' => $this->taches[0]->id
        ]);
    }

    public function testCreateFailRequired()
    {
        $tache = Tache::factory()->make();
        $this->post('api/taches',
            [
               // 'Titre' => $tache->titre,
                'contenu' => $tache->contenu,
                'ordre' => $tache->ordre,
                'complet' => $tache->complet,
                'date_fin' => $tache->date_fin
            ],[
                'HTTP_AUTHORIZATION' => "{$this->token}",
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json'
            ]);

        $this->assertResponseStatus(422); //Affirme que la réponse a un code d'état 422:
        $this->notSeeInDatabase('taches', [
                'id' =>  $tache->id
    ]);
    }

    public function testNotOwnerShowOneTask()
    {
        $mytask=Tache::factory()->create();
        $this->get('api/taches/'.$mytask->id,[
            'HTTP_AUTHORIZATION' => "{$this->token}",
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json'
        ]);

        $this->assertResponseStatus(401); //Affirme que la réponse a un code d'état 401
        $this->seeJsonContains(['message' => "Vous n'êtes pas le propriétaire de la tâche"]);
    }

    public function testCreateFailDataType()
    {
        $tache = Tache::factory()->make();
        $this->post('api/taches',
            [
                'titre' => $tache->titre,
                'contenu' => $tache->contenu,
                'ordre' => $tache->contenu, //insère un string à la place d'un nombre
                'complet' => $tache->complet,
                'date_fin' => $tache->date_fin
            ],[
                'HTTP_AUTHORIZATION' => "{$this->token}",
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json'
            ]);

        $this->assertResponseStatus(422); //Affirme que la réponse a un code d'état 422:
        $this->notSeeInDatabase('taches', [
                'id' =>  $tache->id
            ]);
    }

    public function testNotFound()
    {
        $this->put('api/taches/kk',
            [
                'titre' => 'Appeler coiffeuse',
                'contenu' => '',
                'ordre' => 5,
                'complet' => false,
                'date_fin' => '2022-03-20 16:30:00'
            ,
                'HTTP_AUTHORIZATION' => "{$this->token}",
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json'
            ]);

        $this->assertResponseStatus(404); //Affirme que la réponse a un code d'état 404
        $this->seeJsonContains(['message' => 'Tache inexistante']);

    }

}
