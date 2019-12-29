# Illustrator
イラストを投稿し、評価することができる自作webサービスです。
![スクリーンショット-2019-12-11-22 48 23](https://user-images.githubusercontent.com/48384384/71505061-34ec7e00-28bf-11ea-9dac-12ed6f175bff.png)

# Dependency
HTML, CSS, jQuery, PHP(Version 7.3.7), MySQL

# Setup
MAMPを導入し、ローカル環境にて各ファイルをhtdocsファイルに配置すれば動作可能です。(要MacOS)

# Usage
**・新規会員登録機能**

![1 新規会員登録](https://user-images.githubusercontent.com/48384384/71536929-767d3780-2958-11ea-8542-02eac5b50476.gif)

名前、email、パスワード、パスワードの再入力欄を入力し、DBに登録します。

バリデーションチェック項目
・各項目の未入力項目  
・名前の最大文字数(20文字)  
・emailの最大文字数(255文字)  
・DBに登録されているemailの重複  
・パスワードが半角英数字か  
・パスワードの最大文字数(255文字)  
・パスワードの最小文字数(6文字)  
・パスワード再入力の最大文字数(255文字)  
・パスワード再入力の最小文字数(6文字)  
・パスワードとパスワード再入力が同値か  

**・プロフィール情報変更機能**

![2 プロフィール変更](https://user-images.githubusercontent.com/48384384/71536931-7b41eb80-2958-11ea-8005-03cb717ee07d.gif)

DBに登録されているプロフィール情報を変更します。

バリデーションチェック項目  
・名前の最大文字数(20文字)  
・居住地の最大文字数(255文字)  
・職業の最大文字数(255文字)  
・emailの最大文字数(255文字)  
・DBに登録されているemailの重複
・Emailの形式
・Emailの未入力
・コメントの最大文字数(255文字)  

**・パスワード変更機能**

![3 パスワード変更](https://user-images.githubusercontent.com/48384384/71536933-7ed57280-2958-11ea-914b-7ed618631229.gif)

DBに登録されているパスワード情報を変更します。

バリデーションチェック項目

・パスワードが半角英数字か  
・パスワードの最大文字数(255文字)  
・パスワードの最小文字数(6文字)  
・パスワード再入力の最大文字数(255文字)  
・パスワード再入力の最小文字数(6文字)  
・パスワードとパスワード再入力が同値か  

# Authors
Takayoshi
