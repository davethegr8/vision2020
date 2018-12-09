<?php

require_once '../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$dotenv = new Dotenv\Dotenv(__DIR__.'/../', (getenv('APP_ENV') ?: 'prod').'.env');
$dotenv->load();

$app = new App\Application(__DIR__.'/../App/routes.yml');
$app->addRoutes(__DIR__.'/../App/admin.yml');
$app->addRoutes(__DIR__.'/../App/jury.yml');

$app['debug'] = getenv('DEBUG') == 'true';

$app['config'] = [
    'id' => 'template',
    'env' => getenv('APP_ENV'),
    'tz' => getenv('TZ') ?: 'UTC',
    'project' => 'Burien Arts Vision 20/20',
    'root' => '',
    'repo' => '',
    'version' => 'v1',
];

$app->register(new Silex\Provider\SessionServiceProvider());

$app['session.storage.options'] = [
    'cookie_lifetime' => 60 * 60 * 24 * 7,
    'gc_maxlifetime' => 60 * 60 * 24 * 7,
    'httponly' => true
];

if (getenv('DBHOST')) {
    $app['database'] = new Hep\Foundation\Database\MySQL(
        getenv('DBHOST'),
        getenv('MYSQL_USER'),
        getenv('MYSQL_PASSWORD'),
        getenv('MYSQL_DATABASE')
    );
}

date_default_timezone_set($app['config']['tz']);

$app->register(new Silex\Provider\TwigServiceProvider, array(
    'twig.path' => __DIR__.'/../App/templates/'
));
$app['twig']->addGlobal('hostname', gethostname());
$app['twig']->addExtension(new Aptoma\Twig\Extension\MarkdownExtension(new Aptoma\Twig\Extension\MarkdownEngine\ParsedownEngine));

$app['slack'] = new App\Slack('https://hooks.slack.com/services/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', [
    'username' => 'vision2020',
    'icon_emoji' => ':art:'
]);

$app->run();

/**
* Interpolates context values into the message placeholders.
*/
function interpolate($message, array $context = array())
{
    // build a replacement array with braces around the context keys
    $replace = array();
    foreach ($context as $key => $val) {
        // check that the value can be casted to string
        if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
            $replace['{' . $key . '}'] = $val;
        }
    }

    // interpolate replacement values into the message and return
    return strtr($message, $replace);
}
