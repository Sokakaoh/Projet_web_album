<?php
namespace App\Controller;


use App\Model\PanierModel;
use App\Model\UserModel;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use App\Model\CommandeModel;


class CommandeController implements ControllerProviderInterface
{
    private $commandeModel;
    private $userModel;
    private $panierModel;

    public function __construct()
    {
    }

    public function index(Application $app)
    {
        return $this->show($app);
    }

    public function show(Application $app)
    {
        $this->commandeModel = new CommandeModel($app);
        $commandes = $this->commandeModel->getAllCommandes();
        return $app["twig"]->render('frontOff/Commande/show.html.twig', ['data' => $commandes]);
    }

   public function add(Application $app){
       $this->commandeModel = new CommandeModel($app);
       $this->panierModel = new PanierModel($app);
       $this->userModel = new UserModel($app);

       $user_id = $this->userModel->getIdUser();
       echo $user_id;
       $panier_id = (int)$this->panierModel->getUserPanier($user_id)['id'];
       echo $panier_id;
       $datas = [
           'id' => $panier_id,
           'user_id' => $user_id,
           'prix' => 0,
           'date' => 0,
           'etat_id' => 1
       ];
       $this->commandeModel->add($datas);
       $commandes = $this->commandeModel->getUserCommandes($user_id);
       return $app["twig"]->render('frontOff/Commande/show.html.twig', ['data' => $commandes]);
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

        $controllers->get('/', 'App\Controller\CommandeController::index')->bind('commande.show');
        $controllers->get('/show', 'App\Controller\CommandeController::show')->bind('commande.show');

        $controllers->get('/add', 'App\Controller\CommandeController::add')->bind('commande.add');


        return $controllers;
    }
}
