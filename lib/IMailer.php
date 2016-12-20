<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2016 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace wf\mailer;

/**
 * 发送邮件接口
 *
 * @package     wf.mailer
 * @author      erzh <cmpan@qq.com>
 * @link        http://www.windwork.org/manual/wf.mailer.html
 * @since       0.1.0
 */
interface IMailer {
	
	/**
	 * 发送邮件
	 * 
	 * @param string $to 收件邮箱
	 * @param string $subject  邮件主题
	 * @param string $message  邮件内容
     * @param string $from  发件邮箱
     * @param string $fromName = '' 发件人称呼
     * @param string $toName = ''   收件人称呼
	 * @return bool
     * @throws \wf\mailer\Exception
	 */
	public function send($to, $subject, $message, $from, $fromName = '', $toName = '');
}

