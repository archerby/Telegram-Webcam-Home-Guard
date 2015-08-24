<?php



//>> sudo crontab -e
//* * * * * /usr/bin/php5 /var/www/sweethome/monitor.php
set_time_limit(0);
error_reporting(E_ALL); //Выводим все ошибки и предупреждения
set_time_limit(0);	//Время выполнения скрипта не ограничено
ob_implicit_flush();	//Включаем вывод без буферизации 
ignore_user_abort(true); // Игнорируем закрытие окна браузера с сервером

 

// Create new Bot Object
require('Telegram.php');
require('config.php');
//$bot = new TelegramBot($tokenAPI);


$fLog=fopen($dir."/logMon.txt",'a');
fwrite($fLog, date("d.m.Y H:i:s")." — запуск монитора... ");


$fR=fopen($dir."/running2",'w');
$fl=flock($fR, LOCK_EX | LOCK_NB);



$tg = new telegramBot($tokenAPI);

if($fl){
//	print("I`m the only running process...<hr>");
fwrite($fLog," Успешно!\r\n");

	
	

		while(true){
		// Get lastUpdate ID
				$fileOfAuth=$dir."/authchats.txt";
				if(is_file($fileOfAuth)){
						$authDialogs=file($fileOfAuth);
				}else{
						file_put_contents($fileOfAuth,"");
						$authDialogs=array();
				}
				
				
				$photos = scandir($dir."/new");
				
				foreach($photos as $p=>$photo){
						if($photo!=".." && $photo!="."){
								
								echo$photo."\r\n";
								foreach($authDialogs as $i=>$chatId){
										$tg->sendChatAction($chatId, 'upload_photo');
										$tg->sendPhoto($chatId, $dir."/new/".$photo);
								}
								
								unlink($dir."/new/".$photo);

						}
				}
				
			sleep(2);	
				if(is_file($dir."/stopserver")){
						unlink($dir."/stopserver");
						fwrite($fLog, date("d.m.Y H:i:s")." — Монитор остановлен по команде stopserver!\r\n");
						exit();
				}
		}


}else{
	fwrite($fLog," Процесс уже запущен. Остановка!\r\n");
	echo"Одновременно может быть запущен только один процесс.";
	exit();
}
 

?>