# Chikyu
## インストール
Packagistには登録していないため、composer.jsonに以下のように記述し、composer installを実行してください

```composer.json
{
  "require" : {
    "chikyu/chikyu-sdk": "dev-master"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/chikyuinc/chikyu-sdk-php.git"
    }
  ]
}
```

## SDKを利用する
### テスト段階でのサンプルコード
```test.php
require_once __DIR__ . '/vendor/autoload.php';

use Chikyu\Sdk\Config\ApiConfig;
use Chikyu\Sdk\Resource\Session;
use Chikyu\Sdk\SecureResource;

# 2018/05/14現在、まだ本番環境が未構築であるため、こちらのテスト用の環境名を指定して下さい。
ApiConfig::setMode('devdc');

# セッションの生成
$session = Session::login('token_name', 'login_token', 'login_secret_token');

# APIの呼び出し
$invoker = new SecureResource($session);

print_r($invoker.invoke(
    '/entity/companies/list', 
    data: [items_per_page => 10, page_index => 0]));
```

## 詳細
### class1(APIキーのみで呼び出し可能)
#### APIトークンを生成する
```token.php
require_once __DIR__ . '/vendor/autoload.php';

use Chikyu\Sdk\Resource\Session;
use Chikyu\Sdk\SecureResource;

# 下記のclass2 apiを利用し、予めトークンを生成しておく。
$session = Session::login('token_name', 'login_token', 'login_secret_token');
$invoker = new SecureResource(session);

# 引数にキー名称と関連付けるロールのIDを指定する。
# 関連付けるロールは、予め作成しておく。
$key = $invoker.invoke('/system/api_auth_key/create', {
            'api_key_name': 'key_name',
            'role_id': 1234,
            'allowed_hosts': []
        });

# 生成したキーをファイルなどに保存しておく。
print($key);

```

#### 呼び出しを実行する
```invoke_public.php
require_once __DIR__ . '/vendor/autoload.php';

use Chikyu\Sdk\PublicResource;

$invoker = PublicResource('api_key', 'auth_key');

# 第一引数=APIのパスを指定(詳細については、ページ最下部のリンクを参照)
# 第二引数=リクエスト用JSONの「data」フィールド内の項目を指定
$res = $invoker.invoke('/some/api', ['field1' => 'data']);

# レスポンス用JSONの「data」フィールド内の項目が返ってくる。
# APIの実行に失敗(エラーが発生 or has_errorがtrue)の場合は例外が発生する。
print_r($res);
```

### class2(APIトークンからセッションを生成)
#### APIトークンを生成する
```create_token.php
require_once __DIR__ . '/vendor/autoload.php';

use Chikyu\Sdk\Resource\Token;

# ・トークン名称(任意)
# ・ちきゅうのログイン用メールアドレス
# ・ちきゅうのログイン用パスワード
# ・トークンの有効期限(デフォルトでは24時間 - 秒で指定)
$token = Token::create('token_name', 'emaill', 'password', 86400);

# トークン情報をファイルなどに保存しておく
print(token);
```

#### ログインしてセッションを生成する
```create_session.php
require_once __DIR__ . '/vendor/autoload.php';
use Chikyu\Sdk\Resource\Session;

# セッションを生成する
$session = $session = Session::login('token_name', 'login_token', 'login_secret_token');

# セッション情報のオブジェクトをローカル変数などとして保存し、呼び出しに利用する
print_r($session);

# セッション情報をテキストに変換する
$text = strval($session);

# セッション情報をテキストから復元する
$session = Session::fromStr($text);

# 処理対象の組織を変更する
$session.changeOrgan(1234); # 変更対象の組織IDを指定する

# ログアウトする
$session.logout();
```


#### 呼び出しを実行する
```invoke_secure.php
require_once __DIR__ . '/vendor/autoload.php';
use Chikyu\Sdk\Resource\Session;
use Chikyu\Sdk\SecureResource;

# 上で生成したセッション情報を元に、API呼び出し用のリソースを生成する
$invoker = new SecureResource($session)

# 第一引数=APIのパスを指定(詳細については、ページ最下部のリンクを参照)
# 第二引数=リクエスト用JSONの「data」フィールド内の項目を指定
$res = $invoker.invoke('/some/api', ['field1' => 'data'])

# レスポンス用JSONの「data」フィールド内の項目が返ってくる。
# APIの実行に失敗(エラーが発生 or has_errorがtrue)の場合は例外が発生する。
print_r($res)
```


## APIリスト
こちらをご覧ください。

http://dev-docs.chikyu.mobi/

