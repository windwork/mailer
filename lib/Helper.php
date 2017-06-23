<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\mailer;

/**
 * Mail 帮助常类
 *
 * @package     wf.mailer
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */
class Helper
{
    /**
     * 邮箱（收件人、发件人、抄送、密送）编码
     * @param string $emails 格式：昵称<you@domain.com>,孟春<cmpan@qq.com> 多个账号用半角逗号隔开
     */
    public static function emailEncode($emails, $encode = 'utf-8') 
    {
        $emails = str_replace(['"', "\r", "\n", "\t"], '', $emails);
        $emailArr = explode(',', $emails);
        
        foreach ($emailArr as $k => $mail) {
            $mail = trim($mail);
            
            if (preg_match('/^(.+?)<(.+?)>$/', $mail, $match)) {
                $mail = static::encode(trim($match[1])) . " <{$match[2]}>";
            }
                
            $emailArr[$k] = $mail;
        }
        
        return implode(', ', $emailArr);
    }
    
    /**
     * 
     * @param string $str
     */
    public static function encode($str)
    {
        return '=?utf-8?B?' . base64_encode($str) . '?=';
    }
    
    /**
     * 邮箱名称是否带昵称
     * @param string $email
     * @return number
     */
    public static function hasNickname($email)
    {
        return preg_match('/^(.+?)<(.+?)>$/', $email);
    }
}

