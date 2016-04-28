<?php
namespace App\Model;


use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;

class PanierModel {
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllPaniers(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 'p.quantite', 'p.prix', 'p.dateAjoutPanier', 'a.photo', 'a.nom', 'a.prix * p.quantite as prixTot', 'p.album_id', 'p.user_id')
            ->from('paniers', 'p')
            ->innerJoin('p', 'album', 'a', 'p.album_id = a.id')
            ->innerJoin('p', 'users', 'u', 'p.user_id = u.id')
            ->addOrderBy('p.quantite', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }
    
    public function delete($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('paniers')
            ->where('id = '.$id);
        $queryBuilder->execute();
    }
    
}