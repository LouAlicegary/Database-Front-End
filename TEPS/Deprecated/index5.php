<?php header('Content-Type: text/html; charset=UTF-8');?>
<?php include("../../dblogin.php");?>

<html>

<head>

<title>TEPS Vocab Database Search</title>

	<style type="text/css" title="currentStyle"> 
		@import "http://www.loualicegary.com/datatables/media/css/demo_page.css";
		@import "http://www.loualicegary.com/datatables/media/css/demo_table.css";
	</style>
		
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
    <script src="http://www.loualicegary.com/datatables/media/js/jquery.dataTables.js"></script>
	<script src="http://www.loualicegary.com/datatables/media/js/jquery.highlight-3.js"></script>
	
	<?php
		$todo=$_POST['todo'];
		$type=$_POST['type'];
	
		if(isset($todo) and $todo=="search"){
			$search_text=$_POST['search_text'];
			$search_text=ltrim($search_text);
			$search_text=rtrim($search_text);
		}
		else {
			$search_text="major";
		}
	?>
	
	
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			
			$('#example').dataTable( {
				"bFilter": false, "aaSorting": [[ 0, "desc" ]], "sDom": '<"top"i>rt<"bottom"lp><"clear"f>', "sPaginationType": "full_numbers"
			} );
			
			$('#highlight-plugin').highlight('<?php echo $search_text ?>');
			
		} );
	</script>
	
</head>

<body>

