<?php

namespace App\Http\Service;

use GuzzleHttp\Client;
use Corp104\Common\Crypt\Encrypter;
use Corp104\Common\Crypt\Drivers\WebService;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\StreamFactory;

class ACAdapter {

  public function login($email, $passwd) {
    $endPoint = env('AC_ENDPOINT');
    $url = $endPoint.'/ac/login';

    $driver = new WebService(
      new Client(),
      new RequestFactory(),
      new StreamFactory(),
      env('AES_ENDPOINT'),
      env('AES_TOKEN')
    );
    $encrypter = new Encrypter($driver);

    // 加密
    $encryptObj = $encrypter->encrypt([$email, $passwd]);
    $encEmail = $encryptObj[0];
    $encPasswd = $encryptObj[1];

    $client = new Client();
    $response = $client->request('POST', $url, [
      'headers' => ['Content-Type' => 'application/json'],
      'body' => json_encode([
        'loginid' => $encEmail,
        'password' => $encPasswd,
      ])
    ]);
    return json_decode($response->getBody()->getContents(), true);
  }

  public function checkLogin($ssoTokenId) {
    $endPoint = env('AC_ENDPOINT');

    return ['login' => true, 'pid' => '240000'];
  }

  public function getACInfo($pid) {
    // 含解密

  }

  public function logout($ssoTokenId) {
    $endPoint = env('AC_ENDPOINT');

    return true;
  }
}