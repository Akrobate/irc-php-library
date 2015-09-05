<?php

/**
 *	Classe permettant de gerer la connection a un IRC
 *
 *	@date		2015	
 *	@author		Artiom FEDOROV
 *
 */

class IRCMessage {
	
	public $command = "";
	public $message = "";
	public $pseudo = "";	
	public $channel = "";
	public $typemsg = "";
	public $touser = "";
	
	
	public function parseIrcMessage($data) {
	
		$arr = explode(':', $data);
		
		if (isset($arr[1])) {
			$arr2 = explode(" ", $arr[1]);
			if (isset($arr2[1])) {
				$command = trim($arr2[1]);
				$this->command = $command;
		
				if ($command == "PRIVMSG") {
					// ":Arti!~artiom@EpiK-A42F5158.rev.numericable.fr PRIVMSG #informatique :Bonjour a tous!!"

					// ":Jab!~Jab@EpiK-8CB32F8.cable.012.net.il PRIVMSG #Politique :koollman: certains pays ont un ratio dette/pib qui baisse"

					if (strpos(trim($arr2[2]), "#") === false) {
						$this->typemsg = 'private';
						
						$tmp = explode('!', $arr2[0]);
						$this->pseudo = trim($tmp[0]);
						$this->message = trim($arr[2]);

					} else {
					
						$matches = array();
						$regex = '/:(.*?)!.*? PRIVMSG (.*) :(.*?):(.*)/';
						$hasMatched = preg_match($regex, $data, $matches);

						$user = "";
						$channel = "";
						$message = "";
						$touser = "";

						if ($hasMatched && (strpos(trim($matches[3]), " ") === false)) {
							$user = $matches[1];
							$touser = $matches[3];
							$channel = $matches[2];
							$message = $matches[4];
						} else {
							$regex = '/:(.*?)!.*? PRIVMSG (.*) :(.*)/';
							$hasMatched = preg_match($regex, $data, $matches);
							if ($hasMatched) {
								$user = $matches[1];
								$touser = "";
								$channel = $matches[2];
								$message = $matches[3];
							}
						}
						
						$this->pseudo = $user;
						$this->message = $message;
						$this->touser = $touser;	
									
						$this->channel = trim($channel);
						$this->typemsg = 'public';
					}
		
				} elseif ( $command == "JOIN") {
					// ":quentin!~quentin@EpiK-8B73212F.mc.videotron.ca JOIN :#Informatique"
					$tmp = explode('!', $arr2[0]);
					$this->pseudo = trim($tmp[0]);		
					$this->channel = trim($arr[2]);
					$this->message = "";
				} elseif ( $command == "QUIT") {
					//  ":An0nym!~An0nym@EpiK-21305DB3.w90-22.abo.wanadoo.fr QUIT :Connection reset by peer"
					$tmp = explode('!', $arr2[0]);
					$this->pseudo = trim($tmp[0]);		
					$this->channel = trim($arr[2]);
					$this->message = "";
				} elseif ( $command == "PART") {
					// ":An0nym!~An0nym@EpiK-21305DB3.w90-22.abo.wanadoo.fr QUIT :Connection reset by peer"
					$tmp = explode('!', $arr2[0]);
					$this->pseudo = trim($tmp[0]);		
					$this->channel = trim($arr[2]);
					$this->message = "";
				} elseif ( $command == "322") {
					if (isset($arr2[3]) && isset($arr2[4])) {
				
						// ":An0nym!~An0nym@EpiK-21305DB3.w90-22.abo.wanadoo.fr QUIT :Connection reset by peer"
						//echo("=====================>" . $data);
						$this->pseudo = trim($arr2[2]);		
						$this->channel = trim($arr2[3]);
						$this->nbrUsers = trim($arr2[4]);
						$this->message = "";
					
					} else {
						$command == "";
					}
				}
				
				//echo($data);
				
			}
		}			
			
	}
	
	
	public function savetofile($file) {
		if ($this->command != "") {
			$data = " CH: " . $this->channel .  " CMD: " . $this->command . " PSEUDO : " . $this->pseudo . " MSG : " . $this->message . "\n";
			file_put_contents($file, $data, FILE_APPEND);
		}
	}
	


}
