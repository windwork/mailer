<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\mailer;

/**
 * 发送邮件接口
 *
 * @package     wf.mailer
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.mailer.html
 * @since       0.1.0
 */
interface MailerInterface {
    
    /**
     * 发送邮件
     * 
     * @param string $to 收件邮箱
     * @param string $subject  邮件主题
     * @param string $message  邮件内容
     * @param string $from  发件邮箱
     * @param string $cc = '' 抄送，每个邮件用半角逗号隔开
     * @param string $bcc = ''  密送，每个邮件用半角逗号隔开
     * @return bool
     * @throws \wf\mailer\Exception
     */
    public function send($to, $subject, $message, $from, $cc = '', $bcc = '');
}