<?php
	if (!mysql_connect($db_host, $db_user, $db_pwd))
		die("Can't connect to database");
	if (!mysql_select_db($database))
		die("Can't select database");

	echo "<center><h1>TEPS Vocab Database Search</h1>";
	echo "<table border='2' bgcolor='#228822' width='500px'><tr><td>";
	echo "<font color='#FFFFFF'><center>To search the Vocab database, enter any combination of parameters below.</center></font><br/>";
	echo "  <form method=post action=''>
				<center><table class='search_options' padding='0' margin='0' border='0'>
					<tr>
						<td><input type=hidden name=todo value=search><input type=text name=search_text><input type=submit value=Search></td>
					</tr>
					<tr>
						<td width='500px'><input type=radio name=searchtype value=wildcard checked>Wildcard Match</td>
					</tr>
					<tr>
						<td width='500px'><input type=radio name=searchtype value=exact>Exact Match</td>
					</tr>
					<tr bgcolor='#000000' height='5'>
						<td></td>
					</tr>
				</table>
				<table class='search_options' padding='0' margin='0' border='0'>
					<tr>
						<center><h5>Question Classification</h5></center>
					</tr>
					<tr>
						<td width='250px'><input type=radio name=vocabtype value=all checked>Any Classification</td>
						<td width='250px'><input type=radio name=vocabtype value=vo>Vocabulary (VO)</td>
					</tr>
					<tr>
						<td width='250px'><input type=radio name=vocabtype value=co>Collocation (CO)</td>
						<td width='250px'><input type=radio name=vocabtype value=ix>Idiomatic Expression (IX)</td>
					</tr>
					<tr>
						<td width='250px'><input type=radio name=vocabtype value=pv>Phrasal Verb (PV)</td>
						<td width='250px'><input type=radio name=vocabtype value=id>Idiom (ID)</td>
					</tr>
					<tr>
						<td width='250px'><input type=radio name=vocabtype value=ce>Colloquial Expression (CE)</td>
					</tr>					
					<tr bgcolor='#000000' height='5'>
						<td></td>
						<td></td>
					</tr>
				</table>
				<table class='search_options' padding='0' margin='0' border='0'>
					<tr>
						<h5><center>Answer Choice Part of Speech</h5></center>
					</tr>
					<tr>
						<td width='250px'><input type=radio name=grammar value=all checked>Any POS</td>
						<td width='250px'><input type=radio name=grammar value=v>Verb (v)</td>
					</tr>
					<tr>
						<td width='250px'><input type=radio name=grammar value=j>Adjective (j)</td>
						<td width='250px'><input type=radio name=grammar value=nn>Noun (nn)</td>
					</tr>
					<tr>
						<td width='250px'><input type=radio name=grammar value=r>Adverb (r)</td>
					</tr>
				</table></center>
			</form>";
	echo "</td></tr></table></center>";
	
	mysql_query("SET CHARACTER SET utf8");
	$todo=$_POST['todo'];
	
	echo "<br/><br/><hr height='2' width='100%'>";
	
	if(isset($todo) and $todo=="search"){
		$search_text=$_POST['search_text'];
		$search_text=ltrim($search_text);
		$search_text=rtrim($search_text);
		$search_text=str_replace("'","’",$search_text);

		$vocabtype=$_POST['vocabtype'];
		
		$grammar=$_POST['grammar'];
		
		if(strcmp($searchtype,"exact") != 0){
			$q="(CA LIKE '%$search_text%' OR IA1 LIKE '%$search_text%' OR IA2 LIKE '%$search_text%' OR IA3 LIKE '%$search_text%') ";
		} 
		else {
			$q="(CA LIKE '$search_text' OR IA1 LIKE '$search_text' OR IA2 LIKE '$search_text' OR IA3 LIKE '$search_text') ";
		}
		
		if(strcmp($search_text,"") == 0){
			$q="";
		}
		
		
		if(strcmp($vocabtype,"vo") == 0){
			$qq=" Vocab_Type LIKE 'VO'";
		} 
		else if(strcmp($vocabtype,"co") == 0){
			$qq=" Vocab_Type LIKE 'CO'";
		}
		else if(strcmp($vocabtype,"ix") == 0){
			$qq=" Vocab_Type LIKE 'IX'";
		}	
		else if(strcmp($vocabtype,"pv") == 0){
			$qq=" Vocab_Type LIKE 'PV'";
		}
		else if(strcmp($vocabtype,"id") == 0){
			$qq=" Vocab_Type LIKE 'ID'";
		}
		else if(strcmp($vocabtype,"ce") == 0){
			$qq=" Vocab_Type LIKE 'CE'";
		}
		else {
			$qq="";
		}

		
		if(strcmp($grammar,"v") == 0){
			$qqq="Grammar_Type LIKE 'v'";
		} 
		else if(strcmp($grammar,"j") == 0){
			$qqq="Grammar_Type LIKE 'j'";
		}
		else if(strcmp($grammar,"nn") == 0){
			$qqq="Grammar_Type LIKE 'nn'";
		}
		else if(strcmp($grammar,"r") == 0){
			$qqq="Grammar_Type LIKE 'r'";
		}
		else {
			$qqq="";
		}		
		
		
		
		if(strcmp($q,"") == 0){
			if(strcmp($qq,"") == 0){
				// $q and $qq both blank
				if(strcmp($qqq,"") == 0){
					// none exist
				}
				else {
					// $qqq only
					$q = " WHERE ";
				}
			}
			else {
				// $qq exists
				
				$q = " WHERE ";
				
				if(strcmp($qqq,"") == 0){
					// only $qq
				}
				else {
					// $qq and $qqq, so add AND
					$qq = $qq . " AND ";
				}				
			}
		} 
		else {
			$q = " WHERE " . $q;
			if(strcmp($qq,"") == 0){
				// $q exists but no $qq, so check for $qqq
				if(strcmp($qqq,"") == 0){
					// $q only, so do nothing
				}
				else {
					// $q and $qqq, so add AND
					$q = $q . " AND ";
				}
			}
			else {
				// $q and $qq exist
				if(strcmp($qqq,"") == 0){
					// $q and qq, so add AND
					$q = $q . " AND ";
				}
				else {
					// all three exist, so add ANDs
					$q = $q . " AND ";
					$qq = $qq . " AND ";
				}
			}
		}
		
		
		
		
		
		
		$query="SELECT Set_Name, Passage_Type, Number, Passage, CA, IA1, IA2, IA3, Vocab_Type, Grammar_Type FROM Vocab $q $qq $qqq ";
	

	
		// RUN QUERY
		$result=mysql_query($query);	
		echo mysql_error();
		
		// getting number of columns
		$fields_num = mysql_num_fields($result);
		
		$rows_num = mysql_num_rows($result);
	
	
		if(strcmp($q,"") == 0){
			echo "<div id='highlight-plugin'>";
			
			// printing table header
			echo "<h3>TEPS search results for string '$search_text'</h3>";
		}
		else {
			// printing table header
			echo "<h3>TEPS search results</h3>";
		}

		echo "<h6> [using query: $query]</h6>";
		
		if ($rows_num > 10000) {
			echo "<h3>TOO MANY (OVER 10000) RESULTS RETURNED. PLEASE NARROW YOUR SEARCH.</h3>";
		}
		else {
		
			echo "<table border='1' class='display' id='example'><thead><tr>";
			for($i=0; $i<$fields_num; $i++)
			{
				$field = mysql_fetch_field($result);
				echo "<th>{$field->name}</th>";
			}
			echo "</tr></thead>\n";
			
			// printing table rows
			echo "<tbody>";
			while($row = mysql_fetch_row($result)) {
				echo "<tr>";

				// $row is array... foreach( .. ) puts every element of $row to $cell variable
				foreach($row as $cell)
					echo "<td id='therow'>$cell</td>";

				echo "</tr>\n";
			}
			echo "</tbody></table>";
		}
		mysql_free_result($result);
	}
	else {
		echo "<h3>SEARCH RESULTS WILL DISPLAY IN THIS AREA.</h3>";
	}		
?>
	
</body>
</html>