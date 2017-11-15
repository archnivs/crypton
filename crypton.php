<?php
/**
 * Plugin Name:       Crypton
 * Description:       cryptocurrency
 * Version:           1.0.0
 * Author:            Alvin
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

if( ! defined( 'ABSPATH' ) ) exit;

define( 'CRYPTON_URL', plugin_dir_url(  __FILE__ ) );
define( 'CRYPTON_DIR', plugin_dir_path( __FILE__ ) . '/' );


add_action('wp_enqueue_scripts', 'crypton_enqueue_scripts');
function crypton_enqueue_scripts() {

	global $post;
	if(empty($post)) return;

  	if ( ! has_shortcode( $post->post_content, 'crypton' ) ) return;

  	wp_enqueue_style('crypton-coins', CRYPTON_URL . '/assets/coins/cryptocoins.css' );
  	wp_enqueue_style('crypton-coins-color', CRYPTON_URL . '/assets/coins/cryptocoins-colors.css' );
  	wp_enqueue_style('crypton-style', CRYPTON_URL . '/assets/style.css' );


  	wp_enqueue_script('crypton-style', CRYPTON_URL . '/assets/crypton.js', array(), false, true );

}

add_shortcode('crypton', 'sc_crypton');
function sc_crypton($atts, $content = null){
    extract(shortcode_atts(array(
          'limit'	=> '',
          'convert' => '',
          'start'	=> ''
       ), $atts));


	$response = get_curl_api_response('https://api.coinmarketcap.com/v1/ticker/?limit=10');

    ob_start();

    ?>
    
    <div id="cryptonCC" class="crypton-main">
    	<div  class="crypton-carousel">
		<?php foreach($response as $coin) : ?>
			
			<?php

				$changes_24h = (float) $coin->percent_change_24h;

				$changes_status = ( $changes_24h < 0 ) ? "status_down" : "status_up";

				$price = round($coin->price_usd, 2);
			?>

			<div id="crypton-<?php echo $coin->id; ?>" class="crypton-coin">
				<i class="cc <?php echo $coin->symbol; ?>" title="<?php echo $coin->name; ?>"></i>
				<span class="crypton-coin-name"><?php echo $coin->name; ?> </span>
				<span class="crypton-coin-changes <?php echo $changes_status; ?>">(<?php echo $changes_24h; ?>)</span>
				<span class="crypton-coin-price"><?php echo '$' . $price; ?></span>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
	
    <?php

    return ob_get_clean();
}


function get_curl_api_response($url) {

	$curl = curl_init();

    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($curl, CURLOPT_FAILONERROR, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    /*curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => $url
	));*/

    $result = curl_exec ($curl);
    curl_close ($curl);

    
	return json_decode($result);
   

}