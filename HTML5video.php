<?php
/*
 * An HMTL5 Video Player
 *  - Self-contained entirely to the site
 *  - Video movies are uploaded and stored on the local site
 *  - No links to video hosted on other 3rd party sites (e.g. YouTube)
 *  - Supports mp4, ogv, and webm file extensions
 *  - Plays YouTube video
 *  - iPad, iPhone, and Android compatible
 *  - Internet Explorer, Safari, Opera, Firefox, and Chrome Compatible
 *  - Set width and height option
 *  - Autoplay option
 * 
 * variable:  $wgHTML5VideoPath;
 *     alternate path to local videos
 *     
 * Copyright (c) 2011 William S. Howard
 *
 * Special thanks to website: http://camendesign.com/code/video_for_everybody
 * Special thanks to website: http://fuzzytolerance.info/code/creating-a-html5-video-extension-for-mediawiki/
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 * 
 * Unlimited license is granted to everyone to use and modify the code, 
 * except that the credits must remain unaltered.
*/
 
if ( !defined( 'MEDIAWIKI' ) )
    die( 'This is a MediaWiki extension, and must be run from within MediaWiki.' );

# Credits
$wgExtensionCredits['parserhook'][] = array(
    'path'        => __FILE__,
    'name'        => 'HTML5 Video',
    'author'      => array('[http://wheretosee.org Stamps Howard], [http://www.stabilitytech.com William S. Howard]'),
    'url'         => 'http://www.Stabilitytech.com/',
    'description' => 'Creates a site-contained HTML5 Video Player and video library, without any links to video stored on 3rd party sites (such as YouTube).' .
						' It will also play YouTube and Google video. And it works on iPads, iPods, and Android mobile devices.',
    'version'     => '1.0'
);
 
$wgHooks['ParserFirstCallInit'][] = 'html5videoinit';
 
function html5videoinit( $parser ) {
    $parser->setHook( 'HTML5video', 'html5videorender' );
    return true;
}


function html5videorender( $input, $args) {
	global $wgScriptPath;
	global $wgHTML5VideoPath;
	
	$videosource = array();
  $videosource['youtube' ] = 'http://www.youtube.com/v/' . $input;
  $videosource['HTML5'   ] = $input;
	
	$input_array = explode('|', htmlspecialchars($input));
  $movie     = current($input_array);
	$caption = next($input_array);
  $width  = isset($args['width']) ? $args['width'] : '320';
  $height = isset($args['height']) ? $args['height'] : '240';
	$type   = isset($args['type'],$videosource[$args['type']]) ? $args['type'] : 'HTML5';

	$show_link = isset($args['link'] ) ? $args['link'] : '0';
	$show_info= isset($args['debug'] ) ? $args['debug'] : '0';
	$url = isset($args['url']) ? $args['url'] : ''; # not yet implemented
	
	if( strtolower($type) == 'html5')
  {
  	$video_path = $wgScriptPath . '/extensions/HTML5video/videos/';
  	if ( isset($wgHTML5VideoPath)) {
  		$video_path = $wgHTML5VideoPath;
  	}
  	$autoplay = (isset($args['autoplay']) &&  $args['autoplay'] == 'true') ? 'autoplay' : ' ';
    if (is_numeric($width))
    {
    		$size = ' width="' . $width . '" height="' . $height . '" ';
    }
    else
    {
   		$size = ' width="' . $width . '" ';
    }

    $source =
    	'<source src="' . $video_path . $movie . '.mp4" type="video/mp4" />' .     /* Safari / iOS video */
    	'<source src="' . $video_path . $movie . '.ogv" type="video/ogg" />' .     /* Firefox, Opera, Chrome */
    	'<source src="' . $video_path . $movie . '.webm" type="video/webm" />'     /* New Open Standard */
    	;

    $output = '<video ' . $size . ' autobuffer controls ' . $autoplay . '   preload="auto" >' .
    			$source .
    			'</video>';
    	
    if ( $caption) {
    	// TODO: add CSS for formatting
    	$output .= "<p>$caption </p>";
    }
    if ( $show_link)
    {
		$output .=  '<p><a href="' . $video_path . $movie . '.mp4" >Download .mp4 Video</a></p>';
		$output .=  '<p><a href="' . $video_path . $movie . '.ogv" >Download .ogv Video</a></p>';
		$output .=  '<p><a href="' . $video_path . $movie . '.webm" >Download .webm Video</a></p>';
    }
    if ( $show_info)
    {
		$output .=  "Input value is " . $input . ", ";
		$output .=  "Movie value is " . $movie . ", ";
		$output .=  "Autoplay value is " . $autoplay . ", ";
		$output .=  "Width value is " . $width . ", ";
		$output .=  "Height value is " . $height . ", ";
		$output .=  "Type value is " . $type . ", ";
		$output .=  "video path  value is " . $video_path . ", ";
    }
    return  $output ;
	} // HTML 5
	elseif( strtolower($type) == 'youtube')
	{
			$autoplay = ($args['autoplay'] == 'true') ? '1' : '0';
			
			$output = '<object width="' . $width . '" height="' . $height . '" >' .  
                      ' <embed src="' . $videosource[$type] . '&autoplay=' . $autoplay . '" type="application/x-shockwave-flash" wmode="transparent"' .
                      '  width="' . $width . '" height="' . $height . '" allowfullscreen="true">' .
                      ' </embed></object>';
	return $output;
	}
	
}

		
			