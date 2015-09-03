<?php

/**
 *	Classe permettant de gerer la connection a un IRC
 *
 *	@date		2015	
 *	@author		Artiom FEDOROV
 *
 */

class IRCConnector {

	public $server;
	public $port;
	public $username;
	public $user;		

	public $chan;

	public $socket;
	public $errno;
	public $errstr;


	/**
	 *
	 *		$this->port='6667';
	 *		$this->server='irc.quakenet.org';
	 *		$this->username='QuizdeBot';
	 *		$this->user = $this->username;
	 *		$this->chan = '#LeelaBot';
	 *
	 *
	 */
	
	function __construct($username, $server) {
	
		$this->port = '6667';
		$this->server = $server;
		$this->username = $username;
		$this->user = $this->username;		
		$this->socket = fsockopen( $this->server , $this->port , $this->errno, $this->errstr, 1); // Connexion au serveur.
		$this->chan = array();
	}


	public function joinChanel($chan) {
		if (!in_array($chan, $this->chan)) {
			$this->chan[] = $chan;	
			fputs($this->socket,"JOIN $chan\r\n"); 
		}
	}


	public function connect() {
		$operators = array();
		$voice = array();
		$users_online = array();
		
		if (!$this->socket) {
			exit();
		} else {
			echo("USER {$this->username} {$this->chan} {$this->user}\n\n");
		
			fputs($this->socket, "USER {$this->username} #quiz-sdz {$this->user} .\r\n" );
			fputs($this->socket, "NICK {$this->username}\r\n" ); // Pseudo du bot.
			stream_set_timeout($this->socket, 0);
		}
		
		$continuer = 1; // On initialise une variable permettant de savoir si l'on doit continuer la boucle.
		while($continuer) {
			$donnees = fgets($this->socket, 1024); // Le 1024 permet de limiter la quantité de caractères à recevoir du serveur.
			$retour = explode(':',$donnees); // On sépare les différentes données.
			// On regarde si c'est un PING, et, le cas échéant, on envoie notre PONG.
			if(rtrim($retour[0]) == 'PING')
			{
				fputs($this->socket,'PONG :'.$retour[1]);
				$continuer = 0;
			}
			 if($donnees) // Si le serveur a envoyé des données, on les affiche.
				echo $donnees;
		}
	}



	public function getData($display = true) {

		$donnees = fgets($this->socket, 2048);
		if($donnees) {
			if ($display) {
				echo $donnees;
				file_put_contents("file.log", $donnees, FILE_APPEND);
			}

			if (!$this->pong($donnees)) {
				$ircmsg = new IRCMessage();
				$ircmsg->parseIrcMessage($donnees);
				return $ircmsg;
			}
		}
		return false;
	}


	public function getFullChannelsList() {
		fputs($this->socket, 'LIST'."\r\n");
		
		$allchannels = array();
		
		$continuer = 1;
		while($continuer) {
			$donnees = fgets($this->socket, 1024); // Le 1024 permet de limiter la quantité de caractères à recevoir du serveur.
			if (!$this->pong($donnees)) {
				$ircmsg = new IRCMessage();
				$ircmsg->parseIrcMessage($donnees);
				
				if ($ircmsg->command == 322) {
					$tmp = array();
					$tmp['channel'] = $ircmsg->channel;
					$tmp['users'] = $ircmsg->nbrUsers;				
					$allchannels[] = $tmp;
				} else if ($ircmsg->command == 323) {
					$continuer = 0;
				}
				
			}

		}
		
		return $allchannels;
	}


	public function pong($donnees) {
		$array = explode(':',$donnees);
		if(rtrim($array[0]) == 'PING')
		{
			fputs($this->socket,'PONG :'.$array[1]);
			return true;
		}
		return false;
	}


	// Orderer

	public function getBestChannels($channels) {
		
	


	}

};
