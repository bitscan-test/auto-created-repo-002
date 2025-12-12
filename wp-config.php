<?php
/**
 * WordPress の基本設定
 *
 * このファイルは、MySQL、テーブル接頭辞、秘密鍵、言語、ABSPATH の設定を含みます。
 * より詳しい情報は {@link http://wpdocs.sourceforge.jp/wp-config.php_%E3%81%AE%E7%B7%A8%E9%9B%86
 * wp-config.php の編集} を参照してください。MySQL の設定情報はホスティング先より入手できます。
 *
 * このファイルはインストール時に wp-config.php 作成ウィザードが利用します。
 * ウィザードを介さず、このファイルを "wp-config.php" という名前でコピーして直接編集し値を
 * 入力してもかまいません。
 *
 * @package WordPress
 */

// 注意:
// Windows の "メモ帳" でこのファイルを編集しないでください !
// 問題なく使えるテキストエディタ
// (http://wpdocs.sourceforge.jp/Codex:%E8%AB%87%E8%A9%B1%E5%AE%A4 参照)
// を使用し、必ず UTF-8 の BOM なし (UTF-8N) で保存してください。

// ** MySQL 設定 - この情報はホスティング先から入手してください。 ** //
/** WordPress のためのデータベース名 */
define('DB_NAME', '_02takaseshika');

/** MySQL データベースのユーザー名 */
define('DB_USER', '_02takaseshika');

/** MySQL データベースのパスワード */
define('DB_PASSWORD', 'hes9p5ncqlx6');

/** MySQL のホスト名 */
define('DB_HOST', 'mysql011.phy.heteml.lan');

/** データベースのテーブルを作成する際のデータベースの文字セット */
define('DB_CHARSET', 'utf8');

/** データベースの照合順序 (ほとんどの場合変更する必要はありません) */
define('DB_COLLATE', '');

/**#@+
 * 認証用ユニークキー
 *
 * それぞれを異なるユニーク (一意) な文字列に変更してください。
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org の秘密鍵サービス} で自動生成することもできます。
 * 後でいつでも変更して、既存のすべての cookie を無効にできます。これにより、すべてのユーザーを強制的に再ログインさせることになります。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'EZjW]7~M+6qb8f$ dp,zU^|H_[D##<l,9 ]cN4z|(]B/8SK2Q?tCsww|)u}`jU+D');
define('SECURE_AUTH_KEY',  'myLW!Pb-r|,X=Uf-,MnLA|5Y|mu<h[_] !]jQM#{&U-3c}@shD&U_k?=MFAQ-#qZ');
define('LOGGED_IN_KEY',    '<d1Zc{#7PZzwq]S+CsIMR1j>Hg$9xhxe*auWJ%v<x-h*dcb:cf4|+zx[w8=ovCNp');
define('NONCE_KEY',        'YJ|-CsL%;1+j7R#ws<gFeZ-<L(:@-IL+uGzPe@w@j(K$-+}>OhY^MA^0bE<e)H-3');
define('AUTH_SALT',        '6m4:idkxi^QkN@Ev]u~WexPwZ/~FSXLZsE8b7$HECu6ddPBbL7#VGMzpitc_ZyYQ');
define('SECURE_AUTH_SALT', 'R|Lghh?FL=u85z@_T|-Z;(os:cMc7Un+0(wx:HG?31mpF{Tsp<Lc|5$IZJm:a(X_');
define('LOGGED_IN_SALT',   'S]71b<;HRyu#-:1p3}(()U/{[F>!lq}tqkJPa?MVWQ2$c|Gp>e.*sx[I1n,67D+|');
define('NONCE_SALT',       '>r96R<g:5snF;Gs0SE@h]F3Hr*2[a?s~vG8<f76GhP^bb70-bL9f q_$k&/UxGCC');

/**#@-*/

/**
 * WordPress データベーステーブルの接頭辞
 *
 * それぞれにユニーク (一意) な接頭辞を与えることで一つのデータベースに複数の WordPress を
 * インストールすることができます。半角英数字と下線のみを使用してください。
 */
$table_prefix  = 'uxtnghwt_';

/**
 * 開発者へ: WordPress デバッグモード
 *
 * この値を true にすると、開発中に注意 (notice) を表示します。
 * テーマおよびプラグインの開発者には、その開発環境においてこの WP_DEBUG を使用することを強く推奨します。
 */
define('WP_DEBUG', false);

/* 編集が必要なのはここまでです ! WordPress でブログをお楽しみください。 */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

define('WP_POST_REVISIONS', 10); //リビジョン数を10に設定


/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
