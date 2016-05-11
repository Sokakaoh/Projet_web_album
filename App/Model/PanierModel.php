<?php
namespace App\Model;


use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Validator\Constraints\Date;

class PanierModel {
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllPaniers(){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 'p.quantite', 'p.prix', 'a.photo', 'a.nom', 'a.prix * p.quantite as prixTot', 'p.album_id', 'p.user_id')
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

    public function getUserPanier($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 'p.quantite', 'p.prix', 'a.photo', 'a.nom', 'a.prix * p.quantite as prixTot', 'p.album_id', 'p.user_id')
            ->from('paniers', 'p')
            ->innerJoin('p', 'album', 'a', 'p.album_id = a.id')
            ->innerJoin('p', 'users', 'u', 'p.user_id = u.id')
            ->where('p.user_id = :id')
            ->addOrderBy('p.quantite', 'ASC')
            ->setParameter('id', $id);
        return $queryBuilder->execute()->fetch();
    }

    public function add($datas){
        $paniers = $this->getAllPaniers();
        // On vérifie que l'album n'est pas déja dans le panier
        foreach ($paniers as $p){
            // SI c'est le ca on modifie la quantite et le prix
            if ($p['album_id'] == $datas['albumId']){
                return $this->incrementAlbum($datas);
            }
        }
        // Sinon il s'agit d'une nouvelle entrée
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('paniers')
            ->values([
                'quantite' => 1,
                'prix' => $datas['prix'],
                'user_id' => $datas['userId'],
                'album_id' => $datas['albumId'],
                'commande_id' => $datas['commandeId']
            ]);
        return $queryBuilder->execute();
    }

    public function incrementAlbum($datas){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('paniers')
            ->set('quantite', '?')
            ->where('id=? and user_id=? and commande_id=1')
            ->setParameter(0, (int)$this->getUserPanier($datas['userId'])['quantite'] + 1)
            ->setParameter(1, (int)$datas['id'])
            ->setParameter(2, (int)$datas['userId']);
        return $queryBuilder->execute();
    }

    public function decrementAlbum($datas){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('paniers')
            ->set('quantite', '?')
            ->where('id=? and user_id=? and commande_id=1')
            ->setParameter(0, (int)$this->getUserPanier($datas['userId'])['quantite'] - 1)
            ->setParameter(1, (int)$datas['id'])
            ->setParameter(2, (int)$datas['userId']);
        return $queryBuilder->execute();
    }

    public function getQuantiteById($id, $userId){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('quantite')
            ->from('paniers')
            ->where('id = ? and user_id = ?')
            ->setParameter(0, $id)
            ->setParameter(1, $userId);
        return $queryBuilder->execute()->fetch();
    }
}