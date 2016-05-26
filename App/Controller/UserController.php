<?php
namespace App\Controller;

use App\Model\UserModel;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class UserController implements ControllerProviderInterface {

	private $userModel;

	public function index(Application $app) {
		return $this->connexionUser($app);
	}

	public function connexionUser(Application $app)
	{
		return $app["twig"]->render('v_session_connexion.html.twig');
	}

	public function validFormConnexionUser(Application $app)
	{

		$app['session']->clear();
		$donnees['login']=$app['request']->request->get('login');
		$donnees['password']=$app['request']->request->get('password');

		$this->userModel = new UserModel($app);
		$data=$this->userModel->verif_login_mdp_Utilisateur($donnees['login'],$donnees['password']);

		if($data != NULL)
		{
			$app['session']->set('droit', $data['droit']);  //dans twig {{ app.session.get('droit') }}
			$app['session']->set('login', $data['login']);
			$app['session']->set('logged', 1);
			$app['session']->set('id', $data['id']);
			return $app->redirect($app["url_generator"]->generate("album.index"));
		}
		else
		{
			$app['session']->set('erreur','mot de passe ou login incorrect');
			return $app["twig"]->render('v_session_connexion.html.twig');
		}
	}
	public function deconnexionSession(Application $app)
	{
		$app['session']->clear();
		$app['session']->getFlashBag()->add('msg', 'vous êtes déconnecté');
		return $app->redirect($app["url_generator"]->generate("album.index"));
	}

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];
		$controllers->match('/', 'App\Controller\UserController::index')->bind('user.index');
		$controllers->get('/login', 'App\Controller\UserController::connexionUser')->bind('user.login');
		$controllers->post('/login', 'App\Controller\UserController::validFormConnexionUser')->bind('user.validFormlogin');
		$controllers->get('/logout', 'App\Controller\UserController::deconnexionSession')->bind('user.logout');


		$controllers->put('/edit', 'App\Controller\UserController::validFormEdit')->bind('client.validFormEdit');
		$controllers->get('/edit', 'App\Controller\UserController::edit')->bind('client.edit');

        $controllers->get('/modify{id}', 'App\Controller\UserController::modify')->bind('client.modify')
            ->assert('id', '\d+');
        $controllers->get('/valideDelete{id}', 'App\Controller\UserController::valideDelete')->bind('client.valideDelete')
            ->assert('id', '\d+');
        $controllers->delete('/delete', 'App\Controller\UserController::delete')->bind('client.delete');

        $controllers->get('/inscription', 'App\Controller\UserController::edit')->bind('user.inscription');

        $controllers->put('/add', 'App\Controller\UserController::validFormEdit')->bind('client.add');


		return $controllers;
	}

	public function show(Application $app){
		$this->userModel = new UserModel($app);
		if ($this->userModel->isAdmin()){  
            $data = $this->userModel->getAllUser();
			return $app["twig"]->render('backOff/Client/show.html.twig', ['data' => $data]);
		}else{
			$data = $this->userModel->getUser();
			return $app["twig"]->render('frontOff/Client/show.html.twig', ['data' => $data]);
		}
	}

	public function validFormEdit(Application $app){
        $this->userModel = new UserModel($app);
        if ($this->userModel->isLogged()){
            if (isset($_POST['nom']) && isset($_POST['adresse']) && isset($_POST['code_postal'])&&
                isset($_POST['ville']) && isset($_POST['email']) && isset($_POST['login']) &&
                isset($_POST['password']) && isset($_POST['id']) && isset($_POST['droit'])&&
                isset($_POST['prenom'])){
                $data = [
                    'nom' => htmlspecialchars($_POST['nom']),
                    'prenom' => htmlspecialchars($_POST['prenom']),
                    'adresse' => $_POST['adresse'],
                    'code_postal' => $_POST['code_postal'],
                    'ville' => $_POST['ville'],
                    'email' => $_POST['email'],
                    'login' => $_POST['login'],
                    'password' => $_POST['password'],
                    'id' => $_POST['id'],
                    'droit' => $_POST['droit']
                ];
                $this->userModel->editUser($data);
            }
		}else{
            if (isset($_POST['nom']) && isset($_POST['adresse']) && isset($_POST['code_postal'])&&
                isset($_POST['ville']) && isset($_POST['email']) && isset($_POST['login']) &&
                isset($_POST['password']) && isset($_POST['prenom']) && isset($_POST['password_verif']) &&
                $_POST['password'] == $_POST['password_verif']){
                $data = [
                    'nom' => htmlspecialchars($_POST['nom']),
                    'prenom' => htmlspecialchars($_POST['prenom']),
                    'adresse' => $_POST['adresse'],
                    'code_postal' => $_POST['code_postal'],
                    'ville' => $_POST['ville'],
                    'email' => $_POST['email'],
                    'login' => $_POST['login'],
                    'password' => $_POST['password'],
                ];
                $this->userModel->addUser($data);
                return $app->redirect($app["url_generator"]->generate("album.index"));
            }
        }

		return$this->show($app);
	}

	public function edit(Application $app){
		return $this->show($app);
	}
    
    public function modify(Application $app, $id){
        $this->userModel = new UserModel($app);
        $data = $this->userModel->getUserById($id);
        return $app["twig"]->render('frontOff/Client/show.html.twig', ['data' => $data]);
    }

    public function valideDelete(Application $app, $id){
        $this->userModel = new UserModel($app);
        $data = $this->userModel->getUserById((int)$id);

        return $app["twig"]->render('backOff/Client/valideDelete.html.twig', ['data' => $data]);
    }
    
    public function delete(Application $app){
        if (isset($_POST['id'])){
            $this->userModel = new UserModel($app);
            $this->userModel->delete($_POST['id']);
        }
        return $this->show($app);
    }
}