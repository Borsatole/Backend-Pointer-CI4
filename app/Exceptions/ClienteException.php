<?php

namespace App\Exceptions;

use Exception;

class ClienteException extends Exception
{
    public static function naoEncontrado(): self
    {
        return new self('Cliente não encontrado', 404);
    }

    public static function nomeObrigatorio(): self
    {
        return new self('O campo nome é obrigatório', 400);
    }

    public static function erroCriar(array $errors = []): self
    {
        $message = 'Erro ao criar cliente';
        if (!empty($errors)) {
            $message .= ': ' . implode(', ', $errors);
        }
        return new self($message, 400);
    }

    public static function erroAtualizar(array $errors = []): self
    {
        $message = 'Erro ao atualizar cliente';
        if (!empty($errors)) {
            $message .= ': ' . implode(', ', $errors);
        }
        return new self($message, 400);
    }

    public static function erroDeletar(): self
    {
        return new self('Erro ao deletar cliente', 400);
    }
}