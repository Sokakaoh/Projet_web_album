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
}