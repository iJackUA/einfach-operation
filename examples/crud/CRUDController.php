<?php
require_once 'CreateOperation.php';
require_once 'ReadOperation.php';
require_once 'UpdateOperation.php';
require_once 'DeleteOperation.php';
require_once 'AdminDeleteOperation.php';

class CRUDController
{
    function actionCreate($params)
    {
        $params['user'] = $this->user();
        $result = (new CreateOperation)($params);
        return $this->renderOperationResult($result);
    }

    function actionRead($params)
    {
        $params['user'] = $this->user();
        $result = (new ReadOperation)($params);
        return $this->renderOperationResult($result);
    }

    function actionUpdate($params)
    {
        $params['user'] = $this->user();
        $result = (new UpdateOperation)($params);
        return $this->renderOperationResult($result);
    }

    function actionDelete($params)
    {
        $params['user'] = $this->user();
        $result = (new DeleteOperation)($params);
        return $this->renderOperationResult($result);
    }

    function actionAdminDelete($params)
    {
        $params['user'] = (object) [ 'id' => 30, 'login' => 'admin'];
        $result = (new AdminDeleteOperation)($params);
        return $this->renderOperationResult($result);
    }

    protected function user() 
    {
        return (object) [ 'id' => 20, 'login' => 'yevhen'];
    }

    protected function renderOperationResult($result)
    {
        if ($result->isSuccess()) {
            print_r($result->pipeline());
            print_r($result->params());
        } else {
            print_r($result->pipeline());
            return $result->errorsText();
        }
    }
}
