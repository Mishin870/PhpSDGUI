<?php
require_once('db.php');
require_once('vars.php');

if (isset($_POST['form_add'])) {
	if (!empty($_POST['name'])) {
		mysqli_query($bd, "INSERT INTO vars SET name='{$_POST['name']}', type='{$_POST['type']}', area='{$_POST['area']}', description='{$_POST['description']}'");
	}
} else if (isset($_POST['form_delete'])) {
	mysqli_query($bd, "DELETE FROM vars WHERE id='".intval($_POST['del_id'])."'");
} else if (isset($_POST['form_keys'])) {
	mysqli_query($bd, "UPDATE names SET value='{$_POST['name']}' WHERE nkey='name'");
	mysqli_query($bd, "UPDATE names SET value='{$_POST['rod']}' WHERE nkey='rod'");
	mysqli_query($bd, "UPDATE names SET value='{$_POST['ncase']}' WHERE nkey='ncase'");
	mysqli_query($bd, "UPDATE names SET value='{$_POST['gcase']}' WHERE nkey='gcase'");
}
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>SimplaGUI (item admin)</title>
		<link rel="stylesheet" href="style.css">
		<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>-->
	</head>
	<body>
		<h2><a href="/">Simpla GUI</a></h2>
		<h4>Список переменных:</h4>
		<form id="tableVarsForm" method="post">
			<input type="hidden" name="form_delete"/>
			<input id="formDeleteDelId" type="hidden" name="del_id" value="-1"/>
			<table>
				<tr>
					<th>Название</th>
					<th>Тип</th>
					<th>Положение</th>
					<th>Подпись</th>
					<th>X</th>
				</tr>
				<?php
				$query = mysqli_query($bd, "SELECT * FROM vars");
				while ($row = mysqli_fetch_assoc($query)) {
					printf("<tr>
								<td>%s</td>
								<td>%s</td>
								<td>%s</td>
								<td>%s</td>
								<td><a href='#' onclick='del(%d)'>X</a></td>
							</tr>", $row["name"], $types[$row["type"]], $areas[$row["area"]], $row["description"], $row["id"]);
				}
				mysqli_free_result($query);
				?>
				<script>
					function del(id) {
						document.getElementById("formDeleteDelId").setAttribute("value", id);
						document.getElementById("tableVarsForm").submit();
					}
				</script>
			</table>
		</form>
		<form method="post" class="block fit">
			<input type="hidden" name="form_add"/>
			<div class="line">
				<label>Название переменной</label><input name="name" type="text" class="right"/>
			</div>
			<div class="line">
				<label>Тип переменной</label>
				<select name="type" class="right">
					<?php
					$i = 0;
					foreach ($types as $type) {
						if ($i == 1) {
							printf('<option value="%d" selected>%s</option>', $i, $type);
						} else {
							printf('<option value="%d">%s</option>', $i, $type);
						}
						$i++;
					}
					?>
				</select>
			</div>
			<div class="line">
				<label>Зона переменной</label>
				<select name="area" class="right">
					<?php
					$i = 0;
					foreach ($areas as $area) {
						if ($i == 1) {
							printf('<option value="%d" selected>%s</option>', $i, $area);
						} else {
							printf('<option value="%d">%s</option>', $i, $area);
						}
						$i++;
					}
					?>
				</select>
			</div>
			<div class="line">
				<label>Подпись переменной</label><input name="description" type="text" class="right"/>
			</div>
			<input type="submit" class="button" value="Добавить"/>
		</form>
		
		<hr>
		<h4>Список настроек:</h4>
		<table>
			<tr>
				<th>Ключ</th>
				<th>Значение</th>
			</tr>
			<?php
			$query = mysqli_query($bd ,"SELECT * FROM names");
			$data = array();
			while ($row = mysqli_fetch_assoc($query)) {
				printf("<tr>
							<td>%s</td>
							<td>%s</td>
						</tr>", $row["nkey"], $row["value"]);
				$data[$row["nkey"]] = $row["value"];
			}
			mysqli_free_result($query);
			?>
		</table>
		<form method="post" class="block fit">
			<input type="hidden" name="form_keys"/>
			<div class="line">
				<div class="line"><label>Название шаблона</label><input name="name" type="text" class="right" value="<?php echo $data["name"];?>"/></div>
				<div class="line">
					<label>Род</label>
					<select name="rod" class="right">
						<?php
						$i = 0;
						$rodind = intval($data["rod"]);
						foreach ($rods as $rod) {
							if ($i == $rodind) {
								printf('<option value="%d" selected>%s</option>', $i, $rod);
							} else {
								printf('<option value="%d">%s</option>', $i, $rod);
							}
							$i++;
						}
						?>
					</select>
				</div>
				<div class="line"><label>Н. (что?)</label><input name="ncase" type="text" class="right" value="<?php echo $data["ncase"];?>"/></div>
				<div class="line"><label>Н. (нет чего?)</label><input name="gcase" type="text" class="right" value="<?php echo $data["gcase"];?>"/></div>
			</div>
			<input type="submit" class="button" value="Обновить"/>
		</form>
		
		<hr>
		<div>
			<a class="button autoWid unlink" href="itemAdmin.php" target="_blank">Сгенерировать админский файл</a>
			<a class="button autoWid unlink" href="itemTemplate.php" target="_blank">Сгенерировать шаблон</a>
			<a class="button autoWid unlink" href="api.php" target="_blank">API</a>
		</div>
	</body>
</html>