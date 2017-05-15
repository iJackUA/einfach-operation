<?php

require __DIR__ . '/../../vendor/autoload.php';

include 'CRUDController.php';

$controller = new CRUDController();

echo "Create \n\r";
echo $controller->actionCreate(
        [ 'name' => 'MacBook Pro 15', 'price' => '199' ]
    );

echo "Read \n\r";
echo $controller->actionRead(
        [ 'id' => 10 ]
    );

echo "Update \n\r";
echo $controller->actionUpdate(
        [ 'id' => 10, 'name' => 'MacBook Air', 'price' => '119' ]
    );

echo "Delete \n\r";
echo $controller->actionDelete(
    [ 'id' => 10 ]
);

echo "Admin Delete \n\r";
echo $controller->actionAdminDelete(
    [ 'id' => 10 ]
);
