<?php
namespace App\Controller;


use App\Model\UserModel;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use App\Model\CommandeModel;


class CommandeController implements ControllerProviderInterface
{
    private $commandeModel;

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

   public function add(Application $app, $id, $prix, $date, $etats_id){
        $this->commandeModel = new CommandeModel($app);
        $this->userModel = new UserModel($app);
        $datas = [
            'id' => $id,
            'user_id' => $this->userModel->getIdUser(),
            'prix' => $prix,
            'date' => $date,
            'etats_id' => $etats_id
        ];
        $this->CommandeModel->add($datas);
        return $app["twig"]->render('frontOff/Commande/show.html.twig');
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

        $controllers->get('/', 'App\Controller\CommandeController::index')->bind('commandes.show');
        $controllers->get('/show', 'App\Controller\CommandeController::show')->bind('commandes.show');

        $controllers->post('/add', 'App\Controller\CommandeController::add')->bind('commandes.add');


        return $controllers;
    }
}
