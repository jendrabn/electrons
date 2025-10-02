<?php

namespace Tests\Feature;

use App\Services\AIContentGeneratorService;
use Tests\TestCase;

class AIContentGeneratorServiceTest extends TestCase
{
    /**
     * Test service can be instantiated
     */
    public function test_ai_service_can_be_instantiated(): void
    {
        $service = app(AIContentGeneratorService::class);

        $this->assertInstanceOf(AIContentGeneratorService::class, $service);
    }

    /**
     * Test that service methods exist
     */
    public function test_ai_service_has_required_methods(): void
    {
        $service = app(AIContentGeneratorService::class);

        $this->assertTrue(method_exists($service, 'generateContent'));
        $this->assertTrue(method_exists($service, 'generateTitleSuggestions'));
    }

    /**
     * Test that prompt building includes all parameters
     */
    public function test_prompt_includes_all_parameters(): void
    {
        $service = app(AIContentGeneratorService::class);

        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('buildPrompt');
        $method->setAccessible(true);

        $params = [
            'topic' => 'Laravel Testing',
            'difficulty' => 'intermediate',
            'programming_language' => 'PHP',
            'article_length' => 'medium',
            'include_code_examples' => true,
            'key_points' => 'unit testing, feature testing',
        ];

        $prompt = $method->invoke($service, $params);

        $this->assertStringContainsString('Laravel Testing', $prompt);
        $this->assertStringContainsString('intermediate', $prompt);
        $this->assertStringContainsString('PHP', $prompt);
        $this->assertStringContainsString('unit testing', $prompt);
        $this->assertStringContainsString('feature testing', $prompt);
    }
}
