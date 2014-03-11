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
管理下に含めることは避けたいので, SSH で接続するようにします.

通常, リポジトリのURLは`https://bts.cstap.com/git/kintone-sdk-php`にな
りますが, ここでは
`cstap@bts.cstap.com:/var/opt/alminium/git/kintone-sdk-php`を用いるよ
うにして下さい.

したがって, 本SDKを用いるプロジェクトの `composer.json` は例えば以下の
ようになります.

```json
{
    "name": "you/your-project",
    "repositories": [
        {
            "type": "vcs",
            "url": "cstap@bts.cstap.com:/var/opt/alminium/git/kintone-sdk-php"
        }
    ],
    "require": {
        "cstap/kintone-sdk-php": "dev-master"
    }
}
```

正しく `composer.json` を記述した上で, プロジェクトディレクトリ内で
`composer update` を実行すればSDK及び依存ライブラリがインストールされます.

なお, update 中に何度かパスワードプロンプトが表示されます. 毎回入力し
ても良いですが, 鍵の設定をしておいたほうが良いでしょう.

Usage
-----

`examples` ディレクトリを参照して下さい.

Contributing
------------

まず, プロジェクトを clone し, 依存ライブラリをインストールしてください.

```bash
git clone https://bts.cstap.com/git/kintone-sdk-php
cd kintone-sdk-php
composer install
```

後は `examples` ディレクトリ内を参照して貰えればわかると思います.

なお, 厳密である必要はありませんが, コーディング規約はPSR-2準拠としま
す(examplesを除く, php-cs-fixer等の使用を推奨).

TODO
----

* kintone API対応の充実
* エラーハンドリング
* 高水準APIの実装
* Symfony2連携
