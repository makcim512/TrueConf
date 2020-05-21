<?
    use DI\Container;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
    use Psr\Http\Server\MiddlewareInterface;
    use Slim\Factory\AppFactory;
    use Slim\Views\Twig;
    use Slim\Views\TwigMiddleware;
    use app\JsonDB;
    use app\Api;

    require __DIR__ . '/../../vendor/autoload.php';
    require __DIR__ . '/../app/jsondb.php';
    require __DIR__ . '/../app/api.php';

    // Create Container
    $container = new Container();
    AppFactory::setContainer($container);

    // Set view in Container
    $container->set('view', function() {
        return Twig::create( __DIR__ . '/templates',
            ['cache' => false]);
    });
    
    // Create App
    $app = AppFactory::create();

    // Add Twig-View Middleware
    $app->add(TwigMiddleware::createFromContainer($app));

    // Insert JSON requst
    $app->add(function (Request $req, RequestHandler $handler)
    {
        $contentType = $req->getHeaderLine("Content-Type"); // Get contebt type

        // If content type is json
        if (strstr($contentType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true); // read php port

            // Testing json format
            if (json_last_error() === JSON_ERROR_NONE) {
                $req = $req->withParsedBody($contents);
            }
        }

        return $handler->handle($req);
     });
    
    // index page
    $app->get('/', function (Request $req, Response $res, $args) 
    {
        return $this->get('view')->render($res, 'index.twig', [
            'title' => "Тестовое API для компании TrueConf",
        ]);
    });

    // We prohibit direct access to the main page of the API
    $app->get('/api', function (Request $req, Response $res, $args)
    {
                $res->getBody()->write("Method not support!");
        return  $res->withHeader('Content-Type', 'text/html');
    });
    
    // Add users router
    $app->post('/api/user/add', function (Request $req, Response $res, $args)
    {
        $data           = $req->getParsedBody();            // Get json params
        $manipulation   = API::addUser( $data['name'] );

                $res->getBody()->write( $manipulation );
        return  $res->withHeader('Content-Type', 'aplication/json');
    });

    // Get all user router
    $app->post("/api/user", function(Request $req, Response $res, $args)
    {
        $manipulation   = Api::getUsers();           

                $res->getBody()->write( $manipulation );
        return  $res->withHeader('Content-Type', 'aplication/json');
    });

    // Get user at ID router
    $app->post("/api/user/get", function(Request $req, Response $res, $args)
    {
        $data           = $req->getParsedBody();            // Get json params
        $manipulation   = API::getUser( $data['id'] );

                $res->getBody()->write( $manipulation );
        return  $res->withHeader('Content-Type', 'aplication/json');
    });

    // Update user router
    $app->post("/api/user/updata", function(Request $req, Response $res, $args)
    {
        $data           = $req->getParsedBody();                      // Get json params
        $manipulation   = Api::UpdateUser(
                                    $data['id'], 
                                    $data['name']
                                );

                $res->getBody()->write( $manipulation );
        return  $res->withHeader('Content-Type', 'aplication/json');
    });

    $app->post("/api/user/delete", function(Request $req, Response $res, $args)
    {
        $data           = $req->getParsedBody();                      // Get json params
        $manipulation   = Api::deleteUser($data['id']);

                $res->getBody()->write( $manipulation );
        return  $res->withHeader('Content-Type', 'aplication/json');
    });
    $app->run();
?>