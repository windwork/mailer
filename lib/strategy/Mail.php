<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\mailer\strategy;

use \wf\mailer\Exception;

/**
 * 使用mail函数发邮件 
 * 
 * @package     wf.mailer.strategy
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.mailer.html
 * @since       0.1.0
 */
class Mail implements \wf\mailer\MailerInterface 
{
    protected $cfg;

    public function __construct(array $cfg) 
    {
        $this->cfg = $cfg;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \wf\mailer\MailerInterface::send()
     */
    public function send($to, $subject, $message, $from = '', $cc = '', $bcc = '') 
    {
        $to = \wf\mailer\Helper::emailEncode($to);
        $from = \wf\mailer\Helper::emailEncode($from);    
        $subject = \wf\mailer\Helper::encode($subject);

        $headers = "From: {$from}\r\n";
        $headers = "To: {$to}\r\n";
        
        // 抄送
        if($cc) {
            $cc = \wf\mailer\Helper::emailEncode($cc);
            $headers .= "Cc: {$cc}\r\n";
        }
        
        // 密送
        if($bcc) {
            $bcc = \wf\mailer\Helper::emailEncode($bcc);
            $headers .= "Bcc: {$bcc}\r\n";
        }

        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "X-Mailer: Windwork\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "Content-Transfer-Encoding: base64\r\n";
        
        // 内容处理
        $message = str_replace("\n\r", "\r", $message);
        $message = str_replace("\r\n", "\n", $message);
        $message = str_replace("\r", "\n", $message);
        $message = str_replace("\n", "\r\n", $message);
        $message = chunk_split(base64_encode($message));
        
        if(!mail($to, $subject, $message, $headers)) {
            throw new Exception("Mail failed: {$to} {$subject}");
        }
        
        return true;
    }
}


