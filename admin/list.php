<?php
header('Content-type: text/html; charset=utf-8');
include($_SERVER['DOCUMENT_ROOT'].'/bd.php');
include('checkauth.php');

setlocale(LC_TIME,"ru_RU");
$result = setlocale(LC_ALL, 'ru_RU.UTF-8');

$password = password_hash('12345',PASSWORD_DEFAULT);
echo $password;
echo "<a href='/admin/index.php'><img src='/img/previous.png' title='Вернуться обратно'></a>";

echo "<br/><br/><a href='/admin/addformcli.php'><img src='/img/add_person.png' title='Добавить сотрудника'></a>&nbsp;&nbsp;";
echo "<a href='/admin/addformclitech.php'><img src='/img/add_person_tech.png' title='Добавить администратора'></a>&nbsp;&nbsp;";
echo "<a href='/admin/admlist.php'><img src='/img/edit_person_tech.png' title='Редактировать администраторов'></a>&nbsp;&nbsp;";
echo "<a href='/admin/addformdep.php'><img src='/img/add_department.png' title='Добавить департамент'></a>&nbsp;&nbsp;";
echo "<a href='/admin/deplist.php'><img src='/img/edit_department.png' title='Редактировать департаменты'></a>&nbsp;&nbsp;";

$query_names = "SELECT n.name,n.id,n.weight,n.`show_plan`,n.id_dep,n.iptel,d.short AS dep FROM name AS n JOIN department AS d ON (id_dep = d.id) WHERE `del` <> 1 ORDER BY `id_dep`,`weight`";
$result = $mysqli->query($query_names);
while ($row = $result->fetch_assoc()){
	$rows[] = $row;
}

$query = "SELECT id,name FROM department";
$result2 = $mysqli->query($query);
while ($dep = $result2->fetch_assoc()){
	$deps[] = $dep;
}

echo "<br/>";
echo "<br/>";
echo "<br/>";




echo "<table border=1><th>Ф.И.О.</th><th width='10'>Приоритет выдачи</th><th>Показывать</th><th>Департамент</th><th>IP телефон</th><th>Редактировать</th>";
foreach ($rows as $row){
	echo "<tr><td valign='top'>".$row['name']."</td>";
	echo "<td valign='top' width='10'>".$row['weight']."</td>";
	if ($row['show_plan'] == 1){
		echo "<td valign='top'>Да</td>";
	}
	else {
		echo "<td valign='top'>Нет</td>";
	}
	echo "<td valign='top'>".$row['dep']."</td>";
	echo "<td valign='top'>".$row['iptel']."</td>";
	echo "<td valign='top'><a href='/admin/editformcli.php?id=".$row['id']."'><img src='/img/edit.png' title='Редактировать'></a></td></tr>";
}

	


$mysqli->close();
?>

