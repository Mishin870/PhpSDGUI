<html>
<head>
	<meta charset="utf-8">
	<title>API</title>
</head>
<body>
<?php
require_once("db.php");
$query = mysqli_query($bd, "SELECT * FROM names WHERE nkey='name' LIMIT 1");
$result = mysqli_fetch_assoc($query);
mysqli_free_result($query);
$name = $result["value"];

echo "<style>
* {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    padding: 0;
    margin: 0;
}
body {
    color: #ffffff;
    font-size: 180%;
}
textarea {
    width: 100%;
	height: 100%;
}
</style>";

//The string template for api with placeholders
$str = "<?php
require_once('Simpla.php');

class %class% extends Simpla {
	
	public function get_%name%s(\$filter = array()) {	
%filters%
		
		\$query = \$this->db->placehold(\"SELECT DISTINCT * FROM __%name%s x
										WHERE 1%filters_sql% ORDER BY x.name\");
		
		\$this->db->query(\$query);
		return \$this->db->results();
	}
	
	public function get_%name%(\$id) {
		\$filter = \$this->db->placehold('x.id = ?', intval(\$id));
		\$query = \"SELECT * FROM __%name%s x WHERE \$filter LIMIT 1\";
		\$this->db->query(\$query);
		return \$this->db->result();
	}
	
	public function add_%name%(\$%name%) {
		\$%name% = (array) \$%name%;
		\$query = \$this->db->query(\"INSERT INTO __%name%s SET ?%\", \$%name%);
		return \$this->db->insert_id();
	}
	
	public function update_%name%(\$id, \$%name%) {
		\$query = \$this->db->placehold(\"UPDATE __%name%s SET ?% WHERE id=? LIMIT 1\", \$%name%, intval(\$id));
		\$this->db->query(\$query);
		return \$id;
	}
	
	public function delete_%name%(\$id) {
		if (!empty(\$id)) {
			\$query = \$this->db->placehold(\"DELETE FROM __%name%s WHERE id=? LIMIT 1\", \$id);
			\$this->db->query(\$query);
		}
	}
	
}";

function ecraniseBraces($str) {
	$str = str_replace("<", "&lt", $str);
	$str = str_replace(">", "&gt", $str);
	return $str;
}

/**
 * Makes filter declarations for function get_%name%s of template
 */
function getFilters() {
	global $bd, $name;
	$query = mysqli_query($bd, "SELECT name FROM vars WHERE type='0'");
	$ret = '';
	while ($row = mysqli_fetch_assoc($query)) {
		$n = $row["name"];
		$ret .= "\t\t\${$n}_filter = '';\n\t\tif (isset(\$filter['{$n}'])) \${$n}_filter = \$this->db->placehold('AND x.{$n}=?', intval(\$filter['{$n}']));\n";
	}
	mysqli_free_result($query);
	return ecraniseBraces($ret);
}
/**
 * Makes sql for filters in function get_%name%s of template
 */
function getFiltersSql() {
	global $bd, $name;
	$query = mysqli_query($bd, "SELECT name FROM vars WHERE type='0'");
	$ret = '';
	while ($row = mysqli_fetch_assoc($query)) {
		$n = $row["name"];
		$ret .= " \${$n}_filter";
	}
	mysqli_free_result($query);
	return ecraniseBraces($ret);
}

//Inserting variables into a template
$str = str_replace("<", "&lt", $str);
$str = str_replace(">", "&gt", $str);
$str = str_replace("%class%", ucfirst($name)."s", $str);
$str = str_replace("%name%", $name, $str);
$str = str_replace("%filters%", getFilters(), $str);
$str = str_replace("%filters_sql%", getFiltersSql(), $str);

echo "<textarea>";
echo $str;
echo "</textarea>";
?>
</body>
</html>