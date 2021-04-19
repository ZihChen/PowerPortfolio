Power Portfolio 美股績效管理工具
---
![GitHub last commit](https://img.shields.io/github/last-commit/ZihChen/PowerPortfolio)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/navendu-pottekkat/virtual-drums/issues)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

快速瀏覽自訂股票清單的工具

每日收盤時更新最新的價格、技術指標、資產盈虧、以及基本面資訊



## 目錄
- 安裝
- 功能
- 架構
- 發展
- 貢獻

## 安裝
```bash
git clone https://github.com/ZihChen/PowerPortfolio.git
```
載入相依套件
```bash
composer install
```
產生.env檔案
```bash
cp .example.env .env
```
執行資料庫遷移
```bash
php artisan migrate
```
創建加密需要公鑰以及私鑰
```bash
php artisan passport:install
```
產生初始股票標的相關資料
```bash
php artisan init:data --file_path=app/Form/stock_symbols_01.xlsx
```

## 功能

## 架構

## 發展

## 貢獻



