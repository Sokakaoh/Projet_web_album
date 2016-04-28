<?php
namespace App\Model;

use Silex\Application;

class UserModel {

	private $db;
    private $session;

	public function __construct(Application $app) {
		$this->db = $app['db'];
        $this->session = $app['session'];
	}

	public function verif_login_mdp_Utilisateur($login,$mdp){
		$sql = "SELECT login,password,droit FROM users WHERE login = ? AND password = ?";
		$res=$this->db->executeQuery($sql,[$login,$mdp]);   //md5($mdp);
		if($res->rowCount()==1)
			return $res->fetch();
		else
			return false;
	}

	public function getIdUser(){
        if ($this->session->get('logged') != null){
            return $this->session->get('id');
        }
        return null;
	}
}