<?php
error_reporting(E_ALL | E_STRICT);

$stopwords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount", "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as", "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");

$terms = array("gunman","shooting","gunshots","gun shots","explosion","mass shooting","hiding","heard gun","killed","dead","{NUM} killed","{NUM} dead","you are safe","everyone is safe","everyone in","praying for","stay safe","the victims","going on in","pray for","is happening","happening in","OMG","the people in","for the people","for everyone in","those in","Shooting In","Whats happening","happening","everyone in","shooting in","on in","for those in","safe","the victims","stay","happened in","Please pray for","Please pray","the people","for everyone","praying for all","thoughts are with","prayers are with","horrible","right now","going on","My prayers","My thoughts","hope everyone in","scary","safe in","people of","hope everyone","so sad","everyone is safe");
$common = array("hope", "pray", "hiding", "police", "officer", "time", "shooting", "star", "nice");
$verycommon = array("the","of","and","a","to","in","is","be","that","was","he","for","it","with","as","his","I","on","have","at","by","not","they","this","had","are","but","from","or","she","an","which","you","one","we","all","were","her","would","there","their","will","when","who","him","been","has","more","if","no","out","do","so","can","what","up","said","about","other","into","than","its","time","only","could","new","them","man","some","these","then","two","first","May","any","like","now","my","such","make","over","our","even","most","me","state","after","also","made","many","did","must","before","back","see","through","way","where","get","much","go","well","your","know","should","down","work","year","because","come","people","just","say","each","those","take","day","good","how","long","Mr","own","too","little","use","US","very","great","still","men","here","life","both","between","old","under","last","never","place","same","another","think","house","while","high","right","might","came","off","find","states","since","used","give","against","three","himself","look","few","general","hand","school","part","small","American","home","during","number","again","Mrs","around","thought","went","without","however","govern","don't","does","got","public","United","point","end","become","head","once","course","fact","upon","need","system","set","every","war","put","form","water","took","program","present","government","thing","told","possible","group","large","until","always","city","didn't","order","away","called","want","eyes","something","unite","going","face","far","asked","interest","later","show","knew","though","less","night","early","almost","let","open","enough","side","case","days","yet","better","nothing","tell","problem","toward","given","why","national","room","young","social","light","business","president","help","power","country","next","things","word","looked","real","John","line","second","church","seem","certain","big","Four","felt","several","children","service","feel","important","rather","name","per","among","often","turn","development","keep","family","seemed","white","company","mind","members","others","within","done","along","turned","god","sense","week","best","change","kind","began","child","ever","law","matter","least","means","question","act","close","mean","leave","itself","force","study","York","action","it's","door","experience","human","result","times","run","different","car","example","hands","whole","center","although","call","Five","inform","gave","plan","woman","boy","feet","provide","taken","thus","body","play","seen","today","having","cost","perhaps","field","local","really","am","increase","reason","themselves","clear","I'm","information","figure","late","above","history","love","girl","held","special","move","person","whether","college","sure","probably","either","seems","cannot","art","free","across","death","quite","street","value","anything","making","past","brought","moment","control","office","heard","problems","became","full","near","half","nature","hold","live","available","known","board","effect","already","Economic","money","position","believe","age","together","shall","TRUE","political","court","report","level","rate","air","pay","community","complete","music","necessary","society","behind","type","read","idea","wanted","land","party","class","organize","return","department","education","following","mother","sound","ago","nation","voice","six","bring","wife","common","south","strong","town","book","students","hear","hope","able","industry","stand","tax","west","meet","particular","cut","short","stood","university","spirit","start","total","future","front","low","century","Washington","usually","care","recent","evidence","further","million","simple","road","sometimes","support","view","fire","says","hard","morning","table","left","situation","try","outside","lines","surface","ask","modern","top","peace","personal","member","minutes","lead","schools","talk","consider","gone","soon","father","ground","living","months","therefore","America","started","longer","Dr","dark","various","finally","hour","north","third","fall","greater","pressure","stage","expected","secretary","needed","That's","kept","eye","values","union","private","alone","black","required","space","subject","english","month","understand","I'll","nor","answer","moved","amount","conditions","direct","red","student","rest","nations","heart","costs","record","picture","taking","couldn't","hours","deal","forces","everything","write","coming","effort","market","island","wall","purpose","basis","east","lost","St","except","letter","looking","property","Miles","difference","entire","else","color","followed","feeling","son","makes","friend","basic","cold","including","single","attention","note","cause","hundred","step","paper","developed","tried","simply","can't","story","committee","inside","reached","easy","appear","include","accord","Actually","remember","beyond","dead","shown","fine","religious","continue","ten","defense","getting","Central","beginning","instead","river","received","doing","employ","trade","terms","trying","friends","sort","administration","higher","cent","expect","food","building","religion","meeting","ready","walked","follow","earth","speak","passed","foreign","NATURAL","medical","training","County","list","floor","piece","especially","indeed","stop","wasn't","England","difficult","likely","Suddenly","moral","plant","bad","club","needs","international","working","countries","develop","drive","reach","police","sat","charge","farm","fear","test","determine","hair","results","stock","trouble","happened","growth","square","William","cases","effective","serve","miss","involved","doctor","earlier","increased","being","blue","hall","particularly","boys","paid","sent","production","district","using","thinking","concern","Christian","press","girls","wide","usual","direction","feed","trial","walk","begin","weeks","points","respect","certainly","ideas","industrial","methods","operation","addition","association","combine","knowledge","decided","temperature","statement","Yes","below","game","nearly","science","directly","horse","influence","size","showed","build","throughout","questions","character","foot","Kennedy","firm","reading","husband","doubt","services","according","lay","stay","programs","anyone","average","French","spring","former","summer","bill","lot","chance","due","comes","army","actual","Southern","neither","relate","rise","evening","normal","wish","visit","population","remain","measure","merely","arrange","condition","decision","account","opportunity","pass","demand","strength","window","active","deep","degree","ran","western","E","sales","continued","fight","heavy","arm","standard","generally","carry","hot","provided","serious","led","wait","hotel","opened","performance","maybe","station","changes","literature","marry","claim","works","bed","wrong","main","unit","George","hit","planning","supply","systems","add","chief","officer","Soviet","pattern","stopped","price","success","lack","myself","truth","freedom","manner","quality","gun","manufacture","clearly","share","movement","length","ways","burn","forms","Organization","break","somewhat","efforts","cover","meaning","progress","treatment","beautiful","placed","happy","attack","apparently","blood","groups","carried","sign","radio","dance","I've","regard","man's","train","herself","numbers","corner","REACTION","immediately","language","running","recently","shake","larger","lower","machine","attempt","learn","couple","race","audience","Oh","middle","brown","date","health","persons","understanding","arms","daily","suppose","additional","hospital","pool","technical","served","declare","described","current","poor","steps","reported","sun","based","produce","determined","receive","park","staff","faith","responsibility","Europe","latter","British","season","equal","learned","practice","green","writing","ones","choice","fiscal","term","watch","scene","activity","product","types","ball","heat","clothe","lived","distance","parent","letters","returned","forward","obtained","offer","specific","straight","fix","division","slowly","shot","poet","seven","moving","mass","plane","proper","propose","drink","obviously","plans","whatever","afternoon","figures","parts","approve","saying","born","immediate","fame","gives","extent","justice","cars","mark","pretty","opinion","ahead","glass","refuse","enter","completely","send","desire","judge","none","waiting","popular","Democratic","film","mouth","Corps","importance");

