<?php
// Routes

$app->get('/andy', function ($request, $response, $args) {
    return $response->withJSON(array('Hello' => 'World'));
});

$app->post('/login', 'ANDRES\ejemplo\slim\Login:Login');

$app->group('/api', function() use ($app){


    $app->group('/orders', function(){
        $this->get('', 'ANDRES\ejemplo\slim\orders:lst');
        $this->get('/{idord}','ANDRES\ejemplo\slim\orders:get');
        $this->post('', 'ANDRES\ejemplo\slim\orders:add');
        $this->put('/{idord}', 'ANDRES\ejemplo\slim\orders:upd');
        $this->delete('/{idord}','ANDRES\ejemplo\slim\orders:dlt');

    });
    // agentes = usuarios
    $app->group('/agents', function(){
        $this->get('', 'ANDRES\ejemplo\slim\agents:lst');
        $this->get('/{iduser}','ANDRES\ejemplo\slim\agents:get');
        $this->post('', 'ANDRES\ejemplo\slim\agents:add');
        $this->put('/{iduser}', 'ANDRES\ejemplo\slim\agents:upd');
        $this->delete('/{iduser}','ANDRES\ejemplo\slim\agents:dlt');
    });

    $app->group('/contacts', function(){
        $this->get('', 'ANDRES\ejemplo\slim\users:lst');
        $this->post('', 'ANDRES\ejemplo\slim\users:add');
        $this->get('/{idcont}','ANDRES\ejemplo\slim\users:get');
        $this->put('/{idcont}', 'ANDRES\ejemplo\slim\users:upd');
        $this->delete('/{idcont}','ANDRES\ejemplo\slim\users:dlt');

        //ordenes por contacto
        $this->get('/{idcont}/ordenes','ANDRES\ejemplo\slim\users:obtener');




    });

    $app->group('/acts', function(){
        $this->get('', 'ANDRES\ejemplo\slim\activities:lst');

    });


});




