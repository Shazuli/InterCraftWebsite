<?php

namespace App;

use App\Models\CaptchaResponse;

class Captcha
{
	private static $_config;

	public static function configure($config)
	{
		self::$_config = $config;
	}

	public static function response()
	{
		$url = self::$_config['url'];
		$fields = [
			'secret' => self::$_config['secret_key'],
			'response' => post('g-recaptcha-response'),
		];

		$fieldString = '';
		foreach($fields as $key=>$value)
			$fieldString .= $key.'='.$value.'&';

		rtrim($fieldString, '&');

		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_POST, count($fields));
		curl_setopt($c, CURLOPT_POSTFIELDS, $fieldString);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, True);
		$result = json_decode(curl_exec($c), True);
		curl_close($c);

		return new CaptchaResponse($result);
	}

	public static function html()
	{
		$key = self::$_config['site_key'];
		return "<div class=\"g-recaptcha\" data-sitekey=\"{$key}\"></div>";
	}
}