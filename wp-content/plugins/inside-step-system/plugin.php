<?php
/*
Plugin Name: Inside Step System
Description: Inside Step SystemはSEO内部対策を行います。
Version: 2.7
Plugin URI: http://www.zeromedical.tv/
Author: ZERO MEDICAL
Author URI: http://www.zeromedical.tv/
License: MIT
*/
class InsideStepSystem {

  // バージョンデータ
  public $version_data = '2.0';
  // 共通項目ID
  public $commondata_id = -1;
  // カテゴリーデータフラグ
  public $catflg_false = 0;
  public $catflg_true = 1;
  // 自動下書き
  public $text_draft = "自動下書き";

  //-----------------------------------------------------
  // 【 constructor 】
  // param   ： -
  // return  ： -
  // disposal： 初期処理
  //-----------------------------------------------------
  function __construct() {
    // CSSファイル読み込み
    add_action( 'admin_enqueue_scripts', array( $this, 'readStyles' ) );
    // プラグイン有効化時テーブル作成
    register_activation_hook( __FILE__, array( $this, 'makeTable' ) );
    // 管理画面にメニュー追加
    add_action( 'admin_menu', array( $this, 'addIss' ) );
    // 固定ページにカスタムフィールド追加
    add_action( 'admin_menu', array( $this, 'addFieldPage' ) );
    // 投稿にカスタムフィールド追加
    add_action( 'admin_menu', array( $this, 'addFieldPost' ) );
    // カスタム投稿にカスタムフィールド追加
    add_action( 'admin_menu', array( $this, 'addFieldCustomPost' ) );
    // 登録・更新
    add_action( 'save_post', array( $this, 'saveFieldData' ) );
    // タイトルショートコード設定
    add_shortcode( 'ISS-ttl', array( $this, 'getIssTitle' ) );
    // キーワードショートコード設定
    add_shortcode( 'ISS-key', array( $this, 'getIssKeyword' ) );
    // ディスクリプションショートコード設定
    add_shortcode( 'ISS-desc', array( $this, 'getIssDescription' ) );
    // h1ショートコード設定
    add_shortcode( 'ISS-h1', array( $this, 'getIssH1' ) );
  }

  //-----------------------------------------------------
  // 【 readStyles 】
  // param   ： -
  // return  ： -
  // disposal： CSSファイル読み込み
  //-----------------------------------------------------
  function readStyles() {
    wp_enqueue_style( 'style-iss', plugins_url( 'inside-step-system/css/style.css' ), array(), 'false', 'all' );
  }

