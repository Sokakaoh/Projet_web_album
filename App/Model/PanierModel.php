<?php
namespace App\Model;


use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Validator\Constraints\Date;

class PanierModel {
    private $db;
    private $albumModel;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
        $this->albumModel = new AlbumModel($app);
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

    public function getUserPanier($user_id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 'p.quantite', 'p.prix', 'a.photo', 'a.nom', 'a.prix * p.quantite as prixTot', 'p.album_id', 'p.user_id')
            ->from('paniers', 'p')
            ->innerJoin('p', 'album', 'a', 'p.album_id = a.id')
            ->innerJoin('p', 'users', 'u', 'p.user_id = u.id')
            ->where('p.user_id = :user_id')
            ->addOrderBy('p.quantite', 'ASC')
            ->setParameter('user_id', $user_id);
        return $queryBuilder->execute()->fetchAll();
    }

    public function add($datas){
        $paniers = $this->getAllPaniers();
        echo "test".$datas['nom'];
        // On vérifie que l'album n'est pas déja dans le panier
        foreach ($paniers as $p){
            // Si c'est le cas on modifie la quantite et le prix
            if ($p['album_id'] == $datas['album_id']){
                return $this->incrementAlbum($datas);
            }
        }
        // Sinon il s'agit d'une nouvelle entrée
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('paniers')
            ->values([
                'quantite' => 1,
                'prix' => $datas['prix'],
                'user_id' => $datas['user_id'],
                'album_id' => $datas['album_id'],
                'commande_id' => $datas['commandeId']
            ]);
        
        $this->albumModel->decrementQteAlbum($datas['album_id']);
        return $queryBuilder->execute();
    }

    public function incrementAlbum($datas){
        $qte = (int)$this->getQuantiteById($datas['id'], $datas['user_id']) + 1;
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('paniers')
            ->set('quantite', '?')
            ->where('id=? and user_id=? and album_id=? and commande_id=1' )
            ->setParameter(0, $qte)
            ->setParameter(1, (int)$datas['id'])
            ->setParameter(2, (int)$datas['user_id'])
            ->setParameter(3, (int)$datas['album_id']);
        return $queryBuilder->execute();
    }

    public function decrementAlbum($datas){
        $qte = (int)$this->getQuantiteById($datas['id'], $datas['user_id']) - 1;
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('paniers')
            ->set('quantite', '?')
            ->where('id=? and user_id=? and album_id=? and commande_id=1')
            ->setParameter(0, $qte)
            ->setParameter(1, (int)$datas['id'])
            ->setParameter(2, (int)$datas['user_id'])
            ->setParameter(3, (int)$datas['album_id']);
        return $queryBuilder->execute();
    }

    public function getQuantiteById($id, $user_id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('quantite')
            ->from('paniers')
            ->where('id = ? and user_id = ?')
            ->setParameter(0, $id)
            ->setParameter(1, $user_id);
        return $queryBuilder->execute()->fetch()['quantite'];
    }

    public function getSpecificPanier($user_id, $id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 'p.quantite', 'p.prix', 'a.photo', 'a.nom', 'a.prix * p.quantite as prixTot', 'p.album_id', 'p.user_id')
            ->from('paniers', 'p')
            ->innerJoin('p', 'album', 'a', 'p.album_id = a.id')
            ->innerJoin('p', 'users', 'u', 'p.user_id = u.id')
            ->where('p.user_id = :user_id and p.id = :id')
            ->addOrderBy('p.quantite', 'ASC')
            ->setParameter('user_id', $user_id)
            ->setParameter('id', $id);
        return $queryBuilder->execute()->fetch();
    }

    public function getPanierId($user_id, $album_id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('id')
            ->from('paniers')
            ->where('user_id = ? and album_id = ?')
            ->setParameter(0, $user_id)
            ->setParameter(1, $album_id);

        return (int)$queryBuilder->execute()->fetch()['id'];
    }

    public function deleteUserPanier($user_id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('paniers')
            ->where('user_id = '.$user_id);
        $queryBuilder->execute();
    }


}