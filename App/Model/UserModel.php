<?php
namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class UserModel {

	private $db;
    private $session;

	public function __construct(Application $app) {
		$this->db = $app['db'];
        $this->session = $app['session'];
	}

	public function verif_login_mdp_Utilisateur($login,$mdp){
		$sql = "SELECT id, login,password,droit FROM users WHERE login = ? AND password = ?";
		$res=$this->db->executeQuery($sql,[$login,$mdp]);
		if($res->rowCount()==1)
			return $res->fetch();
		else
			return false;
	}

	public function getIdUser(){
        if ($this->session->get('logged') != null){
			return $this->session->get('id');
        }
        return 0;
	}
	
	public function isAdmin(){
		if ($this->session->get('logged') != null && $this->session->get('droit') == 'DROITadmin')
            return true;
        else
            return false;
	}

	public function getUser(){
		$query = new QueryBuilder($this->db);
		$query
			->select('id', 'email', 'password', 'login', 'nom', 'prenom', 'code_postal',
				'ville', 'adresse', 'valide', 'droit')
			->from('users')
			->where('id = ?')
			->setParameter(0, (int)$this->getIdUser());
		
		return $query->execute()->fetch();
	}

	public function editUser($data){
		$query = new QueryBuilder($this->db);

		$query
			->update('users')
			->set('nom', '?')
			->set('adresse', '?')
			->set('code_postal', '?')
			->set('ville', '?')
			->set('email', '?')
			->set('login', '?')
			->set('password', '?')
            ->set('droit', '?')
			->set('prenom', '?')
			->where('id = ?')
			->setParameter(0, $data['nom'])
			->setParameter(1, $data['adresse'])
			->setParameter(2, $data['code_postal'])
			->setParameter(3, $data['ville'])
			->setParameter(4, $data['email'])
			->setParameter(5, $data['login'])
			->setParameter(6, $data['password'])
            ->setParameter(7, $data['droit'])
            ->setParameter(8, $data['prenom'])
			->setParameter(9, $data['id']);
            

		return $query->execute();
	}

	public function getAllUser(){
		$query = new QueryBuilder($this->db);
		$query
			->select('u.nom', 'u.prenom', 'u.adresse', 'u.code_postal', 'u.ville', 'u.email', 'u.login', 'u.password', 'u.droit', 'u.id')
			->from('users', 'u');

		return $query->execute()->fetchAll();
	}

    public function getUserById($id){
        $query = new QueryBuilder($this->db);
        $query
            ->select('id', 'email', 'password', 'login', 'nom', 'code_postal',
                'ville', 'adresse', 'valide', 'droit', 'prenom')
            ->from('users')
            ->where('id = ?')
            ->setParameter(0, $id);

        return $query->execute()->fetch();
    }

    public function delete($id){
        $query = new QueryBuilder($this->db);
        $query
            ->delete('users')
            ->where('id = ?')
            ->setParameter(0, $id);
        return $query->execute();
    }

    public function addUser($data){
        $query = new QueryBuilder($this->db);
        $query
            ->insert('users')
            ->values([
                'login' => '?',
                'prenom' => '?',
                'nom' => '?',
                'password' => '?',
                'email' => '?',
                'code_postal' => '?',
                'droit' => '?',
                'adresse' => '?',
                'ville' => '?',
                'valide' => 0
            ])
            ->setParameter(0, $data['login'])
            ->setParameter(1, $data['prenom'])
            ->setParameter(2, $data['nom'])
            ->setParameter(3, $data['password'])
            ->setParameter(4, $data['email'])
            ->setParameter(5, $data['code_postal'])
            ->setParameter(6, 'DROITclient')
            ->setParameter(7, $data['adresse'])
            ->setParameter(8, $data['ville']);
        
        return$query->execute();
    }
    
    public function isLogged(){
        return $this->session->get('logged') != null;
    }
}