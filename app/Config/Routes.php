<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


// Rotas de Login
$routes->group('login', function ($routes) {
    $routes->post('/', 'AuthController::login');
    $routes->post('validar', 'AuthController::validarToken');
});


// Rotas de Niveis
$routes->group('papeis', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'NiveisController::index',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->post(
        '',
        'NiveisController::create',
        ['filter' => 'permission:papeis.criar']
    );

    $routes->get(
        '(:num)',
        'NiveisController::show/$1',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->put(
        '(:num)',
        'NiveisController::update/$1',
        ['filter' => 'permission:papeis.editar']
    );

    $routes->delete(
        '(:num)',
        'NiveisController::delete/$1',
        ['filter' => 'permission:papeis.excluir']
    );

});

// Rotas de Permissoes
$routes->group('permissoes', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'PermissoesController::index',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->get(
        '(:num)',
        'PermissoesController::byNivel/$1',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->post(
        '',
        'PermissoesController::create',
        ['filter' => 'permission:papeis.criar']
    );

    $routes->put(
        '(:num)',
        'PermissoesController::updateByNivel/$1',
        ['filter' => 'permission:papeis.editar']
    );

    $routes->delete(
        '(:num)',
        'PermissoesController::delete/$1',
        ['filter' => 'permission:papeis.excluir']
    );

});

// Rotas de Usuarios
$routes->group('usuarios', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'UsuarioController::index',
        ['filter' => 'permission:usuario.visualizar']
    );

    $routes->post(
        '',
        'UsuarioController::create',
        ['filter' => 'permission:usuario.criar']
    );

    $routes->get(
        '(:num)',
        'UsuarioController::show/$1',
        ['filter' => 'permission:usuario.visualizar']
    );

    $routes->put(
        '(:num)',
        'UsuarioController::update/$1',
        ['filter' => 'permission:usuario.editar']
    );

    $routes->delete(
        '(:num)',
        'UsuarioController::delete/$1',
        ['filter' => 'permission:usuario.excluir']
    );

});

// Rotas de Condominios
$routes->group('condominios', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'CondominiosController::index',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->post(
        '',
        'CondominiosController::create',
        ['filter' => 'permission:papeis.criar']
    );

    $routes->get(
        '(:num)',
        'CondominiosController::show/$1',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->put(
        '(:num)',
        'CondominiosController::update/$1',
        ['filter' => 'permission:papeis.editar']
    );

    $routes->delete(
        '(:num)',
        'CondominiosController::delete/$1',
        ['filter' => 'permission:papeis.excluir']
    );
});

// Rotas de Visitas
$routes->group('visitas', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'VisitasController::index',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->post(
        '',
        'VisitasController::create',
        ['filter' => 'permission:papeis.criar']
    );

    $routes->get(
        '(:num)',
        'VisitasController::show/$1',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->put(
        '(:num)',
        'VisitasController::update/$1',
        ['filter' => 'permission:papeis.editar']
    );

    $routes->delete(
        '(:num)',
        'VisitasController::delete/$1',
        ['filter' => 'permission:papeis.excluir']
    );
});

// Rotas de Vistorias
$routes->group('vistorias', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'VistoriasController::index',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->post(
        '',
        'VistoriasController::create',
        ['filter' => 'permission:papeis.criar']
    );

    $routes->get(
        '(:num)',
        'VistoriasController::show/$1',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->put(
        '(:num)',
        'VistoriasController::update/$1',
        ['filter' => 'permission:papeis.editar']
    );

    $routes->delete(
        '(:num)',
        'VistoriasController::delete/$1',
        ['filter' => 'permission:papeis.excluir']
    );
});

$routes->group('itensparavistorias', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'ItensParaVistoriaController::index',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->post(
        '',
        'ItensParaVistoriaController::create',
        ['filter' => 'permission:papeis.criar']
    );

    $routes->get(
        '(:num)',
        'ItensParaVistoriaController::show/$1',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->put(
        '(:num)',
        'ItensParaVistoriaController::update/$1',
        ['filter' => 'permission:papeis.editar']
    );

    $routes->delete(
        '(:num)',
        'ItensParaVistoriaController::delete/$1',
        ['filter' => 'permission:papeis.excluir']
    );
});

$routes->group('itensvistoriados', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'ItensVistoriadosController::index',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->post(
        '',
        'ItensVistoriadosController::create',
        ['filter' => 'permission:papeis.criar']
    );

    $routes->get(
        '(:num)',
        'ItensVistoriadosController::show/$1',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->put(
        '(:num)',
        'ItensVistoriadosController::update/$1',
        ['filter' => 'permission:papeis.editar']
    );

    $routes->delete(
        '(:num)',
        'ItensVistoriadosController::delete/$1',
        ['filter' => 'permission:papeis.excluir']
    );
});