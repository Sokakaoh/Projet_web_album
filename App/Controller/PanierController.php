<?php
namespace App\Controller;


use App\Model\AlbumModel;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use App\Model\PanierModel;
use App\Model\UserModel;

class PanierController implements ControllerProviderInterface
{
    private $panierModel;
    private $userModel;
    private $albumModel;

    public function __construct()
    {
    }

    public function index(Application $app){
        return $this->show($app);
    }

    public function show(Application $app){
        $this->panierModel = new PanierModel($app);
        $this->userModel = new UserModel($app);
        $paniers = $this->panierModel->getUserPanier($this->userModel->getIdUser());
        return $app["twig"]->render('frontOff/Panier/show.html.twig', ['data'=>$paniers]);
    }

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/', 'App\Controller\PanierController::index')->bind('panier.show');
        $controllers->get('/show', 'App\Controller\PanierController::show')->bind('panier.show');
        
        $controllers->get('/delete{id}', 'App\Controller\PanierController::delete')->bind('panier.delete')->assert('id', '\d+');;
        $controllers->delete('/delete', 'App\Controller\PanierController::validFormDelete')->bind('panier.validFormDelete');

        $controllers->get('/add{id}', 'App\Controller\PanierController::add')->bind('panier.add')
            ->assert('id', '\d+');
        $controllers->get('/increment{id}', 'App\Controller\PanierController::increment')->bind('panier.increment')
            ->assert('id', '\d+');
        $controllers->get('/decrement{id}', 'App\Controller\PanierController::decrement')->bind('panier.decrement')
            ->assert('id', '\d+');


        return $controllers;
    }

    public function delete(Application $app, $id){
        $this->panierModel = new PanierModel($app);
        $this->panierModel->delete($id);
        $paniers = $this->panierModel->getAllPaniers();
        return $app["twig"]->render('frontOff/Panier/show.html.twig', ['data'=>$paniers]);
    }

    public function validFormDelete(){

    }

    public function add(Application $app, $id){
        $this->panierModel = new PanierModel($app);
        $this->albumModel = new AlbumModel($app);
        $this->userModel = new UserModel($app);
        $user_id = $this->userModel->getIdUser();
        $datas = [
            'user_id' => $user_id,
            'quantite' => 1,
            'prix' => $this->albumModel->getPrixAlbum($id)['prix'],
            'album_id' => $id,
            'commandeId' => 1,
            'id' => $this->panierModel->getUserPanier($user_id)['id']
        ];
        $this->panierModel->add($datas);
        /*$Albums = $this->albumModel->getAllAlbums();
        return $app["twig"]->render('backOff/Album/show.html.twig', ['data'=>$Albums])*/
        $paniers = $this->panierModel->getAllPaniers();
        return $app["twig"]->render('frontOff/Panier/show.html.twig', ['data'=>$paniers]);
    }
    
    
    public function decrement(Application $app, $id){
        $this->panierModel = new PanierModel($app);
        $this->albumModel = new AlbumModel($app);
        $this->userModel = new UserModel($app);
        $user_id =  $this->userModel->getIdUser();
        if ((int)$this->panierModel->getQuantiteById($id, $user_id) == 1){
            $this->panierModel->delete($id);
        }else{
            $datas = [
                'user_id' => $user_id,
                'id' => $id,
                'commandeId' => 1,
                'album_id' => $this->panierModel->getSpecificPanier($user_id, $id)['album_id']
            ];
            $this->panierModel->decrementAlbum($datas);
        }
        /*$Albums = $this->albumModel->getAllAlbums();
        return $app["twig"]->render('backOff/Album/show.html.twig', ['data'=>$Albums])*/
        $paniers = $this->panierModel->getAllPaniers();
        return $app["twig"]->render('frontOff/Panier/show.html.twig', ['data'=>$paniers]);
    }

    public function increment(Application $app, $id){
        $this->panierModel = new PanierModel($app);
        $this->albumModel = new AlbumModel($app);
        $this->userModel = new UserModel($app);
        $datas = [
            'user_id' => $this->userModel->getIdUser(),
            'id' => $id,
            'commandeId' => 1,
            'album_id' => $this->panierModel->getSpecificPanier($this->userModel->getIdUser(), $id)['album_id']
        ];
        $this->panierModel->incrementAlbum($datas);
        /*$Albums = $this->albumModel->getAllAlbums();
        return $app["twig"]->render('backOff/Album/show.html.twig', ['data'=>$Albums])*/
        $paniers = $this->panierModel->getAllPaniers();
        return $app["twig"]->render('frontOff/Panier/show.html.twig', ['data'=>$paniers]);
    }
}