<?php
namespace App\Model;


use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Validator\Constraints\Date;

class CommandeModel
{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAllCommandes()
    {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('c.id', 'c.user_id', 'c.prix', 'c.date', 'c.etat_id')
            ->from('commandes', 'c');
        return $queryBuilder->execute()->fetchAll();
    }

    public function add($datas){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('commandes')
            ->values([
                'id' => $datas['id'],
                'user_id' => $datas['user_id'],
                'prix' => $datas['prix'],
                'date' => $datas['date'],
                'etats_id' => $datas['etats_id']
            ]);
        return $queryBuilder->execute();
    }


}