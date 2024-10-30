<html>
<head>
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
</head>
<body>
<?php
          $search_raw = $_POST['settings'];
          $explode = explode('|', $search_raw);
          $t = 0;
          $search = array();
          while ($t < count($explode) ) {
            if ($explode[$t] != ''){
              $search[] .= strtolower($explode[$t]);
            }
            $t++;
         }

         $content = $_POST['text'];

         $content = strtolower($content);
         $content = str_replace('</p><p>', '</p> <p>', $content);
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
         // count the total number of words
         $total_words = count($wordlist);
         $wordlist=array_merge($wordlist,$wordlist2,$wordlist3);
         // Build an array of the unique words and number of times they occur.
         $a = array_count_values( $wordlist );


         // Sort the keys alphabetically.
         ksort( $a );
         echo '<table class="chart">';
    	 echo '	<tr>
    	  			<th scope="col">Keyword </th>
    	  			<th scope="col">Count </th>
                    <th scope="col">Density </th>
    	  		</tr>';
         // Assign a font-size to the word based on frequency of use.
         foreach ($a as $word => $count) {
             // check if the current word is in the array
             if (in_array($word, $search)) {
                // calculate the keyword density
                $density = round( (( $count * 100 ) / $total_words), 4);

                // set the styles based on density value
                if ($density > 9.9) {
                  $style = "color: red; font-weight: bold;";
                } else if ( ($density >= 1 ) && ($denstiy <= 3) ) {
                  $style = "color: green;";
                } else {
                  $style = "color: black;";
                }

                 // The keyword needs to be referenced 30 or more times to register.
                 echo '<tr><td width=100px><span style="' . $style . '">' . $word . '</span></td><td width=55px><span style="' . $style . '">' . $count . '</span></td><td width=69px><span style="' . $style . '">' . $density . '%</span></td></tr>';
             }

         }
         echo '<tr><td colspan="3">Total words in post : ' . $total_words . '</td></tr>';
         echo '</table>';
?>

<div="updated"></div>
</body>
</html>
