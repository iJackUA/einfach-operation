<?php

/**
 * Class ComplicatedController
 */
class ComplicatedController
{
    function actionDoTheJob(){
// simulate a common framework flow with operation
        // Let's assume we've got params smth like this
        // $params = $request->getParams();
        $params = [
            'id' => 123,
            'name' => 'Yevhen',
            'phone' => '123456789'
        ];

        $op = (new UpdateOperation)($params);

        if ($result->isSuccess()) {
            render($result->value());
        } else {
            render($result->error());
        }

    }
}
