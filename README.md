Windwork 发邮件组件
============================
封装API通过SMTP/mail函数发送邮件。
如果服务器安装有邮件服务器，如sendmail等，则可以使用内置的mail函数发送邮件获得更高的性能和更多的个性化参数，否则使用smtp发送。
*nix服务器上建议安装postfix邮件服务器，通过mail函数发送邮件。

# 使用方法
```
1. 在 config/app.php中设置 srv.mailer 配置
2. 获取邮件实例来发送邮件，wfMailer()->send('收件人邮箱', '邮件标题', '邮件内容');
```

## 使用案例
```
// 使用smtp发送
$cfg = [
    'class' => '\\wf\\mailer\\strategy\\SMTP', // SMTP）使用smtp发送邮件；Mail）使用mail函数发送邮件
    'port' => 25,
    'host' => 'smtp服务器',
    'auth' => true,
    'user' => 'smtp账号',
    'pass' => '邮箱密码',
];
$class = $cfg['class'];
$mailer = new $class($cfg);

// 在windwork应用中使用下面的方式创建实例，不需要前面的代码，配置信息在config/app.php中设置
//$mailer = wfMailer();
$mailer->send('收件人邮箱', '邮件标题', '邮件内容');

// 使用内置mail函数发送
$cfg = [
    'class'  => '\\wf\\mailer\\strategy\\Mail',
    'user'   => 'xxx@xxx.com', // 发件邮箱
];
$class = $cfg['class'];
$mailer = new $class($cfg);
$mailer->send('收件人邮箱', '邮件标题', '邮件内容');

```

## 发送邮件接口
```
namespace wf\mailer;

/**
 * 发送邮件接口
 *
 * @package     wf.mailer
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.mailer.html
 * @since       0.1.0
 */
interface MailerInterface 
{
    /**
     * 发送邮件
     * 
     * @param string $to 收件邮箱
     * @param string $subject  邮件主题
     * @param string $message  邮件内容
     * @param string $from = ''  发件邮箱，留空则使用配置中的邮箱账号
     * @param string $cc = '' 抄送，每个邮件用半角逗号隔开
     * @param string $bcc = ''  密送，每个邮件用半角逗号隔开
     * @return bool
     * @throws \wf\mailer\Exception
     */
    public function send($to, $subject, $message, $from = '', $cc = '', $bcc = '');
}
```