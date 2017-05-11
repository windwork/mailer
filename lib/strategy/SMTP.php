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
 * 使用SMTP发邮件
 *
 * @package     wf.mailer.strategy
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.mailer.html
 * @since       0.1.0
 */
class SMTP implements \wf\mailer\MailerInterface {
    
    protected $cfg = array(
        'mailPort' => 25,
        'mailHost' => '',
        'mailAuth' => true,
        'mailUser' => '',
        'mailPass' => '',
    );
    
    public function __construct(array $cfg) {
        $this->cfg = $cfg;
    }

    /**
     *
     * {@inheritDoc}
     * @see \wf\mailer\MailerInterface::send()
     */
    public function send($to, $subject, $message, $from = '', $cc = '', $bcc = '') {
        // 端口
        $this->cfg['mailPort'] = empty($this->cfg['mailPort']) ? 25 : $this->cfg['mailPort'];
        
        $to      = \wf\mailer\Helper::emailEncode($to);
        $from    = \wf\mailer\Helper::emailEncode($from);
        $subject = \wf\mailer\Helper::encode($subject);        
        $message = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));
    
        $headers = "From: {$from}\r\n";

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
        
        $headers .= "X-Priority: 3\r\n"
                 . "X-Mailer: Windwork\r\n"
                 . "MIME-Version: 1.0\r\n"
                 . "Content-type: text/html; charset=utf-8\r\n"
                 . "Content-Transfer-Encoding: base64\r\n";
         
        if(!$fp = fsockopen($this->cfg['mailHost'], $this->cfg['mailPort'], $errno, $errstr, 30)) {
            throw new Exception("SMTP ({$this->cfg['mailHost']}:{$this->cfg['mailPort']}) CONNECT - Unable to connect to the SMTP server");
            return false;
        }
        stream_set_blocking($fp, true);
    
        $lastMessage = fgets($fp, 512);
        if(substr($lastMessage, 0, 3) != '220') {
            throw new Exception("SMTP {$this->cfg['mailHost']}:{$this->cfg['mailPort']} CONNECT - $lastMessage");
            return false;
        }
    
        fputs($fp, ($this->cfg['mailAuth'] ? 'EHLO' : 'HELO')." windwork\r\n");
        $lastMessage = fgets($fp, 512);
        if(substr($lastMessage, 0, 3) != 220 && substr($lastMessage, 0, 3) != 250) {
            throw new Exception("SMTP ({$this->cfg['mailHost']}:{$this->cfg['mailPort']}) HELO/EHLO - $lastMessage", 0);
            return false;
        }
    
        while(1) {
            if(substr($lastMessage, 3, 1) != '-' || empty($lastMessage)) {
                break;
            }
            $lastMessage = fgets($fp, 512);
        }
    
        if($this->cfg['mailAuth']) {
            fputs($fp, "AUTH LOGIN\r\n");
            $lastMessage = fgets($fp, 512);
            if(substr($lastMessage, 0, 3) != 334) {
                throw new Exception("SMTP ({$this->cfg['mailHost']}:{$this->cfg['mailPort']}) AUTH LOGIN - $lastMessage", 0);
                return false;
            }
    
            fputs($fp, base64_encode($this->cfg['mailUser'])."\r\n");
            $lastMessage = fgets($fp, 512);
            if(substr($lastMessage, 0, 3) != 334) {
                throw new Exception("SMTP ({$this->cfg['mailHost']}:{$this->cfg['mailPort']}) USERNAME - $lastMessage", 0);
                return false;
            }
    
            fputs($fp, base64_encode($this->cfg['mailPass'])."\r\n");
            $lastMessage = fgets($fp, 512);
            if(substr($lastMessage, 0, 3) != 235) {
                throw new Exception("SMTP ({$this->cfg['mailHost']}:{$this->cfg['mailPort']}) PASSWORD - $lastMessage", 0);
                return false;
            }
    
            $emailFrom = $from;
        }
    
        fputs($fp, "MAIL FROM: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $from).">\r\n");
        $lastMessage = fgets($fp, 512);
        if(substr($lastMessage, 0, 3) != 250) {
            fputs($fp, "MAIL FROM: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $from).">\r\n");
            $lastMessage = fgets($fp, 512);
            if(substr($lastMessage, 0, 3) != 250) {
                throw new Exception("SMTP ({$this->cfg['mailHost']}:{$this->cfg['mailPort']}) MAIL FROM - $lastMessage", 0);
                return false;
            }
        }
    
        fputs($fp, "RCPT TO: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $to).">\r\n");
        $lastMessage = fgets($fp, 512);
        if(substr($lastMessage, 0, 3) != 250) {
            fputs($fp, "RCPT TO: <".preg_replace("/.*\\<(.+?)\\>.*/", "\\1", $to).">\r\n");
            $lastMessage = fgets($fp, 512);
            throw new Exception("SMTP ({$this->cfg['mailHost']}:{$this->cfg['mailPort']}) RCPT TO - $lastMessage", 0);
            return false;
        }
    
        fputs($fp, "DATA\r\n");
        $lastMessage = fgets($fp, 512);
        if(substr($lastMessage, 0, 3) != 354) {
            throw new Exception("SMTP ({$this->cfg['mailHost']}:{$this->cfg['mailPort']}) DATA - $lastMessage", 0);
            return false;
        }
    
        $headers .= 'Message-ID: <'.date('YmdHs').'.'.substr(md5($message.microtime()), 0, 6).rand(100000, 999999).'@'.@$_SERVER['HTTP_HOST'].">\r\n";
    
        fputs($fp, "Date: ".date('r')."\r\n");
        fputs($fp, "To: {$to}\r\n");
        fputs($fp, "Subject: {$subject}\r\n");
        fputs($fp, "{$headers}\r\n");
        fputs($fp, "\r\n\r\n");
        fputs($fp, "{$message}\r\n.\r\n");
        
        $lastMessage = fgets($fp, 512);
        if(substr($lastMessage, 0, 3) != 250) {
            throw new Exception("SMTP ({$this->cfg['mailHost']}:{$this->cfg['mailPort']}) END - {$lastMessage}", 0);
        }
        fputs($fp, "QUIT\r\n");
        
        return true;
    }
}

