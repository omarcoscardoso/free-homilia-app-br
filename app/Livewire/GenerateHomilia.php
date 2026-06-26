<?php

namespace App\Livewire;

use Livewire\Component;
use App\Contracts\SermonGeneratorInterface;

class GenerateHomilia extends Component
{
    // Propriedades do Livewire que serão vinculadas aos inputs do formulário
    public string $text_homilia = '';
    public ?string $verso_homilia = null;
    public ?int $qt_divisao_homilia = null;
    public $showFeedbackModal = false;
    
    // Propriedade para armazenar o resultado da IA
    public ?string $message = null;
    public ?string $error = null; // Para exibir mensagens de erro

    protected SermonGeneratorInterface $sermonGenerator;

    /**
     * Injeção de dependência via método boot do Livewire (SOLID DIP).
     */
    public function boot(SermonGeneratorInterface $sermonGenerator)
    {
        $this->sermonGenerator = $sermonGenerator;
    }

    /**
     * Método chamado quando o formulário for submetido.
     */
    public function generateHomilia()
    {
        // Valida os dados usando as regras necessárias
        $this->validate([
            'text_homilia' => 'required|string|min:10|max:5000',
            'verso_homilia' => 'nullable|string|min:5|max:255',
            'qt_divisao_homilia' => 'nullable|integer|min:1|max:10',
        ],
        [
            'text_homilia.required' => 'Por favor, insira um texto para o esboço.',
            'text_homilia.string' => 'O texto do sermão deve ser uma string válida.',
            'text_homilia.min' => 'O texto do sermão deve ter no mínimo :min caracteres.',
            'text_homilia.max' => 'O texto do sermão não pode exceder :max caracteres.',
            'verso_homilia.string' => 'O versículo deve ser uma string válida.',
            'verso_homilia.min' => 'O versículo deve ter no mínimo :min caracteres.',
            'verso_homilia.max' => 'O versículo não pode exceder :max caracteres.',
            'qt_divisao_homilia.integer' => 'A quantidade de divisões deve ser um número inteiro.',
            'qt_divisao_homilia.min' => 'A quantidade de divisões deve ser no mínimo :min.',
            'qt_divisao_homilia.max' => 'A quantidade de divisões não pode exceder :max.',
        ]);

        // Reseta as mensagens anteriores
        $this->error = null;
        $this->message = null;

        try {
            // Chama a abstração do gerador de sermão
            $this->message = $this->sermonGenerator->generate(
                $this->text_homilia,
                $this->verso_homilia,
                $this->qt_divisao_homilia
            );
        } catch (\Exception $e) {
            // Captura e exibe o erro
            $this->error = 'Erro ao gerar o esboço: ' . $e->getMessage();
        }
    }

    /**
     * Renderiza o template Blade do componente.
     */
    public function render()
    {
        return view('livewire.generate-homilia');
    }
}