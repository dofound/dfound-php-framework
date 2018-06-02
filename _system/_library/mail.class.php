<?php
/**
*  http://www.extmail.org
*
*/
class lib_mail
{
	public $host;
	public $password;
	public $user;
	public $title;
	public $server_email;
	
    public static function sent($email,$body) {
    	$mail = new lib_phpmailer();
		if (is_array($email)) {
			foreach ($email as $pv) {
				$mail->AddAddress($pv);
			}
		} else {
			$mail->AddAddress($email);
		}
		$pdata = date('Y-m-d');
    	$mail->CharSet = 'utf-8';
    	$mail->Encoding = 'base64';
    	$mail->Subject = $this->title;
    	$mail->IsSMTP();
    	$mail->Host = $this->host;
    	$mail->SMTPAuth = true;
    	$mail->Username = $this->user;
    	$mail->Password = $this->password;
    	$mail->SetFrom($this->server_email, 'service');

    	$mail->MsgHTML($body);
    	return $status = $mail->Send();
    }

}