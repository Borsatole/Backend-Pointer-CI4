<?php

namespace App\Traits;

use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Services;
use App\Models\ChamadosImgModel;

trait ImagesTrait
{
    private function salvarImagens($imagens, $chamadoId): void
    {
        if ($imagens instanceof UploadedFile) {
            $imagens = [$imagens];
        }

        $imageService = Services::image();

        foreach ($imagens as $imagem) {

            if (!$imagem->isValid() || $imagem->hasMoved()) {
                continue;
            }

            // Nome final sempre .webp
            $nome = pathinfo($imagem->getRandomName(), PATHINFO_FILENAME) . '.webp';

            $destino = WRITEPATH . 'uploads/images/' . $nome;

            // Move para tmp
            $imagem->move(WRITEPATH . 'uploads/tmp');
            $origem = WRITEPATH . 'uploads/tmp/' . $imagem->getName();

            // 🔥 Converte para WEBP
            $imageService
                ->withFile($origem)
                ->resize(1280, 1280, true, 'width')
                ->convert(IMAGETYPE_WEBP)
                ->save($destino, 70); // qualidade 70

            // Remove temporário
            unlink($origem);

            // salva no model
            $imgModel = new ChamadosImgModel();

            $imgModel->insert([
                'chamado_id' => $chamadoId,
                'arquivo' => $nome,
            ]);

        }
    }

    private function deletarImagens($nome): void
    {
        $caminho = WRITEPATH . 'uploads/images/' . $nome;

        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }
}
