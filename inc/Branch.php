<?php


class Branch{
  public function __construct(){}
  public function branchs(){
    $branchs = [];
    $args = array(
        'post_type' => 'branchs',
        'post_status' => 'publish',
        'posts_per_page' => -1
    );
    $loop = new WP_Query( $args );
    while ( $loop->have_posts() ) : $loop->the_post();
      $branchs[] = (object)["id"=>get_the_ID(),"name"=>get_the_title()];
    endwhile;
    wp_reset_postdata();
    return $branchs;
  }
  public function get($id){
    $post = get_post($id);
    return '<b>'.($post->post_title).'</b><br><i>'.$post->post_content.'</i>';
  }
}
