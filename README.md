Windwork 发邮件组件
============================
通过SMTP/mail函数发送邮件
如果服务器安装有邮件服务器，如sendmail等，则可以使用内置的mail函数发送邮件获得更高的性能和更多的个性化参数，否则使用smtp发送。

## 使用案例
```
// 使用smtp发送
$cfg = [
    'class' => 'SMTP', // SMTP）使用smtp发送邮件；Mail）使用mail函数发送邮件
    'port' => 25,
    'host' => 'smtp服务器',
    'auth' => true,
    'user' => 'smtp账号',
    'pass' => '邮箱密码',
];
$class = "\\wf\\mailer\\strategy\\{$cfg['class']}";
$mailer = new $class($cfg);
$mailer->send('收件人邮箱', '邮件标题', '邮件内容', '发件邮箱');

// 使用内置mail函数发送
$cfg = [
    'class'    => 'Mail',
];
$class = "\\wf\\mailer\\strategy\\{$cfg['class']}";
$mailer = new $class($cfg);
$mailer->send('收件人邮箱', '邮件标题', '邮件内容', '发件邮箱');

```

## 发送邮件接口
```
/**
 * 发送邮件接口
 */
interface MailerInterface 
{
    
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
```