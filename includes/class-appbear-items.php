<?php

use Appbear\Includes\Functions;
use Appbear\Includes\GoogleFonts;

class AppbearItems {

  /**
   * Social Icons Subset
   *
   * @constant
   */
  const SOCIAL_ICONS_SUBSET = array(
    'fa-facebook',
    'fa-facebook-f',
    'fa-facebook-messenger',
    'fa-facebook-square',
    '0xe961',
    '0xe9be',
    'fa-twitter',
    'fa-twitter-square',
    'fa-pinterest',
    'fa-pinterest-square',
    'fa-dribbble',
    'fa-dribbble-square',
    'fa-linkedin',
    'fa-linkedin-in',
    'fa-flickr',
    'fa-youtube',
    'fa-youtube-square',
    'fa-reddit',
    'fa-reddit-alien',
    'fa-reddit-square',
    'fa-tumblr',
    'fa-tumblr-square',
    'fa-vimeo',
    'fa-vimeo-v',
    'fa-vimeo-square',
    'fa-wordpress',
    'fa-wordpress-simple',
    'fa-yelp',
    'fa-lastfm',
    'fa-lastfm-square',
    'fa-xing',
    'fa-xing-square',
    'fa-deviantart',
    'fa-deviantart',
    'fa-apple',
    'fa-apple-pay',
    'fa-foursquare',
    'fa-github',
    'fa-bitbucket',
    'fa-bitcoin',
    'fa-mixcloud',
    'fa-soundcloud',
    'fa-behance',
    'fa-behance-square',
    'fa-instagram',
    'fa-instagram-square',
    'fa-odnoklassniki',
    'fa-odnoklassniki-square',
    'fa-paypal',
    'fa-spotify',
    'fa-google',
    'fa-google-drive',
    'fa-google-pay',
    'fa-google-play',
    'fa-google-plus',
    'fa-google-plus-g',
    'fa-google-plus-square',
    'fa-google-wallet',
    'fa-viadeo',
    'fa-viadeo-square',
    'fa-500px',
    'fa-vk',
    'fa-medium',
    'fa-medium-m',
    'fa-twitch',
    'fa-snapchat',
    'fa-snapchat-square',
    'fa-snapchat-ghost',
    'fa-steam',
    'fa-steam-square',
    'fa-telegram',
    'fa-telegram-plane',
    'fa-tripadvisor',
    'fa-tiktok',
  );

  private static $instance = null;
  public static $google_fonts = array();


