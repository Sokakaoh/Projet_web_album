<?php
namespace App\Model;


use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Validator\Constraints\Date;

class CommandeModel
{
    private $db;
    private $panierModel;
    private $userModel;

    public function __construct(Application $app){
        $this->userModel = new UserModel($app);
        $this->db = $app['db'];
        $this->panierModel = new PanierModel($app);
    }

    public function getAllCommandes(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id', 'c.user_id', 'c.prix', 'c.date', 'c.etat_id', 'e.libelle as etat')
            ->from('commandes', 'c')
            ->InnerJoin('c', 'etats', 'e', 'c.etat_id = e.id');
        return $queryBuilder->execute()->fetchAll();
    }

    public function add($datas){
        $prix = 0;
        $panier = $this->panierModel->getUserPanier($datas['user_id']);
        foreach ($panier as $album){
            $prix += $album['quantite'] * $album['prix'];
        }

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('commandes')
            ->values([
                'user_id' => '?',
                'etat_id' => '1',
                'prix' => '?'
            ])
            ->setParameter(0, (int)$datas['user_id'])
            ->setParameter(1, $prix);
        $queryBuilder->execute();

        $id = $this->db->lastInsertId();

        //TODO: suppression du panier qui a été validé en commande.
        $this->panierModel->deleteUserPanier($datas['user_id']);
    }

    public function getUserCommandes($user_id){
        $queryBuilder = new QueryBuilder($this->db);
        if ($this->userModel->isAdmin())
            return $this->getAllCommandes();
        $queryBuilder
            ->select('c.id', 'c.user_id', 'c.prix', 'c.date', 'c.etat_id', 'e.libelle as etat')
            ->from('commandes', 'c')
            ->InnerJoin('c', 'etats', 'e', 'c.etat_id = e.id')
            ->where('c.user_id = ?')
            ->setParameter(0, $user_id);

        return $queryBuilder->execute()->fetchAll();
    }


}