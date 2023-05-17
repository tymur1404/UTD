<?php
namespace Tests\Unit\Commands;

use App\Commands\GetCategoryPricesCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Tests\TestCase;

class GetCategoryPricesCommandTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        // Устанавливаем базовый путь приложения
        $app['path.base'] = __DIR__.'/../../';
    }

    public function testHandle()
    {
        $httpMock = Http::fake([
            '*' => Http::response([
                ['category' => 'Electronics', 'price' => 10],
                ['category' => 'Electronics', 'price' => 20],
                ['category' => 'Clothing', 'price' => 15],
                ['category' => 'Clothing', 'price' => 25],
            ]),
        ]);

        App::instance(Http::class, $httpMock);

        ob_start();

        $this->artisan('get:category-prices');

        $output = ob_get_clean();

        $expectedOutput = "Electronics - 30\nClothing - 40\n";
        $this->assertSame($expectedOutput, $output);

        $httpMock->assertSent(function ($request) {
            return $request->url() === 'https://fakestoreapi.com/products';
        });
    }
}
