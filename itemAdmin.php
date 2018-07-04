<html>
<head>
	<meta charset="utf-8">
	<title>Админский файл - item</title>
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

//The string template for itemAdmin with placeholders
$str = "<?php
require_once('api/Simpla.php');

class %class% extends Simpla {
	
	function fetch() {
		$%name% = new stdClass;
		if(\$this->request->method('post')) {
			$%name%->name = \$this->request->post('name');
			$%name%->id = \$this->request->post('id', 'integer');
%posts%
			
			if(empty($%name%->id)) {
				$%name%->id = \$this->%api%->%addfunc%($%name%);
				\$this->design->assign('message_success', 'added');
			} else {
				\$this->%api%->%updatefunc%($%name%->id, $%name%);
				\$this->design->assign('message_success', 'updated');
			}
			$%name% = \$this->%api%->%getfunc%($%name%->id);
		} else {
			$%name%->id = \$this->request->get('id', 'integer');
			$%name% = \$this->%api%->%getfunc%($%name%->id);
		}
		
		\$this->design->assign('%name%', $%name%);
		return  \$this->design->fetch('%tplname%.tpl');
	}
}";

function ecraniseBraces($str) {
	$str = str_replace("<", "&lt", $str);
	$str = str_replace(">", "&gt", $str);
	return $str;
}

/**
 * Makes commands to process all POST variables in fetch()
 *
 * @param $bd Link to database
 * @param $name Admin page name
 * @return mixed
 */
function get_posts($bd, $name) {
	$query = mysqli_query($bd, "SELECT name, type FROM vars");
	$ret = '';
	while ($row = mysqli_fetch_assoc($query)) {
		$typeStr = '';
		$type = intval($row["type"]);
		if ($type == 0) {
			$typeStr = ", 'boolean'";
		} else if ($type == 3) {
			$typeStr = ", 'integer'";
		}
		$ret .= "\t\t\t$".$name."->".$row["name"]." = \$this->request->post('".$row["name"]."'".$typeStr.");".PHP_EOL;
	}
	mysqli_free_result($query);
	return ecraniseBraces($ret);
}

//Inserting variables into a template
$str = str_replace("<", "&lt", $str);
$str = str_replace(">", "&gt", $str);
$str = str_replace("%class%", ucfirst($name)."Admin", $str);
$str = str_replace("%name%", $name, $str);
$str = str_replace("%api%", $name."s", $str);
$str = str_replace("%addfunc%", "add_".$name, $str);
$str = str_replace("%getfunc%", "get_".$name, $str);
$str = str_replace("%updatefunc%", "update_".$name, $str);
$str = str_replace("%tplname%", $name, $str);
$str = str_replace("%posts%", get_posts($bd, $name), $str);

echo "<textarea>";
echo $str;
echo "</textarea>";
?>
</body>
</html>