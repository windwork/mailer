Windwork 发邮件组件
============================
通过SMTP/mail函数发送邮件

# useage
```
// 使用smtp发送
$cfg = array(
    'mail_adapter' => 'SMTP',
    'mail_port' => 25,
    'mail_host' => 'smtp服务器',
    'mail_auth' => true,
    'mail_user' => 'smtp账号',
    'mail_pass' => '邮箱密码',
);
$mailer = \wf\mailer\MailerFactory::create($cfg);
$mailer->send('收件人邮箱', '邮件标题', '邮件内容', '发件邮箱');

// 使用内置mail函数发送
$cfg = array(
    'mail_adapter' => 'Mail',
);
$mailer = \wf\mailer\MailerFactory::create($cfg);
$mailer->send('收件人邮箱', '邮件标题', '邮件内容', '发件邮箱');

```

# 发送邮件接口
```
    /**
     * 发送邮件
     * 
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $from 
     * @param string $fromName = '' 网站名称 
     * @param string $toName = '' 收件人地址中包含用户名
     * @return bool
     */
    public function send($to, $subject, $message, $from = '', $fromName = '', $toName = '');
```