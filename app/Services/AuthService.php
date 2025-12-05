<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\NiveisModel;
use App\Models\SistemModel;
use App\Exceptions\AuthException;
use App\Exceptions\MessagesException;
use App\Exceptions\NivelException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;




class AuthService
{
    protected Usuario $usuarios;
    protected NiveisModel $niveis;
    protected SistemModel $sistem;
    protected $db;

    public function __construct()
    {
        $this->usuarios = new Usuario();
        $this->niveis = new NiveisModel();
        $this->sistem = new SistemModel();
        $this->db = \Config\Database::connect();
        helper('email');
    }

    /**
     * Autentica o usuário e retorna dados completos (usuário, menu, token)
     */
    public function autenticar(string $email, string $senha): array
    {
        $dataAtual = date('Y-m-d');
        $usuario = $this->usuarios->where('email', $email)->first();
        $sistema = $this->sistem->first();


        // verificacoes de sistema
        if ($sistema['licenca'] != 'ativo') {
            throw MessagesException::erroGenerico('Entre em contato com o administrador do sistema.');
        }

        if ($sistema['modo_manutencao']) {
            throw MessagesException::erroGenerico($sistema['mensagem_manutencao']);
        }


        

        // verificacoes de usuario
        if (!$usuario) {
            throw AuthException::naoExiste();
        }

        if (!$usuario['ativo']) {
            throw MessagesException::erroGenerico('Conta suspensa. Entre em contato com o administrador do sistema.');
        }

        if (!$this->validarSenha($senha, $usuario['senha'])) {
            throw AuthException::senhaIncorreta();
        }

        if (empty($usuario['nivel'])) {
            throw AuthException::naoPossuiNivelDeAcesso();
        }

        $nivel = $this->buscarNivel($usuario['nivel']);
        $usuario['nivel_nome'] = $nivel['nome'];
        unset($usuario['senha']);

        $payload = $this->criarPayloadJWT($usuario);
        $token = $this->gerarJWT($payload);
        $menu = $sistema['data_proximo_vencimento'] >= $dataAtual ? $this->buscaMenu($usuario) : [];
        $vencido = $sistema['data_proximo_vencimento'] > $dataAtual ? true : false;
        $dataProximoVencimento = $sistema['data_proximo_vencimento'];
        $expirationTime = $payload['exp'];

        // enviarEmailSimples(
        //     // para
        //     'vitoriabotacini7@gmail.com',

        //     // assunto
        //     '🎉 O soca é do paaaai',

        //     // mensagem
        //     "Olá Selma Piruleibe, Só pra falar que o xoca é do pai"
        // );


        // $sucesso = enviarEmailTemplate(
        //     // template
        //     'boas_vindas',

        //     // variaveis
        //     ['nome' => 'Selma Piruleibe', 'codigo' => '123456'],

        //     // para
        //     // $usuario['email'], 
        //     'vitoriabotacini7@gmail.com',

        //     // assunto
        //     '🎉 Bem-vindo à PlayNet!');





        return [
            'usuario' => $usuario,
            'menu' => $menu,
            'vencido' => $vencido,
            'dataProximoVencimento' => $dataProximoVencimento,
            'token' => $token,
            'expirationTime' => $expirationTime
        ];
    }

    private function validarSenha(string $senha, string $senhaHash): bool
    {
        return password_verify($senha, $senhaHash);
    }

    private function criarPayloadJWT(array $usuario): array
    {
        return [
            'iss' => base_url(),
            'iat' => time(),
            // 'exp' => time() + 3600,
            'exp' => time() + 43200,
            'sub' => $usuario['id'],
            'nivel' => $usuario['nivel']
        ];
    }

    public function gerarJWT(array $payload): string
    {
        $secret = env('JWT_SECRET');

        if (empty($secret)) {
            throw AuthException::tokenNaoGerado();
        }

        return JWT::encode($payload, $secret, 'HS256');
    }

    public function validarToken(string $authHeader): array
    {
        $token = $this->extrairToken($authHeader);
        $secret = env('JWT_SECRET');

        try {

            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return (array) $decoded;

        } catch (\Exception $e) {
            throw AuthException::tokenInvalido();
        }
    }

    private function extrairToken(string $authHeader): string
    {
        if (empty($authHeader)) {
            throw AuthException::tokenNaoFornecido();
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw AuthException::tokenInvalido();
        }

        return $matches[1];
    }

    private function buscarNivel(int $id): array
    {
        $nivel = $this->niveis->buscarPorId($id);

        if (!$nivel) {
            throw NivelException::naoEncontrado();
        }

        return $nivel;
    }

    private function buscaMenu(array $usuario): array
    {
        $menusConfig = include(APPPATH . 'Config/Menus.php');
        return $menusConfig[$usuario['nivel']] ?? $menusConfig[2];
    }





}
