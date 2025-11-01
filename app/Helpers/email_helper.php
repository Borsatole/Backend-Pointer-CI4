<?php

use App\Models\JobModel;

if (!function_exists('enviarEmailTemplate')) {
    /**
     * Envia email usando template HTML
     * 
     * @param string $template Nome do template (sem extensão)
     * @param array $variaveis Variáveis para substituição no template
     * @param string $para Email do destinatário
     * @param string $assunto Assunto do email
     * @param int $smtp_profile_id ID do perfil SMTP
     * @return bool True se enfileirado com sucesso, false se falhar
     */
    function enviarEmailTemplate(
        string $template,
        array $variaveis,
        string $para,
        string $assunto,
        int $smtp_profile_id = 1
    ): bool {
        $templatePath = APPPATH . "Views/email/templates/{$template}.html";

        if (!file_exists($templatePath)) {
            log_message('error', "Template de email não encontrado: {$template}");
            return false;
        }

        $html = @file_get_contents($templatePath);
        
        if ($html === false) {
            log_message('error', "Erro ao ler template de email: {$template}");
            return false;
        }
        
        // Substitui variáveis no formato {{chave}}
        $html = str_replace(
            array_map(fn($k) => "{{{$k}}}", array_keys($variaveis)),
            array_values($variaveis),
            $html
        );

        return enfileirarEmail($para, $assunto, $html, true, $smtp_profile_id);
    }

    /**
     * Envia email de texto simples
     * 
     * @param string $para Email do destinatário
     * @param string $assunto Assunto do email
     * @param string $mensagem Corpo do email (texto)
     * @param int $smtp_profile_id ID do perfil SMTP
     * @return bool True se enfileirado com sucesso, false se falhar
     */
    function enviarEmailSimples(
        string $para,
        string $assunto,
        string $mensagem,
        int $smtp_profile_id = 1
    ): bool {
        return enfileirarEmail($para, $assunto, $mensagem, false, $smtp_profile_id);
    }

    /**
     * Função privada para enfileirar email no banco
     * 
     * @param string $para Email do destinatário
     * @param string $assunto Assunto do email
     * @param string $mensagem Corpo do email
     * @param bool $isHtml Se a mensagem é HTML
     * @param int $smtp_profile_id ID do perfil SMTP
     * @return bool True se enfileirado com sucesso, false se falhar
     */
    function enfileirarEmail(
        string $para,
        string $assunto,
        string $mensagem,
        bool $isHtml,
        int $smtp_profile_id
    ): bool {
        try {
            $payload = [
                'para' => $para,
                'assunto' => $assunto,
                'mensagem' => $isHtml ? base64_encode($mensagem) : $mensagem,
                'html' => $isHtml ? 'true' : 'false',
                'smtp_profile_id' => $smtp_profile_id
            ];

            $jobModel = new JobModel();
            $result = $jobModel->insert([
                'task_name' => 'enviar_email',
                'payload' => json_encode($payload),
                'status' => 'pendente',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if (!$result) {
                log_message('error', "Erro ao enfileirar email para: {$para}");
                return false;
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', "Exceção ao enfileirar email: {$e->getMessage()}");
            return false;
        }
    }
}