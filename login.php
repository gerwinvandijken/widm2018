<?php
session_start();

require_once('classes/database.php');

if(isset($_POST['login']))
{
	$username = trim(addslashes($_POST['username']));
	$password = md5(trim($_POST['password']));

	$db = Database::getInstance();
	$deelnemer = $db->getDeelnemer($username, $password);

	if (!is_null($deelnemer))
	{
		$_SESSION['s_logged_n'] = 'true';
		$_SESSION['s_username'] = $deelnemer->getGebruikersnaam();
		$_SESSION['s_name'] = $deelnemer->getVoornaam() . " " . $deelnemer->getAchternaam();
		$_SESSION['s_deelnemer'] = serialize($deelnemer);

		header("Location: deelnemers.php");
	}
	else
	{
		include("header.php");
?>
			<h2>Inloggen</h2>
			<p>Er is een fout opgetreden tijdens inloggen. De opgegeven gebruikersnaam en/of wachtwoord zijn niet correct. Probeer het nogmaals.</p>
<?php
			include("footer.php");
	}
}
else
{
	include("header.php");
?>
		<h2>Inloggen</h2>
		<p>Geef je gebruikersnaam en wachtwoord en klik op submit:</p>

		<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
		<p>Gebruikersnaam:<br>
		<input name="username" type="text" id="username">

		<p>Wachtwoord:<br>
		<input name="password" type="password" id="password">
		</p>

		<p>
		<input name="login" type="submit" id="login" value="Submit">
		</p>

		</form>

			</div>

<?php
	include("footer.php");
}
?>