  //-----------------------------------------------------
  // 【 makeTable 】
  // param   ： -
  // return  ： -
  // disposal： プラグイン有効化時テーブル作成
  //-----------------------------------------------------
  function makeTable() {
    // 共通項目設定
    $data_common_ttl = "初期共通タイトル";
    $data_common_key = "初期共通キーワード";
    $data_common_desc = "初期共通ディスクリプション";
    $data_common_h1 = "初期共通h1";
    // 下書き文字列
    $data_draft = "自動下書き";
    // オプション名
    $optionname ="dbversion_iss";
    // 旧各項目
    $olddatas = '';
    $data_old_id = '';
    $data_old_ttl = '';
    $data_old_key = '';
    $data_old_desc = '';
    $data_old_h1 = '';
    // テーブル名取得
    $tablename = $this->getTablename();
    // 旧プラグインのテーブル存在フラグ
    $flg_table = false;
    // optionsテーブルから現在のプラグインバージョン取得
    $nowversion = get_option( $optionname );
    // 旧プラグインのテーブルが存在するか判定
    if( "" != $nowversion && $this->version_data != $nowversion ) {
      // 旧各項目取得
      $olddatas = $this->selectAllRecords( $tablename );
      // 旧プラグインのテーブル存在フラグ設定
      $flg_table = true;
      // 旧プラグインのテーブル削除
      $this->dropTable( $tablename );
    }
    // プラグインバージョンが一致しなければテーブル作成
    if( $this->version_data != $nowversion ) {
      // SQL文作成
      $sql = 'CREATE TABLE IF NOT EXISTS '.$tablename.' (
        post_id bigint NOT NULL,
        cat_flg int NOT NULL,
        txt_title varchar(400) NOT NULL,
        txt_keyword varchar(400) NOT NULL,
        txt_description varchar(400) NOT NULL,
        txt_h1 varchar(400) NOT NULL,
        PRIMARY KEY (post_id, cat_flg)
      ) DEFAULT CHARSET=utf8;';
      // upgrade.php読み込み
      require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
      // SQL文実行
      dbDelta( $sql );
      // optionsテーブルにプラグインバージョン保存
      update_option( $optionname, $this->version_data );
      // 旧プラグインのテーブルが存在するか判定
      if( $flg_table ) {
        // レコード分繰り返し
        foreach( $olddatas as $olddata ) {
          // 旧各項目取得
          $data_old_id = $olddata->post_id;
          $data_old_ttl = $olddata->txt_title;
          $data_old_key = $olddata->txt_keyword;
          $data_old_desc = $olddata->txt_description;
          $data_old_h1 = $olddata->txt_h1;
          // 自動下書きかどうか判定
          if( !strstr( $data_old_ttl, $data_draft ) ) {
            // データ登録
            $this->insertTable( $tablename, $data_old_id, $this->catflg_false, $data_old_ttl, $data_old_key, $data_old_desc, $data_old_h1 );
          }
        }
        // データが存在するか判定
        if( $this->selectPageId( $tablename, $this->commondata_id, $this->catflg_false ) == 0 ) {
          // 共通データが存在しない場合、共通項目データ登録
          $this->insertTable( $tablename, $this->commondata_id, $this->catflg_false, $data_common_ttl, $data_common_key, $data_common_desc, $data_common_h1 );
        }
      } else {
        // 初期項目設定
        $this->insertTable( $tablename, $this->commondata_id, $this->catflg_false, $data_common_ttl, $data_common_key, $data_common_desc, $data_common_h1 );
      }
      // 既存のデータ登録
      $this->insertExistdata( $tablename, $data_common_ttl, $data_common_key, $data_common_desc, $data_common_h1, $data_draft );
    }
  }

    //-----------------------------------------------------
    // 【 insertExistdata 】
    // param1  ： テーブル名
    // param2  ： 共通項目タイトル
    // param3  ： 共通項目キーワード
    // param4  ： 共通項目ディスクリプション
    // param5  ： 共通項目h1
    // param6  ： 共通項目h1
    // return  ： -
    // disposal： 既存のデータ登録
    //-----------------------------------------------------
    function insertExistdata( $tablename, $data_common_ttl, $data_common_key, $data_common_desc, $data_common_h1, $data_draft ) {
      // アーカイブ用データのID設定
      $archiveid = -2;
      // postsテーブル名取得
      $poststablename = $this->getPostsTablename();
      // 投稿種類全て取得
      $types = $this->selectPostTypeAll( $poststablename );
      // 投稿種類分繰り返し
      foreach( $types as $type ) {
        // 投稿のIDとタイトル取得
        $insertdatas = $this->selectIdTitle( $poststablename, $type->post_type );
        // データ登録
        foreach( $insertdatas as $insertdata ) {
          // データが存在するか判定
          if( $this->selectPageId( $tablename, $insertdata->ID, $this->catflg_false ) == 0 ) {
            // 追加処理
            $this->insertTable( $tablename, $insertdata->ID, $this->catflg_false, $insertdata->post_title.'｜'.$data_common_ttl, $insertdata->post_title.','.$data_common_key, $insertdata->post_title.'、'.$data_common_desc, $insertdata->post_title.'｜'.$data_common_h1 );
          }
        }
        // 投稿種類が投稿以外か判定
        if( $type->post_type != 'page' && $type->post_type != 'mw-wp-form' ) {
          // 投稿以外ではない場合
          // 投稿のラベル名を取得
          $postname = get_post_type_object( $type->post_type )->labels->name;
          // アーカイブ用のデータ追加処理
          $this->insertTable( $tablename, $archiveid, $this->catflg_false, $postname.'｜'.$data_common_ttl, $postname.','.$data_common_key, $postname.'、'.$data_common_desc, $postname.'｜'.$data_common_h1 );
          // optionsテーブルにIDと投稿タイプ保存
          update_option( $type->post_type, $archiveid );
          $archiveid = $archiveid - 1;
        }
      }
    }

  //-----------------------------------------------------
  // 【 addIss 】
  // param   ： -
  // return  ： -
  // disposal： 管理画面にメニュー追加
  //-----------------------------------------------------
  function addIss() {
    add_menu_page( 'InsideStepSystem', 'InsideStepSystem', 8, 'index2.php', array( $this, 'exeIss' ) );
  }

    //-----------------------------------------------------
    // 【 exeIss 】
    // param   ： -
    // return  ： -
    // disposal： データ登録、更新、html出力
    //-----------------------------------------------------
    function exeIss() {
      // 共通データ更新
      $this->updateCommondata();
      // カテゴリーデータ更新・登録
      $this->makeCatdata();
      // アーカイブデータ更新
      $this->makeArchivedata();
      // html出力
      $this->outputHtml();
    }

      //-----------------------------------------------------
      // 【 updateCommondata 】
      // param   ： -
      // return  ： -
      // disposal： 共通データ更新
      //-----------------------------------------------------
      function updateCommondata() {
        // 入力された共通データ取得
        $inputdatas = $this->getInputdatas( $this->catflg_false, 0 );
        // 旧共通項目取得
        $olddatas = $this->selectCommondatas( $this->getTablename() );
        // 共通項目更新
        $this->updateEachTable( $this->getTablename(), $inputdatas[0], $inputdatas[1], $inputdatas[2], $inputdatas[3], $this->commondata_id, $this->catflg_false );
        // 全レコード共通部分更新
        $this->updateCommonAreadata( $olddatas, $inputdatas );
      }

        //-----------------------------------------------------
        // 【 updateCommonAreadata 】
        // param1  ： 旧共通項目データ
        // param2  ： 新共通項目データ
        // return  ： -
        // disposal： 全レコード共通部分更新
        //-----------------------------------------------------
        function updateCommonAreadata( $olddatas, $newdatas ) {
          // 旧各項目
          $data_old_ttl = '';
          $data_old_key = '';
          $data_old_desc = '';
          $data_old_h1 = '';
          // 旧各項目取得
          foreach( $olddatas as $olddata ) {
            $data_old_ttl = $olddata->txt_title;
            $data_old_key = $olddata->txt_keyword;
            $data_old_desc = $olddata->txt_description;
            $data_old_h1 = $olddata->txt_h1;
          }
          // 更新用各項目
          $data_upd_ttl = '';
          $data_upd_key = '';
          $data_upd_desc = '';
          $data_upd_h1 = '';
          // 共通項目以外の全レコード取得
          $allrecorddatas = $this->selectAllRecordsExceptCommon( $this->getTablename() );
          // 全レコード分繰り返し
          foreach( $allrecorddatas as $allrecorddata ) {
            // タイトルに旧共通部分が存在するか判定
            if( strstr( $allrecorddata->txt_title, $data_old_ttl ) && $newdatas[0] != "" ) {
              // 旧共通部分を置換したタイトル設定
              $data_upd_ttl = str_replace( $data_old_ttl, $newdatas[0], $allrecorddata->txt_title );
            } else {
              // タイトル設定
              $data_upd_ttl = $allrecorddata->txt_title;
            }
            // キーワードに旧共通部分が存在するか判定
            if( strstr( $allrecorddata->txt_keyword, $data_old_key ) && $newdatas[1] != "" ) {
              // 旧共通部分を置換したキーワード設定
              $data_upd_key = str_replace( $data_old_key, $newdatas[1], $allrecorddata->txt_keyword );
            } else {
              // キーワード設定
              $data_upd_key = $allrecorddata->txt_keyword;
            }
            // ディスクリプションに旧共通部分が存在するか判定
            if( strstr( $allrecorddata->txt_description, $data_old_desc ) && $newdatas[2] != "" ) {
              // 旧共通部分を置換したディスクリプション設定
              $data_upd_desc = str_replace( $data_old_desc, $newdatas[2], $allrecorddata->txt_description );
            } else {
              // ディスクリプション設定
              $data_upd_desc = $allrecorddata->txt_description;
            }
            // h1に旧共通部分が存在するか判定
            if( strstr( $allrecorddata->txt_h1, $data_old_h1 ) && $newdatas[3] != "" ) {
              // 旧共通部分を置換したh1設定
              $data_upd_h1 = str_replace( $data_old_h1, $newdatas[3], $allrecorddata->txt_h1 );
            } else {
              // h1設定
              $data_upd_h1 = $allrecorddata->txt_h1;
            }
            // レコード更新
            if( $allrecorddata->cat_flg == $this->catflg_false ) {
              $this->updateTable( $this->getTablename(), $data_upd_ttl, $data_upd_key, $data_upd_desc, $data_upd_h1, $allrecorddata->post_id, $this->catflg_false );
            } else {
              $this->updateTable( $this->getTablename(), $data_upd_ttl, $data_upd_key, $data_upd_desc, $data_upd_h1, $allrecorddata->post_id, $this->catflg_true );
            }
          }
        }

      //-----------------------------------------------------
      // 【 makeCatdata 】
      // param   ： -
      // return  ： -
      // disposal： カテゴリーデータ更新・登録
      //-----------------------------------------------------
      function makeCatdata() {
        // 投稿、カスタム投稿のIDとカテゴリー名取得
        $data_post_id_name = $this->selectCategoryname( $this->getTermsTablename() );
        // テーブル名取得
        $tablename = $this->getTablename();
        // 共通項目取得
        $commondatas = $this->selectCommondatas( $tablename );
        // 各項目
        $data_common_ttl = '';
        $data_common_key = '';
        $data_common_desc = '';
        $data_common_h1 = '';
        // 各項目取得
        foreach( $commondatas as $commondata ) {
          $data_common_ttl = $commondata->txt_title;
          $data_common_key = $commondata->txt_keyword;
          $data_common_desc = $commondata->txt_description;
          $data_common_h1 = $commondata->txt_h1;
        }
        // カテゴリーの数分繰り返し
        foreach( $data_post_id_name as $data ) {
          // カテゴリーが登録されているか判定
          if( $this->selectPageId( $tablename, $data->term_id, $this->catflg_true ) != 0 ) {
            // 入力されたデータ取得
            $inputdatas = $this->getInputdatas( $this->catflg_true, $data->term_id );
            // 更新処理
            $this->updateEachTable( $tablename, $inputdatas[0], $inputdatas[1], $inputdatas[2], $inputdatas[3], $data->term_id, $this->catflg_true );
          } else {
            // 自動下書きかどうか判定
            if( $data->name != $this->text_draft ) {
              // 自動下書きでない場合
              // 追加処理
              $this->insertTable( $tablename, $data->term_id, $this->catflg_true, $data->name.'｜'.$data_common_ttl, $data->name.','.$data_common_key, $data->name.'、'.$data_common_desc, $data->name.'｜'.$data_common_h1 );
            }
          }
        }
      }

      //-----------------------------------------------------
      // 【 makeArchivedata 】
      // param   ： -
      // return  ： -
      // disposal： アーカイブデータ更新
      //-----------------------------------------------------
      function makeArchivedata() {
        // アーカイブ用データのID設定
        $archiveid = -3;
        // テーブル名取得
        $tablename = $this->getTablename();
        // postsテーブル名取得
        $poststablename = $this->getPostsTablename();
        // optionsテーブル名取得
        $optionstablename = $this->getOptionsTablename();
        // 投稿種類全て取得
        $types = $this->selectPostTypeAll( $poststablename );
        // 投稿種類分繰り返し
        foreach( $types as $type ) {
          // 投稿種類が投稿以外か判定
          if( $type->post_type != 'page' && $type->post_type != 'mw-wp-form' && $type->post_type != 'post' ) {
            // 投稿以外ではない場合
            // 共通項目取得
            $commondatas = $this->selectCommondatas( $tablename );
            // 各項目
            $data_common_ttl = '';
            $data_common_key = '';
            $data_common_desc = '';
            $data_common_h1 = '';
            // 各項目取得
            foreach( $commondatas as $commondata ) {
              $data_common_ttl = $commondata->txt_title;
              $data_common_key = $commondata->txt_keyword;
              $data_common_desc = $commondata->txt_description;
              $data_common_h1 = $commondata->txt_h1;
            }
            // 投稿のラベル名を取得
            $postname = get_post_type_object( $type->post_type )->labels->name;
            // opionsテーブルに投稿名が存在するか判定
            if( !$this->getOptionsTablePostName( $optionstablename, $postname ) ) {
              // アーカイブ用のデータ追加処理
              $this->insertTable( $tablename, $archiveid, $this->catflg_false, $postname.'｜'.$data_common_ttl, $postname.','.$data_common_key, $postname.'、'.$data_common_desc, $postname.'｜'.$data_common_h1 );
              // optionsテーブルにIDと投稿タイプ保存
              update_option( $type->post_type, $archiveid );
              $archiveid = $archiveid - 1;
            }
          }
        }
        // アーカイブデータが登録されているか判定
        if( $this->selectArchiveId( $tablename ) != 0 ) {
          // アーカイブデータ取得
          $archivedatas = $this->selectArchivedatas( $tablename );
          // アーカイブデータ分繰り返し
          foreach( $archivedatas as $archivedata ) {
            // 入力されたデータ取得
            $inputdatas = $this->getInputdatas( 2, $archivedata->post_id );
            // 更新処理
            $this->updateEachTable( $tablename, $inputdatas[0], $inputdatas[1], $inputdatas[2], $inputdatas[3], $archivedata->post_id, $this->catflg_false );
          }
        } else {
          // アーカイブ用データのID設定
          $archiveid = -2;
          // 共通項目取得
          $commondatas = $this->selectCommondatas( $tablename );
          // 各項目
          $data_common_ttl = '';
          $data_common_key = '';
          $data_common_desc = '';
          $data_common_h1 = '';
          // 各項目取得
          foreach( $commondatas as $commondata ) {
            $data_common_ttl = $commondata->txt_title;
            $data_common_key = $commondata->txt_keyword;
            $data_common_desc = $commondata->txt_description;
            $data_common_h1 = $commondata->txt_h1;
          }
          // アーカイブ用データのID設定
          $archiveid = -2;
          // 投稿種類全て取得
          $types = $this->selectPostTypeAll( $this->getPostsTablename() );
          // 投稿種類分繰り返し
          foreach( $types as $type ) {
            // 投稿種類が投稿以外か判定
            if( $type->post_type != 'page' && $type->post_type != 'mw-wp-form' ) {
              // 投稿以外ではない場合
              // 投稿のラベル名を取得
              $postname = get_post_type_object( $type->post_type )->labels->name;
              // アーカイブ用のデータ追加処理
              $this->insertTable( $tablename, $archiveid, $this->catflg_false, $postname.'｜'.$data_common_ttl, $postname.','.$data_common_key, $postname.'、'.$data_common_desc, $postname.'｜'.$data_common_h1 );
              // optionsテーブルにIDと投稿タイプ保存
              update_option( $type->post_type, $archiveid );
              $archiveid = $archiveid - 1;
            }
          }
        }
      }

        //-----------------------------------------------------
        // 【 getInputdatas 】
        // param1  ： データフラグ
        // param2  ： ID
        // return  ： 入力された各データ
        // disposal： 入力された各データ取得
        //-----------------------------------------------------
        function getInputdatas( $allcatflg, $id ) {
          // 各データ
          $data_ttl = '';
          $data_key = '';
          $data_desc = '';
          $data_h1 = '';
          // 共通データか判定
          if( $allcatflg == 0 ) {
            // タイトル設定
            if( isset( $_POST[ 'allttl' ] ) && $_POST[ 'allttl' ] != '' ) {
              $data_ttl = stripslashes( $_POST[ 'allttl' ] );
            }
            // キーワード設定
            if( isset( $_POST[ 'allkey' ] ) && $_POST[ 'allkey' ] != '' ) {
              $data_key = stripslashes( $_POST[ 'allkey' ] );
            }
            // ディスクリプション設定
            if( isset( $_POST[ 'alldesc' ] ) && $_POST[ 'alldesc' ] != '' ) {
              $data_desc = stripslashes( $_POST[ 'alldesc' ] );
            }
            // h1設定
            if( isset( $_POST[ 'allh1' ] ) && $_POST[ 'allh1' ] != '' ) {
              $data_h1 = stripslashes( $_POST[ 'allh1' ] );
            }
          // アーカイブデータか判定
          } else if( $allcatflg == 2 ) {
            // タイトル設定
            if( isset( $_POST[ 'archivettl'.$id ] ) && $_POST[ 'archivettl'.$id ] != '' ) {
              $data_ttl = stripslashes( $_POST[ 'archivettl'.$id ] );
            }
            // キーワード設定
            if( isset( $_POST[ 'archivekey'.$id ] ) && $_POST[ 'archivekey'.$id ] != '' ) {
              $data_key = stripslashes( $_POST[ 'archivekey'.$id ] );
            }
            // ディスクリプション設定
            if( isset( $_POST[ 'archivedesc'.$id ] ) && $_POST[ 'archivedesc'.$id ] != '' ) {
              $data_desc = stripslashes( $_POST[ 'archivedesc'.$id ] );
            }
            // h1設定
            if( isset( $_POST[ 'archiveh1'.$id ] ) && $_POST[ 'archiveh1'.$id ] != '' ) {
              $data_h1 = stripslashes( $_POST[ 'archiveh1'.$id ] );
            }
          } else {
            // タイトル設定
            if( isset( $_POST[ 'catttl'.$id ] ) && $_POST[ 'catttl'.$id ] != '' ) {
              $data_ttl = stripslashes( $_POST[ 'catttl'.$id ] );
            }
            // キーワード設定
            if( isset( $_POST[ 'catkey'.$id ] ) && $_POST[ 'catkey'.$id ] != '' ) {
              $data_key = stripslashes( $_POST[ 'catkey'.$id ] );
            }
            // ディスクリプション設定
            if( isset( $_POST[ 'catdesc'.$id ] ) && $_POST[ 'catdesc'.$id ] != '' ) {
              $data_desc = stripslashes( $_POST[ 'catdesc'.$id ] );
            }
            // h1設定
            if( isset( $_POST[ 'cath1'.$id ] ) && $_POST[ 'cath1'.$id ] != '' ) {
              $data_h1 = stripslashes( $_POST[ 'cath1'.$id ] );
            }
          }
          // 入力された各データ返却
          return array( $data_ttl, $data_key, $data_desc, $data_h1 );
        }

      //-----------------------------------------------------
      // 【 outputHtml 】
      // param   ： -
      // return  ： -
      // disposal： html出力
      //-----------------------------------------------------
      function outputHtml() {
        // 共通項目
        $data_common_ttl = '';
        $data_common_key = '';
        $data_common_desc = '';
        $data_common_h1 = '';
        // テーブル名取得
        $tablename = $this->getTablename();
        // 各項目取得
        $common_datas = $this->selectCommondatas( $tablename );
        // 各項目設定
        foreach( $common_datas as $common_data ) {
          $data_common_ttl = $common_data->txt_title;
          $data_common_key = $common_data->txt_keyword;
          $data_common_desc = $common_data->txt_description;
          $data_common_h1 = $common_data->txt_h1;
        }
?>
<main id="InsideStepSystem">
  <form action="" method="post">
    <article class="inner-iss fixed-iss">
      <header>
        <h1>Inside Step System</h1>
      </header>
      <section class="chapter-iss fixed-iss">
        <header>
          <h2>Common Settings</h2>
        </header>
        <section class="chapter-inner-iss fixed-iss">
          <header>
            <h3>共通項目</h3>
          </header>
          <section class="fixed-iss">
            <ul>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>タイトル</h4>
                  </header>
                  <textarea name="allttl" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているタイトル</p>
                  <p>「<strong><?php echo $data_common_ttl; ?></strong>」</p>
                </section>
              </li>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>キーワード</h4>
                  </header>
                  <textarea name="allkey" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているキーワード</p>
                  <p>「<strong><?php echo $data_common_key; ?></strong>」</p>
                </section>
              </li>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>ディスクリプション</h4>
                  </header>
                  <textarea name="alldesc" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているディスクリプション</p>
                  <p>「<strong><?php echo $data_common_desc; ?></strong>」</p>
                </section>
              </li>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>h1</h4>
                  </header>
                  <textarea name="allh1" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているh1</p>
                  <p>「<strong><?php echo $data_common_h1; ?></strong>」</p>
                </section>
              </li>
            </ul>
          </section>
          <p class="btn-iss"><input type="submit" value="SET"></p>
        </section>
      </section>
      <section class="chapter-iss fixed-iss">
        <header>
          <h2>Archive Settings</h2>
        </header>
<?php
        // 各項目
        $postname = '';
        $data_archive_id = '';
        $data_archive_ttl = '';
        $data_archive_key = '';
        $data_archive_desc = '';
        $data_archive_h1 = '';
        // アーカイブデータ取得
        $archivedatas = $this->selectArchivedatas( $this->getTablename() );
        // アーカイブデータ分繰り返し
        foreach( $archivedatas as $archivedata ) {
          // id設定
          $data_archive_id = $archivedata->post_id;
          // タイトル設定
          $data_archive_ttl = $archivedata->txt_title;
          // キーワード設定
          $data_archive_key = $archivedata->txt_keyword;
          // ディスクリプション設定
          $data_archive_desc = $archivedata->txt_description;
          // h1設定
          $data_archive_h1 = $archivedata->txt_h1;
          // 投稿名取得
          $postnamedatas = $this->selectArchivePostname( $this->getOptionsTablename(), $data_archive_id );
          foreach( $postnamedatas as $postnamedata ) {
            $postname = $postnamedata->option_name;
          }
?>
        <section class="chapter-inner-iss fixed-iss">
          <header>
            <h3><?php echo get_post_type_object( $postname )->labels->name; ?></h3>
          </header>
          <section class="fixed-iss">
            <ul>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>タイトル</h4>
                  </header>
                  <textarea name="archivettl<?php echo $data_archive_id; ?>" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているタイトル</p>
                  <p>「<strong><?php echo $data_archive_ttl; ?></strong>」</p>
                </section>
              </li>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>キーワード</h4>
                  </header>
                  <textarea name="archivekey<?php echo $data_archive_id; ?>" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているキーワード</p>
                  <p>「<strong><?php echo $data_archive_key; ?></strong>」</p>
                </section>
              </li>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>ディスクリプション</h4>
                  </header>
                  <textarea name="archivedesc<?php echo $data_archive_id; ?>" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているディスクリプション</p>
                  <p>「<strong><?php echo $data_archive_desc; ?></strong>」</p>
                </section>
              </li>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>h1</h4>
                  </header>
                  <textarea name="archiveh1<?php echo $data_archive_id; ?>" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているh1</p>
                  <p>「<strong><?php echo $data_archive_h1; ?></strong>」</p>
                </section>
              </li>
            </ul>
          </section>
          <p class="btn-iss"><input type="submit" value="SET"></p>
        </section>
<?php
        }
?>
      </section>
      <section class="chapter-iss fixed-iss">
        <header>
          <h2>Category Settings</h2>
        </header>
<?php
        // 各項目
        $data_cat_ttl = '';
        $data_cat_key = '';
        $data_cat_desc = '';
        $data_cat_h1 = '';
        // IDとカテゴリー名取得
        $idcatdatas = $this->selectCategoryname( $this->getTermsTablename() );
        // カテゴリー分繰り返し
        foreach( $idcatdatas as $idcatdata ) {
          // レコードが存在するか判定
          if( $this->selectPageId( $tablename, $idcatdata->term_id, $this->catflg_true ) != 0 ) {
            // タイトル取得・設定
            $ttldatas = $this->selectTtl( $tablename, $idcatdata->term_id, $this->catflg_true );
            foreach( $ttldatas as $value ) {
              $data_cat_ttl = $value->txt_title;
            }
            // キーワード取得・設定
            $keydatas = $this->selectKey( $tablename, $idcatdata->term_id, $this->catflg_true );
            foreach( $keydatas as $value ) {
              $data_cat_key = $value->txt_keyword;
            }
            // ディスクリプション取得・設定
            $descdatas = $this->selectDesc( $tablename, $idcatdata->term_id, $this->catflg_true );
            foreach( $descdatas as $value ) {
              $data_cat_desc = $value->txt_description;
            }
            // h1取得・設定
            $h1datas = $this->selectH1( $tablename, $idcatdata->term_id, $this->catflg_true );
            foreach ( $h1datas as $value ) {
              $data_cat_h1 = $value->txt_h1;
            }
?>
        <section class="chapter-inner-iss fixed-iss">
          <header>
            <h3><?php echo $idcatdata->name; ?></h3>
          </header>
          <section class="fixed-iss">
            <ul>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>タイトル</h4>
                  </header>
                  <textarea name="catttl<?php echo $idcatdata->term_id; ?>" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているタイトル</p>
                  <p>「<strong><?php echo $data_cat_ttl; ?></strong>」</p>
                </section>
              </li>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>キーワード</h4>
                  </header>
                  <textarea name="catkey<?php echo $idcatdata->term_id; ?>" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているキーワード</p>
                  <p>「<strong><?php echo $data_cat_key; ?></strong>」</p>
                </section>
              </li>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>ディスクリプション</h4>
                  </header>
                  <textarea name="catdesc<?php echo $idcatdata->term_id; ?>" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているディスクリプション</p>
                  <p>「<strong><?php echo $data_cat_desc; ?></strong>」</p>
                </section>
              </li>
              <li>
                <section class="fixed-iss">
                  <header>
                    <h4>h1</h4>
                  </header>
                  <textarea name="cath1<?php echo $idcatdata->term_id; ?>" rows="4" cols="40"></textarea>
                  <p class="subject">現在登録されているh1</p>
                  <p>「<strong><?php echo $data_cat_h1; ?></strong>」</p>
                </section>
              </li>
            </ul>
          </section>
          <p class="btn-iss"><input type="submit" value="SET"></p>
        </section>
<?php
          }
        }
?>
      </section>
    </article>
  </form>
</main>
<?php
      }

  //-----------------------------------------------------
  // 【 addFieldPage 】
  // param   ： -
  // return  ： -
  // disposal： 固定ページにカスタムフィールド追加
  //-----------------------------------------------------
  function addFieldPage() {
    // 固定ページにカスタムフィールド追加
    add_meta_box( 'InsideStepSystem', 'InsideStepSystem 設定', array( $this, 'outputField' ), 'page', 'normal', 'default' );
  }

  //-----------------------------------------------------
  // 【 addFieldPost 】
  // param   ： -
  // return  ： -
  // disposal： 投稿にカスタムフィールド追加
  //-----------------------------------------------------
  function addFieldPost() {
    // 投稿にカスタムフィールド追加
    add_meta_box( 'InsideStepSystem', 'InsideStepSystem 設定', array( $this, 'outputField' ), 'post', 'normal', 'default' );
  }

  //-----------------------------------------------------
  // 【 addFieldCustomPost 】
  // param   ： -
  // return  ： -
  // disposal： カスタム投稿にカスタムフィールド追加
  //-----------------------------------------------------
  function addFieldCustomPost() {
    // 投稿名・カスタム投稿名取得
    $datas = $this->selectPostType( $this->getPostsTablename() );
    // 投稿名とカスタム投稿名の数分繰り返し
    foreach( $datas as $data ) {
      // カスタム投稿にカスタムフィールド追加
      add_meta_box( 'InsideStepSystem', 'InsideStepSystem 設定', array( $this, 'outputField' ), $data->post_type, 'normal', 'default' );
    }
  }

    //-----------------------------------------------------
    // 【 outputField 】
    // param   ： -
    // return  ： -
    // disposal： カスタムフィールド出力
    //-----------------------------------------------------
    function outputField() {
      // 投稿グローバル変数
      global $post;
      // ID取得
      $data_id = $post->ID;
      // テーブル名取得
      $tablename = $this->getTablename();
      // 各項目
      $data_ttl = '';
      $data_key = '';
      $data_desc = '';
      $data_h1 = '';
      // レコードが存在するか判定
      if( $this->selectPageId( $tablename, $data_id, $this->catflg_false ) == 0 ) {
        // IDを共通レコードに設定
        $data_id = -1;
      }
      // タイトル取得・設定
      $ttldatas = $this->selectTtl( $tablename, $data_id, $this->catflg_false );
      foreach( $ttldatas as $value ) {
        $data_ttl = $value->txt_title;
      }
      // キーワード取得・設定
      $keydatas = $this->selectKey( $tablename, $data_id, $this->catflg_false );
      foreach( $keydatas as $value ) {
        $data_key = $value->txt_keyword;
      }
      // ディスクリプション取得・設定
      $descdatas = $this->selectDesc( $tablename, $data_id, $this->catflg_false );
      foreach( $descdatas as $value ) {
        $data_desc = $value->txt_description;
      }
      // h1取得・設定
      $h1datas = $this->selectH1( $tablename, $data_id, $this->catflg_false );
      foreach( $h1datas as $value ) {
        $data_h1 = $value->txt_h1;
      }
?>
<article class="chapter-post-iss fixed-iss">
  <header>
    <h4>設定項目</h4>
  </header>
  <section class="fixed-iss">
    <ul>
      <li>
        <section class="fixed-iss">
          <header>
            <h5>タイトル</h5>
          </header>
          <textarea class="short" name="detailttl" rows="2" cols="100"></textarea>
          <p>現在登録されているタイトル　「<strong><?php echo $data_ttl; ?></strong>」</p>
        </section>
      </li>
      <li>
        <section class="fixed-iss">
          <header>
            <h5>キーワード</h5>
          </header>
          <textarea class="short" name="detailkey" rows="2" cols="100"></textarea>
          <p>現在登録されているキーワード　「<strong><?php echo $data_key; ?></strong>」</p>
        </section>
      </li>
      <li>
        <section class="fixed-iss">
          <header>
            <h5>ディスクリプション</h5>
          </header>
          <textarea class="large" name="detaildesc" rows="4" cols="100"></textarea>
          <p>現在登録されているディスクリプション　「<strong><?php echo $data_desc; ?></strong>」</p>
        </section>
      </li>
      <li>
        <section class="fixed-iss">
          <header>
            <h5>h1</h5>
          </header>
          <textarea class="short" name="detailh1" rows="4" cols="100"></textarea>
          <p>現在登録されているh1　「<strong><?php echo $data_h1; ?></strong>」</p>
        </section>
      </li>
    </ul>
  </section>
</article>
<?php
    }

  //-----------------------------------------------------
  // 【 saveFieldData 】
  // param   ： -
  // return  ： -
  // disposal： 各詳細ページデータ更新・登録
  //-----------------------------------------------------
  function saveFieldData() {
    // 投稿グローバル変数
    global $post;
    // テーブル名取得
    $tablename = $this->getTablename();
    // 記事タイトル取得
    $page = get_page( $post->ID );
    $pagename = $page->post_title;
    // 自動下書きかどうか判定
    if( !strstr( $pagename, $this->text_draft ) ) {
      // テーブルにデータが存在するか判定
      if( $this->selectPageId( $tablename, $post->ID, $this->catflg_false ) == 0 && $pagename != "" ) {
        // 共通項目取得
        $commondatas = $this->selectCommondatas( $tablename );
        foreach( $commondatas as $commondata ) {
          // 追加処理
          $this->insertTable( $tablename, $post->ID, $this->catflg_false, $pagename.'｜'.$commondata->txt_title, $pagename.','.$commondata->txt_keyword, $pagename.'、'.$commondata->txt_description, $pagename.'｜'.$commondata->txt_h1 );
        }
      } else {
        // 更新処理
        $this->updateEachTable( $tablename, stripslashes( $_POST[ 'detailttl' ] ), stripslashes( $_POST[ 'detailkey' ] ), stripslashes( $_POST[ 'detaildesc' ] ), stripslashes( $_POST[ 'detailh1' ] ), $post->ID, $this->catflg_false );
      }
    }
  }

    //-----------------------------------------------------
    // 【 updateEachTable 】
    // param1  ： テーブル名
    // param2  ： タイトル
    // param3  ： キーワード
    // param4  ： ディスクリプション
    // param5  ： h1
    // param6  ： ID
    // param7  ： カテゴリーフラグ
    // return  ： -
    // disposal： 各項目レコード更新
    //-----------------------------------------------------
    function updateEachTable( $tablename, $data_ttl, $data_key, $data_desc, $data_h1, $data_id, $data_flg ) {
      // タイトル更新
      if( $data_ttl != "" ) {
        $this->updateTableTitle( $tablename, $data_ttl, $data_id, $data_flg );
      }
      // キーワード更新
      if( $data_key != "" ) {
        $this->updateTableKeyword( $tablename, $data_key, $data_id, $data_flg );
      }
      // ディスクリプション更新
      if( $data_desc != "" ) {
        $this->updateTableDescription( $tablename, $data_desc, $data_id, $data_flg );
      }
      // h1更新
      if( $data_h1 != "" ) {
        $this->updateTableH1( $tablename, $data_h1, $data_id, $data_flg );
      }
    }

  //-----------------------------------------------------
  // 【 getIssTitle 】
  // param   ： -
  // return  ： タイトル
  // disposal： タイトル取得
  //-----------------------------------------------------
  function getIssTitle( $atts ) {
    // グローバル変数
    global $post;
    global $term;
    // 返却データ
    $data_return = '';
    // カテゴリーフラグ
    $data_flg = $this->catflg_false;
    // 初期化値設定
    extract( shortcode_atts( array(
      'data_id' => 0
    ), $atts ) );
    // テーブル名取得
    $tablename = $this->getTablename();
    // トップページか判定
    if( is_front_page() ) {
      // IDを共通レコードに設定
      $data_id = -1;
    }
    // アーカイブページか判定
    if( is_archive() ) {
      // アーカイブ用ID取得・設定
      $postdatas = $this->selectArchivePostid( $this->getOptionsTablename(), get_post_type() );
      foreach( $postdatas as $data ) {
        $data_id = $data->option_value;
      }
      // カテゴリーフラグ
      $data_flg = $this->catflg_false;
    }
    // カテゴリーページか判定
    if( is_category() ) {
      // カテゴリーID取得
      $data_id = get_query_var( 'cat' );
      // カテゴリーフラグ
      $data_flg = $this->catflg_true;
    }
    // カスタム投稿カテゴリーページか判定
    if( is_tax() ) {
      // カテゴリーID取得
      $data_id = get_queried_object_id();
      // カテゴリーフラグ
      $data_flg = $this->catflg_true;
    }
    // 検索
    $results = $this->selectTtl( $tablename, $data_id, $data_flg );
    // 検索結果取得
    foreach( $results as $value ) {
      $data_return = $value->txt_title;
    }
    // ページング番号付与
    $data_return = $this->addPagingNumber( $data_return, '｜', $data_flg );
    // 検索結果返却
    return $data_return;
  }

  //-----------------------------------------------------
  // 【 getIssKeyword 】
  // param   ： -
  // return  ： キーワード
  // disposal： キーワード取得
  //-----------------------------------------------------
  function getIssKeyword( $atts ) {
    // 投稿グローバル変数
    global $post;
    global $term;
    // 返却データ
    $data_return = '';
    // カテゴリーフラグ
    $data_flg = $this->catflg_false;
    // 初期化値設定
    extract( shortcode_atts( array(
      'data_id' => 0,
    ), $atts ) );
    // テーブル名取得
    $tablename = $this->getTablename();
    // トップページか判定
    if( is_front_page() ) {
      // IDを共通レコードに設定
      $data_id = -1;
    }
    // アーカイブページか判定
    if( is_archive() ) {
      // アーカイブ用ID取得・設定
      $postdatas = $this->selectArchivePostid( $this->getOptionsTablename(), get_post_type() );
      foreach( $postdatas as $data ) {
        $data_id = $data->option_value;
      }
      // カテゴリーフラグ
      $data_flg = $this->catflg_false;
    }
    // カテゴリーページか判定
    if( is_category() ) {
      // カテゴリーID取得
      $data_id = get_query_var( 'cat' );
      // カテゴリーフラグ
      $data_flg = $this->catflg_true;
    }
    // カスタム投稿カテゴリーページか判定
    if( is_tax() ) {
      // カテゴリーID取得
      $data_id = get_queried_object_id();
      // カテゴリーフラグ
      $data_flg = $this->catflg_true;
    }
    // 検索
    $results = $this->selectKey( $tablename, $data_id, $data_flg );
    // 検索結果取得
    foreach ( $results as $value ) {
      $data_return = $value->txt_keyword;
    }
    // ページング番号付与
    $data_return = $this->addPagingNumber( $data_return, ',', $data_flg );
    // 検索結果返却
    return $data_return;
  }

  //-----------------------------------------------------
  // 【 getIssDescription 】
  // param   ： -
  // return  ： ディスクリプション
  // disposal： ディスクリプション取得
  //-----------------------------------------------------
  function getIssDescription( $atts ) {
    // 投稿グローバル変数
    global $post;
    global $term;
    // 共通項目データ
    $data_common = '';
    // 返却データ
    $data_return = '';
    // カテゴリーフラグ
    $data_flg = $this->catflg_false;
    // 初期化値設定
    extract( shortcode_atts( array(
      'data_id' => 0,
    ), $atts ) );
    // テーブル名取得
    $tablename = $this->getTablename();
    // トップページか判定
    if( is_front_page() ) {
      // IDを共通レコードに設定
      $data_id = -1;
    }
    // アーカイブページか判定
    if( is_archive() ) {
      // アーカイブ用ID取得・設定
      $postdatas = $this->selectArchivePostid( $this->getOptionsTablename(), get_post_type() );
      foreach( $postdatas as $data ) {
        $data_id = $data->option_value;
      }
      // カテゴリーフラグ
      $data_flg = $this->catflg_false;
    }
    // カテゴリーページか判定
    if( is_category() ) {
      // カテゴリーID取得
      $data_id = get_query_var( 'cat' );
      // カテゴリーフラグ
      $data_flg = $this->catflg_true;
    }
    // カスタム投稿カテゴリーページか判定
    if( is_tax() ) {
      // カテゴリーID取得
      $data_id = get_queried_object_id();
      // カテゴリーフラグ
      $data_flg = $this->catflg_true;
    }
    // 検索
    $results = $this->selectDesc( $tablename, $data_id, $data_flg );
    // 検索結果取得
    foreach( $results as $value ) {
      $data_return = $value->txt_description;
    }
    // ページング番号付与
    $data_return = $this->addPagingNumber( $data_return, '、', $data_flg );
    // 検索結果返却
    return $data_return;
  }

  //-----------------------------------------------------
  // 【 getIssH1 】
  // param   ： -
  // return  ： h1
  // disposal： h1取得
  //-----------------------------------------------------
  function getIssH1( $atts ) {
    // 投稿グローバル変数
    global $post;
    global $term;
    // 返却データ
    $data_return = '';
    // カテゴリーフラグ
    $data_flg = $this->catflg_false;
    // 初期化値設定
    extract( shortcode_atts( array(
      'data_id' => 0,
    ), $atts ) );
    // テーブル名取得
    $tablename = $this->getTablename();
    // トップページか判定
    if( is_front_page() ) {
      // IDを共通レコードに設定
      $data_id = -1;
    }
    // アーカイブページか判定
    if( is_archive() ) {
      // アーカイブ用ID取得・設定
      $postdatas = $this->selectArchivePostid( $this->getOptionsTablename(), get_post_type() );
      foreach( $postdatas as $data ) {
        $data_id = $data->option_value;
      }
      // カテゴリーフラグ
      $data_flg = $this->catflg_false;
    }
    // カテゴリーページか判定
    if( is_category() ) {
      // カテゴリーID取得
      $data_id = get_query_var( 'cat' );
      // カテゴリーフラグ
      $data_flg = $this->catflg_true;
    }
    // カスタム投稿カテゴリーページか判定
    if( is_tax() ) {
      // カテゴリーID取得
      $data_id = get_queried_object_id();
      // カテゴリーフラグ
      $data_flg = $this->catflg_true;
    }
    // 検索
    $results = $this->selectH1( $tablename, $data_id, $data_flg );
    // 検索結果取得
    foreach( $results as $value ) {
      $data_return = $value->txt_h1;
    }
    // ページング番号付与
    $data_return = $this->addPagingNumber( $data_return, '｜', $data_flg );
    // 検索結果返却
    return $data_return;
  }

    //-----------------------------------------------------
    // 【 addPagingNumber 】
    // param1  ： ページング番号を付与するデータ
    // param2  ： 区切り文字
    // return  ： ページング番号を付与したデータ
    // disposal： ページング番号を付与する
    //-----------------------------------------------------
    function addPagingNumber( $data, $divide, $flg ) {
      // queryグローバル変数
      global $wp_query;
      // カテゴリーページか判定
      if( $flg ) {
        // 現在のページ数取得
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        // 1ページ目か判定
        if( $paged != 1 ) {
          // 現在のページ数設定
          $data = $data.$divide.'page'.$paged;
        }
      }
      // データ返却
      return $data;
    }


  //----------------------------------------------------- DB START -----------------------------------------------------//


  //-- テーブル名 START -----------------------------------------------------//

  //-----------------------------------------------------
  // 【 getTablename 】
  // param   ： -
  // return  ： insidestepsystemテーブル名
  // disposal： insidestepsystemテーブル名取得
  //-----------------------------------------------------
  function getTablename() {
    // DB接続グローバル変数
    global $wpdb;
    // テーブル名（接頭辞付き）返却
    return $wpdb->prefix.'insidestepsystem';
  }

  //-----------------------------------------------------
  // 【 getOptionsTablename 】
  // param   ： -
  // return  ： optionsテーブル名
  // disposal： optionsテーブル名取得
  //-----------------------------------------------------
  function getOptionsTablename() {
    // DB接続グローバル変数
    global $wpdb;
    // テーブル名（接頭辞付き）返却
    return $wpdb->prefix.'options';
  }

  //-----------------------------------------------------
  // 【 getPostsTablename 】
  // param   ： -
  // return  ： postsテーブル名
  // disposal： postsテーブル名取得
  //-----------------------------------------------------
  function getPostsTablename() {
    // DB接続グローバル変数
    global $wpdb;
    // テーブル名（接頭辞付き）返却
    return $wpdb->prefix.'posts';
  }

  //-----------------------------------------------------
  // 【 getTermsTablename 】
  // param   ： -
  // return  ： termsテーブル名
  // disposal： termsテーブル名取得
  //-----------------------------------------------------
  function getTermsTablename() {
    // DB接続グローバル変数
    global $wpdb;
    // テーブル名（接頭辞付き）返却
    return $wpdb->prefix.'terms';
  }

  //-- テーブル名 END -----------------------------------------------------//

  //-- SELECT START -----------------------------------------------------//

  //-----------------------------------------------------
  // 【 getOptionsTablePostName 】
  // param1  ： テーブル名
  // param2  ： 投稿名
  // return  ： 投稿名
  // disposal： 投稿名取得
  //-----------------------------------------------------
  function getOptionsTablePostName( $tablename, $name ) {
    // DB接続グローバル変数
    global $wpdb;
    // 投稿名返却
    return $wpdb->query( 'SELECT option_name FROM '.$tablename.' WHERE option_name = '.$name );
  }

  //-----------------------------------------------------
  // 【 selectAllRecords 】
  // param1  ： テーブル名
  // return  ： 全レコードデータ
  // disposal： 全レコードデータを取得
  //-----------------------------------------------------
  function selectAllRecords( $tablename ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->get_results( 'SELECT post_id, cat_flg, txt_title, txt_keyword, txt_description, txt_h1 FROM '.$tablename );
  }

  //-----------------------------------------------------
  // 【 selectAllRecordsExceptCommon 】
  // param1  ： テーブル名
  // return  ： 共通項目以外の全レコードデータ
  // disposal： 共通項目以外の全レコードデータを取得
  //-----------------------------------------------------
  function selectAllRecordsExceptCommon( $tablename ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->get_results( 'SELECT post_id, cat_flg, txt_title, txt_keyword, txt_description, txt_h1 FROM '.$tablename.' WHERE NOT post_id = -1' );
  }

  //-----------------------------------------------------
  // 【 selectIdTitle 】
  // param1  ： テーブル名
  // param2  ： 投稿タイプ
  // return  ： IDとタイトル
  // disposal： IDとタイトルを取得
  //-----------------------------------------------------
  function selectIdTitle( $tablename, $type ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->get_results( 'SELECT ID, post_title FROM '.$tablename.' WHERE post_type = "'.$type.'" AND NOT post_status = "auto-draft"' );
  }

  //-----------------------------------------------------
  // 【 selectCommondatas 】
  // param1  ： テーブル名
  // return  ： 共通データ
  // disposal： 共通データを取得
  //-----------------------------------------------------
  function selectCommondatas( $tablename ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->get_results( 'SELECT txt_title, txt_keyword, txt_description, txt_h1 FROM '.$tablename.' WHERE post_id = -1' );
  }

  //-----------------------------------------------------
  // 【 selectCategoryname 】
  // param1  ： テーブル名
  // return  ： 投稿とカスタム投稿のカテゴリー名
  // disposal： 投稿とカスタム投稿のカテゴリー名全て取得
  //-----------------------------------------------------
  function selectCategoryname( $tablename ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->get_results( 'SELECT term_id, name FROM '.$tablename );
  }

  //-----------------------------------------------------
  // 【 selectArchivedatas 】
  // param1  ： テーブル名
  // return  ： アーカイブデータ取得
  // disposal： アーカイブデータを全て取得
  //-----------------------------------------------------
  function selectArchivedatas( $tablename ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->get_results( 'SELECT post_id, txt_title, txt_keyword, txt_description, txt_h1 FROM '.$tablename.' WHERE post_id < -1' );
  }

  //-----------------------------------------------------
  // 【 selectArchivePostname 】
  // param1  ： optionsテーブル名
  // param2  ： アーカイブ用ID
  // return  ： アーカイブ用投稿名取得
  // disposal： アーカイブ用投稿名を取得
  //-----------------------------------------------------
  function selectArchivePostname( $tablename, $data_id ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->get_results( 'SELECT option_name FROM '.$tablename.' WHERE option_value = '.$data_id );
  }

  //-----------------------------------------------------
  // 【 selectArchivePostid 】
  // param1  ： optionsテーブル名
  // param2  ： 投稿名
  // return  ： アーカイブ用投稿ID取得
  // disposal： アーカイブ用投稿IDを取得
  //-----------------------------------------------------
  function selectArchivePostid( $tablename, $name ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->get_results( 'SELECT option_value FROM '.$tablename.' WHERE option_name = "'.$name.'"' );
  }

  //-----------------------------------------------------
  // 【 selectArchiveId 】
  // param1  ： テーブル名
  // return  ： ID
  // disposal： ID取得
  //-----------------------------------------------------
  function selectArchiveId( $tablename ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->query( 'SELECT post_id FROM '.$tablename.' WHERE post_id < -1' );
  }

  //-----------------------------------------------------
  // 【 selectPostType 】
  // param1  ： テーブル名
  // return  ： 投稿とカスタム投稿名
  // disposal： 投稿とカスタム投稿名全て取得
  //-----------------------------------------------------
  function selectPostType( $tablename ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->get_results( 'SELECT DISTINCT post_type FROM '.$tablename.' where post_type not in( "page", "revision", "attachment", "nav_menu_item", "mw-wp-form", "acf" )' );
  }

  //-----------------------------------------------------
  // 【 selectPostTypeAll 】
  // param1  ： テーブル名
  // return  ： 固定ページと投稿とカスタム投稿名
  // disposal： 固定ページと投稿とカスタム投稿名全て取得
  //-----------------------------------------------------
  function selectPostTypeAll( $tablename ) {
    // DB接続グローバル変数
    global $wpdb;
    return $wpdb->get_results( 'SELECT DISTINCT post_type FROM '.$tablename.' where post_type not in( "revision", "attachment", "nav_menu_item", "mw-wp-form", "acf" )' );
  }

  //-----------------------------------------------------
  // 【 selectPageId 】
  // param1  ： テーブル名
  // param2  ： ID
  // param3  ： カテゴリーフラグ
  // return  ： ID
  // disposal： ID取得
  //-----------------------------------------------------
  function selectPageId( $tablename, $data_id, $data_flg ) {
    // DB接続グローバル変数
    global $wpdb;
    // 共通データか判定
    if( $data_id == -1 ) {
      // 共通データの場合
      return $wpdb->query( 'SELECT post_id FROM '.$tablename.' WHERE post_id = '.$data_id );
    } else {
      // 共通データではない場合
      return $wpdb->query( 'SELECT post_id FROM '.$tablename.' WHERE post_id = '.$data_id.' AND cat_flg = '.$data_flg );
    }
  }

  //-----------------------------------------------------
  // 【 selectTtl 】
  // param1  ： テーブル名
  // param2  ： ID
  // param3  ： カテゴリーフラグ
  // return  ： タイトル
  // disposal： タイトル取得
  //-----------------------------------------------------
  function selectTtl( $tablename, $data_id, $data_flg ) {
    // DB接続グローバル変数
    global $wpdb;
    // 共通データか判定
    if( $data_id == -1 ) {
      // 共通データの場合
      return $wpdb->get_results( 'SELECT txt_title FROM '.$tablename.' WHERE post_id = '.$data_id );
    } else {
      // 共通データではない場合
      return $wpdb->get_results( 'SELECT txt_title FROM '.$tablename.' WHERE post_id = '.$data_id.' AND cat_flg = '.$data_flg );
    }
  }

  //-----------------------------------------------------
  // 【 selectKey 】
  // param1  ： テーブル名
  // param2  ： ID
  // param3  ： カテゴリーフラグ
  // return  ： キーワード
  // disposal： キーワード取得
  //-----------------------------------------------------
  function selectKey( $tablename, $data_id, $data_flg ) {
    // DB接続グローバル変数
    global $wpdb;
    // 共通データか判定
    if( $data_id == -1 ) {
      // 共通データの場合
      return $wpdb->get_results( 'SELECT txt_keyword FROM '.$tablename.' WHERE post_id = '.$data_id );
    } else {
      // 共通データではない場合
      return $wpdb->get_results( 'SELECT txt_keyword FROM '.$tablename.' WHERE post_id = '.$data_id.' AND cat_flg = '.$data_flg );
    }
  }

  //-----------------------------------------------------
  // 【 selectDesc 】
  // param1  ： テーブル名
  // param2  ： ID
  // param3  ： カテゴリーフラグ
  // return  ： ディスクリプション
  // disposal： ディスクリプション取得
  //-----------------------------------------------------
  function selectDesc( $tablename, $data_id, $data_flg ) {
    // DB接続グローバル変数
    global $wpdb;
    // 共通データか判定
    if( $data_id == -1 ) {
      // 共通データの場合
      return $wpdb->get_results( 'SELECT txt_description FROM '.$tablename.' WHERE post_id = '.$data_id );
    } else {
      // 共通データではない場合
      return $wpdb->get_results( 'SELECT txt_description FROM '.$tablename.' WHERE post_id = '.$data_id.' AND cat_flg = '.$data_flg );
    }
  }

  //-----------------------------------------------------
  // 【 selectH1 】
  // param1  ： テーブル名
  // param2  ： ID
  // param3  ： カテゴリーフラグ
  // return  ： h1
  // disposal： h1取得
  //-----------------------------------------------------
  function selectH1( $tablename, $data_id, $data_flg ) {
    // DB接続グローバル変数
    global $wpdb;
    // 共通データか判定
    if( $data_id == -1 ) {
      // 共通データの場合
      return $wpdb->get_results( 'SELECT txt_h1 FROM '.$tablename.' WHERE post_id = '.$data_id );
    } else {
      // 共通データではない場合
      return $wpdb->get_results( 'SELECT txt_h1 FROM '.$tablename.' WHERE post_id = '.$data_id.' AND cat_flg = '.$data_flg );
    }
  }

  //-- SELECT END -----------------------------------------------------//

  //-- UPDATE START -----------------------------------------------------//

  //-----------------------------------------------------
  // 【 updateTable 】
  // param1  ： テーブル名
  // param2  ： タイトル
  // param3  ： キーワード
  // param4  ： ディスクリプション
  // param5  ： h1
  // param6  ： ID
  // param7  ： カテゴリーフラグ
  // return  ： -
  // disposal： レコード更新
  //-----------------------------------------------------
  function updateTable( $tablename, $data_ttl, $data_key, $data_desc, $data_h1, $data_id, $data_flg ) {
    // DB接続グローバル変数
    global $wpdb;
    $wpdb->get_results( $wpdb->prepare( 'UPDATE '.$tablename.' SET txt_title = %s, txt_keyword = %s, txt_description = %s, txt_h1 = %s WHERE post_id = %d AND cat_flg = %d', $data_ttl, $data_key, $data_desc, $data_h1, $data_id, $data_flg ) );
  }

  //-----------------------------------------------------
  // 【 updateTableTitle 】
  // param1  ： テーブル名
  // param2  ： タイトル
  // param3  ： ID
  // param4  ： カテゴリーフラグ
  // return  ： -
  // disposal： レコード更新（タイトル）
  //-----------------------------------------------------
  function updateTableTitle( $tablename, $data_ttl, $data_id, $data_flg ) {
    // DB接続グローバル変数
    global $wpdb;
    $wpdb->get_results( $wpdb->prepare( 'UPDATE '.$tablename.' SET txt_title = %s WHERE post_id = %d AND cat_flg = %d', $data_ttl, $data_id, $data_flg ) );
  }

  //-----------------------------------------------------
  // 【 updateTableKeyword 】
  // param1  ： テーブル名
  // param2  ： キーワード
  // param3  ： ID
  // param4  ： カテゴリーフラグ
  // return  ： -
  // disposal： レコード更新（キーワード）
  //-----------------------------------------------------
  function updateTableKeyword( $tablename, $data_key, $data_id, $data_flg ) {
    // DB接続グローバル変数
    global $wpdb;
    $wpdb->get_results( $wpdb->prepare( 'UPDATE '.$tablename.' SET txt_keyword = %s WHERE post_id = %d AND cat_flg = %d', $data_key, $data_id, $data_flg ) );
  }

  //-----------------------------------------------------
  // 【 updateTableDescription 】
  // param1  ： テーブル名
  // param2  ： ディスクリプション
  // param3  ： ID
  // param4  ： カテゴリーフラグ
  // return  ： -
  // disposal： レコード更新（ディスクリプション）
  //-----------------------------------------------------
  function updateTableDescription( $tablename, $data_desc, $data_id, $data_flg ) {
    // DB接続グローバル変数
    global $wpdb;
    $wpdb->get_results( $wpdb->prepare( 'UPDATE '.$tablename.' SET txt_description = %s WHERE post_id = %d AND cat_flg = %d', $data_desc, $data_id, $data_flg ) );
  }

  //-----------------------------------------------------
  // 【 updateTableH1 】
  // param1  ： テーブル名
  // param2  ： h1
  // param3  ： ID
  // param4  ： カテゴリーフラグ
  // return  ： -
  // disposal： レコード更新（h1）
  //-----------------------------------------------------
  function updateTableH1( $tablename, $data_h1, $data_id, $data_flg ) {
    // DB接続グローバル変数
    global $wpdb;
    $wpdb->get_results( $wpdb->prepare( 'UPDATE '.$tablename.' SET txt_h1 = %s WHERE post_id = %d AND cat_flg = %d', $data_h1, $data_id, $data_flg ) );
  }

  //-- UPDATE END -----------------------------------------------------//

  //-- INSERT START -----------------------------------------------------//

  //-----------------------------------------------------
  // 【 insertTable 】
  // param1  ： テーブル名
  // param2  ： ID
  // param3  ： カテゴリーフラグ
  // param4  ： タイトル
  // param5  ： キーワード
  // param6  ： ディスクリプション
  // param7  ： h1
  // return  ： -
  // disposal： レコード追加
  //-----------------------------------------------------
  function insertTable( $tablename, $data_id, $data_flg, $data_ttl, $data_key, $data_desc, $data_h1 ) {
    // DB接続グローバル変数
    global $wpdb;
    $wpdb->query( $wpdb->prepare( 'INSERT INTO '.$tablename.'( post_id, cat_flg, txt_title, txt_keyword, txt_description, txt_h1 ) VALUES ( %d, %d, %s, %s, %s, %s )', $data_id, $data_flg, $data_ttl, $data_key, $data_desc, $data_h1 ) );
  }

  //-- INSERT END -----------------------------------------------------//

  //-- DROP START -----------------------------------------------------//

  //-----------------------------------------------------
  // 【 dropTable 】
  // param   ： テーブル名
  // return  ： -
  // disposal： テーブル削除
  //-----------------------------------------------------
  function dropTable( $tablename ) {
    // DB接続グローバル変数
    global $wpdb;
    $wpdb->query( 'DROP TABLE '.$tablename );
  }

  //-- DROP END -----------------------------------------------------//


  //----------------------------------------------------- DB END -----------------------------------------------------//


}

// インスタンス作成
$insidestepsystem = new InsideStepSystem();
?>
