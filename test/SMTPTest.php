<?php
require_once '../lib/Exception.php';
require_once '../lib/Helper.php';
require_once '../lib/MailerInterface.php';
require_once '../lib/adapter/SMTP.php';

use \wf\mailer\adapter\SMTP;

/**
 * SMTP test case.
 */
class SMTPTest extends PHPUnit_Framework_TestCase {
    
    /**
     *
     * @var SMTP
     */
    private $sMTP;
    
    private $cfg = array(
        'class' => 'SMTP',
        'port' => 25,
        'host' => 'smtp.163.com',
        'auth' => true,
        'user' => 'p_cm@163.com',
        'pass' => 'CM->o.163.',
    );
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp ();
        $this->sMTP = new SMTP($this->cfg);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        // TODO Auto-generated SMTPTest::tearDown()
        $this->sMTP = null;
        
        parent::tearDown ();
    }
    
    /**
     * Tests SMTP->send()
     */
    public function testSend() {
        $this->sMTP->send('cmpan@qq.com', '测试邮件', '测试邮件内容。。。。^_^', 'p_cm@163.com', 'Windwork·夏花', '小花');
        
        $mailer = new \wf\mailer\adapter\SMTP($this->cfg);
        $mailer->send('cmpan@qq.com', '测试邮件', '测试邮件内容。。。。^_^', 'p_cm@163.com');
        
    }
}

