<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace wf\mailer\adapter;

use \wf\mailer\Exception;

/**
 * 使用mail函数发邮件 
 * 
 * @package     wf.mailer.adapter
 * @author      erzh <cmpan@qq.com>
 * @link        http://www.windwork.org/manual/wf.mailer.html
 * @since       0.1.0
 */
class Mail implements \wf\mailer\IMailer {
	protected $cfg;

	public function __construct(array $cfg) {
		$this->cfg = $cfg;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \wf\mailer\IMailer::send()
	 */
	public function send($to, $subject, $message, $from, $fromName = '', $toName = '') {
		// 收件人称呼
		$toName || $toName = 1;
		
		// 发信者
		$emailFrom = $from == '' ? '=?utf-8?B?'.base64_encode($fromName)."?= <".$from.">" : (preg_match('/^(.+?) \<(.+?)\>$/', $from, $mats) ? '=?utf-8?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $from);
		
		$emailTo = preg_match('/^(.+?) \<(.+?)\>$/',$to, $mats) ? ($toName ? '=?utf-8?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $to;;
		
		$emailSubject = '=?utf-8?B?'.base64_encode(preg_replace("/[\r|\n]/", '', '['.$fromName.'] '.$subject)).'?=';
		$emailMessage = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= "X-Mailer: Windwork\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		$headers .= "From: {$emailFrom}\r\n";
		$headers .= "Content-Transfer-Encoding: base64\r\n";
		
		if(!mail($emailTo, $emailSubject, $emailMessage, $headers)) {
			throw new Exception("Mail failed: {$to} {$subject}");
			return false;
		}
		
		return true;
	}
}


