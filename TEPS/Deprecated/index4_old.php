<?php header('Content-Type: text/html; charset=UTF-8');?>
<?php include("../../dblogin.php");?>

<html>

<head>

<title>TEPS Keyword Search</title>

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

	echo "<center><h1>TEPS Database Search</h1>";
	echo "<table border='2' bgcolor='#222288' width='400px'><tr><td>";
	echo "<font color='#FFFFFF'><center>To search the TEPS DB, please enter a keyword below.</center></font><br/>";
	echo "  <form method=post action=''>
				<center><table class='search_options' padding='0' margin='0' border='0'>
					<tr>
						<td><input type=hidden name=todo value=search><input type=text name=search_text><input type=submit value=Search></td>
					</tr>
					<tr>
						<td width='375px'><input type=radio name=type value=wildcard checked>Wildcard Match</td>
					</tr>
					<tr>
						<td width='375px'><input type=radio name=type value=exact>Exact Match</td>
					</tr>
					<tr bgcolor='#000000' height='5'>
						<td></td>
					</tr>
				</table>
				<table class='search_options' padding='0' margin='0' border='0'>
					<tr>
						<td width='150px'><input type=radio name=section value=all checked>All Sections</td>
						<td width='225px'><input type=radio name=section value=l>Listening Only</td>
					</tr>
					<tr>
						<td width='150px'><input type=radio name=section value=r>Reading Only</td>
						<td width='225px'><input type=radio name=section value=gv>Grammar/Vocab Only</td>
					</tr>
					<tr bgcolor='#000000' height='5'>
						<td></td>
						<td></td>
					</tr>
				</table>
				<table class='search_options' padding='0' margin='0' border='0'>
					<tr>
						<td width='150px'><input type=radio name=version value=imitation checked>Imitations only</td>
						<td width='225px'><input type=radio name=version value=original>Originals Only</td>
					</tr>
					<tr>
						<td width='150px'><input type=radio name=version value=both>Both</td>
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

		$section=$_POST['section'];
		
		$version=$_POST['version'];
		
		if(strcmp($type,"exact") != 0){
			$q="Passage LIKE '%$search_text%'";
		} 
		else {
			$q="Passage LIKE '% $search_text %'";
		}
		
		if(strcmp($section,"r") == 0){
			$qq=" AND Passage_Type LIKE 'Reading'";
		} 
		else if(strcmp($section,"l") == 0){
			$qq=" AND Passage_Type LIKE 'Listening'";
		}
		else if(strcmp($section,"gv") == 0){
			$qq=" AND (Passage_Type='Grammar' OR Passage_Type='Vocab')";
		}	
		else {
			$qq="";
		}

		$query="SELECT Set_Name, Passage_Type, Number, Passage FROM $table WHERE $q $qq ";
	
		if(strcmp($version,"imitation") == 0){
			$qqq = " AND summary.Is_Original=0";
			$query="SELECT Sheet1.Set_Name, Sheet1.Passage_Type, Sheet1.Number, Sheet1.Passage FROM Sheet1 JOIN summary ON Sheet1.Set_Name=summary.Set_Name WHERE $q $qq $qqq";
		} 
		else if(strcmp($version,"original") == 0){
			$qqq = " AND summary.Is_Original=1";
			$query="SELECT Sheet1.Set_Name, Sheet1.Passage_Type, Sheet1.Number, Sheet1.Passage FROM Sheet1 JOIN summary ON Sheet1.Set_Name=summary.Set_Name WHERE $q $qq $qqq";
		}
	
	
		// RUN QUERY
		$result=mysql_query($query);	
		echo mysql_error();
		
		// getting number of columns
		$fields_num = mysql_num_fields($result);
		
		$rows_num = mysql_num_rows($result);
	
		echo "<div id='highlight-plugin'>";
	
		// printing table header
		echo "<h3>TEPS search results for string '$search_text'</h3>";
		echo "<h6> [using query: $query]</h6>";
		
		if ($rows_num > 100) {
			echo "<h3>TOO MANY (OVER 100) RESULTS RETURNED. PLEASE NARROW YOUR SEARCH.</h3>";
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