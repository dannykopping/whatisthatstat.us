<?php
	require_once "../vendor/autoload.php";
	require_once "../services/HTTPStatusService.php";
	require_once "../vendor/gabordemooij/redbean/RedBean/redbean.inc.php";

	use Spore\Spore;
	use ActiveRecord\Config;
	use Slim\Extras\Views\Twig;

	$dbLocation = realpath("../db/data.sqlite");
	R::setup("sqlite:" . $dbLocation);
	R::freeze(true);

	Twig::$twigExtensions = array(
		"Twig_Extensions_Slim"
	);

	$twig = new Twig();
	$app  = new Spore(array(
	                       "debug"          => false,
	                       "pass-params"    => false,
	                       "templates.path" => "../templates",
	                       "view"           => $twig,
	                       "theme.path"     => realpath("theme")
	                  ));

	$env = $app->view()->getEnvironment();
	$env->addFilter("parseType", new Twig_Filter_Function("parseType"));

	$app->addService(new HTTPStatusService($app));

	$app->run();

	function parseType($type)
	{
		switch($type)
		{
			default:
			case "INFORMATIONAL":
				return "info";
				break;
			case "SUCCESS":
				return "success";
				break;
			case "REDIRECTION":
				return "inverse";
				break;
			case "CLIENT ERROR":
				return "warning";
				break;
			case "SERVER ERROR":
				return "important";
				break;
		}
	}