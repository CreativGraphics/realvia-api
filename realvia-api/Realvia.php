<?php

namespace RealviaApi;

use RealviaApi\Database\Database;
use RealviaApi\Logger\Logger;
use RealviaApi\Model\Broker;
use RealviaApi\Model\Realestate;
use RealviaApi\Util\DotEnv;

class Realvia
{
    private ?Logger $logger = null;

    private Database $database;

    private $response = [
        'result' => 'ok',
        'message' => ''
    ];

    public function __construct()
    {
        $this->database = new Database(DotEnv::get("DB_HOST"), DotEnv::get("DB_PORT"), DotEnv::get("DB_USER"), DotEnv::get("DB_PASS"), DotEnv::get("DB_NAME"));
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function enableLogger($logDirectory = "logs")
    {
        $this->logger = new Logger($logDirectory);
    }

    public function getRealestate(?string $order = null, ?int $limit = null): array
    {
        return $this->database->findAll(Realestate::class, $order, $limit);
    }

    public function findRealestate(int $id): ?Realestate
    {
        return $this->database->find(Realestate::class, $id);
    }

    public function findBroker(int $id): ?Broker
    {
        return $this->database->find(Broker::class, $id);
    }

    public function getBrokers(?string $order = null, ?int $limit = null): array
    {
        return $this->database->findAll(Broker::class, $order, $limit);
    }

    public function getRealestateByBroker(Broker $broker, ?string $order = null, ?int $limit = null): array
    {
        return $this->database->findBy(Realestate::class, [
            "broker" => $broker->getId()
        ], $order, $limit);
    }

    public function handleRequest()
    {
        header('Content-Type: application/json');
        if ($this->logger != null) $this->logger->logRequest();

        $method = $_SERVER["REQUEST_METHOD"];

        switch ($method) {
            case "POST":
                if ($this->validatePostData()) {
                    $this->parsePostData();
                }
                break;
            default:
                http_response_code(405);
                $this->response['result'] = 'error';
                $this->response['message'] = 'Method Not Allowed';
                break;
        }

        if ($this->logger != null) $this->logger->log('[RESPONSE]' . PHP_EOL . var_export($this->response, true));

        echo json_encode($this->response);
    }

    public function validatePostData(): bool
    {
        if (isset($_POST['realestate'])) return true;
        if (isset($_POST['action']) && isset($_POST['exportId'])) return true;

        $this->response["result"] = "error";
        $this->response["message"] = "Chyba pri spracovaní požiadavky.";

        return false;
    }

    public function parsePostData()
    {
        $realestate = null;
        $broker = null;

        if (isset($_POST["realestate"])) {
            $realestateData = $_POST["realestate"];

            $realestate = Realestate::fromJson($realestateData);
        }

        if (isset($_POST["broker"])) {
            $brokerData = $_POST["broker"];

            $broker = Broker::fromJson($brokerData);

            if ($realestate) $realestate->setProperty('broker', $broker);
        }

        if (isset($_POST["images"])) {
            $imagesData = $_POST["images"];

            if ($realestate) $realestate->setProperty('images', json_decode($imagesData));
        }

        if ($broker) {
            $dbBroker = $this->database->find(Broker::class, $broker->getId());

            if ($dbBroker != null) {
                $this->database->update($broker);
            } else {
                $this->database->insert($broker);
            }
        }

        if ($realestate) {
            $dbRealestate = $this->database->find(Realestate::class, $realestate->getId());

            if ($dbRealestate != null) {
                $this->database->update($realestate);
                $this->response['message'] = 'Inzerát bol aktualizovaný';
            } else {
                $this->database->insert($realestate);
                $this->response['message'] = 'Inzerát bol vložený';
            }
        }

        if (isset($_POST["action"]) && $_POST["action"] == "delete") {
            $id = $_POST["exportId"];

            $realestate = $this->database->find(Realestate::class, $id);

            if ($realestate != null) {
                $this->database->delete($realestate);
            }
        }
    }
}
