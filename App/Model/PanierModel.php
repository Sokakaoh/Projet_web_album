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

    public function add($datas){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('paniers')
            ->values([
                'id' => $datas['id'],
                'quantite' => 1,
                'prix' => $datas['prix'],
                'user_id' => $datas['userId'],
                'album_id' => $datas['albumId'],
                'commande_id' => $datas['commandeId']
            ]);
        return $queryBuilder->execute();
    }

    public function isAlbumInPanier($idUser, $idAlbum){
        if ($idUser == null) return null;
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            -> select('quantite')
            ->from('paniers')
            ->where('panier.user_id = :id and panier.album_id = :iAlbum')
            ->setParameter('id', $idUser)
            ->setParameter('idAlbum', $idAlbum)
        ;
        $qte = $queryBuilder->execute();
        if ($qte == 0 || $qte == null) 
            $qte = 1;
        return $qte;
    }

}