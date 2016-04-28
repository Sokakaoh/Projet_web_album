<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\AlbumModel;
use App\Model\TypeAlbumModel;

use Symfony\Component\Validator\Constraints as Assert;   // pour utiliser la validation
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;

class AlbumController implements ControllerProviderInterface
{
    private $albumController;
    private $typeAlbumModel;
    private $albumModel;

    public function __construct()
    {
    }

    public function index(Application $app) {
        return $this->show($app);
    }

    public function show(Application $app) {
        $this->albumController = new AlbumModel($app);
        $Albums = $this->albumController->getAllAlbums();
        return $app["twig"]->render('backOff/Album/show.html.twig',['data'=>$Albums]);
    }

    public function add(Application $app) {
        $this->typeAlbumModel = new TypeAlbumModel($app);
        $typeAlbum = $this->typeAlbumModel->getAllTypeAlbums();
        return $app["twig"]->render('backOff/Album/add.html.twig',['typeAlbum'=>$typeAlbum,'path'=>BASE_URL]);
    }

    public function validFormAdd(Application $app, Request $req) {
/*        if (isset($_POST['nom']) && isset($_POST['typeAlbum_id']) and isset($_POST['nom']) and isset($_POST['photo'])) {
            $donnees = [
                'nom' => htmlspecialchars($_POST['nom']),                    // echapper les entrées
                'typeAlbum_id' => htmlspecialchars($app['request']->get('typeAlbum_id')),
                'prix' => htmlspecialchars($req->get('prix')),
                'photo' => $app->escape($req->get('photo'))  //$req->query->get('photo')
            ];
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['no   m']))) $erreurs['nom']='nom composé de 2 lettres minimum';
            if(! is_numeric($donnees['typeAlbum_id']))$erreurs['typeAlbum_id']='veuillez saisir une valeur';
            if(! is_numeric($donnees['prix']))$erreurs['prix']='saisir une valeur numérique';
            if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';

            if(! empty($erreurs))
            {
                $this->typeAlbumModel = new TypeAlbumModel($app);
                $typeAlbums = $this->typeAlbumModel->getAllTypeAlbums();
                return $app["twig"]->render('backOff/Album/add.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'typeAlbums'=>$typeAlbums]);
            }
            else
            {
                $this->AlbumModel = new AlbumModel($app);
                $this->AlbumModel->insertAlbum($donnees);
                return $app->redirect($app["url_generator"]->generate("album.index"));
            }

        }
        else
            return $app->abort(404, 'error Pb data form Add');*/
        $donnees = [
            'nom' => htmlspecialchars($_POST['nom']),                    // echapper les entrées
            'typeAlbum_id' => htmlspecialchars($app['request']->get('typeAlbum_id')),
            'artiste' => htmlspecialchars($_POST['artiste']),
            'prix' => htmlspecialchars($req->get('prix')),
            'photo' => $app->escape($req->get('photo'))
        ];
        $this->albumModel = new AlbumModel($app);
        $this->albumModel->insertAlbum($donnees);
        return $app->redirect($app["url_generator"]->generate("album.index"));

    }

    public function delete(Application $app, $id) {
        $this->typeAlbumModel = new TypeAlbumModel($app);
        $typeAlbums = $this->typeAlbumModel->getAllTypeAlbums();
        $this->albumController = new AlbumModel($app);
        $donnees = $this->albumController->getAlbum($id);
        return $app["twig"]->render('backOff/Album/delete.html.twig',['typeAlbums'=>$typeAlbums,'donnees'=>$donnees]);
    }

    public function validFormDelete(Application $app, Request $req) {
        $id=$app->escape($req->get('id'));
        if (is_numeric($id)) {
            $this->albumController = new AlbumModel($app);
            $this->albumController->deleteAlbum($id);
            return $app->redirect($app["url_generator"]->generate("album.index"));
        }
        else
            return $app->abort(404, 'error Pb id form Delete');
    }


    public function edit(Application $app, $id) {
        $this->typeAlbumModel = new TypeAlbumModel($app);
        $typeAlbums = $this->typeAlbumModel->getAllTypeAlbums();
        $this->albumController = new AlbumModel($app);
        $donnees = $this->albumController->getAlbum($id);
        return $app["twig"]->render('backOff/Album/edit.html.twig',['typeAlbums'=>$typeAlbums,'donnees'=>$donnees]);
    }

