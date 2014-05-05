Runa-CCA
========

A Call Center Application for Twilio!
Runa-CCA stands for "Rutoru's Network Laboratory - Call Center Application". This is the dedicated version for Twilio.  

Runa-CCAは、[Twilio](http://twilio.kddi-web.com)を使った、やや本格的なコールセンターシステムです。PHPマイクロフレームワークSlimを使って、MVCを意識した作りになっています。以前作成した[Twilio-MiniCC](https://github.com/rutoru/Twilio-MiniCC)を大幅に作り替えました。

[Twilio](http://twilio.kddi-web.com)調査中というのと、プログラミング勉強中というのとで、いろいろといまいちです。バージョンアップしていきます。

概要
------
### 機能概要 ###
[Twilio](http://twilio.kddi-web.com) を使ったコールセンターシステムで、以下の機能を実装しています。

+ Webクライアント
    + Twilioソフトフォン
        + ログイン認証（SystemAdmin,Supervisor,Operator権限）
        + 発信・着信
        + キューへの発信（オペレータの所属するキューの一覧より）
    + 管理機能
        + ログイン認証（SystemAdmin,Supervisor,Operator権限）
        + オペレータ管理（追加・一覧表示・削除・変更） 
        + キュー管理（追加・一覧表示・削除・変更） 
        + パスワード変更
+ コールフロー
    + 音声自動応答（IVR）
    + 音声ガイダンス
    + キューイング（待ち順番アナウンス付き）
    + ヒストリカルレポート収集

### Webクライアント（Twilioソフトフォン） ###
[flickrに画面イメージを上げておきました](https://www.flickr.com/x/t/0093009/photos/40853659@N06/sets/72157644527599345/)。

### コールフロー ###
#### 電話をかけるお客様から見た動き ####
お客様がある電話番号に電話をかけると、お問い合わせは1を、最新の製品情報をお聞きになりたい場合は2を押すように求められます。1を押したら、オペレータに接続します。オペレータ不在時はキューに入ります。お客様の待ちの順番をアナウンスした後、保留音が流れます。オペレータが準備でき次第、オペレータに接続します。2を押したら、製品情報ガイダンスが流れます。1,2以外が押されたら再度入力が求められます。10秒待っても何の入力も無い場合、あるいはお客様がPB信号を送ることができない場合は、オペレータにつなぎます。

#### ヒストリカルレポート収集 ####
以下の３カ所でヒストリカルレポートを収集します。
MySQLのテーブルに格納されます。レポートの参照は、MySQLの色々なツールで可能だと思います。データベース設計が十分ではありません。

+ お客様側キュー（Enqueue）から出た時 … お客様がキューから出る際の情報です。QueueResultを取得できるので、キューに入った後にお客様が電話を切った（放棄呼）の情報を取得することができます。コールセンターで放棄呼がどれだけ発生しているのかを把握することは極めて重要になります。データは`enqueue_data`テーブルに格納されます。
+ オペレータ側キュー（Queue）から出た時 … オペレータが応答した際の情報です。データは`queue_data`テーブルに格納します。
+ 通話終了時 ... 「StatusCallback」です。StatusCallbackは通話終了後に非同期で発生するリクエストで、通話に関する情報を収集しデータベースに書き込みを行うことができます。デフォルトはオフ。Twilioの設定画面から「電話番号」をクリックした後、 「Optional Voice Settings」をクリック。表示される「Status Callback URL」に作成したプログラムを設定する必要があります。データは`statuscallback_data`テーブルに格納されます。

インストール
------
今後記載。
   
ライセンス
----------
Copyright &copy; 2014 rutoru
Licensed under [MIT license][MIT].    
https://github.com/rutoru/Twilio-MiniCC/blob/master/LICENSE
 
[MIT]: http://www.opensource.org/licenses/mit-license.php
