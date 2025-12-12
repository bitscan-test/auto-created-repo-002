<?php
/*
  ページタイトル表示
    $slugはカスタム投稿の時はpost_type
*/
function disp_page_title($disp_type, $page_type, $slug = null)
{

  //
  // 単純にWordpress関数で表示する
  //
  if ($disp_type == 'normal') {
    if ($page_type == 'page') {
      echo get_the_title();
    } elseif ($page_type == 'home' ||
              $page_type == 'category' ||
              $page_type == 'single') {
      echo 'お知らせ';
      // } elseif ($page_type == 'category') {
      //   echo single_cat_title('', false);
    } elseif ($page_type == 'month') {
      echo get_the_date('Y年m月');
    } elseif ($page_type == 'year') {
      echo get_the_date('Y年');
    } elseif ($page_type == 'search') {
      $get_query = filter_input( INPUT_GET, "s" );
      if(empty($get_query)){
        echo '検索結果一覧';
      }else{
        echo '「'.get_search_query().'」の検索結果';
      }
    } elseif ($page_type == '404') {
      echo '404 Not Found';
      // } elseif ($page_type == 'single') {
      //     if (function_exists('get_category_name_and_id_in_the_single')) {
      //         $catDataArr = get_category_name_and_id_in_the_single();
      //         if (isset($catDataArr['cat_name'])) {
      //             echo($catDataArr['cat_name']);
      //         }
      //     } else {
      // 	  // $cat_arr = get_the_category();
      // 			// echo strip_tags($cat_arr[0]->name);
      // 		echo strip_tags(get_the_term_list(get_the_ID(), 'category', '', '/', '') );
      //         // echo strip_tags(get_the_term_list(get_the_ID(), 'category', '<span class="category">', '</span><span class="category">', '</span>'), '<span>');
      //     }
    } elseif ($page_type == 'custom') {
      echo esc_html(get_post_type_object($slug)->label);
    }

    //
    // 任意の形式で表示させたい時
    //
  } elseif ($disp_type == 'voluntary') {

    // 固定ページ
    if ($page_type == 'page') {
      if ($slug === '') {
        //        echo '<h2></h2>';
      } else {
?>
<h2 class="ttl"><?php echo get_the_title(); ?></h2>
<div class="img">
  <span class="fream-top"></span>
  <img src="<?php echo get_template_directory_uri() ?>/images/sample02.jpg" alt="">
  <span class="fream-bottom"></span>
</div>
<?php

             }
    } elseif ($page_type == 'home') {
?>
<h2 class="ttl">TOPICS</h2>
<div class="img">
  <span class="fream-top"></span>
  <img src="<?php echo get_template_directory_uri() ?>/images/sample02.jpg" alt="">
  <span class="fream-bottom"></span>
</div>
<?php

      // 投稿ページ
                                    } elseif ($page_type == 'category') {
      if ($slug === '') {
        //        echo '<h2></h2>';
      } else {
?>
<h2 class="ttl"><?php echo single_cat_title('', false); ?></h2>
<div class="img">
  <span class="fream-top"></span>
  <img src="<?php echo get_template_directory_uri() ?>/images/sample02.jpg" alt="">
  <span class="fream-bottom"></span>
</div>
<?php

             }


      // 投稿詳細ページ
    } elseif ($page_type == 'single') {
?>
<h2 class="ttl"><?php echo strip_tags(get_the_term_list(get_the_ID(), 'category', '', ' / ', '')) ?></h2>
<div class="img">
  <span class="fream-top"></span>
  <img src="<?php echo get_template_directory_uri() ?>/images/sample02.jpg" alt="">
  <span class="fream-bottom"></span>
</div>
<?php

      // カスタム投稿
                                      } elseif ($page_type == 'custom') {
      if ($slug === 'seminar') {
?>
<h2 class="ttl"><?php echo esc_html(get_post_type_object($slug)->label); ?></h2>
<div class="img">
  <span class="fream-top"></span>
  <img src="<?php echo get_template_directory_uri() ?>/images/sample02.jpg" alt="">
  <span class="fream-bottom"></span>
</div>
<?php

                               }
    }
  }
}

/*
* 投稿のタイトルが未入力ならば自動で挿入する
*/
function replace_post_title($title) {
  global $post;
  //post_typeを判定(post, page, カスタム投稿)
  if( isset($post) && $post->post_type === 'post' ){
    if($title === ""){
      $title .= 'タイトルなし';
    }
  }
  return $title;
}
add_filter('title_save_pre', 'replace_post_title');
