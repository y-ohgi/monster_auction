<!DOCTYPE HTML>
<html lang="ja-JP">
<head>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.3/build/cssreset/cssreset-min.css"]] >
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
	<meta charset="UTF-8">
	<title></title>
<style type="text/css">

.imgs > img {
	display: flexbox;
}

</style>
</head>
<body>

<h1>ほげ〜</h1>

<div class="imgs">
{* 画像タイトル配列を表示 *}
{foreach from=$imgs item=img}
	{if $img == "." || $img == ".."}
		{continue}
	{/if}
	
	<img src="size140/{$img}" alt="" />
{/foreach}
</div><!-- .imgs -->

</body>
</html>
