<?php
namespace App\Model;


use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Validator\Constraints\Date;

class CommandeModel
{
    private $db;
    private $panierModel;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
        $this->panierModel = new PanierModel($app);
    }

    public function getAllCommandes(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id', 'c.user_id', 'c.prix', 'c.date', 'c.etat_id')
            ->from('commandes', 'c');
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
        $queryBuilder->execute();
    }

    public function getUserCommandes($user_id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id', 'c.user_id', 'c.prix', 'c.date', 'c.etat_id')
            ->from('commandes', 'c')
            ->where('c.user_id = ?')
            ->setParameter(0, $user_id);

        return $queryBuilder->execute()->fetchAll();
    }


}