<?php

namespace App\Http\Controllers;

use App\Http\Service\ACAdapter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class FirstController extends Controller {
  protected $acAdapter;

  public function __construct() {
    $this->acAdapter = new ACAdapter();
  }

  public function login(Request $request) {
    $resp = $this->acAdapter->login($request->email, $request->passwd);

    if ($resp['success'] == 'true') {
      setCookie("ssoTokenId", $resp['data']['ssoTokenId']);
      echo '<script>alert("登入成功！")</script>';
      return redirect('/watch');
    } else {
      echo '<script>alert("登入失敗！")</script>';
      return redirect('/login');
    }
  }

  public function watch() {
    if (!@$_COOKIE['ssoTokenId']) {
      // 沒有登入cookie記錄，重新登入
      echo '<script>alert("尚未登入！")</script>';
      return redirect('/login');
    } else {
      // 有登入cookie記錄，確認是否仍有效
      $resp = $this->acAdapter->checkLogin($_COOKIE['ssoTokenId']);

      if ($resp['success'] == 'true') {
        $pid = $resp['data']['Pid'];

        return $this->acAdapter->getACInfo($pid);
      } else {
        echo '<script>alert("登入已過期！")</script>';
        return redirect('/login');
      }
    }
  }
}
