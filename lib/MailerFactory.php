<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace wf\mailer;

/**
 * 静态创建日志操作类实例工厂类
 * 
 * @package     wf.mailer
 * @author      erzh <cmpan@qq.com>
 * @link        http://www.windwork.org/manual/wf.mailer.html
 * @since       0.1.0
 */
final class MailerFactory {
	/**
	 * 
	 * @var array
	 */
	private static $instance = array();
	
	/**
	 * 创建日志组件实例
	 * @param array $cfg
	 * @return \wf\mailer\IMailer
	 */
	public static function create(array $cfg) {
		// 获取带命名空间的类名
		$class = "\\wf\\mailer\\adapter\\{$cfg["mail_adapter"]}";

		$scope = md5(serialize($cfg));
		
		// 如果该类实例未初始化则创建
		if(empty(static::$instance[$scope])) {
			static::$instance[$scope] = new $class($cfg);
		}
		
		return static::$instance[$scope];
	}
}


