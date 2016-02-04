<?php
header('Content-type: text/html; charset=utf-8');
setlocale(LC_TIME,"ru_RU");

include($_SERVER['DOCUMENT_ROOT'].'/bd.php');
include($_SERVER['DOCUMENT_ROOT'].'/date.php');
include('checkauth.php');

$myDate = new mDate();



echo "<table><tr>";
if (isset($_GET['nextweek']) && !empty($_GET['nextweek']) && $_GET['nextweek']=="yes"){
	$array_week = $myDate->createArrayWeek(7);
	$nextweek='yes';
	echo "<td><a href='/admin/index.php'><img src='/img/previous.png' title='Вернуться обратно'></a></td>";
}
else{
	$array_week = $myDate->createArrayWeek();
	$nextweek='no';
	echo "<td><a href='/admin/index.php?nextweek=yes'><img src='/img/calend.png' title='Составить план работы на следующую неделю'></a></td>";
}
	echo "<td><a href='/admin/list.php'><img src='/img/edit_person.png' title='Редактировать сотрудников'></a></td>";
	echo "<td><a href='/admin/plist.php'><img src='/img/edit_factory.png' title='Редактировать подведомственные предприятия'></a></td>";
	echo "<td><a href='/admin/week.php'><img src='/img/old_calend.png' title='Редактировать старый план работы'></a></td>";
	echo "<td><a href='/admin/word.php'><img src='/img/word.png' title='Скачать план работы'></a></td>";

echo "</tr></table>";

$plan = array();



$query_names = "SELECT name,id FROM name WHERE `show_plan`='1' AND `del` <> 1 ORDER BY weight";
$result = $mysqli->query($query_names);
while ($row = $result->fetch_assoc()){
	$rows[] = $row;
}

$plan = array();
$ruks = array();

$query_names = "SELECT name,id FROM place";
$result = $mysqli->query($query_names);
$rowplace = $result->fetch_assoc();
$aktzal = $rowplace['name'];
$aktzalID = $rowplace['id'];



array_push($ruks,$aktzal);
foreach($array_week as $day){
	$descr = null;
	$dayBegin = $myDate->dateBegin($day);
	$dayEnd = $myDate->dateEnd($day);
	$query_todos = "SELECT t.descr,t.id,DATE_FORMAT(t.date, '%H:%i') AS hours,t.place,t.responsible, n.name AS name FROM todo AS t LEFT JOIN name AS n ON (t.id_name = n.id) WHERE t.place_id=".$aktzalID." AND `t`.`date` BETWEEN '$dayBegin' AND '$dayEnd' ORDER BY `t`.`date`";
	$result2 = $mysqli->query($query_todos);
	while($row2 = $result2->fetch_assoc()){
		if($row2['hours'] == "00:00"){
			$row2['hours'] = null;
		}
		if($row2['responsible'] == ""){
			$row2['responsible'] = null;
		}
		else{
			$row2['responsible'] = "<br/><i>Отв.".$row2['responsible']."</i>";
		}

		if($descr != null){
			$descr =$descr." <hr/> <b>".$row2['hours']."</b> ".nl2br($row2['descr'])." <br/><b>$row2[name]</b>  $row2[responsible]<br/><a href=\"editform.php?id=".$row2['id']."&nextweek=".$nextweek."\"><img src='/img/edit.png' title='Редактировать'></a>";
		}
		else{
			$descr =$descr."<b valign=top>".$row2['hours']."</b> ".nl2br($row2['descr'])." <br/><b>$row2[name]</b> $row2[responsible]<br/><a href=\"editform.php?id=".$row2['id']."&nextweek=".$nextweek."\"><img src='/img/edit.png' title='Редактировать'></a>";
		}
	}
	$plan[$aktzal][$day]['descr']=$descr;
	$plan[$aktzal][$day]['id_name'] = $row['id'];
	$plan[$aktzal][$day]['date'] = $day;
	
	$descr = null;
	$query_todos_place = "SELECT t.descr,t.id,DATE_FORMAT(t.date, '%H:%i') AS hours, p.name AS place FROM todo_place AS t LEFT JOIN place AS p ON (t.place_id = p.id) WHERE `t`.`date` BETWEEN '$dayBegin' AND '$dayEnd' ORDER BY `t`.`date`";
	$result_todo_place = $mysqli->query($query_todos_place);
	while($row_place = $result_todo_place->fetch_assoc()){
		if($row_place['hours'] == "00:00"){
			$row_place['hours'] = null;
		}

		if($descr != null){
			$descr =$descr." <hr/> <b>".$row_place['hours']."</b> ".nl2br($row_place['descr'])." <br/><a href=\"editform.php?id=".$row_place['id']."&nextweek=".$nextweek."\"><img src='/img/edit.png' title='Редактировать'></a>";
		}
		else{
			$descr =$descr."<b valign=top>".$row_place['hours']."</b> ".nl2br($row_place['descr'])." <br/><a href=\"editform.php?id=".$row_place['id']."&nextweek=".$nextweek."\"><img src='/img/edit.png' title='Редактировать'></a>";
		}
	}
	
}

