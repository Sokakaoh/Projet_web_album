<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class AlbumModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }
    
    public function getAllAlbums() {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('a.id', 't.libelle', 'a.nom', 'a.artiste', 'a.prix', 'a.photo', 'a.stock')
            ->from('album', 'a')
            ->innerJoin('a', 'typeAlbum', 't', 'a.typeAlbum_id=t.id')
            ->addOrderBy('a.nom', 'ASC');
        return $queryBuilder->execute()->fetchAll();

    }

    public function insertAlbum($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('album')
            ->values([
                'nom' => '?',
                'artiste' => '?',
                'typeAlbum_id' => '?',
                'prix' => '?',
                'photo' => '?'
            ])
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['artiste'])
            ->setParameter(2, $donnees['typeAlbum_id'])
            ->setParameter(3, $donnees['prix'])
            ->setParameter(4, $donnees['photo'])
        ;
        return $queryBuilder->execute();
    }

    function getAlbum($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('id', 'typeAlbum_id', 'nom', 'artiste', 'prix', 'photo')
            ->from('album')
            ->where('id= :id')
            ->setParameter('id', $id);
        return $queryBuilder->execute()->fetch();
    }

    public function updateAlbum($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('album')
            ->set('nom', '?')
            ->set('typeAlbum_id','?')
            ->set('prix','?')
            ->set('photo','?')
            ->where('id= ?')
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['typeAlbum_id'])
            ->setParameter(2, $donnees['prix'])
            ->setParameter(3, $donnees['photo'])
            ->setParameter(4, $donnees['id']);
        return $queryBuilder->execute();
    }

    public function deleteAlbum($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('album')
            ->where('id = :id')
            ->setParameter('id',(int)$id);
        return $queryBuilder->execute();
    }
    public function searchAlbum($idtype){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('nom', 'artiste', 'prix', 'photo')
            ->from('album','typeAlbum')
            ->where('album.typeAlbum_id=typeAlbum.id and typeAlbum.id= :idtype')
            ->setParameter('idtype', $idtype);
        return $queryBuilder->execute()->fetch();
    }

    public function getPrixAlbum($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('prix')
            ->from('album')
            ->where('album.id= :id')
            ->setParameter('id', $id);
        return $queryBuilder->execute()->fetch();
    }

    public function decrementQteAlbum($album_id){
        $query = new QueryBuilder($this->db);
        $query
            ->update('album')
            ->set('stock', 'stock - 1')
            ->where("id = ?")
            ->setParameter(0, $album_id);
        $query->execute();
    }


}