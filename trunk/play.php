<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>test</title>
	</head>
	<body>
		<div>
<?php
$id = $_GET["id"];
echo "http://localhost/cgi-bin/get-flv?id=$id<br>";
?>
		</div>
		<object type="application/x-shockwave-flash" data="vcastr3.swf" width="650" height="500" id="vcastr3">
			<param name="movie" value="vcastr3.swf"/> 
			<param name="allowFullScreen" value="true" />
			<param name="FlashVars" value="xml=
				<vcastr>
					<channel>
						<item>
<?php
echo "<source>http://localhost/cgi-bin/get-flv?id=$id</source>";
?>

							<duration></duration>
							<title></title>
						</item>
					</channel>
					<config>
					</config>
					<plugIns> <!--
						<logoPlugIn>
							<url>logoPlugIn.swf</url>
							<logoText>www.ruochi.com</logoText>
							<logoTextAlpha>0.75</logoTextAlpha>
							<logoTextFontSize>30</logoTextFontSize>
							<logoTextLink>http://www.ruochigroup.com</logoTextLink>
							<logoTextColor>0xffffff</logoTextColor>
							<textMargin>20 20 auto auto</textMargin>
						</logoPlugIn> -->
					</plugIns>
				</vcastr>"/>
		</object>
	</body>
</html>
