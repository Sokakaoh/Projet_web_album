<?php
namespace App\Controller;


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

        $controllers->get('/', 'App\Controller\CommandeController.php::index')->bind('commandes.show');
        $controllers->get('/show', 'App\Controller\CommandeController.php::show')->bind('commandes.show');

        return $controllers;
    }
}
