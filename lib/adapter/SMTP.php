<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2016 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace wf\mailer\adapter;

use \wf\mailer\Exception;

/**
 * 使用SMTP发邮件
 *
 * @package     wf.mailer.adapter
 * @author      erzh <cmpan@qq.com>
 * @link        http://www.windwork.org/manual/wf.mailer.html
 * @since       0.1.0
 */
class SMTP implements \wf\mailer\IMailer {
	
	protected $cfg = array(
		'mail_port' => 25,
		'mail_host' => '',
		'mail_auth' => true,
		'mail_user' => '',
		'mail_pass' => '',
	);
	
	public function __construct(array $cfg) {
		$this->cfg = $cfg;
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see \wf\mailer\IMailer::send()
	 */
	public function send($to, $subject, $message, $from = '', $fromName = '', $toName = '') {
		$fromName && $fromName = '['.$fromName.']';
		
		// 收件人称呼
		$toName || $toName = 1;
		
		// 端口
		$this->cfg['mail_port'] = empty($this->cfg['mail_port']) ? 25 : $this->cfg['mail_port'];
	
		// 发信者
		$emailFrom = $from == '' ? '=?utf-8?B?'.base64_encode($fromName)."?= <".$from.">" : (preg_match('/^(.+?) \<(.+?)\>$/',$from, $mats) ? '=?utf-8?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $from);
	
		$emailTo = preg_match('/^(.+?) \<(.+?)\>$/',$to, $mats) ? ($toName ? '=?utf-8?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $to;;
	
		$emailSubject = '=?utf-8?B?'.base64_encode(preg_replace("/[\r|\n]/", '', ($fromName . ' ' .$subject))).'?=';
		$emailMessage = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));
	
		$headers = "From: {$emailFrom}\r\n"
				 . "X-Priority: 3\r\n"
				 . "X-Mailer: Windwork\r\n"
				 . "MIME-Version: 1.0\r\n"
				 . "Content-type: text/html; charset=utf-8\r\n"
				 . "Content-Transfer-Encoding: base64\r\n";
	
		if(!$fp = fsockopen($this->cfg['mail_host'], $this->cfg['mail_port'], $errno, $errstr, 30)) {
			throw new Exception("SMTP ({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) CONNECT - Unable to connect to the SMTP server");
			return false;
		}
		stream_set_blocking($fp, true);
	
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != '220') {
			throw new Exception("SMTP {$this->cfg['mail_host']}:{$this->cfg['mail_port']} CONNECT - $lastMessage");
			return false;
		}
	
		fputs($fp, ($this->cfg['mail_auth'] ? 'EHLO' : 'HELO')." windwork\r\n");
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != 220 && substr($lastMessage, 0, 3) != 250) {
			throw new Exception("SMTP ({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) HELO/EHLO - $lastMessage", 0);
			return false;
		}
	
		while(1) {
			if(substr($lastMessage, 3, 1) != '-' || empty($lastMessage)) {
				break;
			}
			$lastMessage = fgets($fp, 512);
		}
	
		if($this->cfg['mail_auth']) {
			fputs($fp, "AUTH LOGIN\r\n");
			$lastMessage = fgets($fp, 512);
			if(substr($lastMessage, 0, 3) != 334) {
				throw new Exception("SMTP ({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) AUTH LOGIN - $lastMessage", 0);
				return false;
			}
	
			fputs($fp, base64_encode($this->cfg['mail_user'])."\r\n");
			$lastMessage = fgets($fp, 512);
			if(substr($lastMessage, 0, 3) != 334) {
				throw new Exception("SMTP ({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) USERNAME - $lastMessage", 0);
				return false;
			}
	
			fputs($fp, base64_encode($this->cfg['mail_pass'])."\r\n");
			$lastMessage = fgets($fp, 512);
			if(substr($lastMessage, 0, 3) != 235) {
				throw new Exception("SMTP ({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) PASSWORD - $lastMessage", 0);
				return false;
			}
	
			$emailFrom = $from;
		}
	
		fputs($fp, "MAIL FROM: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $emailFrom).">\r\n");
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != 250) {
			fputs($fp, "MAIL FROM: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $emailFrom).">\r\n");
			$lastMessage = fgets($fp, 512);
			if(substr($lastMessage, 0, 3) != 250) {
				throw new Exception("SMTP ({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) MAIL FROM - $lastMessage", 0);
				return false;
			}
		}
	
		fputs($fp, "RCPT TO: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $to).">\r\n");
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != 250) {
			fputs($fp, "RCPT TO: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $to).">\r\n");
			$lastMessage = fgets($fp, 512);
			throw new Exception("SMTP ({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) RCPT TO - $lastMessage", 0);
			return false;
		}
	
		fputs($fp, "DATA\r\n");
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != 354) {
			throw new Exception("SMTP ({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) DATA - $lastMessage", 0);
			return false;
		}
	
		$headers .= 'Message-ID: <'.date('YmdHs').'.'.substr(md5($emailMessage.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">\r\n";
	
		fputs($fp, "Date: ".date('r')."\r\n");
		fputs($fp, "To: {$emailTo}\r\n");
		fputs($fp, "Subject: {$emailSubject}\r\n");
		fputs($fp, "{$headers}\r\n");
		fputs($fp, "\r\n\r\n");
		fputs($fp, "{$emailMessage}\r\n.\r\n");
		$lastMessage = fgets($fp, 512);
		if(substr($lastMessage, 0, 3) != 250) {
			throw new Exception("SMTP ({$this->cfg['mail_host']}:{$this->cfg['mail_port']}) END - {$lastMessage}", 0);
		}
		fputs($fp, "QUIT\r\n");
		
		return true;
	}
}

