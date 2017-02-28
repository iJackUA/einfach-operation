<?php
include 'UpdateOperation.php';

/**
 * Class ComplicatedController
 */
class ComplicatedController
{
    function actionDoTheJob()
    {
        // simulate a common framework flow with operation
        // Let's assume we've got params smth like this
        // $params = $request->getParams();
        $params = [
            'id' => 123,
            'name' => 'Yevhen',
            'phone' => '123456789'
        ];

        // ✨✨✨ magic is done here ✨✨✨
        $result = (new UpdateOperation)($params);

        // decide what to do with a result
        if ($result->isSuccess()) {
            return "Success! User '{$result->params()['model']['name']}' updated!";
        } else {
            return $result->errorsText();
        }

    }
}
