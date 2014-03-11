kintone API SDK for PHP
=======================

Requirements
------------

* PHP >= 5.4
* Composer

Installation
------------

まず Composer をインストールする必要があります. 下記はグローバルインス
トールを行う例です.

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

また, 現時点で Composer は Basic 認証のかかったリポジトリを読むことが
できません. URL内に認証情報を含めれば可能ですが, 認証情報をバージョン
管理下に含めることは避けたいので,以下のようにします.

1. `COMPOSER_HOME/config.json` 内に認証情報を含めたリポジトリ定義を書く
2. 各プロジェクト内の composer.json 内に依存関係を定義する

COMPOSER_HOMEはデフォルトで *nix では `/home/<user>/.composer`, OSXで
は `/Users/<user>/.composer`, Windows では
`C:\Users\<user>\AppData\Roaming\Composer` に設定されています.

具体的な `config.json` の定義例は以下のようになります.

```json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://username:password@bts.cstap.com/git/kintone-sdk-php"
        }
    ]
}
```

以上のように, `config.json` を設定した上で, 各プロジェクトの
`composer.json` に以下のようにして依存関係を追加して下さい.

```json
{
    "name": "you/your-project",
    "require": {
        "cstap/kintone-sdk-php": "dev-master"
    }
}
```

Usage
-----

`examples` ディレクトリを参照して下さい.