    public function validFormEdit(Application $app, Request $req) {
        // var_dump($app['request']->attributes);
        if (isset($_POST['nom']) && isset($_POST['typeAlbum_id']) and isset($_POST['nom']) and isset($_POST['photo']) and isset($_POST['id'])) {
            $donnees = [
                'nom' => htmlspecialchars($_POST['nom']),                    // echaper les entrées
                'typeAlbum_id' => htmlspecialchars($app['request']->get('typeAlbum_id')),
                'prix' => htmlspecialchars($req->get('prix')),
                'photo' => $app->escape($req->get('photo')),
                'id' => $app->escape($req->get('id'))//$req->query->get('photo')
            ];
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
            if(! is_numeric($donnees['typeAlbum_id']))$erreurs['typeAlbum_id']='veuillez saisir une valeur';
            if(! is_numeric($donnees['prix']))$erreurs['prix']='saisir une valeur numérique';
            if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';
            if(! is_numeric($donnees['id']))$erreurs['id']='saisir une valeur numérique';
           $contraintes = new Assert\Collection(
                [
                    'id' => [new Assert\NotBlank(),new Assert\Type('digit')],
                    'typeAlbum_id' => [new Assert\NotBlank(),new Assert\Type('digit')],
                    'nom' => [
                        new Assert\NotBlank(['message'=>'saisir une valeur']),
                        new Assert\Length(['min'=>2, 'minMessage'=>"Le nom doit faire au moins {{ limit }} caractères."])
                    ],
                    //http://symfony.com/doc/master/reference/constraints/Regex.html
                    'photo' => [
                        new Assert\Length(array('min' => 5)), 
                        new Assert\Regex([ 'pattern' => '/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/',
                        'match'   => true,
                        'message' => 'nom de fichier incorrect (extension jpeg , jpg ou png)' ]),
                    ],
                    'prix' => new Assert\Type(array(
                        'type'    => 'numeric',
                        'message' => 'La valeur {{ value }} n\'est pas valide, le type est {{ type }}.',
                    ))
                ]);
            $errors = $app['validator']->validate($donnees,$contraintes);  // ce n'est pas validateValue

        //    $violationList = $this->get('validator')->validateValue($req->request->all(), $contraintes);
//var_dump($violationList);

          //   die();
            if (count($errors) > 0) {
                // foreach ($errors as $error) {
                //     echo $error->getPropertyPath().' '.$error->getMessage()."\n";
                // }
                // //die();
                //var_dump($erreurs);

            // if(! empty($erreurs))
            // {
                $this->typeAlbumModel = new TypeAlbumModel($app);
                $typeAlbums = $this->typeAlbumModel->getAllTypeAlbums();
                return $app["twig"]->render('backOff/Album/edit.html.twig',['donnees'=>$donnees,'errors'=>$errors,'erreurs'=>$erreurs,'typeAlbums'=>$typeAlbums]);
            }
            else
            {
                $this->albumModel = new AlbumModel($app);
                $this->albumModel->updateAlbum($donnees);
                return $app->redirect($app["url_generator"]->generate("album.show"));
            }

        }
        else
            return $app->abort(404, 'error Pb id form edit');

    }

    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\AlbumController::index')->bind('album.index');
        $controllers->get('/show', 'App\Controller\AlbumController::show')->bind('album.show');

        $controllers->get('/add', 'App\Controller\AlbumController::add')->bind('album.add');
        $controllers->post('/add', 'App\Controller\AlbumController::validFormAdd')->bind('album.validFormAdd');

        $controllers->get('/delete/{id}', 'App\Controller\AlbumController::delete')->bind('album.delete')->assert('id', '\d+');;
        $controllers->delete('/delete', 'App\Controller\AlbumController::validFormDelete')->bind('album.validFormDelete');

        $controllers->get('/edit/{id}', 'App\Controller\AlbumController::edit')->bind('album.edit')->assert('id', '\d+');;
        $controllers->put('/edit', 'App\Controller\AlbumController::validFormEdit')->bind('album.validFormEdit');

        return $controllers;
    }


}
