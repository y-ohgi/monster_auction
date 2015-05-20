<!DOCTYPE HTML>
<html lang="ja-JP">
<head>
	<!-- <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.3/build/cssreset/cssreset-min.css"]] > -->
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script src="./js/game.js"></script>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
<h1>部屋名: {$title}</h1>

<table border>
	<tr>
		<th colspan={$max}>参加者一覧</th>
	</tr>
	<tr>
		{for $i=1 to $max}
			<td><p id="user_{$i}"></p></td>
		{/for}
	</tr>
		
</table>

<div class="img_wrap">
	筆記体のモンスター名読めない<br />
	{foreach $imgs as $img}
		{if $img == "." or $img == ".."}
			{continue}
		{/if}
		<img src="size140/{$img}" />
	{/foreach}
</div><!-- .img_wrap -->

</body>
</html>