$termcount = count($terms);
if( file_exists("data.cache") ) {
	list($PPN, $PPL) = unserialize( file_get_contents("data.cache") );

}

$mysql = new mysqli("localhost", "cs125", "cs125", "cs125");
$mysql->set_charset("utf8");

if( count($argv) == 3 ) {
	$cpu_mc = intval($argv[1]);
	$cpu_c = intval($argv[2]);
	$unscored = $mysql->prepare("SELECT * FROM cs125.sources WHERE score IS NULL && MOD(sid,$cpu_mc)=$cpu_c");
	echo "Parser $cpu_c of $cpu_mc\n";
}else{
	$cpu_mc = 1;
	$cpu_c = 1;
	$unscored = $mysql->prepare("SELECT * FROM cs125.sources WHERE score IS NULL ORDER BY TIME ASC"); // `text` LIKE \"%Ankara%\"
}


$setscore = $mysql->prepare("UPDATE cs125.sources SET score=? WHERE sid=?");
$delpins = $mysql->prepare("DELETE FROM cs125.pins WHERE sid=?");

$addpins = $mysql->prepare("INSERT INTO `cs125`.`pins` (`pid`,`sid`,`score`,`time`) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE score=?");




$score = 0;
$sid = 0;
$setscore->bind_param("di", $score, $sid);

//$sid = 0;
//$delpins->bind_param("i", $sid);


