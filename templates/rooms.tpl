<!DOCTYPE HTML>
<html lang="ja-JP">
<head>
	<!-- <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.3/build/cssreset/cssreset-min.css"]] > -->
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="./js/rooms.js"></script>
	<script>

	</script>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>

	<h1>username:{$name}</h1>
	<h2>ルーム一覧</h1>
	<div class="rooms" id="rooms" style="">
		<table border="1">
			<tr>
				<th>部屋名</th>
				<th>最大人数</th>
				<th>現在人数</th>
			</tr>
		{foreach from=$rooms item=room}
			<tr>
				<td><a href="game.php?id={$room['id']}">{$room['title']}<a/></td>
				<td>{$room['max']}</td>
				<td>{$room['now']}</td>
			</tr>
		{/foreach}
		<table>
	</div>

	<div class="room_create" id="room_create">
		<h2>ルームを作成</h2>
		<form method="post">
			<label>title:<input type="text" name="title" /></label><br />
			<label>max:<input type="number" name="max" value="8" max="8" min="8" /></label><br />
			<input type="submit" value="create" />
		</form>
	</div>
</body>
</html>
