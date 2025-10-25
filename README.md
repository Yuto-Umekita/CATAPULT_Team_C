# 🥛 もちログ  
*by CATAPULT_Team_C*

> **家庭でも企業でも使える在庫管理＋レシピ提案アプリ**  
> Laravel + Tailwind + Alpine.js + Docker で構築されたチーム開発プロジェクト

---

## 📘 概要

**MochiLog（もちログ）** は、在庫管理を「もっと楽しく・スマートに」するWebアプリです。  
食材や備品などの在庫を登録・タグ付けし、在庫状況から作れるレシピを自動提案します。  
家庭モード／企業モードを切り替えて、そこからさらにグループを作成することができます。(※現在は個人用のみ。今後グループメンバー追加機能実装予定)

---

## 🚀 主な機能

| 機能カテゴリ | 内容 |
|---------------|------|
| 🧾 **在庫管理** | アイテム登録・編集・削除。タグや数量、期限管理にも対応。 |
| 🏷️ **タグ機能** | タグの作成・編集・削除。アイテムとの紐付け（多対多対応）。 |
| 🗨️ **メモ機能** | 各アイテムに自由なコメントを記録。履歴も確認可能。 |
| 📌 **ピン留め機能** | よく使うアイテムを固定表示。ユーザーごとに管理。 |
| 📥 **CSV入出力** | 在庫データのエクスポート・インポート機能。 |
| 🍳 **レシピ提案** | Spoonacular APIを使用し、在庫食材から作れる料理を自動提案。 |
| ⭐ **ブックマーク機能** | 気に入ったレシピを保存・一覧表示可能。 |
| 🧑‍💼 **プロフィール機能** | 自身のプロフィールを閲覧。 |
| 🏠 **モード切替機能** | 家庭モード／企業モードでUI・データを分離。 |
| 🌗 **ダークモード対応** | Tailwindベースのライト／ダークテーマ切替。 |

---

## 🧩 技術構成

| 項目 | 内容 |
|------|------|
| フレームワーク | Laravel 10.x |
| PHPバージョン | PHP 8.2 |
| フロントエンド | Blade / Tailwind CSS / Alpine.js |
| DB | MySQL 8.x |
| コンテナ環境 | Laravel Sail（Docker Compose） |
| API | [Spoonacular API](https://spoonacular.com/food-api)（レシピ提案機能） |
　　　　 [DeepL API](https://www.deepl.com/ja/translator) (レシピ翻訳)
| バージョン管理 | GitHub（ブランチ構成：`main` / `develop`） |
---

## 🧱 ディレクトリ構成

```plaintext
MochiLog/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   ├── Models/
│   └── Providers/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── routes/
│   └── web.php
├── docker-compose.yml
├── .env.example
└── README.md
```

## ⚙️ 環境構築手順
本アプリケーションは、Docker環境での実行を推奨する。
###### ❗前提条件
・Git
・Docker Desktopがインストール・起動済みであること

###### 🐳 1️⃣ クローンと移動
```bash
git clone https://github.com/Yuto-Umekita/CATAPULT_Team_C.git
cd CATAPULT_Team_C
```

###### ⚙️ 2️⃣ 環境ファイル設定
.env.exampleをコピーし、.envファイルを作成
``` bash
cp .env.example .env
```
.env を編集して以下の項目を設定
```bash
APP_NAME="MochiLog"
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

# Spoonacular APIキー
SPOONACULAR_API_KEY=your_api_key_here
```

###### 🧰 3️⃣ 依存関係インストール
```bash
#依存パッケージのインストール (PHP)
./vendor/bin/sail composer install 

#フロントエンドの依存パッケージをインストール (Node.js)
./vendor/bin/sail npm install

#フロントエンドアセットのビルド（開発用）
./vendor/bin/sail npm run dev
```

###### 🗄️ 4️⃣ Sailコンテナ起動
こちらはバックグラウンドで実行する
```bash
./vendor/bin/sail up -d
```

###### 🧩 5️⃣ データベース準備
マイグレーション（テーブル作成）と、アプリケーションキー、初期データの投入を行います。

```bash
#アプリケーションキーの生成（.envにAPP_KEYが設定される）
./vendor/bin/sail artisan key:generate

#マイグレーション実行（テーブル作成）と初期シードデータ投入
./vendor/bin/sail artisan migrate --seed
```

###### 🧭 6️⃣ 動作確認

http://localhost
 にアクセスしてログイン画面を確認します。

## 🌿 ブランチ運用ルール
| ブランチ名 | 役割 |
|------|------|
| main | 安定版（本番環境相当） |
| develop | 開発統合ブランチ（機能統合・動作確認用） |

## 🧪 開発補助コマンド

```bash
# Dockerコンテナの実行開始
./vendor/bin/sail up -d

# 実行中のDockerコンテナを停止
./vendor/bin/sail down

# マイグレーション実行
./vendor/bin/sail artisan migrate

# 直前のマイグレーション取り消し
./vendor/bin/sail artisan migrate:rollback

# データ操作用コンソールを開く
./vendor/bin/sail artisan tinker

# フロントエンド開発ビルドの実行開始
./vendor/bin/sail npm run dev

# 本番環境向けフロントエンドアセットをビルド（Vite）
./vendor/bin/sail npm run build

# PHPUnit/Pestによるテストを実行
./vendor/bin/sail artisan test

# ログ確認
./vendor/bin/sail logs -f
```

###### 👥 開発チーム：CATAPULT_Team_C
