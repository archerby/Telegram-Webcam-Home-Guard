<?php



//>> sudo crontab -e
//* * * * * /usr/bin/php5 /var/www/sweethome/server.php
set_time_limit(0);
error_reporting(E_ALL); //Выводим все ошибки и предупреждения
set_time_limit(0);	//Время выполнения скрипта не ограничено
ob_implicit_flush();	//Включаем вывод без буферизации 
ignore_user_abort(true); // Игнорируем закрытие окна браузера с сервером

 

// Create new Bot Object
require('Telegram.php');
require('config.php');
//$bot = new TelegramBot($tokenAPI);


$fLog=fopen($dir."/log.txt",'a');
fwrite($fLog, date("d.m.Y H:i:s")." — запуск сервера... ");


$fR=fopen($dir."/running",'w');
$fl=flock($fR, LOCK_EX | LOCK_NB);



$tg = new telegramBot($tokenAPI);

if($fl){
//	print("I`m the only running process...<hr>");
fwrite($fLog," Успешно!\r\n");

	
	

		while(true){
		// Get lastUpdate ID

				
				$fileOfUpdate=$dir."/lastupdate.txt";
				if(is_file($fileOfUpdate)){
						$updateIdFrom=intval(file_get_contents($fileOfUpdate))+1;
				}else{
						file_put_contents($fileOfUpdate,"0");
						$updateIdFrom=0;
				}
		 
		 
				$fileOfAuth=$dir."/authchats.txt";
				if(is_file($fileOfAuth)){
						$authDialogs=file($fileOfAuth);
				}else{
						file_put_contents($fileOfAuth,"");
						$authDialogs=array();
				}
				
				
				// Get Updates with 30 sec Long-Poll
				//$updates=$bot->GetUpdates($updateIdFrom,null,60)->result;
				$updates = $tg->pollUpdates($updateIdFrom,60);

				foreach($updates['result'] as $data){

					$updateId = $updates['result'][count($updates['result']) - 1]['update_id'];
					$message = $data['message']['text'];
					$chatId = $data['message']['chat']['id'];
					
					

					if(in_array($chatId,$authDialogs)){
					
							if($message=="/reboot"){
											
											$tg->sendMessage($chatId, 'Сервер будет перезапущен в течение минуты!');
											file_put_contents($fileOfUpdate,$updateId);
											fopen($dir."/stopserver");
											exit();
											
							}
					
							 
					
							if($message=="/start"){
									
								
									$tg->sendMessage($chatId,"Вы вошли в режим наблюдения!");
									
							}elseif($message=="/stop"){
								
									
									$k=array_search($chatId,$authDialogs);
									if($k!==false){
											unset($authDialogs[$k]);
											$f=fopen($fileOfAuth,"w+");
											foreach($authDialogs as $i=>$item){
													if(trim($item)!=null){
															fwrite($f,$item."\r\n");
													}
											}
											fclose($f);
											//$bot->SendMessage($chatId,"Вы успешно вышли из системы!");
											$tg->sendMessage($chatId, 'Вы вышли из режима наблюдения!');
									}
							
							}elseif($message=="/photo"){

							
								
									$tg->sendChatAction($chatId, 'upload_photo');
									
									
									$tg->sendMessage($chatId, 'Эта функция пока не реализована!');
									
									/*
									$name=uniqid().".jpg";
								
									exec("service motion stop");
									exec("fswebcam -r 1024x800 --jpeg 99 -D 1 ".$dir."/photos/".$name);
									exec("service motion start");
									sleep(1);
									
									if(is_file($dir."/photos/".$name)){
											$tg->sendPhoto($chatId, $dir."/photos/".$name);
											unlink($dir."/photos/".$name);
									}else{
											$tg->sendMessage($chatId, 'Проблемы с камерой!...');
									}
									
									
								 
									$name=uniqid().".jpg";
									$ph=file_get_contents("http://127.0.0.1:8081");
									
									$f=fopen($dir."/photos/".$name,"w");
									fwrite($f,$ph);
									fclose($f);
									
									if(is_file($dir."/photos/".$name)){
									
											$tg->sendPhoto($chatId, $dir."/photos/".$name);
											//unlink($dir."/photos/".$name);
									}else{
											$tg->sendMessage($chatId, 'Проблемы с камерой!...');
									}
									*/
									
									
							}else{
							
								//$bot->SendMessage($chatId,"Я не знаю что ответить :(");
								$tg->sendMessage($chatId, 'Я не знаю что ответить :(');
							}
					
					
					
					}else{
							if($message=="/start"){
									
									//$bot->SendMessage($chatId,"Для входа в систему необходимо ввести пароль:");
									$tg->sendMessage($chatId, 'Для начала наблюдения необходимо ввести пароль:');
									
									
							}elseif($message==$password){
							
								$authDialogs[]=$chatId;
								$f=fopen($fileOfAuth,"w+");
								foreach($authDialogs as $i=>$item){
										if(trim($item)!=null){
												fwrite($f,$item."\r\n");
										}
								}
								fclose($f);
								
								$tg->sendMessage($chatId,"Вы вошли в режим наблюдения!");
							}else{
								$tg->sendMessage($chatId,"Введите пароль:");
							}
							
					}
					

				 file_put_contents($fileOfUpdate,$updateId);
				 
				 
		 
					
					
					
					
					
				}



 

		}


}else{
	fwrite($fLog," Процесс уже запущен. Остановка!\r\n");
	echo"Одновременно может быть запущен только один процесс.";
	exit();

}

//$q=$BotNow->Status();
//$q=$BotNow->GetUpdates()->result;

?>