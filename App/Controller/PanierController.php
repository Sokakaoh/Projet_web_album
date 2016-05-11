<?php
namespace App\Controller;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use App\Model\PanierModel;
use App\Model\UserModel;

class PanierController implements ControllerProviderInterface
{
    private $panierModel;
    private $userModel;

    public function __construct()
    {
    }

    public function index(Application $app){
        return $this->show($app);
    }

    public function show(Application $app){
        $this->panierModel = new PanierModel($app);
        $paniers = $this->panierModel->getAllPaniers();
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
        $controllers->get('/', 'App\Controller\PanierController::index')->bind('album.show');
        $controllers->get('/', 'App\Controller\PanierController::show')->bind('panier.show');
        
        $controllers->get('/delete{id}', 'App\Controller\PanierController::delete')->bind('panier.delete')->assert('id', '\d+');;
        $controllers->delete('/delete', 'App\Controller\PanierController::validFormDelete')->bind('panier.validFormDelete');

        $controllers->get('/add{id}', 'App\Controller\PanierController::add')->bind('panier.add')->assert('id', '\d+');

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

    public function add(Application $app, $id, $prix, $album_id){
        $this->panierModel = new PanierModel($app);
        $this->userModel = new UserModel($app);
        $datas = [
            'user_id' => $this->userModel->getIdUser(),
            'quantite' => $this->panierModel->isAlbumInPanier($this->userModel->getIdUser(), $album_id),
            'prix' => $prix,
            'id' => $id,
            'album_id' => $album_id,
            'commande_id' => 1
        ];
        $this->panierModel->add($datas);
        return $app["twig"]->render('backOff/Album/show.html.twig');
    }

}