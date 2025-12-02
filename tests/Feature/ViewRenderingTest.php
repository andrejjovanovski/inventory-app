<?php

namespace Tests\Feature;

use Tests\TestCase;

class ViewRenderingTest extends TestCase
{
    public function test_verify_member_view_renders_correctly()
    {
        $url = 'http://example.com/verify';
        $view = view('emails.verify_member', ['url' => $url]);
        
        try {
            $content = $view->render();
            $this->assertStringContainsString($url, $content);
        } catch (\Exception $e) {
            $this->fail("View rendering failed: " . $e->getMessage());
        }
    }
}
