<?php

namespace App\Exceptions;

use Exception;

class MessagesException extends Exception
{
    public static function erroGenerico($mensagem): self
    {
        return new self($mensagem, 400);
    }
    public static function naoEncontrado($nome): self
    {
        return new self("Registro {$nome} não encontrado", 404);
    }

    public static function itemMuitoLongo(): self
    {
        return new self(
            'O nome do item não pode ter mais de 255 caracteres.',
            400
        );
    }

    public static function campoObrigatorio($campo): self
    {
        return new self("O campo {$campo} é obrigatório", 400);
    }


    public static function erroCriar(array $errors = []): self
    {
        $message = 'Erro ao criar item registro';
        if (!empty($errors)) {
            $message .= ': ' . implode(', ', $errors);
        }
        return new self($message, 400);
    }

    public static function erroAtualizar(array $errors = []): self
    {
        $message = 'Erro ao atualizar registro';
        if (!empty($errors)) {
            $message .= ': ' . implode(', ', $errors);
        }
        return new self($message, 400);
    }

    public static function erroDeletar(): self
    {
        return new self('Erro ao deletar registro', 400);
    }
}