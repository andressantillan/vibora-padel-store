<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\BrandController
 */
final class BrandControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $brands = Brand::factory()->count(3)->create();

        $response = $this->get(route('brands.index'));

        $response->assertOk();
        $response->assertViewIs('brands.index');
        $response->assertViewHas('brands', $brands);
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\BrandController::class,
            'store',
            \App\Http\Requests\BrandStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = fake()->name();
        $slug = fake()->slug();

        $response = $this->post(route('brands.store'), [
            'name' => $name,
            'slug' => $slug,
        ]);

        $brands = Brand::query()
            ->where('name', $name)
            ->where('slug', $slug)
            ->get();
        $this->assertCount(1, $brands);
        $brand = $brands->first();

        $response->assertRedirect(route('brands.index'));
    }
}
