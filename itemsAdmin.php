<html>
<head>
	<meta charset="utf-8">
	<title>Админский файл - items</title>
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

$str = "<?php
require_once('api/Simpla.php');

class %class% extends Simpla {
	
	function fetch() {
		if (\$this->request->method('post')) {
			\$ids = \$this->request->post('check');
			
			if (is_array(\$ids)) {
				switch(\$this->request->post('action')) {
					case 'delete':
						foreach(\$ids as \$id) {
							\$this->%api%->delete_%name%(\$id);
						}
						break;
				}
			}
		}
		
		\$%name%s = \$this->%api%->get_%name%s();
		\$this->design->assign('%name%s', \$%name%s);
		
		return \$this->body = \$this->design->fetch('%name%s.tpl');
	}
}";

$str = str_replace("<", "&lt", $str);
$str = str_replace(">", "&gt", $str);
$str = str_replace("%class%", ucfirst($name)."sAdmin", $str);
$str = str_replace("%name%", $name, $str);
$str = str_replace("%api%", $name."s", $str);

echo "<textarea>";
echo $str;
echo "</textarea>";
?>
</body>
</html>