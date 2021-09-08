<?php

namespace App\Http\Service;

use GuzzleHttp\Client;
use Corp104\Common\Crypt\Encrypter;
use Corp104\Common\Crypt\Drivers\WebService;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\StreamFactory;

class ACAdapter {

  protected $encrypter;

  protected $endPoint;

  public function __construct() {
    $this->endPoint = env('AC_ENDPOINT');

    $driver = new WebService(
      new Client(),
      new RequestFactory(),
      new StreamFactory(),
      env('AES_ENDPOINT'),
      env('AES_TOKEN')
    );
    $this->encrypter = new Encrypter($driver);
  }

  public function login($email, $passwd) {
    $url = $this->endPoint.'/ac/login';

    // 加密
    $encryptObj = $this->encrypter->encrypt([$email, $passwd]);
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
    $url = $endPoint.'/ac/getLoginInfo';

    $client = new Client();
    $response = $client->request('POST', $url, [
      'headers' => ['Content-Type' => 'application/json'],
      'body' => json_encode([
        'ssoTokenId' => $ssoTokenId,
      ])
    ]);

    return json_decode($response->getBody()->getContents(), true);
  }

  public function getACInfo($pid) {
    // 含解密
    $url = "{$this->endPoint}/ac/getAccount/{$pid}";

    $client = new Client();
    $response = $client->request('GET', $url);

    $obj = json_decode($response->getBody()->getContents(), true);

    $obj['data']['firstName'] = $this->encrypter->decrypt($obj['data']['firstName']);
    $obj['data']['tel'] = $this->encrypter->decrypt($obj['data']['tel']);
    $obj['data']['cellphone'] = $this->encrypter->decrypt($obj['data']['cellphone']);
    $obj['data']['address'] = $this->encrypter->decrypt($obj['data']['address']);
    $obj['data']['identity'] = $this->encrypter->decrypt($obj['data']['identity']);
    for ($i = 0; $i < count($obj['data']['email']); $i++) {
      $obj['data']['email'][$i]['email'] = $this->encrypter->decrypt($obj['data']['email'][$i]['email']);
    }
    return json_encode($obj['data']);
  }

  public function logout($ssoTokenId) {

    return true;
  }
}