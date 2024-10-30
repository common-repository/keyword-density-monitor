<?php
/*
Plugin Name: Digitalquill Keywords Monitor
Plugin URI: http://keyworddensityplugin.digitalquill.co.uk
Version: 1.2
Author: Digitalquill
Author URI: http://www.digitalquill.co.uk/wordpressplugins/
Description: Allows monitoring and analisys of keyword density

*/

// DigitalQuill Keyword Monitor plugin
if (!class_exists('dqKeywords')) {
    class dqKeywords	{

        private $_optionName = 'dqkeyword_settings';

		/**
		* PHP 4 Compatible Constructor
		*/
		function dqKeywords(){
		  $this->__construct();
        }

		/**
		* PHP 5 Constructor
		*/
		function __construct(){
			global $wpdb, $wp_version;

            // check to make sure wp version is above 2.7
       		$exit_msg= 'DigitalQuill Keywords requires WordPress 2.8 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';

            if (version_compare($wp_version,"2.8","<"))
    		{
	    		exit ($exit_msg);
	    	}

            // if we get this far, wp version is 2.8 or above to initialise the script
            $this->uri = WP_PLUGIN_URL."/".$this->directory;

            // setup the plugin
            add_action('admin_head', array( $this, 'admin_head' ));
		    add_action( 'admin_init', array( $this, 'admin_init' ));
            add_action( 'admin_menu', array($this, 'dqkeyword_setup'));


       }

	   function dqkeyword_widget_setup() {
			wp_add_dashboard_widget( 'dqkeyword_stats_widget', __( 'Keyword Stats' ), array( $this, 'dqkeyword_stats_widget') );
	   }

       function dqkeyword_setup() {
            add_options_page(__('Keyword Setup'), __('Keyword Setup'), 'manage_options', 'dqkeyword', array($this, 'displaySettingsPage'));

        }

        function displaySettingsPage() {
            include ('pages/settings.php');
        }

        function getSettings() {
            if (null === $this->settings) {
                $this->settings = get_option($this->_optionName, array());
            }
            return $this->settings;
        }

        function saveSettings($settings) {
            if (!is_array($settings)) {
                return;
            }
            update_option($this->_optionName, $settings);
        }

        function deleteSettings() {
            delete_option($this->_optionName);
        }

	   function admin_init( ) {

            if (isset($_POST['save-settings']) && current_user_can('manage_options') && check_admin_referer('save-settings')) {

                // test for posted custom values existing in array $name
                if (isset($_POST['name'])) {
                // we have a posted custom values
                    if ( (is_array($_POST['name']))  ) {
                        $custom_name = '';
                        // okay lets build the arraya the database can use, one for name, one for values
                        foreach($_POST['name'] as $key => $nameValue) {
                            $custom_name .= $nameValue . '|';
                        }
                    }
                }
                $settings['keywords'] = $custom_name;
                $this->saveSettings($settings);

                // now update the keyword stats
                $this->keywordStats();

                wp_redirect(admin_url('options-general.php?page=dqkeyword'));
                exit();
            }

            //add_filter('mce_external_plugins', array( $this, 'add_tinymce_plugin') );
            //add_filter('mce_buttons', array( $this, 'add_tinymce_button') );

        if(is_admin()) {
		    add_action('wp_dashboard_setup', array( $this, 'dqkeyword_widget_setup') );
            add_action('submitpost_box', array ( $this, 'dqkeyword_sidebar') );
            add_action('submitpage_box', array ( $this, 'dqkeyword_sidebar') );
            add_action('publish_post', array ( $this, 'keywordStats') );
            add_action('publish_page', array ( $this, 'keywordStats') );

		}

   }
   
       function dqkeyword_sidebar() {
         //echo 'heelo this is a sidebar';
        ?>
<style>
.chart td.value {
	background-image: url(img/gridline58.gif);
	background-repeat: repeat-x;
	background-position: left top;
	border-left: 1px solid #e5e5e5;
	border-right: 1px solid #e5e5e5;
	padding: 0;
	background-color: transparent;
	}
.chart td {
	padding: 4px 6px;
	border-bottom: 1px solid #e5e5e5;
	border-left: 1px solid #e5e5e5;
	background-color: #fff;
	}
.chart td.value img {
	vertical-align: middle;
	margin: 5px 5px 5px 0;
	border-right: 1px solid #828282;
	border-bottom: 1px solid #828282;
	}
.chart th {
	text-align: left;
	vertical-align: top;
	border-bottom: 1px solid #e5e5e5;
	}
.chart .auraltext {
	position: absolute;
	font-size: 0;
	left: -1000px;
	}
table.chart {
	width: 100%;
	}
.chart caption {
	font-size: 90%;
	font-style: italic;
	}
    </style>
              <div id="dqkeyword_content" class="postbox ">
		      <div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span>Keyword Density</span></h3>
		      <div class="inside">
              <div id="update" ><p align="right"><a href="javascript:;" onclick="updateRaw(); return false;">[Update]</a><br /></p></div>
              <div id="count"> </div>
		      </div>
		      </div>
        <?php

       }

       function keywordStats() {
          global $wpdb;

          $settings = $this->getSettings();
          $search_raw = $settings['keywords'];
          $settings['words'] = 0;
          $settings['total_words'] = 0;
          $explode = explode('|', $search_raw);
          $t = 0;
          $search = array();
          while ($t < count($explode) ) {
            if ($explode[$t] != ''){
              $search[] .= strtolower($explode[$t]);
            }
            $t++;
         }

         // now grab all posts from the database, and recursively, process them
         $string_raw = "SELECT post_content FROM " . $wpdb->prefix . "posts where post_status='publish'";
         $string_result = $wpdb->get_results($string_raw);
         $content = '';
         $wcount=array();
         $total_words=0;
         foreach ($string_result as $string ) {
             $content = $string->post_content . '<br/>';
	         $content = strtolower($content);
	         $content = str_replace('<br/>', ' ', $content);
	         $content = str_replace('<br />', ' ', $content);
	         $content = str_replace('<br>', ' ', $content);
	         $content = str_replace('<br >', ' ', $content);
	
	         // Remove punctuation.
	         $content = strip_tags($content);
	         $wordlist = preg_split('/\s*[\s+\.|\?|,|(|)|\-+|\'|\"|=|;|&#0215;|\$|\/|:|{|}]\s*/i', trim($content));
	         $wordlist2=$wordlist3=array();
	         foreach($wordlist as $k=>$v){
	         	if($k+2<sizeof($wordlist)&&htmlentities($wordlist[$k+2])!='&Acirc;&nbsp;'){
	         		$wordlist3[]=$wordlist[$k].' '.$wordlist[$k+1].' '.$wordlist[$k+2];
	         	}
	         	if($k+1<sizeof($wordlist)&&htmlentities($wordlist[$k+1])!='&Acirc;&nbsp;'){
	         		$wordlist2[]=$wordlist[$k].' '.$wordlist[$k+1];
	         	}
	         }
	         $total_words += count($wordlist);
	         $wordlist=array_merge($wordlist,$wordlist2,$wordlist3);
	         // Build an array of the unique words and number of times they occur.
	         $a = array_count_values( $wordlist );
	
	         // Sort the keys alphabetically.
	         ksort( $a );
	
	         // Assign a font-size to the word based on frequency of use.
	         foreach ($a as $word => $count) {
	             // check if the current word is in the array
	             if (in_array($word, $search)) {
	                 // The keyword needs to be referenced 30 or more times to register.
	                 //$count_words .= $word . ',' . $count . '|';
	                 $wcount[$word]+=$count;
	             }
	
	         }

         }
         foreach($wcount as $word=>$count){
	     	$count_words .= $word . ',' . $count . '|';
	     }
	     foreach($search as $term){
	     	if(!isset($wcount[$term])){
	     		$count_words .= $term . ',0|';
	     	}
	     }
	     $settings['words'] = $count_words;
	     $settings['total_words'] = $total_words;
         $this->saveSettings($settings);

         return true;

       }

	   function dqkeyword_stats_widget() {
		  global $wpdb;

          $settings = $this->getSettings();
          if ($settings['words'] != '') {

            $words_to_show = substr($settings['words'], 0, -1);
            $word_count = $settings['total_words'];
            // we now need to rebuild the fields array, so we can use it through the script
            $words_split = split('[,|]', $words_to_show);
            $words = array();
            $string = '';
            for ($t = 1; $t <= count($words_split); $t+=2) {
                $density = round( (( $words_split[$t] *100 ) / $word_count), 4);

                // set the styles based on density value
                if ($density > 9.9) {
                  $style = "color: red; font-weight: bold;";
                } else if ( ($density > 1 ) && ($denstiy < 3) ) {
                  $style = "color: green;";
                } else {
                  $style = "color: black;";
                }

                $string .= '<tr>
                <td style="width:33%"><span style="' . $style . '">' . $words_split[$t-1] . '</span></td>
                <td style="width:33%"><span style="' . $style . '">' . $words_split[$t] . ' times</span></td>
                <td style="width:33%"><span style="' . $style . '">Denisty: ' . $density . '%</span></td></tr>';

            }
          } else {
            $string = 'No stats available';
          }
    	  ?>
    	  <table style="width:100%" class="download_chart" style="margin-bottom:0" summary="<?php _e('Most Downloaded',"dqKeywords"); ?>" cellpadding="0" cellspacing="0">
    	  		<tr>
    	  			<th scope="col" style="text-align:left">&nbsp;<?php _e('Keyword',"dqKeywords"); ?> </th>
    	  			<th scope="col" style="text-align:left">&nbsp;<?php _e('Count',"dqKeywords"); ?> </th>
                    <th scope="col" style="text-align:left">&nbsp;<?php _e('Density',"dqKeywords"); ?> </th>
    	  		</tr>
    	  <?php
                echo $string;
    	  ?>
          </table>
        <p>&nbsp;</p>
        <p><strong><a href="http://keyworddensityplugin.digitalquill.co.uk/">Keyword Density Monitor</a> brought to you by <a href="http://www.digitalquill.co.uk">Digitalquill</a></strong></p>
        <p>&nbsp;</p>

    	  <?php
	   }




	   function admin_head() {
    	    echo "<link rel='stylesheet' href='".$this->uri."keyword-density-monitor/scripts/colorbox.css' type='text/css' media='all' />";
           ?>
            <script type="text/javascript" >
            jQuery(function($){

                $('#content').keyup(update);
                $('#content').keyup(updateRaw);
            });

            function updateRaw() {
	if(document.getElementById('edButtonPreview').className!=''&&tinyMCE.activeEditor) var content= tinyMCE.activeEditor.getContent({format:'text'});
	else var content= document.getElementById('content').value;
              //content = content.replace(/<\/?[^>]+>/g, "");

                <?php $settings = $this->getSettings();
                $keywords = $settings['keywords'];
             ?>
             var settings = "<?php echo $keywords; ?>";
             jQuery.post("<?php echo bloginfo('home') . '/wp-content/plugins/keyword-density-monitor/pages/keywords.php'; ?>", { text: content, settings: settings},
             function(html) {
document.getElementById('count').innerHTML=html;
//                 jQuery('#count').html(html);
             });
            }

            function update() {
              var newContent = jQuery('#content').val();

             // try an ajax call to count keywords
             <?php $settings = $this->getSettings();
                $keywords = $settings['keywords'];
             ?>
             var settings = "<?php echo $keywords; ?>";
             jQuery.post("<?php echo bloginfo('home') . '/wp-content/plugins/keyword-density-monitor/pages/keywords.php'; ?>", { text: newContent, settings: settings},
             function(html) {
                 jQuery('#count').html(html);
             });

            }


            </script>

           <?php
       }


   }   // end of class descriptor

  } // end of class duplicate detection



// now if class exists, initiate it
if (class_exists('dqKeywords')) {
	$dqKeywords = new dqKeywords();
	
}
function dqKeywords_intercept($arr)
{
$arr['onchange_callback']="updateRaw";
return $arr;
}
add_filter('tiny_mce_before_init', 'dqKeywords_intercept');
?>