foreach ($rows as $row){
	array_push($ruks,$row['name']);
	foreach($array_week as $day){
		$descr = null;
		$dayBegin = $myDate->dateBegin($day);
		$dayEnd = $myDate->dateEnd($day);

		$query_todos = "SELECT descr,id,DATE_FORMAT(date, '%H:%i') AS hours,place,responsible,place_id FROM todo WHERE id_name=".$row['id']." AND `date` BETWEEN '$dayBegin' AND '$dayEnd' ORDER BY `date`";
		$result2 = $mysqli->query($query_todos);
		while($row2 = $result2->fetch_assoc()){
			if($row2['hours'] == "00:00"){
				$row2['hours'] = null;
			}
			if ($row2['place'] != ""){
				$row2['place'] = "<br/><b>".$row2['place']."</b>";
			}
			if ($row2['place_id'] == $aktzalID){
				$row2['place'] = "<br/><b>".$aktzal."</b>";
			}
			if($row2['responsible'] == ""){
				$row2['responsible'] = null;
			}
			else{
				$row2['responsible'] = "<br/><i>Отв.".$row2['responsible']."</i>";
			}

			if($descr != null){
				$descr = $descr." <hr/> <b>".$row2['hours']."</b> ".nl2br($row2['descr'])."$row2[place] $row2[responsible]<br/><a href=\"editform.php?id=".$row2['id']."&nextweek=".$nextweek."\"><img src='/img/edit.png' title='Редактировать'></a>";
			}
			else{
				$descr = $descr."<b valign=top>".$row2['hours']."</b> ".nl2br($row2['descr'])."$row2[place] $row2[responsible]<br/><a href=\"editform.php?id=".$row2['id']."&nextweek=".$nextweek."\"><img src='/img/edit.png' title='Редактировать'></a>";
			}
		}
		$plan[$row['name']][$day]['descr']=$descr;
		$plan[$row['name']][$day]['id_name'] = $row['id'];
		$plan[$row['name']][$day]['date'] = $day;
	}
}
echo "<br/>";
echo "<br/>";
echo "<br/>";

$current_date = date('d-m-Y');


echo "<table border=2 bordercolor='#876'>
		<tr>
			<th width='250'>Ф.И.О.</th>
			<th width='320'>Понедельник<br/>$array_week[0]</th>
			<th width='320'>Вторник<br/>$array_week[1]</th>
			<th width='320'>Среда<br/>$array_week[2]</th>
			<th width='320'>Четверг<br/>$array_week[3]</th>
			<th width='320'>Пятница<br/>$array_week[4]</th>
			<th width='320'>Суббота<br/>$array_week[5]</th>
		</tr>
	";

foreach ($ruks as $ruk){
	echo "<tr><td valign='top'>$ruk</td>";
	if($ruk == $aktzal){
		foreach($plan[$aktzal] as $key => $todo){
			if ($current_date == $key){
				echo "<td width='320' valign='top' bgcolor='#CBFCED' >".$todo['descr']."<hr/><a href=\"/admin/addform.php?id=".$todo['id_name']."&date=".$todo['date']."&nextweek=".$nextweek."\"><img src='/img/add.png' title='Добавить'></a></td>";
			}
			else {
				echo "<td width='320' valign='top'>".$todo['descr']."<hr/><a href=\"/admin/addform.php?id=".$todo['id_name']."&date=".$todo['date']."&nextweek=".$nextweek."\"><img src='/img/add.png' title='Добавить'></a></td>";
			}
		}
	}
	else{
	foreach ($plan[$ruk] as $key => $todo){		
		if (!empty($todo['descr'])){
			if ($current_date == $key){
				echo "<td width='320' valign='top' bgcolor='#CBFCED' >".$todo['descr']."<hr/><a href=\"/admin/addform.php?id=".$todo['id_name']."&date=".$todo['date']."&nextweek=".$nextweek."\"><img src='/img/add.png' title='Добавить'></a></td>";
			}
			else {
				echo "<td width='320' valign='top'>".$todo['descr']."<hr/><a href=\"/admin/addform.php?id=".$todo['id_name']."&date=".$todo['date']."&nextweek=".$nextweek."\"><img src='/img/add.png' title='Добавить'></a></td>";
			}
		}
		else {
			if ($current_date == $key){
				echo "<td width='320' valign='top' bgcolor='#CBFCED'><a href=\"/admin/addform.php?id=".$todo['id_name']."&date=".$todo['date']."&nextweek=".$nextweek."\"><img src='/img/add.png' title='Добавить'></a></td>";
			}
			else {
				echo "<td width='320' valign='top'><a href=\"/admin/addform.php?id=".$todo['id_name']."&date=".$todo['date']."&nextweek=".$nextweek."\"><img src='/img/add.png' title='Добавить'></a></td>";
			}
		}
	}
	}
}

	


$mysqli->close();
?>

