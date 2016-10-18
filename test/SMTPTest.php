<?php
require_once '../Exception.php';
require_once '../IMailer.php';
require_once '../MailerFactory.php';
require_once '../adapter/SMTP.php';

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
		'mail_adapter' => 'SMTP',
		'mail_port' => 25,
		'mail_host' => 'smtp.163.com',
		'mail_auth' => true,
		'mail_user' => 'p_cm@163.com',
		'mail_pass' => 'CM->o.163.',
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
		
		$mailer = \wf\mailer\MailerFactory::create($this->cfg);
		$mailer->send('cmpan@qq.com', '测试邮件', '\wf\mailer\MailerFactory 测试邮件内容。。。。^_^', 'p_cm@163.com');
		
	}
}

