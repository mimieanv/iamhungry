<?php

/**
 * @version 2013 April, 24
 * @author jeremy
 */

class Registration implements IModule
{
	/*private $error = '';
	private $state = '';*/

    function __construct() {
    	$this->state = '';
    	$this->error = '';
    	
    	if(isset($_REQUEST['action'])) {
			switch($_REQUEST['action']) {

				# L'USER VEUT S'INSCRIRE
				case 'try_register' :
					$this->state = 'try_register';
				break;

				# CREATION DE COMPTE
				case 'register' :
echo 'creating new account... <br />';
					// Verification des donnees retournees par l'user
					if(strlen($_REQUEST['register_password']) < 8)
						$this->error = 'Password must be at least constitued by 8 characters.';
					if(!isEmail($_REQUEST['register_mail']))
						$this->error = 'Incorrect email address!';
					else {
						$userTest = new User($_REQUEST['register_mail']);
						if(!$userTest->error != '')
							$this->error = "This email is already used!";
					}

					// On checke la presence de toutes les infos necessaires a la creation d'un compte
					if(!isset($_REQUEST['register_name']))
						$this->error = 'Sorry, we need to know your name.';

					if($this->error == '') {
						$this->createAccount($_REQUEST);
						$this->state = 'ok';
					} else {
						echo $this->error;
						$this->state = 'register';
					}
				break;
			}
    	}
    }

    public function display()
    {
		switch($this->state) {
			
			case 'try_register' :
?>	

				<form id="formElem" name="formElem" action="index.php?page=registration&action=register" method="post" >
					<legend>Account</legend>
					
					<label for="email">Email</label>
					<input id="email" name="register_mail" placeholder="iam@hung.ry" type="email" AUTOCOMPLETE=OFF />
					<br />
					<label for="password">Password</label>
					<input id="password" name="register_password" type="password" placeholder="8 characters minimum" AUTOCOMPLETE=OFF />
					<br />
					<label for="last_name">Nom</label>
					<input id="last_name" name="register_name" type="text" AUTOCOMPLETE=OFF />
					
					<button id="registerButton" type="submit">Create my account and eat soon!</button>
				</form>

<?php
			break;
		}
    }
    
    function preProcess($construct)
    {
    	
    }

    /**
     * Function createAccount
     * TODO create a new account for the user
     * @param Array $user_infos
     */
    public function createAccount($register_data) {
 		$email		= DB::getInstance()->real_escape_string($register_data['register_mail']);
		$name		= DB::getInstance()->real_escape_string($register_data['register_name']);
		$name		= ucfirst(strtolower($name));
		
        $password	= hash('sha512', ($firstName . 'lasagnes!' . $register_data['register_password']));

		$id_user = IAMHUNGRY::getInstance()->createUser($email, $password, $name);
		echo "I heard that you're hungry? You're at the good place, welcome hungry man!";
		$this->login(new User($id_user));
    }

    public function login($user) {
    	$_SESSION['id_user']			= $user->id;
		IAMHUNGRY::getInstance()->user	= $user;
		echo "Welcome back guy!";
    }

}