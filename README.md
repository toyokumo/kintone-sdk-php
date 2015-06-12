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
