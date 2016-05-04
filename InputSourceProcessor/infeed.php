<?php
error_reporting(E_ALL | E_STRICT);

$stopwords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount", "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as", "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");

$terms = array("gunman","shooting","gunshots","gun shots","explosion","mass shooting","hiding","heard gun","killed","dead","{NUM} killed","{NUM} dead","you are safe","everyone is safe","everyone in","praying for","stay safe","the victims","going on in","pray for","is happening","happening in","OMG","the people in","for the people","for everyone in","those in","Shooting In","Whats happening","happening","everyone in","shooting in","on in","for those in","safe","the victims","stay","happened in","Please pray for","Please pray","the people","for everyone","praying for all","thoughts are with","prayers are with","horrible","right now","going on","My prayers","My thoughts","hope everyone in","scary","safe in","people of","hope everyone","so sad","everyone is safe");

$PPN = Array();
$PPL = Array();



$mysql = new mysqli("localhost", "cs125", "cs125", "cs125");
$mysql->set_charset("utf8");

$type = 0;
$text = "";
$time = 0;
$addsource = $mysql->prepare("INSERT INTO cs125.sources (`type`,`text`,`time`,`score`) VALUES (?,?,?,NULL)");
$addsource->bind_param("isi", $type, $text, $time);

$addplace = $mysql->prepare("INSERT IGNORE INTO cs125.places (`pid`,`lat`,`long`,`name`,`admin0`,`admin1`,`admin2`,`population`,`pins`) VALUES (?,?,?,?,?,?,?,?,0)");
$addplace->bind_param("iddssssi", $pid, $lat, $long, $name, $admin0, $admin1, $admin2, $pop);



if( file_exists("data.cache") ) {
	list($PPN, $PPL) = unserialize( file_get_contents("data.cache") );
}
//~ print_r( $PPN);
//~ exit;


echo "\n\nListening...\n\n";

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_bind($socket, '127.0.0.1', 4125);

$from = '';
$port = 0;
while( true) {
	socket_recvfrom($socket, $msg, 4096, 0, $from, $port);

	list($timestamp, $msgtype, $msg) = explode("|", $msg, 3);
	$type = intval($msgtype);
	$time = intval($timestamp);


	$msg = trim(preg_replace('/\s+/', ' ', $msg));
	$ucw = strlen(preg_replace('![^A-Z]+!', '', $msg));
	if( $ucw > 6 ) $msg = strtolower($msg);

	$words = explode(" ", $msg);
	$wc = count($words);

	$score = 0;
	$hasplace = false;

	for($i=0; $i<$wc && $hasplace==false; $i++) {
		$w = '';
		for($n=0; $n<3 && ($n+$i)<$wc && $hasplace==false; $n++) {
			if( $n!=0 ) $w .= " ";
			$w .= $words[$i+$n];
			$lw = strtolower($w);

			if( strlen($w) > 3 && !in_array($lw, $stopwords) && isset($PPN[$lw]) ) {
				foreach( $PPN[$lw] as $pp ) {
					list($id, $level) = $pp;
					if( $level == 4 ) {
						$hasplace = true;
						break;
					}
				}
			}
		}
	}

	if( $hasplace && $time > 1457317042 ) {
		$text = $msg;
		echo $text."\n\n";
		$addsource->execute();
	}

}

?>
