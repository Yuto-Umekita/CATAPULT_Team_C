# 🥛 MochiLog（もちログ）  
*by CATAPULT_Team_C*

> **家庭でも企業でも使える在庫管理＋レシピ提案アプリ**  
> Laravel + Tailwind + Alpine.js + Docker で構築されたチーム開発プロジェクト

---

## 📘 概要

**MochiLog（もちログ）** は、在庫管理を「もっと楽しく・スマートに」するWebアプリです。  
食材や備品などの在庫を登録・タグ付けし、在庫状況から作れるレシピを自動提案します。  
家庭モード／企業モードを切り替えて、個人利用からチーム利用まで幅広く対応します。

---

## 🚀 主な機能

| 機能カテゴリ | 内容 |
|---------------|------|
| 🧾 **在庫管理** | アイテム登録・編集・削除。タグや数量、期限管理にも対応。 |
| 🏷️ **タグ機能** | タグの作成・編集・削除。アイテムとの紐付け（多対多対応）。 |
| 🧠 **メモ機能** | 各アイテムに自由なコメントを記録。履歴も確認可能。 |
| 📌 **ピン留め機能** | よく使うアイテムを固定表示。ユーザーごとに管理。 |
| 📥 **CSV入出力** | 在庫データのエクスポート・インポート機能。 |
| 🍳 **レシピ提案** | Spoonacular APIを使用し、在庫食材から作れる料理を自動提案。 |
| ⭐ **ブックマーク機能** | 気に入ったレシピを保存・一覧表示可能。 |
| 🧑‍💼 **プロフィール機能** | 自身・他ユーザーの在庫やブックマークを閲覧。 |
| 🏠 **モード切替機能** | 家庭モード／企業モードでUI・データを分離。 |
| 🌗 **ダークモード対応** | Tailwindベースのライト／ダークテーマ切替。 |
| 📱 **スマホ対応** | Tailwind CSS + Alpine.js によるレスポンシブデザイン。 |

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
| バージョン管理 | GitHub（ブランチ構成：`main` / `develop` / 各featureブランチ） |

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



---

## ⚙️ 環境構築手順

🐳 1️⃣ クローンと移動
```bash
git clone https://github.com/CATAPULT-Team-C/MochiLog.git
cd MochiLog

---

⚙️ 2️⃣ 環境ファイル設定
cp .env.example .env

.env を編集して以下を設定：

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

---

🧰 3️⃣ 依存関係インストール
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

---

🗄️ 4️⃣ Sailコンテナ起動
./vendor/bin/sail up -d

---

🧩 5️⃣ データベース準備
./vendor/bin/sail artisan migrate --seed

---

🧭 6️⃣ 動作確認

http://localhost
 にアクセスしてログイン画面を確認します。

---

 🌿 ブランチ運用ルール
ブランチ名	役割
main	安定版（本番環境相当）
develop	開発統合ブランチ（機能統合・動作確認用）

---

🧪 開発補助コマンド
コマンド	説明
./vendor/bin/sail artisan migrate	マイグレーション実行
./vendor/bin/sail artisan migrate:rollback	直前のマイグレーション取り消し
./vendor/bin/sail artisan tinker	データ操作用コンソール
./vendor/bin/sail npm run dev	フロントエンド開発ビルド
./vendor/bin/sail npm run build	本番ビルド（Vite）
./vendor/bin/sail down	コンテナ停止
./vendor/bin/sail logs -f	ログ確認

---

👥 開発チーム：CATAPULT_Team_C