<?php
// public/index.php (MIS À JOUR)

session_start();

require_once __DIR__ . '/../app/Config/config.php';
require_once __DIR__ . '/../app/Models/Database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Material.php';
require_once __DIR__ . '/../app/Models/Supplier.php';
require_once __DIR__ . '/../app/Models/Client.php';
require_once __DIR__ . '/../app/Models/Prestation.php';
require_once __DIR__ . '/../app/Models/PrestationLigne.php';
require_once __DIR__ . '/../app/Models/Declaration.php';
require_once __DIR__ . '/../app/Models/Transport.php';
require_once __DIR__ . '/../app/Models/TransportLigne.php';
require_once __DIR__ . '/../app/Models/TransportDeclaration.php';
require_once __DIR__ . '/../app/Models/Invoice.php';
require_once __DIR__ . '/../app/Models/InvoiceLine.php';
require_once __DIR__ . '/../app/Models/Weighbridge.php';      // Inclure le modèle Weighbridge
require_once __DIR__ . '/../app/Models/WeighbridgeTax.php';  // Inclure le modèle WeighbridgeTax

require_once __DIR__ . '/../app/Controllers/UserController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/MaterialController.php';
require_once __DIR__ . '/../app/Controllers/SupplierController.php';
require_once __DIR__ . '/../app/Controllers/ClientController.php';
require_once __DIR__ . '/../app/Controllers/PrestationController.php';
require_once __DIR__ . '/../app/Controllers/TransportController.php';
require_once __DIR__ . '/../app/Controllers/InvoiceController.php';
require_once __DIR__ . '/../app/Controllers/WeighbridgeController.php'; // Inclure le contrôleur Weighbridge

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_uri = trim($request_uri, '/');

$base_path = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
if (!empty($base_path) && strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
    $request_uri = trim($request_uri, '/');
}

$segments = explode('/', $request_uri);
$controller_name = !empty($segments[0]) ? $segments[0] : '';
$action_name = !empty($segments[1]) ? $segments[1] : 'index';
$param_id = !empty($segments[2]) ? $segments[2] : null;

if (empty($controller_name)) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/login');
        exit();
    } else {
        $controller = new DashboardController();
        $controller->index();
    }
} elseif ($controller_name == 'login') {
    $controller = new UserController();
    $controller->login();
} elseif ($controller_name == 'logout') {
    $controller = new UserController();
    $controller->logout();
} elseif ($controller_name == 'users') {
    $controller = new UserController();
    if ($action_name == 'create') {
        $controller->create();
    } elseif ($action_name == 'edit' && $param_id) {
        $controller->edit($param_id);
    } elseif ($action_name == 'delete' && $param_id) {
        $controller->delete($param_id);
    } else {
        $controller->index();
    }
} elseif ($controller_name == 'materiels') {
    $controller = new MaterialController();
    if ($action_name == 'create') {
        $controller->create();
    } elseif ($action_name == 'edit' && $param_id) {
        $controller->edit($param_id);
    } elseif ($action_name == 'delete' && $param_id) {
        $controller->delete($param_id);
    } else {
        $controller->index();
    }
} elseif ($controller_name == 'fournisseurs') {
    $controller = new SupplierController();
    if ($action_name == 'create') {
        $controller->create();
    } elseif ($action_name == 'edit' && $param_id) {
        $controller->edit($param_id);
    } elseif ($action_name == 'delete' && $param_id) {
        $controller->delete($param_id);
    } else {
        $controller->index();
    }
} elseif ($controller_name == 'clients') {
    $controller = new ClientController();
    if ($action_name == 'create') {
        $controller->create();
    } elseif ($action_name == 'edit' && $param_id) {
        $controller->edit($param_id);
    } elseif ($action_name == 'delete' && $param_id) {
        $controller->delete($param_id);
    } else {
        $controller->index();
    }
} elseif ($controller_name == 'prestations') {
    $controller = new PrestationController();
    if ($action_name == 'create') {
        $controller->create();
    } elseif ($action_name == 'edit' && $param_id) {
        $controller->edit($param_id);
    } elseif ($action_name == 'delete' && $param_id) {
        $controller->delete($param_id);
    } else {
        $controller->index();
    }
} elseif ($controller_name == 'transports') {
    $controller = new TransportController();
    if ($action_name == 'create') {
        $controller->create();
    } elseif ($action_name == 'edit' && $param_id) {
        $controller->edit($param_id);
    } elseif ($action_name == 'delete' && $param_id) {
        $controller->delete($param_id);
    } else {
        $controller->index();
    }
} elseif ($controller_name == 'weighbridges') {
    $controller = new WeighbridgeController();
    if ($action_name == 'create') {
        $controller->create();
    } elseif ($action_name == 'edit' && $param_id) {
        $controller->edit($param_id);
    } elseif ($action_name == 'delete' && $param_id) {
        $controller->delete($param_id);
    } else {
        $controller->index();
    }
} elseif ($controller_name == 'invoices') {
    $controller = new InvoiceController();
    if ($action_name == 'create') {
        $controller->create();
    } elseif ($action_name == 'edit' && $param_id) {
        $controller->edit($param_id);
    } elseif ($action_name == 'delete' && $param_id) {
        $controller->delete($param_id);
    } elseif ($action_name == 'generate-pdf' && $param_id) {
        $controller->generatePdf($param_id);                 
    } else {
        $controller->index();
    }
}
else {
    http_response_code(404);
    echo "<h1>404 - Page non trouvée</h1>";
}
?>