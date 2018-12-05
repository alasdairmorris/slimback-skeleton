<?php
// Application middleware

class AuthenticationMiddleware extends \Slim\Middleware
{
    public function call()
    {
        $app = $this->app;
        $baseUrl = $app->settings['base_url'];

        // The following routes don't need authentication
        $exceptions = array(
            $app->urlFor('login'),
            $app->urlFor('logout')
        );

        if(!in_array($baseUrl . $this->app->request()->getPathInfo(), $exceptions))
        {
            if(!\App\Authentication::IsUserLoggedIn($this->app)) {
                if(preg_match("/^\/api/", $this->app->request()->getPathInfo())) {
                    $app->response->setStatus(401);
                    $app->response()->header('Content-Type', 'application/json');
                    echo json_encode(array('error' => 'Permission Denied'));
                    return;
                } else {
                    return $app->response()->redirect($app->urlFor('login') . '#');
                }
            }
        }

        $this->next->call();
    }
}

class MaintenanceMiddleware extends \Slim\Middleware
{
    public function call()
    {
        $mode = $this->app->getMode();
        if('maintenance' === $mode) {
            $body = "<html><head><title>Maintenance</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;}h1{margin:0 0 20px 0;font-size:24px;font-weight:normal;line-height:24px;}strong{display:inline-block;width:65px;}.container{text-align: center;}</style></head><body><div class='container'><h1>Maintenance Mode</h1><p>Currently down for maintenance.</p><p>Please try again later.</p></div></body></html>";
            $this->app->contentType('text/html');
            $this->app->response()->status(503);
            $this->app->response()->body($body);
        } else {
            $this->next->call();
        }
    }
}

// $app->add(new AuthenticationMiddleware());
// $app->add(new MaintenanceMiddleware());

