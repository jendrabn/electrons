<?php

namespace Tests\Unit;

use App\Services\AIContentGeneratorService;
use PHPUnit\Framework\TestCase;

class AIContentGeneratorServiceTest extends TestCase
{
    /**
     * Test service can be instantiated
     */
    public function test_ai_service_can_be_instantiated(): void
    {
        $service = new AIContentGeneratorService;

        $this->assertInstanceOf(AIContentGeneratorService::class, $service);
    }

    /**
     * Test that service methods exist
     */
    public function test_ai_service_has_required_methods(): void
    {
        $service = new AIContentGeneratorService;

        $this->assertTrue(method_exists($service, 'generateContent'));
        $this->assertTrue(method_exists($service, 'generateTitleSuggestions'));
    }

    /**
     * Test that prompt building includes all parameters
     */
    public function test_prompt_includes_all_parameters(): void
    {
        $service = new AIContentGeneratorService;

        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('buildPrompt');
        $method->setAccessible(true);

        $params = [
            'topic' => 'Laravel Testing',
            'difficulty' => 'intermediate',
            'language' => 'PHP',
            'length' => 'medium',
            'include_code' => true,
            'focus_areas' => ['unit testing', 'feature testing'],
        ];

        $prompt = $method->invoke($service, $params);

        $this->assertStringContainsString('Laravel Testing', $prompt);
        $this->assertStringContainsString('intermediate', $prompt);
        $this->assertStringContainsString('PHP', $prompt);
        $this->assertStringContainsString('unit testing', $prompt);
        $this->assertStringContainsString('feature testing', $prompt);
    }
}
