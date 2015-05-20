<!DOCTYPE HTML>
<html lang="ja-JP">
<head>
	<!-- <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.3/build/cssreset/cssreset-min.css"]] > -->
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>
<h1>{$title}</h1>

{if $showForm == true}
<form action="./posted.php" method="post">
	<input type="text" name="name" />
	<input type="submit" value="go" />
</form>
{/if}
<a href="./logout.php">session_clear</a>

</body>
</html>