$sid = 0;
$pinscore = 0;
$time = 0;

$addpins->bind_param("iidid", $pid, $sid, $pinscore, $time, $pinscore);


$WORDLIST = file("/usr/share/dict/words", FILE_IGNORE_NEW_LINES);

unset( $PPN["pray"] );
unset( $PPN["hope"] );
unset( $PPN["police"] );
unset( $PPN["officer"] );
unset( $PPN["obama"] );

while( true) {

	$unscored->execute();

    /* bind result variables */
    $unscored->bind_result($sid, $type, $text, $time, $score);

	$unscored->store_result();
    /* fetch value */
    $TC = 0;
    while($unscored->fetch()) {
		$TC++;
		if( $TC % 100 ==0 ){
			echo "Parser $cpu_c of $cpu_mc at  $TC\n";
		}
		$msg = trim(preg_replace('/\s+/', ' ', $text));
		$ucw = strlen(preg_replace('![^A-Z]+!', '', $msg));
		if( $ucw > 6 ) $msg = strtolower($msg);

		$words = explode(" ", $msg);
		$wc = count($words);


		$score = 0.0;

		if( $wc < 4 ) {
			if( ! $setscore->execute() ) {
				printf("Errormessage: %s\n", $setscore->error);
			}

			continue;
		}


		$places = Array();

		$used = Array();

		$loc_word = false;
		$prev = Array();

		for($i=0; $i<$wc; $i++) {
			$w = '';
			$ws = 0;

			for($n=0; $n<3 && ($n+$i)<$wc; $n++) {
				if( $n!=0 ) $w .= " ";
				$w .= $words[$i+$n];
				$lw = strtolower($w);

				$next = Array();
				if( $loc_word  ) $ws = 2.0;
				if( $w == "in" ) $loc_word = true; else $loc_word = false;

				if( !in_array($lw, $used) ) {
					$used[] = $lw;
					if(!in_array($words[$i+$n], $WORDLIST)) {
						$ws += 1;
					}else{
						$ws += 0.05;
					}



					if( strlen($w) > 3 && !in_array($lw, $stopwords) && isset($PPN[$lw]) ) {
						$ids = $PPN[$lw];
						$idlen = count($ids);
						foreach($ids as $idset) {
							list($id, $level) = $idset;
							if( !in_array($id, $next) ) $next[] = $id;
							if( !isset($places[$id]) ) $places[$id]=0;

							$m = 1.0/$idlen;
							$m += $level;

							if( in_array($id, $prev) ) {

								$m += 2.0;	// Extra score for repeating the same location aka: Paris, France
							}else if( in_array($lw, $common) && in_array($lw, $verycommon) ) {
								continue;	// Skip very common words that only have 1 location
							}

							if( ucwords($lw)==$w ) $m += 0.7*($n+1);	// Extra score for proper captitalizations
							if( in_array($lw, $common) ) $m /= 2.0;	// Half score for slightly common
							$places[ $id ] += $ws*$m;
						}
					}
				}
				$prev = $next;



				if( ($index=array_search($lw, $terms) )!==false ) {
					$score += (1.0 + log($termcount-$index));
				}

			}

		}

		//if( ! $delpins->execute() ) {
		//	printf("Errormessage: %s\n", $delpins->error);
		 //}


		if( $score > 0.5  ) {
			if( $score > 10.0 ) $score = 10;


			$sum = 0;
			foreach( $places as $p=>$c ) {
				$places[$p] = $c*floatval(log($PPL[$p][14]));
				$sum +=$places[$p];
			}

			$sum = $sum * ($wc-3);

			if( $sum > 0 ) {
				foreach( $places as $p=>$c ) {
					$places[$p] = $c/$sum;
				}

				//echo "\n\n$msg";

				foreach( $places as $p=>$c ) {
					//$pn = $PPL[$p][1];
					$pid = $p;
					//$pin = $score*$c;
					//echo "\n\t$pn => $c";
					$pinscore = $score*$c;
					if( $pinscore > 0.0001 ) {
						if( ! $addpins->execute() ) {
							printf("Errormessage: %s\n", $addpins->error);
						}
					}
				}
			}
			//echo "\n\tScore=$score\n";
		}
		//echo "Settting[$sid]=";
		 if( ! $setscore->execute() ) {
			printf("Errormessage: %s\n", $setscore->error);
		 }



	}




	if( $cpu_mc > 1 ) break;
	sleep(5);
}

?>
