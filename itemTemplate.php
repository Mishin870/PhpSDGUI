<html>
<head>
	<meta charset="utf-8">
	<title>Шаблон</title>
</head>
<body>
<?php
require_once("db.php");
mb_internal_encoding("UTF-8");

$query = mysqli_query($bd, "SELECT * FROM names");
$data = array();
while ($result = mysqli_fetch_assoc($query)) {
	$data[$result["nkey"]] = $result["value"];
}
$name = $data["name"];
$rod = intval($data["rod"]);
$ncase = $data["ncase"];
$gcase = $data["gcase"];
mysqli_free_result($query);

//Localization
$strings = array(
	"new"	=> array('новый', 'новая'),
	"add"	=> array('добавлен', 'добавлена'),
	"upd"	=> array('обновлен', 'обновлена'),
);

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

//The string template for itemTemplate with placeholders
$str = "{capture name=tabs}
	{include file=\"madmintabs.tpl\" manager=\$manager currTab='%tab%'}
{/capture}

{if $%name%}
{\$meta_title = $%name%->name scope=parent}
{else}
{\$meta_title = '%new%' scope=parent}
{/if}
{include file='tinymce_init.tpl'}

{literal}
<script>
$(function() {
	// Удаление изображений
	$(\".images a.delete\").click( function() {
		$(\"input[name='delete_image']\").val('1');
		$(this).closest(\"ul\").fadeOut(200, function() { $(this).remove(); });
		return false;
	});
});
</script>
 
{/literal}

{if \$message_success}
<div class=\"message message_success\">
	<span class=\"text\">{if \$message_success=='added'}%added%{elseif \$message_success=='updated'}%updated%{else}{\$message_success}{/if}</span>
	{if \$smarty.get.return}
	<a class=\"button\" href=\"{\$smarty.get.return}\">Вернуться</a>
	{/if}
</div>
{/if}

{if \$message_error}
<div class=\"message message_error\">
	<span class=\"text\">{\$message_error}</span>
	<a class=\"button\" href=\"{\$smarty.get.return}\">Вернуться</a>
</div>
{/if}

<form method=post id=%name% enctype=\"multipart/form-data\">
<input type=hidden name=\"session_id\" value=\"{\$smarty.session.id}\">
	<div id=\"name\">
		<input name=id type=\"hidden\" value=\"{\$%name%->id|escape}\"/>
		<input class=\"name\" name=name type=\"text\" value=\"{\$%name%->name|escape}\"/> 
%up_area%
	</div>
	
	<div id=\"column_left\">
		<div class=\"block layer\">
			<h2>%params%</h2>
			<ul>
%left_area%
			</ul>
		</div>
	<input class=\"button_green button_save\" type=\"submit\" name=\"\" value=\"Сохранить\"/>
	</div>
	
	<div class=\"block layer\">
%down_area%
	</div>
	<input class=\"button_green button_save\" type=\"submit\" name=\"\" value=\"Сохранить\"/>
</form>";

function ecraniseBraces($str) {
	$str = str_replace("<", "&lt", $str);
	$str = str_replace(">", "&gt", $str);
	return $str;
}

/**
 * @return mixed localized string in Russian by its key
 */
function localizedString($key) {
	global $strings, $rod;
	return $strings[$key][$rod];
}

function firstCharacterUpper($text) {
	return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

function getUpArea() {
	global $bd, $name;
	$query = mysqli_query($bd, "SELECT name, type, description FROM vars WHERE area='0'");
	$ret = '';
	while ($row = mysqli_fetch_assoc($query)) {
		$type = intval($row["type"]);
		$name = $row["name"];
		$description = $row["description"];
		if ($type == 0) {
			$ret .= "\t\t<div class=\"checkbox\">\n\t\t\t<input name={$name} value='1' type=\"checkbox\" id=\"{$name}_checkbox\"{if \${$name}->{$name}}checked{/if}/> <label for=\"{$name}_checkbox\">{$description}</label>\n\t\t</div>";
		}
	}
	mysqli_free_result($query);
	return ecraniseBraces($ret);
}

function getDownArea() {
	global $bd, $name;
	$query = mysqli_query($bd, "SELECT name, type, description FROM vars WHERE area='3'");
	$ret = '';
	while ($row = mysqli_fetch_assoc($query)) {
		$type = intval($row["type"]);
		$name = $row["name"];
		$description = $row["description"];
		if ($type == 5) {
			$ret .= "\t\t<h2>Описание</h2><textarea name=\"{$description}\" class=\"editor_large\">{\${$name}->{$name}|escape}</textarea>\n";
		}
	}
	mysqli_free_result($query);
	return ecraniseBraces($ret);
}

function getLeftArea() {
	global $bd, $name;
	$query = mysqli_query($bd, "SELECT name, type, description FROM vars WHERE area='1'");
	$ret = '';
	while ($row = mysqli_fetch_assoc($query)) {
		$type = intval($row["type"]);
		$name = $row["name"];
		$description = $row["description"];
//		if ($type == 0) {
//			$ret .= "\t\t<div class=\"checkbox\">\n\t\t\t<input name={$n} value='1' type=\"checkbox\" id=\"{$n}_checkbox\"{if \${$name}->{$n}}checked{/if}/> <label for=\"{$n}_checkbox\">{$d}</label>\n\t\t</div>";
//		}
		if ($type == 3) {
			$ret .= "\t\t\t\t<li><label class=property>{$description}</label><input name=\"{$name}\" class=\"simpla_inp\" type=\"text\" value=\"{\${$name}->{$name}}\" /></li>\n";
		} else if ($type == 4) {
			$ret .= "\t\t\t\t<li>
					<label class=\"property\">{$description}</label>
					<select name=\"{$name}\">
						{\${$name}s = []}
						{\$i = 0}
						{foreach \${$name}s as \${$name}}
							<option value=\"{\$i}\" {if \$i==\${$name}->{$name}}selected=\"selected\"{/if}>{\${$name}}</option>
							{\$i = \$i + 1}
						{/foreach}
					</select>
				</li>\n";
		} else if ($type == 2) {
			$ret .= "\t\t\t\t<li><label class=\"property\">{$description}</label><textarea name=\"{$name}\" class=\"simpla_inp\">{\${$name}->{$name}|escape}</textarea></li>\n";
		} else if ($type == 1) {
			$ret .= "\t\t\t\t<li><label class=\"property\">{$description}</label><input name=\"{$name}\" class=\"simpla_inp\" type=\"text\" value=\"{\${$name}->{$name}|escape}\"/></li>\n";
		}
	}
	mysqli_free_result($query);
	return ecraniseBraces($ret);
}

$str = ecraniseBraces($str);
$str = str_replace("%name%", $name, $str);
$str = str_replace("%tab%", $name."s", $str);
$str = str_replace("%new%", firstCharacterUpper(localizedString("new"))." ".$ncase, $str);
$str = str_replace("%added%", firstCharacterUpper($ncase)." ".localizedString("add"), $str);
$str = str_replace("%updated%", firstCharacterUpper($ncase)." ".localizedString("upd"), $str);
$str = str_replace("%params%", "Параметры ".$gcase, $str);
$str = str_replace("%up_area%", getUpArea(), $str);
$str = str_replace("%left_area%", getLeftArea(), $str);
$str = str_replace("%down_area%", getDownArea(), $str);

echo "<textarea>";
echo $str;
echo "</textarea>";

?>
</body>
</html>