  /*
  |---------------------------------------------------------------------------------------------------
  | Lista de términos de taxonomias
  |---------------------------------------------------------------------------------------------------
  */
  public static function terms( $taxonomy = '', $args = array(), $more_items = array() ){
      $args = wp_parse_args( $args, array(
          'hide_empty' => false,
      ) );
      $terms = get_terms( $taxonomy, $args );
      if( is_wp_error( $terms ) ){
          return array();
      }
      $items = array();
      foreach( $terms as $term ){
          $items[$term->slug] = $term->name;
      }
      return array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Lista de tipos de post
  |---------------------------------------------------------------------------------------------------
  */
  public static function post_types( $args = array(), $operator = 'and', $more_items = array() ){
      $post_types = get_post_types( $args, 'objects', $operator );
      $items = array();
      foreach( $post_types as $post_type ){
          $items[$post_type->name] = $post_type->label;
      }
      return array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Lista de posts de un tipos de post
  |---------------------------------------------------------------------------------------------------
  */
  public static function posts_by_post_type( $post_type = 'post', $args = array(), $more_items = array() ){
      $args = wp_parse_args( $args, array(
          'post_type' => $post_type,
          'posts_per_page' => 5,//=numberposts
      ) );
      $posts = get_posts( $args );
      $items = array();
      foreach( $posts as $post ){
          $items[$post->ID] = $post->post_title;
      }
      return Functions::nice_array_merge( $more_items, $items );
  }


  /*
  |---------------------------------------------------------------------------------------------------
  | Google fonts
  |---------------------------------------------------------------------------------------------------
  */
  public static function google_fonts( $more_items = array() ){
      if( ! empty( self::$google_fonts ) ){
          return Functions::nice_array_merge( $more_items, self::$google_fonts );
      }
      $items = array();
      $gf = new GoogleFonts();
      $google_fonts = $gf->get_fonts();
      foreach( $google_fonts as $font ){
          $items[$font->family] = $font->family;
      }
      self::$google_fonts = $items;
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Web safe fonts
  |---------------------------------------------------------------------------------------------------
  */
  public static function web_safe_fonts( $more_items = array() ){
      $web_safe_fonts = include JPRESS_DIR . 'includes/data/web-safe-fonts.php';
      $items = array();
      foreach( $web_safe_fonts as $key => $font ){
          $items[$key] = $font;
      }
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Dart google fonts
  |---------------------------------------------------------------------------------------------------
  */
  public static function dart_google_fonts( $more_items = array() ){
      $web_safe_fonts = include JPRESS_DIR . 'includes/data/dart-google-fonts.php';
      $items = array();
      foreach( $web_safe_fonts as $font ){
          $items[$font] = $font;
      }
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Border style
  |---------------------------------------------------------------------------------------------------
  */
  public static function border_style( $more_items = array() ){
      $items = array(
          'solid' => 'Solid',
          'none' => 'None',
          'dotted' => 'Dotted',
          'dashed' => 'Dashed',
          'double' => 'Double',
          'groove' => 'Groove',
          //'ridge'  => 'Ridge',
          //'inset'  => 'Inset',
          //'outset' => 'Outset',
          //'hidden' => 'Hidden',
      );
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Opacity
  |---------------------------------------------------------------------------------------------------
  */
  public static function opacity( $more_items = array() ){
      $items = array(
          '1' => '1',
          '0.9' => '0.9',
          '0.8' => '0.8',
          '0.7' => '0.7',
          '0.6' => '0.6',
          '0.5' => '0.5',
          '0.4' => '0.4',
          '0.3' => '0.3',
          '0.2' => '0.2',
          '0.1' => '0.1',
          '0' => '0',
      );
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Text align
  |---------------------------------------------------------------------------------------------------
  */
  public static function text_align( $more_items = array() ){
      $items = array(
          'left' => 'Left',
          'right' => 'Right',
          'center' => 'Center',
          'justify' => 'Justify',
          //'initial' => 'Initial',
          //'inherit' => 'Inherit',
      );
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Font style
  |---------------------------------------------------------------------------------------------------
  */
  public static function font_style( $more_items = array() ){
      $items = array(
          'normal' => 'Normal',
          'italic' => 'Italic',
          'oblique' => 'Oblique',
          //'initial' => 'Initial',
          //'inherit' => 'Inherit',
      );
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Text align
  |---------------------------------------------------------------------------------------------------
  */
  public static function line_height( $more_items = array() ){
      $items = array(
          '' => __( 'Default',   'textdomain' ),
          '1' => __( '1',   'textdomain' ),
          '1.25' => __( '1.25',   'textdomain' ),
          '1.5' => __( '1.5',   'textdomain' ),
          '1.75' => __( '1.75',   'textdomain' ),
          '2' => __( '2',   'textdomain' ),
          '2.25' => __( '2.25',   'textdomain' ),
          '2.5' => __( '2.5',   'textdomain' ),
          '2.75' => __( '2.75',   'textdomain' ),
          '3' => __( '3',   'textdomain' ),
          '3.25' => __( '3.25',   'textdomain' ),
          '3.5' => __( '3.5',   'textdomain' ),
          '3.75' => __( '3.75',   'textdomain' ),
          '4' => __( '4',   'textdomain' ),
          '4.25' => __( '4.25',   'textdomain' ),
          '4.5' => __( '4.5',   'textdomain' ),
          '4.75' => __( '4.75',   'textdomain' ),
          '5' => __( '5',   'textdomain' ),
          '5.25' => __( '5.25',   'textdomain' ),
          '5.50' => __( '5.50',   'textdomain' ),
          '5.75' => __( '5.75',   'textdomain' ),
          '6' => __( '6',   'textdomain' ),
      );
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Text align
  |---------------------------------------------------------------------------------------------------
  */
  public static function font_weight( $more_items = array() ){
      $items = array(
          '' => __( 'Default',   'textdomain' ),
          'FontWeight.w100' => __( 'Thin 100',   'textdomain' ),
          'FontWeight.w200' => __( 'Extra 200 Light',   'textdomain' ),
          'FontWeight.w300' => __( 'Light 300',   'textdomain' ),
          'FontWeight.w400' => __( 'Regular 400',   'textdomain' ),
          'FontWeight.w500' => __( 'Medium 500',   'textdomain' ),
          'FontWeight.w600' => __( 'Semi 600 Bold',   'textdomain' ),
          'FontWeight.w700' => __( 'Bold 700',   'textdomain' ),
          'FontWeight.w800' => __( 'Extra 800 Bold',   'textdomain' ),
          'FontWeight.w900' => __( 'Black 900',   'textdomain' ),
      );
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Text align
  |---------------------------------------------------------------------------------------------------
  */
  public static function font_size( $more_items = array() ){
      $items = array(
          '' => __( 'Default',   'textdomain' ),
          '8' => '8',
          '9' => '9',
          '10' => '10',
          '11' => '11',
          '12' => '12',
          '13' => '13',
          '14' => '14',
          '15' => '15',
          '16' => '16',
          '17' => '17',
          '18' => '18',
          '19' => '19',
          '20' => '20',
          '21' => '21',
          '22' => '22',
          '23' => '23',
          '24' => '24',
          '25' => '25',
          '26' => '26',
          '27' => '27',
          '28' => '28',
          '29' => '29',
          '30' => '30',
          '31' => '31',
          '32' => '32',
          '33' => '33',
          '34' => '34',
          '35' => '35',
          '36' => '36',
          '37' => '37',
          '38' => '38',
          '39' => '39',
          '40' => '40',
          '41' => '41',
          '42' => '42',
          '43' => '43',
          '44' => '44',
          '45' => '45',
          '46' => '46',
          '47' => '47',
          '48' => '48',
          '49' => '49',
          '50' => '50',
      );
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Text transform
  |---------------------------------------------------------------------------------------------------
  */
  public static function text_transform( $more_items = array() ){
      $items = array(
          '' => __( 'Default',   'textdomain' ),
          'uppercase' => __( 'UPPERCASE',   'textdomain' ),
          'lowercase' => __( 'lowercase',   'textdomain' ),
          'capitalize' => __( 'Capitalize',   'textdomain' ),
      );
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Countries
  |---------------------------------------------------------------------------------------------------
  */
  public static function countries_icons( $more_items = array() ){
      $countries = include JPRESS_DIR . 'includes/data/countries-icons.php';
      $items = array();
      foreach( $countries as $country ){
          $value = $country['value'];
          $option = $country['option'];
          if( isset( $country['icon'] ) ){
              $icon = $country['icon'];
              $option = "<i class='{$icon}'></i>" . $option;
          }
          $items[$value] = $option;
      }
      return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Font Awesome Icons with text
  |---------------------------------------------------------------------------------------------------
  */
  public static function icons( $more_items = array() ){
    $icons = false;

    switch(true) {
      case Functions::is_fontawesome_version( '5.x' ) === true:
        $icons = include JPRESS_DIR . 'includes/data/fa-5.15.1/icons-font-awesome-5.15.1.php';
        break;
    }

    if ($icons === false) {
      $icons = include JPRESS_DIR . 'includes/data/icons-font-awesome.php';
    }

    $items = array();

    foreach ( $icons as $key => $icon ) {
      $items[$icon] = "<i class='$icon'></i>";
    }

    return Functions::nice_array_merge( $more_items, $items );
  }

  /*
  |---------------------------------------------------------------------------------------------------
  | Font Awesome Icons
  |---------------------------------------------------------------------------------------------------
  */
  public static function icon_fonts( $more_items = array() ){
    // if( Functions::is_fontawesome_version( '5.x' ) ){
    //     $icons = include JPRESS_DIR . 'includes/data/icons-font-awesome-5.6.3.php';
    // } else{
    //     $icons = include JPRESS_DIR . 'includes/data/icons-font-awesome.php';
    // }

    $icons = include JPRESS_DIR . 'includes/data/icons-spotlayer-framework.php';
    $items = array();

    foreach( $icons as $k => $icon ){
      $items[$k] = "<i class='$icon'></i>";
    }

    // NOTE: Merge FontAwesome Icons by Default
    $more_items = static::icons($more_items);

    return Functions::nice_array_merge( $items, $more_items );
  }

  /*
	|---------------------------------------------------------------------------------------------------
	| All countries
	|---------------------------------------------------------------------------------------------------
	*/
  public static function countries( $more_items = array() ){
    $countries = include JPRESS_DIR . 'includes/data/countries.php';
    return Functions::nice_array_merge( $more_items, $countries );
  }

  /*
	|---------------------------------------------------------------------------------------------------
	| EU (European Union) Countries
	|---------------------------------------------------------------------------------------------------
	*/
  public static function eu_countries( $more_items = array() ){
    $eu_countries = include JPRESS_DIR . 'includes/data/eu-countries.php';
    return Functions::nice_array_merge( $more_items, $eu_countries );
  }
}
