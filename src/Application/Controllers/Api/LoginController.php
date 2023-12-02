<?php

namespace App\Application\Controllers\Api;

use App\Application\Models\UsersModel;
use PDO;
use Psr\Log\LoggerInterface;

class LoginController {
    // Access to model that contains DB queries
    private $model;
    private $log;

    // Get the containers classes you need by Dependency Injection (PDO, Logger, etc)
    public function __construct(PDO $pdo, LoggerInterface $logger) {
        // Instantiate a new model, passing DB conn
        $this->model = new UsersModel($pdo);
        $this->log = $logger;
    }

    /**
     * @OA\Get(
     *     path="/login",
     *     @OA\Response(response="200", description="Login success"),
     *     @OA\Response(response="403", description="Login failed")
     * )
     */
    public function doLogin($req, $res, $args) {
        $body = $req->getParsedBody();
        $email = $body['email'];
        $pass = $body['pass'];

        // Call the rigth method in the model to retrieve data
        $data = $this->model->getByLogin($email);
        //$this->log->info("Data: ". json_encode($data));
        
        // Check user pass
        if ($data) {
            if ( password_verify($pass, $data['password_hash']) ) {
                // Save session and continue process
                $_SESSION['user'] = $data['id'];
                $_SESSION['user_email'] = $email;

                $this->model->updateLastLogin($data['id']);

                return $res
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(200);
            }
        }

        // Any other case, no valid session, send error
        unset($_SESSION["user"]);

        return $res
            ->withHeader('content-type', 'application/json')
            ->withStatus(403);
    }

    public function doLogout($req, $res){
        
        session_destroy();

        return $res
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    }
}
